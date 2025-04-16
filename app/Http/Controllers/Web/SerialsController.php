<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Validator;
use App;
use Lang;
use App\Admin;
use App\Models\User;
use DB;
use Illuminate\Support\Carbon;
use Hash;
use Auth;
use Session;
use Illuminate\Http\Request;

class SerialsController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:PENDING SERIALS,VIEW')->only(['pendingSerials']);
        $this->middleware('check.access.permission:PENDING SERIALS,ADD')->only(['pendingSerials']);
        $this->middleware('check.access.permission:PENDING SERIALS,EDIT')->only(['pendingSerials']);
    }

    public function addBillSerial(Request $request) {
        $date = Carbon::now('Asia/Manila');
        $date_time_now = $date->toDateTimeString();
        $sesh_username = session('user_name');

        $forex_fsid = $request->input('serial-fsid');
        $forex_ftdid = $request->input('serial-ftdid');
        $forex_scid = $request->input('forex-scid-serial');
        $denom_bill_amnt = $request->input('serial-total-amount');
        $rate_used_serial_sell = $request->input('serial-rate-used');
        $true_denom_total_amnt = $request->input('true-serial-total-amount');
        $current_amount_serial = $request->input('get-soldtransact-current-amount');

        $result['user_id'] = DB::connection('pawnshop')->table('tblxusers')
            ->where('tblxusers.Username' , '=' , $sesh_username)
            ->select(
                'tblxusers.UserID',
            )
            ->first();

        $new_bill_amnt = $current_amount_serial + $denom_bill_amnt;

        DB::connection('forex')->table('tblsoldcurrdetails')
            ->where('tblsoldcurrdetails.SCID', '=', $forex_scid)
            ->update([
                'CurrAmount' => $new_bill_amnt,
                'AmountPaid' => $new_bill_amnt * $rate_used_serial_sell
            ]);

        DB::connection('forex')->table('tblsoldserials')
            ->insert([
                'SCID' => $forex_scid,
                'FSID' => $forex_fsid,
                'UserID' => $result['user_id']->UserID,
                'EntryDate' => $date_time_now,
                'BillAmount' => $denom_bill_amnt,
            ]);

        DB::connection('forex')->table('tblforexserials')
            ->where('tblforexserials.FSID', '=', $forex_fsid)
            ->update([
                'Sold' => 1
            ]);

        $message = "Bill/s Added!";
        return redirect()->back()->with('message' , $message);
    }

    public function pendingSerials(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $r_set =  session('time_toggle_status') == 1 ? 'O' : '';

        $menu_id = $this->MenuID;

        $result['pending_serials'] = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->select('fd.TransactionNo', 'fd.TransactionDate', 'tblforexserials.BillAmount', 'tbltransactiontype.TransType', 'tblforexserials.FTDID', 'tblforexserials.FSID', 'tblforexserials.Serials', 'tblcurrency.Currency')
            ->leftJoin('tblcurrency' , 'fd.CurrencyID' , 'tblcurrency.CurrencyID')
            ->leftJoin('tblforexserials' , 'fd.FTDID' , '=' , 'tblforexserials.FTDID')
            ->leftJoin('tbltransactiontype' , 'fd.TransType' , '=' , 'tbltransactiontype.TTID')
            ->whereNull('tblforexserials.Serials')
            ->where('tblforexserials.FSStat' , '=' , 1)
            ->when($r_set == 'O', function($query) use ($r_set) {
                return $query->where('fd.Rset', '=', $r_set);
            })
            ->where('fd.Voided' , 0)
            ->where('fd.BranchID' , '=' , Auth::user()->getBranch()->BranchID)
            // ->orderBy('tblcurrency.Currency', 'ASC')
            // ->orderBy('tblforexserials.FTDID', 'ASC')
            ->orderBy('fd.TransactionDate', 'DESC')
            ->get();

        return view('pending_serials.pending_serials', compact('result', 'menu_id'));
    }

    public function dupeSerial(Request $request) {
        $test_array = [];
        $raw_date = Carbon::now('Asia/Manila');
        $FSIDs = explode(",", $request->input('FSIDs'));
        $serials = explode(",", $request->input('serials'));

        $branch_serials = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('fs.Serials')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->when(is_array($FSIDs), function ($query) use ($FSIDs) {
                return $query->whereIn('FSID', $FSIDs);
            }, function ($query) use ($FSIDs) {
                return $query->where('FSID', $FSIDs);
            })
            ->whereNull('fs.STMDID')
            ->where('fs.Sold', 0)
            ->where('fd.Voided', 0)
            ->where('fs.SoldToManila', 0)
            ->where('fd.BranchID', Auth::user()->getBranch()->BranchID)
            ->groupBy('fs.Serials', 'fs.FSID', 'fs.SoldToManila');

        $admin_serials = DB::connection('forex')->table('tbladminforexserials AS fs')
            ->selectRaw('fs.Serials')
            ->leftJoin('tbladminbuyingtransact AS fd', 'fs.aftdid', '=', 'fd.aftdid')
            ->leftJoin('tblbufferfinancing AS bf', 'fs.bfid', '=', 'bf.bfid')
            ->when(is_array($FSIDs), function ($query) use ($FSIDs) {
                return $query->whereIn('AFSID', $FSIDs);
            }, function ($query) use ($FSIDs) {
                return $query->where('AFSID', $FSIDs);
            })
            ->whereNull('fs.STMDID')
            ->where('fs.Sold', 0)
            ->where('fd.Voided', 0)
            ->where('fs.SoldToManila', 0)
            ->groupBy('fs.Serials', 'fs.AFSID', 'fs.SoldToManila');

        $joined_queries = $branch_serials->unionAll($admin_serials);

        $serial_stocks = DB::connection('forex')->query()->fromSub($joined_queries, 'combined');
            
        $dupe_serials = $serial_stocks->clone()->pluck('Serials')
            ->toArray();

        $difference = array_diff($serials, $dupe_serials);

        $serials_branch = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('fs.Serials')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->when(is_array($difference), function ($query) use ($difference) {
                return $query->whereIn('Serials', $difference);
            }, function ($query) use ($difference) {
                return $query->where('Serials', $difference);
            })
            ->whereNull('fs.STMDID')
            ->where('fs.Sold', 0)
            ->where('fd.Voided', 0)
            ->where('fs.SoldToManila', 0)
            ->where('fd.BranchID', Auth::user()->getBranch()->BranchID)
            ->groupBy('fs.Serials', 'fs.FSID', 'fs.SoldToManila');

        $serials_admin = DB::connection('forex')->table('tbladminforexserials AS fs')
            ->selectRaw('fs.Serials')
            ->leftJoin('tbladminbuyingtransact AS fd', 'fs.aftdid', '=', 'fd.aftdid')
            ->leftJoin('tblbufferfinancing AS bf', 'fs.bfid', '=', 'bf.bfid')
            ->when(is_array($difference), function ($query) use ($difference) {
                return $query->whereIn('Serials', $difference);
            }, function ($query) use ($difference) {
                return $query->where('Serials', $difference);
            })
            ->whereNull('fs.STMDID')
            ->where('fs.Sold', 0)
            ->where('fd.Voided', 0)
            ->where('fs.SoldToManila', 0)
            ->groupBy('fs.Serials', 'fs.AFSID', 'fs.SoldToManila');

        $queries_joined = $serials_branch->unionAll($serials_admin);

        $stocks = DB::connection('forex')->query()->fromSub($queries_joined, 'combined')
                ->selectRaw('Serials');

        $dupe_serials = $stocks->clone()
            ->get();

        $boolean = $stocks->clone()
            ->exists();

        $response = [
            'boolean' => $boolean,
            'dupe_serials' => $dupe_serials
        ];

        return response()->json($response);
    }
}
