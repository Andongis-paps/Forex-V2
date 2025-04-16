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
use Illuminate\Database\Query\Builder;

class BranchMaintenanceController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:BRANCH MAINTENANCE,VIEW')->only(['show']);
        $this->middleware('check.access.permission:BRANCH MAINTENANCE,ADD')->only(['add', '']);
        $this->middleware('check.access.permission:BRANCH MAINTENANCE,EDIT')->only(['edit', 'update']);
        $this->middleware('check.access.permission:BRANCH MAINTENANCE,DELETE')->only(['']);
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['branches'] = DB::connection('forex')->table('tblbranch as tb')
            ->selectRaw('tb.BranchID, tx.BranchCode, tx.BranchID as pxBranchID, tx.Address, tx.OM, tx.DistantLocation')
            ->join('pawnshop.tblxbranch as tx', 'tb.BranchCode', 'tx.BranchCode')
            ->groupBy('tb.BranchID', 'tx.BranchCode', 'pxBranchID', 'tx.Address', 'tx.OM', 'tx.DistantLocation')
            ->orderByRaw('LENGTH(tx.BranchCode) , tx.BranchCode')
            ->paginate(20);

        return view('window.branch_mainte.branch_maintenance', compact('result', 'menu_id'));
    }

    public function add(Request $request) {
        $branch_code = $request->input('branch_code');
        $branch_name = $request->input('branch_name');
        $branch_address = $request->input('branch_address');
        $branch_telno = $request->input('branch_telno');

        $regex = '/^[A-Z0-9\s]*(?<![\[{}\]])[A-Z0-9\s]+(?![\]{}\]])$/';

        $validator = Validator::make($request->all(), [
            'branch_code' => 'required|regex:' .$regex,
            'branch_name' => 'required',
            // 'branch_address' => 'required',
            // 'branch_telno' => 'required',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        } else {
            DB::connection('forex')->table('tblbranch')
                ->where('tblbranch.BranchID' , '=' , $request->branch_id)
                ->insert([
                    'BranchCode' => $branch_code,
                    'BranchName' => $branch_name,
                    'Address' => $branch_address,
                    'Telno' => $branch_telno,
                ]);
        }

        $message = "New branch added!";
        return redirect()->back()->with('message' , $message);
    }

    public function edit(Request $request) {
        $branch_details = DB::connection('forex')->table('tblbranch')
            ->select('tblbranch.BranchID', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.BranchID as pxBranchID', 'pawnshop.tblxbranch.Address', 'pawnshop.tblxbranch.OM')
            ->join('pawnshop.tblxbranch', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.BranchCode')
            ->where('tblbranch.BranchID' , '=' , $request->BranchID)
            ->get();

        return view('window.branch_mainte.update_branch_mainte_modal')->with('branch_details' , $branch_details);
    }

    public function update(Request $request) {
        $regex = '/^[A-Z0-9\s]*(?<![\[{}\]])[A-Z0-9\s]+(?![\]{}\]])$/';
        $branch_code = $request->input('branch_code');
        $branch_name = $request->input('branch_name');
        $branch_address = $request->input('branch_address');
        $branch_telno = $request->input('branch_telno');

        $validator = Validator::make($request->all(), [
            'branch_code' => 'required|regex:' .$regex,
            'branch_name' => 'required',
            'branch_address' => 'required',
            'branch_telno' => 'required',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        } else {
            DB::connection('forex')->table('tblbranch')
                ->where('tblbranch.BranchID' , '=' , $request->branch_id)
                ->update([
                    'BranchCode' => $branch_code,
                    'BranchName' => $branch_name,
                    'Address' => $branch_address,
                    'Telno' => $branch_telno,
                ]);
        }

        $message = "Branch update successful!";
        return redirect()->back()->with('message' , $message);
    }

    public function searchFetch(Request $request) {
        $keyword = $request->get('search_word');

        $branches = DB::connection('forex')->table('tblbranch')
            ->where('tblbranch.BranchCode' , 'LIKE', "%{$keyword}%")
            ->get();

        return response()->json($branches);
    }
}
