@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')

            @php
                use Carbon\Carbon;

                $raw_date = Carbon::now('Asia/Manila');
            @endphp
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-lg-12 mb-4">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                @if(session()->has('message') && session()->has('latest_ftdid'))
                                    <div class="alert alert-success alert-dismissible d-none" role="alert" id="success-message-saving-success" data-successexistence="1" data-recenftdid="{{ session()->get('latest_ftdid') }}">
                                        {{ session()->get('message') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <input type="hidden" id="full-url" value="{{ URL::to('/').'/'.'buyingTransactioradio-rsetn' }}">

                                {{-- <div class="col-12">
                                    <div class="card p-0" id="new-buying-transaction-header">
                                        <div class="card-body p-3">
                                            <span class="text-lg font-semibold p-2 text-white">
                                                {{ trans('labels.new_buying_trans_title') }}
                                            </span>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>

                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-7">
                                <form class="m-0" method="post" id="buying-transact-form">
                                    @csrf
                                    <div class="card">
                                        <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                            <span class="text-lg font-bold p-2 text-black">
                                                <i class='bx bx-list-ul' ></i>&nbsp;{{ trans('labels.new_buying_trans_title') }}
                                            </span>
                                        </div>

                                        <div class="col-12 pt-3 pb-0 border border-gray-300" id="buying-container">
                                            {{-- Buying Transaction - Date --}}
                                            <div class="row align-items-center px-3">
                                                <div class="col-3">
                                                    <strong>
                                                        {{ trans('labels.buying_trans_date') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div>
                                                <div class="col-9">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="transact-date" name="transact-date" autocomplete="off" placeholder="yyyy-mm-dd" readonly>
                                                        <span class="input-group-text bg-slate-950"><i class='bx bx-calendar'></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Buying Transaction - Customer Details --}}
                                            <div class="row align-items-center px-3 mt-3">
                                                <div class="col-3">
                                                    <strong>
                                                        {{ trans('labels.buying_customer_name') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div>
                                                <div class="col-9">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="customer-name-selected" value="" readonly>
                                                        <input type="hidden" class="form-control" id="customer-id-selected" name="customer-id-selected" value="" readonly>
                                                        <input type="hidden" class="form-control" id="customer-no-selected" name="customer-no-selected" value="" readonly>
                                                        <input type="hidden" class="form-control" id="customer-entry-id" name="customer-entry-id" value="" readonly>
                                                        <button class="btn btn-primary" id="customer-detail" type="button" disabled data-bs-toggle="modal" data-bs-target="#customerDeetsModal">Customer</button>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Buying Transaction - Receipt Set --}}
                                            {{-- <div class="row align-items-center px-3 mt-3 @if(session('time_toggle_status') == 1) d-none @endif">
                                                <div class="col-3">
                                                    <strong>
                                                        {{ trans('labels.buying_rset') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div>

                                                <div class="col-9">
                                                    <div class="row">
                                                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                            <input type="radio" class="btn-check" name="radio-rset" id="r-set-o" value="{{ trans('labels.buying_rset_o') }}" @if(session('time_toggle_status') == 1) @endif disabled="true">
                                                            <label class="btn btn-outline-primary" for="r-set-o">
                                                                <strong>{{ trans('labels.buying_rset_o') }}</strong>
                                                            </label>

                                                            <input type="radio" class="btn-check" name="radio-rset" id="r-set-b" value="{{ trans('labels.buying_rset_b') }}" disabled="true">
                                                            <label class="btn btn-outline-primary" for="r-set-b">
                                                                <strong>{{ trans('labels.buying_rset_b') }}</strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> --}}
                                            {{-- Buying Transaction - OR Number --}}
                                            <div class="row align-items-center px-3 mt-3" id="or-number-container">
                                                <div class="col-3">
                                                    <strong>
                                                        {{ trans('labels.buying_or_number') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div>
                                                <div class="col-9">
                                                    {{-- <input type="number" class="form-control" id="or-number-buying" name="or-number-buying" autocomplete="off" placeholder="Invoice No." @if(session('time_toggle_status') == 0) disabled @else   @endif> --}}
                                                    <input type="number" class="form-control" id="or-number-buying" name="or-number-buying" autocomplete="off" placeholder="Invoice No." @if(session('time_toggle_status') == 0) disabled @else   @endif>
                                                </div>
                                            </div>
                                            {{-- Buying Transaction - Transaction Type --}}
                                            <div class="row align-items-center px-3 mt-3">
                                                <div class="col-3">
                                                    <strong>
                                                        {{ trans('labels.buying_trans_type') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div>
                                                <div class="col-9">
                                                    <div class="row text-center">
                                                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                            @foreach ($result['transact_type'] as $transact_type)
                                                                <input type="radio" class="btn-check radio-button" name="radio-transact-type" id="radio-button-{{ $transact_type->TransType }}" value="{{ $transact_type->TTID }}" disabled>
                                                                <label class="btn btn-outline-primary" for="radio-button-{{ $transact_type->TransType }}">
                                                                    <strong>{{ $transact_type->TransType }}</strong>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Buying Transaction - Currency --}}
                                            <div class="row align-items-center px-3 mt-3">
                                                <div class="col-3">
                                                    <strong>
                                                        {{ trans('labels.buying_trans_curr') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div>

                                                <div class="col-9">
                                                    <div class="input-group">
                                                        <select class="form-control" name="currencies" id="currencies_select" disabled>
                                                            <option value="Select a currency" id="buying-default-currency">Select a currency</option>
                                                        </select>

                                                        <button class="btn btn-primary btn-sm" id="currency-manual-modal-button" type="button" disabled>{{ trans('labels.buying_currency_manual') }}</button>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" id="time-and-off-status" data-timeonandoffstat="{{ session('time_toggle_status') }}">
                                            <input type="hidden" id="currency-images-path" data-currimgpath="{{ asset(trans('labels.currency_images_path_2')) }}">

                                            <div class="row px-3">
                                                <div class="col-12">
                                                    <hr>
                                                </div>
                                            </div>

                                            {{-- Buying Transaction - Currency Denomination --}}
                                            <div class="row align-items-center px-3">
                                                <div class="col-12">
                                                    <div id="currency-denom-calcu">
                                                        <table class="table table-bordered" id="currency-denom-table">
                                                            <thead>
                                                                <tr id="buying-transact-headers">
                                                                    <th class="text-center text-xs font-extrabold text-black p-1" id="bill-amount-header">Denomination</th>
                                                                    <th class="text-center text-xs font-extrabold text-black p-1" id="bill-count-header">{{ trans('labels.buying_bill_count') }}</th>
                                                                    <th class="text-center text-xs font-extrabold text-black p-1" id="subtotal-header">{{ trans('labels.buying_bill_subtotal') }}</th>
                                                                    <th class="text-center text-xs font-extrabold text-black p-1" id="sinag-buying-rate-header">{{ trans('labels.buying_bill_sinag_buying_rate') }}</th>
                                                                    <th class="text-center text-xs font-extrabold text-black p-1" id="total-bill-amount-header">{{ trans('labels.buying_bill_total_amount') }}</th>
                                                                </tr>
                                                                <tr class="hidden" id="dpofx-headers">
                                                                    <th class="text-center text-xs font-extrabold text-black p-1" id="mtcn-header">{{ trans('labels.buying_bill_mtcn') }}</th>
                                                                    <th class="text-center text-xs font-extrabold text-black p-1" id="dpofx-bill-amnt-header">{{ trans('labels.buying_bill_dpofx_amnt') }}</th>
                                                                    <th class="text-center text-xs font-extrabold text-black p-1" id="dpofx-rate-header">{{ trans('labels.buying_bill_dpofx_rate') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="buying-transact-table-body">
                                                                <tr id="buying-transact-banner">
                                                                    <td class="text-center text-td-buying text-sm py-3" colspan="13">
                                                                        <span class="buying-no-transactions text-lg">
                                                                            <strong>CHOOSE A CURRENCY</strong>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="5"></td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                        {{ csrf_field() }}
                                                </div>
                                            </div>
                                            {{-- Buying Transaction - Currency Amount --}}
                                            <div class="row align-items-center px-3" id="currency-amount-buying">
                                                <div class="col-4 offset-5 text-end">
                                                    <strong>
                                                        {{ trans('labels.buying_trans_bill_amnt') }}: &nbsp;
                                                    </strong>
                                                </div>
                                                <div class="col-3">
                                                    <div class="mb-3 pt-3">
                                                        <input type="hidden" class="form-control" name="current_amount_true" id="current_amount_true">
                                                        <input type="text" class="form-control text-end" name="current_amount" id="current_amount" autocomplete="off" value="0.00" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Buying Transaction - Rate Used --}}
                                            {{-- <div class="row align-items-center px-3 mt-3">
                                                <div class="col-4">
                                                    <strong>
                                                        {{ trans('labels.buying_trans_rate') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div>
                                                <div class="col-8">
                                                    <div class="input-group mb-3">

                                                        <input type="hidden" class="form-control" id="base-rate" value="">
                                                        <input type="number" class="form-control" name="rate-used" id="rate_used" autocomplete="off" value="">
                                                    </div>
                                                </div>
                                            </div> --}}
                                            {{-- <input type="hidden" class="form-control" id="base-rate" value=""> --}}
                                            {{-- <input type="hidden" class="form-control" name="rate-used" id="rate_used" autocomplete="off" value=""> --}}
                                            {{-- Buying Transaction - Total Amount --}}
                                            <div class="row align-items-center px-3 mb-3" id="total-amount-buying">
                                                <div class="col-4 offset-5 text-end">
                                                    <strong>
                                                        {{ trans('labels.buying_trans_amnt') }}: &nbsp;
                                                    </strong>
                                                </div>
                                                <div class="col-3">
                                                    <div class="col-12 text-end">
                                                        <input type="hidden" class="form-control" name="total_buying_amount_true" id="total_buying_amount_true">
                                                        <span class="text-lg pt-3 font-bold text-black">PHP</span> &nbsp;
                                                        <span class="text-lg pt-3 font-bold text-black" name="total_buying_amount" id="total_buying_amount" value="0">0.00</span>
                                                        {{-- <input type="text" class="form-control" name="total_buying_amount" id="total_buying_amount" autocomplete="off" value="0" readonly>/ --}}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row align-items-center px-3 mt-3 mb-3 hidden" id="payout-input-field" style="display: none;">
                                                <div class="col-5 offset-4 text-end">
                                                    <strong>Payout Amount:</strong>
                                                </div>
                                                <div class="col-3">
                                                    <div class="col-12 text-end">
                                                        <input type="hidden" class="form-control" name="payout_amount" id="payout_amount" value="0.00">
                                                        <span class="text-lg pt-3 font-bold text-black">PHP</span>
                                                        &nbsp;
                                                        <span class="text-lg pt-3 font-bold text-black" name="total-buying-amount-dpofx" id="total-buying-amount-dpofx" value="0">
                                                            0.00
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" id="test-get" value="">
                                            <input type="hidden" id="bill-amount-count" name="bill-amount-count" value="">
                                            <input type="hidden" id="multiplier-total-count" name="multiplier-total-count" value="">
                                            <input type="hidden" id="subtotal-count" name="subtotal-count" value="">
                                            <input type="hidden" id="sinag-buying-rate-count" name="sinag-buying-rate-count" value="">
                                            <input type="hidden" id="sinag-var-buying" name="sinag-var-buying" value="">
                                        </div>

                                        <div class="col-12 border border-gray-300 rounded-bl rounded-br p-2">
                                            <div class="row">
                                                <div class="col-lg-12 text-end pe-3">
                                                    @can('access-permission', $menu_id)
                                                        <a class="btn btn-secondary" type="button" href="{{ route('branch_transactions.buying_transaction') }}">{{ trans('labels.back_action') }}</a>
                                                    @endcan
                                                    @can('add-permission', $menu_id)
                                                        <button class="btn btn-primary" type="button" id="transaction-confirm-button" disabled>{{ trans('labels.confirm_action') }}</button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>

                                        @include('UI.UX.security_code')
                                        @include('UI.UX.customer_searching')
                                    </div>
                                </form>
                            </div>

                            <div class="col-5">
                                @include('UI.UX.customer_info_card')
                                @include('UI.UX.customer_img_zoom')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('UI.UX.currency_manual')
@endsection

@section('buying_scripts')
    @include('script.buying_transact_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>