<?php

namespace App\Helpers;

use Adldap\Laravel\Facades\Adldap;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;

class TransafersManagement {
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

    public static function buffers(){
        $branch_id = self::branch()['branch_id'];
        $raw_date = Carbon::now('Asia/Manila');

        $unacknowledged = DB::connection('forex')->table('tbltransferforex as tfx')
            ->selectRaw('tfx.TransferForexID as TFID, tfx.TransferForexNo as TFXNo, tbf.BufferID, tbf.BufferNo, tbx.BranchCode, tfx.Remarks, tfx.TransferDate')
            ->join('tblbuffertransfer as tbf', 'tfx.TransferForexID', 'tbf.TFID')
            ->join('tblbranch as tb', 'tb.BranchID', 'tfx.BranchID')
            ->join('pawnshop.tblxbranch as tbx', 'tb.BranchCode', 'tbx.BranchCode')
            ->where('tfx.Voided', '=', 0)
            ->where('tbf.BufferTransfer', '<>', 2)
            ->where('tbf.BranchID', Auth::user()->getBranch()->BranchID)
            ->where('tfx.TransferDate', '>', $raw_date->parse('2025-01-01'))
            ->groupBy('TFID', 'TFXNo', 'tbf.BufferID', 'tbf.BufferNo', 'tbx.BranchCode', 'tfx.Remarks', 'tfx.TransferDate')
            ->get();

        return $unacknowledged; 
    }
}
