<?php

namespace App\Helpers;

use Adldap\Laravel\Facades\Adldap;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;

class BufferStocksManagement
{
    public static function branch() {
        $branch_id = DB::connection('forex')->table('tblbranch as tb')
            ->selectRaw('tbx.BranchID as BranchID')
            ->join('pawnshop.tblxbranch as tbx', 'tb.BranchCode', 'tbx.BranchCode')
            ->where('tb.BranchID', Auth::user()->getBranch()->BranchID)
            ->value('BranchID');

        return [
            'branch_id' => $branch_id,
        ];
    }
    
    public static function breakDown(){
        $branch_id = self::branch()['branch_id'];
        $raw_date = Carbon::now('Asia/Manila');
        
        $query = DB::connection('forex')->table('tblbufferfinancing as tbf')
            ->join('tblcurrency as tc', 'tbf.CurrencyID', 'tc.CurrencyID')
            ->where('tbf.Received', 0);

        $BFID = $query->clone()->selectRaw('MAX(tbf.BFID)')
            ->value('BFID');

        $break_down = $query->clone()->selectRaw('tbf.BFNo, tc.Currency, tbf.DollarAmount, tbf.Principal, MAX(tbf.BFID) as BFID')
            ->where('tbf.BFID', $BFID)
            ->groupBy('tbf.BFNo', 'tc.Currency', 'tbf.DollarAmount', 'tbf.Principal')
            ->get();
        
        return $break_down; 
    }

    public static function serials(){
        $branch_id = self::branch()['branch_id'];
        $raw_date = Carbon::now('Asia/Manila');

        $query = DB::connection('forex')->table('tbladminforexserials as afs')
            ->join('tblbufferfinancing as tbf', 'afs.BFID','tbf.BFID')
            ->where('afs.Buffer', 1)
            ->where('afs.BufferType', 2)
            ->whereNull('afs.Serials')
            ->groupBy('afs.BillAmount');

        $BFID = $query->clone()->selectRaw('MAX(tbf.BFID)')
            ->value('BFID');

        $pending_serials_buffer = $query->clone()
            ->selectRaw('afs.BillAmount, COUNT(afs.BillAmount) as quantity, SUM(afs.BillAmount) as total_amount, MAX(tbf.BFID) as BFID')
            ->where('tbf.BFID', $BFID)
            ->get();

        return $pending_serials_buffer; 
    }
}
