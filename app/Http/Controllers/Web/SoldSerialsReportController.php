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

class SoldSerialsReportController extends Controller {
    public function soldSerialsReports(Request $request) {
        $sesh_username = session('user_name');
        $pawnshop_connection = DB::connection('pawnshop');

        $result['user_data'] = $pawnshop_connection->table('tblxusers')
            ->where('tblxusers.Username', '=', $sesh_username)
            ->first();

        $result['branches'] = DB::connection('forex')->table('tblbranch')
            ->get();

        $result['currency'] = DB::connection('forex')->table('tblcurrency')
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->get();

        return view('reports.sold_serial_report.sold_serials_reports', compact('result'));
    }

    public function searchSoldSerialsReports(Request $request) {
        $pawnshop_connection = DB::connection('pawnshop');
        $branch_sold_serials = $request->get('branch_sold_serials');
        $date_to_sold_serials = $request->get('date_to_sold_serials');
        $date_from_sold_serials = $request->get('date_from_sold_serials');
        $selected_branches = $request->get('processed_branch_array');
        $selected_branches_parsed = explode(',' , $selected_branches);

        $search_sold_serials_report = DB::connection('forex')->table('tblsoldcurrdetails')
            ->join('tblbranch', 'tblsoldcurrdetails.BranchID', 'tblbranch.BranchID')
            ->join('tblcurrency', 'tblsoldcurrdetails.CurrencyID', 'tblcurrency.CurrencyID')
            ->join('pawnshop.tblxusers', 'tblsoldcurrdetails.UserID', 'pawnshop.tblxusers.UserID')
            ->join('tblsoldserials', 'tblsoldcurrdetails.SCID', 'tblsoldserials.SCID')
            ->join('tblforexserials', 'tblsoldserials.FSID', 'tblforexserials.FSID')
            ->join('pawnshop.tblxcustomer', 'tblsoldcurrdetails.CustomerID', 'tblxcustomer.CustomerID')
            ->when(is_array($selected_branches_parsed), function ($query) use ($selected_branches_parsed) {
                return $query->whereIn('tblsoldcurrdetails.BranchID', $selected_branches_parsed);
            }, function ($query) use ($selected_branches_parsed) {
                return $query->where('tblsoldcurrdetails.BranchID', $selected_branches_parsed);
            })
            ->whereBetween('tblsoldcurrdetails.DateSold', [$date_from_sold_serials, $date_to_sold_serials])
            ->select(
                'tblcurrency.Currency',
                'tblforexserials.Serials',
                'tblsoldserials.BillAmount',
                'tblsoldcurrdetails.RateUsed',
                'tblforexserials.EntryDate',
                'tblxusers.Name',
                'tblbranch.BranchCode',
                'tblsoldcurrdetails.DateSold',
                'tblsoldcurrdetails.TimeSold',
                'tblxcustomer.FullName'
            )
            ->orderBy('tblsoldcurrdetails.DateSold', 'ASC')
            ->get();

        $response = [
            'search_sold_serials_report' => $search_sold_serials_report,
        ];

        return response()->json($response);
    }
}
