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
                                                    <i class='bx bx-list-ul' ></i>&nbsp;DPOFX Out Details
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                @can('edit-permission', $menu_id)
                                                    <button class="btn btn-primary btn-sm btn-edit-details" id="update-dpo-out-details" type="button">
                                                    {{-- <button class="btn btn-primary btn-sm btn-edit-details" type="button" data-bs-toggle="modal" data-bs-target="#-modal"> --}}
                                                        Edit &nbsp;<i class='bx bx-edit-alt'></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    @foreach ($result['dpo_palit_deets'] as $dpo_palit_deets)
                                        <form class="mb-0" method="post" id="update-dpo-out-details-form">
                                            @csrf
                                            <div class="col-12 py-2 px-0 border border-gray-300">
                                                <div class="row align-items-center px-3">
                                                    <div class="col-3">
                                                        <strong>
                                                            {{ trans('labels.transact_#') }}:
                                                        </strong>
                                                    </div>
                                                    <div class="col-9">
                                                        <input class="form-control" name="transact-no" type="text" value="{{ $dpo_palit_deets->DPOSellingNo }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 my-2">
                                                    <div class="col-3">
                                                        <strong>
                                                            Processed By:
                                                        </strong>
                                                    </div>
                                                    <div class="col-9">
                                                        <input class="form-control" name="processed-by" type="text" value="{{ $dpo_palit_deets->Name }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 my-2">
                                                    <div class="col-3">
                                                        <strong>
                                                            Receipt Set:
                                                        </strong>
                                                    </div>
                                                    <div class="col-9">
                                                        <input class="form-control" id="transact-rset" name="transact-rset" type="text" value="{{ $dpo_palit_deets->Rset }}" readonly>
                                                    </div>
                                                </div>

                                                {{-- Buying Transaction - Customer Details --}}
                                                    <div class="row align-items-center px-3 mt-2 d-none" id="customer-container">
                                                        <div class="col-3">
                                                            <strong>
                                                                {{ trans('labels.transact_customer') }}:
                                                            </strong>
                                                        </div>
                                                        <div class="col-6">
                                                            <input type="text" class="form-control" id="customer-name-selected" value="{{ $dpo_palit_deets->FullName }}" readonly>
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

                                                    <div class="row align-items-center px-3 my-2" id="customer-details-cont">
                                                        <div class="col-3">
                                                            <strong>
                                                                Customer:
                                                            </strong>
                                                        </div>
                                                        <div class="col-9">
                                                            <input class="form-control" name="customer-name" type="text" value="{{ $dpo_palit_deets->FullName }}" readonly>
                                                            <input type="hidden" class="form-control" name="transact-customer-id" value="{{ $dpo_palit_deets->CustomerID }}">
                                                        </div>
                                                    </div>
                                                {{-- Buying Transaction - Customer Details --}}

                                                <div class="row px-3 my-2">
                                                    <div class="col-3">
                                                        <strong>
                                                            Remarks:
                                                        </strong>
                                                    </div>
                                                    <div class="col-9">
                                                        <textarea class="form-control" id="remarks" name="remarks" @if ($dpo_palit_deets->Remarks == null) rows="1" placeholder="N/A" @else rows="3" @endif readonly>{{ $dpo_palit_deets->Remarks }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 my-2">
                                                    <div class="col-3">
                                                        <strong>
                                                            Dollar Amount:
                                                        </strong>
                                                    </div>
                                                    <div class="col-9">
                                                        <input class="form-control" name="dollar-amount" type="text" value="{{ number_format($dpo_palit_deets->DollarAmount, 2, '.', ',') }}" readonly>
                                                    </div>
                                                </div>

                                                {{-- <div class="row align-items-center px-3 my-2">
                                                    <div class="col-3">
                                                        <strong>
                                                            Selling Rate :
                                                        </strong>
                                                    </div>
                                                    <div class="col-9">
                                                        <input class="form-control" name="selling-rate" type="text" value="{{ number_format($dpo_palit_deets->SellingRate, 2, '.', ',') }}" readonly>
                                                    </div>
                                                </div> --}}

                                                <div class="row align-items-center px-3 my-2">
                                                    <div class="col-3">
                                                        <strong>
                                                            Exchange Amount:
                                                        </strong>
                                                    </div>
                                                    <div class="col-9">
                                                        <input class="form-control text-right" name="exchange-amount" type="text" value="{{ number_format($dpo_palit_deets->TotalExchangeAmount, 2, '.', ',') }}" readonly>
                                                        <input name="true-exchange-amount" type="hidden" value="0">
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 my-2">
                                                    <div class="col-3">
                                                        <strong>
                                                            Principal Amount:
                                                        </strong>
                                                    </div>
                                                    <div class="col-9">
                                                        <input class="form-control text-right" name="principal" type="text" value="{{ number_format($dpo_palit_deets->TotalPrincipal, 2, '.', ',') }}" data-actualprincipal="{{ $dpo_palit_deets->TotalPrincipal }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3">
                                                    <div class="col-3">
                                                        <strong>
                                                            {{ trans('labels.transact_gain_loss') }}:
                                                        </strong>
                                                    </div>
                                                    <div class="col-4 offset-5 text-right">
                                                        @php
                                                            $gain_loss = $dpo_palit_deets->TotalGainLoss;
                                                        @endphp

                                                        <strong>
                                                            <input class="form-control text-white text-right py-1 @if ($gain_loss >= 0) success-badge-custom @else danger-badge-custom @endif" name="transact-total-gain-loss" id="total-gain-loss" value="{!! $gain_loss >= 0 ? trans('labels.gain_symbol') . number_format($gain_loss, 2, '.', ',') . ' ▲' : number_format($gain_loss, 2, '.', ',') . ' ▼' !!}" readonly>
                                                            <input type="hidden" name="true-total-gain-loss" value="0">
                                                            <input type="hidden" id="DPODOID" value="{{ $dpo_palit_deets->DPODOID }}">
                                                        </strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    @endforeach

                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center ps-2">
                                            <div class="col-12 text-end">
                                                <a class="btn btn-secondary btn-sm" href="{{ route('admin_transactions.dpofx.dpo_out') }}">
                                                    {{ trans('labels.back_action') }}
                                                </a>
                                                <button class="btn btn-primary btn-sm d-none" type="button" id="update-transction-btn" data-bs-toggle="modal" data-bs-target="#update-dpo-out-sec-code-modal">
                                                    Update
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Transact Breakdown --}}
                            <div class="col-6">
                                @forelse ($result['dpo_out'] as $dpo_out)
                                    <div class="col mb-3">
                                        <div class="card">
                                            <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                                <div class="row align-items-center ps-2">
                                                    <div class="col-6">
                                                        <span class="text-lg text-black">
                                                            <strong>
                                                                {{ $dpo_out->CompanyName }}
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
                                                                {{ str_pad($dpo_out->FormSeries, 6, '0', STR_PAD_LEFT) }}
                                                            </strong>
                                                        </span>
                                                    </div>
                                                    <div class="col-4 text-end">
                                                        @can('print-permission', $menu_id)
                                                            <button class="btn btn-primary btn-sm print-dpofx-receipt ps-2" data-dpooid="{{ $dpo_out->DPODOID }}" data-companyid="{{ $dpo_out->CompanyID }}">
                                                                <i class='bx bxs-printer'></i>&nbsp;&nbsp;{{ trans('labels.selling_admin_print_receipt') }}
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>

                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Currency</th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Amount</th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Selling Rate</th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Total (PESO)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $sum_php_amnt = 0;
                                                    @endphp

                                                    @foreach ($dpo_out->Currency as $currency_id)
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
    @include('UI.UX.customer_searching')
    @include('UI.UX.update_dpo_out_details_modal')
@endsection

@section('qz_tray_scripts')
    @include('script.dpo_out_transact_scripts')
    @include('script.qz_tray_dpo_out_receipt_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
