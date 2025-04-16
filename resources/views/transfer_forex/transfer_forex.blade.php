@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-lg-12 mb-4">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <input type="hidden" id="full-url-transfer-forex" value="{{ URL::to('/').'/'.'transferForexDeets' }}">

                                <input type="hidden" id="full-url-addnewbuying" value="{{ URL::to('/').'/'.'addNewBuyingTrans' }}">
                            </div>

                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-12">
                                <form class="m-0" action="{{ URL::to('/saveTransferForex') }}" method="post" id="transfer-forex-form">
                                    @csrf

                                    <div class="card">
                                        <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                            <div class="row align-items-center">
                                                <div class="col-12">
                                                    <span class="text-lg font-bold p-2 text-black">
                                                        <i class='bx bx-transfer' ></i>&nbsp;{{ trans('labels.transfer_forex_new_title') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 border border-gray-300" id="buying-container">
                                            {{-- Transfer Forex - Date --}}
                                            <div class="row justify-content-center align-items-center px-3 mt-3">
                                                <div class="col-5">
                                                    <div class="input-group mb-1">
                                                        <input type="text" class="form-control" id="transfer-forex-date" name="transfer-forex-date" autocomplete="off" placeholder="yyyy-mm-dd" readonly>
                                                        <span class="input-group-text bg-slate-950"><i class='bx bx-calendar'></i></span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Transfer Forex - Transfer Type --}}
                                            <div class="row justify-content-center align-items-center px-3 mt-2">
                                                <div class="col-5">
                                                    <div class="row text-center">
                                                        <form id="transfer-forex-type-form">
                                                            @can('access-permission', $menu_id)
                                                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                                    @foreach ($result['transact_type'] as $transact_type)
                                                                        <input type="radio" class="btn-check" name="radio-transfer-type" id="radio-button-{{ $transact_type->TransType }}" value="{{ $transact_type->TTID }}" data-transfertype="{{ $transact_type->TransType }}">
                                                                        <label class="btn btn-outline-primary" for="radio-button-{{ $transact_type->TransType }}">
                                                                            <strong>{{ $transact_type->TransType }}</strong>
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                            @endcan
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" class="form-control" id="transfer-forex-remarks" name="transfer-forex-remarks" autocomplete="off" placeholder="Remarks for forex transfer" readonly>

                                            {{-- Transfer Forex - Courier --}}
                                            {{-- <div class="row align-items-center px-3 mt-3">
                                                <div class="col-3">
                                                    <strong>
                                                        {{ trans('labels.transfer_forex_courier') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div>
                                                <div class="col-9">
                                                    <select class="form-select" name="transfer-forex-courier" id="transfer-forex-courier" disabled>
                                                        <option value="Select a courier">Select a courier</option>
                                                        @foreach ($result['courier'] as $couriers)
                                                            <option value="{{ $couriers->EUserID }}">{{ Str::title($couriers->Name) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div> --}}

                                            {{-- <div class="row align-items-center px-3 mb-3">
                                                <div class="col-12">
                                                    <span class="text-lg font-semibold p-1 text-black">{{ trans('labels.transfer_available_serials_transf') }}&nbsp;:</span>
                                                </div>
                                            </div> --}}

                                            <div class="row align-items-center px-4 mt-3 mb-2">
                                                <div class="col-lg-12 border border-solid border-gray-300 rounded-md p-0" id="transfer-forex-bill-container">
                                                    <table class="table table-hover" id="transfer-forex-bill-select-table">
                                                        <thead class="sticky-header">
                                                            <tr>
                                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">
                                                                    <input class="form-check-input" type="checkbox" id="transfer-forex-bill-select-all" name="transfer-forex-bill-select-all" checked>
                                                                </th>
                                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.transfer_forex_date') }}</th>
                                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.transfer_forex_transact_no') }}</th>
                                                                {{-- <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.transfer_forex_receipt_no') }}</th> --}}
                                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.transfer_forex_transact_type') }}</th>
                                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.transfer_forex_currency') }}</th>
                                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.transfer_forex_serials') }}</th>
                                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.transfer_forex_bill_amnt') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="transfer-forex-bill-select-table-tbody">
                                                            <input id="bills-for-transfer" type="hidden" value="{{ count($result['bills_for_transfer']) }}">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <input type="hidden" class="form-control" id="transfer-forex-selected-bill" name="transfer-forex-selected-bill" value="">
                                        </div>

                                        <div class="col-12 border border-gray-300 p-2 ">
                                            <div class="row">
                                                <div class="col-lg-6">

                                                </div>
                                                <div class="col-lg-6 text-end">
                                                    @can('add-permission', $menu_id)
                                                        <a class="btn btn-secondary btn-sm" type="button" href="{{ route('branch_transactions.transfer_forex') }}">{{ trans('labels.back_action') }}</a>
                                                    @endcan
                                                    @can('add-permission', $menu_id)
                                                        <button class="btn btn-primary btn-sm" type="button" id="transfer-confirm-button" disabled>{{ trans('labels.confirm_action') }}</button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bill-for-transfer-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content add-denom">
                @include('transfer_forex.bills_for_transfer_modal')
            </div>
        </div>
    </div>

    @include('UI.UX.security_code')

@endsection

@section('transf_forex_scripts')
    @include('script.transfer_forex_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
