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

class BulkLimitControllerMaintenance extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:SELLING LIMIT MAINTENANCE,VIEW')->only(['show', 'existing']);
        $this->middleware('check.access.permission:SELLING LIMIT MAINTENANCE,ADD')->only(['add', '']);
        $this->middleware('check.access.permission:SELLING LIMIT MAINTENANCE,EDIT')->only(['edit', 'update']);
        $this->middleware('check.access.permission:SELLING LIMIT MAINTENANCE,DELETE')->only(['delete']);
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['selling_limits'] = DB::connection('forex')->table('tblsellinglimit')
            ->join('accounting.tblcompany', 'tblsellinglimit.CompanyID', 'accounting.tblcompany.CompanyID')
            ->paginate(10);

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

        return view('window.bulk_limit_selling_mainte.bulk_limit', compact('result', 'menu_id'));
    }

    public function add(Request $request) {
        DB::connection('forex')->table('tblsellinglimit')
            ->insert([
                'Limit' => $request->input('selling_limit'),
                'CompanyID' => $request->input('company_id')
            ]);
    }

    public function edit(Request $request) {
        $selling_limit_details = DB::connection('forex')->table('tblsellinglimit')
            ->join('accounting.tblcompany', 'tblsellinglimit.CompanyID', 'accounting.tblcompany.CompanyID')
            ->where('tblsellinglimit.SLID', '=', $request->input('SLID'))
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

        return view('window.bulk_limit_selling_mainte.update_selling_limit_modal', compact('selling_limit_details', 'result'));
    }

    public function update(Request $request) {
        DB::connection('forex')->table('tblsellinglimit')
            ->where('tblsellinglimit.SLID', '=', $request->input('SLID'))
            ->update([
                'Limit' => $request->input('selling_limit'),
                'CompanyID' => $request->input('company_id'),
                'Active' => $request->input('status') == "true" ? 1 : 0
            ]);
    }

    public function delete(Request $request) {
        dd("test");
    }

    public function exisiting(Request $request) {
        $exisiting_limits = DB::connection('forex')->table('tblsellinglimit')
            ->get();

        $response = [
            'exisiting_limits' => $exisiting_limits
        ];

        return response()->json($response);
    }
}
