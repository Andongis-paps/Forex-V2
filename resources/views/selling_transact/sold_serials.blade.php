@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <div class="row">
                            <div class="col-12">
                                @if($errors->any())
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        @foreach($errors->all() as $error)
                                            <span class="error-message error-bottom"> {{ $error }} </span>
                                        @endforeach
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                @if(session()->has('message'))
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        {{ session()->get('message') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <span class="counter-autoprint-selling-receipt d-none" id="counter">0</span>

                                <input type="hidden" id="full-url-serials-sold" value="{{ URL::to('/').'/'.'soldSerialsDeets' }}">
                            </div>

                            <div class="col-12">
                                <input type="hidden" id="receipt-water-mark-pdf" value="{{ asset('images/watermark-sinag-logo.png') }}">
                                <hr>
                            </div>

                            <div class="col-6">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tr rounded-tl">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-detail'></i>&nbsp;{{ trans('labels.add_selling_trans_selling_transact_deets') }}
                                                </span>
                                            </div>

                                            <div class="col-6 text-end">
                                                @can('print-permission', $menu_id)
                                                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#selling-transact-modal" id="printing-receipt-selling">{{ trans('labels.sold_curr_print_receipt') }}  &nbsp; <i class='bx bxs-file-doc'></i></button>
                                                @endcan

                                                @can('edit-permission', $menu_id)
                                                    <button class="btn btn-primary btn-sm btn-edit-details" id="update-s-transact-details" type="button">
                                                    {{-- <button class="btn btn-primary btn-sm btn-edit-details" type="button" data-bs-toggle="modal" data-bs-target="#transaction-details-modal"> --}}
                                                        Edit &nbsp;<i class='bx bx-edit-alt'></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                   </div>
                                    @if (count($result['soldcurr_details']) > 0)
                                        @foreach ($result['soldcurr_details'] as $soldcurr_deets)
                                            @php
                                                $formatted_rate = '';
                                                $decimal_places = (strpos((string) $soldcurr_deets->RateUsed, '.') !== false) ? strlen(explode('.', $soldcurr_deets->RateUsed)[1]) : 0;
                                            
                                                if ($decimal_places <= 2 && !in_array($soldcurr_deets->CurrencyID, [12, 14, 31])) {
                                                    $formatted_rate = number_format(floor($soldcurr_deets->RateUsed * 100) / 100, 2);
                                                } else if ($decimal_places <= 4 && in_array($soldcurr_deets->CurrencyID, [12, 14, 31])) {
                                                    $formatted_rate = number_format(floor($soldcurr_deets->RateUsed * 100000) / 100000, 4, '.', ',');
                                                }
                                            @endphp

                                            <form class="mb-0" method="post" id="update-selling-trans-details">
                                                @csrf
                                                <div class="col-12 py-2 px-1 border border-gray-300 rounded-tr rounded-tl" id="buying-container">
                                                    <div class="row align-items-center px-3">
                                                        <div class="col-3">
                                                            <strong>
                                                                {{ trans('labels.sold_curr_date_sold') }}:
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" id="sold-currency-date-sold" value="{{ $soldcurr_deets->DateSold }}" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                Transaction No.:
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" id="sold-currency-selling-number" value="{{ $soldcurr_deets->SellingNo }}" readonly>
                                                        </div>
                                                    </div>

                                                    {{-- <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                {{ trans('labels.sold_curr_receipt_no') }}: &nbsp;
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" id="sold-currency-receipt-number" value="{{ $soldcurr_deets->ReceiptNo }}" readonly>
                                                        </div>
                                                    </div> --}}

                                                    {{-- Buying Transaction - Receipt Set --}}
                                                        {{-- <div class="row align-items-center px-3 mt-2 d-none @if(session('time_toggle_status') == 1) d-none @endif" id="rset-container">
                                                            <div class="col-3">
                                                                <strong>
                                                                    Receipt Set :
                                                                </strong>
                                                            </div>

                                                            <div class="col-9">
                                                                <div class="row">
                                                                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                                        <input type="radio" class="btn-check" name="radio-rset" id="r-set-o" value="{{ trans('labels.buying_rset_o') }}" @if ($soldcurr_deets->Rset == 'O') checked @endif>
                                                                        <label class="btn btn-outline-primary" for="r-set-o">
                                                                            <strong>{{ trans('labels.buying_rset_o') }}</strong>
                                                                        </label>

                                                                        <input type="radio" class="btn-check" name="radio-rset" id="r-set-b" value="{{ trans('labels.buying_rset_b') }}" @if ($soldcurr_deets->Rset == 'B') checked @endif>
                                                                        <label class="btn btn-outline-primary" for="r-set-b">
                                                                            <strong>{{ trans('labels.buying_rset_b') }}</strong>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row align-items-center px-3 mt-2 @if(session('time_toggle_status') == 1) d-none @endif" id="rset-details-cont">
                                                            <div class="col-3">
                                                                <strong>
                                                                    {{ trans('labels.sold_curr_rset') }}: &nbsp;
                                                                </strong>
                                                            </div>
                                                            <div class="col-9">
                                                                <input type="text" class="form-control" id="sold-currency-rset" name="sold-currency-rset" value="{{ $soldcurr_deets->Rset }}" readonly>
                                                            </div>
                                                        </div> --}}
                                                    {{-- Buying Transaction - Receipt Set --}}

                                                    {{-- Buying Transaction - OR Number --}}
                                                        <div class="row align-items-center px-3 mt-2 d-none" id="or-number-container-deet">
                                                            <div class="col-3">
                                                                <strong>
                                                                    {{ trans('labels.buying_or_number') }}:
                                                                </strong>
                                                            </div>
                                                            <div class="col-9">
                                                                <input type="number" class="form-control" id="or-number-selling" name="or-number-selling" value="{{ $soldcurr_deets->ORNo }}" @if($soldcurr_deets->Rset == 'B') readonly @endif autocomplete="off" placeholder="Invoice No."  @if(session('time_toggle_status') == 0) @else   @endif>
                                                            </div>
                                                        </div>

                                                        <div class="row align-items-center px-3 mt-2 @if(session('time_toggle_status') == 1) d-none @endif" id="or-no-details-cont">
                                                            <div class="col-3">
                                                                <strong>
                                                                    {{ trans('labels.transact_invoice_#') }}:
                                                                </strong>
                                                            </div>
                                                            <div class="col-9">
                                                                <input type="text" class="form-control" id="sold-currency-or-number" name="sold-currency-or-number" value="@if ($soldcurr_deets->Rset == 'O'){{ $soldcurr_deets->ORNo }}@endif" @if ($soldcurr_deets->Rset == 'B') placeholder="N/A" @endif readonly>
                                                            </div>
                                                        </div>
                                                    {{-- Buying Transaction - OR Number --}}

                                                    {{-- Buying Transaction - Customer Details --}}
                                                        <div class="row align-items-center px-3 mt-2 d-none" id="customer-container">
                                                            <div class="col-3">
                                                                <strong>
                                                                    {{ trans('labels.transact_customer') }}:
                                                                </strong>
                                                            </div>
                                                            <div class="col-6">
                                                                <input type="text" class="form-control" id="customer-name-selected" value="{{ $soldcurr_deets->FullName }}" readonly>
                                                                <input type="hidden" class="form-control" id="customer-id-selected" name="customer-id-selected" value="" readonly>
                                                                <input type="hidden" class="form-control" id="customer-no-selected" name="customer-no-selected" value="" readonly>
                                                                <input type="hidden" class="form-control" id="customer-entry-id" name="customer-entry-id" value="" readonly>
                                                            </div>
                                                            <div class="col-3">
                                                                <div class="row pe-3">
                                                                    <button class="btn btn-primary btn-sm" id="customer-detail" type="button" data-bs-toggle="modal" data-bs-target="#customerDeetsModal">Customer</button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row align-items-center px-3 mt-2" id="customer-details-cont">
                                                            <div class="col-3">
                                                                <strong>
                                                                    {{ trans('labels.sold_curr_customer') }}:
                                                                </strong>
                                                            </div>
                                                            <div class="col-9">
                                                                <input type="text" class="form-control" id="sold-currency-customer" name="sold-currency-customer" value="{{ $soldcurr_deets->FullName }}" readonly>
                                                                <input type="hidden" class="form-control" id="transact-customer-id" name="transact-customer-id" value="{{ $soldcurr_deets->CustomerID }}">
                                                            </div>
                                                        </div>
                                                    {{-- Buying Transaction - Customer Details --}}

                                                    <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                {{ trans('labels.sold_curr_currency') }}:
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" id="sold-currency-currency" value="{{ $soldcurr_deets->Currency }}" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                {{ trans('labels.sold_curr_curr_amnt') }}:
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" id="sold-currency-curr-amnt" value="{{ number_format($soldcurr_deets->CurrAmount, 2, '.', ',') }}" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                {{ trans('labels.sold_curr_rate_used') }}:
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="number" class="form-control" id="sold-currency-rate-used" name="sold-currency-rate-used" value="{{ $formatted_rate }}" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                {{ trans('labels.sold_curr_amnt') }}:
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" id="sold-currency-total-amnt" name="sold-currency-total-amnt" value="{{ number_format($soldcurr_deets->AmountPaid, 2, '.', ',') }}" readonly>
                                                            <input type="hidden" class="form-control" name="true-sold-currency-total-amnt" value="{{ $soldcurr_deets->AmountPaid }}" readonly>
                                                            <input type="hidden" id="sold-currency-transacted-by" value="{{ $soldcurr_deets->Name }}">
                                                        </div>
                                                    </div>

                                                    {{-- <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                SC Total Amount:
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" id="sc-total-amount" name="sc-total-amount" value="{{ number_format($soldcurr_deets->CurrAmount * $sc_rate, 2, '.', ',') }}" readonly>
                                                        </div>
                                                    </div> --}}

                                                    @can('edit-permission', $menu_id)
                                                        <div class="row align-items-center px-3 mt-2">
                                                            <div class="col-3">
                                                                <strong>
                                                                    Print Count:
                                                                </strong>
                                                            </div>
                                                            <div class="col-9">
                                                                <input type="text" class="form-control" value="{{ $soldcurr_deets->Print }}" readonly>
                                                            </div>
                                                        </div>
                                                    @endcan

                                                    <input type="hidden" id="serials-scid" value="{{ $soldcurr_deets->SCID }}" readonly>
                                                    <input type="hidden" id="selling-print-count" value="{{ $soldcurr_deets->Print }}" readonly>
                                                </div>
                                            </form>
                                        @endforeach
                                    @endif

                                    <div class="col-12 text-end p-2 border border-gray-300 rounded-bl rounded-br">
                                        <a class="btn btn-secondary btn-sm" href="{{ route('branch_transactions.selling_transaction') }}">
                                            Back
                                        </a>
                                        <button class="btn btn-primary btn-sm d-none" type="button" id="update-transction-btn" data-bs-toggle="modal" data-bs-target="#update-s-trans-sec-code-modal">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tr rounded-tl">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-1 text-black">
                                                    <i class='bx bx-list-ul' ></i>&nbsp;{{ trans('labels.sold_serials_title') }}
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                @can('access-permission', $menu_id)
                                                    <a class="btn btn-primary btn-sm text-white" href="{{ route('branch_transactions.selling_transaction.add') }}">
                                                        {{ trans('labels.add_new_selling_trans_title') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table table-hover table-bordered" id="sold-serials-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.sold_serials_currency') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_serials') }}</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.sold_serials_rset') }}</th> --}}
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.sold_serials_bill_amnt') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">Selling Rate</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1">SC Rate</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($result['sold_serial']) > 0)
                                                @foreach ($result['sold_serial'] as $sold_serials)
                                                    <div id="transaction-details">
                                                        <tr class="transact-details-list-table" id="transact-details-list-table">
                                                            <td class="text-center text-sm p-1 serials-sold-currency">
                                                                {{ $sold_serials->Currency }}
                                                                <input type="hidden" class="form-control serials-sold-currency-input" id="serials-sold-currency-input" value="{{ Str::title($sold_serials->Currency) }}">
                                                            </td>
                                                            <td class="text-center text-sm p-1 serials-sold">
                                                                <strong>
                                                                    {{ $sold_serials->Serials }}
                                                                </strong>
                                                                <input type="hidden" class="form-control serials-sold-input" id="serials-sold-input" value="{{ $sold_serials->Serials }}">
                                                            </td>
                                                            {{-- <td class="text-center text-sm p-1 serials-sold-rset"> --}}
                                                                {{-- {{ $sold_serials->Rset }} --}}
                                                                <input type="hidden" class="form-control serials-sold-rset-input" id="serials-sold-rset-input" value="{{  $sold_serials->Rset }}">
                                                                <input type="hidden" class="form-control serials-sold-scid-input" id="serials-sold-scid-input" value="{{  $sold_serials->SCID }}">
                                                                <input type="hidden" class="form-control serials-sold-date-input" id="serials-sold-date-input" value="{{  $sold_serials->DateSold }}">
                                                                <input type="hidden" class="form-control serials-sold-time-input" id="serials-sold-time-input" value="{{  $sold_serials->TimeSold }}">
                                                            {{-- </td> --}}
                                                            <td class="text-right text-sm py-1 px-3 serials-sold-bill-amnt">
                                                                {{ number_format($sold_serials->BillAmount, 2, '.', ',') }}
                                                                <input type="hidden" class="form-control serials-sold-bill-amnt-input" id="serials-sold-bill-amnt-input" value="{{ number_format($sold_serials->BillAmount, 2, '.', ',') }}">
                                                            </td>
                                                            <td class="text-right text-sm py-1 px-3 serials-sold-bill-amnt">
                                                                <strong>{{ number_format(floor($sold_serials->RateUsed * 10000) / 10000, 4, '.', ',') }}</strong>
                                                            </td>
                                                            {{-- <td class="text-right text-sm py-1 px-3 serials-sold-bill-amnt">
                                                                <strong>{{ number_format($sold_serials->RIBRate, 2, '.', ',') }}</strong>
                                                            </td> --}}

                                                            <input type="hidden" id="forex-scid" value="{{ $sold_serials->SCID }}">
                                                        </tr>
                                                    </div>
                                                @endforeach
                                            @endif
                                            <input type="hidden" name="currency-name" value="{{ $sold_serials->CurrencyID }}">
                                        </tbody>
                                    </table>

                                    <input type="hidden" id="sold-serials-url" data-soldserials="{{ route('branch_transactions.selling_transaction.details', ['id' => $sold_serials->SCID]) }}">

                                    <div class="col-12 text-end p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row">
                                            <div class="col-lg-6 offset-1 text-end pe-0">
                                            </div>
                                            <div class="col-lg-5 text-end ps-0">
                                                {{-- <a class="btn btn-primary text-white" type="button" href="{{ route('pendingserials', ['id' => $sold_serials->SCID]) }}">
                                                    {{ trans('labels.sold_serials_add_serial') }}
                                                    <i class='menu-icon tf-icons bx bx-edit-alt text-white ms-1 me-0'></i>
                                                </a> --}}
                                                {{-- <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bill-add-modal"> {{ trans('labels.sold_serials_add_serial') }}<i class='menu-icon tf-icons bx bx-edit-alt text-white ms-1 me-0'></i></button> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tr rounded-tl">
                                        <div class="row">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-1 text-black">
                                                    <i class='bx bx-detail'></i>&nbsp;{{ trans('labels.serials_serial_summary') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover" id="bill-summary-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_denom_amount') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_denom_multiplier') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_denom_total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($result['denom_details'] as $denom_details)
                                                <div id="denom-details">
                                                    <tr class="denom-details-list-tabl" id="denom-details-list-table">
                                                        <td class="text-right pe-3 text-sm py-1 px-3">
                                                            {{ number_format($denom_details->BillAmount, 2 , '.' , ',') }}
                                                        </td>
                                                        <td class="text-center px-3 text-sm p-1">
                                                            {{ $denom_details->bill_count }}
                                                            <input type="hidden" class="form-control bill-count-input" value="{{ $denom_details->bill_count }}">
                                                        </td>
                                                        <td class="text-end text-sm py-1 px-3">
                                                            <strong>
                                                                {{ number_format($denom_details->sub_total, 2 , '.' , ',') }}
                                                            </strong>
                                                            <input type="hidden" class="form-control bill-total-input" id="bill-total-input" value="{{ $denom_details->sub_total }}">
                                                        </td>
                                                        {{-- <td class="text-end text-sm py-1 px-3">
                                                            <div class="col-12 read-only-rate" id="read-only-rate">
                                                                <strong>
                                                                    {{ number_format(floor($denom_details->SinagRateBuying * 10000) / 10000, 4, '.', ',') }}
                                                                </strong>
                                                            </div>

                                                            <div class="col-12 update-rate d-none" id="update-rate">
                                                                <input class="form-control current-rates text-right" name="current-rates" id="current-rates" type="number" value="{{ number_format(floor($denom_details->SinagRateBuying * 10000) / 10000, 4, '.', ',') }}">
                                                            </div>

                                                            <input type="hidden" class="form-control bill-rate-input" value="{{ number_format(floor($denom_details->SinagRateBuying * 10000) / 10000, 4, '.', ',') }}">
                                                        </td> --}}
                                                    </tr>
                                                </div>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-td-buying text-sm py-3" colspan="4">
                                                        <span class="buying-no-transactions text-lg">
                                                            <strong>NO AVAILABLE DENOMINATION</strong>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>

                                        {{-- <input type="hidden" id="buying-rate-input" value="{{ $buying_rate }}"> --}}
                                    </table>
                                    <div class="card-footer p-2 border border-gray-300 rounded-tl rounded-tr">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add new bill via AJAX --}}
    <div class="modal fade" id="bill-add-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content add-bill">
                @include('serials.add_bill_modal')
            </div>
        </div>
    </div>

    <div class="modal fade" id="selling-transact-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content add-denom">
                @include('serials.selling_tranasction_deets_modal')
            </div>
        </div>
    </div>

    @include('UI.UX.security_code')
    @include('UI.UX.customer_searching')
    @include('UI.UX.update_s_transact_details_modal')
@endsection

@section('selling_scripts')
    @include('script.add_s_transact_scripts')
    @include('script.qz_tray_s_receipt_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>

