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
            ->leftJoin('tbldpotype as dt', 'dpc.DPOTID', 'dt.DPOTID')
            ->selectRaw('dpc.DPOCID, dpc.DPOCNo, dt.DPOType, dt.Type, dpc.DollarIn, dpc.DollarOut, tc.CompanyName')
            ->groupBy('dpc.DPOCID', 'dpc.DPOCNo', 'dt.DPOType', 'dt.Type', 'dpc.DollarIn', 'dpc.DollarOut', 'tc.CompanyName');

        $result['dpo_in'] = $query->clone()->where('dt.Type', 1)
            ->orderBy('dpc.DPOCID', 'DESC')
            ->paginate(10, ['*'], 'dpo_in');

        $result['dpo_out'] = $query->clone()->where('dt.Type', 2)
            ->orderBy('dpc.DPOCID', 'DESC')
            ->paginate(10, ['*'], 'dpo_out');

        $result['dollar_in'] = $query->clone()->selectRaw('SUM(dpc.DollarIn) as total_dollar_in')
            ->where('dpc.EntryDate', '>',  '2025-01-01')
            ->pluck('total_dollar_in')
            ->toArray();
            
        $result['dollar_out'] = $query->clone()->selectRaw('SUM(dpc.DollarOut) as total_dollar_out')
            ->where('dpc.EntryDate', '>',  '2025-01-01')
            ->pluck('total_dollar_out')
            ->toArray();

        $result['current_balance'] = DB::connection('forex')->table('tbldpofxcontrol as dpc')
            ->selectRaw('SUM(DollarIn - DollarOut) as Balance')
            ->first();

        return view('DPOFX.dpofx_control', compact('result'));
    }

    public function showIn(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['dpo_ins'] = DB::connection('forex')->table('tbldpoindetails as dd')
            ->selectRaw('dd.DPDID, dd.EntryDate, dd.DPDNo, tbx.Name, dd.DollarAmount, dd.Amount')
            ->join('pawnshop.tblxusers as tbx', 'dd.UserID', 'tbx.UserID')
            ->groupBy('dd.DPDID', 'dd.EntryDate', 'dd.DPDNo', 'tbx.Name', 'dd.DollarAmount', 'dd.Amount')
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
            ->where('sg.SegmentID', 3)
            ->groupBy('tc.CompanyID', 'tc.CompanyName')
            ->orderBy('tc.CompanyID', 'ASC')
            ->get();

        return view('DPOFX.dpo_in_transact', compact('result', 'menu_id'));
    }

    public function DPOFXS(Request $request) {
        $DPO_transacts = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->selectRaw('tb.BranchCode, tc.CompanyID, tc.CompanyName, fd.FTDID, fd.CurrencyAmount, fd.Amount, fd.MTCN, fd.TransactionDate, td.SinagRateBuying, fd.Rset')
            ->join('tblforexserials as fs', 'fd.FTDID', 'fs.FTDID')
            ->join('tbldenom as td', 'fs.DenomID', 'td.DenomID')
            ->join('tblbranch as tb', 'fd.BranchID', 'tb.BranchID')
            ->join('pawnshop.tblxbranch as tbxb', 'tb.BranchCode', 'tbxb.BranchCode')
            ->join('accounting.tblsegmentgroup as sgt', 'tb.BranchID', 'sgt.BranchID')
            ->join('accounting.tblcompany as tc', 'sgt.CompanyID', 'tc.CompanyID')
            ->join('accounting.tblsegments as sgg', 'sgt.SegmentID', 'sgg.SegmentID')
            ->whereNull('fd.DPDID')
            ->where('fd.TransType', '=', 4)
            ->where('sgg.SegmentID', '=', 3)
            // ->where('fd.CompanyID', '=', $request->get('company_id'))
            ->when(is_null($request->get('date_to')), function ($query) use ($request) {
                return $query->where('fd.TransactionDate', '=', $request->get('date_from'));
            },function ($query) use ($request) {
                return $query->whereBetween('fd.TransactionDate', [$request->get('date_from'), $request->get('date_to')]);
            })
            ->orderBy('fd.TransactionDate', 'ASC')
            ->groupBy('tb.BranchCode', 'tc.CompanyID', 'tc.CompanyName', 'fd.FTDID', 'fd.CurrencyAmount', 'fd.Amount', 'fd.MTCN', 'fd.TransactionDate', 'td.SinagRateBuying', 'fd.Rset')
            ->get();

        $reponse = [
            'DPO_transacts' => $DPO_transacts
        ];

        return response()->json($reponse);
    }

    public function saveIn(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $receipt_set_array = $request->input('receipt-set');

        $FTDIDs = array_map('trim', explode(",", trim($request->input('FTDIDs'))));
        $DPOs = array_map('trim', explode(',', trim($request->input('total_dpo_amnt'))));
        $peso_amount = array_map('trim', explode(',', trim($request->input('total_peso_amnt'))));

        $get_dpoc_no = DB::connection('forex')->table('tbldpofxcontrol')
            ->selectRaw('CASE WHEN MAX(DPOCNo) IS NULL THEN 1 ELSE MAX(DPOCNo) + 1 END AS latestDPOControlNo')
            ->value('latestDPOControlNo');

        $query = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->join('tblforexserials as fs', 'fd.FTDID', 'fs.FTDID')
            ->join('tbldenom', 'fs.DenomID', 'tbldenom.DenomID')
            ->when(is_array($FTDIDs), function ($query) use ($FTDIDs) {
                return $query->whereIn('fd.FTDID', $FTDIDs);
            }, function ($query) use ($FTDIDs) {
                return $query->where('fd.FTDID', $FTDIDs);
            });

        $get_dpos = $query->clone()
            ->get();

        $get_dpd_no = DB::connection('forex')->table('tbldpoindetails')
            ->selectRaw('CASE WHEN MAX(DPDNo) IS NULL THEN 1 ELSE MAX(DPDNo) + 1 END AS latestDPDNo')
            ->value('latestDPDNo');

        $insert_dpo_deets_id = DB::connection('forex')->table('tbldpoindetails')
            ->insertGetId([
                'DPDNo' => $get_dpd_no,
                'DollarAmount' => array_sum($DPOs),
                'Amount' => array_sum($peso_amount),
                'UserID' => $request->input('matched_user_id'),
            ]);

        $upcoming_bal = 0;

        foreach ($get_dpos as $key => $dpo_details) {
            $raw_balance = DB::connection('forex')->table('tbldpofxcontrol')->selectRaw('Balance')
                ->where('DPOCNo', DB::raw("(SELECT MAX(DPOCNo) FROM tbldpofxcontrol)"))
                ->value('Balance');

            $current_balance = $raw_balance == null ? 0 : $raw_balance;

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

            DB::connection('forex')->table('tbldpofxcontrol')
                ->insert([
                    'DPOCNo' => $get_dpoc_no++,
                    'DPOTID' => 1,
                    'DPOCType' => 1,
                    'DollarIn' => $dpo_details->CurrencyAmount,
                    'Balance' => floatval($current_balance) + floatval($dpo_details->CurrencyAmount),
                    'CompanyID' => $dpo_details->CompanyID,
                    'UserID' => $request->input('matched_user_id'),
                    'EntryDate' => $raw_date->toDateString(),
                ]);
        }

        $query->clone()->update([
            'DPDID' => $insert_dpo_deets_id
        ]);
    }

    public function inDetails(Request $request) {
        $dpo_in_details = DB::connection('forex')->table('tbldpoin as di')
            ->select('tc.CompanyName', 'tbx.BranchCode', 'di.MTCN', 'di.DollarAmount', 'di.RateUsed', 'di.Amount', 'di.Rset', 'di.EntryDate')
            ->join('tblbranch as tb', 'di.BranchID', 'tb.BranchID')
            ->join('pawnshop.tblxbranch as tbx', 'tb.BranchCode', 'tbx.BranchCode')
            ->join('accounting.tblcompany as tc', 'di.CompanyID', 'tc.CompanyID')
            ->where('di.DPDID', $request->get('DPDID'))
            ->get();

        $response = [
            'dpo_in_details' => $dpo_in_details
        ];

        return response()->json($response);
    }

    public function showOut(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['dpo_out'] = DB::connection('forex')->table('tbldpooutdetails as did')
            ->select('did.DPODOID', 'did.DPOSellingNo', 'tcx.FullName', 'did.DollarAmount', 'did.SellingRate', 'did.Principal', 'did.ExchangeAmount', 'did.GainLoss', 'tbx.Name', 'did.EntryDate', 'did.Remarks')
            ->join('pawnshop.tblxcustomer as tcx', 'did.CustomerID', 'tcx.CustomerID')
            ->join('pawnshop.tblxusers as tbx', 'did.UserID', 'tbx.UserID')
            ->orderBy('did.DPODOID', 'DESC')
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

        $DPO_in_transacts = DB::connection('forex')->table('tbldpoin as di')
            ->selectRaw('di.DPOIID, tc.CompanyName, di.MTCN, di.DollarAmount, di.RateUsed, di.Amount')
            ->selectRaw('SUM(di.DollarAmount) * ? as exchange_amount', [$selling_rate])
            ->selectRaw('(SUM(di.DollarAmount) * ?) - SUM(di.Amount) as gain_loss', [$selling_rate])
            ->join('tbldpoindetails as did', 'di.DPDID', 'did.DPDID')
            ->join('tblbranch as tb', 'di.BranchID', 'tb.BranchID')
            ->join('pawnshop.tblxbranch as tbx', 'tb.BranchCode', 'tbx.BranchCode')
            ->join('accounting.tblsegmentgroup as sgt', 'tbx.BranchID', 'sgt.BranchID')
            ->join('accounting.tblcompany as tc', 'sgt.CompanyID', 'tc.CompanyID')
            ->join('accounting.tblsegments as sgg', 'sgt.SegmentID', 'sgg.SegmentID')
            ->where('di.Sold', 0)
            ->where('sgg.SegmentID', '=', 3)
            ->where('di.Rset', $request->get('receipt_set'))
            ->groupBy('di.DPOIID', 'tc.CompanyName', 'di.MTCN', 'di.DollarAmount', 'di.RateUsed', 'di.Amount')
            ->get();

        $response = [
            'DPO_in_transacts' => $DPO_in_transacts
        ];

        return response()->json($response);
    }

    public function save(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        $max_dpo_out_no = DB::connection('forex')->table('tbldpooutdetails')
            ->selectRaw('CASE WHEN MAX(DPOSellingNo) IS NULL THEN 1 ELSE MAX(DPOSellingNo) + 1 END AS max_dpo_out_no')
            ->value('max_dpo_out_no');

        // $get_latest_fc_series = DB::connection('forex')->table('tblfcformseries as fc')
        //     ->where('fc.CompanyID', 1)
        //     ->where('fc.RSet', $request->get('recept_set'))
        //     ->selectRaw('CASE WHEN MAX(FormSeries) IS NULL THEN 1 ELSE MAX(FormSeries) + 1 END AS FCSeries')
        //     ->value('FCSeries');

        $get_dpodoid = DB::connection('forex')->table('tbldpooutdetails')
            ->insertGetId([
                'DPOSellingNo' => $max_dpo_out_no,
                'CustomerID' => $request->input('customer-id-selected'),
                'DollarAmount' => $request->get('dollar_amnt'),
                'SellingRate' => $request->get('selling_rate'),
                'Principal' => $request->get('amount'),
                'ExchangeAmount' => $request->get('exch_amnt'),
                'GainLoss' => $request->get('gain_loss'),
                'Rset' => $request->get('recept_set'),
                'Remarks' => $request->get('remarks'),
                'FormSeries' => DB::raw("(SELECT MAX(DPOCNo) FROM tbldpofxcontrol)"),
                'UserID' => $request->input('matched_user_id'),
                'EntryDate' => $raw_date->toDateTimeString(),
            ]);

        $max_dpoc_no = DB::connection('forex')->table('tbldpofxcontrol')
            ->selectRaw('CASE WHEN MAX(DPOCNo) IS NULL THEN 1 ELSE MAX(DPOCNo) + 1 END AS latestDPOControlNo')
            ->value('latestDPOControlNo');

        // $current_balance = DB::connection('forex')->table('tbldpofxcontrol as dc')
        //     ->selectRaw('Balance')
        //     ->where('DPOCNo', DB::raw("(SELECT MAX(DPOCNo) FROM tbldpofxcontrol)"))
        //     ->value('Balance');

        // $new_balance = $current_balance == null ? $request->get('dollar_amnt') : floatval($current_balance) - floatval($request->get('dollar_amnt'));

        // DB::connection('forex')->table('tbldpofxcontrol')
        //     ->insert([
        //         'DPOCNo' => $max_dpoc_no,
        //         'DPOTID' => 2,
        //         'DPOCType' => 2,
        //         'DollarOut' => $request->get('dollar_amnt'),
        //         'Balance' => $new_balance,
        //         'UserID' => $request->input('matched_user_id'),
        //         'EntryDate' => $raw_date->toDateString()
        //     ]);

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
                'FormSeries' => DB::raw("(SELECT MAX(DPOCNo) FROM tbldpofxcontrol)")
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
