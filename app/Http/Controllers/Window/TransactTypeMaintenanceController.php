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

class TransactTypeMaintenanceController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:TRANSACTION TYPE MAINTENANCE,VIEW')->only(['show', 'existing']);
        $this->middleware('check.access.permission:TRANSACTION TYPE MAINTENANCE,ADD')->only(['add', '']);
        $this->middleware('check.access.permission:TRANSACTION TYPE MAINTENANCE,EDIT')->only(['edit', 'update']);
        $this->middleware('check.access.permission:TRANSACTION TYPE MAINTENANCE,DELETE')->only(['delete']);
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['transact_types'] = DB::connection('forex')->table('tbltransactiontype')
            ->paginate(10);

        return view('window.transact_types_mainte.transact_types', compact('result', 'menu_id'));
    }

    public function add(Request $request) {
        DB::connection('forex')->table('tbltransactiontype')
            ->insert([
                'TransType' => $request->input('transact_type')
            ]);
    }

    public function edit(Request $request) {
        $transact_type_details = DB::connection('forex')->table('tbltransactiontype')
            ->where('tbltransactiontype.TTID', '=', $request->input('TTID'))
            ->get();

        return view('window.transact_types_mainte.update_transact_type_modal', compact('transact_type_details'));
    }

    public function update(Request $request) {
        DB::connection('forex')->table('tbltransactiontype')
            ->where('tbltransactiontype.TTID', '=', $request->input('TTID'))
            ->update([
                'TransType' => $request->input('transact_type'),
                'Active' => $request->input('status') == "true" ? 1 : 0
            ]);
    }

    public function delete(Request $request) {
        DB::connection('forex')->table('tbltransactiontype')
            ->where('tbltransactiontype.TTID', '=', $request->input('TTID'))
            ->delete();
    }
}
