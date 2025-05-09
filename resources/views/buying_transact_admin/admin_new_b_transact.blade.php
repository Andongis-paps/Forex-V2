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
                                        <form class="m-0" method="GET" action="{{ route('admin_transactions.buying_transaction') }}">
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
                                                    {{-- <button class="btn btn-primary btn-edit-details btn-sm text-white" id="sc-details-button" data-bs-toggle="modal" data-bs-target="#sc-details-modal" type="button">
                                                        View SC Details&nbsp;<i class='menu-icon tf-icons bx bx-show-alt text-white ms-1 me-0'></i>
                                                    </button> --}}
                                                    @can('add-permission', $menu_id)
                                                        <a class="btn btn-primary btn-sm text-white" href="{{ route('admin_transactions.admin_b_transaction.add') }}">
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
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Remarks</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap @if (session('time_toggle_status') == 1) d-none @else @endif">{{ trans('labels.transact_rset') }}</th> --}}
                                                @can('edit-permission', $menu_id)
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Processed By</th>
                                                @endcan
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Status</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.forex_serials_title') }}</th> --}}
                                                @can('access-permission', $menu_id)
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Action</th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div id="">
                                                @if (count($result['transact_details']) > 0)
                                                    @if (session('time_toggle_status') == 1)
                                                        @foreach ($result['transact_details'] as $transact_details)
                                                        @php
                                                        $formatted_rates_arr = [];

                                                        if (isset($transact_details->Voided) && $transact_details->Voided == 0) {
                                                            $count[] = $transact_details->Voided;
                                                        }

                                                        $report_status = $transact_details->HasTicket == 0 && $transact_details->Voided == 0;

                                                        // $formatted_rate = '';
                                                        // $rate_used = $transact_details->whole_rate + $transact_details->decimal_rate;
                                                        // $decimal_places = (strpos((string) $rate_used, '.') !== false) ? strlen(explode('.', $rate_used)[1]) : 0;

                                                        // if ($decimal_places <= 2 && !in_array($transact_details->CurrencyID, [12, 14, 31])) {
                                                        //     $formatted_rate = number_format(floor($rate_used * 100) / 100, 2);
                                                        // } else if ($decimal_places <= 4 && in_array($transact_details->CurrencyID, [12, 14, 31])) {
                                                        //     $formatted_rate = number_format(floor($rate_used * 100000) / 100000, 4, '.', ',');
                                                        // }

                                                        $formatted_rate = '';
                                                        
                                                        foreach ($transact_details->rates as $rate) {
                                                            $whole_number = floor($rate);
                                                            $decimal = $rate - $whole_number;
                                                            $segmented_rate = $whole_number + $decimal;

                                                            $decimal_places = (strpos((string) $segmented_rate, '.') !== false) ? strlen(explode('.', $segmented_rate)[1]) : 0;

                                                            if ($decimal_places <= 2 && !in_array($transact_details->CurrencyID, [12, 14, 31])) {
                                                                $formatted_rate = number_format(floor($segmented_rate * 100) / 100, 2);
                                                            } else if ($decimal_places <= 4 && in_array($transact_details->CurrencyID, [12, 14, 31])) {
                                                                $formatted_rate = number_format(floor($segmented_rate * 100000) / 100000, 4, '.', ',');
                                                            }

                                                            $formatted_rates_arr[] = $formatted_rate;
                                                        }
                                                    @endphp

                                                            <tr class="transact-details-list-table @if($transact_details->Voided == 1) !bg-red-100 @endif" id="transact-details-list-table">
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
                                                                        0
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
                                                                <td class="text-right text-xs p-1">
                                                                    {{ number_format($transact_details->CurrencyAmount, 2, '.', ',') }}
                                                                </td>
                                                                 {{-- <td class="text-right text-xs py-1 px-3">
                                                                    {{ $formatted_rate }}
                                                                </td> --}}
                                                                {{-- <td class="text-right text-xs py-1 px-3" data-bs-toggle="popover" @if (count($formatted_rates_arr) > 1) data-bs-content="{!! $transact_details->breakdown !!}" @endif data-bs-placement="bottom" data-bs-custom-class="popover-dark" tabindex="0">
                                                                    {{ \Illuminate\Support\Str::limit(implode(', ',$formatted_rates_arr), 10, '...') }}
                                                                </td> --}}
                                                                <td class="text-right text-xs py-1 pe-2">
                                                                    @foreach (explode(', ', $transact_details->breakdown) as $value)
                                                                        {!! $value !!}<br>
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
                                                                <td class="text-center text-xs p-1" data-bs-toggle="popover" data-bs-content="{!! $transact_details->Remarks == null ? 'No remarks.' : $transact_details->Remarks !!}" data-bs-placement="bottom" data-bs-custom-class="popover-dark" tabindex="0">
                                                                    @if ($transact_details->Remarks == null)
                                                                        -
                                                                    @else
                                                                        {{ \Illuminate\Support\Str::limit($transact_details->Remarks, 14, '...') }}
                                                                    @endif
                                                                </td>
                                                                @can('edit-permission', $menu_id)
                                                                    <td class="text-center text-xs p-1" data-bs-toggle="popover" data-bs-content="{!! $transact_details->encoder == null ? 'No remarks.' : $transact_details->encoder !!}" data-bs-placement="bottom" data-bs-custom-class="popover-dark" tabindex="0">
                                                                        @if ($transact_details->encoder == null)
                                                                            -
                                                                        @else
                                                                            {{ \Illuminate\Support\Str::limit($transact_details->encoder, 10, '...') }}
                                                                        @endif
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
                                                                            <a class="btn btn-primary button-edit button-edit-trans-details text-white btn-popover btn-details pe-2" href="{{ route('admin_transactions.admin_b_transaction.details', ['id' => $transact_details->AFTDID]) }}">
                                                                                <i class='bx bx-detail'></i>
                                                                            </a>

                                                                            @if (is_array($transact_details->serials) && count(array_filter($transact_details->serials, 'strlen')) !== count($transact_details->serials))
                                                                                <span class="badge position-absolute top-75 start-90 translate-middle pending-badge-danger"><i class='bx bxs-info-circle bx-flashing badge-icon'></i></span>
                                                                            @else
                                                                                <span class="badge position-absolute top-75 start-90 translate-middle pending-badge-success"><i class='bx bx-check-double badge-icon'></i></span>
                                                                            @endif
                                                                        @endif
                                                                    @endcan
                                                                    @can('delete-permission', $menu_id)
                                                                        @if ($transact_details->Voided == 0)

                                                                            {{-- <a class="btn btn-primary button-edit button-edit-trans-details" id="button-trans-details" href="{{ route('editbuyingtransact', ['id' => $transact_details->AFTDID]) }}">
                                                                                <i class='menu-icon tf-icons bx bx-edit-alt text-white'></i>
                                                                            </a> --}}
                                                                            <a class="btn btn-primary button-delete button-delete-trans-details btn-popover btn-delete pe-2" data-transdetailsid="{{ $transact_details->AFTDID }}">
                                                                                <i class='bx bx-trash text-white'></i>
                                                                            </a>
                                                                        @endif
                                                                    @endcan
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        @foreach ($result['transact_details'] as $transact_details)
                                                            <tr class="transact-details-list-table @if($transact_details->Voided == 1) !bg-red-100 @endif" id="transact-details-list-table">
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
                                                                        0
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
                                                                <td class="text-right text-xs p-1">
                                                                    {{ number_format($transact_details->CurrencyAmount, 2, '.', ',') }}
                                                                </td>
                                                                 {{-- <td class="text-right text-xs py-1 px-3">
                                                                    {{ $formatted_rate }}
                                                                </td> --}}
                                                                {{-- <td class="text-right text-xs py-1 px-3" data-bs-toggle="popover" @if (count($formatted_rates_arr) > 1) data-bs-content="{!! $transact_details->breakdown !!}" @endif data-bs-placement="bottom" data-bs-custom-class="popover-dark" tabindex="0">
                                                                    {{ \Illuminate\Support\Str::limit(implode(', ',$formatted_rates_arr), 10, '...') }}
                                                                </td> --}}
                                                                <td class="text-right text-xs py-1 pe-2">
                                                                    @foreach (explode(', ', $transact_details->breakdown) as $value)
                                                                        {!! $value !!}<br>
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
                                                                <td class="text-center text-xs p-1 " data-bs-toggle="popover" data-bs-content="{!! $transact_details->Remarks == null ? 'No remarks.' : $transact_details->Remarks !!}" data-bs-placement="bottom" data-bs-custom-class="popover-dark" tabindex="0">
                                                                    @if ($transact_details->Remarks == null)
                                                                        -
                                                                    @else
                                                                        {{ \Illuminate\Support\Str::limit($transact_details->Remarks, 14, '...') }}
                                                                    @endif
                                                                </td>
                                                                @can('edit-permission', $menu_id)
                                                                    <td class="text-center text-xs p-1" data-bs-toggle="popover" data-bs-content="{!! $transact_details->encoder == null ? 'No remarks.' : $transact_details->encoder !!}" data-bs-placement="bottom" data-bs-custom-class="popover-dark" tabindex="0">
                                                                        @if ($transact_details->encoder == null)
                                                                            -
                                                                        @else
                                                                            {{ \Illuminate\Support\Str::limit($transact_details->encoder, 10, '...') }}
                                                                        @endif
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
                                                                            <a class="btn btn-primary button-edit button-edit-trans-details text-white btn-popover btn-details pe-2" href="{{ route('admin_transactions.admin_b_transaction.details', ['id' => $transact_details->AFTDID]) }}">
                                                                                <i class='bx bx-detail'></i>
                                                                            </a>

                                                                            @if (is_array($transact_details->serials) && count(array_filter($transact_details->serials, 'strlen')) !== count($transact_details->serials))
                                                                                <span class="badge position-absolute top-75 start-90 translate-middle pending-badge-danger"><i class='bx bxs-info-circle bx-flashing badge-icon'></i></span>
                                                                            @else
                                                                                <span class="badge position-absolute top-75 start-90 translate-middle pending-badge-success"><i class='bx bx-check-double badge-icon'></i></span>
                                                                            @endif
                                                                        @endif
                                                                    @endcan
                                                                    @can('delete-permission', $menu_id)
                                                                        @if ($transact_details->Voided == 0)

                                                                            {{-- <a class="btn btn-primary button-edit button-edit-trans-details" id="button-trans-details" href="{{ route('editbuyingtransact', ['id' => $transact_details->AFTDID]) }}">
                                                                                <i class='menu-icon tf-icons bx bx-edit-alt text-white'></i>
                                                                            </a> --}}
                                                                            <a class="btn btn-primary button-delete button-delete-trans-details btn-popover btn-delete pe-2" data-transdetailsid="{{ $transact_details->AFTDID }}">
                                                                                <i class='bx bx-trash text-white'></i>
                                                                            </a>
                                                                        @endif
                                                                    @endcan
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                @else
                                                    <tr>
                                                        <td class="text-center p-1 text-xs py-3" colspan="13">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO AVAILABLE TRANSACTIONS FOR TODAY</strong>
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
                                                        {{ trans('labels.transact_count') }}: <span class="font-bold" id="trans-count">{{ count($result['transact_details']) }}</span>
                                                    </span>
                                                </td>
                                                <td class="text-center p-1" colspan="7">
                                                </td>
                                                <td class="text-right py-1 px-3 whitespace-nowrap" colspan="1">
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('admin_buying_scripts')
    @include('script.admin_add_b_transact_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
