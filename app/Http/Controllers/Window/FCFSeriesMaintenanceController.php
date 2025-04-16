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

class FCFSeriesMaintenanceController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:FC FORM SERIES,VIEW')->only(['show', 'existing']);
        $this->middleware('check.access.permission:FC FORM SERIES,ADD')->only(['add', '']);
        $this->middleware('check.access.permission:FC FORM SERIES,EDIT')->only(['edit', 'update']);
        $this->middleware('check.access.permission:FC FORM SERIES,DELETE')->only(['delete']);
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['fc_form_series'] = DB::connection('forex')->table('tblfcformseries')
            // ->join('pawnshop.tblxusers', 'tblfcformseries.UserID', 'pawnshop.tblxusers.UserID')
            ->join('accounting.tblcompany', 'tblfcformseries.CompanyID', 'accounting.tblcompany.CompanyID')
            ->select(
                'tblfcformseries.FCFSID',
                'accounting.tblcompany.CompanyName',
                'tblfcformseries.FormSeries',
                'tblfcformseries.RSet',
                'tblfcformseries.EntryDate',
                'tblfcformseries.EntryTime',
                // 'pawnshop.tblxusers.Name',
            )
            ->paginate(20);

        $result['company'] = DB::connection('forex')->table('tblbranch')
            ->join('pawnshop.tblxbranch', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.BranchCode')
            ->join('accounting.tblsegmentgroup', 'pawnshop.tblxbranch.BranchID', 'accounting.tblsegmentgroup.BranchID')
            ->join('accounting.tblcompany', 'accounting.tblsegmentgroup.CompanyID', 'accounting.tblcompany.CompanyID')
            ->join('accounting.tblsegments', 'accounting.tblsegmentgroup.SegmentID', 'accounting.tblsegments.SegmentID')
            ->select(
                'accounting.tblcompany.CompanyID',
                'accounting.tblcompany.CompanyName',
            )
            ->where('accounting.tblsegments.SegmentID', '=', 3)
            ->groupBy('accounting.tblcompany.CompanyID')
            ->orderBy('accounting.tblcompany.CompanyID', 'ASC')
            ->get();

        return view('window.fc_form_series_mainte.fc_form_series_admin', compact('result', 'menu_id'));
    }

    public function add(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        DB::connection('forex')->table('tblfcformseries')
            ->insert([
                'CompanyID' => $request->input('company_id'),
                'RSet' => $request->input('radio-rset'),
                'FormSeries' => $request->input('fc_form_series'),
                'UserID' => $request->get('matched_user_id'),
                'EntryDate' => $raw_date->toDateString(),
                'EntryTime' => $raw_date->toTimeString(),
            ]);
    }

    public function edit(Request $request) {
        $FCF_series_details = DB::connection('forex')->table('tblfcformseries')
            ->join('pawnshop.tblxusers', 'tblfcformseries.UserID', 'pawnshop.tblxusers.UserID')
            ->join('accounting.tblcompany', 'tblfcformseries.CompanyID', 'accounting.tblcompany.CompanyID')
            ->select(
                'tblfcformseries.FCFSID',
                'accounting.tblcompany.CompanyID',
                'accounting.tblcompany.CompanyName',
                'tblfcformseries.FormSeries',
                'tblfcformseries.RSet',
                'tblfcformseries.EntryDate',
                'tblfcformseries.EntryTime',
                'pawnshop.tblxusers.Name',
            )
            ->where('tblfcformseries.FCFSID' , '=' , $request->FCFSID)
            ->get();

        $result['company'] = DB::connection('forex')->table('tblbranch')
            ->join('pawnshop.tblxbranch', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.BranchCode')
            ->join('accounting.tblsegmentgroup', 'pawnshop.tblxbranch.BranchID', 'accounting.tblsegmentgroup.BranchID')
            ->join('accounting.tblcompany', 'accounting.tblsegmentgroup.CompanyID', 'accounting.tblcompany.CompanyID')
            ->join('accounting.tblsegments', 'accounting.tblsegmentgroup.SegmentID', 'accounting.tblsegments.SegmentID')
            ->select(
                'accounting.tblcompany.CompanyID',
                'accounting.tblcompany.CompanyName',
            )
            ->where('accounting.tblsegments.SegmentID', '=', 3)
            ->groupBy('accounting.tblcompany.CompanyID')
            ->orderBy('accounting.tblcompany.CompanyID', 'ASC')
            ->get();

        return view('window.fc_form_series_mainte.update_fc_form_series_mainte_modal', compact('FCF_series_details' , 'result'));
    }

    public function update(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        DB::connection('forex')->table('tblfcformseries')
            ->where('tblfcformseries.FCFSID', '=', $request->FCFSID)
            ->update([
                'CompanyID' => $request->input('company_id'),
                'RSet' => $request->input('radio-rset'),
                'FormSeries' => $request->input('fc_form_series'),
                // 'UserID' => $request->get('matched_user_id'),
                // 'EntryDate' => $raw_date->toDateString(),
                // 'EntryTime' => $raw_date->toTimeString(),
            ]);
    }

    public function delete(Request $request) {
        DB::connection('forex')->table('tblfcformseries')
            ->where('tblfcformseries.FCFSID', '=', $request->input('FCFSID'))
            ->delete();
    }

    public function existing(Request $request) {
        $exisiting_series = DB::connection('forex')->table('tblfcformseries')
            ->get();

        $response = [
            'exisiting_series' => $exisiting_series
        ];

        return response()->json($response);
    }
}
