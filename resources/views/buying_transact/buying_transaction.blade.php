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
                                <input type="hidden" id="full-url-serials" value="{{ URL::to('/').'/'.'buyingTransaction' }}">

                                <span class="counter-autoprint-buying-receipt d-none" id="counter-buying">0</span>
                            </div>

                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-6">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tr rounded-tl">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-detail'></i>&nbsp;{{ trans('labels.buying_transaction_title') }}
                                                </span>
                                            </div>

                                            <div class="col-6 text-end">
                                                @can('print-permission', $menu_id)
                                                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#buying-transact-modal" id="printing-receipt-buying">{{ trans('labels.transact_print_buiying_receipt') }}  &nbsp; <i class='bx bxs-file-doc'></i></button>
                                                @endcan

                                                @can('edit-permission', $menu_id)
                                                    <button class="btn btn-primary btn-sm btn-edit-details" id="update-b-transact-details" type="button">
                                                    {{-- <button class="btn btn-primary btn-sm btn-edit-details" type="button" data-bs-toggle="modal" data-bs-target="#transaction-details-modal"> --}}
                                                        Edit &nbsp;<i class='bx bx-edit-alt'></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                    @if (count($result['transact_details']) > 0)
                                        @foreach ($result['transact_details'] as $trans_deets)
                                            @php
                                                $formatted_rate = '';
                                                $decimal_places = (strpos((string) $trans_deets->RateUsed, '.') !== false) ? strlen(explode('.', $trans_deets->RateUsed)[1]) : 0;
                                            
                                                if ($decimal_places <= 2 && !in_array($trans_deets->CurrencyID, [12, 14, 31])) {
                                                    $formatted_rate = number_format(floor($trans_deets->RateUsed * 100) / 100, 2);
                                                } else if ($decimal_places <= 4 && in_array($trans_deets->CurrencyID, [12, 14, 31])) {
                                                    $formatted_rate = number_format(floor($trans_deets->RateUsed * 100000) / 100000, 4, '.', ',');
                                                }
                                            @endphp
                                            
                                            <form class="m-0" method="post" id="update-buying-trans-details">
                                                @csrf
                                                <div class="col-12 py-2 px-1 border border-gray-300" id="buying-container">
                                                    <div class="row align-items-center px-3">
                                                        <div class="col-3">
                                                            <strong>
                                                                {{ trans('labels.transact_date') }}&nbsp : &nbsp;
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" id="buying-receipt-transact-date" name="transact-date" value="{{ $trans_deets->TransactionDate }}" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                {{ trans('labels.transact_#') }}: &nbsp;
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" id="buying-receipt-transact-number" name="transact-number" value="{{ $trans_deets->TransactionNo }}" readonly>
                                                        </div>
                                                    </div>

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
                                                                        <input type="radio" class="btn-check" name="radio-rset" id="r-set-o" value="{{ trans('labels.buying_rset_o') }}" @if ($trans_deets->Rset == 'O') checked @endif>
                                                                        <label class="btn btn-outline-primary" for="r-set-o">
                                                                            <strong>{{ trans('labels.buying_rset_o') }}</strong>
                                                                        </label>

                                                                        <input type="radio" class="btn-check" name="radio-rset" id="r-set-b" value="{{ trans('labels.buying_rset_b') }}" @if ($trans_deets->Rset == 'B') checked @endif>
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
                                                                    Receipt Set : &nbsp;
                                                                </strong>
                                                            </div>
                                                            <div class="col-9">
                                                                <input type="text" class="form-control" id="buying-receipt-rset" name="transact-receipt-rset" value="{{ $trans_deets->Rset }}" readonly>
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
                                                                <input type="number" class="form-control" id="or-number-buying" name="or-number-buying" value="{{ $trans_deets->ORNo }}" @if($trans_deets->Rset == 'B') readonly @endif autocomplete="off" placeholder="Invoice No."  @if(session('time_toggle_status') == 0) @else   @endif>
                                                                {{-- <input type="number" class="form-control" id="or-number-buying" name="or-number-buying" value="{{ $trans_deets->ORNo }}" @if($trans_deets->Rset == 'B') readonly @endif autocomplete="off" placeholder="Invoice No."  @if(session('time_toggle_status') == 0) @else   @endif> --}}
                                                            </div>
                                                        </div>

                                                        <div class="row align-items-center px-3 mt-2 @if(session('time_toggle_status') == 1) d-none @endif" id="or-no-details-cont">
                                                            <div class="col-3">
                                                                <strong>
                                                                    {{ trans('labels.transact_invoice_#') }}: &nbsp;
                                                                </strong>
                                                            </div>
                                                            <div class="col-9">
                                                                <input type="text" class="form-control" id="buying-receipt-receipt-number" name="transact-receipt-no" value="@if ($trans_deets->Rset == 'O') {{ $trans_deets->ORNo }} @endif" @if ($trans_deets->Rset == 'B') placeholder="N/A" @endif readonly>
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
                                                                <input type="text" class="form-control" id="customer-name-selected" value="{{ $trans_deets->FullName }}" readonly>
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
                                                                    {{ trans('labels.transact_customer') }}: &nbsp;
                                                                </strong>
                                                            </div>
                                                            <div class="col-9">
                                                                <input type="text" class="form-control" id="buying-receipt-customer" name="transact-customer" value="{{ $trans_deets->FullName }}" readonly>
                                                                <input type="hidden" class="form-control" id="transact-customer-id" name="transact-customer-id" value="{{ $trans_deets->CustomerID }}">
                                                            </div>
                                                        </div>

                                                    {{-- Buying Transaction - Customer Details --}}
                                                    <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                {{ trans('labels.transact_curr') }}: &nbsp;
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" id="buying-receipt-currency" name="transact-currency" value="{{ $trans_deets->Currency }}" readonly>
                                                            <input type="hidden" id="buying-receipt-currency-abbrev" value="{{ $trans_deets->CurrAbbv }}">
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                {{ trans('labels.transact_type') }}:
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" id="buying-receipt-transact-type" name="transact-type" value="{{ $trans_deets->TransType }}" readonly>
                                                        </div>
                                                    </div>

                                                    @if ($trans_deets->TransType == 'DPOFX')
                                                        <div class="row align-items-center px-3 mt-2" id="mtcn-container">
                                                            <div class="col-3">
                                                                <strong>
                                                                    {{ trans('labels.transfer_forex_mtcn') }}:
                                                                </strong>
                                                            </div>
                                                            <div class="col-9">
                                                                <input type="text" class="form-control" id="buying-receipt-mtcn" name="transact-mtcn" value="{{ $trans_deets->MTCN }}" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="row align-items-center px-3 mt-2 d-none" id="mtcn-cont">
                                                            <div class="col-3">
                                                                <strong>
                                                                    {{ trans('labels.transfer_forex_mtcn') }}:
                                                                </strong>
                                                            </div>
                                                            <div class="col-9">
                                                                <input type="text" class="form-control" id="buying-receipt-mtcn" name="new-transact-mtcn" value="{{ $trans_deets->MTCN }}">
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                Total Bill Amount: &nbsp;
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" id="buying-receipt-currency-amount" name="transact-currency-amount" value="{{ number_format($trans_deets->CurrencyAmount, 2 , '.' , ',') }}" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                Rate: &nbsp;
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            
                                                            <input type="text" class="form-control" id="buying-receipt-rate" name="transact-rate" value="{{ $formatted_rate }}" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center px-3 mt-2">
                                                        <div class="col-3">
                                                            <strong>
                                                                Total Amount: &nbsp;
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input type="text" class="form-control" id="buying-receipt-total-amount" name="transact-total-amount" value="{{ number_format($trans_deets->Amount , 2 , '.' , ',') }}" readonly>
                                                            <input type="hidden" id="og-total-amount" value="{{ number_format($trans_deets->Amount , 2 , '.' , ',') }}">
                                                            <input type="hidden" class="form-control" id="buying-receipt-transacted-by" value="{{ $trans_deets->Name }}">
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
                                                                <input type="text" class="form-control" value="{{ $trans_deets->Print }}" readonly>
                                                            </div>
                                                        </div>
                                                    @endcan

                                                    <input type="hidden" id="serials-ftdid" value="{{ $trans_deets->FTDID }}">
                                                    <input type="hidden" id="buying-print-count" value="{{ $trans_deets->Print }}">
                                                </div>
                                            </form>
                                        @endforeach
                                    @endif

                                    <div class="col-12 text-end p-2 border border-gray-300 rounded-bl rounded-br">
                                        <a class="btn btn-secondary btn-sm" href="{{ route('branch_transactions.buying_transaction') }}">Back</a>
                                        <button class="btn btn-primary btn-sm d-none" type="button" id="update-transction-btn" data-bs-toggle="modal" data-bs-target="#update-b-trans-sec-code-modal">
                                            Update
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tr rounded-tl">
                                        <div class="row align-items-cent">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-1 text-black">
                                                    <i class='bx bx-detail'></i>&nbsp;{{ trans('labels.serials_serials') }}
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                @can('add-permission', $menu_id)
                                                    <a class="btn btn-primary btn-sm text-white" href="{{ route('branch_transactions.buying_transaction.add') }}">
                                                        {{ trans('labels.forex_return_buying') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1">{{ trans('labels.serials_currency') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1">{{ trans('labels.serials_curr_denom') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1">{{ trans('labels.serials_serials') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1">{{ trans('labels.serials_trans_type') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($result['forex_serials'] as $forex_serials)
                                                <div id="transaction-details">
                                                    <tr class="transact-details-list-table" id="transact-details-list-table">
                                                        <td class="text-center text-sm p-1">
                                                            {{ $forex_serials->Currency }}
                                                        </td>
                                                        <td class="text-right text-sm py-1 px-3">
                                                            {{ number_format($forex_serials->BillAmount , 2 , '.' , ',') }}
                                                        </td>
                                                        <td class="text-center text-sm p-1">
                                                            <strong>
                                                                {{ $forex_serials->Serials }}
                                                            </strong>
                                                        </td>
                                                        <td class="text-center text-sm p-1">
                                                            {{ $forex_serials->TransType }}
                                                        </td>
                                                        <input type="hidden" name="forex-ftdid" value="{{ $forex_serials->FTDID }}">
                                                    </tr>
                                                </div>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-td-buying text-sm py-3" colspan="4">
                                                        <span class="buying-no-transactions text-lg">
                                                            <strong>NO AVAILABLE SERIALS</strong>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforelse

                                            @if (count($result['forex_serials']) > 0)
                                                <input type="hidden" name="currency-name" value="{{ $forex_serials->CurrencyID }}">
                                                <input type="hidden" id="forex-url" data-forexurl="{{ route('branch_transactions.buying_transaction.details', ['id' => $forex_serials->FTDID]) }}">
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="py-1" colspan="12">
                                                    {{ $result['forex_serials']->links() }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row">
                                            <div class="col-lg-6 offset-1 text-end pe-0">
                                                {{-- <a class="btn btn-secondary text-white" type="button" href="{{ URL::to('/addNewBuyingTrans') }}">{{ trans('labels.back_action') }}</a> --}}
                                            </div>
                                            <div class="col-lg-5 text-end ps-0">
                                                <a class="btn btn-primary text-white btn-sm" type="button" @if (count($result['forex_serials']) > 0) href="{{ route('branch_transactions.buying_transaction.pending_serials', ['id' => $forex_serials->FTDID]) }}" @else disabled @endif>
                                                    {{ trans('labels.serials_add_serials') }}
                                                    <i class='menu-icon tf-icons bx bx-edit-alt text-white ms-1 me-0'></i>
                                                </a>
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
                                            <div class="col-6 text-end">
                                                @can('edit-permission', $menu_id)
                                                    <button class="btn btn-primary btn-sm btn-edit-details" id="update-b-rate" type="button">
                                                    {{-- <button class="btn btn-primary btn-sm btn-edit-details" type="button" data-bs-toggle="modal" data-bs-target="#transaction-details-modal"> --}}
                                                        Edit &nbsp;<i class='bx bx-edit-alt'></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover" id="bill-summary-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_denom_amount') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_denom_multiplier') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_denom_total') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_denom_sinag_buying_rate') }}</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1">SC Rate</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($result['denom_details'] as $denom_details)
                                                <form method="post" action="{{ route('branch_transactions.buying_transaction.update_rate') }}" id="update-rates">
                                                    @csrf
                                                    <div id="denom-details">
                                                        <tr class="denom-details-list-tabl" id="denom-details-list-table">
                                                            <td class="text-right pe-3 text-sm py-1 px-3">
                                                                {{ number_format($denom_details->BillAmount, 2 , '.' , ',') }}
                                                                <input type="hidden" class="form-control denom-id" value="{{ $denom_details->DenomID }}">
                                                                <input type="hidden" class="form-control bill-amount-input" name="bill-amount-input" value="{{ $denom_details->BillAmount }}">
                                                            </td>
                                                            <td class="text-center px-3 text-sm p-1">
                                                                {{ $denom_details->Multiplier }}
                                                                <input type="hidden" class="form-control bill-count-input" value="{{ $denom_details->Multiplier }}">
                                                            </td>
                                                            <td class="text-end text-sm py-1 px-3">
                                                                <strong>
                                                                    {{ number_format($denom_details->Total, 2 , '.' , ',') }}
                                                                </strong>
                                                                <input type="hidden" class="form-control bill-total-input" id="bill-total-input" value="{{ $denom_details->Total }}">
                                                            </td>
                                                            <td class="text-end text-sm py-1 px-3">
                                                                <div class="col-12 read-only-rate" id="read-only-rate">
                                                                    <strong>
                                                                        {{ number_format(floor($denom_details->SinagRateBuying * 10000) / 10000, 4, '.', ',') }}
                                                                    </strong>
                                                                </div>
    
                                                                <div class="col-12 update-rate d-none" id="update-rate">
                                                                    <input class="form-control current-rates text-right" name="current-rates" id="current-rates" type="number" value="{{ number_format(floor($denom_details->SinagRateBuying * 10000) / 10000, 4, '.', ',') }}">
                                                                </div>
    
                                                                <input type="hidden" class="form-control bill-rate-input" value="{{ number_format(floor($denom_details->SinagRateBuying * 10000) / 10000, 4, '.', ',') }}">
                                                            </td>
                                                        </tr>
                                                    </div>
                                                </form>
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

                                        <input type="hidden" id="buying-rate-input" value="{{ $buying_rate }}">
                                    </table>
                                    <div class="card-footer p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row">
                                            <div class="col-lg-6 offset-1 text-end pe-0">

                                            </div>
                                            <div class="col-lg-5 text-end ps-0">
                                                <button class="btn btn-primary btn-sm d-none" id="update-new-rates" data-bs-toggle="modal" data-bs-target="#update-b-rate-sec-code-modal" type="button">Update</button>
                                                {{-- <a class="btn btn-primary text-white" type="button" id="button-add-denom" data-bs-toggle="modal" data-bs-target="#denom-add-modal">
                                                    {{ trans('labels.serials_denom_crud') }}
                                                    <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                </a> --}}
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

    {{-- Add new denom via AJAX --}}
    @if ($trans_deets->TTID != 4)
        <div class="modal fade" id="denom-add-modal" tabindex="-1" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content add-denom">
                    @include('serials.add_denom_modal')
                </div>
            </div>
        </div>
    @endif

    <div class="modal fade" id="buying-transact-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content add-denom">
                @include('serials.buying_tranasction_deets_modal')
            </div>
        </div>
    </div>

    @include('UI.UX.security_code')
    @include('UI.UX.customer_searching')
    @include('UI.UX.update_b_rate_modal')
    @include('UI.UX.update_b_transact_details_modal')
@endsection

@section('buying_scripts')
    @include('script.add_b_transact_scripts')
    @include('script.qz_tray_b_receipt_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>


