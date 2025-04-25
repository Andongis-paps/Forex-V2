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
                                <span class="counter-autoprint-selling-receipt d-none" id="counter">0</span>

                                <input type="hidden" id="full-url-serials-sold" value="{{ URL::to('/').'/'.'adminSoldSerialsDeets' }}">
                            </div>

                            <div class="col-12">
                                <input type="hidden" id="receipt-water-mark-pdf" value="{{ asset('images/watermark-sinag-logo.png') }}">
                                <hr>
                            </div>

                            <div class="col-6">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-list-ul'></i>&nbsp;{{ trans('labels.add_selling_trans_selling_transact_deets') }}
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                @can('print-permission', $menu_id)
                                                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#selling-transact-modal" id="printing-receipt-selling">{{ trans('labels.sold_curr_print_receipt') }}  &nbsp; <i class='bx bxs-file-doc'></i></button>
                                                @endcan

                                                @can('edit-permission', $menu_id)
                                                    <button class="btn btn-primary btn-sm btn-edit-details" id="update-admin-s-transact-details" type="button">
                                                    {{-- <button class="btn btn-primary btn-sm btn-edit-details" type="button" data-bs-toggle="modal" data-bs-target="#-modal"> --}}
                                                        Edit &nbsp;<i class='bx bx-edit-alt'></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                    @if (count($result['soldcurr_details']) > 0)
                                        @foreach ($result['soldcurr_details'] as $soldcurr_deets)
                                            <form class="mb-0" method="post" id="update-admin-selling-trans-details">
                                                @csrf
                                                <div class="col-12 py-2 px-1 border border-gray-300 rounded-tr rounded-tl" id="buying-container">
                                                    <div class="row align-items-center px-3">
                                                        <div class="col-3">
                                                            <strong>
                                                                {{ trans('labels.sold_curr_date_sold') }}: &nbsp;
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" id="sold-currency-date-sold" value="{{ $soldcurr_deets->DateSold }}" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                Transaction No.: &nbsp;
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
                                                        <div class="row align-items-center px-3 mt-2 d-none @if(session('time_toggle_status') == 1) d-none @endif" id="rset-container">
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
                                                        </div>
                                                    {{-- Buying Transaction - Receipt Set --}}

                                                    {{-- Buying Transaction - OR Number --}}
                                                        <div class="row align-items-center px-3 mt-2 d-none" id="or-number-container-deet">
                                                            <div class="col-3">
                                                                <strong>
                                                                    {{ trans('labels.buying_or_number') }} :
                                                                </strong>
                                                            </div>
                                                            <div class="col-9">
                                                                <input type="number" class="form-control" id="or-number-selling" name="or-number-selling" value="{{ $soldcurr_deets->ORNo }}" @if($soldcurr_deets->Rset == 'B') readonly @endif autocomplete="off" placeholder="Invoice No."  @if(session('time_toggle_status') == 0) @else   @endif>
                                                            </div>
                                                        </div>

                                                        <div class="row align-items-center px-3 mt-2 @if(session('time_toggle_status') == 1) d-none @endif" id="or-no-details-cont">
                                                            <div class="col-3">
                                                                <strong>
                                                                    {{ trans('labels.transact_invoice_#') }} : &nbsp;
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
                                                                    {{ trans('labels.transact_customer') }}&nbsp;:
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
                                                                    {{ trans('labels.sold_curr_customer') }}: &nbsp;
                                                                </strong>
                                                            </div>
                                                            <div class="col-9">
                                                                <input type="text" class="form-control" id="sold-currency-customer" name="sold-currency-customer" value="{{ $soldcurr_deets->FullName }}" readonly>
                                                                <input type="hidden" class="form-control" name="transact-customer-id" value="{{ $soldcurr_deets->CustomerID }}">
                                                            </div>
                                                        </div>
                                                    {{-- Buying Transaction - Customer Details --}}

                                                    <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                {{ trans('labels.sold_curr_currency') }}: &nbsp;
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" id="sold-currency-currency" value="{{ $soldcurr_deets->Currency }}" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                {{ trans('labels.sold_curr_curr_amnt') }}: &nbsp;
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" id="sold-currency-curr-amnt" value="{{ number_format($soldcurr_deets->CurrAmount, 2, '.', ',') }}" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                {{ trans('labels.sold_curr_rate_used') }}: &nbsp;
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="number" class="form-control" id="sold-currency-rate-used" name="sold-currency-rate-used" value="{{ number_format($soldcurr_deets->RateUsed, 2, '.', ',') }}" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                {{ trans('labels.sold_curr_amnt') }}: &nbsp;
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" id="sold-currency-total-amnt" name="sold-currency-total-amnt" value="{{ number_format($soldcurr_deets->AmountPaid, 2, '.', ',') }}" readonly>
                                                            <input type="hidden" class="form-control" name="true-sold-currency-total-amnt" value="{{ $soldcurr_deets->AmountPaid }}" readonly>
                                                            <input type="hidden" id="sold-currency-transacted-by" value="{{ $soldcurr_deets->Name }}">
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                Remarks: &nbsp;
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <textarea class="form-control" id="transact-remarks" name="transact-remarks" @if ($soldcurr_deets->Remarks == null) rows="1" placeholder="N/A" @else rows="3" @endif readonly>{{ $soldcurr_deets->Remarks }}</textarea>
                                                        </div>
                                                    </div>

                                                    @can('edit-permission', $menu_id)
                                                        <div class="row align-items-center px-3 mt-2">
                                                            <div class="col-3">
                                                                <strong>
                                                                    Print Count&nbsp; : &nbsp;
                                                                </strong>
                                                            </div>
                                                            <div class="col-9">
                                                                <input type="text" class="form-control" value="{{ $soldcurr_deets->Print }}" readonly>
                                                            </div>
                                                        </div>
                                                    @endcan

                                                    <input type="hidden" id="serials-scid" value="{{ $soldcurr_deets->ASCID }}" readonly>
                                                    <input type="hidden" id="selling-print-count" value="{{ $soldcurr_deets->Print }}" readonly>
                                                </div>
                                            </form>
                                        @endforeach
                                    @endif

                                    <div class="col-12 text-end p-2 border border-gray-300 rounded-bl rounded-br">
                                        <button class="btn btn-primary btn-sm d-none" type="button" id="update-transction-btn" data-bs-toggle="modal" data-bs-target="#update-admin-s-trans-sec-code-modal">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-list-ul'></i>&nbsp;{{ trans('labels.sold_serials_title') }}
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                @can('access-permission', $menu_id)
                                                    <a class="btn btn-primary text-white btn-sm" href="{{ route('admin_transactions.selling_transaction') }}">
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
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.sold_serials_rset') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.sold_serials_bill_amnt') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($result['sold_serial']) > 0)
                                                @foreach ($result['sold_serial'] as $sold_serials)
                                                    <div id="">
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
                                                            <td class="text-center text-sm p-1 serials-sold-rset">
                                                                {{ $sold_serials->Rset }}
                                                                <input type="hidden" class="form-control serials-sold-rset-input" id="serials-sold-rset-input" value="{{  $sold_serials->Rset }}">
                                                                <input type="hidden" class="form-control serials-sold-scid-input" id="serials-sold-scid-input" value="{{  $sold_serials->ASCID }}">
                                                                <input type="hidden" class="form-control serials-sold-date-input" id="serials-sold-date-input" value="{{  $sold_serials->DateSold }}">
                                                                <input type="hidden" class="form-control serials-sold-time-input" id="serials-sold-time-input" value="{{  $sold_serials->TimeSold }}">
                                                            </td>
                                                            <td class="text-right text-sm py-1 px-3 serials-sold-bill-amnt">
                                                                {{ number_format($sold_serials->BillAmount, 2, '.', ',') }}
                                                                <input type="hidden" class="form-control serials-sold-bill-amnt-input" id="serials-sold-bill-amnt-input" value="{{ number_format($sold_serials->BillAmount, 2, '.', ',') }}">
                                                            </td>

                                                            <input type="hidden" id="forex-ascid" value="{{ $sold_serials->ASCID }}">
                                                        </tr>
                                                    </div>
                                                @endforeach
                                            @endif
                                            <input type="hidden" name="currency-name" value="{{ $sold_serials->CurrencyID }}">
                                        </tbody>
                                    </table>

                                    <input type="hidden" id="sold-serials-url" data-soldserials="{{ route('admin_transactions.admin_s_transaction.details', ['id' => $sold_serials->ASCID]) }}">

                                    <div class="card-footer pe-2 pb-2 pt-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row">
                                            <div class="col-lg-6 offset-1 text-end pe-0">
                                            </div>
                                            <div class="col-lg-5 text-end ps-0">
                                                {{-- <a class="btn btn-primary text-white" type="button" href="{{ route('pendingserials', ['id' => $sold_serials->ASCID]) }}">
                                                    {{ trans('labels.sold_serials_add_serial') }}
                                                    <i class='menu-icon tf-icons bx bx-edit-alt text-white ms-1 me-0'></i>
                                                </a> --}}
                                                {{-- <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bill-add-modal"> {{ trans('labels.sold_serials_add_serial') }}<i class='menu-icon tf-icons bx bx-edit-alt text-white ms-1 me-0'></i></button> --}}
                                            </div>
                                        </div>
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
    {{-- <div class="modal fade" id="bill-add-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content add-bill">
                @include('serials.add_bill_modal')
            </div>
        </div>
    </div> --}}

    @include('UI.UX.security_code')
    @include('UI.UX.customer_searching')
    @include('UI.UX.update_admin_s_transact_details_modal')

    <div class="modal fade" id="selling-transact-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content add-denom">
                @include('serials.selling_tranasction_deets_modal')
            </div>
        </div>
    </div>

@endsection

@section('admin_selling_scripts')
    @include('script.admin_add_s_transact')
    @include('script.qz_tray_r_s_admin_receipt_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>

