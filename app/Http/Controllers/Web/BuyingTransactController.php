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
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Artisan;

class BuyingTransactController extends Controller{
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:BUYING TRANSACTION,VIEW')->only(['show', 'details']);
        $this->middleware('check.access.permission:BUYING TRANSACTION,ADD')->only(['add', 'save', 'addSerials']);
        $this->middleware('check.access.permission:BUYING TRANSACTION,EDIT')->only(['edit', 'update', 'updateRate']);
        $this->middleware('check.access.permission:BUYING TRANSACTION,DELETE')->only(['delete']);
        $this->middleware('check.access.permission:BUYING TRANSACTION,PRINT')->only(['details', 'printCountBuying']);
        // $this->middleware('check.access.permission:BUYING TRANSACTION,ACKNOWLEDGE')->only(['edit', 'update', 'printCountBuying']);
        // $this->middleware('check.access.permission:BUYING TRANSACTION,ARCHIVE')->only(['delete']);
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $raw_date = Carbon::now('Asia/Manila');
        $r_set =  session('time_toggle_status') == 1 ? 'O' : '';

        $date_to = $request->query('date-to-search');
        $date_from = $request->query('date-from-search');
        $invoice_no = $request->query('invoice-search');
        $filter = intval($request->query('radio-search-type'));

        $query = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->join('tbldenom as tdm' , 'fd.FTDID' , 'tdm.FTDID')
            ->join('tblforexserials as fs' , 'fd.FTDID' , 'fs.FTDID')
            ->join('tblcurrency as tr' , 'fd.CurrencyID' , 'tr.CurrencyID')
            ->join('tbltransactiontype as tt' , 'fd.TransType' , 'tt.TTID')
            ->join('pawnshop.tblxusers as tbx', 'fd.UserID', '=', 'tbx.UserID')
            ->join('pawnshop.tblxcustomer as tcx' , 'fd.CustomerID' , 'tcx.CustomerID')
            ->where('fd.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->when($r_set == 'O', function($query) use ($r_set) {
                return $query->where('fd.Rset', '=', $r_set);
            })
            ->when(empty($filter), function ($query) use ($raw_date) {
                return $query->where('fd.TransactionDate', $raw_date->toDateString());
            })
            ->when(!empty($filter), function ($query) use ($filter, $date_from, $date_to, $invoice_no) {
                switch ($filter) {
                    case 1:
                        return $query->whereBetween('fd.TransactionDate', [$date_from, $date_to]);
                    case 2:
                        return $query->where('fd.ORNo', $invoice_no);
                    default:
                        return $query;
                }
            });

        $result['transact_details'] = $query->clone()
            ->selectRaw('fd.TransactionDate, fd.TransactionNo, fd.ReceiptNo, fd.ORNo, tr.Currency, fd.CurrencyID, tt.TransType, fd.CurrencyAmount, fd.Amount, tbx.Name, tcx.FullName, fd.FTDID, fd.Rset, tbx.Name as encoder, fd.Voided, fd.HasTicket,
                MAX(CASE WHEN fs.Serials IS NULL THEN 1 ELSE 0 END) as pending_serials,
                GROUP_CONCAT(DISTINCT tdm.BillAmount ORDER BY tdm.BillAmount DESC) as denoms
            ')
            ->groupBy('fd.TransactionDate', 'fd.TransactionNo', 'fd.ReceiptNo', 'fd.ORNo', 'tr.Currency', 'fd.CurrencyID', 'tt.TransType', 'fd.CurrencyAmount', 'fd.Amount', 'tbx.Name', 'tcx.FullName', 'fd.FTDID', 'fd.Rset', 'encoder', 'fd.Voided', 'fd.HasTicket')
            ->orderBy('fd.TransactionNo' , 'DESC')
            ->paginate(30)
            ->appends([
                'date-to-search' => $date_to,
                'date-from-search' => $date_from,
                'invoice-search' => $invoice_no,
                'radio-search-type' => $filter,
            ]);

        $FTDIDs = $query->clone()->pluck('fd.FTDID')
            ->toArray();

        $rates = DB::connection('forex')->table('tbldenom as tdm')
            ->selectRaw('
                tdm.FTDID,
                CASE
                    WHEN fd.CurrencyID NOT IN (12, 14, 31) THEN GROUP_CONCAT(FORMAT(FLOOR(tdm.SinagRateBuying * 100) / 100, 2))
                    WHEN fd.CurrencyID IN (12, 14, 31) THEN GROUP_CONCAT(FORMAT(FLOOR(tdm.SinagRateBuying * 100000) / 100000, 4))
                    ELSE GROUP_CONCAT(tdm.SinagRateBuying)
                END AS Rate
            ')
            ->join('tblforextransactiondetails as fd' , 'tdm.FTDID' , 'fd.FTDID')
            ->when(is_array($FTDIDs), function ($query) use ($FTDIDs) {
                return $query->whereIn('tdm.FTDID', $FTDIDs);
            }, function ($query) use ($FTDIDs) {
                return $query->where('tdm.FTDID', $FTDIDs);
            })
            ->groupBy('tdm.FTDID')
            ->orderBy('tdm.FTDID', 'DESC')
            ->pluck('Rate')
            ->toArray();

        foreach ($result['transact_details'] as $index => $transaction) {
            $transaction->rates = $rates[$index];
        }

        return view('buying_transact.add_new_buying_transact', compact('result' , 'rates', 'menu_id'));
    }

    public function scDetails(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $r_set =  session('time_toggle_status') == 1 ? 'O' : '';

        $date_to = $request->query('date-to-search');
        $date_from = $request->query('date-from-search');
        $invoice_no = $request->query('invoice-search');
        $filter = intval($request->query('radio-search-type'));

        $details = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->selectRaw('fd.TransactionNo, fd.ORNo, tr.Currency, fd.CurrencyAmount, FLOOR(fd.RIBRate) as whole_rate, fd.RIBRate, (fd.RIBRate - FLOOR(fd.RIBRate)) as decimal_rate, fd.RIBAmount, tcx.FullName')
            ->join('tblcurrency as tr' , 'fd.CurrencyID' , 'tr.CurrencyID')
            ->join('pawnshop.tblxcustomer as tcx' , 'fd.CustomerID' , 'tcx.CustomerID')
            ->where('fd.Voided', 0)
            ->where('fd.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->when($r_set == 'O', function($query) use ($r_set) {
                return $query->where('fd.Rset', '=', $r_set);
            })
            ->when(empty($filter), function ($query) use ($raw_date) {
                return $query->where('fd.TransactionDate', $raw_date->toDateString());
            })
            ->when(!empty($filter), function ($query) use ($filter, $date_from, $date_to, $invoice_no) {
                switch ($filter) {
                    case 1:
                        return $query->whereBetween('fd.TransactionDate', [$date_from, $date_to]);
                    case 2:
                        return $query->where('fd.ORNo', $invoice_no);
                    default:
                        return $query;
                }
            })
            ->groupBy('fd.TransactionNo', 'fd.ORNo', 'tr.Currency', 'fd.CurrencyAmount', 'whole_rate', 'fd.RIBRate', 'decimal_rate', 'fd.RIBAmount', 'tcx.FullName')
            ->orderBy('fd.TransactionNo' , 'DESC')
            ->get();

        $response = [
            'details' => $details
        ];

        return response()->json($response);
    }

    public function search(Request $request) {
        $search = $request->query('query');
        $filter = $request->query('filter');
        $fromdatefilter = $request->query('fromdatefilter');
        $todatefilter = $request->query('todatefilter');
        $showall = $request->query('showall')? 1 : 0;

        dd($filter);
    }

    public function add(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['transact_type'] = DB::connection('forex')->table('tbltransactiontype')
            ->where('tbltransactiontype.Active', '!=', 0)
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

        return view('buying_transact.buying_transact', compact('result', 'menu_id'));
    }

    public function denominations(Request $request) {
        $sel_curr_id = $request->get('sel_curr_id');
        $transact_type = $request->get('transact_type');

        $rate_config = DB::connection('forex')->table('tblrateconfig')
            ->where('tblrateconfig.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->where('tblrateconfig.CurrencyID', '=', $sel_curr_id)
            ->get();

        $currency_denom = DB::connection('forex')->table('tblcurrencydenom')
            ->where('tblcurrencydenom.CurrencyID' , '=' , $sel_curr_id)
            ->where('tblcurrencydenom.TransType' , '=' , $transact_type)
            ->where('tblcurrencydenom.StopBuying' , '=' , 0)
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

        $currency_details = DB::connection('forexcurrency')->table('tblcurrency')
            ->leftJoin('forexcurrency.tblcurrencydetails' , 'tblcurrency.CurrID' , '=' , 'forexcurrency.tblcurrencydetails.CurrID')
            ->where('tblcurrencydetails.CurrID' , '=' ,  $currency_->CurrID)
            ->get();

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
            'currency_details' => $currency_details,
            'dpofx_rate' => $dpofx_rate,
            'test_details' => $test_details
        ];

        return response()->json($response);
    }

    public function currencies(Request $request) {
        $receipt_set = $request->get('receipt_set');
        $transact_type = $request->get('transact_type');
        $r_set =  session('time_toggle_status') == 1 ? 1 : '';

        $currencies = null;

        switch ($transact_type) {
            case '1':
                $excluded_currencies = DB::connection('forex')->table('tblcurrencydenom')
                    ->select('tblcurrencydenom.CurrencyID')
                    ->where('tblcurrencydenom.BranchID', Auth::user()->getBranch()->BranchID)
                    ->where('tblcurrencydenom.TransType', $transact_type)
                    ->groupBy('tblcurrencydenom.CurrencyID')
                    ->havingRaw('SUM(CASE WHEN tblcurrencydenom.StopBuying = 1 THEN 1 ELSE 0 END) = COUNT(tblcurrencydenom.StopBuying)')
                    ->pluck('CurrencyID');

                $currencies = DB::connection('forex')->table('tblcurrency')
                    ->select('tblcurrency.CurrencyID', 'tblcurrency.Currency')
                    ->join('tblcurrencydenom', 'tblcurrency.CurrencyID', 'tblcurrencydenom.CurrencyID')
                    ->join('tblbranch', 'tblcurrencydenom.BranchID', 'tblbranch.BranchID')
                    ->when($receipt_set == 'O', function($query) {
                        return $query->where('tblcurrency.WithSetO', '=', 1);
                    })
                    ->when($receipt_set == 'B', function($query) {
                        return $query->where('tblcurrency.WithSetB', '=', 1);
                    })
                    ->when($r_set == 1, function($query) use ($r_set) {
                        return $query->where('tblcurrency.WithSetO', '=', $r_set);
                    })
                    ->where('tblcurrencydenom.BranchID', Auth::user()->getBranch()->BranchID)
                    ->whereNotIn('tblcurrency.CurrencyID', $excluded_currencies)
                    ->groupBy('tblcurrency.CurrencyID', 'tblcurrency.Currency')
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
                $excluded_currencies = DB::connection('forex')->table('tblcurrencydenom')
                    ->select('tblcurrencydenom.CurrencyID')
                    ->where('tblcurrencydenom.BranchID', Auth::user()->getBranch()->BranchID)
                    ->where('tblcurrencydenom.TransType', $transact_type)
                    ->groupBy('tblcurrencydenom.CurrencyID')
                    ->havingRaw('SUM(CASE WHEN tblcurrencydenom.StopBuying = 1 THEN 1 ELSE 0 END) = COUNT(tblcurrencydenom.StopBuying)')
                    ->pluck('CurrencyID');

                $currencies = DB::connection('forex')->table('tblcurrency')
                    ->select('tblcurrency.CurrencyID', 'tblcurrency.Currency')
                    ->join('tblcurrencydenom', 'tblcurrency.CurrencyID', 'tblcurrencydenom.CurrencyID')
                    ->join('tblbranch', 'tblcurrencydenom.BranchID', 'tblbranch.BranchID')
                    ->when($receipt_set == 'O', function($query) {
                        return $query->where('tblcurrency.WithSetO', '=', 1);
                    })
                    ->when($receipt_set == 'B', function($query) {
                        return $query->where('tblcurrency.WithSetB', '=', 1);
                    })
                    ->where('tblcurrencydenom.BranchID', Auth::user()->getBranch()->BranchID)
                    ->whereNotIn('tblcurrency.CurrencyID', $excluded_currencies)
                    ->groupBy('tblcurrency.CurrencyID', 'tblcurrency.Currency')
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
        $raw_date = Carbon::now('Asia/Manila');

        $or_numbers = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->where('fd.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->select('fd.ORNo')
            ->where('fd.TransactionDate', '>', $raw_date->parse('2025-01-01'))
            ->where('fd.ORNo', $request->get('current_or_number'))
            ->where('fd.Voided', 0)
            ->exists();

        $or_numbers_sell = DB::connection('forex')->table('tblsoldcurrdetails as sc')
            ->select('sc.ORNo')
            ->where('sc.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->where('sc.DateSold', '>', $raw_date->parse('2025-01-01'))
            ->where('sc.ORNo', $request->get('current_or_number'))
            ->where('sc.Voided', 0)
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

    public function mtcnDuplicateBuying(Request $request) {
        $boolean = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->where('fd.BranchID', Auth::user()->getBranch()->BranchID)
            ->where('fd.TransType', 4)
            ->where('fd.MTCN', $request->get('mtcn_number'))
            ->exists();

        $response = [
            'boolean' => $boolean
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
        $radio_rset = session('time_toggle_status') == 1 ? 'O' : 'O';
        // $radio_rset =  session('time_toggle_status') == 1 ? 'O' : $request->input('radio-rset');

        $get_transaction_no = DB::connection('forex')->table('tblforextransactiondetails')
            ->selectRaw('MAX(TransactionNo) + 1 AS updatedTransactNo')
            ->value('updatedTransactNo');

        $get_receipt_no = DB::connection('forex')->table('tblforextransactiondetails')
            ->selectRaw('MAX(ReceiptNo) + 1 AS updatedReceiptNo')
            ->value('updatedReceiptNo');

        $customer_entry_id = $request->input('customer-entry-id');
        $rset_dpofx = $request->input('rset-dpofx');

        // DPOFX input fields
        $subtotal_dpofx = $request->input('subtotal');

        $CRID = DB::connection('forex')->table('tblcurrentrate as tcr')
            ->selectRaw('MAX(CRID) as CRID')
            ->where('tcr.CurrencyID', $request->input('currencies'))
            ->pluck('CRID');

        $RIB_buying_variance = DB::connection('forex')->table('tblcurrentrate as tcr')
            ->selectRaw('SUM(tcr.Rate - tc.RIBVariance) as RIB_buying_variance')
            ->join('tblcurrency as tc', 'tcr.CurrencyID', 'tc.CurrencyID')
            ->where('tcr.CRID', $CRID)
            ->value('RIB_buying_variance');

        $RateUsed = DB::connection('forex')->table('tblcurrencydenom as tcd')
            ->selectRaw('tcd.SinagRateBuying')
            ->where('tcd.CurrencyID', $request->input('currencies'))
            ->where('tcd.BranchID', Auth::user()->getBranch()->BranchID)
            ->groupBy('tcd.SinagRateBuying')
            ->value('RateUsed');

        switch ($request->input('radio-transact-type')) {
            case '1':
            case '2':
            case '3':
                $data = array(
                    'CurrencyID' => $request->input('currencies'),
                    'CurrencyAmount' => $request->input('current_amount_true'),
                    'RateUsed' => $RateUsed,
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
                    'Rset' => $radio_rset,
                    'ReceiptNo' => $get_receipt_no,
                    'CompanyID' => Auth::user()->getBranch()->CompanyID,
                    'RIBRate' => $RIB_buying_variance,
                    'RIBAmount' => $RIB_buying_variance * $request->input('current_amount_true'),
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
                    DB::connection('forex')->table('tblforextransactiondetails')
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

                $get_transact_deets_ftdid = DB::connection('forex')->table('tblforextransactiondetails')
                    ->orderBy('tblforextransactiondetails.FTDID', 'DESC')
                    ->limit(1)
                    ->select('tblforextransactiondetails.FTDID')
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
                    DB::connection('forex')->table('tbldenom')->insert([
                        'FTDID' => $get_transact_deets_ftdid[0]->FTDID,
                        'BillAmount' => $new_bill_amount_array[$key_test],
                        'Multiplier' => $multip_parsed_array[$key_test],
                        'Total' => $subutotal_parsed_array[$key_test],
                        'SinagRateBuying' => $buying_raw_new_array[$key_test],
                        'RIBRate' => $RIB_buying_variance,
                        // 'VarianceBuying' => $sinag_var_buying_parsed_array[$key_test],
                    ]);
                }

                $get_tbldenom_denom_id = DB::connection('forex')->table('tbldenom')
                    ->where('tbldenom.FTDID' , '=' , $get_transact_deets_ftdid[0]->FTDID)
                    ->select('tbldenom.DenomID')
                    ->get();

                foreach ($multip_val_to_int_array as $multip_key => $multip_value) {
                    $new_set_index = array_fill(0, $multip_value, null);

                    foreach($new_set_index as $new_index_key => $new_index_value) {
                        DB::connection('forex')->table('tblforexserials')->insert([
                            'FTDID' => $get_transact_deets_ftdid[0]->FTDID,
                            'BillAmount' => $new_bill_amount_array[$multip_key],
                            'DenomID' => $get_tbldenom_denom_id[$multip_key]->DenomID,
                            'Serials' => null,
                            'UserID' => $request->input('matched_user_id'),
                            'FSType' => $request->input('radio-transact-type')
                        ]);
                    }
                }

                $latest_ftdid = $get_transact_deets_ftdid[0]->FTDID;

                session(['buying_trans_open_count' => 1]);

                $message = "Buying Transaction Success!";
                return response()->json(['message' => 'Buying Transaction Success!', 'latest_ftdid' => $latest_ftdid]);

                break;
            case '4':
                $data = array(
                    'CurrencyID' => $request->input('currencies'),
                    'CurrencyAmount' => $request->input('dpofx-bill-amount'),
                    'RateUsed' => $request->get('dpofx_rate'),
                    'Amount' => $request->input('payout_amount'),
                    'EntryDate' => $raw_date->toDateTimeString(),
                    'UserID' => $request->input('matched_user_id'),
                    'CustomerID' => $request->input('customer-id-selected'),
                    'MTCN' => $request->input('mtcn_number'),
                    'TransactionDate' => $raw_date->toDateString(),
                    'TransactionTime' => $raw_date->toTimeString(),
                    'BranchID' => Auth::user()->getBranch()->BranchID,
                    'TransactionNo' => $get_transaction_no,
                    'ORNo' => $request->input('or-number-buying'),
                    'TransType' => $request->input('radio-transact-type'),
                    'Rset' => $radio_rset,
                    'ReceiptNo' => $get_receipt_no,
                    'CompanyID' => Auth::user()->getBranch()->CompanyID,
                    'RIBRate' => $RIB_buying_variance,
                    'RIBAmount' => $RIB_buying_variance * $request->input('dpofx-bill-amount'),
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
                    DB::connection('cis')->table('tbltranstypehistory')
                        ->where('tbltranstypehistory.EntryID', '=', $customer_entry_id)
                        ->update([
                            'UsedFlag' => 1
                        ]);

                    $get_transact_ftdid = DB::connection('forex')->table('tblforextransactiondetails')
                        ->insertGetId($data);

                    $get_transact_denom_id = DB::connection('forex')->table('tbldenom')
                        ->where('tblforexserials.FTDID', '=', $get_transact_ftdid)
                        ->insertGetId([
                            'FTDID' => $get_transact_ftdid,
                            'BillAmount' => $request->input('dpofx-bill-amount'),
                            'Multiplier' => 1,
                            'Total' => $request->input('dpofx-bill-amount'),
                            'SinagRateBuying' => $request->input('dpofx-rate'),
                        ]);

                    DB::connection('forex')->table('tblforexserials')
                        ->where('tblforexserials.FTDID', '=', $get_transact_ftdid)
                        ->insert([
                            'FTDID' => $get_transact_ftdid,
                            'Serials' => 'DPOFX',
                            'BillAmount' => $request->input('dpofx-bill-amount'),
                            'UserID' => $request->input('matched_user_id'),
                            'EntryDate' => $raw_date->toDateTimeString(),
                            'FSType' => $request->input('radio-transact-type'),
                            'FSStat' => 1,
                            'DenomID' => $get_transact_denom_id,
                        ]);
                }

                $get_transact_deets_ftdid = DB::connection('forex')->table('tblforextransactiondetails')
                    ->orderBy('tblforextransactiondetails.FTDID', 'DESC')
                    ->limit(1)
                    ->select('tblforextransactiondetails.FTDID')
                    ->get();

                $latest_ftdid = $get_transact_deets_ftdid[0]->FTDID;

                session(['buying_trans_open_count' => 1]);

                $message = "Buying Transaction Success!";
                return response()->json(['message' => 'Buying Transaction Success!', 'latest_ftdid' => $latest_ftdid]);

                break;
            default:
                dd("no transactions available!");
        }
    }

    public function update(Request $request) {
        // $receipt_set = $request->input('radio-rset') == null ? $request->input('transact-receipt-rset') : $request->input('radio-rset');
        $receipt_set = $request->input('radio-rset') == null ? 'O' : $request->input('radio-rset');
        $or_number = $request->input('or-number-buying') == null || $receipt_set == 'B' ? null : $request->input('or-number-buying');
        $customer_id = $request->input('customer-id-selected') == null ? $request->input('transact-customer-id') : $request->input('customer-id-selected');
        $MTCN = $request->input('new-transact-mtcn') == null ? $request->input('transact-mtcn') : $request->input('new-transact-mtcn');

        $data_updated = array(
            'Rset' =>  $receipt_set,
            'ORNo' => $or_number,
            'CustomerID' => $customer_id,
            'MTCN' => $MTCN,
        );

        $validator = Validator::make($request->all(), [
            'radio-rset' => 'nullable',
			'or-number-buying' => 'nullable',
			'customer-id-selected' => 'nullable',
        ]);

        if ($validator->fails()) {
			return redirect()->back()->withErrors($validator);
		} else {
			DB::connection('forex')->table('tblforextransactiondetails')
                ->where('tblforextransactiondetails.FTDID', '=' , $request->get('trans_id'))
                ->update($data_updated);

            $response = [
                'FTDID' => $request->get('trans_id')
            ];
    
            return response()->json($response);
		}
    }

    public function updateRate(Request $request) {
        $rates = explode(", ", $request->get('rates'));
        $denom_ids = explode(", ", $request->get('denom_ids'));
        $total_amount = explode(", ", $request->get('total_amount'));

        DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->where('fd.FTDID', $request->get('FTDID'))
            ->update([
                'Amount' => str_replace(',', '', $request->get('new_total_amnt')),
            ]);

        foreach ($denom_ids as $key => $IDs) {
            DB::connection('forex')->table('tbldenom as td')
                ->where('td.DenomID', $IDs)
                ->update([
                    'SinagRateBuying' => $rates[$key],
                ]);
        }

        $response = [
            'FTDID' => $request->get('FTDID')
        ];

        return response()->json($response);
    }

    public function delete(Request $request) {
        DB::connection('forex')->table('tblforextransactiondetails')
            ->where('tblforextransactiondetails.FTDID', '=' ,  $request->get('trans_id'))
            ->update([
                'Voided' => 1
            ]);

        $message = "Transaction deleted successfully!";
		return redirect()->back()->with('message' , $message);
    }

    public function printCountBuying(Request $request) {
        $buying_transaction_details = DB::connection('forex')->table('tblforextransactiondetails')
            ->where('tblforextransactiondetails.FTDID', '=', $request->get('b_trans_id'))
            ->selectRaw('MAX(Print) + 1 AS latest_print_count')
            ->value('latest_print_count');

        DB::connection('forex')->table('tblforextransactiondetails')
            ->where('tblforextransactiondetails.FTDID', '=', $request->get('b_trans_id'))
            ->update([
                'Print' => $buying_transaction_details
            ]);


        return response()->json(['print_b_count_latest' => $buying_transaction_details]);
    }

    public function details(Request $request) {
        $forex_connection = DB::connection('forex');
        $pawnshop_connection = DB::connection('pawnshop');
        $sesh_username = session('user_name');

        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['transact_details']  = $forex_connection->table('tblforextransactiondetails as fd')
            ->selectRaw('fd.TransactionDate, fd.TransactionTime, fd.TransactionNo, fd.FTDID, fd.ReceiptNo, fd.BranchID, fd.Rset, fd.ORNo, fd.MTCN, fd.CustomerID, fd.Print, tc.Currency, fd.CurrencyID, tc.CurrAbbv, tt.TransType, tt.TTID, fd.CurrencyAmount, fd.Amount, fd.RateUsed, pawnshop.tblxusers.Username, pawnshop.tblxusers.Name, pawnshop.tblxcustomer.FullName')
            ->leftJoin('tblcurrency as tc' , 'fd.CurrencyID' , 'tc.CurrencyID')
            ->leftJoin('tbltransactiontype as tt' , 'fd.TransType' , 'tt.TTID')
            ->leftJoin('pawnshop.tblxusers', 'fd.UserID', '=', 'pawnshop.tblxusers.UserID')
            ->leftJoin('pawnshop.tblxcustomer', 'fd.CustomerID', '=', 'pawnshop.tblxcustomer.CustomerID')
            ->groupBy('fd.TransactionDate', 'fd.TransactionTime', 'fd.TransactionNo', 'fd.FTDID', 'fd.ReceiptNo', 'fd.BranchID', 'fd.Rset', 'fd.ORNo', 'fd.MTCN', 'fd.CustomerID', 'fd.Print', 'tc.Currency', 'fd.CurrencyID', 'tc.CurrAbbv', 'tt.TransType', 'tt.TTID', 'fd.CurrencyAmount', 'fd.Amount', 'fd.RateUsed', 'pawnshop.tblxusers.Username', 'pawnshop.tblxusers.Name', 'pawnshop.tblxcustomer.FullName')
            ->where('fd.FTDID', '=' , $request->id)
            ->get();

        $date = Carbon::now('Asia/Manila');
        $date_now = $date->toDateString();

        $user_info = DB::connection('forex')->table('tblbranch')
            ->where('tblbranch.BranchCode', '=', session('branch_code'))
            ->first();

        $result['forex_serials'] = DB::connection('forex')->table('tblforextransactiondetails')
            ->join('tblcurrency' , 'tblforextransactiondetails.CurrencyID' , 'tblcurrency.CurrencyID')
            ->join('tblforexserials' , 'tblforextransactiondetails.FTDID' , '=' , 'tblforexserials.FTDID')
            ->join('tbltransactiontype' , 'tblforextransactiondetails.TransType' , '=' , 'tbltransactiontype.TTID')
            // ->where('tblforextransactiondetails.TransactionDate', '=' , $date_now)
            ->where('tblforexserials.FTDID', '=' , $request->id)
            // ->where('tblforexserials.FSStat' , '=' , 1)
            ->select(
                'tblcurrency.Currency',
                'tblcurrency.CurrencyID',
                'tblforexserials.BillAmount',
                'tbltransactiontype.TransType',
                'tblforexserials.FTDID',
                'tblforexserials.FSID',
                'tblforexserials.Serials'
            )
            ->paginate(15);
            // ->get();

        $query = DB::connection('forex')->table('tbldenom')
            ->where('tbldenom.FTDID', '=' , $request->id);

        $SC_Rate = $query->clone()
                ->selectRaw('tbldenom.RIBRate as SC_Rate')
                ->groupBy('SC_Rate')
                ->value('SC_Rate');

        $buying_rate = $query->clone()
                ->selectRaw('tbldenom.SinagRateBuying as BuyingRate')
                ->groupBy('BuyingRate')
                ->value('BuyingRate');

        $result['denom_details'] = $query->clone()
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

        return view('buying_transact.buying_transaction', compact('result', 'menu_id', 'SC_Rate', 'buying_rate'));
    }

    public function pendingSerials(Request $request) {
        $date = Carbon::now('Asia/Manila');
        $date_now = $date->toDateString();

        $result['pending_serials'] = DB::connection('forex')->table('tblforextransactiondetails')
            ->leftJoin('tblcurrency' , 'tblforextransactiondetails.CurrencyID' , 'tblcurrency.CurrencyID')
            ->leftJoin('tblforexserials' , 'tblforextransactiondetails.FTDID' , '=' , 'tblforexserials.FTDID')
            ->leftJoin('tbltransactiontype' , 'tblforextransactiondetails.TransType' , '=' , 'tbltransactiontype.TTID')
            ->where('tblforextransactiondetails.TransactionDate', '=' , $date_now)
            ->where('tblforexserials.FTDID', '=' , $request->id)
            ->where('tblforexserials.FSStat' , '=' , 1)
            ->select(
                'tblforexserials.BillAmount',
                'tbltransactiontype.TransType',
                'tblforexserials.FTDID',
                'tblforexserials.FSID',
                'tblforexserials.Serials',
                'tblcurrency.Currency',
            )
            ->get();

        return view('buying_transact.transact_pending_serials')->with('result', $result);
    }

    public function addSerials(Request $request) {
        $forex_ftdid = $request->input('forex-ftdid');
        $FSIDs = explode(',', $request->input('parsed_fsid'));
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
                if (isset($FSIDs[$key])) {
                    $update_data = ['Serials' => $serial];

                    DB::connection('forex')->table('tblforexserials')
                        ->where('tblforexserials.FSID', '=', $FSIDs[$key])
                        ->update($update_data);
                }
            }

            $message = "Serials Added!";
            return response()->json(['message' => $message]);
        }
    }

    public function addDenom(Request $request) {
        $forex_ftdid = $request->input('forex-ftdid-denom');
        $denom_bill_amnt = $request->input('denom-bill-amount');
        $denom_multiplier = $request->input('denom-multiplier');
        $true_denom_total_amnt = $request->input('true-denom-total-amount');
        $current_amount = $request->input('get-transact-current-amount');
        $curr_amnt_parsed = str_replace(',', '', $current_amount);

        $get_ftdid = DB::connection('forex')->table('tbldenom')
            ->where('tbldenom.FTDID', '=' , $forex_ftdid)
            ->first();

        $denom_ftdid = array($get_ftdid);

        $new_set_index = array_fill(0, $denom_multiplier, null);

        DB::connection('forex')->table('tbldenom')
            ->where('tbldenom.FTDID', '=' , $denom_ftdid[0]->FTDID)->insert([
                'FTDID' => $denom_ftdid[0]->FTDID,
                'BillAmount' => $denom_bill_amnt,
                'Multiplier' => $denom_multiplier,
                'Total' => $true_denom_total_amnt,
            ]);

        $get_tbldenom_denom_id = DB::connection('forex')->table('tbldenom')
            ->where('tbldenom.FTDID' , '=' , $forex_ftdid)
            ->select('tbldenom.DenomID')
            ->get();

        foreach($new_set_index as $new_index_key => $new_index_value) {
            DB::connection('forex')->table('tblforexserials')->insert([
                'FTDID' => $forex_ftdid,
                'BillAmount' => $denom_bill_amnt,
                'DenomID' => $get_tbldenom_denom_id[$new_index_key]->DenomID,
                'Serials' => null
            ]);
        }

        $updated_amount = intval($curr_amnt_parsed) + intval($true_denom_total_amnt);

        DB::connection('forex')->table('tblforextransactiondetails')
            ->where('tblforextransactiondetails.FTDID' , '=' , $forex_ftdid)
            ->update([
                'CurrencyAmount' => $updated_amount
            ]);

        $message = "Denomination/s Added!";
        return redirect()->back()->with('message' , $message);
    }
}
