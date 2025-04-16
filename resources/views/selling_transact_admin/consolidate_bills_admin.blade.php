@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row">
                    <div class="col-12">
                        <input type="hidden" id="full-url-addnewselling-admin" value="{{ URL::to('/').'/'.'addSellingTransAdmin' }}">
                    </div>

                    <div class="col-12">
                        <hr>
                    </div>

                    <div class="col-lg-4 mb-4 pe-0">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <span class="text-lg font-bold p-2 text-black">
                                            <i class='bx bx-select-multiple' ></i>&nbsp;{{ trans('labels.selling_admin_select_transfers') }}
                                        </span>
                                    </div>
                                    <div class="col-12 pt-1 py-2 border">
                                        <div class="row align-items-center justify-content-center px-3 mt-1">
                                            <div class="col-12">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="transact-date-transfers-to-sell" name="transact-date-selling" autocomplete="off" placeholder="yyyy-mm-dd" readonly>
                                                    <span class="input-group-text bg-slate-950"><i class='bx bx-calendar'></i></span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- <div class="row align-items-center justify-content-center px-3 mt-2">
                                            <div class="col-12">
                                                <select class="form-control" name="currencies-selling" id="currencies-select-selling">
                                                    <option value="default-val">Select a currency</option>
                                                    @foreach ($result['currency'] as $currencies)
                                                        <option value="{{ $currencies->CurrencyID }}" data-currencyrate="" name="selected-currency">{{ $currencies->Currency }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div> --}}

                                        <input type="hidden" id="last-entry-crid" value="">
                                        <input type="hidden" id="latest-entry-crid" value="">

                                        <div class="row align-items-center justify-content-center px-3 mt-2">
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                        @foreach ($result['transact_type'] as $transact_type)
                                                            <input type="radio" class="btn-check radio-button" name="radio-transact-type" id="radio-button-{{ $transact_type->TransType }}" value="{{ $transact_type->TTID }}" checked>
                                                            <label class="btn btn-outline-primary" for="radio-button-{{ $transact_type->TransType }}">
                                                                <strong>{{ $transact_type->TransType }}</strong>
                                                            </label>
                                                        @endforeach

                                                        <input type="radio" class="btn-check radio-button" name="radio-transact-type-buffer" id="radio-button-buffer" value="buffer">
                                                        <label class="btn btn-outline-primary" for="radio-button-buffer">
                                                            <strong>Buffer</strong>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row align-items-center justify-content-center px-3 mt-2">
                                            <div class="col-12">
                                                <hr class="m-0">
                                            </div>
                                        </div>
                                        
                                        <div class="row align-items-center justify-content-center px-3 mt-2">
                                            <div class="col-12">
                                                <table class="table table-hovered table-bordered" id="currency-tables">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-black text-center text-xs font-extrabold whitespace-nowrap p-1 w-50">Currency</th>
                                                            <th class="text-black text-center text-xs font-extrabold whitespace-nowrap p-1 w-50">Selling Rate</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr id="empty-banner">
                                                            <td class="text-center text-td-buying text-xs py-1" colspan="12">
                                                                <span class="buying-no-transactions text-sm">
                                                                    <strong>SELECT A TRANSACTION TYPE</strong>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div> 

                                        <div class="row justify-content-center px-3 mt-2">
                                            <div class="col-6">
                                                <div class="row">
                                                    @can('add-permission', $menu_id)
                                                        <button class="btn btn-primary consolidate-transfers-button btn-sm" id="consolidate-transfers-button" name="consolidate-transfers-button" type="button">{{ trans('labels.selling_admin_transact_generate_bills') }}</button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" id="selected-forex-serials-fsid-val" name="selected-forex-serials-fsid-val" value="">

                                    <div class="col-12 border border-gray-300 rounded-bl rounded-br p-2">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <input type="hidden" id="user-id-selling" name="user-id-selling">
                                            </div>
                                            <div class="col-lg-6 text-end">
                                                @can('access-permission', $menu_id)
                                                    <a class="btn btn-secondary btn-sm" type="button" href="{{ route('admin_transactions.bulk_selling') }}">{{ trans('labels.back_action') }}</a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card">
                            <div class="col-12 border border-gray-300 rounded-tl rounded-tr p-2 ps-3">
                                <div class="row">
                                    <div class="col-6">
                                        <span class="text-lg font-bold p-1 text-black">
                                            <i class='bx bx-list-check'></i>&nbsp;Generated Bills
                                        </span>
                                    </div>
                                    <div class="col-6 text-end">
                                        @can('access-permission', $menu_id)
                                            <button class="btn btn-primary btn-sm" id="availalbe-stocks-button" data-bs-toggle="modal" data-bs-target="#availalbe-stocks-modal">
                                                Availble Stocks
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                            {{-- Selling Transact - Select Bills - Admin --}}
                            <form class="m-0" method="POST" id="consolidate-bills-form">
                                @csrf
                                <div id="transfer-forex-to-sell">
                                    <table class="table table-bordered" id="transfer-forex-to-sell-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center p-1">
                                                    <input class="form-check-input" type="checkbox" id="bills-to-sell-select-all" name="bills-to-sell-select-all" disabled>
                                                </th>
                                                {{-- <th class="text-th-buying text-center text-xl font-extrabold text-black py-1 px-1 whitespace-nowrap">{{ trans('labels.selling_admin_sel_trans_branch') }}</th> --}}
                                                {{-- <th class="text-center text-sm font-extrabold text-black py-1 px-1 whitespace-nowrap" colspan="1">{{ trans('labels.selling_admin_sel_trans_currency') }}</th> --}}
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap">Currency</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap">{{ trans('labels.selling_admin_sel_trans_amnt') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap">{{ trans('labels.selling_admin_sel_trans_bill_count') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap">Selling Rate</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap">{{ trans('labels.selling_admin_sel_trans_rate_used') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap">{{ trans('labels.selling_admin_sel_trans_eer') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap">{{ trans('labels.selling_admin_sel_trans_capital') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap">{{ trans('labels.selling_admin_sel_trans_gn_ls') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="transfer-forex-to-sell-table-body">
                                            {{-- <tr id="buying-transact-banner">
                                                <td class="text-center text-td-buying text-xs py-3" colspan="12">
                                                    <span class="buying-no-transactions text-lg">
                                                        <strong>CHOOSE A CURRENCY</strong>
                                                    </span>
                                                </td>
                                            </tr> --}}
                                        </tbody>
                                        <tfoot id="transfer-forex-to-sell-table-foot">
                                            <tr>
                                                <td class="text-center text-sm whitespace-nowrap py-1 px-1" colspan="1">
                                                </td>
                                                <td class="text-center text-sm whitespace-nowrap py-1 px-1" colspan="1">
                                                </td>
                                                <td class="text-right text-sm whitespace-nowrap py-1 px-1" colspan="1">
                                                    {{-- <strong>
                                                        <span id="total-generated-amount">0.00</span>
                                                    </strong> --}}

                                                    <input id="total-generated-amount-input" type="hidden" value="">
                                                </td>
                                                <td class="text-center text-sm py-1 px-1" colspan="3">
                                                </td>
                                                <td class="text-right text-sm whitespace-nowrap py-1 px-2" colspan="1">
                                                    <strong>
                                                        <span id="total-generated-ex-ex-r">0.00</span>
                                                    </strong>

                                                    <input id="total-generated-ex-ex-r-input" type="hidden" value="">
                                                </td>
                                                <td class="text-right text-sm py-1 px-2" colspan="1">
                                                    <strong>
                                                        <span id="total-generated-capital">0.00</span>
                                                    </strong>

                                                    <input id="total-generated-capital-input" type="hidden" value="">
                                                </td>
                                                <td class="text-right text-sm whiespace-nowrap py-1 px-2" colspan="1" id="total-generated-gain-loss">
                                                    {{-- <strong>
                                                        <span class="badge" id="total-generated-gain-loss">0.00</span>
                                                    </strong> --}}
                                                </td>
                                                <input id="total-generated-gain-loss-input" type="hidden" value="">
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </form>
                            <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br text-end">
                                @can('add-permission', $menu_id)
                                    <button class="btn btn-primary btn-sm ps-2" type="button" id="selling-add-to-queueing" disabled>
                                        <i class='bx bx-checkbox-checked'></i> Select
                                    </button>
                                @endcan
                            </div>
                        </div>

                        <div class="card mt-2">
                            <div class="col-12 border border-gray-300 rounded-tl rounded-tr p-2 ps-3">
                                <div class="row">
                                    <div class="col-6">
                                        <span class="text-lg font-bold p-1 text-black">
                                            {{-- <i class='bx bx-list-check'></i>&nbsp;{{ trans('labels.selling_admin_sel_trans_for_selling') }} --}}
                                            <i class='bx bx-list-check'></i>&nbsp;Queued Bills
                                        </span>
                                    </div>
                                    <div class="col-6 text-end">
                                        @can('add-permission', $menu_id)
                                            @if ($result['buffer_if_any']) 
                                                <button class="btn btn-primary btn-edit-details btn-sm" id="set-buffer-rate" data-bs-toggle="modal" data-bs-target="#set-buff-rate-modal">
                                                    Set Buffer Rate<i class="menu-icon tf-icons bx bx-plus text-white ms-1 me-0"></i>
                                                </button>
                                            @endif
                                        @endcan

                                        @can('print-permission', $menu_id)
                                            @if (count($result['queued_bills']) > 0)
                                                <button class="btn btn-primary btn-edit-details btn-sm" id="print-queued-bills">
                                                {{-- <button class="btn btn-primary btn-edit-details btn-sm" data-bs-toggle="modal" data-bs-target="#queued-by-rset-modal"> --}}
                                                    Print Queued Bills <i class='bx bxs-file-blank bx-xs text-white ms-1 me-0'></i>
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                            </div>

                            <table class="table table-bordered table-hover" id="bills-for-selling-table">
                                <thead>
                                    <tr>
                                        <th class="text-center p-1">
                                            <input class="form-check-input" type="checkbox" id="consolidate-bills-select-all" name="consolidate-bills-select-all" @if (count($result['queued_bills']) < 0) disabled @else checked @endif>
                                        </th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap" colspan="1">{{ trans('labels.selling_admin_sel_trans_currency') }}</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap" colspan="1">-</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.selling_admin_sel_trans_amnt') }}</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.selling_admin_sel_trans_bill_count') }}</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.selling_admin_sel_trans_mnl_rate') }}</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.selling_admin_sel_trans_rate_used') }}</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.selling_admin_sel_trans_eer') }}</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.selling_admin_sel_trans_capital') }}</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.selling_admin_sel_trans_gn_ls') }}</th>
                                    </tr>
                                </thead>

                                @php
                                    $computed_total_bill_amnt = 0;
                                    $computed_capital = 0;
                                    $computed_ex_ex_r = 0;
                                    $computed_gain_loss = 0;
                                @endphp

                                <tbody id="bills-for-selling-table-tbody">
                                    @forelse ($result['queued_bills'] as $queued_bills)
                                        <tr>
                                            <td class="text-center text-sm p-1">
                                                <div class="row align-items-center">
                                                    <div class="text-rate-maintenance col-12 px-0">
                                                        <input class="form-check-input consolidate-bills-select-one"  @if ($queued_bills->Buffer == 1) disabled @endif type="checkbox" data-serialfsid="{{ $queued_bills->All_FSIDs }}" data-serialafsid="{{ $queued_bills->All_AFSIDs }}" @if ($queued_bills->Buffer == 1) data-buffer="true" @endif checked="true">
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center text-xs p-1">
                                                <strong>
                                                    {{ $queued_bills->CurrAbbv }}
                                                </strong>
                                            </td>
                                            <td class="text-center text-xs p-1">
                                                @if ($queued_bills->Buffer == 1)
                                                    <span class="badge success-badge-custom">
                                                        Buffer
                                                    </span>
                                                @else
                                                    <span class="badge primary-badge-custom">
                                                        Regular
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-right text-sm whitespace-nowrap py-1 px-2">
                                                {{ number_format($queued_bills->total_bill_amount, 2, '.', ',') }}
                                            </td>
                                            <td class="text-right text-sm whitespace-nowrap p-1">
                                                {{ $queued_bills->total_bill_count }}
                                            </td>
                                            <td class="text-right text-sm whitespace-nowrap py-1 px-2">
                                                {{ $queued_bills->CMRUsed }}
                                            </td>
                                            <td class="text-right text-sm whitespace-nowrap py-1 px-2">
                                                {{ $queued_bills->SinagRateBuying }}
                                            </td>
                                            <td class="text-right text-sm whitespace-nowrap py-1 px-2">
                                                {{ number_format($queued_bills->total_exchange_amount, 2, '.', ',') }}
                                            </td>
                                            <td class="text-right text-sm whitespace-nowrap py-1 px-2">
                                                {{ number_format($queued_bills->total_principal, 2, '.', ',') }}
                                            </td>
                                            <td class="text-right text-sm whitespace-nowrap py-1 px-2">
                                                {{-- <span class="badge @if ($queued_bills->gain_loss >= 0) success-badge-custom @else danger-badge-custom @endif">
                                                    @if ($queued_bills->gain_loss >= 0){{ trans('labels.gain_symbol') }}@else{{ trans('labels.loss_symbol') }}@endif{{ number_format(str_replace('-', '', $queued_bills->gain_loss), 2, '.', ',') }}  @if ($queued_bills->gain_loss >= 0) <i class='bx bxs-up-arrow pb-1' style="font-size: .5rem;"></i> @else <i class='bx bxs-down-arrow pb-1' style="font-size: .5rem;"></i> @endif
                                                </span> --}}
                                                <span class="font-bold text-xs @if ($queued_bills->gain_loss >= 0) text-[#00A65A]@else text-[#DC3545] @endif">
                                                    @if ($queued_bills->gain_loss >= 0){{ trans('labels.gain_symbol') }}@else{{ trans('labels.loss_symbol') }}@endif{{ number_format(str_replace('-', '', $queued_bills->gain_loss), 2, '.', ',') }}  @if ($queued_bills->gain_loss >= 0) <i class='bx bxs-up-arrow pb-1' style="font-size: .5rem;"></i> @else <i class='bx bxs-down-arrow pb-1' style="font-size: .5rem;"></i> @endif
                                                </span>
                                            </td>

                                            @php
                                                $computed_total_bill_amnt += $queued_bills->total_bill_amount;
                                                $computed_capital += $queued_bills->total_principal;
                                                $computed_ex_ex_r += $queued_bills->total_exchange_amount;
                                                $computed_gain_loss += $queued_bills->gain_loss;
                                            @endphp
                                        </tr>
                                    @empty
                                        <tr id="selling-pool-banner">
                                            <td class="text-center text-td-buying text-sm py-3" colspan="13">
                                                <span class="buying-no-transactions text-lg">
                                                    <strong>NO BILLS FOR SELLING</strong>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot id="transfer-forex-to-sell-table-foot">
                                    <tr>
                                        <td class="text-center text-sm py-1 px-1" colspan="1"></td>
                                        <td class="text-center text-sm py-1 px-1 whitespace-nowrap" colspan="1"></td>
                                        <td class="text-right text-sm py-1 px-1 whitespace-nowrap" colspan="1">
                                            <strong>
                                                <span id="conso-total-generated-amount">
                                                    {{-- {{ number_format($computed_total_bill_amnt, 2, '.', ',') }} --}}
                                                </span>
                                            </strong>

                                            <input id="conso-total-generated-amount-input" type="hidden" value="{{ $computed_total_bill_amnt }}">
                                        </td>
                                        <td class="text-right text-sm py-1 px-1" colspan="4"></td>
                                        <td class="text-right text-sm py-1 px-2 whitespace-nowrap" colspan="1">
                                            <strong>
                                                <span id="conso-total-generated-ex-ex-r">
                                                    {{ number_format($computed_ex_ex_r, 2, '.', ',') }}
                                                </span>
                                            </strong>

                                            <input id="conso-total-generated-ex-ex-r-input" type="hidden" value="{{ $computed_ex_ex_r }}">
                                        </td>
                                        <td class="text-right text-sm py-1 px-2 whitespace-nowrap" colspan="1">
                                            <strong>
                                                <span id="conso-total-generated-capital">
                                                    {{ number_format($computed_capital, 2, '.', ',') }}
                                                </span>
                                            </strong>

                                            <input id="conso-total-generated-capital-input" type="hidden" value="{{ $computed_capital }}">
                                        </td>
                                        <td class="text-right text-sm py-1 px-2 whitespace-nowrap" id="conso-gain-loss-cell" colspan="1">
                                            <strong>
                                                <span class="badge @if ($computed_gain_loss == 0) primary-badge-custom @elseif ($computed_gain_loss >= 0) success-badge-custom @else danger-badge-custom @endif" id="conso-total-generated-gain-loss">
                                                    @if($computed_gain_loss == 0) @elseif ($computed_gain_loss >= 0){{ trans('labels.gain_symbol') }}@else{{ trans('labels.loss_symbol') }}@endif{{ number_format(str_replace('-', '', $computed_gain_loss), 2, '.', ',') }} @if ($computed_gain_loss == 0)  @elseif ($computed_gain_loss >= 0) <i class='bx bxs-up-arrow pb-1' style="font-size: .5rem;"></i> @else <i class='bx bxs-down-arrow pb-1' style="font-size: .5rem;"></i> @endif</strong>
                                                </span>
                                            </strong>
                                        </td>
                                        <input id="conso-total-generated-gain-loss-input" type="hidden" value="{{ $computed_gain_loss }}">
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <input type="hidden" id="user-id-selling" name="user-id-selling">
                                    </div>
                                    <div class="col-lg-6 text-end">
                                        @can('edit-permission', $menu_id)
                                            <button class="btn btn-secondary btn-sm ps-2" type="button" id="for-selling-unselect-bills">
                                                <i class='bx bx-checkbox-minus'></i> Unqueue
                                            </button>
                                        @endcan

                                        @can('add-permission', $menu_id)
                                            <a class="btn btn-primary btn-sm" type="button" href="{{ route('admin_transactions.bulk_selling.sell') }}">{{ trans('labels.selling_admin_sell_to_manila') }}</a>
                                        @endcan
                                        {{-- <button class="btn btn-primary" type="button" id="selling-consolidate-bills" disabled>Consolidate Bills</button> --}}
                                        {{-- <button class="btn btn-primary" type="button" id="selling-trans-admin-confirm-button" disabled>{{ trans('labels.confirm_action') }}</button> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('UI.UX.security_code')
    @include('UI.UX.queued_rset_modal')
    @include('UI.UX.add_buff_rate_modal')
    @include('UI.UX.available_stocks_modal')
@endsection

@section('selling_admin_scripts')
    @include('script.selling_admin_conso_scripts')
    @include('script.qz_tray_print_queued_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
