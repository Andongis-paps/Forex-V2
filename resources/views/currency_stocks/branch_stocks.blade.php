@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-12">

                        @if (session()->has('message'))
                            <div class="alert alert-success alert-dismissible" role="alert" data-successexistence="1">
                                {{ session()->get('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>

                    <div class="col-12">
                        <hr>
                    </div>

                    {{-- Branch Currency Stocks --}}
                    <div class="col-lg-5">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bxs-coin-stack'></i>&nbsp;Branch Stocks
                                                </span>
                                            </div>
                                            <div class="col-6 text-start">
                                                @can('add-permission', $menu_id)
                                                    <form class="mb-0" action="{{ route('admin_transactions.stocks.branch_stocks') }}" method="GET">
                                                        <div class="row align-items-center">
                                                            <div class="@if (is_null(request()->query('query'))) col-12 @else col-9 @endif">
                                                                <div class="input-group input-group-sm">
                                                                    <select class="form-select" name="query">
                                                                        <option value="default">Select branch</option>
                                                                        @forelse ($result['branches'] as $branches)
                                                                            <option value="{{ $branches->BranchCode }}" @if (request()->query('query') == $branches->BranchCode) selected @endif>{{ $branches->BranchCode }}</option>
                                                                        @empty
                                                                            Hello world!
                                                                        @endforelse
                                                                    </select>
                                                                    <button class="btn btn-primary px-1 btn-sm" id="search" type="submit"><i class="bx bx-search mx-1"></i></button>
                                                                </div>
                                                            </div>
                                                            <div class="@if (request()->query('query')) ps-0 col-3 @endif">
                                                                <div class="row align-items-center px-3">
                                                                    @if (request()->query('query'))
                                                                        <a class="btn btn-primary btn-sm btn-danger-serial" href="{{ route('admin_transactions.stocks.branch_stocks') }}">Clear</a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <table class="table table-hovered table-bordered" id="">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap w-75">{{ trans('labels.curr_stocks_branch') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap w-25">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- @if (count($result['branches_with_overdue']) > 0)
                                                    <input class="overdue-branches" type="hidden" value="{{ $result['branches_with_overdue'][0]->BranchesWithOverdue }}">
                                                @endif --}}

                                                @forelse ($result['currency_stocks'] as $currency_stocks)
                                                    <tr>
                                                        <td class="text-center text-xs whitespace-nowrap p-1">
                                                            {{ $currency_stocks->BranchCode }}
                                                        </td>

                                                        @can('access-permission', $menu_id)
                                                            <td class="text-center text-xs whitespace-nowrap p-1 position-relative">
                                                                <button class="btn btn-primary button-edit stock-details-button pe-1 text-white" id="branch-stock-details" data-branchid="{{ $currency_stocks->BranchID }}" data-bs-toggle="modal" data-bs-target="#stock-details-modal">
                                                                    <i class='bx bx-detail me-1'></i>
                                                                </button>

                                                                @if ($currency_stocks->InStockFor3DaysOrMore == 1)
                                                                    <input class="overdue-branches" type="hidden" value="{{ $currency_stocks->BranchCode }}">
                                                                    <span class="position-absolute top-75 start-90 translate-middle badge warning-badge-custom pt-2"><i class='bx bxs-error bx-flashing badge-icon'></i></span>
                                                                @endif
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="text-center text-sm py-3" colspan="13">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO BRANCH WITH STOCKS</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-12 px-3 border border-gray-300 rounded-bl rounded-br py-2">
                                        {{ $result['currency_stocks']->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <span class="text-lg font-bold ps-2 text-black">
                                                    <i class='bx bxs-coin-stack'></i>&nbsp;Available Currencies
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 border border-solid border-gray-300" @if(count($result['currencies']) > 10) id="currency-summary-container" @endif>
                                        <table class="table table-hover mb-0">
                                            <thead class="sticky-header">
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Currency</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Quantity</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Amount</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Principal</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Action</th>
                                                </tr>
                                            </thead>
                                            @php
                                                $summed_up_principal = 0;
                                            @endphp
                                            <tbody>
                                                @forelse ($result['currencies'] as $currencies)
                                                    @php
                                                        $integer_part = floor($currencies->total_principal);
                                                        $decim_part = $currencies->total_principal - $integer_part;

                                                        if ($decim_part < 0.25) {
                                                            $decim_part = 0;
                                                        } elseif ($decim_part >= 0.25 && $decim_part < 0.50) {
                                                            $decim_part = 0.25;
                                                        } elseif ($decim_part >= 0.50 && $decim_part < 0.75) {
                                                            $decim_part = 0.50;
                                                        } elseif ($decim_part >= 0.75 && $decim_part < 1) {
                                                            $decim_part = 0.75;
                                                        }

                                                        $rounded_total_amnt = $integer_part + $decim_part;

                                                        $exploded_branch = explode(",", $currencies->BranchIDs);
                                                        $summed_up_principal += $rounded_total_amnt;
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center text-sm whitespace-nowrap p-1">
                                                            {{ $currencies->Currency }}
                                                        </td>
                                                        <td class="text-center text-sm whitespace-nowrap p-1">
                                                            {{ $currencies->total_bill_count }}
                                                        </td>
                                                        <td class="text-right text-sm whitespace-nowrap font-bold py-1 px-3">
                                                            {{ number_format($currencies->total_bill_amount, 2, '.', ',') }}
                                                        </td>
                                                        <td class="text-right text-sm whitespace-nowrap font-bold py-1 px-3">
                                                            {{ number_format($rounded_total_amnt, 2, '.', ',') }}
                                                        </td>
                                                        @can('access-permission', $menu_id)
                                                            <td class="text-center text-sm py-1 px-3">
                                                                {{-- @foreach (array_slice($exploded_branch, 0, 5) as $branches_qt)
                                                                    <span class="badge bg-label-info">{{ $branches_qt }}</span>
                                                                @endforeach

                                                                @if ($display_count > 1)
                                                                    <span class="badge bg-label-secondary cursor-pointer toggle-btn">{{ $display_count }} more...</span>
                                                                @endif --}}

                                                                @if (count($exploded_branch) >= 1)
                                                                    <button class="btn btn-primary button-edit view-branches pe-1 text-white" data-currencyid="{{ $currencies->CurrencyID }}" data-branchids="{{ $currencies->BranchIDs }}" data-FSIDs="{{ $currencies->FSIDs }}" data-bs-toggle="modal" data-bs-target="#branches-modal">
                                                                        <i class='bx bx-detail me-1'></i>
                                                                    </button>

                                                                    @if ($currencies->has_pending == 1)
                                                                        <span class="badge position-absolute top-75 start-90 translate-middle pending-badge-danger z-50"><i class='bx bxs-info-circle bx-flashing badge-icon'></i></span>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="text-center text-xs py-3" colspan="13">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO AVAILABLE STOCK</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot class="sticky-footer">
                                                <tr>
                                                    <td colspan="3"></td>
                                                    <td class="border-transparent text-right text-sm py-1 px-3">
                                                        Total Principal:&nbsp;&nbsp;<span class="text-sm font-extrabold text-black">PHP</span>&nbsp; <strong>{{ number_format($summed_up_principal, 2, '.', ',') }}</strong>
                                                    </td>
                                                    <td class="border-transparent" colspan="1"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="col-12 px-3 border border-gray-300 rounded-bl rounded-br py-1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Branch stock details breakdown via AJAX --}}
    <div class="modal fade" id="stock-details-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content stock-details-modal-body">
                @include('currency_stocks.branch_stock_details_modal')
            </div>
        </div>
    </div>

    <div class="modal fade" id="branches-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                @include('currency_stocks.branches_modal')
            </div>
        </div>
    </div>

@endsection

@section('curr_stocks_scripts')
    @include('script.curr_stocks_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
