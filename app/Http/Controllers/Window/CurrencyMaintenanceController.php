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
use Dotenv\Validator as DotenvValidator;
use Session;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;

class CurrencyMaintenanceController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:CURRENCY MAINTENANCE,VIEW')->only(['show', 'existing']);
        $this->middleware('check.access.permission:CURRENCY MAINTENANCE,ADD')->only(['add']);
        $this->middleware('check.access.permission:CURRENCY MAINTENANCE,EDIT')->only(['edit', 'update', 'editDenom']);
        $this->middleware('check.access.permission:CURRENCY MAINTENANCE,DELETE')->only(['delete', 'deleteDenom']);
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['currencies'] = DB::connection('forex')->table('tblcurrency as tc')
            ->join('tblcountries as tr', 'tc.CountryID', 'tr.CountryID')
            ->orderBy('tc.Currency', 'ASC')
            ->paginate(20);

        $result['counrties'] = DB::connection('forex')->table('tblcountries')
            ->get();

        return view('window.currency_mainte.currency_maintenance', compact('result', 'menu_id'));
    }

    public function add(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $sesh_username = session('user_name');
        $curr_name = $request->input('currency-name');

        $regex = '/^[A-Z0-9\s]*(?<![\[{}\]])[A-Z0-9\s]+(?![\]{}\]])$/';

        $validator = Validator::make($request->all(), [
            // 'currency_name' => 'required|regex: /^[a-zA-z]\s*/',
            // 'currency_sign' => 'required|regex: /(?:\p{Sc})?[a-zA-Z]\s*/',
            // 'currency_abbrev' => 'required',
            // 'currency_percent' => 'required|regex: /^[^a-zA-Z0-9.]*$/',
            'currency-name' => 'required|distinct',
            'currency-sign' => 'required',
            'currency-abbrev' => 'required',
            // 'currency-percent' => 'required',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        } else {
            $result['currency'] = DB::connection('forex')->table('tblcurrency')
                ->get();

            $currency_dup = collect($result['currency'])->contains(function ($currency) use ($curr_name) {
                return $currency->Currency == $curr_name;
            });

            if ($currency_dup == false) {
                $currency_id = DB::connection('forex')->table('tblcurrency')
                    ->insertGetId([
                        'Currency' => $request->input('currency-name'),
                        'CurrencySign' => $request->input('currency-sign'),
                        'CurrAbbv' => $request->input('currency-abbrev'),
                        'WithSerials' => $request->input('currency-serial-stat'),
                        'RIBVariance' => $request->input('rib-variance'),
                        'CountryID' => $request->input('currency-country-origin'),
                        'WithSetO' => $request->get('for_set_o'),
                        'WithSetB' => $request->get('for_set_b'),
                    ]);

                DB::connection('forex')->table('tblcurrentrate')
                    ->insert([
                        'CurrencyID' => $currency_id,
                        'EntryDate' => $raw_date->toDateString(),
                        'EntryDateTime' => $raw_date->toDateTimeString(),
                        'Rate' => 0.000000,
                        'UserID' => $request->input('matched_user_id'),
                        'CountryID' => $request->input('currency-country-origin'),
                    ]);

                // return redirect()->route('maintenance.currency_maintenance.edit_denom', ['currency_id' => $currency_id]);

                $response = [
                    'currency_id' => $currency_id
                ];

                return response()->json($response);
            }
        }
    }

    public function edit(Request $request) {
        $currency_details = DB::connection('forex')->table('tblcurrency')
            ->where('tblcurrency.CurrencyID' , '=' , $request->CurrencyID)
            ->get();

        $countries = DB::connection('forex')->table('tblcountries')
            ->get();

        return view("window.currency_mainte.update_currency_mainte_modal", compact('currency_details', 'countries'));
    }

    public function update(Request $request) {
        $regex = '/^[A-Z0-9\s]*(?<![\[{}\]])[A-Z0-9\s]+(?![\]{}\]])$/';

        DB::connection('forex')->table('tblcurrency')
            ->where('tblcurrency.CurrencyID', '=' , $request->currency_id)
            ->update([
                'Currency' => $request->currency_name,
                'CurrencySign' => $request->currency_sign,
                'CurrAbbv' => $request->currency_abbrev,
                'CoinsPercentage' => $request->currency_percent,
                'RIBVariance' => $request->rib_variance,
                'WithSerials' => $request->currency_serial_stat,
                'WithSetO' => $request->for_set_o,
                'WithSetB' => $request->for_set_b,
            ]);

        $message = "Currency details updated!";
        return redirect()->back()->with('message', $message);
    }

    public function search(Request $request) {
        $keyword = $request->get('search_word');

        $currencies = DB::connection('forex')->table('tblcurrency')
            ->where('tblcurrency.Currency' , 'LIKE' , "%{$keyword}%")
            ->get();

        return response()->json($currencies);
    }

    public function delete(Request $request) {
        DB::connection('forex')->table('tblcurrency')
            ->where('tblcurrency.CurrencyID', '=', $request->input('currency_id'))
            ->delete();

        DB::connection('forex')->table('tbldenominationmaintenance')
            ->where('tbldenominationmaintenance.CurrencyID', '=', $request->input('currency_id'))
            ->delete();

        DB::connection('forex')->table('tblcurrencydenom')
            ->where('tblcurrencydenom.CurrencyID', '=', $request->input('currency_id'))
            ->delete();

        $message = "Currency deleted successfully!";
		return redirect()->back()->with('message' , $message);
    }

    public function editDenom(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['denominations'] = DB::connection('forex')->table('tbldenominationmaintenance')
            ->join('tbltransactiontype', 'tbldenominationmaintenance.TTID', 'tbltransactiontype.TTID')
            ->where('tbldenominationmaintenance.CurrencyID', '=', $request->currency_id)
            ->get();

        $result['currency'] = DB::connection('forex')->table('tblcurrency')
            ->where('tblcurrency.CurrencyID', '=', $request->currency_id)
            ->first();

        $result['transact_type'] = DB::connection('forex')->table('tbltransactiontype')
            ->orderBy('tbltransactiontype.TTID', 'ASC')
            ->get();

        return view("window.currency_mainte.denomination_maintenance", compact('result', 'menu_id'));
    }

    public function transType(Request $request) {
        $transact_type = DB::connection('forex')->table('tbltransactiontype')
            ->where('tbltransactiontype.TransType', '!=', 'DPOFX')
            ->orderBy('tbltransactiontype.TTID', 'ASC')
            ->get();

        $response = [
            'transact_type' => $transact_type
        ];

        return response()->json($response);
    }

    public function updateDenominations(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $denominations = $request->input('denominations');
        $transact_type = $request->input('transact-type');
        $currency_id = $request->input('currency');
        $message = 'Denomination/s Added!';

        $validator = Validator::make($request->all(), [
            'denominations' => 'required|array',
            'denominations.*' => 'required',
            'transact-type' => 'required|array',
            'transact-type.*' => 'required'
        ], [
            'denominations.*.required' => 'The denomination is required.',
            'transact-type.*.required' => 'The transact type is required.',
        ]);

        $result['branches'] = DB::connection('forex')->table('tblbranch')
            ->select('tblbranch.BranchID')
            ->get();

        $branch_ids = $result['branches']->pluck('BranchID')->toArray();

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            foreach ($denominations as $key => $denom_val) {
                DB::connection('forex')->table('tbldenominationmaintenance')->insert([
                    'CurrencyID' => $currency_id,
                    'BillAmount' => $denom_val,
                    'UserID' => $request->input('matched_user_id'),
                    'TTID' => $transact_type[$key]
                ]);
            }

            foreach ($branch_ids as $branch_index => $branch_id) {
                foreach ($denominations as $key => $denom_val) {
                    DB::connection('forex')->table('tblcurrencydenom')->insert([
                        'CurrencyID' => $currency_id,
                        'BillAmount' => $denom_val,
                        'UserID' =>  $request->input('matched_user_id'),
                        'TransType' => $transact_type[$key],
                        'BranchID' => $branch_id,
                        'SinagRateBuying' => 0.00000,
                        'SinagRateSelling' => 0.00000,
                        'VarianceBuying' => 0.0000,
                        'VarianceSelling' => 0.0000,
                        'EntryDate' => $raw_date->toDateTimeString()
                    ]);
                }
            }
        }

    }

    public function updateOneDenom(Request $request) {
        DB::connection('forex')->table('tblcurrencydenom as tcd')
            ->where('tcd.CurrencyID', $request->input('currency'))
            ->where('tcd.BillAmount', $request->input('denomination'))
            ->where('tcd.TransType', $request->input('trans-type'))
            ->update([
                'StopBuying' => $request->get('status') == 1 ? 0 : 1
            ]);

        DB::connection('forex')->table('tbldenominationmaintenance as tdm')
            ->where('tdm.DenominationID', $request->get('denom_id'))
            ->update([
                'BillAmount' => $request->input('denomination'),
                'UserID' => $request->get('matched_user_id'),
                'TTID' => $request->input('trans-type'),
                'Status' => $request->get('status')
            ]);
    }

    public function deleteDenom(Request $request) {
        $query = DB::connection('forex')->table('tbldenominationmaintenance as dm')
            ->where('dm.DenominationID', '=', $request->input('denomination_id'));
        
        DB::connection('forex')->table('tblcurrencydenom as cd')
            ->where('cd.CurrencyID',  $query->pluck('CurrencyID'))
            ->where('cd.BillAmount',  $query->pluck('BillAmount'))
            ->delete();

        $query->delete();
    }

    public function existing(Request $request) {
        $exisiting_curr = DB::connection('forex')->table('tblcurrency')
            ->get();

        $response = [
            'exisiting_curr' => $exisiting_curr
        ];

        return response()->json($response);
    }
}
