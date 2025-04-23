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

class AdminDPOFXController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:DPOFX IN,VIEW')->only(['showIn', 'inDetails']);
        $this->middleware('check.access.permission:DPOFX IN,ADD')->only(['addIn', 'saveIn', 'DPOFXS']);

        $this->middleware('check.access.permission:DPOFX OUT,VIEW')->only(['showOut', 'outDetails']);
        $this->middleware('check.access.permission:DPOFX OUT,ADD')->only(['addOut', 'save', 'DPOFXINS']);
        $this->middleware('check.access.permission:DPOFX OUT,EDIT')->only(['print']);
        $this->middleware('check.access.permission:DPOFX OUT,DELETE')->only(['revert']);
        $this->middleware('check.access.permission:DPOFX OUT,PRINT')->only(['print']);

        $this->middleware('check.access.permission:DPOFX CONTROL,VIEW')->only(['wallet']);
    }

    public function wallet(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $query = DB::connection('forex')->table('tbldpofxcontrol as dpc')
            ->join('accounting.tblcompany as tc', 'dpc.CompanyID', 'tc.CompanyID')
            ->join('tbldpotype as dt', 'dpc.DPOTID', 'dt.DPOTID');

        $result['dpo_in'] = $query->clone()->selectRaw('dpc.DPOCID, dpc.DPOCNo, dt.DPOType, dpc.DollarIn, tc.CompanyName')
            ->groupBy('dpc.DPOCID', 'dpc.DPOCNo', 'dt.DPOType', 'dpc.DollarIn', 'tc.CompanyName')
            ->orderBy('dpc.DPOCID', 'DESC')
            ->paginate(10, ['*'], 'dpo_in');

        $result['dpo_out'] = $query->clone()->selectRaw('dpc.DPOCID, dpc.DPOCNo, dt.DPOType, dpc.DollarOut, tc.CompanyName')
            ->groupBy('dpc.DPOCID', 'dpc.DPOCNo', 'dt.DPOType', 'dpc.DollarOut', 'tc.CompanyName')
            ->orderBy('dpc.DPOCID', 'DESC')
            ->paginate(10, ['*'], 'dpo_out');

        $result['dollar_in'] = $query->clone()->selectRaw('SUM(dpc.DollarIn) as total_dollar_in')
            ->where('dpc.EntryDate', '>=',  '2025-01-01')
            ->value('total_dollar_in');
            
        $result['dollar_out'] = $query->clone()->selectRaw('SUM(dpc.DollarOut) as total_dollar_out')
            ->where('dpc.EntryDate', '>=',  '2025-01-01')
            ->value('total_dollar_out');

        $result['current_balance'] = DB::connection('forex')->table('tbldpofxcontrol as dpc')
            ->selectRaw('SUM(DollarIn - DollarOut) as Balance')
            ->first();

        return view('DPOFX.dpofx_control', compact('result'));
    }

    public function showIn(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['dpo_ins'] = DB::connection('forex')->table('tbldpoindetails as dd')
            ->selectRaw('dd.DPDID, dd.EntryDate, dd.DPDNo, tc.CompanyName, tbx.Name, dd.DollarAmount, dd.Amount')
            ->join('pawnshop.tblxusers as tbx', 'dd.UserID', '=', 'tbx.UserID')
            ->join('accounting.tblcompany as tc', 'dd.CompanyID', '=', 'tc.CompanyID')
            ->groupBy('dd.DPDID', 'dd.EntryDate', 'dd.DPDNo', 'tc.CompanyName', 'tbx.Name', 'dd.DollarAmount', 'dd.Amount')
            ->orderBy('dd.DPDID', 'DESC')
            ->paginate(15);

        return view('DPOFX.add_new_dpo_in', compact('result', 'menu_id'));
    }

    public function addIn(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['company'] = DB::connection('forex')->table('tblbranch as tb')
            ->selectRaw('tc.CompanyID, tc.CompanyName')
            ->join('pawnshop.tblxbranch as tbx', 'tb.BranchCode', 'tbx.BranchCode')
            ->join('accounting.tblsegmentgroup as asg', 'tbx.BranchID', 'asg.BranchID')
            ->join('accounting.tblcompany as tc', 'asg.CompanyID', 'tc.CompanyID')
            ->join('accounting.tblsegments as sg', 'asg.SegmentID', 'sg.SegmentID')
            ->where('sg.SegmentID', '=', 3)
            ->groupBy('tc.CompanyID', 'tc.CompanyName')
            ->orderBy('tc.CompanyID', 'ASC')
            ->get();

        return view('DPOFX.dpo_in_transact', compact('result', 'menu_id'));
    }

    public function DPOFXS(Request $request) {
        $DPO_transacts = DB::connection('forex')->table('tblforextransactiondetails')
            ->select('tblbranch.BranchCode', 'accounting.tblcompany.CompanyName', 'tblforextransactiondetails.FTDID', 'tblforextransactiondetails.CurrencyAmount', 'tblforextransactiondetails.Amount', 'tblforextransactiondetails.MTCN', 'tblforextransactiondetails.TransactionDate', 'tbldenom.SinagRateBuying', 'tblforextransactiondetails.Rset')
            ->join('tblforexserials', 'tblforextransactiondetails.FTDID', 'tblforexserials.FTDID')
            ->join('tbldenom', 'tblforexserials.DenomID', 'tbldenom.DenomID')
            ->join('tblbranch', 'tblforextransactiondetails.BranchID', 'tblbranch.BranchID')
            ->join('pawnshop.tblxbranch', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.BranchCode')
            ->join('accounting.tblsegmentgroup', 'pawnshop.tblxbranch.BranchID', 'accounting.tblsegmentgroup.BranchID')
            ->join('accounting.tblcompany', 'accounting.tblsegmentgroup.CompanyID', 'accounting.tblcompany.CompanyID')
            ->join('accounting.tblsegments', 'accounting.tblsegmentgroup.SegmentID', 'accounting.tblsegments.SegmentID')
            ->when(
                is_null($request->get('date_to')),
                function ($query) use ($request) {
                    return $query->where('tblforextransactiondetails.TransactionDate', '=', $request->get('date_from'));
                },
                function ($query) use ($request) {
                    return $query->whereBetween('tblforextransactiondetails.TransactionDate', [$request->get('date_from'), $request->get('date_to')]);
                }
            )
            ->whereNull('tblforextransactiondetails.DPDID')
            ->where('accounting.tblsegments.SegmentID', '=', 3)
            ->where('tblforextransactiondetails.TransType', '=', 4)
            ->where('tblforextransactiondetails.CompanyID', '=', $request->get('company_id'))
            // ->groupBy('accounting.tblcompany.CompanyID')
            ->orderBy('tblforextransactiondetails.TransactionDate', 'ASC')
            // ->orderBy('pawnshop.tblxbranch.BranchID', 'ASC')
            ->get();

        $reponse = [
            'DPO_transacts' => $DPO_transacts
        ];

        return response()->json($reponse);
    }

    public function saveIn(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $receipt_set_array = $request->input('receipt-set');

        $exploded_ftdids = explode(",", trim($request->input('FTDIDs')));
        $trimmed_ftdids = array_map('trim', $exploded_ftdids);

        $get_dpoc_no = DB::connection('forex')->table('tbldpofxcontrol')
            ->selectRaw('CASE WHEN MAX(DPOCNo) IS NULL THEN 1 ELSE MAX(DPOCNo) + 1 END AS latestDPOControlNo')
            ->value('latestDPOControlNo');

        $current_balance = DB::connection('forex')->table('tbldpofxcontrol')
            ->select('Balance')
            ->where('DPOCNo', DB::raw("(SELECT MAX(DPOCNo) FROM tbldpofxcontrol)"))
            ->value('Balance');

        $new_balance = $current_balance == null ? $request->get('total_dpo_amnt') : floatval($current_balance) + floatval($request->get('total_dpo_amnt'));

        DB::connection('forex')->table('tbldpofxcontrol')
            ->insert([
                'DPOCNo' => $get_dpoc_no,
                'DPOTID' => 1,
                'DPOCType' => 1,
                'DollarIn' => $request->get('total_dpo_amnt'),
                'Balance' => $new_balance,
                'CompanyID' => $request->input('select-company'),
                'UserID' => $request->input('matched_user_id'),
                'EntryDate' => $raw_date->toDateString(),
            ]);

        $dpofx_transacts = DB::connection('forex')->table('tblforextransactiondetails')
            ->join('tblforexserials', 'tblforextransactiondetails.FTDID', 'tblforexserials.FTDID')
            ->join('tbldenom', 'tblforexserials.DenomID', 'tbldenom.DenomID')
            ->when(is_array($trimmed_ftdids), function ($query) use ($trimmed_ftdids) {
                return $query->whereIn('tblforextransactiondetails.FTDID', $trimmed_ftdids);
            }, function ($query) use ($trimmed_ftdids) {
                return $query->where('tblforextransactiondetails.FTDID', $trimmed_ftdids);
            });

        $get_dpos = $dpofx_transacts->get();

        $get_dpd_no = DB::connection('forex')->table('tbldpoindetails')
            ->selectRaw('CASE WHEN MAX(DPDNo) IS NULL THEN 1 ELSE MAX(DPDNo) + 1 END AS latestDPDNo')
            ->value('latestDPDNo');

        $insert_dpo_deets_id = DB::connection('forex')->table('tbldpoindetails')
            ->insertGetId([
                'DPDNo' => $get_dpd_no,
                'DollarAmount' => $request->get('total_dpo_amnt'),
                'Amount' => $request->get('total_peso_amnt'),
                'UserID' => $request->input('matched_user_id'),
                'CompanyID' => $request->input('select-company'),
            ]);

        foreach ($get_dpos as $key => $dpo_details) {
            DB::connection('forex')->table('tbldpoin')
                ->insert([
                    'DPDID' => $insert_dpo_deets_id,
                    'FTDID' => $dpo_details->FTDID,
                    'BranchID' => $dpo_details->BranchID,
                    'CompanyID' => $dpo_details->CompanyID,
                    'MTCN' => $dpo_details->MTCN,
                    'DollarAmount' => $dpo_details->CurrencyAmount,
                    'RateUsed' => $dpo_details->SinagRateBuying,
                    'Amount' => $dpo_details->Amount,
                    'UserID' => $dpo_details->UserID,
                    'Rset' => $receipt_set_array[$key],
                    'Inserted' => 1,
                    'EntryDate' => $raw_date->toDateString(),
                    'EntryTime' => $raw_date->toTimeString(),
                ]);
        }

        $get_dpos = $dpofx_transacts->update([
            'DPDID' => $insert_dpo_deets_id
        ]);

    }

    public function inDetails(Request $request) {
        $dpo_in_details = DB::connection('forex')->table('tbldpoin')
            ->select('pawnshop.tblxbranch.BranchCode', 'tbldpoin.MTCN', 'tbldpoin.DollarAmount', 'tbldpoin.RateUsed', 'tbldpoin.Amount', 'tbldpoin.Rset', 'tbldpoin.EntryDate')
            ->join('tblbranch', 'tbldpoin.BranchID', 'tblbranch.BranchID')
            ->join('pawnshop.tblxbranch', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.BranchCode')
            ->where('tbldpoin.DPDID', $request->get('DPDID'))
            ->get();

        $response = [
            'dpo_in_details' => $dpo_in_details
        ];

        return response()->json($response);
    }

    public function showOut(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['dpo_out'] = DB::connection('forex')->table('tbldpooutdetails')
            ->select('tbldpooutdetails.DPODOID', 'tbldpooutdetails.DPOSellingNo', 'pawnshop.tblxcustomer.FullName', 'tbldpooutdetails.DollarAmount', 'tbldpooutdetails.SellingRate', 'tbldpooutdetails.Principal', 'tbldpooutdetails.ExchangeAmount', 'tbldpooutdetails.GainLoss', 'pawnshop.tblxusers.Name', 'tbldpooutdetails.EntryDate', 'tbldpooutdetails.Remarks')
            ->join('pawnshop.tblxcustomer', 'tbldpooutdetails.CustomerID', 'pawnshop.tblxcustomer.CustomerID')
            ->join('pawnshop.tblxusers', 'tbldpooutdetails.UserID', 'pawnshop.tblxusers.UserID')
            ->orderBy('tbldpooutdetails.DPODOID', 'DESC')
            ->paginate(20);

        return view('DPOFX.add_new_dpo_out', compact('result', 'menu_id'));
    }

    public function addOut(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        return view('DPOFX.dpo_out_transact', compact('menu_id'));
    }

    public function DPOFXINS(Request $request) {
        $selling_rate = floatval($request->get('selling_rate'));

        $DPO_in_transacts = DB::connection('forex')->table('tbldpoin')
            ->selectRaw('tbldpoin.DPOIID, accounting.tblcompany.CompanyName, tbldpoin.MTCN, tbldpoin.DollarAmount, tbldpoin.RateUsed, tbldpoin.Amount')
            ->selectRaw('SUM(tbldpoin.DollarAmount) * ? as exchange_amount', [$selling_rate])
            ->selectRaw('(SUM(tbldpoin.DollarAmount) * ?) - SUM(tbldpoin.Amount) as gain_loss', [$selling_rate])
            ->join('tbldpoindetails', 'tbldpoin.DPDID', 'tbldpoindetails.DPDID')
            ->join('tblbranch', 'tbldpoin.BranchID', 'tblbranch.BranchID')
            ->join('pawnshop.tblxbranch', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.BranchCode')
            ->join('accounting.tblsegmentgroup', 'pawnshop.tblxbranch.BranchID', 'accounting.tblsegmentgroup.BranchID')
            ->join('accounting.tblcompany', 'accounting.tblsegmentgroup.CompanyID', 'accounting.tblcompany.CompanyID')
            ->join('accounting.tblsegments', 'accounting.tblsegmentgroup.SegmentID', 'accounting.tblsegments.SegmentID')
            ->where('tbldpoin.Sold', 0)
            ->where('accounting.tblsegments.SegmentID', '=', 3)
            ->where('tbldpoin.Rset', $request->get('receipt_set'))
            ->groupBy('tbldpoin.DPOIID', 'accounting.tblcompany.CompanyName', 'tbldpoin.MTCN', 'tbldpoin.DollarAmount', 'tbldpoin.RateUsed', 'tbldpoin.Amount')
            ->get();

        $response = [
            'DPO_in_transacts' => $DPO_in_transacts
        ];

        return response()->json($response);
    }

    public function save(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        $get_dpoc_no = DB::connection('forex')->table('tbldpofxcontrol')
            ->selectRaw('CASE WHEN MAX(DPOCNo) IS NULL THEN 1 ELSE MAX(DPOCNo) + 1 END AS latestDPOControlNo')
            ->value('latestDPOControlNo');

        $current_balance = DB::connection('forex')->table('tbldpofxcontrol')
            ->select('Balance')
            ->where('DPOCNo', DB::raw("(SELECT MAX(DPOCNo) FROM tbldpofxcontrol)"))
            ->value('Balance');

        $get_latest_fc_series = DB::connection('forex')->table('tblfcformseries')
            ->where('tblfcformseries.RSet', '=', $request->get('recept_set'))
            ->where('tblfcformseries.CompanyID', '=', 1)
            ->selectRaw('CASE WHEN MAX(FormSeries) IS NULL THEN 1 ELSE MAX(FormSeries) + 1 END AS latestFCSeries')
            ->value('latestFCSeries');

        $new_balance = $current_balance == null ? $request->get('dollar_amnt') : floatval($current_balance) - floatval($request->get('dollar_amnt'));

        DB::connection('forex')->table('tbldpofxcontrol')
            ->insert([
                'DPOCNo' => $get_dpoc_no,
                'DPOTID' => 2,
                'DPOCType' => 2,
                'DollarOut' => $request->get('dollar_amnt'),
                'Balance' => $new_balance,
                'UserID' => $request->input('matched_user_id'),
                'EntryDate' => $raw_date->toDateString()
            ]);

        $get_dpo_selling_no = DB::connection('forex')->table('tbldpooutdetails')
            ->selectRaw('CASE WHEN MAX(DPOSellingNo) IS NULL THEN 1 ELSE MAX(DPOSellingNo) + 1 END AS latestDPOSellingControlNo')
            ->value('latestDPOSellingControlNo');

        $get_dpodoid = DB::connection('forex')->table('tbldpooutdetails')
            ->insertGetId([
                'DPOSellingNo' => $get_dpo_selling_no,
                'CustomerID' => $request->input('customer-id-selected'),
                'DollarAmount' => $request->get('dollar_amnt'),
                'SellingRate' => $request->get('selling_rate'),
                'Principal' => $request->get('amount'),
                'ExchangeAmount' => $request->get('exch_amnt'),
                'GainLoss' => $request->get('gain_loss'),
                'Rset' => $request->get('recept_set'),
                'Remarks' => $request->get('remarks'),
                'FormSeries' => $get_latest_fc_series,
                'UserID' => $request->input('matched_user_id'),
                'EntryDate' => $raw_date->toDateTimeString(),
            ]);

        $exploded_dpoiids = explode(",", trim($request->input('selected_dpoins')));
        $trimmed_dpoiids = array_map('trim', $exploded_dpoiids);

        DB::connection('forex')->table('tbldpoin')
            ->when(is_array($trimmed_dpoiids), function ($query) use ($trimmed_dpoiids) {
                return $query->whereIn('tbldpoin.DPOIID', $trimmed_dpoiids);
            }, function ($query) use ($trimmed_dpoiids) {
                return $query->where('tbldpoin.DPOIID', $trimmed_dpoiids);
            })
            ->update([
                'Sold' => 1
            ]);

        $selling_rate = $request->get('selling_rate');

        $DPO_in_transacts = DB::connection('forex')->table('tbldpoin')
            ->selectRaw('tbldpoin.DPOIID, tbldpoin.CompanyID, tbldpoin.MTCN, tbldpoin.DollarAmount, tbldpoin.RateUsed, tbldpoin.Amount')
            ->selectRaw('ROUND(SUM(tbldpoin.DollarAmount) * ?) as exchange_amount', [$selling_rate])
            ->selectRaw('ROUND((SUM(tbldpoin.DollarAmount) * ?) - SUM(tbldpoin.Amount)) as gain_loss', [$selling_rate])
            ->join('tbldpoindetails', 'tbldpoin.DPDID', 'tbldpoindetails.DPDID')
            ->join('tblbranch', 'tbldpoin.BranchID', 'tblbranch.BranchID')
            ->join('pawnshop.tblxbranch', 'tblbranch.BranchCode', 'pawnshop.tblxbranch.BranchCode')
            ->join('accounting.tblsegmentgroup', 'pawnshop.tblxbranch.BranchID', 'accounting.tblsegmentgroup.BranchID')
            ->join('accounting.tblcompany', 'accounting.tblsegmentgroup.CompanyID', 'accounting.tblcompany.CompanyID')
            ->join('accounting.tblsegments', 'accounting.tblsegmentgroup.SegmentID', 'accounting.tblsegments.SegmentID')
            ->when(is_array($trimmed_dpoiids), function ($query) use ($trimmed_dpoiids) {
                return $query->whereIn('tbldpoin.DPOIID', $trimmed_dpoiids);
            }, function ($query) use ($trimmed_dpoiids) {
                return $query->where('tbldpoin.DPOIID', $trimmed_dpoiids);
            })
            ->groupBy('tbldpoin.DPOIID', 'accounting.tblcompany.CompanyName', 'tbldpoin.MTCN', 'tbldpoin.DollarAmount', 'tbldpoin.RateUsed', 'tbldpoin.Amount')
            ->get();

        foreach ($DPO_in_transacts as $dpo_in_details) {
            DB::connection('forex')->table('tbldpoout')
                ->insert([
                    'DPODOID' => $get_dpodoid,
                    'DPOIID' => $dpo_in_details->DPOIID,
                    'CompanyID'  => $dpo_in_details->CompanyID,
                    'MTCN' => $dpo_in_details->MTCN,
                    'DollarAmount' => $dpo_in_details->DollarAmount,
                    'RateUsed' => $dpo_in_details->RateUsed,
                    'Amount' => $dpo_in_details->Amount,
                    'ExchangeAmount' => $dpo_in_details->exchange_amount,
                    'GainLoss' => $dpo_in_details->gain_loss,
                    'UserID' => $request->input('matched_user_id'),
                    'EntryDate' => $raw_date->toDateString(),
                    'EntryTime' => $raw_date->toTimeString(),
                ]);
        }

        DB::connection('forex')->table('tblfcformseries')
            ->where('tblfcformseries.RSet', '=', $request->get('recept_set'))
            ->where('tblfcformseries.CompanyID', '=', 1)
            ->update([
                'FormSeries' => $get_latest_fc_series
            ]);
    }

    public function outDetails(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['dpo_out'] = DB::connection('forex')->table('tbldpooutdetails')
            ->select('tbldpooutdetails.DPODOID', 'tbldpooutdetails.DPOSellingNo', 'pawnshop.tblxcustomer.FullName', 'tbldpooutdetails.DollarAmount', 'tbldpooutdetails.SellingRate', 'tbldpooutdetails.Principal', 'tbldpooutdetails.ExchangeAmount', 'tbldpooutdetails.GainLoss', 'pawnshop.tblxusers.Name', 'tbldpooutdetails.EntryDate', 'tbldpooutdetails.Remarks', 'tbldpooutdetails.Rset', 'tbldpooutdetails.CustomerID')
            ->join('pawnshop.tblxcustomer', 'tbldpooutdetails.CustomerID', 'pawnshop.tblxcustomer.CustomerID')
            ->join('pawnshop.tblxusers', 'tbldpooutdetails.UserID', 'pawnshop.tblxusers.UserID')
            // ->join('tblfcformseries', 'tbldpooutdetails.CompanyID', 'tblfcformseries.CompanyID')
            ->join('accounting.tblcompany', 'tbldpooutdetails.CompanyID', 'accounting.tblcompany.CompanyID')
            ->where('tbldpooutdetails.DPODOID', $request->id)
            // ->where('tblfcformseries.CompanyID', 1)
            // ->where('tblfcformseries.Rset', 'O')
            ->get();

        $result['fc_form_series'] = DB::connection('forex')->table('tblfcformseries')
            ->join('accounting.tblcompany', 'tblfcformseries.CompanyID', 'accounting.tblcompany.CompanyID')
            ->where('tblfcformseries.CompanyID', 1)
            ->where('tblfcformseries.Rset', $result['dpo_out'][0]->Rset)
            ->first();

        return view('DPOFX.dpo_out_details', compact('result', 'menu_id'));
    }

    public function update(Request $request) {
        $customer_id = $request->input('customer-id-selected') == null ? $request->input('transact-customer-id') : $request->input('customer-id-selected');
        $remarks = $request->input('remarks') == null ? null : $request->input('remarks');
        $rate_used = $request->input('selling-rate');
        $exchange_amount = $request->input('true-exchange-amount');
        $gain_loss = $request->input('true-total-gain-loss');

        $data_updated = array(
            'CustomerID' => $customer_id,
            'SellingRate' => $rate_used,
            'ExchangeAmount' => $exchange_amount,
            'GainLoss' => $gain_loss,
            'Remarks' => $remarks,
        );

        $validator = Validator::make($request->all(), [
			'customer-id-selected' => 'nullable',
        ]);

        if ($validator->fails()) {
			return redirect()->back()->withErrors($validator);
		} else {
			DB::connection('forex')->table('tbldpooutdetails')
                ->where('tbldpooutdetails.DPODOID', '=' , $request->get('trans_id'))
                ->update($data_updated);
		}
    }

    public function print(Request $request) {
        $dpo_out = DB::connection('forex')->table('tbldpooutdetails')
            ->select('pawnshop.tblxcustomer.Nameofemployer', 'pawnshop.tblxcustomer.Address2', 'accounting.tblcompany.CompanyName', 'tblfcformseries.FormSeries', 'tbldpooutdetails.DPODOID', 'tbldpooutdetails.DPOSellingNo', 'pawnshop.tblxcustomer.FullName', 'tbldpooutdetails.DollarAmount', 'tbldpooutdetails.SellingRate', 'tbldpooutdetails.Principal', 'tbldpooutdetails.ExchangeAmount', 'tbldpooutdetails.GainLoss', 'pawnshop.tblxusers.Name', 'tbldpooutdetails.EntryDate', 'tbldpooutdetails.Rset')
            ->join('pawnshop.tblxcustomer', 'tbldpooutdetails.CustomerID', 'pawnshop.tblxcustomer.CustomerID')
            ->join('pawnshop.tblxusers', 'tbldpooutdetails.UserID', 'pawnshop.tblxusers.UserID')
            ->join('tblfcformseries', 'tbldpooutdetails.CompanyID', 'tblfcformseries.CompanyID')
            ->join('accounting.tblcompany', 'tbldpooutdetails.CompanyID', 'accounting.tblcompany.CompanyID')
            ->where('tbldpooutdetails.DPODOID', $request->get('DPODOID'))
            // ->where('tblfcformseries.CompanyID', 1)
            ->where('tblfcformseries.Rset', $request->get('receipt_set'))
            ->get();

        if ($request->ajax()) {
            $html = view('DPOFX.dpo_out_details_per_company', ['test' => $dpo_out])->render();
            return response()->json(['html' => $html, 'test' => $dpo_out]);
        }

        return view('DPOFX.dpo_out_details_per_company')->with(['test' => $dpo_out]);

    }
}
