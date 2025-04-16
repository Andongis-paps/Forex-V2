<?php

namespace App\Http\Controllers\Window;
use App\Http\Controllers\Controller;
use Validator;
use App;
use Lang;
use App\Admin;
use App\Models\User;
use Illuminate\Support\Carbon;
use DB;
use Hash;
use Auth;
use Dotenv\Validator as DotenvValidator;
use Session;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;

class RateConfigurationController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:RATE CONFIGURATION,VIEW')->only(['show']);
        $this->middleware('check.access.permission:RATE CONFIGURATION,ADD')->only(['denom']);
        $this->middleware('check.access.permission:RATE CONFIGURATION,EDIT')->only(['update']);
        $this->middleware('check.access.permission:RATE CONFIGURATION,DELETE')->only([]);
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $currency_query = DB::connection('forex')->table('tblcurrentrate')
            ->join('tblcurrency', 'tblcurrentrate.CurrencyID', 'tblcurrency.CurrencyID')
            ->joinSub(function ($query) {
                $query->from('tblcurrentrate')
                    ->select('CurrencyID', DB::raw('MAX(EntryDate) AS MaxEntryDate'))
                    ->groupBy('CurrencyID');
            }, 'max_dates', function ($join) {
                $join->on('tblcurrentrate.CurrencyID', '=', 'max_dates.CurrencyID')
                    ->on('tblcurrentrate.EntryDate', '=', 'max_dates.MaxEntryDate');
            })
            ->joinSub(function ($query) {
                $query->from('tblcurrentrate')
                    ->select('CurrencyID', 'EntryDate', DB::raw('MAX(CRID) AS MaxCRID'))
                    ->groupBy('CurrencyID', 'EntryDate');
            }, 'latest_updates', function ($join) {
                $join->on('tblcurrentrate.CurrencyID', '=', 'latest_updates.CurrencyID')
                    ->on('tblcurrentrate.EntryDate', '=', 'latest_updates.EntryDate')
                    ->on('tblcurrentrate.CRID', '=', 'latest_updates.MaxCRID');
            })
            ->select(
                'tblcurrentrate.Rate',
                'tblcurrentrate.CRID',
                'tblcurrency.Currency',
                'tblcurrency.CurrAbbv',
                'tblcurrency.RIBVariance',
                'tblcurrentrate.CurrencyID',
                'tblcurrentrate.EntryDate as RateMaxDate'
            )
            ->orderBy('tblcurrency.Currency', 'ASC');

        // Rate  Maintenance
        $result['currency'] = $currency_query->clone()
            ->get();

        // Rate  Config
        $result['current_rate'] = $currency_query->clone()
            ->paginate(10);

        $result['branches'] = DB::connection('forex')->table('tblbranch')
            ->selectRaw('tblbranch.BranchID, tblbranch.BranchCode, pawnshop.tblxbranch.Address, pawnshop.tblxbranch.OMID')
            ->join('pawnshop.tblxbranch', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.BranchCode')
            ->join('accounting.tblsegmentgroup', 'pawnshop.tblxbranch.BranchID', 'accounting.tblsegmentgroup.BranchID')
            ->join('accounting.tblcompany', 'accounting.tblsegmentgroup.CompanyID', 'accounting.tblcompany.CompanyID')
            ->join('accounting.tblsegments', 'accounting.tblsegmentgroup.SegmentID', 'accounting.tblsegments.SegmentID')
            ->where('accounting.tblsegments.SegmentID', '=', 3)
            ->groupBy('tblbranch.BranchID', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.Address', 'pawnshop.tblxbranch.OMID')
            ->orderByRaw('LENGTH(pawnshop.tblxbranch.BranchCode) , pawnshop.tblxbranch.BranchCode')
            ->get();

        $result['area'] = DB::connection('pawnshop')->table('tblxbranchom')
            ->get();

        $result['provinces'] = DB::connection('pawnshop')->table('tblxbranch')
            ->leftJoin('pawnshop.table_province', 'table_province.province_id', '=', 'tblxbranch.province_id')
            ->select(
                'table_province.province_id',
                'table_province.province_name'
            )
            ->where(function($query) {
                $query->where('tblxbranch.BranchCode', 'REGEXP', '^S[0-9]+')
                    ->orWhere('tblxbranch.BranchCode', '=', 'ADMIN');
            })
            ->whereNotIn('tblxbranch.BranchCode', ['S998', 'S999'])
            ->where('tblxbranch.IsActive', 1)
            ->distinct()
            ->orderBy('table_province.province_name', 'asc')
            ->get();

        $result['transact_type'] = DB::connection('forex')->table('tbltransactiontype')
            ->where('tbltransactiontype.Active', '!=', 0)
            ->get();

        return view('window.rate_config.rate_configuration', compact('result', 'menu_id'));
    }

    public function denom(Request $request) {
        $trans_type_id = $request->get('transact_type_id');
        $rate_conf_curr_id = $request->get('rate_conf_selected_curr_id');

        $curr_denom = DB::connection('forex')->table('tblcurrencydenom')
            ->where('tblcurrencydenom.CurrencyID', '=', $rate_conf_curr_id)
            ->where('tblcurrencydenom.BranchID', Auth::user()->getBranch()->BranchID)
            ->where('tblcurrencydenom.TransType', $trans_type_id)
            ->orderBy('tblcurrencydenom.BillAmount', 'DESC')
            ->get();

        $response = [
            'curr_denom' => $curr_denom,
        ];

        return response()->json($response);
    }

    public function update(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        $TTID = $request->input('transact-type-id');
        $currency_id = $request->input('rate-config-currid');
        $branches = array_filter(explode(',', str_replace(' ', '', $request->input('rate-config-selected-branch'))));

        $new_rate_config = [
            'denominations' => $request->input('rate-config-denominations'),
            'branch_ids' => '',
            'mnl_rate' => $request->input('rate-config-var-mnl-rate'),
            'buying' => $request->input('rate-config-var-buying'),
            'selling' => $request->input('rate-config-var-selling'),
            'coins' => $request->input('rate-config-var-coins'),
            'sinag_rate_buying' => $request->input('rate-config-sinag-rate-buying'),
            'sinag_rate_selling' => $request->input('rate-config-sinag-rate-selling'),
        ];

        $query = DB::connection('forex')->table('tblcurrencydenom as tcd')
            ->when(is_array($branches), function ($query) use ($branches) {
                return $query->whereIn('tcd.BranchID', $branches);
            }, function ($query) use ($branches) {
                return $query->where('tcd.BranchID', $branches);
            })
            ->where('tcd.CurrencyID', $currency_id)
            ->where('tcd.TransType', $TTID);

        $current_rate_config = $query->clone()
            ->select('tcd.CDID')
            ->orderBy('tcd.BranchID')
            ->orderByDesc('tcd.BillAmount')
            ->pluck('CDID');

        $branch_ids = $query->clone()
            ->select('tcd.BranchID')
            ->orderBy('tcd.BranchID')
            ->orderByDesc('tcd.BillAmount')
            ->pluck('BranchID');

        $cdidCount = $current_rate_config->count();
        $mnl_rate_config = count($new_rate_config['mnl_rate']);

        if ($mnl_rate_config === 0) {
            dd("Test");
        }

        $updates = [];

        foreach ($current_rate_config as $index => $cdid) {
            $rate_index = $index % $mnl_rate_config;

            $updates[] = [
                'CDID' => $cdid,
                'CurrencyID' => $currency_id,
                'BillAmount' => $new_rate_config['denominations'][$rate_index],
                'TransType' => $TTID,
                'BranchID' => $branch_ids[$index],
                'ManilaRate' => $new_rate_config['mnl_rate'][$rate_index],
                'SinagRateBuying' => $new_rate_config['sinag_rate_buying'][$rate_index],
                'SinagRateSelling' => $new_rate_config['sinag_rate_selling'][$rate_index],
                'VarianceBuying' => $new_rate_config['buying'][$rate_index],
                'VarianceSelling' => $new_rate_config['selling'][$rate_index],
                'UserID' => Auth::user()->UserID,
                'EntryDate' => $raw_date->toDateTimeString()
            ];
        }

        DB::connection('forex')->table('tblcurrencydenom')
            ->upsert($updates, ['CDID'], [
                'ManilaRate',
                'SinagRateBuying',
                'SinagRateSelling',
                'VarianceBuying',
                'VarianceSelling',
                'UserID',
                'EntryDate'
            ]);

        DB::connection('forex')->table('tblrateconfigtrail')
            ->upsert($updates, [
                'CDID',
                'CurrencyID',
                'BillAmount',
                'TransType',
                'BranchID',
                'ManilaRate',
                'SinagRateBuying',
                'SinagRateSelling',
                'VarianceBuying',
                'VarianceSelling',
                'UserID',
                'EntryDate'
            ]);

        $message = "Rate Config Updated!";
        return redirect()->back()->with('message', $message);
    }

    public function configHistory(Request $request) {
        $config_details = DB::connection('forex')->table('tblcurrencydenom')
            ->selectRaw('tblcurrency.Currency, tblcurrency.CurrencyID,
                GROUP_CONCAT(tblcurrencydenom.BillAmount) as BillAmount,
                GROUP_CONCAT(tblcurrencydenom.ManilaRate) as ManilaRate,
                GROUP_CONCAT(tblcurrencydenom.VarianceBuying) as VarianceBuying,
                GROUP_CONCAT(tblcurrencydenom.VarianceSelling) as VarianceSelling,
                GROUP_CONCAT(tblcurrencydenom.SinagRateBuying) as SinagRateBuying,
                GROUP_CONCAT(tblcurrencydenom.SinagRateSelling) as SinagRateSelling
            ') // Removed the trailing comma here
            ->join('tblcurrency', 'tblcurrencydenom.CurrencyID', 'tblcurrency.CurrencyID')
            ->where('tblcurrencydenom.BranchID', $request->get('branch_id'))
            ->where('tblcurrencydenom.TransType', '!=', 4)
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->groupBy('tblcurrency.Currency', 'tblcurrency.CurrencyID')
            ->get();

        // $get_configurations = [];

        // foreach ($config_details as $conf_details) {
        //     $current_config = DB::connection('forex')->table('tblcurrencydenom')
        //         ->selectRaw('tblcurrencydenom.CurrencyID, tblcurrencydenom.BillAmount, tblcurrencydenom.ManilaRate, tblcurrencydenom.VarianceBuying, tblcurrencydenom.VarianceSelling, tblcurrencydenom.SinagRateBuying, tblcurrencydenom.SinagRateSelling')
        //         ->where('tblcurrencydenom.BranchID', $request->get('branch_id'))
        //         ->where('tblcurrencydenom.CurrencyID', $conf_details->CurrencyID)
        //         ->groupBy('tblcurrencydenom.CurrencyID', 'tblcurrencydenom.BillAmount', 'tblcurrencydenom.ManilaRate', 'tblcurrencydenom.VarianceBuying', 'tblcurrencydenom.VarianceSelling', 'tblcurrencydenom.SinagRateBuying', 'tblcurrencydenom.SinagRateSelling')
        //         ->get();

        //     $get_configs = [];

        //     foreach ($current_config as $configs) {
        //         $get_configs[] = $configs;
        //     }

        //     $conf_details->denom_config = $get_configs;

        //     $get_configurations[] = $conf_details;
        // }

        $response = [
            'config_details' => $config_details,
        ];

        return response()->json($response);
    }
}
