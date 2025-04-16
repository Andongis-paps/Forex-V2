@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-12">

                    </div>

                    <div class="col-12">
                        <hr>
                    </div>

                    {{-- Admin Currency Stocks --}}
                    <div class="col-lg-12">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bxs-coin-stack'></i> &nbsp;Admin Stocks
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <table class="table table-hovered table-bordered" id="">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.curr_stocks_admin_stocks_curr') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.curr_stocks_admin_stocks_pieces') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.curr_stocks_admin_stocks_total_curr_amnt') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Selling (PCS)</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Amount (Selling)</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Principal</th>
                                                    @can('access-permission', $menu_id)
                                                        <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap"></th>
                                                    @endcan
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $overall_principal = 0;
                                                @endphp

                                                @if (count($result['stocks_admin']) > 0)
                                                    @foreach ($result['stocks_admin'] as $stocks_admin)
                                                        @php
                                                            $overall_principal += $stocks_admin->total_principal;
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center text-xs whitespace-nowrap p-1">
                                                                {{ $stocks_admin->Currency }}
                                                            </td>
                                                            <td class="text-center text-xs whitespace-nowrap p-1">
                                                                {{ $stocks_admin->total_bill_count }}
                                                            </td>
                                                            <td class="text-right text-xs whitespace-nowrap py-1 px-3">
                                                                <strong>
                                                                    {{ number_format($stocks_admin->total_curr_amount, 2, '.', ',') }}
                                                                </strong>
                                                            </td>
                                                            <td class="text-center text-xs whitespace-nowrap p-1">
                                                                {{ $stocks_admin->queued_total_bill_count }}
                                                            </td>
                                                            <td class="text-right text-xs whitespace-nowrap py-1 px-3">
                                                                <strong>
                                                                    {{ number_format($stocks_admin->queued_total_curr_amnt, 2, '.', ',') }}
                                                                </strong>
                                                            </td>
                                                            <td class="text-right text-xs whitespace-nowrap py-1 px-3">
                                                                <strong>
                                                                    {{ number_format($stocks_admin->total_principal, 2, '.', ',') }}
                                                                </strong>
                                                            </td>
                                                            @can('access-permission', $menu_id)
                                                                <td class="text-center text-xs whitespace-nowrap p-1">
                                                                    <button class="btn btn-primary button-edit admin-stocks-details pe-1 text-white" data-currabbv="{{ $stocks_admin->CurrAbbv }}" data-currency="{{ $stocks_admin->Currency }}" data-currencyid="{{ $stocks_admin->CurrencyID }}" data-bs-toggle="modal" data-bs-target="#admin-stock-details-modal">
                                                                        <i class='bx bx-detail me-1'></i>
                                                                    </button>
                                                                </td>
                                                            @endcan
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-td-buying text-sm py-3" colspan="13">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO AVAILABLE STOCK</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5"></td>
                                                    <td class="text-end text-sm whitespace-nowrap py-1 px-3" colspan="1">
                                                        Total Principal:&nbsp;&nbsp;<strong>PHP&nbsp;{{ number_format($overall_principal, 2, '.', ',') }}</strong>
                                                    </td>
                                                    <td colspan="1"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="col-12 px-3 border border-gray-300 rounded-bl rounded-br py-2">
                                        {{ $result['stocks_admin']->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Admin stock details breakdown via AJAX --}}
    <div class="modal fade" id="admin-stock-details-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content admin-stock-details-modal-body">
                @include('currency_stocks.admin_stock_details_modal')
            </div>
        </div>
    </div>

@endsection

@section('curr_stocks_scripts')
    @include('script.curr_stocks_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
