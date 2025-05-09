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
            ->selectRaw('dd.DPDID, dd.EntryDate, dd.DPDNo, tbx.Name, dd.DollarAmount, dd.TotalPrincipalAmount')
            ->join('pawnshop.tblxusers as tbx', 'dd.UserID', 'tbx.UserID')
            ->groupBy('dd.DPDID', 'dd.EntryDate', 'dd.DPDNo', 'tbx.Name', 'dd.DollarAmount', 'dd.TotalPrincipalAmount')
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
            ->selectRaw('tbx.BranchCode, tc.CompanyID, tc.CompanyName, fd.FTDID, fd.CurrencyAmount, fd.Amount, fd.MTCN, fd.TransactionDate, td.SinagRateBuying, fd.Rset')
            ->join('tblforexserials as fs', 'fd.FTDID', 'fs.FTDID')
            ->join('tbldenom as td', 'fs.DenomID', 'td.DenomID')
            ->join('tblbranch as tb', 'fd.BranchID', 'tb.BranchID')
            ->join('pawnshop.tblxbranch as tbx', 'tb.BranchCode', 'pawnshop.tbx.BranchCode')
            ->join('accounting.tblsegmentgroup as sgt', 'pawnshop.tbx.BranchID', 'accounting.sgt.BranchID')
            ->join('accounting.tblcompany as tc', 'accounting.sgt.CompanyID', 'tc.CompanyID')
            ->join('accounting.tblsegments as sgg', 'accounting.sgt.SegmentID', 'accounting.sgg.SegmentID')
            ->whereNull('fd.DPDID')
            ->where('fd.TransType', 4)
            ->where('sgg.SegmentID', 3) 
            ->where('fd.BranchID', '<>', 10)
            ->where('fd.Rset', $request->get('receipt_set'))
            ->when(is_null($request->get('date_to')), function ($query) use ($request) {
                return $query->where('fd.TransactionDate', $request->get('date_from'));
            },function ($query) use ($request) {
                return $query->whereBetween('fd.TransactionDate', [$request->get('date_from'), $request->get('date_to')]);
            })
            ->orderBy('fd.TransactionDate', 'DESC')
            ->groupBy('tbx.BranchCode', 'tc.CompanyID', 'tc.CompanyName', 'fd.FTDID', 'fd.CurrencyAmount', 'fd.Amount', 'fd.MTCN', 'fd.TransactionDate', 'td.SinagRateBuying', 'fd.Rset')
            ->get();

        $response = [
            'DPO_transacts' => $DPO_transacts
        ];

        return response()->json($response);
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
                'TotalPrincipalAmount' => array_sum($peso_amount),
                'UserID' => $request->input('matched_user_id'),
                'Rset' => $request->input('receipt_set'),
                'Remarks' => $request->input('remarks') == null ? null : $request->input('remarks'),
                'DateSold' => $raw_date->toDateString(),
                'TimeSold' => $raw_date->toTimeString(),
            ]);

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
                    'SinagRateBuying' => $dpo_details->SinagRateBuying,
                    'PrincipalAmount' => $dpo_details->Amount,
                    'Rset' => $request->get('receipt_set'),
                    'UserID' => $dpo_details->UserID,
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
            ->selectRaw('tc.CompanyName, tbx.BranchCode, di.MTCN, di.DollarAmount, di.SinagRateBuying, di.PrincipalAmount, di.Rset, di.EntryDate')
            ->join('tblbranch as tb', 'di.BranchID', 'tb.BranchID')
            ->join('pawnshop.tblxbranch as tbx', 'tb.BranchCode', 'tbx.BranchCode')
            ->join('accounting.tblsegmentgroup as sgt', 'tbx.BranchID', 'sgt.BranchID')
            ->join('accounting.tblcompany as tc', 'di.CompanyID', 'tc.CompanyID')
            ->join('accounting.tblsegments as sgg', 'sgt.SegmentID', 'sgg.SegmentID')
            ->where('sgg.SegmentID', 3)
            ->where('di.DPDID', $request->get('DPDID'))
            ->groupBy('tc.CompanyName', 'tbx.BranchCode', 'di.MTCN', 'di.DollarAmount', 'di.SinagRateBuying', 'di.PrincipalAmount', 'di.Rset', 'di.EntryDate')
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
            ->select('did.DPODOID', 'did.DPOSellingNo', 'tcx.FullName', 'did.DollarAmount', 'did.TotalPrincipal', 'did.TotalExchangeAmount', 'did.TotalGainLoss', 'tbx.Name', 'did.TransactionDate', 'did.Remarks')
            ->join('pawnshop.tblxcustomer as tcx', 'did.CustomerID', 'tcx.CustomerID')
            ->join('pawnshop.tblxusers as tbx', 'did.UserID', 'tbx.UserID')
            ->orderBy('did.DPODOID', 'DESC')
            ->paginate(20);

        return view('DPOFX.add_new_dpo_out', compact('result', 'menu_id'));
    }

    public function addOut(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $customerid = $request->query('customerid');

        $result = '';

        if ($customerid) $result = CustomerManagement::customerInfo($customerid);

        return view('DPOFX.dpo_out_transact', compact('result', 'menu_id'));
    }

    public function DPOFXINS(Request $request) {
        $selling_rate = floatval($request->get('selling_rate'));

        $results = DB::connection('forex')->table('tbldpoin as di')
            ->selectRaw('di.DPOIID, tc.CompanyName, di.MTCN, di.DollarAmount, di.SinagRateBuying, di.PrincipalAmount')
            ->selectRaw('SUM(di.DollarAmount) * ? as exchange_amount', [$selling_rate])
            ->selectRaw('(SUM(di.DollarAmount) * ?) - SUM(di.PrincipalAmount) as gain_loss', [$selling_rate])
            ->join('tbldpoindetails as did', 'di.DPDID', 'did.DPDID')
            ->join('tblbranch as tb', 'di.BranchID', 'tb.BranchID')
            ->join('pawnshop.tblxbranch as tbx', 'tb.BranchCode', 'tbx.BranchCode')
            ->join('accounting.tblsegmentgroup as sgt', 'tbx.BranchID', 'sgt.BranchID')
            ->join('accounting.tblcompany as tc', 'sgt.CompanyID', 'tc.CompanyID')
            ->join('accounting.tblsegments as sgg', 'sgt.SegmentID', 'sgg.SegmentID')
            ->where('di.Sold', 0)
            ->where('di.Inserted', 1)
            ->where('sgg.SegmentID', '=', 3)
            ->where('di.Rset', $request->get('receipt_set'))
            ->groupBy('di.DPOIID', 'tc.CompanyName', 'di.MTCN', 'di.DollarAmount', 'di.SinagRateBuying', 'di.PrincipalAmount')
            ->get();

        $response = [
            'DPO_in_transacts' => $results
        ];

        return response()->json($response);
    }

    public function save(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');

        $max_dpo_out_no = DB::connection('forex')->table('tbldpooutdetails')
            ->selectRaw('CASE WHEN MAX(DPOSellingNo) IS NULL THEN 1 ELSE MAX(DPOSellingNo) + 1 END AS max_dpo_out_no')
            ->value('max_dpo_out_no');

        $DPODOID = DB::connection('forex')->table('tbldpooutdetails')
            ->insertGetId([
                'DPOSellingNo' => $max_dpo_out_no,
                'DollarAmount' => $request->get('dollar_amnt'),
                'TotalExchangeAmount' => $request->get('exch_amnt'),
                'TotalPrincipal' => $request->get('amount'),
                'TotalGainLoss' => $request->get('gain_loss'),
                'UserID' => $request->input('matched_user_id'),
                'CustomerID' => $request->input('customer-id-selected'),
                'TransactionDate' => $raw_date->toDateTimeString(),
                'Rset' => $request->get('receipt_set'),
                'Remarks' => $request->get('remarks'),
                'DateSold' => $raw_date->toDateString(),
                'TimeSold' => $raw_date->toTimeString(),
            ]);

        $max_dpoc_no = DB::connection('forex')->table('tbldpofxcontrol')
            ->selectRaw('CASE WHEN MAX(DPOCNo) IS NULL THEN 1 ELSE MAX(DPOCNo) + 1 END AS latestDPOControlNo')
            ->value('latestDPOControlNo');

        $trimmed_dpoiids = array_map('trim', explode(",", trim($request->input('selected_dpoins'))));

        $query = DB::connection('forex')->table('tbldpoin as di')
            ->join('tblbranch', 'di.BranchID', 'tblbranch.BranchID')
            ->join('pawnshop.tblxbranch as tbx', 'tblbranch.BranchCode', 'tbx.BranchCode')
            ->join('accounting.tblsegmentgroup as sgt', 'tbx.BranchID', 'sgt.BranchID')
            ->join('accounting.tblcompany as tc', 'sgt.CompanyID', 'tc.CompanyID')
            ->join('accounting.tblsegments as sgg', 'sgt.SegmentID', 'sgg.SegmentID')
            ->where('di.Sold', 0)
            ->where('di.Inserted', 1)
            ->where('sgg.SegmentID', 3)
            ->where('di.Rset', $request->get('receipt_set'))
            ->when(is_array($trimmed_dpoiids), function ($query) use ($trimmed_dpoiids) {
                return $query->whereIn('di.DPOIID', $trimmed_dpoiids);
            }, function ($query) use ($trimmed_dpoiids) {
                return $query->where('di.DPOIID', $trimmed_dpoiids);
            });

        $DPO_in_transacts = $query->clone()
            ->selectRaw('di.DPOIID, di.CompanyID, di.MTCN, di.DollarAmount, di.SinagRateBuying, di.PrincipalAmount, tbx.BranchID')
            ->selectRaw('ROUND(SUM(di.DollarAmount) * ?) as exchange_amount', [$request->get('selling_rate')])
            ->selectRaw('ROUND((SUM(di.DollarAmount) * ?) - SUM(di.PrincipalAmount)) as gain_loss', [$request->get('selling_rate')])
            ->groupBy('di.DPOIID', 'tc.CompanyName', 'di.MTCN', 'di.DollarAmount', 'di.SinagRateBuying', 'di.PrincipalAmount', 'tbx.BranchID')
            ->get();

        $latest_series = DB::connection('forex')->table('tblfcformseries as fc')
            ->where('fc.RSet', $request->input('radio-rset'))
            ->where('fc.CompanyID', 1)
            ->selectRaw('MAX(FormSeries) + 1 AS latest_series')
            ->value('latest_series');

        $get_tc_no = DB::connection('forex')->table('tblfxtranscap')
            ->selectRaw('CASE WHEN MAX(TCNo) IS NULL THEN 1 ELSE MAX(TCNo) + 1 END AS max_tc_no')
            ->value('max_tc_no');

        foreach ($DPO_in_transacts as $key => $value) {
            $raw_balance = DB::connection('forex')->table('tbldpofxcontrol')->selectRaw('Balance')
                ->where('DPOCNo', DB::raw("(SELECT MAX(DPOCNo) FROM tbldpofxcontrol)"))
                ->value('Balance');

            $current_balance = $raw_balance == null ? 0 : $raw_balance;

            DB::connection('forex')->table('tbldpoout')
                ->insert([
                    'DPODOID' => $DPODOID,
                    'DPOIID' => $value->DPOIID,
                    'CompanyID'  => 1,
                    // 'CompanyID'  => $value->CompanyID,
                    'MTCN' => $value->MTCN,
                    'DollarAmount' => $value->DollarAmount,
                    'SinagRateBuying' => $value->SinagRateBuying,
                    'CMRUsed' => $request->get('selling_rate'),
                    'PrincipalAmount' => $value->PrincipalAmount,
                    'ExchangeAmount' => $value->exchange_amount,
                    'GainLoss' => $value->gain_loss,
                    'UserID' => $request->input('matched_user_id'),
                    'FormSeries' => $latest_series,
                    'EntryDate' => $raw_date->toDateString(),
                    'EntryTime' => $raw_date->toTimeString(),
                ]);

            DB::connection('forex')->table('tbldpofxcontrol')
                ->insert([
                    'DPOCNo' => $max_dpoc_no++,
                    'DPOTID' => 2,
                    'DPOCType' => 2,
                    'DollarOut' => $value->DollarAmount,
                    'Balance' =>   floatval($current_balance) - floatval($value->DollarAmount),
                    'CompanyID' => $value->CompanyID,
                    'UserID' => $request->input('matched_user_id'),
                    'EntryDate' => $raw_date->toDateString()
                ]);

            DB::connection('forex')->table('tblfxtranscap')->insert([
                'Transferred' => 1,
                'DPODOID' => $DPODOID,
                'TCNo' => $get_tc_no++,
                'BranchID' => $value->BranchID,
                'TranscapAmount' => $value->exchange_amount,
                'UserID' => $request->input('matched_user_id')
            ]);
        }

        DB::connection('forex')->table('tblfcformseries')
            ->where('tblfcformseries.CompanyID', 1)
            ->where('tblfcformseries.RSet', $request->get('receipt_set'))
            ->update([
                'FormSeries' => $latest_series + 1
            ]);
            
        $query->clone()
            ->update([
                'Sold' => 1
            ]);

        $trans_cap_details = DB::connection('forex')->table('tblfxtranscap as tc')
            ->selectRaw('tc.DPODOID, tc.TCID, tc.TranscapAmount, tc.BranchID')
            ->where('tc.DPODOID', $DPODOID)
            ->groupBy('tc.DPODOID', 'tc.TCID', 'tc.TranscapAmount', 'tc.BranchID')
            ->get();

        $get_tr_no = DB::connection('generaldcpr')->table('tbltransaddcap')
            ->selectRaw('CASE WHEN MAX(trno) IS NULL THEN 1 ELSE MAX(trno) + 1 END AS max_tr_no')
            ->value('max_tr_no');

        foreach ($trans_cap_details as $details) {
            DB::connection('generaldcpr')->table('tbltransaddcap')
                ->insert([
                    'tentrydate' => $raw_date->toDateString(),
                    'tentrytime' => $raw_date->toTimeString(),
                    'trno' => $get_tr_no++,
                    'trdate' => $raw_date->toDateString(),
                    'tramount' => $details->TranscapAmount,
                    'tobranchid' => $details->BranchID,
                    'tremarks' => 'TRANSCAP - FOREX (DPOFX)',
                    'tbranchid' => 1,
                    'tappraiserid' => Auth::user()->UserID,
                    'tuserid' => $request->input('matched_user_id'),
                    'DPODOID' => $DPODOID,
                    'TCID' => $details->TCID,
                    'SegmentID' => 3,
                ]);
        }
    }

    public function outDetails(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $query = DB::connection('forex')->table('tbldpooutdetails as dod')
            ->join('tbldpoout as dop', 'dod.DPODOID', 'dop.DPODOID')
            ->join('tblcurrency as tcr', 'dop.CurrencyID', 'tcr.CurrencyID')
            ->join('pawnshop.tblxcustomer as tc', 'dod.CustomerID', 'tc.CustomerID')
            ->join('pawnshop.tblxusers as tbx', 'dod.UserID', 'tbx.UserID')
            ->where('dod.DPODOID', $request->id);

        $result['dpo_out'] = $query->clone()
            ->selectRaw('dod.DPODOID, tc.CompanyID, tc.CompanyName, dop.FormSeries')
            ->join('accounting.tblcompany as tc', 'dop.CompanyID', 'tc.CompanyID')
            ->groupBy('dod.DPODOID', 'tc.CompanyID', 'tc.CompanyName', 'dop.FormSeries')
            ->get();

        $sales = [];

        foreach ($result['dpo_out'] as $index => $dpo_out) {
            $get_currencies_query = $query->clone()
                ->where('dop.CompanyID', $dpo_out->CompanyID)
                ->selectRaw('dop.CurrencyID, tcr.Currency, dop.CMRUsed, SUM(dop.DollarAmount) as total_curr_amount')
                ->groupBy('dop.CurrencyID', 'tcr.Currency', 'dop.CMRUsed')
                ->orderBy('tcr.Currency', 'ASC')
                ->get();

            $currency_ids = [];

            foreach ($get_currencies_query as $get_currency_ids) {
                $currency_ids[] = $get_currency_ids;
            }

            $dpo_out->Currency = $currency_ids;

            $sales[] = $dpo_out;
        }

        $result['dpo_palit_deets'] = $query->clone()
            ->selectRaw('dod.DPODOID, dod.DPOSellingNo, tc.FullName, dod.DollarAmount, dod.TotalPrincipal, dod.TotalExchangeAmount, dod.TotalGainLoss, tbx.Name, dod.DateSold, dod.Remarks, dod.Rset, dod.CustomerID')
            ->groupBy('dod.DPODOID', 'dod.DPOSellingNo', 'tc.FullName', 'dod.DollarAmount', 'dod.TotalPrincipal', 'dod.TotalExchangeAmount', 'dod.TotalGainLoss', 'tbx.Name', 'dod.DateSold', 'dod.Remarks', 'dod.Rset', 'dod.CustomerID')
            ->get();

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
            // 'CMRUsed' => $rate_used,
            // 'TotalExchangeAmount' => $exchange_amount,
            // 'TotalGainLoss' => $gain_loss,
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
        $dpo_out = DB::connection('forex')->table('tbldpooutdetails as dod')
            ->selectRaw('tcx.Nameofemployer, tcx.Address2, tc.CompanyName, fc.FormSeries, dod.DPODOID, dod.DPOSellingNo, tcx.FullName, dod.DollarAmount, dop.CMRUsed, dod.TotalPrincipal, dod.TotalExchangeAmount, dod.TotalGainLoss, tbx.Name, dod.DateSold, dod.Rset')
            ->join('tbldpoout as dop', 'dod.DPODOID', 'dop.DPODOID')
            ->join('pawnshop.tblxcustomer as tcx', 'dod.CustomerID', 'tcx.CustomerID')
            ->join('pawnshop.tblxusers as tbx', 'dod.UserID', 'tbx.UserID')
            ->join('tblfcformseries as fc', 'dop.CompanyID', 'fc.CompanyID')
            ->join('accounting.tblcompany as tc', 'dop.CompanyID', 'tc.CompanyID')
            ->where('dod.DPODOID', $request->get('DPODOID'))
            // ->where('tblfcformseries.CompanyID', 1)
            ->where('fc.Rset', $request->get('receipt_set'))
            ->groupBy('tcx.Nameofemployer', 'tcx.Address2', 'tc.CompanyName', 'fc.FormSeries', 'dod.DPODOID', 'dod.DPOSellingNo', 'tcx.FullName', 'dod.DollarAmount', 'dop.CMRUsed', 'dod.TotalPrincipal', 'dod.TotalExchangeAmount', 'dod.TotalGainLoss', 'tbx.Name', 'dod.DateSold', 'dod.Rset')
            ->get();

        if ($request->ajax()) {
            $html = view('DPOFX.dpo_out_details_per_company', ['test' => $dpo_out])->render();
            return response()->json(['html' => $html, 'test' => $dpo_out]);
        }

        return view('DPOFX.dpo_out_details_per_company')->with(['test' => $dpo_out]);

    }
}
