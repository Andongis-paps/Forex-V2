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
                                <hr>
                            </div>

                            {{-- Transact Summary --}}
                            <div class="col-6">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center ps-2">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-list-ul' ></i>&nbsp;{{ trans('labels.selling_admin_details') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- @php
                                        $string = Str::title('SINAG PAWNSHOP CORP.');
                                        // Replace with non-breaking space for HTML
                                        $formattedString = preg_replace('/(?<!^)(?=[A-Z])/', '&nbsp;', $string);
                                    @endphp

                                    <p>{!! $formattedString !!}</p> --}}

                                    @foreach ($result['bills_sold_to_mnl'] as $bills_sold_to_mnl)
                                        <div class="col-12 p-2 border border-gray-300">
                                            <div class="row align-items-center px-3 mt-1">
                                                <div class="col-4">
                                                    <strong>
                                                        {{ trans('labels.transact_#') }} :
                                                    </strong>
                                                </div>
                                                <div class="col-8">
                                                    <input class="form-control" name="transact-no" type="text" value="{{ $bills_sold_to_mnl->STMNo }}" readonly>
                                                </div>
                                            </div>
                                            <div class="row align-items-center px-3 my-2">
                                                <div class="col-4">
                                                    <strong>
                                                        {{ trans('labels.transact_customer_name') }} :
                                                    </strong>
                                                </div>
                                                <div class="col-8">
                                                    <input class="form-control" name="customer-name" type="text" value="{{ $bills_sold_to_mnl->FullName }}" readonly>
                                                </div>
                                            </div>
                                            <div class="row align-items-center px-3 my-2">
                                                <div class="col-4">
                                                    <strong>
                                                        {{ trans('labels.transact_rset') }} :
                                                    </strong>
                                                </div>
                                                <div class="col-8">
                                                    <input class="form-control" name="transact-rset" type="text" value="{{ $bills_sold_to_mnl->RSet }}" readonly>
                                                </div>
                                            </div>
                                            <div class="row align-items-center px-3 my-2">
                                                <div class="col-4">
                                                    <strong>
                                                        {{ trans('labels.transact_date_sold') }} :
                                                    </strong>
                                                </div>
                                                <div class="col-8">
                                                    <input class="form-control" name="transact-date-sold" type="text" value="{{ $bills_sold_to_mnl->DateSold }}" readonly>
                                                </div>
                                            </div>
                                            <div class="row align-items-center px-3 my-2">
                                                <div class="col-4">
                                                    <strong>
                                                        {{ trans('labels.transact_time_sold') }} :
                                                    </strong>
                                                </div>
                                                <div class="col-8">
                                                    <input class="form-control" name="transact-time-sold" type="text" value="{{ $bills_sold_to_mnl->TimeSold }}" readonly>
                                                </div>
                                            </div>
                                            <div class="row align-items-center px-3 my-2">
                                                <div class="col-4">
                                                    <strong>
                                                        {{ trans('labels.transact_exchange_amnt') }} :
                                                    </strong>
                                                </div>
                                                <div class="col-8">
                                                    <input class="form-control text-right" name="transact-total-xchng-amnt" type="text" value="{{ number_format($bills_sold_to_mnl->TotalExchangeAmount, 2,'.',',') }}" readonly>
                                                </div>
                                            </div>
                                            <div class="row align-items-center px-3 my-2">
                                                <div class="col-4">
                                                    <strong>
                                                        {{ trans('labels.transact_principal_amnt') }} :
                                                    </strong>
                                                </div>
                                                <div class="col-8">
                                                    <input class="form-control text-right" name="transact-total-principal-amnt" type="text" value="{{ number_format($bills_sold_to_mnl->TotalPrincipal, 2,'.',',') }}" readonly>
                                                </div>
                                            </div>
                                            <div class="row align-items-center px-3 mb-1">
                                                <div class="col-4">
                                                    <strong>
                                                        {{ trans('labels.transact_gain_loss') }} :
                                                    </strong>
                                                </div>
                                                <div class="col-4 offset-4 text-right">
                                                    @php
                                                        $gain_loss = $bills_sold_to_mnl->TotalGainLoss;
                                                    @endphp

                                                    <strong>
                                                        <input class="form-control text-white text-right py-1 @if ($gain_loss >= 0) success-badge-custom @else danger-badge-custom @endif" name="transact-total-gain-loss" id="total-gain-loss" data-gainloss="{{ $gain_loss }}" value="{!! $gain_loss >= 0 ? trans('labels.gain_symbol') . number_format($gain_loss, 2, '.', ',') . ' ▲' : number_format($gain_loss, 2, '.', ',') . ' ▼' !!}" readonly>
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center ps-2">
                                            <div class="col-12 text-end">
                                                @can('access-permission', $menu_id)
                                                    <a class="btn btn-secondary btn-sm" href="{{ route('admin_transactions.bulk_selling') }}">
                                                        {{ trans('labels.back_action') }}
                                                    </a>
                                                @endcan
                                                {{-- <button class="btn btn-primary">
                                                    <i class='bx bxs-printer ps-0 pe-1'></i>{{ trans('labels.selling_admin_print_receipts') }}
                                                </button> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Transact Breakdown --}}
                            <div class="col-6">
                                @forelse ($result['selling_trans_details'] as $selling_trans_details)
                                    <div class="col mb-3">
                                        <div class="card">
                                            <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                                <div class="row align-items-center ps-2">
                                                    <div class="col-6">
                                                        <span class="text-lg text-black">
                                                            <strong>
                                                                {{ $selling_trans_details->CompanyName }}
                                                            </strong>
                                                        </span>
                                                    </div>
                                                    <div class="col-2 text-end">
                                                        <span class="text-sm">
                                                            <strong class="text-red-500">
                                                                No.
                                                            </strong>
                                                        </span>
                                                        <span class="text-md">
                                                            <strong class="text-red-500">
                                                                {{ str_pad($selling_trans_details->FormSeries, 6, '0', STR_PAD_LEFT) }}
                                                            </strong>
                                                        </span>
                                                    </div>
                                                    <div class="col-4 text-end">
                                                        @can('access-permission', $menu_id)
                                                            <button class="btn btn-primary btn-sm print-selling-admin-receipt ps-2" data-stmdid="{{ $STMDID }}" data-companyid="{{ $selling_trans_details->CompanyID }}">
                                                                <i class='bx bxs-printer'></i>&nbsp;&nbsp;{{ trans('labels.selling_admin_print_receipt') }}
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>

                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center text-sm font-extrabold text-black p-1 whitespace-nowrap">Currency</th>
                                                        <th class="text-center text-sm font-extrabold text-black p-1 whitespace-nowrap">Amount</th>
                                                        <th class="text-center text-sm font-extrabold text-black p-1 whitespace-nowrap">Selling Rate</th>
                                                        <th class="text-center text-sm font-extrabold text-black p-1 whitespace-nowrap">Total (PESO)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $sum_php_amnt = 0;
                                                    @endphp

                                                    @foreach ($selling_trans_details->Currency as $currency_id)
                                                        @php
                                                            $total_php_amnt = $currency_id->total_curr_amount * $currency_id->CMRUsed;
                                                        @endphp

                                                        <tr>
                                                            <td class="text-center text-sm p-1">
                                                                {{ $currency_id->Currency }}
                                                            </td>
                                                            <td class="text-right text-sm py-1 px-3">
                                                                {{ number_format($currency_id->total_curr_amount, 2, '.', ',') }}
                                                            </td>
                                                            <td class="text-right text-sm py-1 px-3">
                                                                {{ number_format($currency_id->CMRUsed, 2, '.', ',') }}
                                                            </td>
                                                            <td class="text-right text-sm py-1 px-3">
                                                                <strong>
                                                                    {{ number_format($total_php_amnt, 2, '.', ',') }}
                                                                </strong>
                                                            </td>
                                                        </tr>

                                                        @php
                                                            $sum_php_amnt += $total_php_amnt;
                                                        @endphp
                                                    @endforeach
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3">
                                                        </td>
                                                        <td class="text-right text-sm py-1 px-3" colspan="1">
                                                            <strong>
                                                                {{ number_format($sum_php_amnt, 2, '.', ',') }}
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>

                                            <div class="col-12 p-1 border border-gray-300 rounded-bl rounded-br">
                                                <div class="row align-items-center ps-2">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col">
                                        <div class="card">
                                            <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                                <div class="row align-items-center">
                                                    Tite
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('UI.UX.security_code')

@endsection

@section('qz_tray_scripts')
    @include('script.qz_tray_s_admin_receipt_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
