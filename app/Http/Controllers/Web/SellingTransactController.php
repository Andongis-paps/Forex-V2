<?php

namespace App\Http\Controllers\Web;
use DB;
use App;
use Auth;
use Hash;
use Lang;
use Session;
use App\Admin;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Helpers\CustomerManagement;
use App\Http\Controllers\Controller;

class SellingTransactController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:SELLING TRANSACTION,VIEW')->only(['show', 'details']);
        $this->middleware('check.access.permission:SELLING TRANSACTION,ADD')->only(['add', 'save']);
        $this->middleware('check.access.permission:SELLING TRANSACTION,EDIT')->only(['edit', 'update']);
        $this->middleware('check.access.permission:SELLING TRANSACTION,DELETE')->only(['delete']);
        $this->middleware('check.access.permission:SELLING TRANSACTION,PRINT')->only(['details', 'printCountSelling']);
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

        $result['selling_transact_details'] = DB::connection('forex')->table('tblsoldcurrdetails as sc')
            ->selectRaw('sc.DateSold, sc.SellingNo, sc.ReceiptNo, sc.ORNo, tc.Currency, sc.CurrencyID, sc.CurrAmount, FLOOR(sc.RateUsed) as whole_rate, sc.RateUsed, (sc.RateUsed - FLOOR(sc.RateUsed)) as decimal_rate, sc.AmountPaid, sc.Rset, sc.SCID, sc.Voided, sc.HasTicket, txc.FullName, tbx.Name as encoder')
            ->join('tblcurrency as tc',  'sc.CurrencyID' , 'tc.CurrencyID')
            ->join('pawnshop.tblxusers as tbx', 'sc.UserID', 'tbx.UserID')
            ->join('pawnshop.tblxcustomer as txc' , 'sc.CustomerID' , 'txc.CustomerID')
            ->where('sc.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->when($r_set == 'O', function($query) use ($r_set) {
                return $query->where('sc.Rset', '=', $r_set);
            })
            ->when(empty($filter), function ($query) use ($raw_date) {
                return $query->where('sc.DateSold', $raw_date->toDateString());
            })
            ->when(!empty($filter), function ($query) use ($filter, $date_from, $date_to, $invoice_no) {
                switch ($filter) {
                    case 1:
                        return $query->whereBetween('sc.DateSold', [$date_from, $date_to]);
                    case 2:
                        return $query->where('sc.ORNo', $invoice_no);
                    default:
                        return $query;
                }
            })
            ->groupBy('sc.DateSold', 'sc.SellingNo', 'sc.ReceiptNo', 'sc.ORNo', 'tc.Currency', 'sc.CurrencyID', 'sc.CurrAmount', 'whole_rate', 'sc.RateUsed', 'decimal_rate', 'sc.AmountPaid', 'sc.Rset', 'sc.SCID', 'sc.Voided', 'sc.HasTicket', 'txc.FullName', 'encoder')
            ->orderBy('sc.SellingNo' , 'DESC')
            ->paginate(30);

        return view('selling_transact.add_new_selling_transact', compact('result', 'menu_id'));
    }

    public function add(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $r_set =  session('time_toggle_status') == 1 ? 'O' : '';

        $customerid = $request->query('customerid');
        if ($customerid) $result['customer'] = CustomerManagement::customerInfo($customerid);

        $selling_transact = DB::connection('forex')->table('tblforextransactiondetails')
            ->join('tblcurrency', 'tblforextransactiondetails.CurrencyID', '=', 'tblcurrency.CurrencyID')
            ->join('tblforexserials', 'tblforextransactiondetails.FTDID', '=', 'tblforexserials.FTDID')
            ->where('tblforexserials.FSType' , '=' , 1)
            ->where('tblforexserials.FSStat' , '=' , 1)
            ->where('tblforexserials.Sold' , '=' , 0)
            ->where('tblforexserials.Transfer' , '=' , 0)
            ->where('tblforexserials.Received' , '=' , 0)
            ->where('tblforexserials.Queued' , '=' , 0)
            ->where('tblforexserials.SoldToManila' , '=' , 0)
            ->where('tblforexserials.Serials', '!=', null)
            ->where('tblforextransactiondetails.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->where('tblforextransactiondetails.TransactionDate', '>=', '2025-01-01');

        $result['available_serials'] = $selling_transact->clone()
            ->selectRaw('tblforexserials.BillAmount, tblcurrency.Currency, tblforextransactiondetails.Rset, COUNT(tblforexserials.BillAmount) as bill_amount_count')
            ->when($r_set == 'O', function($query) use ($r_set) {
                return $query->where('tblforextransactiondetails.Rset', '=', $r_set);
            })
            ->groupBy('tblforexserials.BillAmount' , 'tblcurrency.Currency', 'tblforextransactiondetails.Rset')
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->orderBy('tblforexserials.BillAmount', 'DESC')
            ->get();

        $result['currency'] = $selling_transact->clone()
            ->select('tblforextransactiondetails.CurrencyID', 'tblcurrency.Currency')
            ->when($r_set == 'O', function($query) use ($r_set) {
                return $query->where('tblforextransactiondetails.Rset', '=', $r_set);
            })
            ->groupBy('tblforextransactiondetails.CurrencyID', 'tblcurrency.Currency')
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->get();

        $result['stocks_set_b'] = $selling_transact->clone()
            ->selectRaw('tblforexserials.BillAmount, tblcurrency.Currency, tblforextransactiondetails.Rset, COUNT(tblforexserials.BillAmount) as bill_amount_count')
            ->where('tblforextransactiondetails.Rset', '=', 'B')
            ->groupBy('tblforexserials.BillAmount' , 'tblcurrency.Currency', 'tblforextransactiondetails.Rset')
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->orderBy('tblforexserials.BillAmount', 'DESC')
            ->get();

        $result['stocks_set_o'] = $selling_transact->clone()
            ->selectRaw('tblforexserials.BillAmount, tblcurrency.Currency, tblforextransactiondetails.Rset, COUNT(tblforexserials.BillAmount) as bill_amount_count, GROUP_CONCAT(tblforexserials.Serials) as serials')
            ->where('tblforextransactiondetails.Rset', '=', 'O')
            ->groupBy('tblforexserials.BillAmount' , 'tblcurrency.Currency', 'tblforextransactiondetails.Rset')
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->orderBy('tblforexserials.BillAmount', 'DESC')
            ->get();

        return view('selling_transact.selling_transact', compact('result'));
    }

    public function scDetails(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $r_set =  session('time_toggle_status') == 1 ? 'O' : '';

        $date_to = $request->query('date-to-search');
        $date_from = $request->query('date-from-search');
        $invoice_no = $request->query('invoice-search');
        $filter = intval($request->query('radio-search-type'));

        $details = DB::connection('forex')->table('tblsoldcurrdetails as sc')
            ->selectRaw('sc.DateSold, sc.SellingNo, sc.ORNo, tc.Currency, sc.CurrencyID, sc.CurrAmount, FLOOR(sc.RIBRate) as whole_rate, sc.RIBRate, (sc.RIBRate - FLOOR(sc.RIBRate)) as decimal_rate, sc.RIBAmount, txc.FullName')
            ->join('tblcurrency as tc',  'sc.CurrencyID' , 'tc.CurrencyID')
            ->join('pawnshop.tblxcustomer as txc' , 'sc.CustomerID' , 'txc.CustomerID')
            ->where('sc.Voided', 0)
            ->where('sc.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->when($r_set == 'O', function($query) use ($r_set) {
                return $query->where('sc.Rset', '=', $r_set);
            })
            ->when(empty($filter), function ($query) use ($raw_date) {
                return $query->where('sc.DateSold', $raw_date->toDateString());
            })
            ->when(!empty($filter), function ($query) use ($filter, $date_from, $date_to, $invoice_no) {
                switch ($filter) {
                    case 1:
                        return $query->whereBetween('sc.DateSold', [$date_from, $date_to]);
                    case 2:
                        return $query->where('sc.ORNo', $invoice_no);
                    default:
                        return $query;
                }
            })
            ->groupBy('sc.DateSold', 'sc.SellingNo', 'sc.ReceiptNo', 'sc.ORNo', 'tc.Currency', 'sc.CurrencyID', 'sc.CurrAmount', 'whole_rate', 'sc.RIBRate', 'sc.RIBAmount', 'decimal_rate', 'txc.FullName')
            ->orderBy('sc.SellingNo' , 'ASC')
            ->get();

        $response = [
            'details' => $details
        ];

        return response()->json($response);
    }

    public function availableCurrency(Request $request) {
        $currency = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->join('tblcurrency as tc', 'fd.CurrencyID', '=', 'tc.CurrencyID')
            ->join('tblforexserials as fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->where('fd.Voided', 0)
            ->where('fs.FSType', 1)
            ->where('fs.FSStat', 1)
            ->where('fs.Sold', 0)
            ->where('fs.Transfer', 0)
            ->where('fs.Received', 0)
            ->where('fs.Queued', 0)
            ->where('fs.SoldToManila', 0)
            ->where('fs.Serials', '!=', null)
            ->where('fd.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->select('fd.CurrencyID', 'tc.Currency')
            ->where('fd.Rset', '=', $request->get('rset_value'))
            ->groupBy('fd.CurrencyID', 'tc.Currency')
            ->orderBy('tc.Currency', 'ASC')
            ->get();

        $response = [
            'currency' => $currency
        ];

        return response()->json($response);
    }

    public function serialDetails(Request $request) {
        $rate_used_selling = DB::connection('forex')->table('tblcurrentrate')
            ->leftJoin('tblcurrency', 'tblcurrentrate.CurrencyID', 'tblcurrency.CurrencyID')
            ->whereRaw('tblcurrentrate.EntryDateTime = (SELECT MAX(EntryDateTime) FROM tblcurrentrate WHERE CurrencyID = :selected_curr_id)' , ['selected_curr_id' => $request->get('selected_curr_id')])
            ->select('tblcurrentrate.CurrencyID', 'tblcurrentrate.EntryDateTime', 'tblcurrentrate.Rate', 'tblcurrentrate.EntryDate', 'tblcurrentrate.CRID')
            ->get();

        // $rate_config = DB::connection('forex')->table('tblrateconfig')
        //     ->where('tblrateconfig.BranchID', '=', Auth::user()->getBranch()->BranchID)
        //     ->where('tblrateconfig.CurrencyID', '=',  $request->get('selected_curr_id'))
        //     ->get();

        $rate_config = DB::connection('forex')->table('tblcurrencydenom')
            ->selectRaw('tblcurrencydenom.VarianceSelling')
            ->where('tblcurrencydenom.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->where('tblcurrencydenom.CurrencyID', '=',  $request->get('selected_curr_id'))
            ->groupBy('tblcurrencydenom.VarianceSelling')
            ->get();

        $stocks_query = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->join('tblcurrency as tc', 'fd.CurrencyID', '=', 'tc.CurrencyID')
            ->join('tblforexserials as fs', 'fd.FTDID', '=', 'fs.FTDID')
            // ->where('fd.Rset', '=', $request->get('selected_r_set'))
            ->where('fd.Rset', '=', 'O')
            ->where('tc.CurrencyID' , '=' ,  $request->get('selected_curr_id'))
            ->where('fs.FSType' , '=' , 1)
            ->where('fs.FSStat' , '=' , 1)
            ->where('fs.Received' , '=' , 0)
            ->where('fs.Queued' , '=' , 0)
            ->where('fs.SoldToManila' , '=' , 0)
            ->where('fs.Sold' , '=' , 0)
            ->where('fs.Serials', '!=', null)
            ->where('fd.BranchID' , '=' , Auth::user()->getBranch()->BranchID)
            ->where('fd.TransactionDate', '>=', '2025-01-01')
            ->orderBy('fs.BillAmount', 'DESC');

        $avaible_serials_currency = $stocks_query->clone()
            ->select('tc.Currency', 'fs.FSID', 'fs.Serials', 'fs.BillAmount', 'fd.Rset')
            ->get();

        $denoms = $stocks_query->clone()
            ->selectRaw('GROUP_CONCAT(DISTINCT fs.BillAmount ORDER BY BillAmount DESC) as denominations')
            ->get();

        $response = [
            'denoms' => $denoms,
            'rate_config' => $rate_config,
            'rate_used_selling' => $rate_used_selling,
            'avaible_serials_currency' => $avaible_serials_currency,
        ];

        return response()->json($response);
    }

    public function save(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $radio_rset = session('time_toggle_status') == 1 ? 'O' : 'O';
        // $radio_rset =  session('time_toggle_status') == 1 ? 'O' : $request->input('radio-rset');

        // For tblsoldserials
        $bill_amnt_selling = $request->input('bill-amnt-selling');
        $bill_serial_fsid_selling = $request->input('bill-serial-fsid-selling');

        $get_receipt_selling = DB::connection('forex')->table('tblsoldcurrdetails')
            ->selectRaw('MAX(SellingNo) + 1 AS updatedSellingNo, MAX(ReceiptNo) + 1 AS updatedReceiptNo')
            ->first();

        $CRID = DB::connection('forex')->table('tblcurrentrate as tcr')
            ->selectRaw('MAX(CRID) as CRID')
            ->where('tcr.CurrencyID', $request->input('currencies-selling'))
            ->pluck('CRID');

        $RIB_selling_variance = DB::connection('forex')->table('tblcurrentrate as tcr')
            ->selectRaw('SUM(tcr.Rate + tc.RIBVariance) as RIB_selling_variance')
            ->join('tblcurrency as tc', 'tcr.CurrencyID', 'tc.CurrencyID')
            ->where('tcr.CRID', $CRID)
            ->value('RIB_selling_variance');

        $data = array(
            'CurrencyID' => $request->input('currencies-selling'),
            'CurrAmount' => array_sum($request->input('currency-amnt-selling')),
            'RateUsed' => $request->input('rate-used-true'),
            'AmountPaid' => $request->input('true-total-amnt-selling'),
            'EntryDate' => $raw_date->toDateTimeString(),
            'UserID' => $request->input('matched_user_id'),
            'CustomerID' => $request->input('customer-id-selected'),
            'DateSold' => $request->input('transact-date-selling'),
            'TimeSold' => $raw_date->toTimeString(),
            'BranchID' => Auth::user()->getBranch()->BranchID,
            'SellingNo' => $get_receipt_selling->updatedSellingNo,
            'ReceiptNo' => $get_receipt_selling->updatedReceiptNo,
            'ORNo' => $request->input('or-number-selling'),
            'Rset' => $radio_rset,
            'CompanyID' => Auth::user()->getBranch()->CompanyID,
            'RIBRate' => $RIB_selling_variance,
            'RIBAmount' => $RIB_selling_variance * array_sum($request->input('currency-amnt-selling')),
        );

        $validator = Validator::make($request->all(), [
            'transact-date-selling' => 'required',
            'customer-id-selected' => 'required',
            // 'radio-rset' => 'required',
            'currencies-selling' => 'required',
            'rate-used-true' => 'required',
            'bill-serial-selling' => 'required',
            'currency-amnt-selling' => 'required',
            'bill-amnt-selling' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        } else {
            $latest_scid = DB::connection('forex')->table('tblsoldcurrdetails')
                ->insertGetId($data);

            foreach ($bill_serial_fsid_selling as $key => $serial_fsid) {
                DB::connection('forex')->table('tblsoldserials')
                    ->insert([
                        // 'SCID' => $get_scid[0]->SCID,
                        'SCID' => $latest_scid,
                        'FSID' => $serial_fsid,
                        'UserID' => $request->input('matched_user_id'),
                        'EntryDate' => $raw_date->toDateTimeString(),
                        'BillAmount' => $bill_amnt_selling[$key],
                        'RIBRate' => $RIB_selling_variance,
                    ]);
            }

            DB::connection('forex')->table('tblforexserials')
                ->when(is_array($bill_serial_fsid_selling), function ($query) use ($bill_serial_fsid_selling) {
                    return $query->whereIn('tblforexserials.FSID', $bill_serial_fsid_selling);
                }, function ($query) use ($bill_serial_fsid_selling) {
                    return $query->where('tblforexserials.FSID', $bill_serial_fsid_selling);
                })
                ->update([
                    'Sold' => 1,
                    'FSStat' => 3
                ]);

            $response = [
                'latest_scid' => $latest_scid
            ];

            return response()->json($response);
        }
        // return redirect()->back()->with(['message' => $message, 'latest_scid' => $latest_scid]);
    }

    // public function searchSerials(Request $request) {
    //     $searched_serial = $request->get('search_serial');

    //     $serials_available = DB::connection('forex')->table('tblforextransactiondetails')
    //         ->join('tblcurrency', 'tblforextransactiondetails.CurrencyID', 'tblcurrency.CurrencyID')
    //         ->join('tblforexserials', 'tblforextransactiondetails.FTDID', 'tblforexserials.FTDID')
    //         ->where('tblforextransactiondetails.BranchID', '=', Auth::user()->getBranch()->BranchID)
    //         ->where('tblforexserials.FSType' , '=' , 1)
    //         ->where('tblforexserials.FSStat' , '=' , 1)
    //         ->where('tblforexserials.Sold' , '=' , 0)
    //         ->where('tblforextransactiondetails.Rset', '=', $request->get('selected_r_set'))
    //         ->where('tblforextransactiondetails.CurrencyID' , '=' ,  $request->get('selected_curr_id'))
    //         ->where('tblforexserials.Serials' , 'LIKE', "{$searched_serial}%")
    //         // ->when($searched_serial != null, function($query) use ($searched_serial) {
    //         //     return $query->where('tblforexserials.Serials' , 'LIKE', "{$searched_serial}%");
    //         // })
    //         ->select('tblforexserials.Serials', 'tblforexserials.BillAmount', 'tblforexserials.FSID')
    //         ->orderBy('tblforexserials.BillAmount', 'DESC')
    //         ->get();

    //     return response()->json($serials_available);
    // }

    public function orNumbDuplicateSelling(Request $request) {
        $or_numbers = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->where('fd.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->select('fd.ORNo')
            ->where('fd.ORNo', $request->get('current_or_number'))
            ->where('fd.Voided', 0)
            ->exists();

        $or_numbers_sell = DB::connection('forex')->table('tblsoldcurrdetails as sc')
            ->select('sc.ORNo')
            ->where('sc.BranchID', '=', Auth::user()->getBranch()->BranchID)
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

    public function printCountSelling(Request $request) {
        $selling_transaction_details = DB::connection('forex')->table('tblsoldcurrdetails')
            ->where('tblsoldcurrdetails.SCID', '=', $request->get('s_trans_id'))
            ->selectRaw('MAX(Print) + 1 AS latest_print_count')
            ->value('latest_print_count');

        DB::connection('forex')->table('tblsoldcurrdetails')
            ->where('tblsoldcurrdetails.SCID', '=', $request->get('s_trans_id'))
            ->update([
                'Print' => $selling_transaction_details
            ]);

        return response()->json(['print_s_count_latest' => $selling_transaction_details]);
    }

    public function edit(Request $request) {
        $selling_transact_details = DB::connection('forex')->table('tblsoldcurrdetails')
            ->join('pawnshop.tblxusers', 'tblsoldcurrdetails.UserID', '=', 'pawnshop.tblxusers.UserID')
            ->join('pawnshop.tblxcustomer', 'tblsoldcurrdetails.CustomerID', '=', 'pawnshop.tblxcustomer.CustomerID')
            ->join('tblcurrency' , 'tblsoldcurrdetails.CurrencyID' , 'tblcurrency.CurrencyID')
            ->select(
                'tblsoldcurrdetails.DateSold',
                'tblsoldcurrdetails.TimeSold',
                'tblsoldcurrdetails.SellingNo',
                'tblsoldcurrdetails.ReceiptNo',
                'tblcurrency.Currency',
                'tblsoldcurrdetails.CurrAmount',
                'tblsoldcurrdetails.RateUsed',
                'tblsoldcurrdetails.AmountPaid',
                'tblsoldcurrdetails.Rset',
                'tblsoldcurrdetails.SCID',
                'pawnshop.tblxusers.SecurityCode',
                'pawnshop.tblxcustomer.FullName',
            )
            ->where('tblsoldcurrdetails.SCID', '=' , $request->id)
            ->get();

        return view('selling_transact.edit_selling_transact')->with('selling_transact_details' , $selling_transact_details);
    }

    public function delete(Request $request) {
        $query = DB::connection('forex')->table('tblsoldserials as ss')
            ->where('ss.SCID', '=' ,  $request->get('trans_id'));

        $FSIDs = $query->clone()->pluck('ss.FSID')
            ->toArray();

        DB::connection('forex')->table('tblforexserials as fs')
            ->when(is_array($FSIDs), function ($query) use ($FSIDs) {
                return $query->whereIn('fs.FSID', $FSIDs);
            }, function ($query) use ($FSIDs) {
                return $query->where('fs.FSID', $FSIDs);
            })->update([
                'Sold' => 0,
                'FSStat' => 1,
            ]);

        DB::connection('forex')->table('tblsoldcurrdetails as sc')
            ->where('sc.SCID', '=' ,  $request->get('trans_id'))
            ->update([
                'Voided' => 1
            ]);

        $query->delete();


        // $get_soldcurr_row = DB::connection('forex')->table('tblsoldcurrdetails')
        //     ->where('tblsoldcurrdetails.SCID', '=' ,  $request->get('trans_id'));

        // $selling_curr_deets_deets = $get_soldcurr_row->get();
        // $modified_date = date('y-m-d h:i:s');

        // $selling_curr_deets_data = array(
        //     'SCID' => $selling_curr_deets_deets[0]->SCID,
        //     'SellingID' => $selling_curr_deets_deets[0]->SellingID,
        //     'CurrencyID' => $selling_curr_deets_deets[0]->CurrencyID,
        //     'CurrAmount' => $selling_curr_deets_deets[0]->CurrAmount,
        //     'RateUsed' => $selling_curr_deets_deets[0]->RateUsed,
        //     'AmountPaid' => $selling_curr_deets_deets[0]->AmountPaid,
        //     'EntryDate' => $selling_curr_deets_deets[0]->EntryDate,
        //     'UserID' => $selling_curr_deets_deets[0]->UserID,
        //     'ModifyDate' => $modified_date,
        //     'ModifyBy' => $selling_curr_deets_deets[0]->UserID,
        //     'CustomerID' => $selling_curr_deets_deets[0]->CustomerID,
        //     'DateSold' => $selling_curr_deets_deets[0]->DateSold,
        //     'TimeSold' => $selling_curr_deets_deets[0]->TimeSold,
        //     'BranchID' => $selling_curr_deets_deets[0]->BranchID,
        //     'SellingNo' => $selling_curr_deets_deets[0]->SellingNo,
        //     'ReceiptNo' => $selling_curr_deets_deets[0]->ReceiptNo,
        //     'Rset' => $selling_curr_deets_deets[0]->Rset,
        // );

        // DB::connection('forex')->table('tbldeletedsoldcurrdetails')
        //     ->insert($selling_curr_deets_data);

        // $get_soldcurr_row->delete();

        $message = "Transaction deleted successfully!";
        return redirect()->back()->with('message' , $message);
    }

    public function details(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $forex_connection = DB::connection('forex');
        $pawnshop_connection = DB::connection('pawnshop');
        $sesh_username = session('user_name');

        $result['soldcurr_details'] = $forex_connection->table('tblsoldcurrdetails')
            ->leftJoin('tblcurrency' , 'tblsoldcurrdetails.CurrencyID' , 'tblcurrency.CurrencyID')
            ->leftJoin('pawnshop.tblxusers', 'tblsoldcurrdetails.UserID', '=', 'pawnshop.tblxusers.UserID')
            ->leftJoin('pawnshop.tblxcustomer', 'tblsoldcurrdetails.CustomerID', '=', 'pawnshop.tblxcustomer.CustomerID')
            ->select('tblsoldcurrdetails.CompanyID', 'tblsoldcurrdetails.SCID', 'tblsoldcurrdetails.SellingID', 'tblcurrency.Currency', 'tblcurrency.CurrAbbv', 'tblsoldcurrdetails.CurrAmount', 'tblsoldcurrdetails.RateUsed', 'tblsoldcurrdetails.AmountPaid', 'pawnshop.tblxusers.Username', 'tblsoldcurrdetails.DateSold', 'tblsoldcurrdetails.TimeSold', 'tblsoldcurrdetails.SellingNo', 'tblsoldcurrdetails.ReceiptNo', 'tblsoldcurrdetails.Rset', 'tblsoldcurrdetails.ORNo', 'tblsoldcurrdetails.CurrencyID', 'tblsoldcurrdetails.Print', 'tblsoldcurrdetails.CustomerID', 'pawnshop.tblxusers.SecurityCode', 'pawnshop.tblxusers.Name', 'pawnshop.tblxcustomer.FullName')
            ->where('tblsoldcurrdetails.SCID', '=' , $request->id)
            ->get();

        $query = DB::connection('forex')->table('tblsoldserials as ss')
            ->join('tblforexserials as fs' , 'ss.FSID' , '=' , 'fs.FSID')
            ->join('tblsoldcurrdetails as scd' , 'ss.SCID' , '=' , 'scd.SCID')
            ->join('tblcurrency as tc' , 'scd.CurrencyID' , 'tc.CurrencyID');
            
        $result['sold_serial'] = $query->clone()
            ->selectRaw('tc.Currency, scd.Rset, scd.CurrencyID, ss.BillAmount, fs.Serials, fs.FTDID, scd.SCID, scd.DateSold, scd.TimeSold, scd.RateUsed, ss.RIBRate')
            ->groupBy('tc.Currency', 'scd.Rset', 'scd.CurrencyID', 'ss.BillAmount', 'fs.Serials', 'fs.FTDID', 'scd.SCID', 'scd.DateSold', 'scd.TimeSold', 'scd.RateUsed', 'ss.RIBRate')
            ->where('ss.SCID', '=' , $request->id)
            ->get();

        $result['denom_details'] = $query->clone()
            ->selectRaw('ss.BillAmount, COUNT(ss.BillAmount) as bill_count, SUM(ss.BillAmount) as sub_total')
            ->groupBy('ss.BillAmount')
            ->where('ss.SCID', '=' , $request->id)
            ->get();

        $sc_rate = $query->clone()
            ->selectRaw('ss.RIBRate')
            ->groupBy('ss.RIBRate')
            ->where('ss.SCID', '=' , $request->id)
            ->value('ss.RIBRate');

        $result['available_bills'] = DB::connection('forex')->table('tblforextransactiondetails')
            ->join('tblforexserials', 'tblforextransactiondetails.FTDID', '=', 'tblforexserials.FTDID')
            ->join('tblcurrency', 'tblforextransactiondetails.CurrencyID', '=', 'tblcurrency.CurrencyID')
            ->where('tblforexserials.FSType' , '=' , 1)
            ->where('tblforexserials.FSStat' , '=' , 1)
            ->where('tblforexserials.Sold' , '=' , 0)
            ->where('tblforexserials.Serials', '!=', null)
            ->where('tblcurrency.CurrencyID', '=', $result['soldcurr_details'][0]->CurrencyID)
            ->orderBy('tblforexserials.BillAmount', 'ASC')
            ->get();

        return view('selling_transact.sold_serials', compact('result', 'menu_id', 'sc_rate'));
    }

    public function update(Request $request) {
        // $receipt_set = $request->input('radio-rset') == null ? $request->input('sold-currency-rset') : $request->input('radio-rset');
        $receipt_set = $request->input('radio-rset') == null ? 'O' : $request->input('radio-rset');
        $or_number = $request->input('or-number-selling') == null && $receipt_set == 'B' ? null : $request->input('or-number-selling');
        $customer_id = $request->input('customer-id-selected') == null ? $request->input('transact-customer-id') : $request->input('customer-id-selected');
        $rate_used = $request->input('sold-currency-rate-used');
        $total_amount = $request->input('true-sold-currency-total-amnt');

        $data_updated = array(
            'Rset' =>  $receipt_set,
            'ORNo' => $or_number,
            'CustomerID' => $customer_id,
            'RateUsed' => $rate_used,
            'AmountPaid' => $total_amount,
        );

        $validator = Validator::make($request->all(), [
            'radio-rset' => 'nullable',
			'or-number-selling' => 'nullable',
			'customer-id-selected' => 'nullable',
        ]);

        if ($validator->fails()) {
			return redirect()->back()->withErrors($validator);
		} else {
			DB::connection('forex')->table('tblsoldcurrdetails')
                ->where('tblsoldcurrdetails.SCID', '=' , $request->get('trans_id'))
                ->update($data_updated);
		}
    }
}
