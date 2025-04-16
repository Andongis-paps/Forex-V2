<?php

namespace App\Http\Controllers\Window;

use App\Http\Controllers\Controller;
use Validator;
use App;
use Lang;
use App\Admin;
use DB;
use Hash;
use Auth;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\Query\Builder;

class RsetSeriesMaintenanceController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:R-SET SERIES MAINTENANCE,VIEW')->only(['show', 'existing']);
        $this->middleware('check.access.permission:R-SET SERIES MAINTENANCE,ADD')->only(['add', '']);
        $this->middleware('check.access.permission:R-SET SERIES MAINTENANCE,EDIT')->only(['edit', 'update']);
        $this->middleware('check.access.permission:R-SET SERIES MAINTENANCE,DELETE')->only(['delete']);
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['r_set_series'] = DB::connection('forex')->table('tblrsetseries as rs')
            ->selectRaw('rs.RSID, tc.CompanyName, rs.RSetSeries, rs.EntryDate, rs.EntryTime, tbx.Name')
            ->join('accounting.tblcompany as tc', 'rs.CompanyID', 'tc.CompanyID')
            ->join('pawnshop.tblxusers as tbx', 'rs.UserID', 'tbx.UserID')
            ->groupBy('rs.RSID', 'rs.RSetSeries', 'rs.EntryDate', 'rs.EntryTime', 'tbx.Name')
            ->paginate(20);

        $result['company'] = DB::connection('forex')->table('tblbranch')
            ->selectRaw('accounting.tblcompany.CompanyID, accounting.tblcompany.CompanyName')
            ->join('pawnshop.tblxbranch', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.BranchCode')
            ->join('accounting.tblsegmentgroup', 'pawnshop.tblxbranch.BranchID', 'accounting.tblsegmentgroup.BranchID')
            ->join('accounting.tblcompany', 'accounting.tblsegmentgroup.CompanyID', 'accounting.tblcompany.CompanyID')
            ->join('accounting.tblsegments', 'accounting.tblsegmentgroup.SegmentID', 'accounting.tblsegments.SegmentID')
            ->where('accounting.tblsegments.SegmentID', '=', 3)
            ->whereIn('accounting.tblcompany.CompanyID', [3, 4, 5, 6, 15])
            ->groupBy('accounting.tblcompany.CompanyID', 'accounting.tblcompany.CompanyName')
            ->orderBy('accounting.tblcompany.CompanyName', 'ASC')
            ->get();

        return view('window.r_set_series_mainte.r_set_series', compact('result', 'menu_id'));
    }
    
    public function add(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        DB::connection('forex')->table('tblrsetseries')
            ->insert([
                'CompanyID' => $request->input('company_id'),
                'RSetSeries' => $request->input('r_set_series'),
                'UserID' => $request->get('matched_user_id'),
                'EntryDate' => $raw_date->toDateString(),
                'EntryTime' => $raw_date->toTimeString(),
            ]);
    }

    public function exisisting(Request $request) {
        $exisisting_series = DB::connection('forex')->table('tblrsetseries')
            ->get();

        $response = [
            'exisisting_series' => $exisisting_series
        ];

        return response()->json($response);
    }

    public function edit(Request $request){
        $details = DB::connection('forex')->table('tblrsetseries as rs')
            ->selectRaw('rs.RSID, tc.CompanyName, rs.CompanyID, rs.RSetSeries')
            ->join('pawnshop.tblxusers as tbx', 'rs.UserID', 'tbx.UserID')
            ->join('accounting.tblcompany as tc', 'rs.CompanyID', 'tc.CompanyID')
            ->where('rs.RSID' , '=' , $request->RSID)
            ->groupBy('rs.RSID', 'tc.CompanyName', 'rs.CompanyID', 'rs.RSetSeries')
            ->get();

        $result['company'] = DB::connection('forex')->table('tblbranch')
            ->selectRaw('accounting.tblcompany.CompanyID, accounting.tblcompany.CompanyName')
            ->join('pawnshop.tblxbranch', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.BranchCode')
            ->join('accounting.tblsegmentgroup', 'pawnshop.tblxbranch.BranchID', 'accounting.tblsegmentgroup.BranchID')
            ->join('accounting.tblcompany', 'accounting.tblsegmentgroup.CompanyID', 'accounting.tblcompany.CompanyID')
            ->join('accounting.tblsegments', 'accounting.tblsegmentgroup.SegmentID', 'accounting.tblsegments.SegmentID')
            ->where('accounting.tblsegments.SegmentID', '=', 3)
            ->whereIn('accounting.tblcompany.CompanyID', [3, 4, 5, 6, 15])
            ->groupBy('accounting.tblcompany.CompanyID', 'accounting.tblcompany.CompanyName')
            ->orderBy('accounting.tblcompany.CompanyName', 'ASC')
            ->get();

        return view('window.r_set_series_mainte.update_r_set_series_modal', compact('details' , 'result'));
    }

    public function update(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        DB::connection('forex')->table('tblrsetseries as rs')
            ->where('rs.RSID', '=', $request->RSID)
            ->update([
                'CompanyID' => $request->input('company_id'),
                'RSetSeries' => $request->input('r_set_series'),
                // 'UserID' => $request->get('matched_user_id'),
                // 'EntryDate' => $raw_date->toDateString(),
                // 'EntryTime' => $raw_date->toTimeString(),
            ]);
    }

    public function delete(Request $request) {
        DB::connection('forex')->table('tblrsetseries as rs')
            ->where('rs.RSID', '=', $request->input('RSID'))
            ->delete();
    }
}
