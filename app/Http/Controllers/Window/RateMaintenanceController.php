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

class RateMaintenanceController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:RATE MAINTENANCE,VIEW')->only(['show', 'dpofxRate']);
        $this->middleware('check.access.permission:RATE MAINTENANCE,ADD')->only(['add', 'save']);
        $this->middleware('check.access.permission:RATE MAINTENANCE,EDIT')->only(['edit', 'update', 'editDpo', 'updateDPORate']);
        $this->middleware('check.access.permission:RATE MAINTENANCE,DELETE')->only([]);
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['current_rate'] = DB::connection('forex')->table('tblcurrentrate as tcr')
            ->selectRaw('tc.CurrencyID, tc.Currency, cr.Country, tc.CurrAbbv, MAX(CRID) as CRID, MAX(tcr.EntryDateTime) as MaxEntryDateTime')
            ->join('tblcurrency as tc', 'tcr.CurrencyID', '=', 'tc.CurrencyID')
            ->join('tblcountries as cr', 'tc.CountryID', '=', 'cr.CountryID')
            ->groupBy('tc.CurrencyID', 'tc.Currency', 'cr.Country', 'tc.CurrAbbv', 'cr.Country')
            ->orderBy('tc.Currency', 'ASC')
            ->paginate(15);

        $curr_rate = [];

        foreach ($result['current_rate'] as $index => $current_rate_details) {
            $get_curr_rate_deets = DB::connection('forex')->table('tblcurrentrate as tcr')
                ->selectRaw('tcr.Rate')
                ->where('tcr.CRID', '=', $current_rate_details->CRID)
                ->get();

            $rate = [];

            foreach ($get_curr_rate_deets as $get_rate) {
                $rate[] = $get_rate;
            }

            $current_rate_details->Rate = $rate;

            $curr_rate[] = $current_rate_details;
        }

        $result['branches'] = DB::connection('forex')->table('tblbranch')
            ->get();

        $result['currencies'] = DB::connection('forex')->table('tblcurrency')
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->get();

        $result['countries'] = DB::connection('forex')->table('tblcountries')
            ->orderBy('tblcountries.Country', 'ASC')
            ->get();

        return view('window.rate_mainte.rate_maintenance', compact('result', 'menu_id'));
    }

    public function edit(Request $request) {
        $rate_details = DB::connection('forex')->table('tblcurrentrate')
            ->selectRaw('tblcurrency.CurrencyID, tblcurrency.Currency, tblcountries.CountryID, tblcountries.Country, tblcurrentrate.CRID, tblcurrentrate.Rate, IF(COUNT(tbldenominationmaintenance.DenominationID) = 0, "false", "true") as DenomStatus')
            ->join('tblcurrency', 'tblcurrentrate.CurrencyID', 'tblcurrency.CurrencyID')
            ->leftJoin('tbldenominationmaintenance', 'tblcurrency.CurrencyID', 'tbldenominationmaintenance.CurrencyID')
            ->join('tblcountries', 'tblcurrency.CountryID', 'tblcountries.CountryID')
            ->where('tblcurrentrate.CRID' , '=' , $request->CRID)
            ->groupBy('tblcurrency.CurrencyID', 'tblcurrency.Currency', 'tblcountries.CountryID', 'tblcountries.Country', 'tblcurrentrate.CRID', 'tblcurrentrate.Rate')
            ->get();

        $result['countries'] = DB::connection('forex')->table('tblcountries')
            ->get();

        return view('window.rate_mainte.update_rate_mainte_modal', compact('rate_details' , 'result'));
    }

    public function update(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $current_manila_rate = $request->input('currency-rate-mainte');
        $currency_id = $request->input('currency-id');

        $validator = Validator::make($request->all(), [
            'currency-rate-mainte' => 'required',
        ]);

        $insert_updated_rate = array(
            'Rate' => $current_manila_rate,
            'UserID' => $request->input('matched_user_id'),
            'EntryDate' => $raw_date->toDateString(),
            'CurrencyID' => $currency_id,
            'CountryID' => $request->input('currency-mainte-country'),
            'EntryDateTime' => $raw_date->toDateTimeString()
        );

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        } else {
            $result['currency_denom'] = DB::connection('forex')->table('tblcurrencydenom')
                ->select('tblcurrencydenom.CDID','tblcurrencydenom.VarianceBuying','tblcurrencydenom.VarianceSelling')
                ->where('tblcurrencydenom.CurrencyID', '=', $currency_id)
                ->get();

            if ($result['currency_denom']->isEmpty()) {
                $response = [
                    'status' => 0
                ];

                return response()->json($response);
            } else {
                DB::connection('forex')->table('tblcurrentrate')
                    ->insert($insert_updated_rate);

                DB::connection('forex')->table('tblcurrency')
                    ->where('tblcurrency.CurrencyID', '=', $request->input('currency-rate-currency-id'))
                    ->update([
                        'CountryID' => $request->input('currency-mainte-country')
                    ]);

                DB::connection('forex')->table('tblcurrencydenom')
                    ->where('tblcurrencydenom.CurrencyID', '=', $currency_id)
                    ->update([
                        'ManilaRate' => floatval($current_manila_rate),
                        'SinagRateBuying' => DB::raw($current_manila_rate . ' - VarianceBuying'),
                        'SinagRateSelling' => DB::raw($current_manila_rate . ' + VarianceSelling'),
                    ]);

                $response = [
                    'status' => 1
                ];

                return response()->json($response);
            }
        }
    }

    public function save(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $curr_name = $request->input('currency-name');
        $curr_country_origin = $request->input('currency-country-origin-true');
        $curr_rate = $request->input('currency-rate');

        $regex = '/^[A-Z0-9\s]*(?<![\[{}\]])[A-Z0-9\s]+(?![\]{}\]])$/';

        $validator = Validator::make($request->all(), [
            'currency-name' => 'required',
            'currency-rate' => 'required',
            'currency-country-origin' => 'required',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        } else {
            DB::connection('forex')->table('tblcurrentrate')
                ->insert([
                    'CurrencyID' => $curr_name,
                    'EntryDate' => $raw_date->toDateString(),
                    'EntryDateTime' => $raw_date->toDateTimeString(),
                    'Rate' => $curr_rate,
                    'UserID' => $request->input('matched_user_info'),
                    'CountryID' => $curr_country_origin,
                ]);
        }

        $message = "New rate added!";
        return redirect()->back()->with('message', $message);
    }

    public function history(Request $request) {
        $rate_history = DB::connection('forex')->table('tblcurrentrate')
            ->selectRaw('FORMAT(tblcurrentrate.Rate, 4) as Rate, DATE(tblcurrentrate.EntryDateTime) as EntryDate, TIME(tblcurrentrate.EntryDateTime) as EntryTime, pawnshop.tblxusers.Name')
            ->join('pawnshop.tblxusers', 'tblcurrentrate.UserID', 'pawnshop.tblxusers.UserID')
            ->where('tblcurrentrate.CurrencyID', $request->get('currency_id'))
            ->where('tblcurrentrate.EntryDate', '>', '2025-01-01')
            ->groupBy('Rate', 'EntryDate', 'EntryTime', 'pawnshop.tblxusers.Name')
            ->orderBy('EntryDate', 'DESC')
            ->get();

        $response = [
            'rate_history' => $rate_history
        ];

        return response()->json($response);
    }

    public function countryAutoSelect(Request $request) {
        $country_id = $request->get('country_id');

        $response = DB::connection('forex')->table('tblcountries')
            ->where('tblcountries.CountryID', '=', $country_id)
            ->get();

        return response()->json($response);
    }

    public function dpofxRate(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['dpofx_rate'] = DB::connection('forex')->table('tbldpoindirate')
            ->join('tblbranch', 'tbldpoindirate.BranchID', 'tblbranch.BranchID')
            ->paginate(20);

        $result['branches'] = DB::connection('forex')->table('tblbranch')
            ->get();

        return view('window.rate_mainte.dpo_rate_maintenance', compact('result', 'menu_id'));
    }

    public function editDpo(Request $request) {
        $dpo_rate_deets = DB::connection('forex')->table('tbldpoindirate')
            ->join('tblbranch', 'tbldpoindirate.BranchID', 'tblbranch.BranchID')
            ->where('tbldpoindirate.EID' , '=' , $request->EID)
            ->get();

        return view('window.rate_mainte.update_dpo_rate_modal', compact('dpo_rate_deets'));
    }

    public function updateDpofxRate(Request $request) {
        $validator = Validator::make($request->all(), [
            'dpofx_rate' => 'required',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        } else {
            DB::connection('forex')->table('tbldpoindirate')
                ->where('tbldpoindirate.EID' , '=' , $request->eid)
                ->update([
                    'Rate' => $request->dpofx_rate,
                ]);
        }

        $message = "DPOFX rate update successful!";
        return redirect()->back()->with('message' , $message);
    }

    public function updateDPORate(Request $request) {
        $selected_branch_raw = $request->input('selected-branches');
        $selected_branches_parsed = explode(', ', $selected_branch_raw);
        $dpofx_rate = $request->input('dpofx-rate');

        $branch_id = DB::connection('forex')->table('tbldpoindirate')
            // ->join('tblbranch', 'tbldpoindirate.BranchCode', 'tblbranch.BranchCode')
            ->when(is_array($selected_branches_parsed), function ($query) use ($selected_branches_parsed) {
                return $query->whereIn('tbldpoindirate.BranchID', $selected_branches_parsed);
            }, function ($query) use ($selected_branches_parsed) {
                return $query->where('tbldpoindirate.BranchID', $selected_branches_parsed);
            })
            ->orderBy('tbldpoindirate.EID', 'DESC')
            ->get();

        $pluck_eids = $branch_id->pluck('EID')->toArray();

        // Update rates for each fetched record
        foreach ($pluck_eids as $EID_index => $EIDS) {
            DB::connection('forex')->table('tbldpoindirate')
                ->where('tbldpoindirate.EID', $EIDS)
                ->update([
                    'Rate' => $dpofx_rate
                ]);
        }

        $message = "DPOFX rate update successful!";
        return redirect()->back()->with('message' , $message);
    }
}
