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

class TagsMaintenanceController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:BILL TAGS MAINTENANCE,VIEW')->only(['show', 'existing']);
        $this->middleware('check.access.permission:BILL TAGS MAINTENANCE,ADD')->only(['add', '']);
        $this->middleware('check.access.permission:BILL TAGS MAINTENANCE,EDIT')->only(['edit', 'update']);
        $this->middleware('check.access.permission:BILL TAGS MAINTENANCE,DELETE')->only(['delete']);
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['bill_tags'] = DB::connection('forex')->table('tblbillstatus')
            ->paginate(10);

        return view('window.bill_tags_mainte.bill_tags', compact('result', 'menu_id'));
    }

    public function add(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        DB::connection('forex')->table('tblbillstatus')
            ->insert([
                'BillStatus' => $request->input('tag_description'),
                'UserID' => $request->get('matched_user_id'),
                'EntryDate' => $raw_date->toDateString(),
                'EntryTime' => $raw_date->toTimeString(),
            ]);
    }

    public function edit(Request $request) {
        $bill_tag_details = DB::connection('forex')->table('tblbillstatus')
            ->where('tblbillstatus.BillStatID', '=', $request->input('BillStatID'))
            ->get();

        return view('window.bill_tags_mainte.update_bill_tag_modal', compact('bill_tag_details'));
    }

    public function update(Request $request) {
        DB::connection('forex')->table('tblbillstatus')
            ->where('tblbillstatus.BillStatID', '=', $request->input('BillStatID'))
            ->update([
                'BillStatus' => $request->input('tag_description'),
            ]);
    }

    public function delete(Request $request) {
        DB::connection('forex')->table('tblbillstatus')
            ->where('tblbillstatus.BillStatID', '=', $request->input('BillStatID'))
            ->delete();
    }
}
