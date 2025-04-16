<?php

namespace App\Http\Controllers\Window;
use App\Http\Controllers\Controller;
use Validator;
use App;
use Lang;
use App\Admin;

use DB;
//for password encryption or hash protected
use Hash;
//use App\Administrator;

//for authenitcate login data
use Auth;
use Dotenv\Validator as DotenvValidator;
use Session;
//for requesting a value
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;

class DenominationMaintenanceController extends Controller {
    public function denominations(Request $request) {
        $result['currency_denom'] = DB::connection('forex')->table('tblcurrency')
            ->join('tblcountries', 'tblcurrency.CountryID', 'tblcountries.CountryID')
            ->orderBy('tblcurrency.Currency' , 'ASC')
            ->paginate(20);

        return view('window.denom_mainte.denomination', compact('result'));
    }

    public function addDenominations(Request $request) {
        $result['currencies'] = DB::connection('forex')->table('tblcurrency')
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->get();

        $result['transact_type'] = DB::connection('forex')->table('tbltransactiontype')
            ->orderBy('tbltransactiontype.TTID', 'ASC')
            ->get();

        return view('window.denom_mainte.add_denominations', compact('result'));
    }

    public function saveDenomination(Request $request) {
        $currency_id = $request->input('currency');
        $transact_type_id = $request->input('transact-type');
        $denominations = $request->input('denominations');
        $message = 'Denomination/s Added!';

        $validator = Validator::make($request->all(), [
            'currency' => 'required',
            'denominations' => 'required|array',
            'denominations.*' => 'required',
        ], [
            'denominations.*.required' => 'The denomination is required.',
            'currency.required' => 'Currency is required.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            foreach ($denominations as $key => $denom_val) {
                DB::connection('forex')->table('tbldenominationmaintenance')->insert([
                    'CurrencyID' => $currency_id,
                    'BillAmount' => $denom_val,
                    'UserID' => session('user_id'),
                    'TTID' => $transact_type_id
                ]);
            }

            return redirect()->route('denominations')->with('message', $message);
        }
    }

    public function editDenomination(Request $request) {
        $result['denominations'] = DB::connection('forex')->table('tbldenominationmaintenance')
            ->join('tbltransactiontype', 'tbldenominationmaintenance.TTID', 'tbltransactiontype.TTID')
            ->where('tbldenominationmaintenance.CurrencyID', '=', $request->currency_id)
            ->get();

        $result['currency'] = DB::connection('forex')->table('tblcurrency')
            ->where('tblcurrency.CurrencyID', '=', $request->currency_id)
            ->first();

        return view('window.denom_mainte.edit_denominations', compact('result'));
    }
}
