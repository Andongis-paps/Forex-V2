<?php

namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Validator;
use App;
use Lang;
use App\Admin;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Hash;
use Auth;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Helpers\CreateNotifications;

class TransferForexController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:TRANSFER FOREX,VIEW')->only(['show', 'details', 'detail', 'bufferDetails', 'serials']);
        $this->middleware('check.access.permission:TRANSFER FOREX,ADD')->only(['add', 'save', 'acknowledge', 'validation', 'addTrackingNo']);
        $this->middleware('check.access.permission:TRANSFER FOREX,EDIT')->only(['edit', 'update', 'removeTrackingNo']);
        $this->middleware('check.access.permission:TRANSFER FOREX,DELETE')->only(['delete']);
    }

    protected function adminStocks() {
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

        $admin_stock_details_s = DB::connection('forex')->query()->fromSub($joined_queries, 'combined')
            ->selectRaw('FSID as ID, Rset, CurrencyID, BillAmount, SinagRateBuying, Buffer, Queued, QueuedBy, source_type')
            ->groupBy('ID', 'Rset', 'CurrencyID', 'BillAmount', 'SinagRateBuying', 'Buffer', 'Queued', 'QueuedBy', 'source_type')
            ->where('Buffer', 1)
            ->where('Queued', 0)
            ->get();

        return $admin_stock_details_s;
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $raw_date = Carbon::now('Asia/Manila');
        // CASE
        //     WHEN tracking.tblitemtransfer.RecepReceived = 0 THEN "Tracking created"
        //     WHEN tracking.tblitemtransfer.RecepReceived = 1 AND tracking.tblitemtransfer.BahayReceived = 0 AND tracking.tblitemtransfer.BranchReceived = 0 THEN "Recep. received"
        //     WHEN tracking.tblitemtransfer.BahayReceived = 1 AND tracking.tblitemtransfer.BranchReceived = 0 AND tracking.tblitemtransfer.PUID IS NULL THEN "Bahay received"
        //     WHEN tracking.tblitemtransfer.PUID IS NOT NULL AND tracking.tblitemtransfer.BranchReceived = 0 THEN "On the way"
        //     WHEN tracking.tblitemtransfer.BranchReceived = 1 THEN "Delivered"
        //     ELSE "Burnek"
        // END AS TrackingStatus

        $result['transfer_forex'] = DB::connection('forex')->table('tbltransferforex')
            ->selectRaw('
                tbltransferforex.TransferForexID,
                tbltransferforex.TransferForexNo,
                tbltransferforex.TransferDate,
                tbltransferforex.ITID,
                tbltransferforex.ITNo,
                tbltransferforex.Remarks,
                tblbuffertransfer.BufferTransfer,
                tblbuffertransfer.BufferID,
                tblreceivetransfer.Received,
                tbltransferforex.Voided,
                tbltransferforex.HasTicket,
                tb.BranchCode,
                CASE
                    WHEN tracking.tblitemtransfer.RecepReceived = 0 THEN 1
                    WHEN tracking.tblitemtransfer.RecepReceived = 1 AND tracking.tblitemtransfer.BahayReceived = 0 AND tracking.tblitemtransfer.BranchReceived = 0 THEN 2
                    WHEN tracking.tblitemtransfer.BahayReceived = 1 AND tracking.tblitemtransfer.BranchReceived = 0 AND tracking.tblitemtransfer.PUID IS NULL THEN 3
                    WHEN tracking.tblitemtransfer.PUID IS NOT NULL AND tracking.tblitemtransfer.BranchReceived = 0 THEN 4
                    WHEN tracking.tblitemtransfer.BranchReceived = 1 THEN 5
                    ELSE 0
                END AS TrackingStatus
            ')
            ->leftJoin('tblbuffertransfer', 'tbltransferforex.TransferForexID', '=', 'tblbuffertransfer.TFID')
            ->leftJoin('tblreceivetransfer', 'tbltransferforex.TransferForexID', '=', 'tblreceivetransfer.TFID')
            ->leftJoin('tracking.tblitemtransfer', 'tbltransferforex.ITID', '=', 'tracking.tblitemtransfer.ITID')
            ->join('tblbranch as tb', 'tbltransferforex.BranchID', 'tb.BranchID')
            ->where('tbltransferforex.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->where('tbltransferforex.Remarks', '!=', 'DPOFX')
            // ->where('tbltransferforex.TransferDate', '>', Carbon::now()->subDays(400)->toDateString())
            ->where('tbltransferforex.TransferDate', '>', $raw_date->parse('2025-01-01'))
            // ->where('tblbuffertransfer.BufferTransfer', '=', 0)
            // ->whereNull('tbltransferforex.ITNo') // Corrected the NULL check
            ->whereNull('tblreceivetransfer.Received')
            ->orderBy('tbltransferforex.TransferForexNo', 'DESC')
            ->groupBy(
                'tbltransferforex.TransferForexID',
                'tbltransferforex.TransferForexNo',
                'tbltransferforex.TransferDate',
                'tbltransferforex.ITID',
                'tbltransferforex.ITNo',
                'tbltransferforex.Remarks',
                'tblbuffertransfer.BufferTransfer',
                'tblbuffertransfer.BufferID',
                'tblreceivetransfer.Received',
                'tbltransferforex.Voided',
                'tbltransferforex.HasTicket',
                'tb.BranchCode'
            )
            ->paginate(15);

        $result['ITIDs'] = DB::connection('forex')->table('tbltransferforex')
            ->select('tbltransferforex.ITID')
            ->leftJoin('tblbuffertransfer', 'tbltransferforex.TransferForexID', 'tblbuffertransfer.TFID')
            ->leftJoin('tblreceivetransfer', 'tbltransferforex.TransferForexID', 'tblreceivetransfer.TFID')
            ->where('tbltransferforex.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->where('tbltransferforex.Remarks', '!=', 'BUFFER')
            ->where('tbltransferforex.Remarks', '!=', 'DPOFX')
            // ->where('tblreceivetransfer.Received', '=', null)
            ->whereNotNull('tbltransferforex.ITID')
            ->groupBy('tbltransferforex.ITID')
            ->get();

        $result['tracking_number'] = DB::connection('itinventory')->table('tbldepartment as dp')
            ->select('it.itno as TrackingNumber','it.ITID as TrackingID', 'it.itdate as EntryDate')
            ->join('tracking.tblitemtransfer as it', 'dp.deptid', 'it.deptid')
            ->where('it.DeptID', '=', 5)
            ->where('it.Sender', '=', Auth::user()->getBranch()->pxBranchID)
            ->where('it.itdate', '>=',  DB::raw('DATE_SUB(CURDATE(), INTERVAL 3 DAY)'))
            // ->where('it.itdate', '>=', $raw_date->toDateString())
            // ->where('it.BahayReceived', '=', 0)
            ->orderBy('it.itno', 'DESC')
            ->get();

        $ITDIDs = [];

        foreach ($result['tracking_number'] as $index => $tracking_number) {
            $tracking_numbers = DB::connection('tracking')->table('tblitemtransferdetails')
                ->selectRaw('tblitemtransferdetails.ITID, tblitem.ItemDesc, tblitemtransferdetails.Qty')
                ->join('tblitem', 'tblitemtransferdetails.ItemID', 'tblitem.ItemID')
                ->where('tblitemtransferdetails.ITID', '=', $tracking_number->TrackingID)
                ->whereIn('tracking.tblitemtransferdetails.ItemID', [28, 35, 45, 97])
                ->orderBy('tblitemtransferdetails.ITID', 'ASC')
                ->get();

            $item_desc = [];
            $item_type = [];
            $item_quantity = [];

            foreach ($tracking_numbers as $get_item_desc_id) {
                $item_desc_value = $get_item_desc_id->ItemDesc;

                $item_desc[] = "{$item_desc_value} ({$get_item_desc_id->Qty})";

                if ($item_desc_value === 'FOREX/BUFFER') {
                    $item_desc_value = 'BUFFER';
                } elseif ($item_desc_value === 'FOREX-PALIT') {
                    $item_desc_value = 'BILLS';
                }

                for ($i = 0; $i < $get_item_desc_id->Qty; $i++) {
                    $item_type[] = $item_desc_value;
                }
            }

            $tracking_number->ItemDesc = implode(', ', $item_desc);
            $tracking_number->ItemType = implode(', ', $item_type);

            $ITDIDs[] = $tracking_number;
        }

        return view('transfer_forex.add_new_transfer_forex', compact('result', 'menu_id'));
    }

    public function validation(Request $request) {
        $pending_serials = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->join('tblcurrency as tc', 'fd.CurrencyID', '=', 'tc.CurrencyID')
            ->join('tblforexserials as fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->where('fs.FSStat' , '=' , 1)
            ->where('fs.Sold' , '=' , 0)
            ->where('fs.Serials', '=', null)
            ->where('fd.Voided' , 0)
            ->where('fd.BranchID' , Auth::user()->getBranch()->BranchID)
            ->orderBy('fd.TransactionDate', 'DESC')
            ->get();

        $response = [
            'pending_serials' => $pending_serials,
        ];

        return response()->json($response);
    }

    public function add(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['bills_for_transfer'] = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->join('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', '=', 'tc.CurrencyID')
            ->join('tblforexserials as fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->where('fd.Voided', 0)
            ->where('fs.FSType' , '=' , 1)
            ->where('fs.FSStat' , '=' , 1)
            ->where('fs.TFID', '=', 0)
            ->where('fs.TFUID', '=', 0)
            ->where('fs.Transfer', '=', 0)
            ->where('fs.Sold', '=', 0)
            ->where('fs.Serials', '!=', null)
            ->where('fd.BranchID' , '=' , Auth::user()->getBranch()->BranchID)
            ->orderBy('tc.Currency', 'ASC')
            ->get();

        $result['currency'] = DB::connection('forex')->table('tblcurrency')
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->get();

        $result['courier'] = DB::connection('hris')->table('tblemployee')
            ->where('tblemployee.PositionID', '=', 10)
            ->where('tblemployee.EmpStatus', '=', 1)
            ->orderBy('tblemployee.Name', 'ASC')
            ->get();

        $result['transact_type'] = DB::connection('forex')->table('tbltransactiontype')
            ->where('tbltransactiontype.Active', '!=', 0)
            ->where('tbltransactiontype.TransType', '!=', 'DPOFX')
            ->get();

        return view('transfer_forex.transfer_forex', compact('result', 'menu_id'));
    }

    public function save(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $raw_date = Carbon::now('Asia/Manila');
        $transfer_forex_courier = $request->input('transfer-forex-courier');
        $transfer_forex_currency = $request->input('transfer-forex-currency');
        $transfer_forex_bills = $request->input('transfer-forex-selected-bill');
        $coins = $request->input('radio-transfer-type') == '1' ? 0 : ($request->input('radio-transfer-type') == '3' ? 2 : null);

        $selected_bills_parsed = explode(',' , $transfer_forex_bills);

        // CreateNotifications::CreateBranchNotifications([$branch_id], $menu_id, "Transfer forex created by");

        $get_transfer_forex_no = DB::connection('forex')->table('tbltransferforex')
            ->selectRaw('MAX(TransferForexNo) + 1 AS maxTransferForex')
            ->value('maxTransferForex');

        $result['courier'] = DB::connection('hris')->table('tblemployee')
            ->where('tblemployee.PositionID', '=', 10)
            ->where('tblemployee.EUserID', '=', $transfer_forex_courier)
            ->orderBy('tblemployee.Name', 'ASC')
            ->get();

        DB::connection('forex')->table('tbltransferforex')
            ->insert([
                'TransferForexNo' => $get_transfer_forex_no,
                'TransferDate' => $raw_date->toDateString(),
                'TransferTime' => $raw_date->toTimeString(),
                'BranchID' => Auth::user()->getBranch()->BranchID,
                'Remarks' => $request->input('transfer-forex-remarks'),
                'EntryDate' => $raw_date->toDateTimeString(),
                'UserID' => $request->input('matched_user_id'),
                'Coin' => $coins
                // 'ITID' => $request->get('tracking_id'),
                // 'ITNo' => $request->get('tracking_number'),
            ]);

        $tfid_first = DB::connection('forex')->table('tbltransferforex')
            ->select('tbltransferforex.TransferForexID')
            ->orderBy('tbltransferforex.TransferForexID', 'desc')
            ->first();

        if (count($selected_bills_parsed) == 1) {
            DB::connection('forex')->table('tblforexserials')
                ->where('tblforexserials.FSID', '=', $selected_bills_parsed[0])
                ->update([
                    'Transfer' => 1,
                    'TFID' => $tfid_first->TransferForexID,
                    'TFUID' => $request->input('matched_user_id'),
                    'FSStat' => 2
                ]);

            DB::connection('forex')->table('tbltransferforexdetails')
                ->insert([
                    'TransferForexID' => $tfid_first->TransferForexID,
                    'FSID' => $selected_bills_parsed[0],
                    'UserID' => $request->input('matched_user_id'),
                ]);
        } else {
            foreach($selected_bills_parsed as $key => $fsid_val) {
                DB::connection('forex')->table('tblforexserials')
                    ->where('tblforexserials.FSID', '=', $fsid_val)
                    ->update([
                        'Transfer' => 1,
                        'TFID' => $tfid_first->TransferForexID,
                        'TFUID' => $request->input('matched_user_id'),
                        'FSStat' => 2
                    ]);
            }

            foreach($selected_bills_parsed as $key => $fsid_val) {
                DB::connection('forex')->table('tbltransferforexdetails')
                    ->insert([
                        'TransferForexID' => $tfid_first->TransferForexID,
                        'FSID' => $fsid_val,
                        'UserID' => $request->input('matched_user_id'),
                    ]);
            }
        }

        $latest_tfid = $tfid_first->TransferForexID;

        $message = "Forex successfully transfered!";
        // return redirect()->back()->with(['message' => $message, 'latest_tfid' => $latest_tfid]);
        return response()->json(['message' => 'Transfer Forex Success!', 'latest_tfid' => $latest_tfid]);
    }

    public function detail(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');

        $menu_id = $this->MenuID;

        $raw_date = Carbon::now('Asia/Manila');

        $result['transfer_forex'] = DB::connection('forex')->table('tbltransferforex')
            ->join('tblbranch', 'tbltransferforex.BranchID', '=', 'tblbranch.BranchID')
            ->where('tbltransferforex.TransferForexID', '=', $request->id)
            // ->where('tbltransferforex.TransferDate', '=', $raw_date->toDateString())
            ->get();

        $result['serials_transferred'] = DB::connection('forex')->table('tblforextransactiondetails')
            ->join('tblcurrency', 'tblforextransactiondetails.CurrencyID', '=', 'tblcurrency.CurrencyID')
            ->join('tblforexserials', 'tblforextransactiondetails.FTDID', '=', 'tblforexserials.FTDID')
            ->join('tbltransferforex', 'tblforexserials.TFID', '=', 'tbltransferforex.TransferForexID')
            // ->where('tblforexserials.FSType' , '=' , 1)
            ->where('tblforexserials.FSStat' , '=' , 2)
            ->where('tblforexserials.Transfer' , '=' , 1)
            ->where('tblforexserials.TFID' , '=' , $request->id)
            ->where('tblforextransactiondetails.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->paginate(20);

        $result['serial_count_per_currency'] = DB::connection('forex')->table('tblforextransactiondetails')
            ->join('tblcurrency', 'tblforextransactiondetails.CurrencyID', '=', 'tblcurrency.CurrencyID')
            ->join('tblforexserials', 'tblforextransactiondetails.FTDID', '=', 'tblforexserials.FTDID')
            ->join('tbltransferforex', 'tblforexserials.TFID', '=', 'tbltransferforex.TransferForexID')
            // ->where('tblforexserials.FSType', '=', 1)
            ->where('tblforexserials.FSStat', '=', 2)
            ->where('tblforexserials.Transfer', '=', 1)
            ->where('tblforexserials.Sold', '=', 0)
            ->where('tblforexserials.TFID', '=', $request->id)
            ->where('tblforextransactiondetails.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->selectRaw('tblcurrency.Currency, SUM(tblforexserials.BillAmount) as total_bill_amount, COUNT(tblforexserials.BillAmount) as bill_amount_count')
            ->groupBy('tblcurrency.Currency')
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->get();

        $result['serial_breakdown'] = DB::connection('forex')->table('tblforextransactiondetails')
            ->join('tblcurrency', 'tblforextransactiondetails.CurrencyID', '=', 'tblcurrency.CurrencyID')
            ->join('tblforexserials', 'tblforextransactiondetails.FTDID', '=', 'tblforexserials.FTDID')
            ->join('tbltransferforex', 'tblforexserials.TFID', '=', 'tbltransferforex.TransferForexID')
            ->where('tblforexserials.FSStat', '=', 2)
            ->where('tblforexserials.Transfer', '=', 1)
            ->where('tblforexserials.Sold', '=', 0)
            ->where('tblforexserials.TFID', '=', $request->id)
            ->where('tblforextransactiondetails.BranchID', '=', Auth::user()->getBranch()->BranchID)
            ->selectRaw('tblforexserials.BillAmount, tblcurrency.Currency, SUM(tblforexserials.BillAmount) as total_bill_amount, COUNT(tblforexserials.BillAmount) as bill_amount_count')
            ->groupBy('tblforexserials.BillAmount', 'tblcurrency.Currency')
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->get();

        return view("transfer_forex.transfer_forex_details", compact('result', 'menu_id'));
    }

    public function details(Request $request) {
        $transfer_details = DB::connection('forex')->table('tblforextransactiondetails')
            ->join('tblcurrency', 'tblforextransactiondetails.CurrencyID', '=', 'tblcurrency.CurrencyID')
            ->join('tblforexserials', 'tblforextransactiondetails.FTDID', '=', 'tblforexserials.FTDID')
            ->join('tbltransferforex', 'tblforexserials.TFID', '=', 'tbltransferforex.TransferForexID')
            ->where('tblforexserials.FSType' , '=' , 1)
            ->where('tblforexserials.FSStat' , '=' , 2)
            ->where('tblforexserials.Transfer' , '=' , 1)
            ->where('tblforexserials.TFID' , '=' , $request->get('transfer_tfid'))
            ->where('tblforexserials.Sold' , '=' , 0)
            ->select(
                'tblforexserials.FSType',
                'tblforexserials.FSStat',
                'tblforexserials.Transfer',
                'tblforexserials.TFID',
                'tblforexserials.Sold',
                'tblforextransactiondetails.TransactionDate',
                'tblforextransactiondetails.ReceiptNo',
                'tblcurrency.Currency',
                'tblforexserials.BillAmount',
                'tblforexserials.Serials',
                'tbltransferforex.TransferForexID',
                'tbltransferforex.TransferForexNo',
            )
            ->get();

        return response()->json($transfer_details);
    }

    public function serials(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $trans_type_id = $request->get('selected_trans_type_id');
        // $item_id = $trans_type_id == "1" ? 28 : ($trans_type_id == "2" ? 35 : ($trans_type_id == "3" ? 45 : $trans_type_id));

        $bills_for_transfer = DB::connection('forex')->table('tblforextransactiondetails as fd')
            ->join('tbltransactiontype as tt', 'fd.TransType', 'tt.TTID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', '=', 'tc.CurrencyID')
            ->join('tblforexserials as fs', 'fd.FTDID', '=', 'fs.FTDID')
            ->join('tbldenom as td', 'fs.DenomID', 'td.DenomID')
            ->where('fd.Voided', 0)
            ->where('fs.FSType' , '=' , $trans_type_id)
            ->where('fs.FSStat' , '=' , 1)
            ->where('fs.TFID', '=', 0)
            ->where('fs.TFUID', '=', 0)
            ->where('fs.Transfer', '=', 0)
            ->where('fs.Sold', '=', 0)
            ->where('fs.Serials', '!=', null)
            ->where('td.SinagRateBuying', '!=', 0.0)
            ->where('fd.TransType' , '=' , $trans_type_id)
            ->where('fd.TransactionDate', '>=', '2025-01-01')
            ->where('fd.BranchID' , '=' , Auth::user()->getBranch()->BranchID)
            ->orderBy('tc.Currency', 'ASC')
            ->orderBy('fs.BillAmount', 'DESC')
            ->orderBy('fd.TransactionDate', 'DESC')
            ->get();

        $response = [
            'bills_for_transfer' => $bills_for_transfer
        ];

        return response()->json($response);
    }

    public function delete(Request $request) {
        DB::connection('forex')->table('tbltransferforex')
            ->where('tbltransferforex.TransferForexID', '=', $request->get('transfer_forex_id'))
            ->update([
                'Voided' => 1,
                'ITNo' => null,
                'ITID' => null,
            ]);

        DB::connection('forex')->table('tblforexserials')
            ->where('tblforexserials.TFID', '=', $request->get('transfer_forex_id'))
            ->update([
                'Transfer' => 0,
                'Received' => 0,
                'TFID' => 0,
                'TFUID' => 0,
                'FSStat' => 1,
                'CMRUsed' => 0.000000,
                'QueuedBy' => null,
                'Buffer' => 0,
                'BufferType' => 0
            ]);
    }

    public function addTrackingNo(Request $request) {
        $parse_tfids = explode(", ", $request->input('parse_tfids'));

        foreach ($parse_tfids as $TFIDs) {
            DB::connection('forex')->table('tbltransferforex')
                ->where('tbltransferforex.TransferForexID', '=', $TFIDs)
                ->update([
                    'ITID' => $request->input('tracking_id'),
                    'ITNo' => $request->input('tracking_no'),
                ]);
        }
    }

    public function removeTrackingNo(Request $request) {
        DB::connection('forex')->table('tbltransferforex')
            ->where('tbltransferforex.TransferForexID', '=', $request->get('transf_fx_id'))
            ->update([
                'ITID' => null,
                'ITNo' => null,
            ]);
    }

    public function bufferDetails(Request $request) {
        $result['buffer_transf_deets'] = DB::connection('forex')->table('tbltransferforexdetails as fxd')
            ->select('tc.Currency', 'fs.BillAmount' ,'fs.Serials' ,'fx.TransferDate' ,'fx.TransferForexID' ,'fs.FSID')
            ->join('tblforexserials as fs', 'fxd.FSID', 'fs.FSID')
            ->join('tbltransferforex as fx', 'fxd.TransferForexID', 'fx.TransferForexID')
            ->join('tblforextransactiondetails as fd', 'fs.FTDID', 'fd.FTDID')
            ->join('tblcurrency as tc', 'fd.CurrencyID', 'tc.CurrencyID')
            ->where('fxd.TransferForexID', '=', $request->transfer_forex_id)
            ->get();

        $buffer_transf = DB::connection('forex')->table('tblbuffertransfer as bf')
            ->select('bf.BufferType')
            ->where('bf.TFID', '=', $request->transfer_forex_id)
            ->first();

        return view("transfer_forex.buffer_transf_details_modal", compact('result', 'buffer_transf'));
    }

    public function acknowledge(Request $request) {
        $raw_date = Carbon::now('Asia/Manila');
        $admin_stock_details_s = $this->adminStocks();

        $query = DB::connection('forex')->table('tblbuffertransfer')
            ->where('tblbuffertransfer.TFID', '=', $request->get('transfer_forex_id'));

        $BufferID = $query->pluck('BufferID')->first();

        $query->clone()
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

        $CMR = $queueing_details->clone()
            ->selectRaw('MAX(fs.CMRUsed)')
            ->value('CMRUsed');

        $cmr_used = $CMR == null ? 0 : $CMR;

        $queued_by = $queueing_details->clone()
            ->selectRaw('MAX(fs.QueuedBy)')
            ->value('QueuedBy');

        if (intval($request->get('buffer_type')) == 1) {
            $limit = $request->get('total_buffer_amount');
            $transcap_amnt = $limit * $cmr_used;

            $total_amount = 0;
            $selected_ids = [];
            $selected_rates = [];

            foreach ($admin_stock_details_s as $details) {
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
                        'tblforexserials.CMRUsed' => $cmr_used,
                        'tblforexserials.Queued' => 1,
                        'tblforexserials.QueuedBy' =>  $queued_by,
                        'tblforexserials.BufferType' => $request->get('buffer_type'),
                        'tblforexserials.BufferID' => $BufferID,
                    ]);
            }

            if (count($admin_update->get())) {
                $admin_update->clone()
                    ->update([
                        'tbladminforexserials.CMRUsed' => $cmr_used,
                        'tbladminforexserials.Queued' => 1,
                        'tbladminforexserials.QueuedBy' =>  $queued_by,
                        'tbladminforexserials.BufferType' => $request->get('buffer_type'),
                        'tbladminforexserials.BufferID' => $BufferID,
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
            $limit = $request->get('total_buffer_amount');
            $transcap_amnt = $limit * $cmr_used;

            $max_tc_no = DB::connection('forex')->table('tblfxtranscap')
                ->selectRaw('CASE WHEN MAX(TCNo) IS NULL THEN 1 ELSE MAX(TCNo) + 1 END AS maxTCNo')
                ->value('maxTCNo');

            $TCID = DB::connection('forex')->table('tblfxtranscap')
                ->insertGetId([
                    'TCNo' => $max_tc_no,
                    'BranchID' => Auth::user()->getBranch()->BranchID,
                    'TranscapAmount' => $transcap_amnt,
                    'UserID' => $queued_by,
                ]);

            $pawn_branch_id = DB::connection('forex')->table('tblbranch as tb')
                ->join('pawnshop.tblxbranch as txb', 'tb.BranchCode', 'txb.BranchCode')
                ->where('tb.BranchID', Auth::user()->getBranch()->BranchID)
                ->pluck('txb.BranchID')
                ->toArray();

            $get_tr_no = DB::connection('generaldcpr')->table('tbltransaddcap')
                ->selectRaw('MAX(trno) + 1 AS max_tr_no')
                ->value('max_tr_no');

            DB::connection('generaldcpr')->table('tbltransaddcap')
                ->insert([
                    'tentrydate' => $raw_date->toDateString(),
                    'tentrytime' => $raw_date->toTimeString(),
                    'trno' => $get_tr_no,
                    'trdate' => $raw_date->toDateString(),
                    'tramount' => $transcap_amnt,
                    'tobranchid' => $pawn_branch_id[0],
                    'tremarks' => 'TRANSCAP - FOREX',
                    'tbranchid' => 1,
                    'tappraiserid' => Auth::user()->UserID,
                    'tuserid' => $request->input('matched_user_id'),
                    // 'STMDID' => $STMDID,
                    'TCID' => $TCID,
                    'SegmentID' => 2,
                ]);

            // Create an auto transcap to the branch where the buffer is from
            DB::connection('forex')->table('tblforexserials as fs')
                ->where('fs.TFID', $request->get('transfer_forex_id'))
                ->update([
                    'fs.CMRUsed' => 0.000000,
                    'fs.QueuedBy' => null
                ]);
        }

        $message = "Buffer successfully transfered!";
        return redirect()->back()->with(['message' => $message]);
    }

    public function tracking(Request $request) {
        $tracking_number = DB::connection('itinventory')->table('tbldepartment')
            ->select('tblitemtransfer.itno as TrackingNumber','tblitemtransfer.ITID as TrackingID', 'tblitemtransfer.itdate as EntryDate')
            ->join('tracking.tblitemtransfer', 'tbldepartment.deptid', 'tracking.tblitemtransfer.deptid')
            ->where('tracking.tblitemtransfer.DeptID', '=', 5)
            ->where('tracking.tblitemtransfer.Sender', '=', Auth::user()->getBranch()->pxBranchID)
            ->where('tracking.tblitemtransfer.itdate', '=', Carbon::now()->toDateString())
            ->orderBy('tracking.tblitemtransfer.itno', 'DESC')
            ->get();

        $ITDIDs = [];

        foreach ($tracking_number as $index => $details) {
            $tracking_numbers = DB::connection('tracking')->table('tblitemtransferdetails')
                ->selectRaw('tblitemtransferdetails.ITID, tblitem.ItemDesc, tblitemtransferdetails.Qty')
                ->join('tblitem', 'tblitemtransferdetails.ItemID', 'tblitem.ItemID')
                ->where('tblitemtransferdetails.ITID', '=', $details->TrackingID)
                ->whereIn('tracking.tblitemtransferdetails.ItemID', [28, 35, 45, 97])
                ->orderBy('tblitemtransferdetails.ITID', 'ASC')
                ->get();

            $item_desc = [];
            $item_type = [];
            $item_quantity = [];

            foreach ($tracking_numbers as $get_item_desc_id) {
                $item_desc[] = "{$get_item_desc_id->ItemDesc} ({$get_item_desc_id->Qty})";
                $item_type[] = $get_item_desc_id->ItemDesc;
                $item_quantity[] = $get_item_desc_id->Qty;
            }

            $details->ItemDesc = implode(',', $item_desc);
            $details->ItemType = $item_type;
            $details->ItemQty = $item_quantity;

            $ITDIDs[] = $details;
        }

        $reponse = [
            'tracking_number' => $tracking_number
        ];

        return response()->json($reponse);
    }
}
