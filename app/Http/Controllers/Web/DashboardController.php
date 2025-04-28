<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Validator;
use App;
use Lang;
use App\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use DB;
use Hash;
use Auth;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use App\Helpers\MenuManagement;
use App\Http\Requests\AuthenticateRequest;

class DashboardController extends Controller {
    protected function queries() {
        $stocks = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->join('tblcurrency as tc', 'fd.CurrencyID', '=', 'tc.CurrencyID')
            ->join('tblforexserials as fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->where('fd.Voided' , 0)
            ->where('fs.Sold' , '=' , 0)
            ->where('fs.Queued' , '=' , 0)
            ->where('fs.SoldToManila' , '=' , 0)
            ->where('fs.Serials', '!=', null)
            ->where('fd.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->where('fd.TransactionDate', '>=', '2025-01-01')
            ->where('fd.Rset', '=', 'O');
            
        return [
            'stocks' => $stocks
        ];
    }

    public function branchdDashboard(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $r_set =  session('time_toggle_status') == 1 ? 'O' : '';

        $result['buying_sales'] = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->selectRaw('SUM(fd.Amount) as Amount, COUNT(fd.FTDID) as transct_count')
            ->where('fd.Voided', 0)
            ->where('fd.TransactionDate', '=', $raw_date->toDateString())
            ->where('fd.TransactionDate', '=', $raw_date->toDateString())
            ->where('fd.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->when($r_set == 'O', function($query) use ($r_set) {
                return $query->where('fd.Rset', '=', $r_set);
            })
            ->get();

        $result['selling_sales'] = DB::connection('forex')->table('tblsoldcurrdetails as sc')
            ->selectRaw('SUM(sc.AmountPaid) as Amount, COUNT(sc.SCID) as transct_count')
            ->where('sc.Voided', 0)
            ->where('sc.DateSold', '=', $raw_date->toDateString())
            ->where('sc.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->when($r_set == 'O', function($query) use ($r_set) {
                return $query->where('sc.Rset', '=', $r_set);
            })
            ->get();

        $current_rate_query = DB::connection('forex')->table('tblcurrencydenom as tcd')
            ->selectRaw('tc.CurrencyID, tc.Currency, FLOOR(tcd.SinagRateBuying) as whole_b_rate, tcd.SinagRateBuying, (tcd.SinagRateBuying - FLOOR(tcd.SinagRateBuying)) as b_decimal_rate, FLOOR(tcd.SinagRateSelling) as whole_s_rate, tcd.SinagRateSelling, (tcd.SinagRateSelling - FLOOR(tcd.SinagRateSelling)) as s_decimal_rate, REPLACE(GROUP_CONCAT(tcd.BillAmount), ",", " - ") as Denomination')
            ->join('tblcurrency as tc', 'tcd.CurrencyID', 'tc.CurrencyID')
            ->join('tbltransactiontype as tt', 'tcd.TransType', 'tt.TTID')
            ->where('tcd.BranchID', Auth::user()->getBranch()->BranchID)
            ->where('tt.Active', 1)
            ->whereIn('tcd.TransType', [1])
            ->where('tcd.StopBuying', 0)
            ->where('tcd.SinagRateBuying', '>', 0)
            ->where('tcd.SinagRateSelling', '>', 0);
     
        $result['general_rates'] = $current_rate_query->clone()
            ->whereNotIn('tc.CurrencyID', [11, 12, 24, 27])
            ->groupBy('tc.CurrencyID', 'tc.Currency', 'tcd.SinagRateBuying', 'whole_b_rate', 'b_decimal_rate', 'tcd.SinagRateSelling', 'whole_s_rate', 's_decimal_rate')
            ->orderBy('tc.Currency', 'ASC')
            ->orderBy('tcd.SinagRateBuying', 'DESC')
            ->get();

        $result['priority_rates'] = $current_rate_query->clone()
            ->whereIn('tc.CurrencyID', [11, 12, 24, 27])
            ->groupBy('tc.CurrencyID', 'tc.Currency', 'tcd.SinagRateBuying', 'whole_b_rate', 'b_decimal_rate', 'tcd.SinagRateSelling', 'whole_s_rate', 's_decimal_rate')
            ->orderByRaw("FIELD(tc.CurrencyID, 11, 12, 27, 24)")
            ->orderBy('tcd.SinagRateBuying', 'DESC')
            ->get();

        $result['dpofx_rate'] = DB::connection('forex')->table('tbldpoindirate as dpo')
            ->where('dpo.BranchID', Auth::user()->getBranch()->BranchID)
            ->pluck('dpo.Rate');

        $branch_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('fd.CurrencyID, COUNT(fd.CurrencyID) AS Cnt, SUM(CASE WHEN fs.Queued = 0 THEN fs.BillAmount ELSE 0 END) AS TotalCurrencyAmount, SUM(fs.BillAmount * d.SinagRateBuying) AS Principal, MAX(DATEDIFF(CURDATE(), fd.TransactionDate)) AS stock_days')
            // ->selectRaw('fd.CurrencyID, COUNT(fd.CurrencyID) AS Cnt, SUM(CASE WHEN fs.Queued = 0 THEN fs.BillAmount ELSE 0 END) AS TotalCurrencyAmount, SUM(fs.BillAmount * d.SinagRateBuying) AS Principal, DATEDIFF(CURDATE(), MIN(fd.TransactionDate)) AS min_days, DATEDIFF(CURDATE(), MAX(fd.TransactionDate)) AS max_days, DATEDIFF(CURDATE(), fd.TransactionDate) AS stock_days')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tbldenom AS d', 'fs.DenomID', '=', 'd.DenomID')
            ->where('fd.TransactionDate', '>=', '2025-01-01')
            ->where('fd.Voided', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.Transfer', 0)
            ->whereNotNull('fs.FTDID')
            ->where('fs.Received', 0)
            ->where('fs.SoldToManila', '=', 0)
            ->where('fs.FSStat', 1)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fd.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->when($r_set == 'O', function($query) use ($r_set) {
                return $query->where('fd.Rset', '=', $r_set);
            })
            ->groupBy('fd.CurrencyID');
            // ->groupBy('fd.CurrencyID', 'stock_days');

        $result['available_stocks'] = DB::connection('forex')->table('tblcurrency AS c')
            ->leftJoinSub($branch_stocks_query, 'bs', function($join) {
                $join->on('c.currencyid', '=', 'bs.CurrencyID');
            })
            ->selectRaw("c.CurrencyID, c.Currency, c.CurrAbbv, Cnt, TotalCurrencyAmount, stock_days")
            // ->selectRaw("c.CurrencyID, c.Currency, c.CurrAbbv, Cnt, TotalCurrencyAmount, min_days, max_days, CASE WHEN stock_days <= 3 THEN 'Fresh Stocks' ELSE 'Old Stocks' END AS stock_category")
            ->havingRaw('TotalCurrencyAmount > 0')
            ->orderBy('c.currency')
            ->get();

        $result['pending_serials'] = DB::connection('forex')->table('tblforextransactiondetails')
            ->selectRaw('tblcurrency.Currency, tblcurrency.CurrAbbv, COUNT(tblforexserials.BillAmount) as serial_count')
            ->leftJoin('tblcurrency', 'tblforextransactiondetails.CurrencyID', 'tblcurrency.CurrencyID')
            ->leftJoin('tblforexserials', 'tblforextransactiondetails.FTDID', '=' , 'tblforexserials.FTDID')
            ->whereNull('tblforexserials.Serials')
            ->where('tblforexserials.FSStat', '=' , 1)
            ->where('tblforextransactiondetails.Voided' , 0)
            ->where('tblforextransactiondetails.BranchID' , '=' , Auth::user()->getBranch()->BranchID)
            ->when($r_set == 'O', function($query) use ($r_set) {
                return $query->where('tblforextransactiondetails.Rset', '=', $r_set);
            })
            ->groupBy('tblcurrency.Currency', 'tblcurrency.CurrAbbv')
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->get();

        $old_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('fd.CurrencyID, COUNT(fd.CurrencyID) AS Cnt, SUM(CASE WHEN fs.Queued = 0 THEN fs.BillAmount ELSE 0 END) AS TotalCurrencyAmount, DATEDIFF(CURDATE(), MIN(fd.TransactionDate)) AS min_days, DATEDIFF(CURDATE(), MAX(fd.TransactionDate)) AS max_days')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tbldenom AS d', 'fs.DenomID', '=', 'd.DenomID')
            ->where('fd.TransactionDate', '>=', '2025-01-01')
            ->where('fd.Voided', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.Transfer', 0)
            ->whereNotNull('fs.FTDID')
            ->where('fs.Received', 0)
            ->where('fs.FSStat', 1)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fd.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->where('fs.EntryDate', '<=', Carbon::now()->subDays(3)->toDateString())
            ->when($r_set == 'O', function($query) use ($r_set) {
                return $query->where('fd.Rset', '=', $r_set);
            })
            ->groupBy('fd.CurrencyID');

        $result['old_stocks'] = DB::connection('forex')
            ->table('tblcurrency AS c')
            ->leftJoinSub($old_stocks_query, 'os', function($join) {
                $join->on('c.currencyid', '=', 'os.CurrencyID');
            })
            ->selectRaw('c.CurrencyID, c.Currency, c.CurrAbbv, Cnt, TotalCurrencyAmount, min_days, max_days')
            ->orderBy('c.Currency')
            ->groupBy('c.CurrencyID', 'c.Currency', 'c.CurrAbbv', 'Cnt', 'TotalCurrencyAmount')
            ->havingRaw('Cnt > 0')
            ->get();

        $b_transactions = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('fd.FTDID, fd.CurrencyID, c.Currency, c.CurrAbbv, SUM(fd.Amount) as total_curr_amnt, fd.EntryDate, xus.Name, fd.Voided, 1 as source_type')
            ->join('tblcurrency as c', 'fd.CurrencyID', '=', 'c.CurrencyID')
            ->join('pawnshop.tblxusers as xus', 'fd.UserID', '=', 'xus.UserID')
            ->where('fd.TransactionDate', '=', $raw_date->toDateString())
            ->where('fd.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->when($r_set == 'O', function($query) use ($r_set) {
                return $query->where('fd.Rset', '=', $r_set);
            })
            ->groupBy('fd.FTDID', 'fd.CurrencyID', 'c.Currency', 'c.CurrAbbv', 'fd.EntryDate', 'xus.Name', 'fd.Voided');

        $s_transactions = DB::connection('forex')->table('tblsoldcurrdetails AS sc')
            ->selectRaw('sc.SCID as FTDID, sc.CurrencyID, c.Currency, c.CurrAbbv, SUM(sc.AmountPaid) as total_curr_amnt, sc.EntryDate, xus.Name, sc.Voided, 2 as source_type')
            ->join('tblcurrency as c', 'sc.CurrencyID', '=', 'c.CurrencyID')
            ->join('pawnshop.tblxusers as xus', 'sc.UserID', '=', 'xus.UserID')
            ->where('sc.DateSold', '=', $raw_date->toDateString())
            ->where('sc.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->when($r_set == 'O', function($query) use ($r_set) {
                return $query->where('sc.Rset', '=', $r_set);
            })
            ->groupBy('sc.SCID', 'sc.CurrencyID', 'c.Currency', 'c.CurrAbbv', 'sc.EntryDate', 'xus.Name', 'sc.Voided');

        $joined_queries = $b_transactions->unionAll($s_transactions);

        $result['transactions'] = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('FTDID as IDs, CurrencyID, Currency, CurrAbbv, SUM(total_curr_amnt) as total_curr_amnt, EntryDate as TransactDate, Name as User, Voided, source_type')
            ->havingRaw('total_curr_amnt > 0')
            ->groupBy('FTDID', 'CurrencyID', 'Currency', 'CurrAbbv', 'TransactDate', 'User', 'Voided', 'source_type', 'total_curr_amnt')
            ->orderBy('TransactDate', 'DESC')
            ->get();

        $result['tagged_bills'] = DB::connection('forex')->table('tbltaggedbills as tb')
            ->join('tbltaggedbillsdetails as tbd', 'tb.TBTID', 'tbd.TBTID')
            ->get();

        $TBTIDs = [];

        foreach ($result['tagged_bills'] as $index => $tagged_bills) {
            $details = DB::connection('forex')->table('tbltaggedbillsdetails as tbd')
                ->join('tblbillstatus as bs', 'bs.BillStatID', 'tbd.BillStatID')
                ->select('bs.BillStatus')
                // ->select('tbd.BillStatID')
                ->where('tbd.TBTID', '=', $tagged_bills->TBTID)
                ->orderBy('tbd.TBDID', 'DESC')
                ->get();

            $bill_tags = [];

            foreach ($details as $tags) {
                $bill_tags[] = $tags;
            }

            $tagged_bills->BillTags = $bill_tags;

            $TBTIDs[] = $tagged_bills;
        }

        $stocks = $this->queries()['stocks'];

        $result['buffer_stocks'] = $stocks->clone()
            ->selectRaw('tc.CurrencyID, tc.Currency, tc.CurrAbbv, fd.Rset, COUNT(fs.BillAmount) as count, SUM(fs.BillAmount) as total_amount, 1 as buffer')
            ->join('tbltransferforex as tf', 'fs.TFID', '=', 'tf.TransferForexID')
            ->where('fs.Buffer' , 1)
            ->where('fs.Transfer', 1)
            ->where('fs.FSType' , '=' , 1)
            ->where('fs.FSStat' , '=' , 2)
            ->where('fs.Received' , '=' , 0)
            ->where('tf.TransferDate', '>=', '2025-01-01')
            ->where('fs.EntryDate', '>=', '2025-01-01')
            ->groupBy('tc.CurrencyID' , 'tc.Currency', 'tc.CurrAbbv', 'fd.Rset', 'buffer')
            ->get();

        // Patext API ===========================================================================================
            // $result['customers'] =  DB::select('CALL spjsamplesms()');

            // $url = 'https://enterprise.messagingsuite.smart.com.ph/cgpapi/messages/sms';

            // $headers = [
            //     'X-MEMS-API-ID' => '1384',
            //     'X-MEMS-API-KEY' => '0664a4e79c6b41a081b38ecabfb287d9',
            //     'Content-Type' => 'application/json;charset=UTF-8'
            // ];

            // $response_data = [];

            // foreach ($result['customers'] as $key => $customers) {
            //     $customers->CelNo = preg_replace('/^\+63/', '0', $customers->CelNo);

            //     $data = [
            //         'messageType' => 'sms',
            //         'destination' => $customers->CelNo,
            //         'text' => $customers->Msg
            //     ];

            //     $response = Http::withHeaders($headers)->post($url, $data);

            //     if ($response->successful()) {
            //         $response_data[] = [
            //             'status' => 'Message sent successfully',
            //             'customer' => $customers->Msg,
            //             'number' => $customers->CelNo,
            //             'data' => $response->json()
            //         ];
            //     } else {
            //         $response_data[] = [
            //             'status' => 'Failed to send message',
            //             'customer' => $customers->Msg,
            //             'number' => $customers->CelNo,
            //             'error' => $response->body()
            //         ];
            //     }
            // }
        // End of Patext API ====================================================================================

        return view('blades.branch_dashboard', compact('result'));
    }

    public function buyingSalesBreakdown() {
        $raw_date = Carbon::now('Asia/Manila');
        $r_set =  session('time_toggle_status') == 1 ? 'O' : '';

        $buying_sales_breakdown = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->selectRaw('tbltransactiontype.TransType, SUM(fd.Amount) as Amount, COUNT(fd.FTDID) as transct_count')
            ->join('tbltransactiontype', 'fd.TransType', 'tbltransactiontype.TTID')
            ->where('fd.Voided', 0)
            ->where('fd.TransactionDate', '=', $raw_date->toDateString())
            ->where('fd.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->when($r_set == 'O', function($query) use ($r_set) {
                return $query->where('fd.Rset', '=', $r_set);
            })
            ->groupBy('tbltransactiontype.TransType')
            ->get();

        $response = [
            'buying_sales_breakdown' => $buying_sales_breakdown
        ];

        return response()->json($response);
    }

    public function stocks(Request $request) {
        $stocks = $this->queries()['stocks'];

        // DATEDIFF(CURDATE(), MAX(fd.TransactionDate)) AS max_days

        $stocks_set_o = $stocks->clone()
            ->selectRaw('fs.BillAmount, tc.Currency, fd.Rset, COUNT(fs.BillAmount) as bill_amount_count, GROUP_CONCAT(fs.Serials) as serials, SUM(fs.BillAmount) as sub_total, DATEDIFF(CURDATE(), fd.TransactionDate) AS max_days')
            ->where('fs.Received' , '=' , 0)
            ->where('fs.FSType' , '=' , 1)
            ->where('fs.FSStat' , '=' , 1)
            ->where('fs.Transfer' , '=' , 0)
            ->groupBy('fs.BillAmount' , 'tc.Currency', 'fd.Rset', 'max_days')
            ->orderBy('tc.Currency', 'ASC')
            ->orderBy('fs.BillAmount', 'DESC')
            ->get();

        $response = [
            'stocks_set_o' => $stocks_set_o
        ];

        return response()->json($response);
    }

    public function buffer(Request $request) {
        $stocks = $this->queries()['stocks'];

        // DATEDIFF(CURDATE(), MAX(fd.TransactionDate)) AS max_days

        $buffer_o = $stocks->clone()
            ->selectRaw('fs.BillAmount, tc.Currency, fd.Rset, COUNT(fs.BillAmount) as bill_amount_count, GROUP_CONCAT(fs.Serials) as serials, SUM(fs.BillAmount) as sub_total')
            ->join('tbltransferforex as tf', 'fs.TFID', '=', 'tf.TransferForexID')
            ->where('fs.Buffer' , 1)
            ->where('fs.Transfer', 1)
            ->where('fs.FSType' , '=' , 1)
            ->where('fs.FSStat' , '=' , 2)
            ->where('fs.Received' , '=' , 0)
            ->where('tf.TransferDate', '>=', '2025-01-01')
            ->where('fs.EntryDate', '>=', '2025-01-01')
            ->groupBy('fs.BillAmount' , 'tc.Currency', 'fd.Rset')
            ->orderBy('tc.Currency', 'ASC')
            ->orderBy('fs.BillAmount', 'DESC')
            ->get();

        $response = [
            'buffer_o' => $buffer_o,
        ];

        return response()->json($response);
    }

    public function adminDashboard() {
        return view('blades.admin_dashboard');
    }

    public function userData(Request $request) {
        $result['user'] = DB::connection('pawnshop')->table('tblxusers')
            ->get();

        return view('template.navbar')->with('result', $result);
    }

    public function sendEmail() {
        $message = "Hello, this is a test email!";

        Mail::raw($message, function ($email) {
            $email->to('andongis2016@gmail.com')
                  ->subject('Test Email');
        });

        return response()->json(['message' => 'Email sent successfully']);
    }
}
