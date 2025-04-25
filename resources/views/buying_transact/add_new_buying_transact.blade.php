@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <form class="m-0" method="GET" action="{{ route('branch_transactions.buying_transaction') }}">
                                            <div class="row align-items-center">
                                                <div class="col-3">
                                                    <span class="text-lg font-bold p-2 text-black">
                                                        <i class='bx bxs-dollar-circle'></i>&nbsp;{{ trans('labels.buying_trans_title') }}
                                                    </span>
                                                </div>

                                                <div class="col-6">
                                                    <div class="row">
                                                        @can('edit-permission', $menu_id)
                                                            <div class="col-10 p-0">
                                                                <div class="col-12 @if (request()->query('radio-search-type') == 2) d-none @endif" id="date-range-searching">
                                                                    <div class="input-group input-group-sm">
                                                                        <input class="form-control" name="date-from-search" id="date-from-search" type="date" value="{{ request()->query('date-from-search') }}">
                                                                        <input class="form-control" name="date-to-search" id="date-to-search" type="date" value="{{ request()->query('date-to-search') }}">
                                                                        <button class="btn btn-primary btn-sm shadow-none" type="submit">Search</button>
                                                                    </div>
                                                                </div>

                                                                <div class="col-12 @if (request()->query('radio-search-type') == 2) @else d-none @endif " id="invoice-searching">
                                                                    <div class="input-group input-group-sm">
                                                                        <input class="form-control" name="invoice-search" id="invoice-search" type="number" value="{{ request()->query('invoice-search') }}">
                                                                        <button class="btn btn-primary btn-sm shadow-none" type="submit">Search</button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-2">
                                                                <div class="row ps-3">
                                                                    <button class="btn btn-secondary btn-sm shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#accordion-filter" aria-expanded="true" aria-controls="accordion-filter"><i class='bx bx-filter-alt bx-xs me-1'></i>Filter</button>
                                                                </div>
                                                            </div>
                                                        @endcan
                                                    </div>
                                                </div>

                                                <div class="col-3 text-end">
                                                    <button class="btn btn-primary btn-edit-details btn-sm text-white" id="sc-details-button" data-bs-toggle="modal" data-bs-target="#sc-details-modal" type="button">
                                                        View SC Details&nbsp;<i class='menu-icon tf-icons bx bx-show-alt text-white ms-1 me-0'></i>
                                                    </button>
                                                    @can('add-permission', $menu_id)
                                                        <a class="btn btn-primary btn-sm text-white" href="{{ route('branch_transactions.buying_transaction.add') }}"  type="button">
                                                            {{ trans('labels.forex_return_buying') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                        </a>
                                                    @endcan
                                                </div>
                                            </div>

                                            <div class="row justify-content-center">
                                                <div class="col-6">
                                                    <div id="accordion" class="accordion accordion-without-arrow">
                                                        <div class="accordion-item !bg-transparent">
                                                            <div id="accordion-filter" class="accordion-collapse collapse" data-bs-parent="#accordion">
                                                                <div class="accordion-body">
                                                                    <div class="row text-center">
                                                                        <div class="btn-group btn-group-sm" role="group" aria-label="Basic radio toggle button group">
                                                                            <input type="radio" class="btn-check" name="radio-search-type" id="date-range" value="1" @if (request()->query('date-to-search') || request()->query('date-from-search')) checked @else checked @endif>
                                                                            <label class="btn btn-outline-primary" for="date-range">
                                                                                <strong>Date Range</strong>
                                                                            </label>

                                                                            <input type="radio" class="btn-check" name="radio-search-type" id="invoice-no" value="2" @if (request()->query('invoice-search')) checked @endif>
                                                                            <label class="btn btn-outline-primary" for="invoice-no">
                                                                                <strong>Invoice No.</strong>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_date') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_#') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_invoice_#') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_customer_name') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_type') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_curr') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_curr_amnt') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Rate</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_amnt') }}</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap @if (session('time_toggle_status') == 1) d-none @else @endif">{{ trans('labels.transact_rset') }}</th> --}}
                                                @can('edit-permission', $menu_id)
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Processed By</th>
                                                @endcan
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Status</th>
                                                @can('access-permission', $menu_id)
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Action</th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                                @php
                                                    $count = [];
                                                @endphp
                                                
                                                @if (count($result['transact_details']) > 0)
                                                    @foreach ($result['transact_details'] as $transact_details)
                                                        @php
                                                            $formatted_rates_arr = [];

                                                            if (isset($transact_details->Voided) && $transact_details->Voided == 0) {
                                                                $count[] = $transact_details->Voided;
                                                            }

                                                            $report_status = $transact_details->HasTicket == 0 && $transact_details->Voided == 0;

                                                            $formatted_rate = '';
                                                            $rates_array = explode(",", $transact_details->rates);
                                                            $denoms_array = explode(",", $transact_details->denoms);
                                                        @endphp

                                                        @if (session('time_toggle_status') == 1)
                                                            <tr class="transact-details-list-table @if($transact_details->Voided == 1) !bg-red-100 @endif" id="tranact-details-list-table">
                                                                <td class="text-center text-xs p-1">
                                                                    {{ $transact_details->TransactionDate }}
                                                                </td>
                                                                <td class="text-center text-xs p-1">
                                                                    {{ $transact_details->TransactionNo }}
                                                                </td>
                                                                <td class="text-center text-xs p-1">
                                                                    @if ($transact_details->Rset == 'O')
                                                                        {{ $transact_details->ORNo }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td class="text-center text-xs p-1">
                                                                    {{ $transact_details->FullName }}
                                                                </td>
                                                                <td class="text-center text-xs p-1">
                                                                    {{ $transact_details->TransType }}
                                                                </td>
                                                                <td class="text-center text-xs p-1 whitespace-nowrap">
                                                                    {{ $transact_details->Currency }}
                                                                </td>
                                                                <td class="text-right text-xs py-1 px-3">
                                                                    {{ number_format($transact_details->CurrencyAmount, 2, '.', ',') }}
                                                                </td>
                                                                 {{-- <td class="text-right text-xs py-1 px-3">
                                                                    {{ $formatted_rate }}
                                                                </td> --}}
                                                                {{-- <td class="text-right text-xs py-1 px-3" data-bs-toggle="popover" @if (count($formatted_rates_arr) > 1) data-bs-content="{!! $transact_details->breakdown !!}" @endif data-bs-placement="bottom" data-bs-custom-class="popover-dark" tabindex="0">
                                                                    {{ \Illuminate\Support\Str::limit(implode(', ',$formatted_rates_arr), 10, '...') }}
                                                                </td> --}}
                                                                <td class="text-right text-xs py-1 pe-2">
                                                                    @foreach ($denoms_array as $key => $value)
                                                                        {{ $value }} - (<strong>{{ $rates_array[$key] }}</strong>)<br>
                                                                    @endforeach
                                                                </td>
                                                                <td class="text-right text-xs py-1 px-3">
                                                                    {{ number_format($transact_details->Amount, 2, '.', ',') }}
                                                                    @if ($transact_details->Voided == 0)
                                                                        <input type="hidden" class="currency-amount" id="currency-amount" value="{{ $transact_details->Amount }}">
                                                                    @else
                                                                        <input type="hidden" class="currency-amount" id="currency-amount" value="0">
                                                                    @endif
                                                                </td>
                                                                @can('edit-permission', $menu_id)
                                                                    <td class="text-center text-xs p-1">
                                                                        {{ $transact_details->encoder }}
                                                                    </td>
                                                                @endcan
                                                                <td class="text-center text-xs p-1">
                                                                    @if ($transact_details->Voided == 1)
                                                                        <span class="badge bg-label-danger text-xs font-bold">
                                                                            Void
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-label-success font-bold">
                                                                            <i class='bx bx-check-double'></i>
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center p-1 whitespace-nowrap">
                                                                    @can('access-permission', $menu_id)
                                                                        @if ($transact_details->Voided == 0)
                                                                            <a class="btn btn-primary button-edit button-edit-trans-details text-white btn-popover btn-details pe-2" href="{{ route('branch_transactions.buying_transaction.details', ['id' => $transact_details->FTDID]) }}">
                                                                                <i class='bx bx-detail'></i>
                                                                            </a>
                                                                            @if ($transact_details->pending_serials == 1)
                                                                                <span class="badge position-absolute top-75 start-90 translate-middle pending-badge-danger"><i class='bx bxs-info-circle bx-flashing badge-icon'></i></span>
                                                                            @else
                                                                                <span class="badge position-absolute top-75 start-90 translate-middle pending-badge-success"><i class='bx bx-check-double badge-icon'></i></span>
                                                                            @endif
                                                                        @endif
                                                                    @endcan
                                                                    @if ($report_status)
                                                                        <button class="btn btn-primary btn-warning report-error-modal-btn btn-popover btn-css-report pe-2" data-id="{{ $transact_details->FTDID }}" data-menuid="{{ $menu_id }}" type="button" data-bs-toggle="modal" data-bs-target="#report-error-modal">
                                                                            <i class='bx bx-error'></i>
                                                                        </button>
                                                                    @endif
                                                                    @can('delete-permission', $menu_id)
                                                                        @if ($transact_details->Voided == 0)
                                                                            {{-- <a class="btn btn-primary button-edit button-edit-trans-details" id="button-trans-details" href="{{ route('branch_transactions.buying_transaction.edit', ['id' => $transact_details->FTDID]) }}">
                                                                                <i class='menu-icon tf-icons bx bx-edit-alt text-white'></i>
                                                                            </a> --}}
                                                                            <a class="btn btn-primary button-delete button-delete-trans-details btn-popover btn-delete pe-2" data-bs-toggle="modal" data-bs-target="#deleteBuyingTransactModal" data-transdetailsid="{{ $transact_details->FTDID }}">
                                                                                <i class='bx bx-trash text-white'></i>
                                                                            </a>
                                                                        @endif
                                                                    @endcan
                                                                </td>
                                                            </tr>
                                                        @else
                                                            <tr class="transact-details-list-table @if($transact_details->Voided == 1) !bg-red-100 @else @endif" id="transact-details-list-table">
                                                                <td class="text-center text-xs p-1">
                                                                    {{ $transact_details->TransactionDate }}
                                                                </td>
                                                                <td class="text-center text-xs p-1">
                                                                    {{ $transact_details->TransactionNo }}
                                                                </td>
                                                                <td class="text-center text-xs p-1">
                                                                    @if ($transact_details->Rset == 'O')
                                                                        {{ $transact_details->ORNo }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td class="text-center text-xs p-1">
                                                                    {{ $transact_details->FullName }}
                                                                </td>
                                                                <td class="text-center text-xs p-1">
                                                                    {{ $transact_details->TransType }}
                                                                </td>
                                                                <td class="text-center text-xs p-1 whitespace-nowrap">
                                                                    {{ $transact_details->Currency }}
                                                                </td>
                                                                <td class="text-right text-xs py-1 px-3">
                                                                    {{ number_format($transact_details->CurrencyAmount, 2, '.', ',') }}
                                                                </td>
                                                                {{-- <td class="text-right text-xs py-1 px-3">
                                                                    {{ $formatted_rate }}
                                                                </td> --}}
                                                                {{-- <td class="text-right text-xs py-1 px-3" data-bs-toggle="popover" @if (count($formatted_rates_arr) > 1) data-bs-content="{!! $transact_details->breakdown !!}" @endif data-bs-placement="bottom" data-bs-custom-class="popover-dark" tabindex="0">
                                                                    {{ \Illuminate\Support\Str::limit(implode(', ',$formatted_rates_arr), 10, '...') }}
                                                                </td> --}}
                                                                <td class="text-right text-xs py-1 pe-2">
                                                                    @foreach ($denoms_array as $key => $value)
                                                                        {{ $value }} - (<strong>{{ $rates_array[$key] }}</strong>)<br>
                                                                    @endforeach
                                                                </td>
                                                                <td class="text-right text-xs py-1 px-3">
                                                                    {{ number_format($transact_details->Amount, 2, '.', ',') }}
                                                                    @if ($transact_details->Voided == 0)
                                                                        <input type="hidden" class="currency-amount" id="currency-amount" value="{{ $transact_details->Amount }}">
                                                                    @else
                                                                        <input type="hidden" class="currency-amount" id="currency-amount" value="0">
                                                                    @endif
                                                                </td>
                                                                @can('edit-permission', $menu_id)
                                                                    <td class="text-center text-xs p-1">
                                                                        {{ $transact_details->encoder }}
                                                                    </td>
                                                                @endcan
                                                                <td class="text-center text-xs p-1">
                                                                    @if ($transact_details->Voided == 1)
                                                                        <span class="badge bg-label-danger font-bold">
                                                                            Void
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-label-success font-bold">
                                                                            <i class='bx bx-check-double'></i>
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center p-1 whitespace-nowrap">
                                                                    @can('access-permission', $menu_id)
                                                                        @if ($transact_details->Voided == 0)
                                                                            <a class="btn btn-primary button-edit button-edit-trans-details text-white btn-popover btn-details pe-2" href="{{ route('branch_transactions.buying_transaction.details', ['id' => $transact_details->FTDID]) }}">
                                                                                <i class='bx bx-detail'></i>
                                                                            </a>
                                                                            @if ($transact_details->pending_serials == 1)
                                                                                <span class="badge position-absolute top-75 start-90 translate-middle pending-badge-danger"><i class='bx bxs-info-circle bx-flashing badge-icon'></i></span>
                                                                            @else
                                                                                <span class="badge position-absolute top-75 start-90 translate-middle pending-badge-success"><i class='bx bx-check-double badge-icon'></i></span>
                                                                            @endif
                                                                        @endif
                                                                    @endcan
                                                                    @if ($report_status)
                                                                        <button class="btn btn-primary btn-warning report-error-modal-btn btn-popover btn-css-report pe-2" data-id="{{ $transact_details->FTDID }}" data-menuid="{{ $menu_id }}" type="button" data-bs-toggle="modal" data-bs-target="#report-error-modal">
                                                                            <i class='bx bx-error'></i>
                                                                        </button>
                                                                    @endif
                                                                    @can('delete-permission', $menu_id)
                                                                        @if ($transact_details->Voided == 0)
                                                                            {{-- <a class="btn btn-primary button-edit button-edit-trans-details" id="button-trans-details" href="{{ route('branch_transactions.buying_transaction.edit', ['id' => $transact_details->FTDID]) }}">
                                                                                <i class='menu-icon tf-icons bx bx-edit-alt text-white'></i>
                                                                            </a> --}}
                                                                            <a class="btn btn-primary button-delete button-delete-trans-details btn-popover btn-delete pe-2" data-bs-toggle="modal" data-bs-target="#deleteBuyingTransactModal" data-transdetailsid="{{ $transact_details->FTDID }}">
                                                                                <i class='bx bx-trash text-white'></i>
                                                                            </a>
                                                                        @endif
                                                                    @endcan
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center p-1 text-xs py-3" colspan="13">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO AVAILABLE TRANSACTIONS</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </div>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="text-center p-1 py-1 whitespace-nowrap" colspan="1">
                                                    <span class="text-sm" id="transaction-count">
                                                        {{ trans('labels.transact_count') }}: <span class="font-bold" id="trans-count">{{ count($count) }}</span>
                                                    </span>
                                                </td>
                                                <td class="text-center p-1" colspan="7">
                                                </td>
                                                <td class="text-right px-3 py-1 whitespace-nowrap" colspan="1">
                                                    <span class="text-sm" id="transaction-total-amount">
                                                        {{ trans('labels.transact_total_amount') }}: <strong><span class="text-sm">PHP</span></strong>&nbsp; <span class="font-bold" id="trans-amount"></span>
                                                    </span>
                                                </td>
                                                <td class="text-center p-1" colspan="3">
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <div class="col-12 py-1 px-3 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                {{ $result['transact_details']->links() }}
                                            </div>
                                        </div>
                                    </div>

                                    @include('UI.UX.security_code')
                                    @include('buying_transact.sc_details_modal')
                                    @include('buying_transact.report_error_modal')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('buying_scripts')
    @include('script.add_b_transact_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
