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

class AdminSerialsController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:PENDING SERIALS,VIEW')->only(['show']);
        $this->middleware('check.access.permission:PENDING SERIALS,ADD')->only(['show']);
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        // $result['pending_serials'] = DB::connection('forex')->table('tbladminbuyingtransact')
        //     ->leftJoin('tblcurrency' , 'tbladminbuyingtransact.CurrencyID' , 'tblcurrency.CurrencyID')
        //     ->leftJoin('tbladminforexserials' , 'tbladminbuyingtransact.AFTDID' , '=' , 'tbladminforexserials.AFTDID')
        //     ->leftJoin('tbltransactiontype' , 'tbladminbuyingtransact.TransType' , '=' , 'tbltransactiontype.TTID')
        //     ->where('tbladminforexserials.FSStat' , '=' , 1)
        //     ->where('tbladminbuyingtransact.BranchID' , '=' , Auth::user()->getBranch()->BranchID)
        //     ->whereNull('tbladminforexserials.Serials')
        //     ->select(
        //         'tbladminbuyingtransact.TransactionNo',
        //         'tbladminbuyingtransact.TransactionDate',
        //         'tbladminforexserials.BillAmount',
        //         'tbltransactiontype.TransType',
        //         'tbladminforexserials.AFTDID',
        //         'tbladminforexserials.AFSID',
        //         'tbladminforexserials.Serials',
        //         'tblcurrency.Currency',
        //     )
        //     ->where('tbladminbuyingtransact.Voided', 0)
        //     // ->orderBy('tblcurrency.Currency', 'ASC')
        //     // ->orderBy('tblforexserials.FTDID', 'ASC')
        //     ->orderBy('tbladminbuyingtransact.TransactionDate', 'DESC')
        //     ->get();
        //     // ->paginate(10);

        $regular = DB::connection('forex')->table('tbladminforexserials AS fs')
            ->selectRaw('fs.AFSID, tc.Currency, fs.Serials, fs.BillAmount, fs.EntryDate, fs.Buffer')
            ->leftJoin('tbladminbuyingtransact AS fd', 'fs.AFTDID', '=', 'fd.AFTDID')
            ->leftJoin('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            ->whereNull('fs.Serials')
            ->whereNotNull('fs.AFTDID')
            ->groupBy('fs.AFSID', 'tc.Currency', 'fs.Serials', 'fs.BillAmount', 'fs.EntryDate', 'fs.Buffer');

        $financing = DB::connection('forex')->table('tbladminforexserials AS fs')
            ->selectRaw('fs.AFSID, tc.Currency, fs.Serials, fs.BillAmount, fs.EntryDate, fs.Buffer')
            ->leftJoin('tblbufferfinancing AS bf', 'fs.BFID', '=', 'bf.BFID')
            ->leftJoin('tblcurrency as tc', 'bf.CurrencyID', 'tc.CurrencyID')
            ->whereNull('fs.Serials')
            ->whereNotNull('fs.BFID')
            ->groupBy('fs.AFSID', 'tc.Currency', 'fs.Serials', 'fs.BillAmount', 'fs.EntryDate', 'fs.Buffer');

        $joined_queries = $regular->unionAll($financing);

        $result['pending_serials']  = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('AFSID as ID, Currency, Serials, BillAmount, EntryDate, Buffer')
            ->groupBy('ID', 'Currency', 'Serials', 'BillAmount', 'EntryDate', 'Buffer')
            ->orderBy('EntryDate', 'DESC')
            ->get();

        return view('admin_pending_serials.admin_pending_serials', compact('result', 'menu_id'));
    }
}
