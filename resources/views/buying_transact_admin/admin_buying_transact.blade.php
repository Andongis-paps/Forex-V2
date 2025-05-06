@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
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
                                                <i class='bx bxs-badge-dollar' ></i>&nbsp;{{ trans('labels.new_buying_trans_title') }}
                                            </span>
                                        </div>
                                        <div class="col-12 border border-gray-300 pt-3 pb-0" id="buying-container">
                                            {{-- Buying Transaction - Date --}}
                                            <div class="row align-items-center px-3 mb-3">
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
                                                        <input type="text" class="form-control" id="customer-name-selected" value="{{ !empty($result['customer']->FullName) ? $result['customer']->FullName : '' }}" readonly>
                                                        <input type="hidden" class="form-control" id="customer-id-selected" name="customer-id-selected" value="{{ !empty($result['customer']->CustomerID) ? $result['customer']->CustomerID : '' }}" readonly>
                                                        <input type="hidden" class="form-control" id="customer-no-selected" name="customer-no-selected" value="{{ !empty($result['customer']->CustomerNo) ? $result['customer']->CustomerNo : '' }}" readonly>
                                                        <input type="hidden" class="form-control" id="customer-entry-id" name="customer-entry-id" value="{{ !empty($result['customer']->CustomerID) ? $result['customer']->CustomerID : '' }}" readonly>
                                                        <button class="btn btn-primary" id="customer-detail" type="button" disabled data-bs-toggle="modal" data-bs-target="#customerDeetsModal">Customer</button>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- <div class="row px-3 align-items-center mb-3">
                                                <div class="col-3">
                                                    <strong>
                                                        Company: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div>
                                                <div class="col-8">
                                                    <input type="text" class="form-control" id="customer-employer" value="" readonly>
                                                </div>
                                            </div> --}}

                                            {{-- Buying Transaction - Receipt Set --}}
                                            <div class="row align-items-center px-3 my-3 @if(session('time_toggle_status') == 1) d-none @endif">
                                                <div class="col-3">
                                                    <strong>
                                                        {{ trans('labels.buying_rset') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div>

                                                <div class="col-9">
                                                    <div class="row">
                                                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                            <input type="radio" class="btn-check" name="radio-rset" id="r-set-o" value="{{ trans('labels.buying_rset_o') }}" @if(session('time_toggle_status') == 1) @endif @if (empty($result['customer'])) disabled @else @endif>
                                                            <label class="btn btn-outline-primary" for="r-set-o">
                                                                <strong>{{ trans('labels.buying_rset_o') }}</strong>
                                                            </label>

                                                            <input type="radio" class="btn-check" name="radio-rset" id="r-set-b" value="{{ trans('labels.buying_rset_b') }}"  @if (empty($result['customer'])) disabled @else @endif>
                                                            <label class="btn btn-outline-primary" for="r-set-b">
                                                                <strong>{{ trans('labels.buying_rset_b') }}</strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Buying Transaction - OR Number --}}
                                            <div class="row align-items-center px-3 mt-3" id="or-number-container">
                                                <div class="col-3">
                                                    <strong>
                                                        {{ trans('labels.buying_or_number') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div>
                                                <div class="col-9">
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
                                                                <input type="radio" class="btn-check radio-button" name="radio-transact-type" id="radio-button-{{ $transact_type->TransType }}" value="{{ $transact_type->TTID }}">
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
                                                
                                                {{-- <div class="col-6">
                                                    <select class="form-control" name="currencies" id="currencies_select" disabled>
                                                        <option value="Select a currency" id="buying-default-currency">Select a currency</option>
                                                    </select>
                                                    <input type="hidden" id="last-entry-crid" value="">
                                                    <input type="hidden" id="latest-entry-crid" value="">
                                                </div>
                                                <div class="col-3">
                                                    <div class="row pe-3 ">
                                                        <button class="btn btn-primary btn-sm" id="currency-manual-modal-button" type="button" data-bs-toggle="modal" data-bs-target="#currency-manual-modal" disabled><i class='bx bx-spreadsheet pb-1'></i>&nbsp;{{ trans('labels.buying_currency_manual') }}</button>
                                                    </div>
                                                </div> --}}
                                            </div>

                                            <div class="row align-items-center px-3 mt-3">
                                                <div class="col-3">
                                                    <strong>
                                                        Remarks: &nbsp;
                                                    </strong>
                                                </div>
                                                <div class="col-9">
                                                    <textarea class="form-control" id="remarks" name="remarks" rows="2" disabled></textarea>
                                                </div>
                                            </div>

                                            <input type="hidden" id="time-and-off-status" data-timeonandoffstat="{{ session('time_toggle_status') }}">
                                            <input type="hidden" id="currency-images-path" data-currimgpath="{{ asset(trans('labels.currency_images_path_2')) }}">
                                            {{-- Buying Transaction - Currency Denomination --}}
                                            <div class="row align-items-center px-3 mt-3">
                                                {{-- <div class="col-3">
                                                    <strong>
                                                        {{ trans('labels.buying_trans_curr_denom') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div> --}}
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
                                                                    <th hidden class="text-center text-xs font-extrabold text-black p-1" id="mtcn-header">{{ trans('labels.buying_bill_mtcn') }}</th>
                                                                    <th hidden class="text-center text-xs font-extrabold text-black p-1" id="dpofx-bill-amnt-header">{{ trans('labels.buying_bill_dpofx_amnt') }}</th>
                                                                    <th hidden class="text-center text-xs font-extrabold text-black p-1" id="dpofx-rate-header">{{ trans('labels.buying_bill_dpofx_rate') }}</th>
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

                                            <div class="row align-items-center px-3 mt-3" id="buffer-option" style="display: none;">
                                                <div class="col-2 offset-3">
                                                    <span>Declare as:</span>
                                                </div>

                                                <div class="col-7" id="buffer-options">
                                                    <span>Received in:</span>
                                                </div>

                                                {{-- <div class="col-2 mt-1 offset-3">
                                                    <div class="col-12 text-left">
                                                        <div class="row">
                                                            <label class="switch switch-success switch-square">
                                                                <input type="checkbox" class="switch-input buffer-option" id="buffer-option" disabled>
                                                                <span class="switch-toggle-slider">
                                                                    <span class="switch-on"></span>
                                                                    <span class="switch-off"></span>
                                                                </span>
                                                                <span class="switch-label cursor-pointer">
                                                                    <strong>
                                                                        Buffer
                                                                    </strong>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-7 mt-1" id="buffer-options">
                                                    <div class="row">
                                                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                            <input type="radio" class="btn-check" name="radio-buffer-options" id="r-buffer-opt-1" value="1" disabled="true">
                                                            <label class="btn btn-outline-primary" for="r-buffer-opt-1">
                                                                <strong>In PHP</strong>
                                                            </label>

                                                            <input type="radio" class="btn-check" name="radio-buffer-options" id="r-buffer-opt-2" value="2" disabled="true">
                                                            <label class="btn btn-outline-primary" for="r-buffer-opt-2">
                                                                <strong>In USD</strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                            </div>

                                            {{-- Buying Transaction - Currency Amount --}}
                                            <div class="row align-items-center px-3 " id="currency-amount-buying">
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
                                                        <input type="hidden" class="form-control" name="total_buying_amount_input" id="total_buying_amount_input">
                                                        <span class="text-lg pt-3 font-bold text-black">PHP</span> &nbsp;
                                                        <span class="text-lg pt-3 font-bold text-black" name="total_buying_amount" id="total_buying_amount" value="0">0.00</span>
                                                        {{-- <input type="text" class="form-control" name="total_buying_amount" id="total_buying_amount" autocomplete="off" value="0" readonly>/ --}}
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
                                                <div class="col-lg-6">

                                                </div>
                                                <div class="col-lg-6 text-end">
                                                    @can('access-permission', $menu_id)
                                                        <a class="btn btn-secondary btn-sm" type="button" href="{{ route('admin_transactions.buying_transaction') }}">{{ trans('labels.back_action') }}</a>
                                                    @endcan
                                                    @can('add-permission', $menu_id)
                                                        <button class="btn btn-primary btn-sm" type="button" id="transaction-confirm-button" disabled>{{ trans('labels.confirm_action') }}</button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="col-5">
                                @include('UI.UX.security_code')
                                @include('UI.UX.customer_searching')
                                @include('UI.UX.customer_info_card',['customer' => !empty($result['customer']) ? $result['customer'] : '']) {{-- Customer Information Card --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('UI.UX.currency_manual')
@endsection

@section('admin_buying_scripts')
    @include('script.admin_buying_trans_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
