@extends('template.layout')
@section('content')

   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <hr>
                    </div>

                    <div class="col-lg-12">
                        <div class="row justify-content-center">
                            <div class="col-6 pe-0">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-detail'></i>&nbsp;Details
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                {{-- @can('add-permission', $menu_id)
                                                    <button class="btn btn-primary text-white btn-sm" type="button" id="add-buffer" data-bs-toggle="modal" data-bs-target="#add-buffer-modal">
                                                        Add Buffer <i class='bx bx-plus'></i>
                                                    </button>
                                                @endcan --}}

                                                @can('add-permission', $menu_id)
                                                    {{-- <button class="btn btn-primary text-white btn-sm " type="button" id="add-breakdown"> --}}
                                                    <button class="btn btn-primary text-white btn-sm @if (count($result['breakdown']) > 0) d-none @endif" type="button" id="add-breakdown">
                                                        Add Breakdown&nbsp;<i class='bx bx-plus'></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        @foreach ($result['buffer_details'] as $buffer_details)
                                            <div class="col-12 pb-0 border border-gray-300" id="buying-container">
                                                <div class="row align-items-center px-3 mt-2">
                                                    <div class="col-3">
                                                        <strong>
                                                            BF No.:
                                                        </strong>
                                                    </div>
                                                    <div class="col-9">
                                                        <input type="hidden" id="buff-id"value="{{ $buffer_details->BFID }}" readonly>
                                                        <input type="text" class="form-control" id="buff-no" name="buff-no" value="{{ $buffer_details->BFNo }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center px-3 my-2">
                                                    <div class="col-3">
                                                        <strong>
                                                            BF Date:
                                                        </strong>
                                                    </div>
                                                    <div class="col-9">
                                                        <input type="text" class="form-control" id="buff-date" name="buff-date" value="{{ $buffer_details->BFDate }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center px-3 my-2">
                                                    <div class="col-3">
                                                        <strong>
                                                            Currency:
                                                        </strong>
                                                    </div>
                                                    <div class="col-9">
                                                        <input type="text" class="form-control" id="currency" name="currency" data-currencyid="{{ $buffer_details->CurrencyID }}" value="{{ $buffer_details->Currency }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="row align-items-center px-3 my-2">
                                                    <div class="col-3">
                                                        <strong>
                                                           Amount:
                                                        </strong>
                                                    </div>
                                                    <div class="col-9">
                                                        <input type="text" class="form-control text-right font-bold" value="{{ number_format($buffer_details->DollarAmount, 2, '.', ',') }}" readonly>
                                                        <input type="hidden" id="amount" name="amount" value="{{ $buffer_details->DollarAmount }}">
                                                    </div>
                                                </div>
                                                <div class="row align-items-center px-3 my-2">
                                                    <div class="col-3">
                                                        <strong>
                                                           Principal (PHP):
                                                        </strong>
                                                    </div>
                                                    <div class="col-9">
                                                        <input type="text" class="form-control text-right font-bold" value="{{ number_format($buffer_details->Principal, 2, '.', ',') }}" readonly>
                                                        <input type="hidden" id="principal-amount" name="principal-amount" value="{{ $buffer_details->Principal }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="row px-3 mt-2">
                                                    <div class="col-3">
                                                        <strong>
                                                            Remarks:
                                                        </strong>
                                                    </div>
                                                    <div class="col-9">
                                                        <textarea class="form-control" id="remarks" name="remarks" @if ($buffer_details->Remarks == null) rows="1" placeholder="N/A" @else rows="3" @endif readonly>{{ $buffer_details->Remarks }}</textarea>
                                                    </div>
                                                </div>
                                        @endforeach
                                            <div class="row px-3">
                                                <div class="col-12">
                                                    <hr class="my-2">
                                                </div>
                                            </div>

                                            {{-- Buying Transaction - Currency Denomination --}}
                                            <div class="row align-items-center px-3 d-none" id="denom-t-container">
                                                <div class="col-12">
                                                    <div id="currency-denom-calcu">
                                                        <table class="table table-bordered" id="currency-denom-table">
                                                            <thead>
                                                                <tr id="buying-transact-headers">
                                                                    <th class="text-center text-sm font-extrabold text-black p-1" id="bill-amount-header">Denomination</th>
                                                                    <th class="text-center text-sm font-extrabold text-black p-1" id="bill-count-header">{{ trans('labels.buying_bill_count') }}</th>
                                                                    <th class="text-center text-sm font-extrabold text-black p-1" id="subtotal-header">{{ trans('labels.buying_bill_subtotal') }}</th>
                                                                    <th class="text-center text-sm font-extrabold text-black p-1" id="sinag-buying-rate-header">{{ trans('labels.buying_bill_sinag_buying_rate') }}</th>
                                                                    <th class="text-center text-sm font-extrabold text-black p-1" id="total-bill-amount-header">{{ trans('labels.buying_bill_total_amount') }}</th>
                                                                    <th hidden class="text-center text-sm font-extrabold text-black p-1" id="mtcn-header">{{ trans('labels.buying_bill_mtcn') }}</th>
                                                                    <th hidden class="text-center text-sm font-extrabold text-black p-1" id="dpofx-bill-amnt-header">{{ trans('labels.buying_bill_dpofx_amnt') }}</th>
                                                                    <th hidden class="text-center text-sm font-extrabold text-black p-1" id="dpofx-rate-header">{{ trans('labels.buying_bill_dpofx_rate') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="buying-transact-table-body">
                                                                <tr id="buying-transact-banner">
                                                                    <td class="text-center text-td-buying text-sm py-3" colspan="13">
                                                                        <span class="buying-no-transactions text-lg">
                                                                            <strong>Apply Breakdown</strong>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                        {{ csrf_field() }}
                                                </div>
                                            </div>
                                            <form class="m-0" method="post" id="buff-breakdown-form">
                                                @csrf
                                                {{-- Buying Transaction - Currency Amount --}}
                                                <div class="row align-items-center px-3 d-none" id="currency-amount-buying">
                                                    <div class="col-4 offset-5 text-end">
                                                        <strong>
                                                            {{ trans('labels.buying_trans_bill_amnt') }}: &nbsp;
                                                        </strong>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="mb-3 pt-3">
                                                            <input type="hidden" class="form-control" name="current_amount_true" id="current_amount_true">
                                                            <input type="text" class="form-control text-end" name="current_amount" id="current_amount" autocomplete="off" value="0.00" readonly>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 mb-3 d-none" id="total-amount-buying">
                                                    <div class="col-4 offset-5 text-end">
                                                        <strong>
                                                            {{ trans('labels.buying_trans_amnt') }}: &nbsp;
                                                        </strong>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="col-12 text-end">
                                                            <input type="hidden" class="form-control" name="total_buying_amount_true" id="total_buying_amount_true">
                                                            <input type="hidden" class="form-control" name="total_buying_amount_input" id="total_buying_amount_input">
                                                            <span class="text-lg pt-3 font-bold text-black">PHP</span> &nbsp;
                                                            <span class="text-lg pt-3 font-bold text-black" name="total_buying_amount" id="total_buying_amount" value="0">0.00</span>
                                                            {{-- <input type="text" class="form-control" name="total_buying_amount" id="total_buying_amount" autocomplete="off" value="0" readonly>/ --}}
                                                        </div>
                                                    </div>
                                                </div>

                                                <input type="hidden" id="test-get" value="">
                                                <input type="hidden" id="bill-amount-count" name="bill-amount-count" value="">
                                                <input type="hidden" id="multiplier-total-count" name="multiplier-total-count" value="">
                                                <input type="hidden" id="subtotal-count" name="subtotal-count" value="">
                                                <input type="hidden" id="sinag-buying-rate-count" name="sinag-buying-rate-count" value="">
                                                <input type="hidden" id="sinag-var-buying" name="sinag-var-buying" value="">
                                                <input type="hidden" name="BFID" value="{{ $BFID }}">
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="col-12 text-end">
                                            @can('access-permission', $menu_id)
                                                <a class="btn btn-secondary btn-sm" href="{{ route('admin_transactions.buffer.buffer_financing') }}">{{ trans('labels.back_action') }}</a>
                                            @endcan

                                            @can('edit-permission', $menu_id)
                                                <button class="btn btn-primary btn-sm d-none" id="save-break-d" disabled>{{ trans('labels.confirm_action') }}</button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Buffer Breakdown --}}
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-list-ul'></i>&nbsp;Breakdown
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <table class="table table-bordered table-hover" id="transfers-result-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">Currency</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">Serials</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">Denomination</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">Rate Used</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($result['breakdown'] as $breakdown)
                                                    <tr>
                                                        <td class="text-center text-sm text-black p-1 whitespace-nowrap">
                                                            {{ $breakdown->Currency }}
                                                        </td>
                                                        <td class="text-center text-sm text-black py-1 pe-3 whitespace-nowrap">
                                                            @if ($breakdown->Serials)
                                                                {{ $breakdown->Serials }}
                                                            @else
                                                                <strong>-</strong>
                                                            @endif
                                                        </td>
                                                        <td class="text-right text-xs text-black py-1 pe-3 whitespace-nowrap">
                                                            <strong>
                                                                {{ number_format($breakdown->BillAmount, 2, '.', ',') }}
                                                            </strong>
                                                        </td>
                                                        <td class="text-right text-xs text-black py-1 pe-3 whitespace-nowrap">
                                                            {{ number_format($breakdown->SinagRateBuying, 4, '.', ',') }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="text-center text-td-buying text-sm py-2" colspan="13">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO BREAKDOWN AVAILABLE</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td class="text-end p-2" colspan="4">
                                                        {{ $result['breakdown']->links() }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="col-12 text-end p-2 border border-gray-300 rounded-bl rounded-br">
                                        @if (count($result['breakdown']) > 0)
                                            <a class="btn btn-primary text-white btn-sm" type="button" @if (count($result['breakdown']) > 0) href="{{ route('admin_transactions.buffer.buffer_serials', ['BFID' => $breakdown->BFID]) }}" @else disabled @endif>
                                                {{ trans('labels.serials_add_serials') }}
                                                <i class='menu-icon tf-icons bx bx-edit-alt text-white ms-1 me-0'></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr serials-header summary-header">
                                        <span class="text-lg font-bold p-1 text-black">
                                            <i class='bx bx-list-ul' ></i>&nbsp;Summary
                                        </span>
                                    </div>
                                    <table class="table table-bordered table-hover" id="bill-summary-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_denom_amount') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_denom_multiplier') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_denom_total') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_denom_sinag_buying_rate') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($result['denom_details'] as $denom_details)
                                                <div id="denom-details">
                                                    <tr class="denom-details-list-tabl" id="denom-details-list-table">
                                                        <td class="text-right pe-3 text-sm py-1 px-3">
                                                            {{ number_format($denom_details->BillAmount, 2 , '.' , ',') }}
                                                            <input type="hidden" class="form-control bill-amount-input" value="{{ number_format($denom_details->BillAmount, 2 , '.' , ',') }}">
                                                        </td>
                                                        <td class="text-center px-3 text-sm p-1">
                                                            {{ $denom_details->Multiplier }}
                                                            <input type="hidden" class="form-control bill-count-input" value="{{ $denom_details->Multiplier }}">
                                                        </td>
                                                        <td class="text-end text-sm py-1 px-3">
                                                            <strong>
                                                                {{ number_format($denom_details->Total, 2 , '.' , ',') }}
                                                            </strong>
                                                            <input type="hidden" class="form-control bill-total-input" value="{{ $denom_details->Total }}">
                                                        </td>
                                                        <td class="text-end text-sm py-1 px-3">
                                                            <strong>
                                                                {{ $denom_details->SinagRateBuying }}
                                                            </strong>
                                                            <input type="hidden" class="form-control bill-rate-input" value="{{ $denom_details->SinagRateBuying }}">
                                                        </td>
                                                    </tr>
                                                </div>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-td-buying text-sm py-2" colspan="4">
                                                        <span class="buying-no-transactions text-lg">
                                                            <strong>NO AVAILABLE SUMMARY</strong>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    <div class="card-footer p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row">
                                            <div class="col-lg-6 offset-1 text-end pe-0">

                                            </div>
                                            <div class="col-lg-5 text-end ps-0">
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

    @include('UI.UX.security_code')

@endsection

@section('buffer_transfer_scripts')
    @include('script.break_d_buff_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
