<?php

namespace App\Http\Controllers\Window;
use App\Http\Controllers\Controller;
use Validator;
use App;
use Lang;
use App\Admin;
use Illuminate\Support\Carbon;
use DB;
use Hash;
use Auth;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Query\Builder;

class StopBuyingMaintenanceController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:DENOM CONFIGURATION,VIEW')->only(['show']);
        $this->middleware('check.access.permission:DENOM CONFIGURATION,ADD')->only(['getDenomination']);
        $this->middleware('check.access.permission:DENOM CONFIGURATION,EDIT')->only(['updateStopBuyingStatus']);
        $this->middleware('check.access.permission:DENOM CONFIGURATION,DELETE')->only([]);
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
                'tblcurrentrate.CurrencyID',
                'tblcurrentrate.EntryDate as RateMaxDate'
            )
            ->orderBy('tblcurrency.Currency', 'ASC');

        // Rate  Maintenance
        $result['currency'] = $currency_query->get();

        // Rate  Config
        $result['current_rate'] = $currency_query->paginate(10);

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

        return view('window.stop_buying_mainte.stop_buying', compact('result', 'menu_id'));
    }

    public function getDenomination(Request $request) {
        $trans_type_id = $request->get('transact_type_id');
        $currency_id = $request->get('stop_buying_selected_curr_id');

        $curr_denom = DB::connection('forex')->table('tblcurrencydenom')
            ->selectRaw('tblcurrencydenom.BillAmount, tblcurrencydenom.StopBuying, tbltransactiontype.TransType, tblcurrencydenom.TransType as TTID')
            ->join('tbltransactiontype', 'tblcurrencydenom.TransType', 'tbltransactiontype.TTID')
            ->where('tblcurrencydenom.CurrencyID', '=', $currency_id)
            ->where('tblcurrencydenom.BranchID', Auth::user()->getBranch()->BranchID)
            ->whereIn('tblcurrencydenom.TransType', [1, 2, 3])
            ->groupBy('tblcurrencydenom.BillAmount', 'tblcurrencydenom.StopBuying', 'tbltransactiontype.TransType', 'TTID')
            ->orderBy('TTID', 'ASC')
            ->orderBy('tblcurrencydenom.BillAmount', 'DESC')
            ->get();

        $response = [
            'curr_denom' => $curr_denom,
        ];

        return response()->json($response);
    }

    public function updateStopBuyingStatus(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        $exploded_buy_status = explode(",", trim($request->get('joined_stop_buying')));
        $trimmed_buy_status = array_map('trim', $exploded_buy_status);

        $exploded_bill_amnts = explode(",", trim($request->get('joined_bill_amounts')));
        $trimmed_bill_amnts = array_map('trim', $exploded_bill_amnts);

        $new_rate_config = [
            'stop_buying' => $trimmed_buy_status,
        ];

        $TTID = $request->input('transact-type-id');
        $currency_id = $request->input('stop-buying-currid');
        $branches = array_filter(explode(',', str_replace(' ', '', $request->input('stop-buying-selected-branch'))));

        $current_rate_config = DB::connection('forex')->table('tblcurrencydenom as tdc')
            // ->select('tdc.CDID', 'tdc.BranchID', 'tdc.BillAmount')
            ->where('tdc.CurrencyID', $currency_id)
            // ->where('TransType', $TTID)
            ->when($branches, fn($query) => $query->whereIn('tdc.BranchID', $branches))
            ->orderBy('tdc.BranchID', 'DESC')
            ->orderBy('tdc.BillAmount', 'DESC')
            ->pluck('tdc.CDID');

        $cdidCount = $current_rate_config->count();
        $stop_buying_statuses = count($new_rate_config['stop_buying']);

        if ($stop_buying_statuses === 0) {
            dd("Test");
        }

        $updates = [];

        foreach ($current_rate_config as $index => $cdid) {
            $rate_index = $index % $stop_buying_statuses;

            if (isset($new_rate_config['stop_buying'][$rate_index])) {
                $updates[] = [
                    'CDID' => $cdid,
                    'StopBuying' => $new_rate_config['stop_buying'][$rate_index],
                ];
            }
        }

        DB::connection('forex')->table('tblcurrencydenom')->upsert($updates, ['CDID'], [
            'StopBuying'
        ]);

        $message = "Rate Config Updated!";
        return redirect()->back()->with('message', $message);
    }

    public function currentStop(Request $request) {
        $stop_buying_details = DB::connection('forex')->table('tblcurrencydenom')
            ->selectRaw('tblcurrency.Currency, tblcurrency.CurrencyID,
                GROUP_CONCAT(tblcurrencydenom.BillAmount) as BillAmount,
                GROUP_CONCAT(tblcurrencydenom.StopBuying) as StopBuying,
                GROUP_CONCAT(tbltransactiontype.TransType) as TransType
            ') // Removed the trailing comma here
            ->join('tblcurrency', 'tblcurrencydenom.CurrencyID', 'tblcurrency.CurrencyID')
            ->join('tbltransactiontype', 'tblcurrencydenom.TransType', 'tbltransactiontype.TTID')
            ->where('tblcurrencydenom.BranchID', $request->get('branch_id'))
            ->where('tblcurrencydenom.TransType', '!=', 4)
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->groupBy('tblcurrency.Currency', 'tblcurrency.CurrencyID')
            ->get();

        $response = [
            'stop_buying_details' => $stop_buying_details,
        ];

        return response()->json($response);
    }
}
