@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <div class="row justify-content-center">


                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <form class="m-0" method="GET" action="{{ route('branch_transactions.selling_transaction') }}">
                                            <div class="row align-items-center">
                                                <div class="col-3">
                                                    <span class="text-lg font-bold p-2 text-black">
                                                        <i class='bx bxs-dollar-circle'></i>&nbsp;{{ trans('labels.selling_transact') }}
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
                                                    @can('access-permission', $menu_id)
                                                        <a class="btn btn-primary btn-sm text-white" href="{{ route('branch_transactions.selling_transaction.add') }}" type="button">
                                                            {{ trans('labels.add_new_selling_trans_title') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
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
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.add_selling_trans_date') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.add_selling_trans_selling_no') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Invoice No.</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.add_selling_customer_name') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.add_selling_trans_currency') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.add_selling_trans_curr_amnt') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.add_selling_trans_rate_used') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.add_selling_trans_total_amount') }}</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap @if(session('time_toggle_status') == 1) d-none @else @endif">{{ trans('labels.add_selling_trans_rset') }}</th> --}}
                                                @can('edit-permission', $menu_id)
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Processed By</th>
                                                @endcan
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Status</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($result['selling_transact_details']) > 0)
                                                @foreach ($result['selling_transact_details'] as $selling_transact_details)
                                                    @php
                                                        $report_status = $selling_transact_details->HasTicket == 0 && $selling_transact_details->Voided == 0;

                                                        $formatted_rate = '';
                                                        $yes = '';
                                                        $rate_used = $selling_transact_details->whole_rate + $selling_transact_details->decimal_rate;
                                                        $decimal_places = (strpos((string) $rate_used, '.') !== false) ? strlen(explode('.', $rate_used)[1]) : 0;

                                                        if ($decimal_places <= 2 && !in_array($selling_transact_details->CurrencyID, [12, 14, 31])) {
                                                            $formatted_rate = number_format(floor($rate_used * 100) / 100, 2);
                                                        } else if ($decimal_places <= 4 && in_array($selling_transact_details->CurrencyID, [12, 14, 31])) {
                                                            $yes = "yes";
                                                            $formatted_rate = number_format(floor($rate_used * 10000) / 10000, 4, '.', ',');
                                                        }
                                                    @endphp

                                                    @if(session('time_toggle_status') == 1)
                                                        <tr class="selling-transact-details-list-table  @if($selling_transact_details->Voided == 1) !bg-red-100 @else @endif"" id="selling-transact-details-list-table">
                                                            <td class="text-center text-sm p-1">
                                                                {{ $selling_transact_details->DateSold }}
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                {{ $selling_transact_details->SellingNo }}
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                @if ($selling_transact_details->Rset == 'O')
                                                                    {{ $selling_transact_details->ORNo }}
                                                                @else
                                                                    0
                                                                @endif
                                                            </td>
                                                            <td class="text-center text-sm p-1 whitespace-nowrap">
                                                                {{ $selling_transact_details->FullName }}
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                {{ $selling_transact_details->Currency }}
                                                            </td>
                                                            <td class="text-right text-sm py-1 px-3">
                                                                {{ number_format($selling_transact_details->CurrAmount, 2, '.', ',') }}
                                                            </td>
                                                            <td class="text-right text-sm py-1 px-3">
                                                                {{ $formatted_rate }}
                                                            </td>
                                                            <td class="text-right text-sm py-1 px-3">
                                                                {{ number_format($selling_transact_details->AmountPaid, 2, '.', ',') }}
                                                                @if ($selling_transact_details->Voided == 0)
                                                                    <input type="hidden" class="total-amountpaid-selling" id="total-amountpaid-selling" value="{{ $selling_transact_details->AmountPaid }}">
                                                                @else
                                                                    <input type="hidden" class="total-amountpaid-selling" id="total-amountpaid-selling" value="0">
                                                                @endif
                                                            </td>
                                                            @can('edit-permission', $menu_id)
                                                                <td class="text-center text-xs p-1">
                                                                    {{ $selling_transact_details->encoder }}
                                                                </td>
                                                            @endcan
                                                            <td class="text-center text-sm p-1">
                                                                @if ($selling_transact_details->Voided == 1)
                                                                    <span class="badge bg-label-danger font-bold">
                                                                        Void
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-label-success font-bold">
                                                                        <i class='bx bx-check-double'></i>
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                @can('access-permission', $menu_id)
                                                                    @if ($selling_transact_details->Voided == 0)
                                                                        <a class="btn btn-primary button-edit button-edit-trans-details text-white btn-popover btn-details pe-2" href="{{ route('branch_transactions.selling_transaction.details', ['id' => $selling_transact_details->SCID]) }}">
                                                                            <i class='bx bx-detail'></i>
                                                                        </a>
                                                                    @endif
                                                                @endcan

                                                                @if ($report_status)
                                                                    <button class="btn btn-primary pe-2 btn-warning report-error-modal-btn btn-popover btn-css-report" data-id="{{ $selling_transact_details->SCID }}"  data-menuid="{{ $menu_id }}" type="button" data-bs-toggle="modal" data-bs-target="#report-error-modal">
                                                                        <i class='bx bx-error'></i>
                                                                    </button>
                                                                @endif

                                                                @can('delete-permission', $menu_id)
                                                                    @if ($selling_transact_details->Voided == 0)
                                                                        <a class="btn btn-primary button-delete button-delete-selling-trans-details btn-popover btn-delete pe-2" data-bs-toggle="modal" data-bs-target="#deleteSellingTransactModal" data-sellingtransdetailsid="{{ $selling_transact_details->SCID }}">
                                                                            <i class='bx bx-trash text-white'></i>
                                                                        </a>
                                                                    @endif
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @else
                                                        <tr class="selling-transact-details-list-table  @if($selling_transact_details->Voided == 1) !bg-red-100 @else @endif"" id="selling-transact-details-list-table">
                                                            <td class="text-center text-sm p-1">
                                                                {{ $selling_transact_details->DateSold }}
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                {{ $selling_transact_details->SellingNo }}
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                @if ($selling_transact_details->Rset == 'O')
                                                                    {{ $selling_transact_details->ORNo }}
                                                                @else
                                                                    0
                                                                @endif
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                {{ $selling_transact_details->FullName }}
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                {{ $selling_transact_details->Currency }}
                                                            </td>
                                                            <td class="text-right text-sm py-1 px-3">
                                                                {{ number_format($selling_transact_details->CurrAmount, 2, '.', ',') }}
                                                            </td>
                                                            <td class="text-right text-sm py-1 px-3">
                                                                {{ $formatted_rate }}
                                                            </td>
                                                            <td class="text-right text-sm py-1 px-3">
                                                                {{ number_format($selling_transact_details->AmountPaid, 2, '.', ',') }}
                                                                @if ($selling_transact_details->Voided == 0)
                                                                    <input type="hidden" class="total-amountpaid-selling" id="total-amountpaid-selling" value="{{ $selling_transact_details->AmountPaid }}">
                                                                @else
                                                                    <input type="hidden" class="total-amountpaid-selling" id="total-amountpaid-selling" value="0">
                                                                @endif
                                                            </td>
                                                            {{-- <td class="text-center text-sm p-1">
                                                                {{ $selling_transact_details->Rset }}
                                                            </td> --}}
                                                            @can('edit-permission', $menu_id)
                                                                <td class="text-center text-xs p-1">
                                                                    {{ $selling_transact_details->encoder }}
                                                                </td>
                                                            @endcan
                                                            <td class="text-center text-sm p-1">
                                                                @if ($selling_transact_details->Voided == 1)
                                                                    <span class="badge bg-label-danger font-bold">
                                                                        Void
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-label-success font-bold">
                                                                        <i class='bx bx-check-double'></i>
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                @can('access-permission', $menu_id)
                                                                    @if ($selling_transact_details->Voided == 0)
                                                                        <a class="btn btn-primary button-edit button-edit-trans-details text-white btn-popover btn-details pe-2" href="{{ route('branch_transactions.selling_transaction.details', ['id' => $selling_transact_details->SCID]) }}">
                                                                            <i class='bx bx-detail'></i>
                                                                        </a>
                                                                    @endif
                                                                @endcan

                                                                @if ($report_status)
                                                                    <button class="btn btn-primary pe-2 btn-warning report-error-modal-btn btn-popover btn-css-report" data-id="{{ $selling_transact_details->SCID }}"  data-menuid="{{ $menu_id }}" type="button" data-bs-toggle="modal" data-bs-target="#report-error-modal">
                                                                        <i class='bx bx-error'></i>
                                                                    </button>
                                                                @endif

                                                                @can('delete-permission', $menu_id)
                                                                    @if ($selling_transact_details->Voided == 0)
                                                                        <a class="btn btn-primary button-delete button-delete-selling-trans-details btn-popover btn-delete pe-2" data-bs-toggle="modal" data-bs-target="#deleteSellingTransactModal" data-sellingtransdetailsid="{{ $selling_transact_details->SCID }}">
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
                                                    <td class="text-center text-td-buying text-sm py-3" colspan="12">
                                                        <span class="buying-no-transactions text-lg">
                                                            <strong>NO AVAILABLE TRANSACTIONS FOR TODAY</strong>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="text-center text-sm py-1 whitespace-nowrap" colspan="1">
                                                    <span class="" id="transaction-count">
                                                        {{ trans('labels.add_selling_trans_count') }}: <span class="font-bold" id="selling-trans-count">{{ count($result['selling_transact_details']) }}</span>
                                                    </span>
                                                </td>
                                                <td class="text-center" colspan="6">
                                                </td>
                                                <td class="text-right text-sm py-1 px-3 whitespace-nowrap" colspan="1">
                                                    <span class="transaction-total-amount" id="transaction-total-amount">
                                                        Total: <strong><span class="text-sm">PHP</span></strong> &nbsp; <span class="table-footer-texts font-bold" id="selling-trans-amount"></span>
                                                    </span>
                                                </td>
                                                <td colspan="3"></td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <div class="col-12 py-1 px-3 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                {{ $result['selling_transact_details']->links() }}
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

@section('selling_scripts')
    @include('script.add_s_transact_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
