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

class AdminOnholdController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:RESERVED STOCKS,VIEW')->only(['show', 'details']);
        $this->middleware('check.access.permission:RESERVED STOCKS,ADD')->only(['revert']);
        $this->middleware('check.access.permission:RESERVED STOCKS,EDIT')->only(['details']);
        $this->middleware('check.access.permission:RESERVED STOCKS,DELETE')->only(['']);
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $branch_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('fd.CurrencyID, COUNT(fd.CurrencyID) - SUM(CASE WHEN fs.Queued = 1 THEN 1 ELSE 0 END) AS Cnt, SUM(fs.BillAmount) AS BillAmt, SUM(fs.BillAmount * d.SinagRateBuying) AS Principal')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tbldenom AS d', 'fs.DenomID', '=', 'd.DenomID')
            // ->where('fs.Buffer', 0)
            ->where('fs.Queued', 0)
            ->whereNull('fs.QueuedBy')
            ->whereNotNull('fs.HeldBy')
            ->where('fs.Onhold', 1)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.Transfer', 1)
            ->whereNotNull('fs.FTDID')
            ->where('fs.Received', 1)
            ->where('fs.FSStat', 2)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->groupBy('fd.CurrencyID');

        $admin_stocks_query = DB::connection('forex')->table('tbladminforexserials AS fs')
            ->selectRaw('COALESCE(fd.CurrencyID, bf.CurrencyID) as CurrencyID, COUNT(COALESCE(fd.CurrencyID, bf.CurrencyID)) - SUM(CASE WHEN fs.Queued = 1 THEN 1 ELSE 0 END) AS Cnt, SUM(fs.BillAmount) AS BillAmt, SUM(fs.BillAmount * d.SinagRateBuying) AS Principal')
            ->leftJoin('tbladminbuyingtransact AS fd', 'fs.aftdid', '=', 'fd.aftdid')
            ->join('tbladmindenom AS d', 'fs.adenomid', '=', 'd.adenomid')
            ->leftJoin('tblbufferfinancing AS bf', 'fs.bfid', '=', 'bf.bfid')
            // ->where('fs.Buffer', 0)
            ->where('fs.Queued', 0)
            ->whereNull('fs.QueuedBy')
            ->whereNotNull('fs.HeldBy')
            ->where('fs.Onhold', 1)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.FSStat', 1)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->groupBy('CurrencyID');

        $result['held_bills'] = DB::connection('forex')->table('tblcurrency AS c')
            ->leftJoinSub($branch_stocks_query, 'bs', function($join) {
                $join->on('c.currencyid', '=', 'bs.CurrencyID');
            })
            ->leftJoinSub($admin_stocks_query, 'ad', function($join) {
                $join->on('c.currencyid', '=', 'ad.CurrencyID');
            })
            ->selectRaw('c.CurrencyID, c.Currency, c.CurrAbbv,
                IFNULL(bs.Cnt, 0) + IFNULL(ad.Cnt, 0) AS total_bill_count,
                IFNULL(bs.BillAmt, 0) + IFNULL(ad.BillAmt, 0) AS total_onhold_amount,
                IFNULL(bs.Principal, 0) + IFNULL(ad.Principal, 0) AS total_principal'
            )
            ->havingRaw('total_onhold_amount > 0')
            ->orderBy('c.currency')
            ->paginate(15);

        return view('onhold_bills_admin.held_bills', compact('result', 'menu_id'));
    }

    public function details(Request $request) {
        $branch_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('fd.CurrencyID, fs.FSID, tu.Name, fs.OnholdDate, fs.Serials, fs.BillAmount, tt.TransType, d.SinagRateBuying, fd.Rset, SUM(fs.BillAmount * d.SinagRateBuying) as principal, 2 as source_type')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tbldenom AS d', 'fs.DenomID', '=', 'd.DenomID')
            ->join('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            ->join('pawnshop.tblxusers as tu', 'fs.HeldBy', 'tu.UserID')
            // ->where('fs.Buffer', 0)
            ->whereNotNull('fs.HeldBy')
            ->where('fs.Onhold', 1)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.Transfer', 1)
            ->whereNotNull('fs.FTDID')
            ->where('fs.Received', 1)
            ->where('fs.FSStat', 2)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->where('fd.CurrencyID', $request->get('currency_id'))
            ->groupBy('fd.CurrencyID', 'fs.OnholdDate', 'tu.Name', 'fs.FSID', 'fs.Serials', 'fs.BillAmount', 'tt.TransType', 'd.SinagRateBuying', 'fd.Rset');

        $admin_stocks_query = DB::connection('forex')->table('tbladminforexserials AS fs')
            ->selectRaw('COALESCE(fd.CurrencyID, bf.CurrencyID) as CurrencyID, fs.AFSID, tu.Name, fs.OnholdDate, fs.Serials, fs.BillAmount, tt.TransType, d.SinagRateBuying, COALESCE(fd.Rset, bf.Rset) as Rset, SUM(fs.BillAmount * d.SinagRateBuying) as principal, 1 as source_type')
            ->leftJoin('tbladminbuyingtransact AS fd', 'fs.aftdid', '=', 'fd.aftdid')
            ->join('tbladmindenom AS d', 'fs.adenomid', '=', 'd.adenomid')
            ->leftJoin('tblbufferfinancing AS bf', 'fs.bfid', '=', 'bf.bfid')
            ->leftJoin('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            ->join('pawnshop.tblxusers as tu', 'fs.HeldBy', 'tu.UserID')
            // ->where('fs.Buffer', 0)
            ->whereNotNull('fs.HeldBy')
            ->where('fs.Onhold', 1)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.FSStat', 1)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereRaw('COALESCE(fd.CurrencyID, bf.CurrencyID) = ?' , $request->get('currency_id'))
            ->groupBy('CurrencyID', 'fs.OnholdDate', 'tu.Name', 'fs.AFSID', 'fs.Serials', 'fs.BillAmount', 'tt.TransType', 'd.SinagRateBuying', 'Rset');

        $joined_queries = $branch_stocks_query->unionAll($admin_stocks_query);

        $held_stock_details = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('FSID as ID, Rset, CurrencyID, DATE(OnholdDate) as DateHeld, Name, BillAmount, Serials, SinagRateBuying, SUM(BillAmount) as total_bill_amount, SUM(principal) as total_principal, source_type')
            ->groupBy('ID', 'Rset', 'CurrencyID', 'DateHeld', 'Name', 'BillAmount', 'Serials', 'SinagRateBuying', 'source_type')
            ->get();

        $denoms =  DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('GROUP_CONCAT(DISTINCT BillAmount ORDER BY BillAmount DESC) as denominations')
            ->get();

        $response = [
            'denoms' => $denoms ,
            'held_stock_details' => $held_stock_details
        ];

        return response()->json($response);
    }

    public function revert(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        $exploded_fsids = explode(", ", $request->input('FSIDs'));
        $exploded_afsids = explode(", ", $request->input('AFSIDs'));

        if (!is_null($request->input('FSIDs'))) {
            DB::connection('forex')->table('tblforexserials')
                ->when(is_array($exploded_fsids), function ($query) use ($exploded_fsids) {
                    return $query->whereIn('tblforexserials.FSID', $exploded_fsids);
                }, function ($query) use ($exploded_fsids) {
                    return $query->where('tblforexserials.FSID', $exploded_fsids);
                })
                ->update([
                    'tblforexserials.Onhold' => 0,
                    'tblforexserials.HeldBy' => null,
                    'tblforexserials.Onholddate' => null,
                ]);

            DB::connection('forex')->table('tblonholdstockstrail')
                ->when(is_array($exploded_fsids), function ($query) use ($exploded_fsids) {
                    return $query->whereIn('tblonholdstockstrail.FSID', $exploded_fsids);
                }, function ($query) use ($exploded_fsids) {
                    return $query->where('tblonholdstockstrail.FSID', $exploded_fsids);
                })
                ->update([
                    'tblonholdstockstrail.Reverted' => 1,
                    'tblonholdstockstrail.RevertedBy' => $request->input('matched_user_id'),
                    'tblonholdstockstrail.RevertDate' => $raw_date->toDateTimeString(),
                ]);
        }

        if (!is_null($request->input('AFSIDs'))) {
            DB::connection('forex')->table('tbladminforexserials')
                ->when(is_array($exploded_afsids), function ($query) use ($exploded_afsids) {
                    return $query->whereIn('tbladminforexserials.AFSID', $exploded_afsids);
                }, function ($query) use ($exploded_afsids) {
                    return $query->where('tbladminforexserials.AFSID', $exploded_afsids);
                })
                ->update([
                    'tbladminforexserials.Onhold' => 0,
                    'tbladminforexserials.HeldBy' => null,
                    'tbladminforexserials.Onholddate' => null,
                ]);

            DB::connection('forex')->table('tblonholdstockstrail')
                ->when(is_array($exploded_afsids), function ($query) use ($exploded_afsids) {
                    return $query->whereIn('tblonholdstockstrail.AFSID', $exploded_afsids);
                }, function ($query) use ($exploded_afsids) {
                    return $query->where('tblonholdstockstrail.AFSID', $exploded_afsids);
                })
                ->update([
                    'tblonholdstockstrail.Reverted' => 1,
                    'tblonholdstockstrail.RevertedBy' => $request->input('matched_user_id'),
                    'tblonholdstockstrail.RevertDate' => $raw_date->toDateTimeString(),
                ]);
        }
    }
}
