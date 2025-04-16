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
use Session;
use Illuminate\Http\Request;
use Auth;

class AdminBuyingTransactionController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:ADMIN BUYING TRANSACTION,VIEW')->only(['show', 'details', 'serials']);
        $this->middleware('check.access.permission:ADMIN BUYING TRANSACTION,ADD')->only(['add', 'save', 'saveSerials']);
        $this->middleware('check.access.permission:ADMIN BUYING TRANSACTION,EDIT')->only(['edit', 'update']);
        $this->middleware('check.access.permission:ADMIN BUYING TRANSACTION,DELETE')->only(['void']);
        $this->middleware('check.access.permission:BUYING TRANSACTION,PRINT')->only(['details', 'print']);
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $raw_date = Carbon::now('Asia/Manila');
        $r_set =  session('time_toggle_status') == 1 ? 'O' : '';

        $result['transact_details'] = DB::connection('forex')->table('tbladminbuyingtransact')
            ->leftJoin('tblcurrency' , 'tbladminbuyingtransact.CurrencyID' , 'tblcurrency.CurrencyID')
            ->leftJoin('tbltransactiontype' , 'tbladminbuyingtransact.TransType' , 'tbltransactiontype.TTID')
            ->leftJoin('pawnshop.tblxusers', 'tbladminbuyingtransact.UserID', '=', 'pawnshop.tblxusers.UserID')
            ->leftJoin('pawnshop.tblxcustomer' , 'tbladminbuyingtransact.CustomerID' , 'pawnshop.tblxcustomer.CustomerID')
            ->select(
                'tbladminbuyingtransact.TransactionDate',
                'tbladminbuyingtransact.TransactionNo',
                'tbladminbuyingtransact.ReceiptNo',
                'tbladminbuyingtransact.ORNo',
                'tblcurrency.Currency',
                'tbltransactiontype.TransType',
                'tbladminbuyingtransact.CurrencyAmount',
                'tbladminbuyingtransact.RateUsed',
                'tbladminbuyingtransact.Amount',
                'pawnshop.tblxusers.Name',
                'pawnshop.tblxcustomer.FullName',
                'pawnshop.tblxusers.SecurityCode',
                'tbladminbuyingtransact.AFTDID',
                'tbladminbuyingtransact.Remarks',
                'tbladminbuyingtransact.Rset',
                'tbladminbuyingtransact.Voided',
            )
            ->where('tbladminbuyingtransact.TransactionDate', '=' , $raw_date->toDateString())
            ->when($r_set == 'O', function($query) use ($r_set) {
                return $query->where('tbladminbuyingtransact.Rset', '=', $r_set);
            })
            ->where('tbladminbuyingtransact.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->orderBy('tbladminbuyingtransact.TransactionNo' , 'DESC')
            ->paginate(10);

        $get_ftdid = [];

        foreach ($result['transact_details'] as $transaction) {
            $serials = DB::connection('forex')->table('tbladminforexserials')
                ->where('tbladminforexserials.AFTDID', '=', $transaction->AFTDID)
                ->selectRaw('tbladminforexserials.Serials')
                ->get();

            $get_serials = [];

            foreach ($serials as $serial) {
                $get_serials[] = $serial->Serials;
            }

            $transaction->serials = $get_serials;

            $get_ftdid[] = $transaction;
        }

        return view('buying_transact_admin.admin_new_b_transact', compact('result' , 'get_ftdid', 'menu_id'));
    }

    public function add(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['transact_type'] = DB::connection('forex')->table('tbltransactiontype')
            ->where('tbltransactiontype.Active', '!=', 0)
            ->where('tbltransactiontype.TransType', '!=', 'DPOFX')
            ->get();

        $result['currency'] = DB::connection('forex')->table('tblcurrency')
            ->orderBy('tblcurrency.Currency' , 'ASC')
            ->get();

        $result['currency_denom'] = DB::connection('forex')->table('tblcurrencydenom')
            ->get();

        $result['cis_details'] = DB::connection('cis')->table('tbltranstypehistory')
            ->leftJoin('pawnshop.tblxcustomer' , 'tbltranstypehistory.CustomerID' , 'pawnshop.tblxcustomer.CustomerID')
            ->select(
                'tbltranstypehistory.EntryID',
                'tbltranstypehistory.CustomerID',
                'tbltranstypehistory.CustomerNo',
                'tbltranstypehistory.TransDT',
                'tbltranstypehistory.UsedFlag',
                'pawnshop.tblxcustomer.FullName'
            )
            ->where('tbltranstypehistory.TransDate' , '=' , $raw_date->toDateString())
            ->where('tbltranstypehistory.TransID' , '=' , 13)
            ->where('tbltranstypehistory.UsedFlag', '=', 0)
            ->orderBy('tbltranstypehistory.EntryID', 'DESC')
            ->get();

        $result['used_customer_details'] = DB::connection('cis')->table('tbltranstypehistory')
            ->leftJoin('pawnshop.tblxcustomer' , 'tbltranstypehistory.CustomerID' , 'pawnshop.tblxcustomer.CustomerID')
            ->select(
                'tbltranstypehistory.EntryID',
                'tbltranstypehistory.CustomerID',
                'tbltranstypehistory.CustomerNo',
                'tbltranstypehistory.TransDT',
                'tbltranstypehistory.UsedFlag',
                'pawnshop.tblxcustomer.FullName'
            )
            ->where('tbltranstypehistory.TransDate' , '=' , $raw_date->toDateString())
            ->where('tbltranstypehistory.TransID' , '=' , 13)
            ->where('tbltranstypehistory.UsedFlag', '=', 1)
            ->orderBy('tbltranstypehistory.EntryID', 'DESC')
            ->get();

        return view('buying_transact_admin.admin_buying_transact', compact('result', 'menu_id'));
    }

    public function denomination(Request $request) {
        $sel_curr_id = $request->get('sel_curr_id');
        $transact_type = $request->get('transact_type');

        $rate_config = DB::connection('forex')->table('tblrateconfig')
            ->where('tblrateconfig.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->where('tblrateconfig.CurrencyID', '=', $sel_curr_id)
            ->get();

        $currency_denom = DB::connection('forex')->table('tblcurrencydenom')
            ->where('tblcurrencydenom.CurrencyID' , '=' , $sel_curr_id)
            ->where('tblcurrencydenom.TransType' , '=' , $transact_type)
            ->where('tblcurrencydenom.BranchID' , '=' , Auth::user()->getBranch()->BranchID)
            ->orderBy('tblcurrencydenom.BillAmount' , 'DESC')
            ->get();

        $rate_used_max = DB::connection('forex')->table('tblcurrentrate')
            ->leftJoin('tblcurrency', 'tblcurrentrate.CurrencyID', 'tblcurrency.CurrencyID')
            ->whereRaw('tblcurrentrate.EntryDateTime = (SELECT MAX(EntryDateTime) FROM tblcurrentrate WHERE CurrencyID = :sel_curr_id)' , ['sel_curr_id' => $sel_curr_id])
            ->select('tblcurrentrate.CurrencyID', 'tblcurrentrate.EntryDateTime', 'tblcurrentrate.Rate', 'tblcurrentrate.EntryDate', 'tblcurrentrate.CRID')
            ->get();

        $dpofx_rate = DB::connection('forex')->table('tbldpoindirate')
            ->where('tbldpoindirate.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->select(
                'tbldpoindirate.Rate'
            )
            ->first();

        $currency_ = DB::connection('forex')->table('tblcurrency')
            ->leftJoin('forexcurrency.tblcurrency', 'tblcurrency.CurrencyID', '=', 'forexcurrency.tblcurrency.fxCurrecnyID')
            ->where('tblcurrency.CurrencyID' , '=' , $sel_curr_id)
            ->first();

        $test_details = DB::connection('forexcurrency')->table('tblcurrencymanual as cm')
            ->selectRaw('tc.CurrencyID, tc.Currency, dm.DenominationID, cm.BillAmount, mt.CMTID, mt.ManualTag, cm.BillAmountImage, cm.StopBuying, cm.Remarks')
            ->join('forex.tblcurrency as tc', 'cm.CurrencyID', 'tc.CurrencyID')
            ->join('forex.tbldenominationmaintenance as dm', 'cm.DenominationID', 'dm.DenominationID')
            ->join('forex.tblcurrmanualtags as mt', 'cm.CMTID', 'mt.CMTID')
            ->where('cm.CurrencyID', $sel_curr_id)
            ->groupBy('tc.CurrencyID', 'tc.Currency', 'dm.DenominationID', 'cm.BillAmount', 'mt.CMTID', 'mt.ManualTag', 'cm.BillAmountImage', 'cm.StopBuying', 'cm.Remarks')
            ->get();

        $response = [
            'currency_denom' => $currency_denom,
            'rate_used_max' => $rate_used_max,
            'rate_config' => $rate_config,
            'test_details' => $test_details,
            'dpofx_rate' => $dpofx_rate,
        ];

        return response()->json($response);
    }

    public function currencies(Request $request) {
        $transact_type = $request->get('transact_type');

        $currencies = null;

        switch ($transact_type) {
            case '1':
                $currencies = DB::connection('forex')->table('tblcurrency')
                    ->orderBy('tblcurrency.Currency', 'ASC')
                    ->get();
                break;
            case '2':
                $currencies = DB::connection('forex')->table('tblcurrency')
                    ->where('tblcurrency.CurrencyID', '=', 11)
                    ->orderBy('tblcurrency.Currency', 'ASC')
                    ->get();
                break;
            case '3':
                $currencies = DB::connection('forex')->table('tblcurrency')
                    ->orderBy('tblcurrency.Currency', 'ASC')
                    ->get();
                break;
            case '4':
                $currencies = DB::connection('forex')->table('tblcurrency')
                    ->where('tblcurrency.CurrencyID', '=', 11)
                    ->orderBy('tblcurrency.Currency', 'ASC')
                    ->get();
                break;
            default:
                dd("no transactions available!");
        }

        $response = [
            'currencies' => $currencies
        ];

        return response()->json($response);
    }

    public function orNumbDuplicateBuying(Request $request) {
        $or_numbers = DB::connection('forex')->table('tbladminbuyingtransact')
            ->where('tbladminbuyingtransact.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->select(
                'tbladminbuyingtransact.ORNo'
            )
            ->whereNotNull('tbladminbuyingtransact.ORNo')
            ->where('tbladminbuyingtransact.ORNo', '!=', 0)
            ->get();

        $response = [
            'or_numbers' => $or_numbers
        ];

        return response()->json($response);
    }

    public function latestEntry(Request $request) {
        $sel_curr_id = $request->get('sel_curr_id');
        $transact_type = $request->get('transact_type');

        $currency_denom = DB::connection('forex')->table('tblcurrencydenom')
            ->where('tblcurrencydenom.CurrencyID' , '=' , $sel_curr_id)
            ->where('tblcurrencydenom.TransType' , '=' , $transact_type)
            ->orderBy('tblcurrencydenom.CDID' , 'ASC')
            ->get();

        $rate_used_least = DB::connection('forex')->table('tblcurrentrate')
            ->leftJoin('tblcurrency', 'tblcurrentrate.CurrencyID', 'tblcurrency.CurrencyID')
            ->where('tblcurrency.CurrencyID', '=' , $sel_curr_id)
            ->orderBy('tblcurrentrate.EntryDateTime' , 'DESC')
            ->limit(2)
            ->get();

        $response = [
            'currency_denom' => $currency_denom,
            'rate_used_least' => $rate_used_least,
        ];

        return response()->json($response);
    }

    public function save(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $radio_rset =  session('time_toggle_status') == 1 ? 'O' : $request->input('radio-rset');
        $buffer_status = $request->input('buffer_option') == "true" ? 1 : 0;

        $get_transaction_no = DB::connection('forex')->table('tbladminbuyingtransact')
            ->selectRaw('CASE WHEN MAX(TransactionNo) IS NULL THEN 1 ELSE MAX(TransactionNo) + 1 END AS updatedTransactNo')
            ->value('updatedTransactNo');

        $get_receipt_no = DB::connection('forex')->table('tbladminbuyingtransact')
            ->selectRaw('CASE WHEN MAX(ReceiptNo) IS NULL THEN 1 ELSE MAX(ReceiptNo) + 1 END AS updatedReceiptNo')
            ->value('updatedReceiptNo');

        $get_buffer_no = DB::connection('forex')->table('tblbuffertransfer')
            ->selectRaw('CASE WHEN MAX(BufferNo) IS NULL THEN 1 ELSE MAX(BufferNo) + 1 END AS latestBufferNo')
            ->value('latestBufferNo');

        $customer_entry_id = $request->input('customer-entry-id');
        $rset_dpofx = $request->input('rset-dpofx');

        switch ($request->input('radio-transact-type')) {
            case '1':
            case '2':
            case '3':
                $data = array(
                    'CurrencyID' => $request->input('currencies'),
                    'CurrencyAmount' => $request->input('current_amount_true'),
                    'Amount' => $request->input('total_buying_amount_true'),
                    'EntryDate' => $raw_date->toDateTimeString(),
                    'UserID' => $request->input('matched_user_id'),
                    'CustomerID' => $request->input('customer-id-selected'),
                    'TransactionDate' => $raw_date->toDateString(),
                    'TransactionTime' => $raw_date->toTimeString(),
                    'BranchID' => Auth::user()->getBranch()->BranchID,
                    'TransactionNo' => $get_transaction_no,
                    'ORNo' => $request->input('or-number-buying'),
                    'TransType' => $request->input('radio-transact-type'),
                    'Remarks' => $request->input('remarks'),
                    'Rset' => $radio_rset,
                    'ReceiptNo' => $get_receipt_no,
                    'CompanyID' => Auth::user()->getBranch()->CompanyID,
                    'Buffer' => $buffer_status
                );

                $validator = Validator::make($request->all(), [
                    'transact-date' => 'required',
                    'radio-transact-type' => 'required',
                    'currencies' => 'required',
                    'current_amount' => 'required',
                    'total_buying_amount_true' => 'required',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    DB::connection('forex')->table('tbladminbuyingtransact')
                        ->insert($data);

                    DB::connection('cis')->table('tbltranstypehistory')
                        ->where('tbltranstypehistory.EntryID', '=', $customer_entry_id)
                        ->update([
                            'UsedFlag' => 1
                        ]);
                }

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

                $get_transact_deets_aftdid = DB::connection('forex')->table('tbladminbuyingtransact')
                    ->orderBy('tbladminbuyingtransact.AFTDID', 'DESC')
                    ->limit(1)
                    ->select('tbladminbuyingtransact.AFTDID')
                    ->get();

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
                        'AFTDID' => $get_transact_deets_aftdid[0]->AFTDID,
                        'BillAmount' => $new_bill_amount_array[$key_test],
                        'Multiplier' => $multip_parsed_array[$key_test],
                        'Total' => $subutotal_parsed_array[$key_test],
                        'SinagRateBuying' => $buying_raw_new_array[$key_test],
                        // 'VarianceBuying' => $sinag_var_buying_parsed_array[$key_test],
                    ]);
                }

                $get_tbldenom_denom_id = DB::connection('forex')->table('tbladmindenom')
                    ->where('tbladmindenom.AFTDID' , '=' , $get_transact_deets_aftdid[0]->AFTDID)
                    ->select('tbladmindenom.ADenomID')
                    ->get();

                foreach ($multip_val_to_int_array as $multip_key => $multip_value) {
                    $new_set_index = array_fill(0, $multip_value, null);

                    foreach($new_set_index as $new_index_key => $new_index_value) {
                        DB::connection('forex')->table('tbladminforexserials')->insert([
                            'AFTDID' => $get_transact_deets_aftdid[0]->AFTDID,
                            'BillAmount' => $new_bill_amount_array[$multip_key],
                            'ADenomID' => $get_tbldenom_denom_id[$multip_key]->ADenomID,
                            'Serials' => null,
                            'UserID' => $request->input('matched_user_id'),
                            'FSType' => $request->input('radio-transact-type'),
                            'Buffer' => $buffer_status,
                        ]);
                    }
                }

                if ($buffer_status == 1) {
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
                            'Received' => 1,
                            'RDate' => $raw_date->toDateString(),
                            'RUserID' => $request->input('matched_user_id'),
                            'RDate' => $raw_date->toDateTimeString(),
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
                }

                $latest_aftdid = $get_transact_deets_aftdid[0]->AFTDID;

                session(['buying_trans_open_count' => 1]);

                return response()->json(['latest_aftdid' => $latest_aftdid]);

                break;
            default:
                dd("no transactions available!");
        }
    }

    public function print(Request $request) {
        $admin_b_transact_count = DB::connection('forex')->table('tbladminbuyingtransact')
            ->where('tbladminbuyingtransact.AFTDID', '=', $request->get('b_trans_id'))
            ->selectRaw('MAX(Print) + 1 AS latest_print_count')
            ->value('latest_print_count');

        DB::connection('forex')->table('tbladminbuyingtransact')
            ->where('tbladminbuyingtransact.AFTDID', '=', $request->get('b_trans_id'))
            ->update([
                'Print' => $admin_b_transact_count
            ]);

        return response()->json(['print_b_count_latest' => $admin_b_transact_count]);
    }

    public function details(Request $request) {
        $forex_connection = DB::connection('forex');
        $pawnshop_connection = DB::connection('pawnshop');
        $sesh_username = session('user_name');

        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['transact_details']  = $forex_connection->table('tbladminbuyingtransact')
            ->leftJoin('tblcurrency' , 'tbladminbuyingtransact.CurrencyID' , 'tblcurrency.CurrencyID')
            ->leftJoin('tbltransactiontype' , 'tbladminbuyingtransact.TransType' , 'tbltransactiontype.TTID')
            ->leftJoin('pawnshop.tblxusers', 'tbladminbuyingtransact.UserID', '=', 'pawnshop.tblxusers.UserID')
            ->leftJoin('pawnshop.tblxcustomer', 'tbladminbuyingtransact.CustomerID', '=', 'pawnshop.tblxcustomer.CustomerID')
            ->select(
                'tbladminbuyingtransact.TransactionDate',
                'tbladminbuyingtransact.Remarks',
                'tbladminbuyingtransact.TransactionTime',
                'tbladminbuyingtransact.TransactionNo',
                'tbladminbuyingtransact.AFTDID',
                'tbladminbuyingtransact.ReceiptNo',
                'tbladminbuyingtransact.BranchID',
                'tbladminbuyingtransact.Rset',
                'tbladminbuyingtransact.ORNo',
                'tbladminbuyingtransact.Print',
                'tbladminbuyingtransact.CustomerID',
                'tblcurrency.Currency',
                'tblcurrency.CurrAbbv',
                'tbltransactiontype.TransType',
                'tbltransactiontype.TTID',
                'tbladminbuyingtransact.CurrencyAmount',
                'tbladminbuyingtransact.RateUsed',
                'tbladminbuyingtransact.Amount',
                // 'tbladminbuyingtransact.AFTDID',
                'pawnshop.tblxusers.SecurityCode',
                'pawnshop.tblxusers.Username',
                'pawnshop.tblxusers.Name',
                'pawnshop.tblxcustomer.FullName',
            )
            ->where('tbladminbuyingtransact.AFTDID', '=' , $request->id)
            ->get();

        $date = Carbon::now('Asia/Manila');
        $date_now = $date->toDateString();

        $user_info = DB::connection('forex')->table('tblbranch')
            ->where('tblbranch.BranchCode', '=', session('branch_code'))
            ->first();

        $result['forex_serials'] = DB::connection('forex')->table('tbladminbuyingtransact')
            ->join('tblcurrency' , 'tbladminbuyingtransact.CurrencyID' , 'tblcurrency.CurrencyID')
            ->join('tbladminforexserials' , 'tbladminbuyingtransact.AFTDID' , '=' , 'tbladminforexserials.AFTDID')
            ->join('tbltransactiontype' , 'tbladminbuyingtransact.TransType' , '=' , 'tbltransactiontype.TTID')
            ->where('tbladminbuyingtransact.TransactionDate', '=' , $date_now)
            ->where('tbladminforexserials.AFTDID', '=' , $request->id)
            // ->where('tbladminforexserials.FSStat' , '=' , 1)
            ->select(
                'tblcurrency.Currency',
                'tblcurrency.CurrencyID',
                'tbladminforexserials.BillAmount',
                'tbltransactiontype.TransType',
                'tbladminforexserials.AFTDID',
                'tbladminforexserials.AFSID',
                'tbladminforexserials.Serials'
            )
            ->paginate(15);
            // ->get();

        $result['denom_details'] = DB::connection('forex')->table('tbladmindenom')
            ->where('tbladmindenom.AFTDID', '=' , $request->id)
            ->get();

        $currency_id = $request->input('currency-name');

        if ($result['transact_details'][0]->TTID != 4) {
            $result['bill_amount'] = DB::connection('forex')->table('tblcurrencydenom')
                ->leftJoin('tblcurrency' , 'tblcurrencydenom.CurrencyID' , 'tblcurrency.CurrencyID')
                ->where('tblcurrencydenom.CurrencyID', '=' , $result['forex_serials'][0]->CurrencyID)
                ->where('tblcurrencydenom.BranchID', '=' , $result['transact_details'][0]->BranchID)
                ->select(
                    'tblcurrency.Currency',
                    'tblcurrencydenom.CurrencyID',
                    'tblcurrencydenom.BillAmount',
                )
                ->orderBy('tblcurrencydenom.BillAmount', 'ASC')
                ->get();
        }

        return view('buying_transact_admin.admin_b_transact_deets', compact('result', 'menu_id'));
    }

    public function update(Request $request) {
        $receipt_set = $request->input('radio-rset') == null ? $request->input('transact-receipt-rset') : $request->input('radio-rset');
        $or_number = $request->input('or-number-buying') == null || $receipt_set == 'B' ? null : $request->input('or-number-buying');
        $customer_id = $request->input('customer-id-selected') == null ? $request->input('transact-customer-id') : $request->input('customer-id-selected');
        $remarks = $request->input('transact-remarks') == null ? null : $request->input('transact-remarks');

        $data_updated = array(
            'Rset' =>  $receipt_set,
            'ORNo' => $or_number,
            'CustomerID' => $customer_id,
            'Remarks' => $remarks
        );

        $validator = Validator::make($request->all(), [
            'radio-rset' => 'nullable',
			'or-number-buying' => 'nullable',
			'customer-id-selected' => 'nullable',
        ]);

        if ($validator->fails()) {
			return redirect()->back()->withErrors($validator);
		} else {
			DB::connection('forex')->table('tbladminbuyingtransact')
                ->where('tbladminbuyingtransact.AFTDID', '=' , $request->get('trans_id'))
                ->update($data_updated);
		}
    }

    public function serials(Request $request) {
        $date = Carbon::now('Asia/Manila');
        $date_now = $date->toDateString();

        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['pending_serials'] = DB::connection('forex')->table('tbladminbuyingtransact')
            ->select('tbladminforexserials.BillAmount', 'tbltransactiontype.TransType', 'tbladminforexserials.AFTDID', 'tbladminforexserials.AFSID', 'tbladminforexserials.Serials', 'tblcurrency.Currency')
            ->leftJoin('tblcurrency' , 'tbladminbuyingtransact.CurrencyID' , 'tblcurrency.CurrencyID')
            ->leftJoin('tbladminforexserials' , 'tbladminbuyingtransact.AFTDID' , '=' , 'tbladminforexserials.AFTDID')
            ->leftJoin('tbltransactiontype' , 'tbladminbuyingtransact.TransType' , '=' , 'tbltransactiontype.TTID')
            ->where('tbladminbuyingtransact.TransactionDate', '=' , $date_now)
            ->where('tbladminforexserials.AFTDID', '=' , $request->id)
            ->where('tbladminforexserials.FSStat' , '=' , 1)
            ->get();

        return view('buying_transact_admin.admin_b_transact_serials', compact('result', 'menu_id'));
    }

    public function saveSerials(Request $request) {
        $forex_ftdid = $request->input('forex-ftdid');
        $AFSIDs = explode(',', $request->input('parsed_fsid'));
        $serials = explode(',', $request->input('parsed_serials'));

        $serials_regex = '/^[a-zA-Z0-9]+$/';

        $validator = Validator::make($request->all(), [
            'serials' => 'array',
            'serials.*' => 'nullable|regex:' .$serials_regex,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            foreach ($serials as $key => $serial) {
                if (isset($AFSIDs[$key])) {
                    $updateData = ['Serials' => $serial];

                    DB::connection('forex')->table('tbladminforexserials')
                        ->where('tbladminforexserials.AFSID', '=', $AFSIDs[$key])
                        ->update($updateData);
                }
            }

            $message = "Serials Added!";
            return response()->json(['message' => $message]);
        }
    }

    public function void(Request $request) {
        DB::connection('forex')->table('tbladminbuyingtransact')
            ->where('tbladminbuyingtransact.AFTDID', $request->get('trans_id'))
            ->update([
                'Voided' => 1
            ]);
    }
}
