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

class BufferController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:TRANSFER BUFFER,VIEW')->only(['buffer', 'financing']);
        $this->middleware('check.access.permission:TRANSFER BUFFER,ADD')->only(['stocks', 'saveBuffer']);

        $this->middleware('check.access.permission:BUFFER WALLET,VIEW')->only(['wallet']);

        $this->middleware('check.access.permission:RECEIVE BUFFER,VIEW')->only(['transfers', 'details', 'serials']);
        $this->middleware('check.access.permission:RECEIVE BUFFER,ADD')->only(['receive', 'receiveTransfers']);
        $this->middleware('check.access.permission:RECEIVE BUFFER,EDIT')->only(['details', 'breakdownBuffer', 'bufferSerials']);
        $this->middleware('check.access.permission:RECEIVE BUFFER,DELETE')->only(['revert']);
    }

    protected function adminStocks() {
        $branch_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('fd.CurrencyID, fs.Queued, fs.SoldToManila, fs.Buffer, fs.FSID, fs.Serials, fs.BillAmount, tt.TransType, d.SinagRateBuying, fd.Rset, SUM(fs.BillAmount * d.SinagRateBuying) as principal, 2 as source_type')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tbldenom AS d', 'fs.DenomID', '=', 'd.DenomID')
            ->join('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            // ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.Transfer', 1)
            ->whereNotNull('fs.FTDID')
            ->where('fs.Received', 1)
            ->where('fs.FSStat', 2)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            // ->where('fd.CurrencyID', $request->get('CurrencyID'))
            ->where('fd.CurrencyID', 11)
            ->groupBy('fd.CurrencyID', 'fs.FSID', 'fs.SoldToManila', 'fs.Serials', 'fs.BillAmount', 'tt.TransType', 'd.SinagRateBuying', 'fd.Rset', 'fs.Buffer');

        $admin_stocks_query = DB::connection('forex')->table('tbladminforexserials AS fs')
            ->selectRaw('fd.CurrencyID, fs.Queued, fs.SoldToManila, fs.Buffer, fs.AFSID, fs.Serials, fs.BillAmount, tt.TransType, d.SinagRateBuying, bf.Rset, SUM(fs.BillAmount * d.SinagRateBuying) as principal, 1 as source_type')
            ->leftJoin('tbladminbuyingtransact AS fd', 'fs.aftdid', '=', 'fd.aftdid')
            ->leftJoin('tblbufferfinancing AS bf', 'fs.bfid', '=', 'bf.bfid')
            ->join('tbladmindenom AS d', 'fs.adenomid', '=', 'd.adenomid')
            ->leftJoin('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            // ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.FSStat', 1)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            // ->where('fd.CurrencyID', $request->get('CurrencyID'))
            // ->where('fd.CurrencyID', 11)
            ->groupBy('fd.CurrencyID', 'fs.AFSID', 'fs.SoldToManila', 'fs.BFID', 'fs.Serials', 'fs.BillAmount', 'tt.TransType', 'd.SinagRateBuying', 'bf.Rset', 'fs.Buffer');

        $joined_queries = $branch_stocks_query->unionAll($admin_stocks_query);

        $admin_stock_details_s = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('FSID as ID, Rset, CurrencyID, BillAmount, SinagRateBuying, Buffer, Queued, source_type')
            ->groupBy('ID', 'Rset', 'CurrencyID', 'BillAmount', 'SinagRateBuying', 'Buffer', 'Queued', 'source_type')
            ->whereNotNull('Serials')
            ->where('Buffer', 1)
            ->where('Queued', 0)
            ->get();

        $a_buffer_bal_breakdown = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('
                SUM(CASE WHEN Serials IS NULL AND Queued = 0 AND SoldToManila = 0 THEN BillAmount ELSE 0 END) as total_w_o_serial,
                SUM(CASE WHEN Serials IS NOT NULL AND Queued = 0 AND SoldToManila = 0 THEN BillAmount ELSE 0 END) as total_w_serial
            ')
            // >selectRaw('
            //     SUM(CASE WHEN Serials IS NULL AND Queued = 0 AND SoldToManila = 0 THEN BillAmount ELSE 0 END) as total_w_o_serial,
            //     COUNT(CASE WHEN Serials IS NULL AND Queued = 0 AND SoldToManila = 0 THEN 1 END) AS count_w_o_serial,
            //     SUM(CASE WHEN Serials IS NOT NULL AND Queued = 0 AND SoldToManila = 0 THEN BillAmount ELSE 0 END) as total_w_serial,
            //     COUNT(CASE WHEN Serials IS NOT NULL AND Queued = 0 AND SoldToManila = 0 THEN 1 END) AS count_w_serial
            // ')
            ->where('Buffer', 1)
            ->get();

        return [
            'admin_stock_details_s' => $admin_stock_details_s,
            'a_buffer_bal_breakdown' => $a_buffer_bal_breakdown
        ];
    }

    protected function stocksForAcknowledgement() {
        $branch_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('fd.CurrencyID, fs.Queued, fs.QueuedBy, fs.Buffer, fs.FSID, fs.Serials, fs.BillAmount, tt.TransType, d.SinagRateBuying, fd.Rset, SUM(fs.BillAmount * d.SinagRateBuying) as principal, 2 as source_type')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tbldenom AS d', 'fs.DenomID', '=', 'd.DenomID')
            ->join('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
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
            // ->where('fd.CurrencyID', $request->get('CurrencyID'))
            ->where('fd.CurrencyID', 11)
            ->groupBy('fd.CurrencyID', 'fs.FSID', 'fs.Serials', 'fs.BillAmount', 'tt.TransType', 'd.SinagRateBuying', 'fd.Rset', 'fs.Buffer', 'fs.Queued', 'fs.QueuedBy');

        $admin_stocks_query = DB::connection('forex')->table('tbladminforexserials AS fs')
            ->selectRaw('bf.CurrencyID, fs.Queued, fs.QueuedBy, fs.Buffer, fs.AFSID, fs.Serials, fs.BillAmount, tt.TransType, d.SinagRateBuying, bf.Rset, SUM(fs.BillAmount * d.SinagRateBuying) as principal, 1 as source_type')
            ->leftJoin('tbladminbuyingtransact AS fd', 'fs.aftdid', '=', 'fd.aftdid')
            ->leftJoin('tblbufferfinancing AS bf', 'fs.bfid', '=', 'bf.bfid')
            ->join('tbladmindenom AS d', 'fs.adenomid', '=', 'd.adenomid')
            ->leftJoin('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.FSStat', 1)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            // ->where('fd.CurrencyID', $request->get('CurrencyID'))
            // ->where('fd.CurrencyID', 11)
            ->groupBy('bf.CurrencyID', 'fs.AFSID', 'fs.BFID', 'fs.Serials', 'fs.BillAmount', 'tt.TransType', 'd.SinagRateBuying', 'bf.Rset', 'fs.Buffer', 'fs.Queued', 'fs.QueuedBy');

        $joined_queries = $branch_stocks_query->unionAll($admin_stocks_query);

        $ack_stocks = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('FSID as ID, Rset, CurrencyID, BillAmount, SinagRateBuying, Buffer, Queued, QueuedBy, source_type')
            ->groupBy('ID', 'Rset', 'CurrencyID', 'BillAmount', 'SinagRateBuying', 'Buffer', 'Queued', 'QueuedBy', 'source_type')
            ->where('Buffer', 1)
            ->where('Queued', 0)
            ->get();

        return $ack_stocks;
    }

    public function buffer(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['branch_stocks'] = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->selectRaw('tb.BranchCode, tc.CurrencyID, tc.Currency, tb.BranchID, SUM(fs.BillAmount) as total_stock_amount, GROUP_CONCAT(fs.FSID) as FSIDs, tx.DistantLocation')
            ->join('tblbranch as tb', 'fd.BranchID', 'tb.BranchID')
            ->join('pawnshop.tblxbranch as tx', 'tb.BranchCode', 'tx.BranchCode')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            ->join('tblforexserials as fs', 'fd.FTDID', 'fs.FTDID')
            ->where('fd.CurrencyID', '=', 11)
            ->where('fs.FSType', '=', 1)
            ->where('fs.FSStat', '=', 1)
            ->where('fs.Sold', '=', 0)
            ->where('fs.Transfer', '=', 0)
            ->whereNotNull('fs.Serials')
            ->where('fd.TransactionDate', '>=', '2025-01-01')
            ->where('tb.BranchCode', '!=', 'ADMIN')
            ->groupBy('tb.BranchCode', 'tc.CurrencyID', 'tc.Currency', 'tb.BranchID', 'tx.DistantLocation')
            ->orderByRaw('CASE WHEN tx.DistantLocation = 1 THEN 0 ELSE 1 END')
            ->orderBy('tb.BranchID', 'ASC')
            ->paginate(15);

        $result['buffer_in_out'] = DB::connection('forex')->table('tblbuffercontrol')
            ->selectRaw('SUM(DollarIn - DollarOut) as Balance')
            ->where('tblbuffercontrol.BCDate', '>=',  '2025-01-01')
            ->first();

        return view('buffer.buffer_cut_list', compact('result', 'menu_id'));
    }

    public function bufferCutBranch(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $admin_stock_details_s = $this->adminStocks()['admin_stock_details_s'];

        $query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->join('tblbranch', 'fd.BranchID', 'tblbranch.BranchID')
            ->join('tblcurrency', 'fd.CurrencyID', 'tblcurrency.CurrencyID')
            ->join('tblforexserials as fs', 'fd.FTDID', 'fs.FTDID')
            ->join('tbldenom as dm', 'fs.DenomID', 'dm.DenomID')
            ->where('fd.TransType', '!=', 4)
            ->where('fd.BranchID', '=', $request->get('branch_id'))
            ->where('fd.CurrencyID', '=', $request->get('currency_id'))
            ->where('fs.FSType', '=', 1)
            ->where('fs.FSStat', '=', 1)
            ->where('fs.Sold', '=', 0)
            ->where('fs.Transfer', '=', 0)
            ->whereNotNull('fs.Serials');

        $branch_stock_details = $query->clone()
            ->selectRaw('fs.FSID, dm.SinagRateBuying, fs.BillAmount, fs.Serials, tblcurrency.CurrencyID, tblcurrency.Currency, fd.TransactionDate, fd.TransactionNo, fd.Rset, SUM(fs.BillAmount * dm.SinagRateBuying) as Principal')
            ->groupBy('fs.FSID', 'dm.SinagRateBuying', 'fs.BillAmount', 'fs.Serials', 'tblcurrency.CurrencyID', 'tblcurrency.Currency', 'fd.TransactionDate', 'fd.TransactionNo', 'fd.Rset')
            ->orderBy('fd.TransactionDate', 'ASC')
            ->orderBy('fS.BillAmount', 'DESC')
            ->get();

        if ($request->get('buffer_type') == 1) {
            $admin_limit = $request->get('amount');
            $admin_total_amount = 0;
            $admin_selected_ids = [];
            $admin_selected_rates = [];

            foreach ($admin_stock_details_s as $details) {
                if ($admin_total_amount + $details->BillAmount <= $admin_limit) {
                    $admin_selected_ids[] = $details->ID;
                    // $selected_amount[] = $details->BillAmount;
                    $admin_selected_rates[] = $details->SinagRateBuying;

                    $admin_total_amount += $details->BillAmount;
                }

                if ($admin_total_amount >= $admin_limit) {
                    break;
                }
            }

            $branch_total_amount = 0;
            $branch_selected_ids = [];
            $branch_selected_rates = [];

            foreach ($branch_stock_details as $details) {
                if ($branch_total_amount + $details->BillAmount <= $admin_limit) {
                    $branch_selected_ids[] = $details->FSID;
                    // $selected_amount[] = $details->BillAmount;
                    $branch_selected_rates[] = $details->SinagRateBuying;

                    $branch_total_amount += $details->BillAmount;
                }

                if ($branch_total_amount >= $admin_limit) {
                    break;
                }
            }

            $validity = $admin_total_amount < $admin_limit ? 0 : 1;
            $validity_branch = $branch_total_amount < $admin_limit ? 0 : 1;

            if ($validity == 0) {
                $response = [
                    'validity' => $validity,
                    'buffer_type' => $request->get('buffer_type')
                ];

                return response()->json($response);
            } else if ($validity_branch == 0) {
                $response = [
                    'validity_branch' => $validity_branch,
                    'buffer_type' => $request->get('buffer_type')
                ];

                return response()->json($response);
            } else {
                $limit = $request->get('amount');
                $total_amount = 0;
                $selected_ids = [];
                $selected_amount = [];
                $selected_rates = [];
                $selected_principal = [];
                $grouped_by_rates = '';

                foreach ($branch_stock_details as $details) {
                    if ($total_amount + $details->BillAmount <= $limit) {
                        $selected_ids[] = $details->FSID;
                        $selected_amount[] = $details->BillAmount;
                        $selected_rates[] = $details->SinagRateBuying;
                        $selected_principal[] = $details->BillAmount * $details->SinagRateBuying;

                        $total_amount += $details->BillAmount;
                    }

                    if ($total_amount >= $limit) {
                        break;
                    }
                }

                $grouped_by_rates = $query->clone()
                    ->selectRaw('SUM(fs.BillAmount) as total_bill_amount, COUNT(fs.BillAmount) as total_bill_count, dm.SinagRateBuying')
                    ->selectRaw('SUM(fs.BillAmount * dm.SinagRateBuying) as principal')
                    ->selectRaw('? as selling_rate', [$request->get('selling_rate')])
                    ->selectRaw('SUM(fs.BillAmount) * ? as total_exchange_amount', [$request->get('selling_rate')])
                    ->selectRaw('(SUM(fs.BillAmount) * ? - SUM(fs.BillAmount * dm.SinagRateBuying)) as gain_loss', [$request->get('selling_rate')])
                    ->when(is_array($selected_ids), function ($query) use ($selected_ids) {
                        return $query->whereIn('fs.FSID', $selected_ids);
                    }, function ($query) use ($selected_ids) {
                        return $query->where('fs.FSID', $selected_ids);
                    })
                    ->groupBy('SinagRateBuying')
                    ->get();

                $exchange_amnt = $request->get('amount') * $request->get('selling_rate');
                $income = $exchange_amnt - array_sum($selected_principal);

                $response = [
                    'exchange_amnt' => $exchange_amnt,
                    'principal' => array_sum($selected_principal),
                    'income' => $income,
                ];

                $response_1 = [
                    'grouped_by_rates' => $grouped_by_rates
                ];

                return response()->json([$response, $response_1]);
            }
        } else {
            $limit = $request->get('amount');
            $total_amount = 0;
            $selected_ids = [];
            $selected_amount = [];
            $selected_rates = [];
            $selected_principal = [];
            $grouped_by_rates = '';

            foreach ($branch_stock_details as $details) {
                if ($total_amount + $details->BillAmount <= $limit) {
                    $selected_ids[] = $details->FSID;
                    $selected_amount[] = $details->BillAmount;
                    $selected_rates[] = $details->SinagRateBuying;
                    $selected_principal[] = $details->BillAmount * $details->SinagRateBuying;

                    $total_amount += $details->BillAmount;
                }

                if ($total_amount >= $limit) {
                    break;
                }
            }

            $grouped_by_rates = $query->clone()
                ->selectRaw('SUM(fs.BillAmount) as total_bill_amount, COUNT(fs.BillAmount) as total_bill_count, dm.SinagRateBuying')
                ->selectRaw('SUM(fs.BillAmount * dm.SinagRateBuying) as principal')
                ->selectRaw('? as selling_rate', [$request->get('selling_rate')])
                ->selectRaw('SUM(fs.BillAmount) * ? as total_exchange_amount', [$request->get('selling_rate')])
                ->selectRaw('(SUM(fs.BillAmount) * ? - SUM(fs.BillAmount * dm.SinagRateBuying)) as gain_loss', [$request->get('selling_rate')])
                ->when(is_array($selected_ids), function ($query) use ($selected_ids) {
                    return $query->whereIn('fs.FSID', $selected_ids);
                }, function ($query) use ($selected_ids) {
                    return $query->where('fs.FSID', $selected_ids);
                })
                ->groupBy('SinagRateBuying')
                ->get();

            $validity = $total_amount < $limit ? 0 : 1;

            if ($validity == 0) {
                $response = [
                    'validity' => $validity,
                    'buffer_type' => $request->get('buffer_type')
                ];

                return response()->json($response);
            } else {
                $exchange_amnt = $request->get('amount') * $request->get('selling_rate');
                $income = $exchange_amnt - array_sum($selected_principal);

                $response = [
                    'exchange_amnt' => $exchange_amnt,
                    'principal' => array_sum($selected_principal),
                    'income' => $income,
                ];

                $response_1 = [
                    'grouped_by_rates' => $grouped_by_rates
                ];

                return response()->json([$response, $response_1]);
            }
        }
    }

    public function cutProcessing(Request $request) {
        $branch_stock_details = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('tblforexserials.FSID, tbldenom.SinagRateBuying, tblforexserials.BillAmount, tblforexserials.Serials, tblcurrency.CurrencyID, tblcurrency.Currency, fd.TransactionDate, fd.TransactionNo, fd.Rset, SUM(tblforexserials.BillAmount * tbldenom.SinagRateBuying) as Principal')
            ->join('tblbranch', 'fd.BranchID', 'tblbranch.BranchID')
            ->join('tblcurrency', 'fd.CurrencyID', 'tblcurrency.CurrencyID')
            ->join('tblforexserials', 'fd.FTDID', 'tblforexserials.FTDID')
            ->join('tbldenom', 'tblforexserials.DenomID', 'tbldenom.DenomID')
            ->where('fd.BranchID', '=', $request->get('branch_id'))
            ->where('fd.CurrencyID', '=', $request->get('currency_id'))
            ->where('tblforexserials.FSType', '=', 1)
            ->where('tblforexserials.FSStat', '=', 1)
            ->where('tblforexserials.Sold', '=', 0)
            ->where('tblforexserials.Transfer', '=', 0)
            ->where('tblforexserials.Serials', '!=', null)
            ->groupBy('tblforexserials.FSID', 'tbldenom.SinagRateBuying', 'tblforexserials.BillAmount', 'tblforexserials.Serials', 'tblcurrency.CurrencyID', 'tblcurrency.Currency', 'fd.TransactionDate', 'fd.TransactionNo', 'fd.Rset')
            ->orderBy('fd.TransactionNo', 'DESC')
            // ->paginate(15);
            ->get();

        $response = [
            'branch_stock_details' => $branch_stock_details
        ];

        return response()->json($response);
    }

    public function saveBuffer(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $transfer_forex_remarks = 'BUFFER';
        $raw_date = Carbon::now('Asia/Manila');
        $branch_id = $request->get('branch_id');

        // $cmr_used = $request->get('buffer_type') == 1 ? $request->input('selling_rate') : "0.000000";
        $queued_by = $request->get('buffer_type') == 1 ? $request->input('matched_user_id') : null;

        // $parsed_serials_for_buffer = $request->get('parsed_serials_for_buffer');
        // $array_serials_buffer = explode(',' , $parsed_serials_for_buffer);
        // $admin_stock_details_s = $this->adminStocks()['admin_stock_details_s'];

        $branch_stock_details = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('FS.FSID, tbldenom.SinagRateBuying, fs.BillAmount, fs.Serials, tc.CurrencyID, tc.Currency, fd.TransactionDate, fd.TransactionNo, fd.Rset, SUM(fs.BillAmount * tbldenom.SinagRateBuying) as Principal')
            ->join('tblbranch as tb', 'fd.BranchID', 'tb.BranchID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            ->join('tblforexserials as fs', 'fd.FTDID', 'fs.FTDID')
            ->join('tbldenom', 'fs.DenomID', 'tbldenom.DenomID')
            ->where('fd.BranchID', '=', $request->get('branch_id'))
            ->where('fd.CurrencyID', '=', $request->get('currency_id'))
            ->where('fs.FSType', '=', 1)
            ->where('fs.FSStat', '=', 1)
            ->where('fs.Sold', '=', 0)
            ->where('fs.Transfer', '=', 0)
            ->where('fs.Received', '=', 0)
            ->where('fs.Serials', '!=', null)
            ->groupBy('fs.FSID', 'tbldenom.SinagRateBuying', 'fs.BillAmount', 'fs.Serials', 'tc.CurrencyID', 'tc.Currency', 'fd.TransactionDate', 'fd.TransactionNo', 'fd.Rset')
            ->orderBy('fs.BillAmount', 'DESC')
            ->orderBy('fd.TransactionNo', 'ASC')
            ->get();

        $limit =  $request->get('parsed_bill_amounts');
        $total_amount = 0;
        $selected_ids = [];
        $selected_rates = [];
        // $selected_amount = [];

        foreach ($branch_stock_details as $details) {
            if ($total_amount + $details->BillAmount <= $limit) {
                $selected_ids[] = $details->FSID;
                // $selected_amount[] = $details->BillAmount;
                $selected_rates[] = $details->SinagRateBuying;

                $total_amount += $details->BillAmount;
            }

            if ($total_amount >= $limit) {
                break;
            }
        }

        // Queueing new
            // $branch_update = DB::connection('forex')->table('tblforexserials')
            //     ->when(is_array($selected_ids), function ($query) use ($selected_ids) {
            //         return $query->whereIn('tblforexserials.FSID', $selected_ids);
            //     }, function ($query) use ($selected_ids) {
            //         return $query->where('tblforexserials.FSID', $selected_ids);
            //     });

            // $admin_update = DB::connection('forex')->table('tbladminforexserials')
            //     ->when(is_array($selected_ids), function ($query) use ($selected_ids) {
            //         return $query->whereIn('tbladminforexserials.AFSID', $selected_ids);
            //     }, function ($query) use ($selected_ids) {
            //         return $query->where('tbladminforexserials.AFSID', $selected_ids);
            //     });

            // if (count($branch_update->get())) {
            //     $branch_update->clone()
            //         ->update([
            //             'tblforexserials.CMRUsed' => $request->input('selling_rate'),
            //             'tblforexserials.Queued' => 2,
            //             'tblforexserials.QueuedBy' =>  $request->input('matched_user_id'),
            //         ]);
            // }

            // if (count($admin_update->get())) {
            //     $admin_update->clone()
            //     ->update([
            //         'tbladminforexserials.CMRUsed' => $request->input('selling_rate'),
            //         'tbladminforexserials.Queued' => 2,
            //         'tbladminforexserials.QueuedBy' =>  $request->input('matched_user_id'),
            //     ]);
            // }
        // Queueing new

        CreateNotifications::CreateBranchNotifications([$branch_id], $menu_id, "Buffer cut created by Admin.", 'branch_transactions/transfer_forex');
        // CreateNotifications::CreateBranchNotifications([$branch_id], $menu_id, "Buffer cut created by Admin.", 'branch_transactions/transfer_forex');

        $get_transfer_forex_no = DB::connection('forex')->table('tbltransferforex')
            ->selectRaw('MAX(TransferForexNo) + 1 AS maxTransferForex')
            ->value('maxTransferForex');

        $transfer_forex_id = DB::connection('forex')->table('tbltransferforex')
            ->insertGetId([
                'TransferForexNo' => $get_transfer_forex_no,
                'TransferDate' => $raw_date->toDateString(),
                'TransferTime' => $raw_date->toTimeString(),
                'BranchID' => $branch_id,
                'Remarks' => $transfer_forex_remarks,
                'EntryDate' => $raw_date->toDateTimeString(),
                'UserID' => $request->input('matched_user_id'),
                'Coin' => 5
            ]);

        $get_transfer_buffer_no = DB::connection('forex')->table('tblbuffertransfer')
            ->selectRaw('MAX(BufferNo) + 1 AS maxBufferNo')
            ->value('maxBufferNo');

         DB::connection('forex')->table('tblbuffertransfer')
            ->insertGetId([
                'BufferNo' => $get_transfer_buffer_no,
                'BufferDate' => $raw_date->toDateString(),
                'DollarAmount' => $total_amount,
                'TFID' => $transfer_forex_id,
                'BranchID' => $branch_id,
                'UserID' => $request->input('matched_user_id'),
                'EntryDate' => $raw_date->toDateTimeString(),
                'BufferType' => $request->get('buffer_type'),
                // 'Remarks' => $transfer_forex_remarks,
            ]);

        if (count($selected_ids) == 1) {
            DB::connection('forex')->table('tblforexserials')
                ->where('tblforexserials.FSID', '=', $selected_ids[0])
                ->update([
                    'Transfer' => 1,
                    'TFID' => $transfer_forex_id,
                    'TFUID' => $request->input('matched_user_id'),
                    'FSStat' => 2,
                    'Buffer' => 1,
                    'BufferType' => $request->get('buffer_type'),
                    'Coins' => 5,
                    'CMRUsed' => $request->input('selling_rate'),
                    'QueuedBy' => $request->input('matched_user_id')
                ]);

            DB::connection('forex')->table('tbltransferforexdetails')
                ->insert([
                    'TransferForexID' => $transfer_forex_id,
                    'FSID' => $selected_ids[0],
                    'UserID' => $request->input('matched_user_id'),
                ]);
        } else {
            $IDs = array_map(fn($id) => trim((string) $id), $selected_ids);

            DB::connection('forex')->table('tblforexserials as fs')
                ->when(is_array($IDs), function ($query) use ($IDs) {
                    return $query->whereIn('fs.FSID', $IDs);
                }, function ($query) use ($IDs) {
                    return $query->where('fs.FSID', $IDs);
                })
                ->update([
                    'Transfer' => 1,
                    'TFID' => $transfer_forex_id,
                    'TFUID' => $request->input('matched_user_id'),
                    'FSStat' => 2,
                    'Buffer' => 1,
                    'BufferType' => $request->get('buffer_type'),
                    'Coins' => 5,
                    'CMRUsed' => $request->input('selling_rate'),
                    'QueuedBy' => $request->input('matched_user_id')
                ]);

            foreach($selected_ids as $key => $fsid_val) {
                DB::connection('forex')->table('tbltransferforexdetails')
                    ->insert([
                        'TransferForexID' => $transfer_forex_id,
                        'FSID' => trim($fsid_val),
                        'UserID' => $request->input('matched_user_id'),
                    ]);
            }
        }
    }

    public function transfers(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $queries = DB::connection('forex')->table('tbltransferforex as tfx')
            ->selectRaw('bf.BranchID, bf.BufferID, bf.BufferNo, bf.DollarAmount, tblbranch.BranchCode, tfx.TransferForexNo, tfx.TransferForexID, tfx.ITNo, bf.BufferDate, bf.BufferTransfer, bf.Received, bf.RDate, pawnshop.tblxusers.Name, tc.CurrAbbv, bf.BufferType')
            ->join('tblbranch', 'tfx.BranchID', 'tblbranch.BranchID')
            ->join('tblbuffertransfer as bf', 'tfx.TransferForexID', 'bf.TFID')
            ->join('tblforexserials as fs', 'tfx.TransferForexID', 'fs.TFID')
            ->join('tblforextransactiondetails as fd', 'fs.FTDID', 'fd.FTDID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            ->join('pawnshop.tblxusers', 'bf.UserID', 'pawnshop.tblxusers.UserID')
            ->where('tfx.Voided', '=', 0)
            ->where('tfx.Coin', '=', 5)
            ->orderBy('tfx.TransferForexID', 'DESC')
            ->groupBy('bf.BranchID', 'bf.BufferID', 'bf.BufferNo', 'bf.BufferDate', 'bf.DollarAmount', 'tblbranch.BranchCode', 'tfx.TransferForexNo', 'tfx.TransferForexID', 'tfx.ITNo', 'bf.BufferDate', 'bf.BufferTransfer', 'bf.Received', 'bf.RDate', 'pawnshop.tblxusers.Name', 'tc.CurrAbbv', 'bf.BufferType');

        $result['received_buffers'] = $queries->clone()
            ->where('bf.BufferDate', '>=', '2025-01-01')
            ->where('bf.Received', '=', 1)
            ->orderBy('bf.BufferID', 'DESC')
            ->paginate(15, ['*'], 'received');

        $result['incoming_buffers'] = $queries->clone()
            ->where('bf.BufferDate', '>=', '2025-01-01')
            ->where('bf.Received', '=', 0)
            ->orderBy('bf.BufferID', 'DESC')
            ->paginate(15, ['*'], 'incoming');

        return view('buffer.buffer_stocks', compact('result', 'menu_id'));
    }

    public function receiveTransfers(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['currency'] = DB::connection('forex')->table('tblcurrency')
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->get();

        return view('buffer.receive_buffers', compact('result', 'menu_id'));
    }

    public function searchBuffer(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $search_type = $request->input('search_type');
        $parsed_transf_fx_no = $request->get('parsed_transf_fx_no');
        $transf_fx_no = explode(', ', $parsed_transf_fx_no);

        $buffer_transf_search = [];

        $test = DB::connection('forex')->table('tbltransferforex as tfx')
            ->selectRaw('tfx.TransferForexID AS TFID, tfx.ITNo AS TrackingNo, tfx.TransferForexNo AS TFNO, tfx.TransferDate AS TFDate, tfx.BranchID AS TFBranch, tfx.Remarks AS TFRemarks, DATE(tfx.EntryDate) AS TFEntryDate, rf.Received AS RTReceived, tblbranch.BranchCode AS BranchCode, fd.CurrencyID, tc.Currency, bf.DollarAmount, bf.BufferType')
            ->join('tblbranch', 'tfx.BranchID', 'tblbranch.BranchID')
            ->join('tblbuffertransfer as bf', 'tfx.TransferForexID', 'bf.TFID')
            ->join('tblforexserials as fs', 'tfx.TransferForexID', 'fs.TFID')
            ->join('tblforextransactiondetails as fd', 'fs.FTDID', 'fd.FTDID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            ->leftJoin('tblreceivetransfer as rf', 'tfx.TransferForexID', 'rf.TFID')
            // ->whereBetween('tfx.TransferDate', ['2025-01-01', $raw_date->toDateString()])
            ->whereNull('rf.Received')
            ->whereNotNull('tfx.ITNo')
            ->where('tfx.Voided', '=', 0)
            ->where('tfx.Coin', '=', 5)
            ->where('bf.BufferTransfer', 1)
            ->orderBy('tfx.TransferForexID', 'ASC')
            ->groupBy('tfx.TransferForexID', 'tfx.ITNo', 'tfx.TransferForexNo', 'tfx.TransferDate', 'tfx.BranchID', 'tfx.Remarks', DB::raw('DATE(tfx.EntryDate)'), 'rf.Received', 'tblbranch.BranchCode', 'fd.CurrencyID', 'tc.Currency', 'bf.DollarAmount', 'bf.BufferType');

        switch ($search_type) {
            case 1:
                $buffer_transf_search = $test->get();

                break;
            case 2:
                $buffer_transf_search = $test->when(is_array($transf_fx_no), function ($query) use ($transf_fx_no) {
                        return $query->whereIn('tfx.TransferForexNo', $transf_fx_no);
                    }, function ($query) use ($transf_fx_no) {
                        return $query->where('tfx.TransferForexNo', $transf_fx_no);
                    })
                    ->get();

                break;
            case 3:
                $buffer_transf_search = $test->where('tfx.ITNo', '=', $request->get('tracking_no'))
                    ->get();

                break;
            default:
                dd("no transactions available!");
        }

        $response = [
            'buffer_transf_search' => $buffer_transf_search
        ];

        return response()->json($response);
    }

    public function receive(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        $IDs = $request->get('TFIDs');
        $TFIDs = explode(',' , $IDs);

        $types = $request->get('buffer_types');
        $buffer_types = explode(',' , $types);

        $max_bcno = DB::connection('forex')->table('tblbuffercontrol')
            ->selectRaw('MAX(BCNO) + 1 AS maxBCNO')
            ->value('maxBCNO');

        $buffer_transfer = DB::connection('forex')->table('tblbuffertransfer as bf')
            ->when(is_array($TFIDs), function ($query) use ($TFIDs) {
                    return $query->whereIn('bf.TFID', $TFIDs);
                }, function ($query) use ($TFIDs) {
                    return $query->where('bf.TFID', $TFIDs);
                })
            ->get();
      
        $buff_transf_query = DB::connection('forex')->table('tblbuffertransfer as bt')
            ->when(is_array($TFIDs), function ($query) use ($TFIDs) {
                return $query->whereIn('bt.TFID', $TFIDs);
            }, function ($query) use ($TFIDs) {
                return $query->where('bt.TFID', $TFIDs);
            });

        $get_buff_t = $buff_transf_query->clone()
            ->get();

        foreach ($get_buff_t as $value) {
            DB::connection('forex')->table('tblforexserials as fs')
                ->where('fs.TFID', $value->TFID)
                ->update([
                    'Received' => 1,
                    'BufferID' => $value->BufferID,
                ]);
        }

        $buff_transf_query->clone()
            ->update([
                'Received' => 1,
                'BufferTransfer' => 2,
                'RUserID' => $request->input('matched_user_id'),
                'RDate' => $raw_date->toDateString(),
                'REntryDate' => $raw_date->toDateTimeString(),
            ]);

        foreach ($buffer_transfer as $key => $buff_details) {
            DB::connection('forex')->table('tblreceivetransfer')
                ->insert([
                    'TFID' => $buff_details->TFID,
                    'EntryDate' => $raw_date->toDateTimeString(),
                    'RTDate' => $raw_date->toDateString(),
                    'UserID' => $request->input('matched_user_id'),
                    'Received' => 1,
                    'Remarks' => 'BUFFER'
                ]);

            DB::connection('forex')->table('tblbuffercontrol')
                ->insert([
                    'BCNO' => $max_bcno++,
                    'BCDate' => $raw_date->toDateString(),
                    'BCType' => 1,
                    'DITID' => $buffer_types[$key] == 1 ? 1 : 3,
                    'DollarIn' => $buff_details->DollarAmount,
                    'Balance' => 0,
                    'UserID' => $request->input('matched_user_id'),
                    'EntryDate' => $raw_date->toDateTimeString(),
                    'TFID' => $buff_details->TFID,
                    'BranchID' => $buff_details->BranchID,
                ]);
        }
    }

    public function details(Request $request) {
        $date = Carbon::now('Asia/Manila');
        $date_now = $date->toDateString();

        $result['buffer_transfer_forex_deet'] = DB::connection('forex')->table('tbltransferforexdetails')
            ->join('tblforexserials', 'tbltransferforexdetails.FSID', 'tblforexserials.FSID')
            ->join('tbltransferforex', 'tbltransferforexdetails.TransferForexID', 'tbltransferforex.TransferForexID')
            ->join('tblforextransactiondetails', 'tblforexserials.FTDID', 'tblforextransactiondetails.FTDID')
            ->join('tblcurrency', 'tblforextransactiondetails.CurrencyID', 'tblcurrency.CurrencyID')
            ->where('tblforexserials.Transfer', '=', 1)
            ->where('tblforexserials.FSStat', '=', 2)
            ->where('tbltransferforex.Remarks', '=', 'BUFFER')
            ->where('tbltransferforexdetails.TransferForexID', '=', $request->id)
            ->select(
                'tblforexserials.FSID',
                'tblforexserials.Serials',
                'tblforexserials.BillAmount',
                'tblcurrency.Currency',
            )
            ->paginate(20);

        $result['buffer_transfer'] = DB::connection('forex')->table('tblbuffertransfer')
            ->join('tblbranch', 'tblbuffertransfer.BranchID', '=', 'tblbranch.BranchID')
            ->where('tblbuffertransfer.TFID', '=', $request->id)
            ->get();

        $result['serial_count_per_currency'] = DB::connection('forex')->table('tbltransferforexdetails')
            ->join('tblforexserials', 'tbltransferforexdetails.FSID', 'tblforexserials.FSID')
            ->join('tbltransferforex', 'tbltransferforexdetails.TransferForexID', 'tbltransferforex.TransferForexID')
            ->join('tblforextransactiondetails', 'tblforexserials.FTDID', 'tblforextransactiondetails.FTDID')
            ->join('tblcurrency', 'tblforextransactiondetails.CurrencyID', 'tblcurrency.CurrencyID')
            ->where('tblforexserials.Transfer', '=', 1)
            ->where('tblforexserials.FSStat', '=', 2)
            ->where('tbltransferforexdetails.TransferForexID', '=', $request->id)
            ->selectRaw('tblcurrency.Currency, SUM(tblforexserials.BillAmount) as total_bill_amount, COUNT(tblforexserials.BillAmount) as bill_amount_count')
            ->groupBy('tblcurrency.Currency')
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->get();

        $result['serial_breakdown'] =DB::connection('forex')->table('tbltransferforexdetails')
            ->join('tblforexserials', 'tbltransferforexdetails.FSID', 'tblforexserials.FSID')
            ->join('tbltransferforex', 'tbltransferforexdetails.TransferForexID', 'tbltransferforex.TransferForexID')
            ->join('tblforextransactiondetails', 'tblforexserials.FTDID', 'tblforextransactiondetails.FTDID')
            ->join('tblcurrency', 'tblforextransactiondetails.CurrencyID', 'tblcurrency.CurrencyID')
            ->where('tblforexserials.FSStat', '=', 2)
            ->where('tblforexserials.Transfer', '=', 1)
            ->where('tbltransferforexdetails.TransferForexID', '=', $request->id)
            ->selectRaw('tblforexserials.BillAmount, tblcurrency.Currency, SUM(tblforexserials.BillAmount) as total_bill_amount, COUNT(tblforexserials.BillAmount) as bill_amount_count')
            ->groupBy('tblforexserials.BillAmount', 'tblcurrency.Currency')
            ->orderBy('tblforexserials.BillAmount', 'DESC')
            ->paginate(20);

        return view('buffer.received_buffer_transf_details', compact('result'));
    }

    public function revert(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        $serials = $request->input('parsed_unreceive_serials');
        $parsed_serials = explode(',', $serials);
        $bill_amount = $request->input('parsed_unreceive_bill_amnt');
        $parsed_bill_amnt = explode(',', $bill_amount);
        $buffer_no = $request->input('buffer_transfer_no');
        $transfer_forex_id = $request->input('transfer_forex_id');

        $buffer_transfer = DB::connection('forex')->table('tblbuffertransfer')
            ->where('tblbuffertransfer.BufferNo', '=', intval($buffer_no))
            ->first();

        if ($buffer_transfer) {
            $max_bcno = DB::connection('forex')->table('tblbuffercontrol')
                ->selectRaw('MAX(BCNO) + 1 AS maxBCNO')
                ->value('maxBCNO');

            DB::connection('forex')->table('tblbuffercontrol')
                ->insert([
                    'BCNO' => $max_bcno,
                    'BCDate' => $raw_date->toDateString(),
                    'BCType' => 2,
                    'DOTID' => 1,
                    'DollarOut' => array_sum($parsed_bill_amnt),
                    'Balance' => 0,
                    'UserID' => $request->input('matched_user_id'),
                    'EntryDate' => $raw_date->toDateTimeString(),
                    'TFID' => $transfer_forex_id,
                    'BranchID' => $buffer_transfer->BranchID,
                ]);

            DB::connection('forex')->table('tblforexserials')
                ->when(is_array($parsed_serials), function ($query) use ($parsed_serials) {
                    return $query->whereIn('tblforexserials.FSID', $parsed_serials);
                }, function ($query) use ($parsed_serials) {
                    return $query->where('tblforexserials.FSID', $parsed_serials);
                })
                ->update([
                    'Transfer' => 0,
                    'Received' => 0,
                    'TFID' => 0,
                    'TFUID' => 0,
                    'FSStat' => 1,
                ]);

            DB::connection('forex')->table('tblbuffertransfer')
                ->where('tblbuffertransfer.BufferNo', '=', intval($buffer_no))
                ->update([
                    'DollarAmount' => $buffer_transfer->DollarAmount - array_sum($parsed_bill_amnt)
                ]);
        }
    }

    public function incomingBuffDetails(Request $request) {
        $result['buff_cut_details'] = DB::connection('forex')->table('tbltransferforexdetails')
            ->join('tblforexserials', 'tbltransferforexdetails.FSID', 'tblforexserials.FSID')
            ->join('tbltransferforex', 'tbltransferforexdetails.TransferForexID', 'tbltransferforex.TransferForexID')
            ->join('tblforextransactiondetails', 'tblforexserials.FTDID', 'tblforextransactiondetails.FTDID')
            ->join('tblcurrency', 'tblforextransactiondetails.CurrencyID', 'tblcurrency.CurrencyID')
            ->where('tbltransferforexdetails.TransferForexID', '=', $request->get('transfer_fx_id'))
            ->select(
                'tblcurrency.Currency',
                'tblforexserials.BillAmount',
                'tblforexserials.Serials',
                'tbltransferforex.TransferDate',
                'tbltransferforex.TransferForexID',
                'tblforexserials.FSID',
            )
            ->get();

        return view("buffer.incoming_buff_details", compact('result'));
    }

    public function revertBuffer(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $not_s_stocks = $this->stocksForAcknowledgement();

        $selected_ids = explode(',' , $request->get('FSIDS'));
        $currency_amnt = explode(',' , $request->get('currency_amnt'));

        DB::connection('forex')->table('tblforexserials as fs')
            ->when(is_array($selected_ids), function ($query) use ($selected_ids) {
                return $query->whereIn('fs.FSID', $selected_ids);
            }, function ($query) use ($selected_ids) {
                return $query->where('fs.FSID', $selected_ids);
            })->update([
                'Transfer' => 0,
                'QueuedBy' => null,
                'TFID' => 0,
                'TFUID' => 0,
                'Buffer' => 0,
                'FSStat' => 1,
                'CMRUsed' => 0.000000,
            ]);

        DB::connection('forex')->table('tbltransferforexdetails as fxd')
            ->where('fxd.TransferForexID', $request->get('transfer_forex_id'))
            ->when(is_array($selected_ids), function ($query) use ($selected_ids) {
                return $query->whereIn('fxd.FSID', $selected_ids);
            }, function ($query) use ($selected_ids) {
                return $query->where('fxd.FSID', $selected_ids);
            })->delete();

        $buff_transf_query = DB::connection('forex')->table('tblbuffertransfer')
            ->where('TFID', $request->get('transfer_forex_id'));

        $get_amount = $buff_transf_query->clone()
            ->pluck('DollarAmount');

        if (floatval($get_amount[0]) == array_sum($currency_amnt)) {
            DB::connection('forex')->table('tbltransferforex as tfx')
                ->where('tfx.TransferForexID', $request->get('transfer_forex_id'))
                ->update([
                    'Voided' => 1
                ]);
        }

        $buff_transf_query->update([
            'DollarAmount' => DB::raw('DollarAmount -' . array_sum($currency_amnt))
        ]);

        $not_selected_ids = explode(',' , $request->get('not_FSIDS'));
        $not_currency_amnt = explode(',' , $request->get('not_s_currency_amnt'));

        DB::connection('forex')->table('tblbuffertransfer')
            ->where('tblbuffertransfer.TFID', '=', $request->get('transfer_forex_id'))
            ->update([
                'BufferTransfer' => 1,
                'BTby' => Auth::user()->getBranch()->BranchID,
                'BTDate' => $raw_date->toDateString(),
                'BTEntryDate' => $raw_date->toDateTimeString(),
            ]);

        $cmr_used = 0;
        $queued_by = '';

        $queueing_details = DB::connection('forex')->table('tblforexserials as fs')
            ->where('fs.TFID', $request->get('transfer_forex_id'));

        $cmr_used = $queueing_details->clone()
            ->selectRaw('MAX(fs.CMRUsed)')
            ->value('CMRUsed');

        $queued_by = $queueing_details->clone()
            ->selectRaw('MAX(fs.QueuedBy)')
            ->value('QueuedBy');

        if (intval($request->get('buffer_type')) == 1) {
            $limit = array_sum($not_currency_amnt);
            $transcap_amnt = $limit * $cmr_used;

            $total_amount = 0;
            $selected_ids = [];
            $selected_rates = [];

            foreach ($not_s_stocks as $details) {
                if ($total_amount + $details->BillAmount <= $limit) {
                    $selected_ids[] = $details->ID;
                    $selected_rates[] = $details->SinagRateBuying;

                    $total_amount += $details->BillAmount;
                }

                if ($total_amount >= $limit) {
                    break;
                }
            }

            $branch_update = DB::connection('forex')->table('tblforexserials')
                ->when(is_array($selected_ids), function ($query) use ($selected_ids) {
                    return $query->whereIn('tblforexserials.FSID', $selected_ids);
                }, function ($query) use ($selected_ids) {
                    return $query->where('tblforexserials.FSID', $selected_ids);
                });

            $admin_update = DB::connection('forex')->table('tbladminforexserials')
                ->when(is_array($selected_ids), function ($query) use ($selected_ids) {
                    return $query->whereIn('tbladminforexserials.AFSID', $selected_ids);
                }, function ($query) use ($selected_ids) {
                    return $query->where('tbladminforexserials.AFSID', $selected_ids);
                });

            if (count($branch_update->get())) {
                $branch_update->clone()
                    ->update([
                        'tblforexserials.CMRUsed' => $cmr_used ,
                        'tblforexserials.Queued' => 1,
                        'tblforexserials.QueuedBy' =>  $queued_by,
                        'tblforexserials.BufferType' => 1,
                    ]);
            }

            if (count($admin_update->get())) {
                $admin_update->clone()
                    ->update([
                        'tbladminforexserials.CMRUsed' => $cmr_used ,
                        'tbladminforexserials.Queued' => 1,
                        'tbladminforexserials.QueuedBy' =>  $queued_by,
                        'tbladminforexserials.BufferType' => 1,
                    ]);
            }

            $max_bcno = DB::connection('forex')->table('tblbuffercontrol')
                ->selectRaw('MAX(BCNO) + 1 AS maxBCNO')
                ->value('maxBCNO');

            DB::connection('forex')->table('tblbuffercontrol')
                ->insert([
                    'BCNO' => $max_bcno,
                    'BCDate' => $raw_date->toDateString(),
                    'BCType' => 2,
                    'DOTID' => 2,
                    'DollarOut' => $limit,
                    'Balance' => 0,
                    'UserID' => $queued_by,
                    'EntryDate' => $raw_date->toDateTimeString(),
                    'TFID' => $request->get('transfer_forex_id'),
                    'BranchID' => Auth::user()->getBranch()->BranchID,
                ]);

            DB::connection('forex')->table('tblforexserials as fs')
                ->where('fs.TFID', $request->get('transfer_forex_id'))
                ->update([
                    'fs.CMRUsed' => 0.000000,
                    'fs.QueuedBy' => null
                ]);
        } else {
            $limit = array_sum($not_currency_amnt);
            $transcap_amnt = $limit * $cmr_used;

            $max_tc_no = DB::connection('forex')->table('tblfxtranscap')
                ->selectRaw('CASE WHEN MAX(TCNo) IS NULL THEN 1 ELSE MAX(TCNo) + 1 END AS maxTCNo')
                ->value('maxTCNo');

            DB::connection('forex')->table('tblfxtranscap')
                ->insert([
                    'TCNo' => $max_tc_no,
                    'BranchID' => Auth::user()->getBranch()->BranchID,
                    'TranscapAmount' => $transcap_amnt,
                    'UserID' => $queued_by,
                ]);

            DB::connection('forex')->table('tblforexserials as fs')
                ->where('fs.TFID', $request->get('transfer_forex_id'))
                ->update([
                    'fs.CMRUsed' => 0.000000,
                    'fs.QueuedBy' => null
                ]);
        }

        return response()->json($selected_ids);
    }

    public function wallet(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $admin_stock_details_s = $this->adminStocks()['admin_stock_details_s'];

        $query = DB::connection('forex')->table('tblbuffercontrol as bfc')
            ->join('pawnshop.tblxusers as tbx', 'bfc.UserID', 'tbx.UserID')
            ->join('tblbranch as tb', 'bfc.BranchID', 'tb.BranchID')
            ->leftJoin('tbldollarintype as di', 'bfc.DITID', 'di.DITID')
            ->leftJoin('tbldollarouttype as do', 'bfc.DOTID', 'do.DOTID')
            ->leftJoin('tbltransferforex as tfx', 'bfc.TFID', 'tfx.TransferForexID')
            ->selectRaw('bfc.BCID, bfc.BCNO, bfc.BCDate, bfc.BCType, di.DollarInType, do.DollarOutType, bfc.DollarIn, bfc.DollarOut, bfc.Balance, bfc.Remarks, tb.BranchCode, tbx.Name')
            ->groupBy('bfc.BCID', 'bfc.BCNO', 'bfc.BCDate', 'bfc.BCType', 'di.DollarInType', 'do.DollarOutType', 'bfc.DollarIn', 'bfc.DollarOut', 'bfc.Balance', 'bfc.Remarks', 'tb.BranchCode', 'tbx.Name');

        $result['buffer_in'] = $query->clone()->where('bfc.BCType', 1)
            // ->where('bfc.BCDate', '>=', '2025-01-01')
            ->where('bfc.BCDate', '>=',  '2025-01-01')
            ->orderBy('bfc.BCID', 'DESC')
            ->paginate(10, ['*'], 'buffer_in');

        $result['buffer_out'] = $query->clone()->where('bfc.BCType', 2)
            // ->where('bfc.BCDate', '>=', '2025-01-01')
            ->where('bfc.BCDate', '>=',  '2025-01-01')
            ->orderBy('bfc.BCID', 'DESC')
            ->paginate(10, ['*'], 'buffer_out');

        $query = DB::connection('forex')->table('tblbufferfinancing as bf')
            ->leftJoin('tblcurrency as tc', 'bf.CurrencyID', '=', 'tc.CurrencyID')
            ->join('pawnshop.tblxusers', 'bf.UserID', '=', 'pawnshop.tblxusers.UserID')
            ->where(function ($query) {
                $query->where('bf.Remarks', 'NOT LIKE', '%WU commission%')
                    ->where('bf.Remarks', 'NOT LIKE', '%WU  Comm.%')
                    ->where('bf.Remarks', 'NOT LIKE', '%WU Comm.%');
            })
            ->where('bf.BFDate', '>=',  '2025-01-01')
            ->orderBy('bf.BFID', 'DESC');

        $result['totality'] = $query->clone()
            ->selectRaw('SUM(bf.DollarAmount) as buffer_totality')
            ->first();

        $result['dollar_in'] = DB::connection('forex')->table('tblbuffercontrol as bc')
            ->selectRaw('SUM(bc.DollarIn) as total_dollar_in')
            ->where('bc.BCDate', '>=',  '2025-01-01')
            ->first();

        $result['dollar_out'] = DB::connection('forex')->table('tblbuffercontrol as bc')
            ->selectRaw('SUM(bc.DollarOut) as total_dollar_out')
            // ->join('tblbuffertransfer as bt', 'bc.TFID',  'bt.TFID')
            ->where('bc.BCDate', '>=',  '2025-01-01')
            ->first();

        $result['dollar_out_card'] = DB::connection('forex')->table('tblbuffercontrol as bc')
            ->selectRaw('SUM(bc.DollarOut) as total_dollar_out')
            ->join('tblbuffertransfer as bt', 'bc.TFID',  'bt.TFID')
            ->where('bt.Received', 0)
            ->where('bc.BCDate', '>=',  '2025-01-01')
            ->first();

        $result['buffer_in_out'] = DB::connection('forex')->table('tblbuffercontrol')
            ->selectRaw('SUM(DollarIn - DollarOut) as Balance')
            ->where('tblbuffercontrol.BCDate', '>=',  '2025-01-01')
            ->first();

        $result['incoming_buffer_amnt'] = DB::connection('forex')->table('tblbuffertransfer')
            ->selectRaw('SUM(DollarAmount) as Balance')
            ->join('tbltransferforex', 'tblbuffertransfer.TFID', 'tbltransferforex.TransferForexID')
            ->where('tblbuffertransfer.BufferDate', '>=',  '2025-01-01')
            // ->whereNotNull('tbltransferforex.ITNo')
            ->where('tblbuffertransfer.Received', 0)
            ->first();

        $avail_breakdown = $this->adminStocks()['a_buffer_bal_breakdown'];

        return view('buffer.buffer_wallet', compact('result', 'menu_id', 'avail_breakdown'));
    }

    public function financing(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['currency'] = DB::connection('forex')->table('tblcurrency as tc')
            ->orderBy('tc.Currency', 'ASC')
            ->get();

        $query = DB::connection('forex')->table('tblbufferfinancing as bf')
            ->leftJoin('tblcurrency as tc', 'bf.CurrencyID', '=', 'tc.CurrencyID')
            ->join('pawnshop.tblxusers', 'bf.UserID', '=', 'pawnshop.tblxusers.UserID')
            ->where(function ($query) {
                $query->where('bf.Remarks', 'NOT LIKE', '%WU commission%')
                    ->where('bf.Remarks', 'NOT LIKE', '%WU  Comm.%')
                    ->where('bf.Remarks', 'NOT LIKE', '%WU Comm.%');
            })
            ->where('bf.BFDate', '>=',  '2025-01-01')
            ->orderBy('bf.BFID', 'DESC');

        $result['buffer_financing'] = $query->clone()
            ->selectRaw('bf.BFID, bf.BFNo, bf.BFDate, tc.Currency, bf.Remarks, bf.DollarAmount, bf.Principal, bf.Received')
            ->groupBy('bf.BFID', 'bf.BFNo', 'bf.BFDate', 'tc.Currency', 'bf.Remarks', 'bf.DollarAmount', 'bf.Principal', 'bf.Received')
            ->paginate(25);

        $result['totality'] = $query->clone()
            ->selectRaw('SUM(bf.DollarAmount) as buffer_totality')
            ->first();

        $result['total_principal'] = $query->clone()
            ->selectRaw('SUM(bf.Principal) as total_principal')
            ->first();

        return view('buffer.buffer_financing', compact('result', 'menu_id'));
    }

    public function saveFinancing(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        $BFNo = DB::connection('forex')->table('tblbufferfinancing as bf')
            ->selectRaw('MAX(BFNo) + 1 AS maxBFNo')
            ->value('maxBFNo');

        $BFID = DB::connection('forex')->table('tblbufferfinancing')
            ->insertGetId([
                'BFNo' => $BFNo,
                'BFDate' => $raw_date->toDateString(),
                'CurrencyID' => $request->input('currency'),
                'DollarAmount' => $request->input('buffer-amount'),
                'Principal' => $request->input('principal-amount'),
                'Remarks' => $request->input('remarks'),
                'UserID' => $request->get('matched_user_id'),
                'BFDate' => $raw_date->toDateTimeString()
            ]);

        $response = [
            'BFID' => $BFID
        ];

        return response()->json($response);
    }

    public function breakdownBuffer(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $BFID = $request->BFID;

        $result['buffer_details'] = DB::connection('forex')->table('tblbufferfinancing as bf')
            ->selectRaw('bf.BFID, bf.BFNo, bf.BFDate, tc.Currency, tc.CurrencyID, bf.Remarks, bf.DollarAmount, bf.Principal, bf.Received')
            ->join('tblcurrency as tc', 'bf.CurrencyID', '=', 'tc.CurrencyID')
            ->join('pawnshop.tblxusers', 'bf.UserID', '=', 'pawnshop.tblxusers.UserID')
            ->where('bf.BFID', $request->BFID)
            ->groupBy('bf.BFID', 'bf.BFNo', 'bf.BFDate', 'tc.Currency', 'tc.CurrencyID', 'bf.Remarks', 'bf.DollarAmount', 'bf.Principal', 'bf.Received')
            ->get();

        $result['breakdown'] = DB::connection('forex')->table('tblbufferfinancing as bf')
            ->selectRaw('afs.AFSID, bf.BFID, afs.BillAmount, tc.CurrencyID, tc.Currency, adm.SinagRateBuying, afs.Serials')
            ->join('tbladminforexserials as afs', 'bf.BFID', 'afs.BFID')
            ->join('tbladmindenom as adm', 'afs.ADenomID', 'adm.ADenomID')
            ->join('tblcurrency as tc', 'bf.CurrencyID', '=', 'tc.CurrencyID')
            ->where('bf.BFID', $request->BFID)
            ->groupBy('afs.AFSID', 'bf.BFID', 'afs.BillAmount', 'tc.CurrencyID', 'tc.Currency', 'adm.SinagRateBuying', 'afs.Serials')
            ->paginate(10);

        $result['denom_details'] = DB::connection('forex')->table('tbladmindenom')
            ->where('tbladmindenom.BFID', '=' , $request->BFID)
            ->get();

        return view('buffer.buffer_breakdown', compact('result', 'menu_id', 'BFID'));
    }

    public function denominations(Request $request) {
        $currency_denom = DB::connection('forex')->table('tblcurrencydenom AS cd')
            ->where('cd.TransType' , '=' , 1)
            ->where('cd.CurrencyID' , '=' , $request->get('currency_id'))
            ->where('cd.StopBuying' , '=' , 0)
            ->where('cd.BranchID' , '=' , Auth::user()->getBranch()->BranchID)
            ->orderBy('cd.BillAmount' , 'DESC')
            ->get();

        $response = [
            'currency_denom' => $currency_denom,
        ];

        return response()->json($response);
    }

    public function saveBreakdownBuff(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        $get_buffer_no = DB::connection('forex')->table('tblbuffertransfer')
            ->selectRaw('CASE WHEN MAX(BufferNo) IS NULL THEN 1 ELSE MAX(BufferNo) + 1 END AS latestBufferNo')
            ->value('latestBufferNo');

        // For tbldenom and pending serial saving
        $bill_amount = $request->input('bill-amount-count');
        $bill_amount_parsed = explode(',' , $bill_amount);

        $multip = $request->input('multiplier-total-count');
        $multip_parsed = explode(',' , $multip);
        $multip_val_to_int = array_map('intval' , $multip_parsed);

        $subutotal = $request->input('subtotal-count');
        $subutotal_parsed = explode(',' , $subutotal);

        // $sinag_buying_rate = $request->input('sinag-buying-rate-count');
        // $sinag_buying_rate_parsed = explode(',' , $sinag_buying_rate);

        $sinag_buying_rate_new = $request->input('sinag-buying-rate-count');
        $buying_raw_new_array = explode(',' , $sinag_buying_rate_new);

        $sinag_var_buying = $request->input('sinag-var-buying');
        $sinag_var_buying_parsed = explode(',' , $sinag_var_buying);

        // For denomination and pending serials saving
        $multip_parsed_processed = collect($multip_parsed)->filter(function ($value) {
            return $value !== "0";
        })->toArray();

        $multip_parsed_array = [];

        foreach ($multip_parsed_processed as $index => $value) {
            if (isset($multip_parsed_processed[$index])) {
                $multip_parsed_array[] = $value;
            }
        }

        $multip_val_to_int_processed = collect($multip_val_to_int)->filter(function ($value) {
            return $value !== 0;
        })->toArray();

        $multip_val_to_int_array = [];

        foreach ($multip_val_to_int_processed as $index => $value) {
            if (isset($multip_parsed_processed[$index])) {
                $multip_val_to_int_array[] = $value;
            }
        }

        $bill_amount_parsed_processed = collect($bill_amount_parsed)->filter(function ($value) {
            return $value !== "0";
        })->toArray();

        $new_bill_amount_array = [];

        foreach ($bill_amount_parsed_processed as $index => $value) {
            if (isset($multip_parsed_processed[$index])) {
                $new_bill_amount_array[] = $value;
            }
        }

        $sinag_buying_rate_processed = collect($sinag_buying_rate_new)->filter(function ($value) {
            return $value !== "0";
        })->toArray();

        $new_sinag_buying_rate_array = [];

        foreach ($sinag_buying_rate_processed as $index => $value) {
            if (isset($multip_parsed_processed[$index])) {
                $new_sinag_buying_rate_array[] = $value;
            }
        }

        $subutotal_parsed_processed = collect($subutotal_parsed)->filter(function ($value) {
            return $value !== "0";
        })->toArray();

        $subutotal_parsed_array = [];

        foreach ($subutotal_parsed_processed as $index => $value) {
            if (isset($multip_parsed_processed[$index])) {
                $subutotal_parsed_array[] = $value;
            }
        }

        $sinag_var_buying_processed = collect($sinag_var_buying_parsed)->filter(function ($value) {
            return $value !== "0";
        })->toArray();

        $sinag_var_buying_parsed_array = [];

        foreach ($sinag_var_buying_processed as $index => $value) {
            if (isset($multip_parsed_processed[$index])) {
                $sinag_var_buying_parsed_array[] = $value;
            }
        }

        $subutotal_parsed_processed = array_values($subutotal_parsed_processed);
        $multip_parsed_processed = array_values($multip_parsed_processed);

        foreach ($multip_parsed_processed as $key_test => $value_test) {
            DB::connection('forex')->table('tbladmindenom')->insert([
                'BFID' => $request->get('buff_id'),
                'BillAmount' => $new_bill_amount_array[$key_test],
                'Multiplier' => $multip_parsed_array[$key_test],
                'Total' => $subutotal_parsed_array[$key_test],
                'SinagRateBuying' => $buying_raw_new_array[$key_test],
                // 'VarianceBuying' => $sinag_var_buying_parsed_array[$key_test],
            ]);
        }

        $get_tbldenom_denom_id = DB::connection('forex')->table('tbladmindenom')
            ->where('tbladmindenom.BFID' , '=' , $request->get('buff_id'))
            ->select('tbladmindenom.ADenomID')
            ->get();

        foreach ($multip_val_to_int_array as $multip_key => $multip_value) {
            $new_set_index = array_fill(0, $multip_value, null);

            foreach($new_set_index as $new_index_key => $new_index_value) {
                DB::connection('forex')->table('tbladminforexserials')->insert([
                    'BFID' => $request->get('buff_id'),
                    'BillAmount' => $new_bill_amount_array[$multip_key],
                    'ADenomID' => $get_tbldenom_denom_id[$multip_key]->ADenomID,
                    'Serials' => null,
                    'UserID' => $request->input('matched_user_id'),
                    'FSType' => 1,
                    'Buffer' => 1,
                    'BufferType' => 2,
                ]);
            }
        }

        DB::connection('forex')->table('tblbuffertransfer')
            ->insert([
                'BufferNo' => $get_buffer_no,
                'BufferDate' => $raw_date->toDateString(),
                'DollarAmount' => $request->input('current_amount_true'),
                'BranchID' => Auth::user()->getBranch()->BranchID,
                'UserID' => $request->input('matched_user_id'),
                'EntryDate' => $raw_date->toDateTimeString(),
                'BufferTransfer' => 2,
                'BTBy' => $request->input('matched_user_id'),
                'BTDate' => $raw_date->toDateString(),
                'BTEntryDate' => $raw_date->toDateTimeString(),
                // 'Received' => 1,
                // 'RDate' => $raw_date->toDateString(),
                // 'RUserID' => $request->input('matched_user_id'),
                // 'RDate' => $raw_date->toDateTimeString(),
                'BufferType' => 2,
            ]);

        $max_bcno = DB::connection('forex')->table('tblbuffercontrol')
            ->selectRaw('MAX(BCNO) + 1 AS maxBCNO')
            ->value('maxBCNO');

        DB::connection('forex')->table('tblbuffercontrol')
            ->insert([
                'BCNO' => $max_bcno,
                'BCDate' => $raw_date->toDateString(),
                'DITID' => 2,
                'BCType' => 1,
                'DollarIn' => $request->input('current_amount_true'),
                'Balance' => 0,
                'UserID' => $request->input('matched_user_id'),
                'EntryDate' => $raw_date->toDateTimeString(),
                'BranchID' =>  Auth::user()->getBranch()->BranchID,
                'Remarks' => $request->input('remarks'),
            ]);

        DB::connection('forex')->table('tblbufferfinancing as bf')
            ->where('bf.BFID', $request->input('BFID'))
            ->update([
                'EntryDate' => $raw_date->toDateTimeString(),
                'Received' => 1,
                'RDate' => $raw_date->toDateString(),
                'REntryDate' => $raw_date->toDateTimeString(),
            ]);
    }

    public function bufferSerials(Request $request) {
        $date = Carbon::now('Asia/Manila');
        $date_now = $date->toDateString();

        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['pending_serials'] = DB::connection('forex')->table('tbladminforexserials AS fs')
            ->selectRaw('fs.AFSID, fs.BFID, tc.Currency, fs.Serials, fs.BillAmount, fs.EntryDate')
            ->leftJoin('tblbufferfinancing AS bf', 'fs.BFID', '=', 'bf.BFID')
            ->leftJoin('tblcurrency as tc', 'bf.CurrencyID', 'tc.CurrencyID')
            ->where('fs.BFID', $request->BFID)
            ->groupBy('fs.AFSID', 'fs.BFID', 'tc.Currency', 'fs.Serials', 'fs.BillAmount', 'fs.EntryDate')
            ->get();

        return view('buffer.buffer_serials', compact('result', 'menu_id'));
    }
    // Old buffer approach
    public function cutValidation(Request $request) {
        $admin_stock_details_s = $this->adminStocks()['admin_stock_details_s'];

        $parsed_serials_for_buffer = $request->get('parsed_serials_for_buffer');
        $parse_bill_amounts = $request->get('parse_bill_amounts');

        $array_serials_buffer = explode(',' , $parsed_serials_for_buffer);
        $array_bills_buffer = explode(',' , $parse_bill_amounts);

        $limit = array_sum($array_bills_buffer);
        $total_amount = 0;
        $selected_ids = [];
        $selected_rates = [];
        // $selected_amount = [];

        foreach ($admin_stock_details_s as $details) {
            if ($total_amount + $details->BillAmount <= $limit) {
                $selected_ids[] = $details->ID;
                // $selected_amount[] = $details->BillAmount;
                $selected_rates[] = $details->SinagRateBuying;

                $total_amount += $details->BillAmount;
            }

            if ($total_amount >= $limit) {
                break;
            }
        }

        $validity = $total_amount < $limit ? 0 : 1;

        $response = [
            'validity' => $validity
        ];

        return response()->json($response);
    }
    // Old buffer approach
    public function stocks(Request $request) {
        $admin_stock_details_s = $this->adminStocks()['admin_stock_details_s'];
        $branch_id = $request->branch_id;

        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['branch_stock_details'] = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('tblforexserials.FSID, tbldenom.SinagRateBuying, tblforexserials.BillAmount, tblforexserials.Serials, tblcurrency.CurrencyID, tblcurrency.Currency, fd.TransactionDate, fd.TransactionNo, fd.Rset, SUM(tblforexserials.BillAmount * tbldenom.SinagRateBuying) as Principal')
            ->join('tblbranch', 'fd.BranchID', 'tblbranch.BranchID')
            ->join('tblcurrency', 'fd.CurrencyID', 'tblcurrency.CurrencyID')
            ->join('tblforexserials', 'fd.FTDID', 'tblforexserials.FTDID')
            ->join('tbldenom', 'tblforexserials.DenomID', 'tbldenom.DenomID')
            ->where('fd.CurrencyID', '=', 11)
            ->where('fd.BranchID', '=', $branch_id)
            ->where('tblforexserials.FSType', '=', 1)
            ->where('tblforexserials.FSStat', '=', 1)
            ->where('tblforexserials.Sold', '=', 0)
            ->where('tblforexserials.Transfer', '=', 0)
            ->where('tblforexserials.Serials', '!=', null)
            ->groupBy('tblforexserials.FSID', 'tbldenom.SinagRateBuying', 'tblforexserials.BillAmount', 'tblforexserials.Serials', 'tblcurrency.CurrencyID', 'tblcurrency.Currency', 'fd.TransactionDate', 'fd.TransactionNo', 'fd.Rset')
            ->orderBy('fd.TransactionNo', 'DESC')
            // ->paginate(15);
            ->get();

        return view('buffer.buffer_cut_details', compact('result', 'branch_id', 'menu_id'));
    }
}
