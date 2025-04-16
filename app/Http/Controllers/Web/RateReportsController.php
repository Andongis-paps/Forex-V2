<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Validator;
use App;
use Lang;
use App\Admin;
use Illuminate\Support\Carbon;

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

class RateReportsController extends Controller {
    public function rateReports(Request $request) {
        $sesh_username = session('user_name');
        $pawnshop_connection = DB::connection('pawnshop');

        // $result['rate_data'] = DB::connection('forex')->table('tblcurrentrate')
        //     ->join('pawnshop.tblxusers' , 'tblcurrentrate.UserID' , 'pawnshop.tblxusers.UserID')
        //     ->where('pawnshop.tblxusers.Username', '=', $sesh_username)
        //     ->first();

        $result['rate_data'] = $pawnshop_connection->table('tblxusers')
            ->where('tblxusers.Username', '=', $sesh_username)
            ->first();

        return view('reports.rate_report.rate_reports', compact('result'));
    }

    public function searchRateReports(Request $request) {
        $date_to = $request->get('date_to');
        $date_from = $request->get('date_from');
        $pawnshop_connection = DB::connection('pawnshop');

        $search_rate_report = DB::connection('forex')->table('tblcurrentrate')
            ->join('tblcurrency' , 'tblcurrentrate.CurrencyID' , 'tblcurrency.CurrencyID')
            ->join('pawnshop.tblxusers' , 'tblcurrentrate.UserID' , 'pawnshop.tblxusers.UserID')
            ->whereBetween('tblcurrentrate.EntryDate' , [$date_from , $date_to])
            ->select(
                'tblcurrency.Currency',
                'tblcurrentrate.Rate',
                'pawnshop.tblxusers.Name',
                'tblcurrentrate.EntryDate',
                'tblcurrentrate.EntryDateTime',
            )
            ->orderBy('tblcurrentrate.EntryDate', 'ASC')
            ->get();

        $response = [
            'search_rate_report' => $search_rate_report,
        ];

        return response()->json($response);
    }
}
