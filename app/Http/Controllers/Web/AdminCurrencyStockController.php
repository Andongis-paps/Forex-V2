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

class AdminCurrencyStockController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:ADMIN STOCKS,VIEW')->only(['adminStocks']);
        $this->middleware('check.access.permission:ADMIN STOCKS,ADD')->only(['save']);
        $this->middleware('check.access.permission:ADMIN STOCKS,EDIT')->only(['adminStockDetails']);
        $this->middleware('check.access.permission:ADMIN STOCKS,DELETE')->only(['']);

        $this->middleware('check.access.permission:BRANCH STOCKS,VIEW')->only(['branchStocks']);
        $this->middleware('check.access.permission:BRANCH STOCKS,ADD')->only(['']);
        $this->middleware('check.access.permission:BRANCH STOCKS,EDIT')->only(['branchStockDetails', 'currencyStockDetails']);
        $this->middleware('check.access.permission:BRANCH STOCKS,DELETE')->only(['']);
    }

    public function adminStocks(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $branch_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('fd.CurrencyID, COUNT(fd.CurrencyID) - SUM(CASE WHEN fs.Queued = 1 THEN 1 ELSE 0 END) AS Cnt, SUM(CASE WHEN fs.Queued = 0 THEN fs.BillAmount ELSE 0 END) AS BillAmt, SUM(CASE WHEN fs.Queued = 1 THEN 1 ELSE 0 END) AS Queued, SUM(CASE WHEN fs.Queued = 1 THEN fs.BillAmount ELSE 0 END) AS AmountQ, SUM(fs.BillAmount * d.SinagRateBuying) AS Principal')
            // ->selectRaw('fd.CurrencyID, COUNT(fd.CurrencyID) - SUM(CASE WHEN fs.Queued = 1 THEN 1 ELSE 0 END) AS Cnt, SUM(fs.BillAmount) AS BillAmt, SUM(CASE WHEN fs.Queued = 1 THEN 1 ELSE 0 END) AS Queued, SUM(CASE WHEN fs.Queued = 1 THEN fs.BillAmount ELSE 0 END) AS AmountQ, SUM(fs.BillAmount * d.SinagRateBuying) AS Principal')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tbldenom AS d', 'fs.DenomID', '=', 'd.DenomID')
            // ->where('fs.Buffer', 0)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
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
            ->selectRaw('COALESCE(fd.CurrencyID, bf.CurrencyID) as CurrencyID, COUNT(COALESCE(fd.CurrencyID, bf.CurrencyID)) - SUM(CASE WHEN fs.Queued = 1 THEN 1 ELSE 0 END) AS Cnt, SUM(CASE WHEN fs.Queued = 0 THEN fs.BillAmount ELSE 0 END) AS BillAmt, SUM(CASE WHEN fs.Queued = 1 THEN 1 ELSE 0 END) AS Queued, SUM(CASE WHEN fs.Queued = 1 THEN fs.BillAmount ELSE 0 END) AS AmountQ, SUM(fs.BillAmount * d.SinagRateBuying) AS Principal')
            // ->selectRaw('fd.CurrencyID, COUNT(fd.CurrencyID) - SUM(CASE WHEN fs.Queued = 1 THEN 1 ELSE 0 END) AS Cnt, SUM(fs.BillAmount) AS BillAmt, SUM(CASE WHEN fs.Queued = 1 THEN 1 ELSE 0 END) AS Queued, SUM(CASE WHEN fs.Queued = 1 THEN fs.BillAmount ELSE 0 END) AS AmountQ, SUM(fs.BillAmount * d.SinagRateBuying) AS Principal')
            ->leftJoin('tbladminbuyingtransact AS fd', 'fs.aftdid', '=', 'fd.aftdid')
            ->join('tbladmindenom AS d', 'fs.adenomid', '=', 'd.adenomid')
            ->leftJoin('tblbufferfinancing AS bf', 'fs.bfid', '=', 'bf.bfid')
            // ->where('fs.Buffer', 0)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.FSStat', 1)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->groupBy('CurrencyID');

        $result['stocks_admin'] = DB::connection('forex')->table('tblcurrency AS c')
            ->leftJoinSub($branch_stocks_query, 'bs', function($join) {
                $join->on('c.currencyid', '=', 'bs.CurrencyID');
            })
            ->leftJoinSub($admin_stocks_query, 'ad', function($join) {
                $join->on('c.currencyid', '=', 'ad.CurrencyID');
            })
            ->selectRaw('c.CurrencyID, c.Currency, c.CurrAbbv,
                IFNULL(bs.Cnt, 0) + IFNULL(ad.Cnt, 0) AS total_bill_count,
                IFNULL(bs.BillAmt, 0) + IFNULL(ad.BillAmt, 0) AS total_curr_amount,
                IFNULL(bs.Queued, 0) + IFNULL(ad.Queued, 0) AS queued_total_bill_count,
                IFNULL(bs.AmountQ, 0) + IFNULL(ad.AmountQ, 0) AS queued_total_curr_amnt,
                IFNULL(bs.Principal, 0) + IFNULL(ad.Principal, 0) AS total_principal'
            )
            ->havingRaw('total_principal > 0')
            // ->havingRaw('total_curr_amount > 0')
            ->orderBy('c.currency')
            ->paginate(15);

        return view('currency_stocks.admin_stocks', compact('result', 'menu_id'));
    }

    public function save(Request $request) {
        $exploded_fsids = explode(", ", $request->input('FSIDs'));
        $exploded_afsids = explode(", ", $request->input('AFSIDs'));

        if (!is_null($request->input('FSIDs'))) {
            $raw_date = Carbon::now('Asia/Manila');

            $branch_stocks = DB::connection('forex')->table('tblforexserials')
                ->when(is_array($exploded_fsids), function ($query) use ($exploded_fsids) {
                    return $query->whereIn('tblforexserials.FSID', $exploded_fsids);
                }, function ($query) use ($exploded_fsids) {
                    return $query->where('tblforexserials.FSID', $exploded_fsids);
                });

                $branch_stocks->update([
                    'tblforexserials.Onhold' => 1,
                    'tblforexserials.HeldBy' => $request->input('matched_user_id'),
                    'tblforexserials.OnholdDate' => $raw_date->toDateTimeString(),
                ]);

                $held_bills = $branch_stocks->clone()
                    ->selectRaw('tblforexserials.FSID, tblforexserials.BillAmount, tblforexserials.Serials')
                    ->groupBy('tblforexserials.FSID', 'tblforexserials.BillAmount', 'tblforexserials.Serials')
                    ->orderBy('tblforexserials.FSID', 'ASC')
                    ->get();

                foreach ($held_bills as $index => $value) {
                    DB::connection('forex')->table('tblonholdstockstrail')
                        ->insert([
                            'tblonholdstockstrail.FSID' => $exploded_fsids[$index],
                            'tblonholdstockstrail.BillAmount' => $value->BillAmount,
                            'tblonholdstockstrail.Serials' => $value->Serials,
                            'tblonholdstockstrail.EntryDate' => $raw_date->toDateString(),
                            'tblonholdstockstrail.Onhold' => 1,
                            'tblonholdstockstrail.HeldBy' => $request->input('matched_user_id'),
                            'tblonholdstockstrail.OnholdDate' => $raw_date->toDateTimeString(),
                        ]);
                }
        }

        if (!is_null($request->input('AFSIDs'))) {
            $raw_date = Carbon::now('Asia/Manila');

            $admin_stocks = DB::connection('forex')->table('tbladminforexserials as fasx')
                ->when(is_array($exploded_afsids), function ($query) use ($exploded_afsids) {
                    return $query->whereIn('fasx.AFSID', $exploded_afsids);
                }, function ($query) use ($exploded_afsids) {
                    return $query->where('fasx.AFSID', $exploded_afsids);
                });

            $admin_stocks->update([
                'fasx.Onhold' => 1,
                'fasx.HeldBy' => $request->input('matched_user_id'),
                'fasx.OnholdDate' => $raw_date->toDateTimeString(),
            ]);

            $test = $admin_stocks->clone()
                ->selectRaw('fasx.AFSID, fasx.BillAmount, fasx.Serials')
                ->groupBy('fasx.AFSID', 'fasx.BillAmount', 'fasx.Serials')
                ->orderBy('fasx.AFSID', 'ASC')
                ->get();

            foreach ($test as $index => $value) {
                DB::connection('forex')->table('tblonholdstockstrail')
                    ->insert([
                        'tblonholdstockstrail.AFSID' => $exploded_afsids[$index],
                        'tblonholdstockstrail.BillAmount' => $value->BillAmount,
                        'tblonholdstockstrail.Serials' => $value->Serials,
                        'tblonholdstockstrail.EntryDate' => $raw_date->toDateString(),
                        'tblonholdstockstrail.Onhold' => 1,
                        'tblonholdstockstrail.HeldBy' => $request->input('matched_user_id'),
                        'tblonholdstockstrail.OnholdDate' => $raw_date->toDateTimeString(),
                    ]);
            }
        }
    }

    public function adminStockDetails(Request $request) {
        $branch_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('fd.CurrencyID, fs.Queued, fs.Buffer, fs.FSID, fs.Serials, fs.BillAmount, tt.TransType, d.SinagRateBuying, fd.Rset, SUM(fs.BillAmount * d.SinagRateBuying) as principal, 2 as source_type')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tbldenom AS d', 'fs.DenomID', '=', 'd.DenomID')
            ->join('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            // ->whereNull('fs.QueuedBy')
            // ->where('fs.Queued', 0)
            // ->where('fs.Buffer', 0)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.Transfer', 1)
            ->whereNotNull('fs.FTDID')
            ->where('fs.Received', 1)
            ->where('fs.FSStat', 2)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->where('fd.CurrencyID', $request->get('CurrencyID'))
            ->groupBy('fd.CurrencyID', 'fs.FSID', 'fs.Serials', 'fs.BillAmount', 'tt.TransType', 'd.SinagRateBuying', 'fd.Rset', 'fs.Buffer');

        $admin_stocks_query = DB::connection('forex')->table('tbladminforexserials AS fs')
            ->selectRaw('COALESCE(fd.CurrencyID, bf.CurrencyID) as CurrencyID, fs.Queued, fs.Buffer, fs.AFSID, fs.Serials, fs.BillAmount, tt.TransType, d.SinagRateBuying, COALESCE(fd.Rset, bf.Rset) as Rset, SUM(fs.BillAmount * d.SinagRateBuying) as principal, 1 as source_type')
            ->leftJoin('tbladminbuyingtransact AS fd', 'fs.aftdid', '=', 'fd.aftdid')
            ->join('tbladmindenom AS d', 'fs.adenomid', '=', 'd.adenomid')
            ->leftJoin('tblbufferfinancing AS bf', 'fs.bfid', '=', 'bf.bfid')
            ->leftJoin('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            // ->whereNull('fs.QueuedBy')
            // ->where('fs.Queued', 0)
            // ->where('fs.Buffer', 0)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.FSStat', 1)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereRaw('COALESCE(fd.CurrencyID, bf.CurrencyID) = ? ', $request->get('CurrencyID'))
            ->groupBy('CurrencyID', 'fs.AFSID', 'fs.Serials', 'fs.BillAmount', 'tt.TransType', 'd.SinagRateBuying', 'Rset', 'fs.Buffer');

        $joined_queries = $branch_stocks_query->unionAll($admin_stocks_query);

        $admin_stock_details_s = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('FSID as ID, Buffer, Rset, CurrencyID, Serials, BillAmount, TransType, SinagRateBuying, SUM(BillAmount) as total_bill_amount, SUM(principal) as total_principal, source_type, Buffer, Queued')
            ->groupBy('ID', 'Buffer', 'Rset', 'CurrencyID', 'Serials', 'BillAmount', 'TransType', 'SinagRateBuying', 'source_type', 'Buffer', 'Queued')
            // ->where('Buffer', 0)
            ->where('Queued', 0)
            // ->where('Queued', 1)
            ->get();

        $denoms =  DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('GROUP_CONCAT(DISTINCT BillAmount ORDER BY BillAmount DESC) as denominations')
            ->get();

        $sorted_by_r_set_buffer =  DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('Rset, Queued, COUNT(BillAmount) as total_bill_count, SUM(BillAmount) as total_bil_amount')
            ->where('Queued', 0)
            // ->where('Queued', 1)
            ->where('Buffer', 1)
            ->whereIn('Rset', ['O', 'B'])
            ->groupBy('Rset', 'Queued')
            ->get();

        $sorted_by_r_set =  DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('Rset, Queued, COUNT(BillAmount) as total_bill_count, SUM(BillAmount) as total_bil_amount')
            ->where('Queued', 0)
            // ->where('Queued', 1)
            ->where('Buffer', 0)
            ->whereIn('Rset', ['O', 'B'])
            ->groupBy('Rset', 'Queued')
            ->get();

        $sorted_by_buffer =  DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('Buffer, Queued, SUM(BillAmount) as total_buffer_amount, COUNT(BillAmount) as total_bill_count')
            ->where('Queued', 0)
            // ->where('Queued', 1)
            ->where('Buffer', 1)
            ->groupBy('Buffer', 'Queued')
            ->get();

        $stock_breakdown =  DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('Buffer, BillAmount, Queued, COUNT(BillAmount) as denom_count, SUM(BillAmount) as total_bill_amount, SUM(BillAmount * SinagRateBuying) as principal')
            ->where('Buffer', 0)
            ->where('Queued', 0)
            // ->where('Queued', 1)
            ->groupBy('Buffer', 'BillAmount', 'Queued')
            ->get();

        $stock_breakdown_buffer =  DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('Buffer, BillAmount, Queued, COUNT(BillAmount) as denom_count, SUM(BillAmount) as total_bill_amount, SUM(BillAmount * SinagRateBuying) as principal')
            ->where('Queued', 0)
            // ->where('Queued', 1)
            ->where('Buffer', 1)
            ->groupBy('Buffer', 'BillAmount', 'Queued')
            ->get();

        $response = [
            'denoms' => $denoms,
            'admin_stock_details_s' => $admin_stock_details_s,
            'sorted_by_r_set' => $sorted_by_r_set,
            'sorted_by_r_set_buffer' => $sorted_by_r_set_buffer,
            'sorted_by_buffer' => $sorted_by_buffer,
            'stock_breakdown' => $stock_breakdown,
            'stock_breakdown_buffer' => $stock_breakdown_buffer
        ];

        return response()->json($response);
    }

    public function branchStocks(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $search = $request->query('query');
        $raw_date = Carbon::now('Asia/Manila');

        $result['branches'] = DB::connection('forex')->table('tblbranch')
            ->selectRaw('tblbranch.BranchID, tblbranch.BranchCode, pawnshop.tblxbranch.Address, accounting.tblcompany.CompanyID, accounting.tblcompany.CompanyName')
            ->join('pawnshop.tblxbranch', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.BranchCode')
            ->join('accounting.tblsegmentgroup', 'pawnshop.tblxbranch.BranchID', 'accounting.tblsegmentgroup.BranchID')
            ->join('accounting.tblcompany', 'accounting.tblsegmentgroup.CompanyID', 'accounting.tblcompany.CompanyID')
            ->join('accounting.tblsegments', 'accounting.tblsegmentgroup.SegmentID', 'accounting.tblsegments.SegmentID')
            ->where('accounting.tblsegments.SegmentID', '=', 3)
            ->where('pawnshop.tblxbranch.BranchCode', '!=', 'ADMIN')
            ->groupBy('tblbranch.BranchID', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.Address', 'accounting.tblcompany.CompanyID', 'accounting.tblcompany.CompanyName')
            ->get();

        $currency_query = DB::connection('forex')->table('tblforextransactiondetails')
            ->join('tblbranch', 'tblforextransactiondetails.BranchID', 'tblbranch.BranchID')
            ->join('tblforexserials', 'tblforextransactiondetails.FTDID', 'tblforexserials.FTDID')
            ->where('tblforextransactiondetails.BranchID', '!=', 10)
            ->where('tblforexserials.Onhold', '=', 0)
            ->where('tblforexserials.Sold', '=', 0)
            ->where('tblforexserials.Transfer', '=', 0)
            ->where('tblforexserials.Received', '=', 0)
            ->where('tblforexserials.SoldToManila', '=', 0)
            ->where('tblforexserials.FSStat', '=', 1)
            ->whereIn('tblforexserials.FSType', [1, 2, 3])
            ->whereNotNull('tblforexserials.Serials')
            // ->where('tblforextransactiondetails.TransactionDate', '<=', DB::raw('DATE_SUB(CURDATE(), INTERVAL 3 DAY)'))
            ->when($search, function ($query, $search) {
                return $query->where('tblbranch.BranchCode', $search);
            });

        $result['currency_stocks'] = $currency_query->clone()
            ->selectRaw('tblforextransactiondetails.BranchID, tblbranch.BranchCode, CASE WHEN SUM(CASE WHEN tblforextransactiondetails.TransactionDate <= DATE_SUB(CURDATE(), INTERVAL 3 DAY) THEN 1 ELSE 0 END) > 0 THEN 1 ELSE 0 END AS InStockFor3DaysOrMore')
            ->where('tblforextransactiondetails.TransactionDate', '>=', '2025-01-01')
            ->groupBy('tblforextransactiondetails.BranchID', 'tblbranch.BranchCode')
            ->orderBy('tblforextransactiondetails.BranchID', 'ASC')
            ->paginate(15);

        $result['currencies'] = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->selectRaw('fd.CurrencyID, tc.Currency, COUNT(fs.BillAmount) as total_bill_count, SUM(fs.BillAmount) as total_bill_amount, SUM(fs.BillAmount * td.SinagRateBuying) as total_principal, GROUP_CONCAT(DISTINCT tb.BranchID ORDER BY tb.BranchID ASC) as BranchIDs, MAX(CASE WHEN fs.Serials IS NULL THEN 1 ELSE 0 END) AS has_pending, GROUP_CONCAT(fs.FSID) as FSIDs')
            ->join('tblbranch as tb', 'fd.BranchID', 'tb.BranchID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            ->join('tblforexserials as fs', 'fd.FTDID', 'fs.FTDID')
            ->join('tbldenom as td', 'fs.DenomID', 'td.DenomID')
            ->where('fd.Voided', 0)
            ->where('fd.BranchID', '!=', 10)
            ->where('fs.Onhold', '=', 0)
            ->where('fs.Sold', '=', 0)
            ->where('fs.Transfer', '=', 0)
            ->where('fs.Received', '=', 0)
            ->where('fs.SoldToManila', '=', 0)
            ->where('fs.FSStat', '=', 1)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fd.TransactionDate', '>=', '2025-01-01')
            // ->where('fd.TransactionDate', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL 3 DAY)'))
            ->groupBy('fd.CurrencyID', 'tc.Currency')
            ->orderBy('tc.Currency', 'ASC')
            ->get();

        return view('currency_stocks.branch_stocks', compact('result', 'menu_id'));
    }

    public function branchesOfCurrency(Request $request) {
        $exploded_branch_ids = explode(",", $request->input('branch_ids'));

        $branches_of_currency = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->selectRaw('fd.BranchID, tb.BranchCode, COUNT(CASE WHEN fs.Serials IS NOT NULL THEN fs.BillAmount END) as total_count_per_branch, SUM(CASE WHEN fs.Serials IS NOT NULL THEN fs.BillAmount END) as total_amount_per_branch, COUNT(CASE WHEN fs.Serials IS NULL THEN fs.BillAmount END) as pending_count_per_branch, SUM(CASE WHEN fs.Serials IS NULL THEN fs.BillAmount END) as pending_amount_per_branch, MAX(CASE WHEN fs.Serials IS NULL THEN 1 ELSE 0 END) AS has_pending')
            ->join('tblbranch as tb', 'fd.BranchID', 'tb.BranchID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            ->join('tblforexserials as fs', 'fd.FTDID', 'fs.FTDID')
            ->where('fd.CurrencyID', '=', $request->input('curr_id'))
            ->when(is_array($exploded_branch_ids), function ($query) use ($exploded_branch_ids) {
                return $query->whereIn('fd.BranchID', $exploded_branch_ids);
            }, function ($query) use ($exploded_branch_ids) {
                return $query->where('fd.BranchID', $exploded_branch_ids);
            })
            ->where('fd.Voided', 0)
            ->where('fs.Onhold', '=', 0)
            ->where('fs.Sold', '=', 0)
            ->where('fs.Transfer', '=', 0)
            ->where('fs.Received', '=', 0)
            ->where('fs.SoldToManila', '=', 0)
            ->where('fs.FSStat', '=', 1)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fd.TransactionDate', '>=', '2025-01-01')
            ->groupBy('fd.BranchID', 'tb.BranchCode')
            ->orderBy('fd.BranchID', 'ASC')
            ->get();

        $response = [
            'branches_of_currency' => $branches_of_currency
        ];

        return response()->json($response);
    }

    public function branchStockDetails(Request $request) {
        $branch_stocks = DB::connection('forex')->table('tblforextransactiondetails')
            ->selectRaw('tblforextransactiondetails.CurrencyID, tblcurrency.Currency, SUM(tblforexserials.BillAmount) as total_bill_amount, COUNT(tblforexserials.BillAmount)as total_bill_count, SUM(tblforexserials.BillAmount * tbldenom.SinagRateBuying) as total_principal, CASE WHEN SUM(CASE WHEN tblforextransactiondetails.TransactionDate <= DATE_SUB(CURDATE(), INTERVAL 3 DAY) THEN 1 ELSE 0 END) > 0 THEN 1 ELSE 0 END AS InStockFor3DaysOrMore')
            ->join('tblcurrency', 'tblforextransactiondetails.CurrencyID', 'tblcurrency.CurrencyID')
            ->join('tblforexserials', 'tblforextransactiondetails.FTDID', 'tblforexserials.FTDID')
            ->join('tbldenom', 'tblforexserials.DenomID', 'tbldenom.DenomID')
            ->where('tblforextransactiondetails.BranchID', $request->input('BranchID'))
            ->where('tblforexserials.Onhold', '=', 0)
            ->where('tblforexserials.Sold', '=', 0)
            ->where('tblforexserials.Transfer', '=', 0)
            ->where('tblforexserials.Received', '=', 0)
            ->where('tblforexserials.SoldToManila', '=', 0)
            ->where('tblforexserials.FSStat', '=', 1)
            ->whereIn('tblforexserials.FSType', [1, 2, 3])
            ->whereNotNull('tblforexserials.Serials')
            ->where('tblforextransactiondetails.TransactionDate', '>=', '2025-01-01')
            ->groupBy('tblforextransactiondetails.CurrencyID', 'tblcurrency.Currency')
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->get();

        $response = [
            'branch_stocks' => $branch_stocks
        ];

        return response()->json($response);
    }
    
    public function currencyStockDetails(Request $request) {
       $curr_stocks_by_bill = DB::connection('forex')->table('tblforextransactiondetails')
            ->selectRaw('tblforexserials.BillAmount, COUNT(tblforexserials.BillAmount)as total_bill_count, SUM(tblforexserials.BillAmount) as total_bill_amount')
            ->join('tblcurrency', 'tblforextransactiondetails.CurrencyID', 'tblcurrency.CurrencyID')
            ->join('tblforexserials', 'tblforextransactiondetails.FTDID', 'tblforexserials.FTDID')
            ->join('tbldenom', 'tblforexserials.DenomID', 'tbldenom.DenomID')
            ->where('tblforextransactiondetails.BranchID', $request->input('BranchID'))
            ->where('tblforextransactiondetails.CurrencyID', $request->input('CurrencyID'))
            ->where('tblforexserials.Onhold', '=', 0)
            ->where('tblforexserials.Sold', '=', 0)
            ->where('tblforexserials.Transfer', '=', 0)
            ->where('tblforexserials.Received', '=', 0)
            ->where('tblforexserials.SoldToManila', '=', 0)
            ->where('tblforexserials.FSStat', '=', 1)
            ->whereIn('tblforexserials.FSType', [1, 2, 3])
            ->whereNotNull('tblforexserials.Serials')
            ->where('tblforextransactiondetails.TransactionDate', '>=', '2025-01-01')
            ->groupBy('tblforexserials.BillAmount')
            ->orderBy('tblforexserials.BillAmount', 'DESC')
            ->get();

        $curr_stocks_total = DB::connection('forex')->table('tblforextransactiondetails')
            ->selectRaw('SUM(tblforexserials.BillAmount) as total_bill_amount, COUNT(tblforexserials.BillAmount) as total_bill_count, SUM(tblforexserials.BillAmount * tbldenom.SinagRateBuying) as total_principal')
            ->join('tblcurrency', 'tblforextransactiondetails.CurrencyID', 'tblcurrency.CurrencyID')
            ->join('tblforexserials', 'tblforextransactiondetails.FTDID', 'tblforexserials.FTDID')
            ->join('tbldenom', 'tblforexserials.DenomID', 'tbldenom.DenomID')
            ->where('tblforextransactiondetails.BranchID', $request->input('BranchID'))
            ->where('tblforextransactiondetails.CurrencyID', $request->input('CurrencyID'))
            ->where('tblforexserials.Onhold', '=', 0)
            ->where('tblforexserials.Sold', '=', 0)
            ->where('tblforexserials.Transfer', '=', 0)
            ->where('tblforexserials.Received', '=', 0)
            ->where('tblforexserials.SoldToManila', '=', 0)
            ->where('tblforexserials.FSStat', '=', 1)
            ->whereIn('tblforexserials.FSType', [1, 2, 3])
            ->whereNotNull('tblforexserials.Serials')
            ->where('tblforextransactiondetails.TransactionDate', '>=', '2025-01-01')
            ->orderBy('tblforexserials.BillAmount', 'DESC')
            ->get();

        $curr_stocks_by_serial = DB::connection('forex')->table('tblforextransactiondetails')
            ->selectRaw('tblforexserials.BillAmount, tblforextransactiondetails.TransactionDate, SUM(tblforexserials.BillAmount) as total_bill_amount, SUM(tblforexserials.BillAmount * tbldenom.SinagRateBuying) as total_principal, DATEDIFF(NOW(), tblforextransactiondetails.TransactionDate) as days_in_stock')
            // ->selectRaw('tblforexserials.FSID, tblforexserials.BillAmount, tblforexserials.Serials, tbltransactiontype.TransType, tbldenom.SinagRateBuying, SUM(tblforexserials.BillAmount * tbldenom.SinagRateBuying) as total_principal, DATEDIFF(NOW(), tblforextransactiondetails.TransactionDate) as days_in_stock')
            ->join('tbltransactiontype', 'tblforextransactiondetails.TransType', 'tbltransactiontype.TTID')
            ->join('tblcurrency', 'tblforextransactiondetails.CurrencyID', 'tblcurrency.CurrencyID')
            ->join('tblforexserials', 'tblforextransactiondetails.FTDID', 'tblforexserials.FTDID')
            ->join('tbldenom', 'tblforexserials.DenomID', 'tbldenom.DenomID')
            ->where('tblforextransactiondetails.BranchID', $request->input('BranchID'))
            ->where('tblforextransactiondetails.CurrencyID', $request->input('CurrencyID'))
            ->where('tblforexserials.Onhold', '=', 0)
            ->where('tblforexserials.Sold', '=', 0)
            ->where('tblforexserials.Transfer', '=', 0)
            ->where('tblforexserials.Received', '=', 0)
            ->where('tblforexserials.SoldToManila', '=', 0)
            ->where('tblforexserials.FSStat', '=', 1)
            ->whereIn('tblforexserials.FSType', [1, 2, 3])
            ->whereNotNull('tblforexserials.Serials')
            ->where('tblforextransactiondetails.TransactionDate', '>=', '2025-01-01')
            ->groupBy('tblforexserials.BillAmount', 'tblforextransactiondetails.TransactionDate')
            ->orderBy('tblforextransactiondetails.TransactionDate', 'ASC')
            ->get();

        $response = [
            'curr_stocks_by_bill' => $curr_stocks_by_bill,
            'curr_stocks_total' => $curr_stocks_total,
            'curr_stocks_by_serial' => $curr_stocks_by_serial,
        ];

        return response()->json($response);
    }
}
