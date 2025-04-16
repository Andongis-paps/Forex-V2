<?php

namespace App\Http\Controllers\Window;
use App\Http\Controllers\Controller;
use Validator;
use App;
use Lang;
use App\Admin;
use Illuminate\Support\Carbon;
use DB;
use Hash;
use Auth;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Query\Builder;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class CurrencyManualMaintenanceController extends Controller {
    protected $MenuID;

    public function __construct() {
        $this->middleware('check.access.permission:CURRENCY MANUAL,VIEW')->only(['show', 'currencyManualDetail']);
        $this->middleware('check.access.permission:CURRENCY MANUAL,ADD')->only(['addCurrencyManual', 'getDenominations']);
        $this->middleware('check.access.permission:CURRENCY MANUAL,EDIT')->only(['editManualDetails', 'updateCurrencyManual']);
        $this->middleware('check.access.permission:CURRENCY MANUAL,DELETE')->only(['deleteManual']);
    }

    public function show(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['currencies'] = DB::connection('forex')->table('tblcurrency')
            ->join('tblcountries', 'tblcurrency.CountryID', 'tblcountries.CountryID')
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->paginate(15);

        return view('window.currency_manual_mainte.currency_manual', compact('result', 'menu_id'));
    }

    public function getDenominations(Request $request) {
        $denominations = DB::connection('forex')->table('tbldenominationmaintenance')
            ->where('tbldenominationmaintenance.CurrencyID', $request->get('currency_id'))
            ->get();

        $response = [
            'denominations' => $denominations
        ];

        return response()->json($response);
    }

    public function addCurrencyManual(Request $request) {
        $bill_image_path = '';
        $raw_date = Carbon::now('Asia/Manila');

        if ($request->hasFile('manual-image')) {
            $front_image = $request->file('manual-image');

            $resized_front = Image::make($front_image)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode($front_image->getClientOriginalExtension());

            $resized_size = strlen((string) $resized_front);

            if ($resized_size > 800 * 1024) {
                return redirect()->back()->withErrors(['manual-image' => 'Resized image size should not exceed 800 kilobytes.'])->withInput();
            } else {
                $validator = Validator::make($request->all(), [
                    'manual-image' => 'nullable|image|mimes:jpeg,png,jpg',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                } else {
                    $front_timestamp = now()->format('YmdHis');
                    $front_image_name = $front_timestamp . '.' . $front_image->getClientOriginalExtension();
                    $bill_image_path = 'uploads/currency_manual_images/' . $front_image_name;

                    Storage::put('public/' . $bill_image_path, $resized_front);
                }
            }
            // Assuming $this->addWatermark() function is correctly defined elsewhere
            // $this->addWatermark(storage_path('app/public/' . $finalImagePath), $finalImagePath);
        } else {
            $bill_image_path == null;
        }

        // manual_image

        $stop_buying = $request->get('stop_buying') == "null" ? null : $request->get('stop_buying');

        DB::connection('forexcurrency')->table('tblcurrencymanual')
            ->insert([
                'CurrencyID' => $request->get('currency'),
                'DenominationID' => $request->get('denomination'),
                'BillAmount' => $request->get('bill_amount'),
                'CMTID' => $request->get('manual_type'),
                'BillAmountImage' => $bill_image_path != null ? $bill_image_path : null,
                'StopBuying' => $stop_buying,
                'Remarks' => $request->get('manual_remarks'),
                // 'UserID' => $request->input('matched_user_id')
            ]);
    }

    public function currencyManualDetail(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['currency_manual'] = DB::connection('forexcurrency')->table('tblcurrencymanual')
            ->selectRaw('tblcurrencymanual.CMID, tblcurrency.CurrencyID, tblcurrency.Currency, tblcurrency.CurrAbbv, tbldenominationmaintenance.DenominationID, tbldenominationmaintenance.BillAmount, tblcurrmanualtags.ManualTag, tblcurrmanualtags.CMTID, tblcurrencymanual.BillAmountImage, tblcurrencymanual.StopBuying, tblcurrencymanual.Remarks, tblcurrencymanual.EntryDate')
            ->join('forex.tblcurrency', 'tblcurrencymanual.CurrencyID', 'tblcurrency.CurrencyID')
            ->join('forex.tbldenominationmaintenance', 'tblcurrencymanual.DenominationID', 'tbldenominationmaintenance.DenominationID')
            ->join('forex.tblcurrmanualtags', 'tblcurrencymanual.CMTID', 'tblcurrmanualtags.CMTID')
            ->where('tblcurrencymanual.CurrencyID', $request->id)
            ->groupBy('tblcurrencymanual.CMID', 'tblcurrency.CurrencyID', 'tblcurrency.Currency', 'tblcurrency.CurrAbbv', 'tbldenominationmaintenance.DenominationID', 'tbldenominationmaintenance.BillAmount', 'tblcurrmanualtags.ManualTag', 'tblcurrmanualtags.CMTID', 'tblcurrencymanual.BillAmountImage', 'tblcurrencymanual.StopBuying', 'tblcurrencymanual.Remarks', 'tblcurrencymanual.EntryDate')
            ->get();

        $result['currencies'] = DB::connection('forex')->table('tblcurrency')
            ->join('tblcountries', 'tblcurrency.CountryID', 'tblcountries.CountryID')
            ->orderBy('tblcurrency.Currency', 'ASC')
            ->where('tblcurrency.CurrencyID', $request->id)
            ->get();

        $result['manual_tags'] = DB::connection('forex')->table('tblcurrmanualtags')
            ->get();

        return view('window.currency_manual_mainte.edit_currency_manual', compact('result', 'menu_id'));
    }

    public function existing(Request $request) {
        $boolean = DB::connection('forexcurrency')->table('tblcurrencymanual as cm')
            ->where('cm.CurrencyID', $request->get('currency'))
            ->where('cm.BillAmount', $request->get('bill_amount'))
            ->where('cm.CMTID', $request->get('manual_type'))
            ->exists();

        $response = [
            'boolean' => $boolean
        ];

        return response()->json($response);
    }

    public function editManualDetails(Request $request) {
        $this->MenuID = $request->attributes->get('MenuID');
        $menu_id = $this->MenuID;

        $result['currency_manual'] = DB::connection('forexcurrency')->table('tblcurrencymanual')
            ->selectRaw('tblcurrencymanual.CMID, tblcurrency.CurrencyID, tblcurrency.Currency, tblcurrency.CurrAbbv, tbldenominationmaintenance.DenominationID, tbldenominationmaintenance.BillAmount, tblcurrmanualtags.ManualTag, tblcurrmanualtags.CMTID, tblcurrencymanual.BillAmountImage, tblcurrencymanual.StopBuying, tblcurrencymanual.Remarks, tblcurrencymanual.EntryDate')
            ->join('forex.tblcurrency', 'tblcurrencymanual.CurrencyID', 'tblcurrency.CurrencyID')
            ->join('forex.tbldenominationmaintenance', 'tblcurrencymanual.DenominationID', 'tbldenominationmaintenance.DenominationID')
            ->join('forex.tblcurrmanualtags', 'tblcurrencymanual.CMTID', 'tblcurrmanualtags.CMTID')
            // ->join('pawnshop.tblxusers', 'tblcurrencymanual.UserID', 'pawnshop.tblxusers.UserID')
            ->where('tblcurrencymanual.CMID', $request->get('cmid'))
            ->groupBy('tblcurrencymanual.CMID', 'tblcurrency.CurrencyID', 'tblcurrency.Currency', 'tblcurrency.CurrAbbv', 'tbldenominationmaintenance.DenominationID', 'tbldenominationmaintenance.BillAmount', 'tblcurrmanualtags.ManualTag', 'tblcurrmanualtags.CMTID', 'tblcurrencymanual.BillAmountImage', 'tblcurrencymanual.StopBuying', 'tblcurrencymanual.Remarks', 'tblcurrencymanual.EntryDate')
            ->get();

        $result['manual_tags'] = DB::connection('forex')->table('tblcurrmanualtags')
            ->get();

        return view('window.currency_manual_mainte.edit_currency_manual_modal', compact('result', 'menu_id'));
    }

    public function updateCurrencyManual(Request $request) {
        $bill_image_path_ = '';
        $raw_date = Carbon::now('Asia/Manila');

        $exsisting_data = DB::connection('forexcurrency')->table('tblcurrencymanual')
            ->where('tblcurrencymanual.CMID', $request->get('CMID'))
            ->value('BillAmountImage');

        if ($request->hasFile('manual-image-update')) {
            $front_image = $request->file('manual-image-update');

            $resized_front = Image::make($front_image)->resize(800, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode($front_image->getClientOriginalExtension());

            $resized_size = strlen((string) $resized_front);

            if ($resized_size > 800 * 1024) {
                return redirect()->back()->withErrors(['manual-image-update' => 'Resized image size should not exceed 800 kilobytes.'])->withInput();
            } else {
                $validator = Validator::make($request->all(), [
                    'manual-image-update' => 'nullable|image|mimes:jpeg,png,jpg',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                } else {

                    $front_timestamp = now()->format('YmdHis');
                    $front_image_name = $front_timestamp . '.' . $front_image->getClientOriginalExtension();
                    $bill_image_path_ = 'uploads/currency_manual_images/' . $front_image_name;

                    Storage::put('public/' . $bill_image_path_, $resized_front);
                }
            }
            // Assuming $this->addWatermark() function is correctly defined elsewhere
            // $this->addWatermark(storage_path('app/public/' . $finalImagePath), $finalImagePath);
        } else {
            $bill_image_path_ == null;
        }

        $stop_buying = $request->get('stop_buying') == "null" ? null : $request->get('stop_buying');

        DB::connection('forexcurrency')->table('tblcurrencymanual')
            ->where('tblcurrencymanual.CMID', $request->get('CMID'))
            ->update([
                'CurrencyID' => $request->get('currency'),
                'DenominationID' => $request->get('denomination'),
                'BillAmount' => $request->get('bill_amount'),
                'CMTID' => $request->get('manual_type'),
                'BillAmountImage' => $bill_image_path_ != null ? $bill_image_path_ : $exsisting_data,
                'StopBuying' => $stop_buying,
                'Remarks' => $request->get('manual_remarks'),
                // 'UserID' => $request->input('matched_user_id')
            ]);
    }

    public function deleteManual(Request $request) {
        DB::connection('forexcurrency')->table('tblcurrencymanual')
            ->where('tblcurrencymanual.CMID', $request->get('CMID'))
            ->delete();
    }
}
