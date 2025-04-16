<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Validator;
use App;
use Lang;
use App\Admin;
use App\Models\User;
use DB;
use Hash;
use Auth;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use App\Helpers\CreateNotifications;

class AdminTransCapController extends Controller {
    public function show(Request $request) {
        $result['trans_cap'] = DB::connection('forex')->table('tblfxtranscap as txc')
            ->selectRaw('tblbranch.BranchID, tblbranch.BranchCode, SUM(txc.TranscapAmount) as TranscapAmount')
            // ->join('pawnshop.tblxusers', 'txc.UserID', 'pawnshop.tblxusers.UserID')
            ->join('tblbranch', 'txc.BranchID', 'tblbranch.BranchID')
            // ->where('txc.Transferred', 0)
            // ->where('txc.Received', 0)
            ->groupBy('tblbranch.BranchID', 'tblbranch.BranchCode')
            ->orderBy('tblbranch.BranchID', 'ASC')
            ->paginate(15);

        return view('admin_trans_cap.trans_cap', compact('result'));
    }

    public function details(Request $request) {
        $result['details'] = DB::connection('forex')->table('tblfxtranscap as txc')
            ->selectRaw('txc.TCID, txc.TCNo, txc.BranchID, txc.TranscapAmount, txc.Transferred, txc.Received')
            ->where('txc.BranchID', $request->get('branch_id'))
            ->groupBy('txc.TCID', 'txc.TCNo', 'txc.BranchID', 'txc.TranscapAmount', 'txc.Transferred', 'txc.Received')
            ->orderBy('txc.TCID', 'ASC')
            ->get();

        return view('admin_trans_cap.trans_c_details_modal', compact('result'));
    }

    public function transfer(Request $request) {
        $TCIDs = explode(", ", $request->input('TCIDs'));

        $trans_cap_query = DB::connection('forex')->table('tblfxtranscap as txc')
            ->when(is_array($TCIDs), function ($query) use ($TCIDs) {
                return $query->whereIn('txc.TCID', $TCIDs);
            }, function ($query) use ($TCIDs) {
                return  $query->where('txc.TCID', $TCIDs);
            });

        $trans_cap_query->clone()
            ->update([
                'Transferred' => 1
            ]);
    }
}
