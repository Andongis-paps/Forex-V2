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

class AdminRetailSellingController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:RETAIL SELLING,VIEW')->only(['show', 'details', 'serials']);
        $this->middleware('check.access.permission:RETAIL SELLING,ADD')->only(['add', 'save']);
        $this->middleware('check.access.permission:RETAIL SELLING,EDIT')->only(['edit', 'update']);
        $this->middleware('check.access.permission:RETAIL SELLING,DELETE')->only(['void']);
        $this->middleware('check.access.permission:RETAIL SELLING,PRINT')->only(['details', 'print']);
    }

    public function show(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $r_set = session('time_toggle_status') == 1 ? 'O' : '';

        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $date_to = $request->query('date-to-search');
        $date_from = $request->query('date-from-search');
        $invoice_no = $request->query('invoice-search');
        $filter = intval($request->query('radio-search-type'));

        $result['selling_transact_details'] = DB::connection('forex')->table('tbladminsoldcurr as asc')
            ->selectRaw('asc.DateSold, asc.SellingNo, asc.ReceiptNo, asc.ORNo, tblcurrency.Currency, asc.CurrAmount, asc.RateUsed, asc.AmountPaid, asc.Rset, asc.ASCID, asc.Remarks, asc.Voided, tcx.FullName, tbx.SecurityCode')
            ->join('tblcurrency' , 'asc.CurrencyID' , 'tblcurrency.CurrencyID')
            ->join('pawnshop.tblxcustomer as tcx' , 'asc.CustomerID' , 'tcx.CustomerID')
            ->leftJoin('pawnshop.tblxusers as tbx', 'asc.UserID', '=', 'tbx.UserID')
            ->where('asc.DateSold', '=' , $raw_date->toDateString())
            ->where('asc.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->when($r_set == 'O', function($query) use ($r_set) {
                return $query->where('asc.Rset', '=', $r_set);
            })
            ->when(empty($filter), function ($query) use ($raw_date) {
                return $query->where('asc.DateSold', $raw_date->toDateString());
            })
            ->when(!empty($filter), function ($query) use ($filter, $date_from, $date_to, $invoice_no) {
                switch ($filter) {
                    case 1:
                        return $query->whereBetween('asc.DateSold', [$date_from, $date_to]);
                    case 2:
                        return $query->where('asc.ORNo', $invoice_no);
                    default:
                        return $query;
                }
            })
            ->groupBy('asc.DateSold', 'asc.SellingNo', 'asc.ReceiptNo', 'asc.ORNo', 'tblcurrency.Currency', 'asc.CurrAmount', 'asc.RateUsed', 'asc.AmountPaid', 'asc.Rset', 'asc.ASCID', 'asc.Remarks', 'asc.Voided', 'tcx.FullName', 'tbx.SecurityCode')
            ->orderBy('asc.SellingNo' , 'DESC')
            ->paginate(30);

        return view('retail_selling_transact_admin.admin_new_s_transact', compact('result', 'menu_id'));
    }

    public function add(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $time_togg_status = session('time_toggle_status');
        $r_set = $time_togg_status == 1 ? 'O' : null;

        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $branch_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('tc.CurrencyID, tc.Currency, fs.BillAmount, COUNT(fs.BillAmount) as bill_amount_count, SUM(fs.BillAmount) as bill_amount, fd.Rset')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            // ->where('fs.Buffer', 0)
            ->where('fs.Queued', 0)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.Transfer', 1)
            ->where('fs.Received', 1)
            ->where('fs.FSStat', 2)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->when($r_set, function ($query) use ($r_set) {
                return $query->where('fd.Rset', $r_set);
            })
            ->groupBy('tc.CurrencyID', 'tc.Currency', 'fs.BillAmount', 'fd.Rset');

        $admin_stocks_query = DB::connection('forex')->table('tbladminbuyingtransact AS fd')
            ->selectRaw('tc.CurrencyID, tc.Currency, fs.BillAmount, COUNT(fs.BillAmount) as bill_amount_count, SUM(fs.BillAmount) as bill_amount, fd.Rset')
            ->join('tbladminforexserials AS fs', 'fd.aftdid', '=', 'fs.aftdid')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            // ->where('fs.Buffer', 0)
            ->where('fs.Queued', 0)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.FSStat', 1)
            ->where('fs.FSType', 1)
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->when($r_set, function ($query) use ($r_set) {
                return $query->where('fd.Rset', $r_set);
            })
            ->groupBy('tc.CurrencyID', 'tc.Currency', 'fs.BillAmount', 'fd.Rset');

        $joined_queries = $branch_stocks_query->unionAll($admin_stocks_query);

        $result['available_serials'] = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->select('Currency', 'BillAmount', 'bill_amount_count', 'bill_amount', 'Rset')
            ->groupBy('Currency', 'BillAmount', 'bill_amount_count', 'bill_amount', 'Rset')
            ->when($r_set, function ($query) use ($r_set) {
                return $query->where('Rset', $r_set);
            })
            ->orderBy('Currency', 'ASC')
            ->orderBy('BillAmount', 'DESC')
            ->get();

        $result['currency'] = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->select('CurrencyID', 'Currency')
            ->where('Rset', '=', 'B')
            ->groupBy('CurrencyID', 'Currency')
            ->orderBy('Currency', 'ASC')
            ->get();

        $result['stocks_set_b'] = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->select('BillAmount', 'Currency', 'Rset', 'bill_amount_count')
            ->where('Rset', '=', 'B')
            ->groupBy('BillAmount' , 'Currency', 'Rset', 'bill_amount_count')
            ->orderBy('Currency', 'ASC')
            ->orderBy('BillAmount', 'DESC')
            ->get();

        $result['stocks_set_o'] = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->select('BillAmount', 'Currency', 'Rset', 'bill_amount_count')
            ->where('Rset', '=', 'O')
            ->groupBy('BillAmount' , 'Currency', 'Rset', 'bill_amount_count')
            ->orderBy('Currency', 'ASC')
            ->orderBy('BillAmount', 'DESC')
            ->get();

        return view('retail_selling_transact_admin.admin_s_transact', compact('result', 'menu_id'));
    }

    public function serials(Request $request) {
        $branch_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('tc.CurrencyID, tc.Currency, fs.FSID, fs.BillAmount, fs.Serials, fd.Rset, cd.BranchID, cd.SinagRateSelling, 2 as source_type')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            ->join('tbldenom AS d', 'fs.DenomID', '=', 'd.DenomID')
            ->join('tblcurrencydenom AS cd', function($join) {
                $join->on('fd.CurrencyID', '=', 'cd.CurrencyID')
                    ->where('cd.BranchID', '=', Auth::user()->getBranch()->BranchID);
            })
            // ->where('fs.Buffer', 1)
            ->where('fs.Queued', 0)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.Transfer', 1)
            ->where('fs.Received', 1)
            ->where('fs.FSStat', 2)
            ->where('fs.FSType', 1)
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->where('fd.CurrencyID', $request->get('selected_curr_id'))
            ->groupBy('tc.CurrencyID', 'tc.Currency', 'fs.FSID', 'fs.BillAmount', 'fs.Serials', 'fd.Rset', 'cd.BranchID', 'cd.SinagRateSelling');

        $admin_stocks_query = DB::connection('forex')->table('tbladminbuyingtransact AS fd')
            ->selectRaw('tc.CurrencyID, tc.Currency, fs.AFSID, fs.BillAmount, fs.Serials, fd.Rset, cd.BranchID, cd.SinagRateSelling, 1 as source_type')
            ->join('tbladminforexserials AS fs', 'fd.aftdid', '=', 'fs.aftdid')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            ->join('tbladmindenom AS d', 'fs.ADenomID', '=', 'd.ADenomID')
            ->join('tblcurrencydenom AS cd', function($join) {
                $join->on('fd.CurrencyID', '=', 'cd.CurrencyID')
                    ->where('cd.BranchID', '=', Auth::user()->getBranch()->BranchID);
            })
            // ->where('fs.Buffer', 1)
            ->where('fs.Queued', 0)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.FSStat', 1)
            ->where('fs.FSType', 1)
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->where('fd.CurrencyID', $request->get('selected_curr_id'))
            ->groupBy('tc.CurrencyID', 'tc.Currency', 'fs.AFSID', 'fs.BillAmount', 'fs.Serials', 'fd.Rset', 'cd.BranchID', 'cd.SinagRateSelling');

        $joined_queries = $branch_stocks_query->unionAll($admin_stocks_query);

        $avaible_serials_currency = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('Currency, FSID as ID, Serials, BillAmount, Rset, source_type')
            ->where('Rset', $request->get('selected_r_set'))
            ->groupBy('Currency', 'ID', 'Serials', 'BillAmount', 'Rset', 'source_type')
            ->orderBy('BillAmount', 'DESC')
            ->get();

        $rate_used_selling = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->select('BranchID', 'CurrencyID', 'SinagRateSelling')
            ->groupBy('BranchID', 'CurrencyID', 'SinagRateSelling')
            ->get();

        $denoms = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('GROUP_CONCAT(DISTINCT BillAmount ORDER BY BillAmount DESC) as denominations')
            ->get();

        $response = [
            'denoms' => $denoms,
            'rate_used_selling' => $rate_used_selling,
            'avaible_serials_currency' => $avaible_serials_currency,
        ];

        return response()->json($response);
    }

    public function stocks(Request $request) {
        $branch_stocks_query = DB::connection('forex')->table('tblforextransactiondetails AS fd')
            ->selectRaw('tc.CurrencyID, tc.Currency, fs.BillAmount, COUNT(fs.BillAmount) as bill_amount_count, SUM(fs.BillAmount) as bill_amount, fd.Rset')
            ->join('tblforexserials AS fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            // ->where('fs.Buffer', 0)
            ->where('fs.Queued', 0)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.Transfer', 1)
            ->where('fs.Received', 1)
            ->where('fs.FSStat', 2)
            ->whereIn('fs.FSType', [1, 2, 3])
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->where('fd.Rset', $request->get('rset_value'))
            ->groupBy('tc.CurrencyID', 'tc.Currency', 'fs.BillAmount', 'fd.Rset');

        $admin_stocks_query = DB::connection('forex')->table('tbladminbuyingtransact AS fd')
            ->selectRaw('tc.CurrencyID, tc.Currency, fs.BillAmount, COUNT(fs.BillAmount) as bill_amount_count, SUM(fs.BillAmount) as bill_amount, fd.Rset')
            ->join('tbladminforexserials AS fs', 'fd.aftdid', '=', 'fs.aftdid')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            // ->where('fs.Buffer', 0)
            ->where('fs.Queued', 0)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 0)
            ->where('fs.FSStat', 1)
            ->where('fs.FSType', 1)
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->where('fd.Rset', $request->get('rset_value'))
            ->groupBy('tc.CurrencyID', 'tc.Currency', 'fs.BillAmount', 'fd.Rset');

        $joined_queries = $branch_stocks_query->unionAll($admin_stocks_query);

        $currency = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->select('CurrencyID', 'Currency')
            ->groupBy('CurrencyID', 'Currency')
            ->orderBy('Currency', 'ASC')
            ->get();

        $response = [
            'currency' => $currency
        ];

        return response()->json($response);
    }

    public function save(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $radio_rset =  session('time_toggle_status') == 1 ? 'O' : $request->input('radio-rset');

        $bill_amnt_selling = $request->input('bill-amnt-selling');

        $get_transaction_no = DB::connection('forex')->table('tbladminsoldcurr')
            ->selectRaw('CASE WHEN MAX(SellingNo) IS NULL THEN 1 ELSE MAX(SellingNo) + 1 END AS updatedTransactNo')
            ->value('updatedTransactNo');

        $get_receipt_no = DB::connection('forex')->table('tbladminsoldcurr')
            ->selectRaw('CASE WHEN MAX(ReceiptNo) IS NULL THEN 1 ELSE MAX(ReceiptNo) + 1 END AS updatedReceiptNo')
            ->value('updatedReceiptNo');

        $data_soldcurrdeets = array(
            'CurrencyID' => $request->input('currencies-selling'),
            'CurrAmount' => array_sum($request->input('currency-amnt-selling')),
            'RateUsed' => $request->input('rate-used-selling'),
            'AmountPaid' => $request->input('true-total-amnt-selling'),
            'EntryDate' => $raw_date->toDateTimeString(),
            'UserID' => $request->input('matched_user_id'),
            'Remarks' => $request->input('remarks'),
            'CustomerID' => $request->input('customer-id-selected'),
            'DateSold' => $request->input('transact-date-selling'),
            'TimeSold' => $raw_date->toTimeString(),
            'BranchID' => Auth::user()->getBranch()->BranchID,
            'SellingNo' => $get_transaction_no,
            'ReceiptNo' => $get_receipt_no,
            'ORNo' => $request->input('or-number-selling'),
            'Rset' => $radio_rset
        );

        $validator = Validator::make($request->all(), [
            'transact-date-selling' => 'required',
            'customer-id-selected' => 'required',
            'radio-rset' => 'required',
            'currencies-selling' => 'required',
            'rate-used-true' => 'required',
            'bill-serial-selling' => 'required',
            'currency-amnt-selling' => 'required',
            'bill-amnt-selling' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $exploded_fsids = explode(", ", $request->input('FSIDs'));
            $exploded_afsids = explode(", ", $request->input('AFSIDs'));

            DB::connection('forex')->table('tbladminsoldcurr')->insertGetID($data_soldcurrdeets);

            $get_scid = DB::connection('forex')->table('tbladminsoldcurr')
                ->where('tbladminsoldcurr.SellingNo' , '=' , $get_transaction_no)
                ->select('tbladminsoldcurr.ASCID')
                ->get();

            if (!is_null($request->input('FSIDs'))) {
                foreach ($exploded_fsids as $key => $serial_fsid) {
                    DB::connection('forex')->table('tblsoldserials')
                        ->insert([
                            'ASCID' => $get_scid[0]->ASCID,
                            'FSID' => $serial_fsid,
                            'UserID' => $request->input('matched_user_id'),
                            'EntryDate' => $raw_date->toDateTimeString(),
                            'BillAmount' => $bill_amnt_selling[$key],
                    ]);
                }

                DB::connection('forex')->table('tblforexserials')
                    ->when(is_array($exploded_fsids), function ($query) use ($exploded_fsids) {
                        return $query->whereIn('tblforexserials.FSID', $exploded_fsids);
                    }, function ($query) use ($exploded_fsids) {
                        return $query->where('tblforexserials.FSID', $exploded_fsids);
                    })
                    ->update([
                        'Sold' => 1,
                        'FSStat' => 3
                    ]);
            }

            if (!is_null($request->input('AFSIDs'))) {
                foreach ($exploded_afsids as $key => $serial_afsid) {
                    DB::connection('forex')->table('tbladminsoldserials')
                        ->insert([
                            'ASCID' => $get_scid[0]->ASCID,
                            'AFSID' => $serial_afsid,
                            'UserID' => $request->input('matched_user_id'),
                            'EntryDate' => $raw_date->toDateTimeString(),
                            'BillAmount' => $bill_amnt_selling[$key],
                    ]);
                }

                DB::connection('forex')->table('tbladminforexserials')
                    ->when(is_array($exploded_afsids), function ($query) use ($exploded_afsids) {
                        return $query->whereIn('tbladminforexserials.AFSID', $exploded_afsids);
                    }, function ($query) use ($exploded_afsids) {
                        return $query->where('tbladminforexserials.AFSID', $exploded_afsids);
                    })
                    ->update([
                        'Sold' => 1,
                        'FSStat' => 3
                    ]);
            }

            $message = "Selling Transaction Success!";
            return response()->json(['message' => 'Selling Transaction Success!', 'latest_ascid' => $get_scid[0]->ASCID]);
        }
    }

    public function OrNoDuplicateRetailSelling(Request $request) {
        $or_numbers = DB::connection('forex')->table('tbladminbuyingtransact as afd')
            ->where('afd.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->select('afd.ORNo')
            ->where('afd.ORNo', $request->get('current_or_number'))
            ->where('afd.Voided', 0)
            ->exists();

        $or_numbers_sell = DB::connection('forex')->table('tbladminsoldcurr as asc')
            ->select('asc.ORNo')
            ->where('asc.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->where('asc.ORNo', $request->get('current_or_number'))
            ->where('asc.Voided', 0)
            ->exists();

        $boolean = '';

        if ($or_numbers || $or_numbers_sell) {
            $boolean = true;
        } else {
            $boolean = false;
        }

        $response = [
            'boolean' => $boolean,
        ];

        return response()->json($response);
    }

    public function print(Request $request) {
        $selling_transaction_details = DB::connection('forex')->table('tbladminsoldcurr')
            ->where('tbladminsoldcurr.ASCID', '=', $request->get('s_trans_id'))
            ->selectRaw('MAX(Print) + 1 AS latest_print_count')
            ->value('latest_print_count');

        DB::connection('forex')->table('tbladminsoldcurr')
            ->where('tbladminsoldcurr.ASCID', '=', $request->get('s_trans_id'))
            ->update([
                'Print' => $selling_transaction_details
            ]);

        return response()->json(['print_s_count_latest' => $selling_transaction_details]);
    }

    public function details(Request $request) {
        $date = Carbon::now('Asia/Manila');
        $date_now = $date->toDateString();

        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['soldcurr_details'] = DB::connection('forex')->table('tbladminsoldcurr')
            ->leftJoin('tblcurrency' , 'tbladminsoldcurr.CurrencyID' , 'tblcurrency.CurrencyID')
            ->leftJoin('pawnshop.tblxusers', 'tbladminsoldcurr.UserID', '=', 'pawnshop.tblxusers.UserID')
            ->leftJoin('pawnshop.tblxcustomer', 'tbladminsoldcurr.CustomerID', '=', 'pawnshop.tblxcustomer.CustomerID')
            ->select(
                'tbladminsoldcurr.CompanyID',
                'tbladminsoldcurr.Remarks',
                'tbladminsoldcurr.ASCID',
                'tbladminsoldcurr.SellingID',
                'tblcurrency.Currency',
                'tblcurrency.CurrAbbv',
                'tbladminsoldcurr.CurrAmount',
                'tbladminsoldcurr.RateUsed',
                'tbladminsoldcurr.AmountPaid',
                'pawnshop.tblxusers.Username',
                'tbladminsoldcurr.DateSold',
                'tbladminsoldcurr.TimeSold',
                'tbladminsoldcurr.SellingNo',
                'tbladminsoldcurr.ReceiptNo',
                'tbladminsoldcurr.CustomerID',
                'tbladminsoldcurr.Rset',
                'tbladminsoldcurr.ORNo',
                'tbladminsoldcurr.CurrencyID',
                'tbladminsoldcurr.Print',
                'pawnshop.tblxusers.SecurityCode',
                'pawnshop.tblxusers.Name',
                'pawnshop.tblxcustomer.FullName',
            )
            ->where('tbladminsoldcurr.ASCID', '=' , $request->id)
            ->get();

        $branch_stocks_query = DB::connection('forex')->table('tblsoldserials AS ss')
            ->selectRaw('tc.CurrencyID, tc.Currency, ss.BillAmount, fs.FSID, fs.Serials, sc.ASCID, sc.DateSold, sc.TimeSold, sc.Rset')
            ->join('tblforexserials AS fs', 'ss.FSID', '=', 'fs.FSID')
            ->join('tbladminsoldcurr AS sc', 'ss.ASCID', '=', 'sc.ASCID')
            ->join('tblcurrency as tc', 'sc.CurrencyID', 'tc.CurrencyID')
            // ->where('fs.Buffer', 0)
            ->where('fs.Queued', 0)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 1)
            ->where('fs.FSStat', 3)
            ->where('fs.FSType', 1)
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->where('ss.ASCID', $request->id)
            ->groupBy('tc.CurrencyID', 'tc.Currency', 'ss.BillAmount', 'fs.FSID', 'fs.Serials','sc.ASCID', 'sc.DateSold', 'sc.TimeSold', 'sc.Rset');

        $admin_stocks_query = DB::connection('forex')->table('tbladminsoldserials AS ss')
            ->selectRaw('tc.CurrencyID, tc.Currency, ss.BillAmount, fs.AFSID, fs.Serials, sc.ASCID, sc.DateSold, sc.TimeSold, sc.Rset')
            ->join('tbladminforexserials AS fs', 'ss.AFSID', '=', 'fs.AFSID')
            ->join('tbladminsoldcurr AS sc', 'ss.ASCID', '=', 'sc.ASCID')
            ->join('tblcurrency as tc', 'sc.CurrencyID', 'tc.CurrencyID')
            // ->where('fs.Buffer', 0)
            ->where('fs.Queued', 0)
            ->whereNull('fs.HeldBy')
            ->where('fs.Onhold', 0)
            ->whereNotNull('fs.Serials')
            ->where('fs.Sold', 1)
            ->where('fs.FSStat', 3)
            ->where('fs.FSType', 1)
            ->where('fs.SoldToManila', 0)
            ->whereNull('fs.STMDID')
            ->where('ss.ASCID', $request->id)
            ->groupBy('tc.CurrencyID', 'tc.Currency', 'ss.BillAmount', 'fs.AFSID', 'fs.Serials', 'sc.ASCID', 'sc.DateSold', 'sc.TimeSold', 'sc.Rset');

        $joined_queries = $branch_stocks_query->unionAll($admin_stocks_query);

        $result['sold_serial'] = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('FSID as ID, CurrencyID, Currency, BillAmount, Serials, ASCID, Rset, DateSold, TimeSold')
            ->groupBy('ID', 'CurrencyID', 'Currency', 'BillAmount', 'Serials', 'ASCID', 'Rset', 'DateSold', 'TimeSold')
            ->orderBy('BillAmount', 'DESC')
            ->get();

        return view('retail_selling_transact_admin.admin_sold_serials', compact('result', 'menu_id'));
    }

    public function update(Request $request) {
        $receipt_set = $request->input('radio-rset') == null ? $request->input('sold-currency-rset') : $request->input('radio-rset');
        $or_number = $request->input('or-number-buying') == null && $receipt_set == 'B' ? null : $request->input('or-number-buying');
        $customer_id = $request->input('customer-id-selected') == null ? $request->input('transact-customer-id') : $request->input('customer-id-selected');
        $remarks = $request->input('transact-remarks') == null ? null : $request->input('transact-remarks');
        $rate_used = $request->input('sold-currency-rate-used');
        $total_amount = $request->input('true-sold-currency-total-amnt');

        $data_updated = array(
            'Rset' =>  $receipt_set,
            'ORNo' => $or_number,
            'CustomerID' => $customer_id,
            'RateUsed' => $rate_used,
            'AmountPaid' => $total_amount,
            'Remarks' => $remarks,
        );

        $validator = Validator::make($request->all(), [
            'radio-rset' => 'nullable',
			'or-number-buying' => 'nullable',
			'customer-id-selected' => 'nullable',
        ]);

        if ($validator->fails()) {
			return redirect()->back()->withErrors($validator);
		} else {
			DB::connection('forex')->table('tbladminsoldcurr')
                ->where('tbladminsoldcurr.ASCID', '=' , $request->get('trans_id'))
                ->update($data_updated);
		}
    }

    public function void(Request $request) {
        DB::connection('forex')->table('tbladminsoldcurr')
            ->where('tbladminsoldcurr.ASCID', $request->get('trans_id'))
            ->update([
                'Voided' => 1
            ]);
    }
}
