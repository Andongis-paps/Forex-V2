@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row">
                    <div class="col-12">
                        <input type="hidden" id="full-url-selling" value="{{ URL::to('/').'/'.'soldSerialsDeets' }}">
                    </div>

                    <div class="col-12">
                        <hr>
                    </div>

                    <div class="col-lg-7">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <form class="m-0" action="{{ URL::to('/saveSellingTransact') }}" method="post" id="selling-transact-form">
                                    @csrf

                                    <div class="card">
                                        <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                            <span class="text-lg font-bold p-2 text-black">
                                                <i class='bx bxs-badge-dollar' ></i>&nbsp;{{ trans('labels.new_selling_trans_title') }}
                                            </span>
                                        </div>

                                        <div class="col-12 border border-gray-300 pt-3 pb-0" id="selling-container">
                                            {{-- Selling Transact - Date --}}
                                            <div class="row align-items-center px-3">
                                                <div class="col-3">
                                                    <strong>
                                                        {{ trans('labels.selling_trans_date') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div>
                                                <div class="col-9">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="transact-date" name="transact-date-selling" autocomplete="off" placeholder="yyyy-mm-dd" readonly>
                                                        {{-- <button class="btn btn-outline-secondary" type="button" id="transact-date-button"><i class='bx bx-calendar'></i></button> --}}
                                                        <span class="input-group-text bg-slate-950"><i class='bx bx-calendar'></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Selling Transact - Customer Name --}}
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
                                      
                                                        <input id="available-serials-count" type="hidden" value="{{ count($result['available_serials']) }}">
                                                        <button class="btn btn-primary" id="customer-detail-selling" type="button" disabled data-bs-toggle="modal" data-bs-target="#customerDeetsModal" @if (count($result['available_serials']) == 0) disabled @else  @endif>
                                                            Customer
                                                        </button>
                                                        {{-- <button class="btn btn-primary" id="customer-detail-selling" type="button" disabled data-bs-toggle="modal" data-bs-target="#customerDeetsSellingModal" @if (count($result['available_serials']) == 0) disabled @else  @endif>Customer &nbsp; <i class='bx bxs-user-detail pb-1'></i></button> --}}
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- Selling Transact - Receipt Set --}}
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

                                            <input type="hidden" value="{{ session('time_toggle_status') }}" id="time-toggle-stat">
                                            {{-- Selling Transaction - OR Number --}}
                                            <div class="row align-items-center px-3 mt-3" id="or-number-container">
                                                <div class="col-3">
                                                    <strong>
                                                        {{ trans('labels.selling_or_number') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div>
                                                <div class="col-9">
                                                    <input type="number" class="form-control or-number-selling" id="or-number-selling" name="or-number-selling" autocomplete="off" placeholder="Invoice No." @if(session('time_toggle_status') == 0) disabled @else  @endif">
                                                </div>
                                            </div>
                                            {{-- Selling Transact - Currency --}}
                                            <div class="row align-items-center px-3 mt-3">
                                                <div class="col-3">
                                                    <strong>
                                                        {{ trans('labels.selling_trans_curr') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div>
                                                <div class="col-9">
                                                    <select class="form-control" name="currencies-selling" id="currencies-select-selling" disabled>
                                                        <option id="currencies-selling-default-val" value="default-val">Select a currency</option>
                                                        @foreach ($result['currency'] as $currencies)
                                                            <option value="{{ $currencies->CurrencyID }}" name="selected-currency">{{ $currencies->Currency }}</option>
                                                        @endforeach

                                                        {{-- <input type="hidden" name="currency-ftdid" value="{{ $currencies->FTDID }}"> --}}
                                                    </select>
                                                    <input type="hidden" id="last-entry-crid" value="">
                                                    <input type="hidden" id="latest-entry-crid" value="">
                                                </div>
                                            </div>
                                            {{-- Selling Transact - Rate --}}
                                            <div class="row align-items-center px-3 mt-3">
                                                <div class="col-3">
                                                    <strong>
                                                        {{ trans('labels.selling_trans_rate') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div>
                                                <div class="col-9">
                                                    <input type="text" class="form-control" id="rate-used-selling" name="rate-used-selling" autocomplete="off" placeholder="0.00" disabled>
                                                    <input type="hidden" class="form-control" id="rate-used-selling-curr-id" value="">
                                                    <input type="hidden" class="form-control" id="rate-used-true" value="" name="rate-used-true">
                                                </div>
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

                                            <div class="row align-items-center px-3 mt-3">
                                                <div class="col-9 offset-3 px-4">
                                                    <div class="row align-items-center">
                                                        <button class="btn btn-secondary btn-sm button-add-serial" id="button-add-serial" type="button"  data-bs-toggle="modal" data-bs-target="#serial-stock-modal-appended" disabled>Add Serial &nbsp; <i class='bx bx-plus pb-1'></i></button>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Selling Transact - Appending Elements --}}
                                            <div id="new-serial-container"></div>

                                            {{-- Selling Transact - Total Currency Amount --}}
                                            <div class="row align-items-center px-3 mt-3">
                                                <div class="col-3 offset-5 text-end">
                                                    <strong>
                                                        {{ trans('labels.selling_trans_total_bill_amnt') }}: &nbsp;
                                                    </strong>
                                                </div>
                                                <div class="col-4 text-right">
                                                    <input type="text" class="form-control text-end" id="currency-amnt-selling-new" name="currency-amnt-selling-new" autocomplete="off" placeholder="0.00" readonly>
                                                    <input type="hidden" class="form-control" id="true-currency-amnt-selling" name="true-currency-amnt-selling">
                                                </div>
                                            </div>

                                            {{-- Selling Transact - Total Amount --}}
                                            <div class="row align-items-center px-3 mt-3 mb-2">
                                                <div class="col-3 offset-5 text-end">
                                                    <strong>
                                                        {{ trans('labels.selling_trans_total_amnt') }}: &nbsp;
                                                    </strong>
                                                </div>
                                                {{-- <div class="col-8">
                                                    <input type="text" class="form-control" id="total-amnt-selling" name="total-amnt-selling" autocomplete="off" placeholder="0.00" readonly>
                                                    <input type="hidden" class="form-control" id="true-total-amnt-selling" name="true-total-amnt-selling">
                                                </div> --}}

                                                <div class="col-4">
                                                    <div class="col-12 text-end">
                                                        <span class="text-xl pt-3 font-bold text-black">PHP</span> &nbsp;
                                                        <span class="text-xl pt-3 font-bold text-black" id="total-amnt-selling" name="total-amnt-selling">0.00</span>
                                                        <input type="hidden" class="form-control" id="true-total-amnt-selling" name="true-total-amnt-selling">
                                                    </div>
                                                </div>

                                                {{-- <div class="col-4">
                                                    <input type="text" class="form-control" id="total-amnt-selling" name="total-amnt-selling" autocomplete="off" placeholder="0.00" readonly>
                                                    <input type="hidden" class="form-control" id="true-total-amnt-selling" name="true-total-amnt-selling">
                                                </div>
                                                <div class="col-4">
                                                    <button class="btn btn-primary" id="compute-selling-serial" type="button">Compute</button>
                                                </div> --}}
                                            </div>
                                        </div>

                                        <div class="col-12 border border-gray-300 rounded-bl rounded-br p-2">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <input type="hidden" id="user-id-selling" name="user-id-selling">
                                                </div>
                                                <div class="col-lg-6 text-end">
                                                    @can('access-permission', $menu_id)
                                                        <a class="btn btn-secondary" type="button" href="{{ URL::to('/addNewSellingTrans') }}">{{ trans('labels.back_action') }}</a>
                                                    @endcan

                                                    @can('add-permission', $menu_id)
                                                        <button class="btn btn-primary" type="button" id="transaction-confirm-button" disabled>{{ trans('labels.confirm_action') }}</button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        @include('UI.UX.customer_info_card')

                        <div class="card rounded-tr-sm rounded-tl-sm mt-3">
                            <div class="col-12 p-1 border border-gray-300 rounded-tr rounded-tl">
                                <div class="row align-items-center px-2 py-1">
                                    <div class="col-12">
                                        <span class="text-lg font-bold p-2 text-black">
                                            <i class='bx bxs-coin-stack' ></i>&nbsp;{{ trans('labels.selling_availble_bills') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_currency') }}</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_bill_amount') }}</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_pieces') }}</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_sub_total') }}</th>
                                        @if (session('time_toggle_status') == 0)
                                            <th class="text-center text-xs font-extrabold text-black p-1">Receipt Set</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($result['available_serials']) > 0)
                                        @php
                                            $original_count = count($result['available_serials']);
                                            $spliced_count = count($result['available_serials']->slice(0, 5));
                                            $display_count = $original_count - $spliced_count;
                                        @endphp

                                        @foreach ($result['available_serials']->slice(0, 5) as $available_serials)
                                            <tr class="denom-details-list-tabl" id="denom-details-list-table">
                                                <td class="text-center text-sm py-1">
                                                    {{ $available_serials->Currency }}
                                                </td>
                                                <td class="text-center text-sm py-1">
                                                    {{ $available_serials->BillAmount }}
                                                </td>
                                                <td class="text-center text-sm py-1">
                                                    {{ $available_serials->bill_amount_count }}
                                                </td>
                                                <td class="text-right text-sm font-bold py-1">
                                                    @php
                                                        $bill_amnt =  $available_serials->BillAmount;
                                                        $bill_amnt_cnt =  $available_serials->bill_amount_count;

                                                        $subtotal = $bill_amnt * $bill_amnt_cnt;
                                                    @endphp
                                                    {{ number_format($subtotal, 2 , '.' , ',') }}
                                                </td>
                                                @if (session('time_toggle_status') == 0)
                                                    <td class="text-center text-sm py-1">
                                                        {{ $available_serials->Rset }}
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach

                                        @if ($spliced_count < $original_count)
                                            <tr>
                                                <td class="text-center text-sm py-1" colspan="5">
                                                    @can('access-permission', $menu_id)
                                                        <a class="cursor-pointer" data-bs-toggle="modal" data-bs-target="#available-stocks-modal">
                                                            <span class="text-decoration-none text-gray-400 hover:text-black">
                                                                See all&nbsp;(<strong>{{ $display_count }}</strong> more item)
                                                            </span>
                                                        </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endif
                                    @else
                                        <td class="text-center text-td-buying text-sm py-3" colspan="12">
                                            <span class="buying-no-transactions text-lg">
                                                <strong>NO AVAILABLE STOCK</strong>
                                            </span>
                                        </td>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="text-center p-1" colspan="5">
                                            {{-- <button class="btn btn-primary btn-sm w-25" type="button" data-bs-toggle="modal" data-bs-target="#available-stocks-modal">
                                                <i class='bx bx-list-plus pb-1'></i> {{ trans('labels.see_more_action') }}
                                            </button> --}}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        @include('UI.UX.availbale_stock_modal')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('UI.UX.security_code')
    @include('UI.UX.customer_searching')

    {{-- Serial stock via AJAX --}}
    <div class="modal fade" id="serial-stock-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content serial-stock">
                @include('retail_selling_transact_admin.serial_stock_modal')
            </div>
        </div>
    </div>

    <div class="modal fade" id="serial-stock-modal-appended" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content serial-stock-appended">
                @include('retail_selling_transact_admin.serial_stock_modal_appended')
            </div>
        </div>
    </div>

@endsection

@section('admin_selling_scripts')
    @include('script.admin_retail_s_trans_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
