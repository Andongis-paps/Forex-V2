@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-12">
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-12">
                        <hr>
                    </div>
                </div>

                <form class="mb-0" method="post" id="dpofx-out-form">
                    @csrf
                    <div class="row">
                        <div class="col-4 pe-0">
                            <div class="card">
                                <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                    <div class="row align-items-center">
                                        <div class="col-12">
                                            <span class="text-lg font-bold p-2 text-black">
                                                <i class="bx bx-select-multiple"></i>&nbsp;{{ trans('labels.dpo_transact_out') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 px-3 py-2 border border-gray-300">
                                    <div class="row align-items-center justify-content-center">
                                        <div class="col-12">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="selling-transact-date-manila" name="transact-date-selling" autocomplete="off" placeholder="yyyy-mm-dd" readonly>
                                                <span class="input-group-text bg-slate-950"><i class='bx bx-calendar'></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row align-items-center justify-content-center mt-2">
                                        <div class="col-12">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="customer-name-selected" value="" readonly>
        
                                                <button class="btn btn-primary !text-sm" id="cuomster-detail" type="button" data-bs-toggle="modal" data-bs-target="#customerDeetsModal">
                                                    Customer
                                                </button>
                                            </div>
        
                                            <input type="hidden" class="form-control" id="customer-id-selected" name="customer-id-selected" value="" readonly>
                                            <input type="hidden" class="form-control" id="customer-no-selected" name="customer-no-selected" value="" readonly>
                                            <input type="hidden" class="form-control" id="customer-entry-id" name="customer-entry-id" value="" readonly>
                                        </div>
                                    </div>
        
                                    <div class="row align-items-center justify-content-center mt-2 @if(session('time_toggle_status') == 1) d-none @endif">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                    <input type="radio" class="btn-check" name="radio-rset" id="r-set-o" value="{{ trans('labels.buying_rset_o') }}" @if(session('time_toggle_status') == 1) @endif disabled="true">
                                                    <label class="btn btn-outline-primary" for="r-set-o">
                                                        <strong>{{ trans('labels.buying_rset_o') }}</strong>
                                                    </label>
        
                                                    <input type="radio" class="btn-check" name="radio-rset" id="r-set-b" value="{{ trans('labels.buying_rset_b') }}" disabled="true">
                                                    <label class="btn btn-outline-primary" for="r-set-b">
                                                        <strong>{{ trans('labels.buying_rset_b') }}</strong>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="row align-items-center justify-content-center mt-2">
                                        <div class="col-12">
                                            <textarea class="form-control" id="remarks" name="remarks" rows="2" disabled placeholder="Remarks"></textarea>
                                        </div>
                                    </div>

                                    <div class="row align-items-center px-3">
                                        <hr class="my-2">
                                    </div>
        
                                    <div class="row justify-content-center align-items-center justify-content-center mb-2">
                                        <div class="col-12">
                                            <div class="input-group">
                                                <span class="input-group-text font-bold">$</span>
                                                {{-- <span class="input-group-text">Selling Rate</span> --}}
        
                                                <input class="form-control" type="number" name="petnet-selling-rate" id="petnet-selling-rate" disabled>
                                            </div>
                                        </div>
                                    </div>
        
                                    <div class="row justify-content-center align-items-center mt-2">
                                        <div class="col-6 text-center">
                                            <div class="row">
                                                @can('add-permission', $menu_id)
                                                    <button class="btn btn-primary btn-sm" type="button" id="generate-dpo-in-data">Generate</button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                    <div class="row">
                                        <div class="col-12 text-end">
                                            @can('access-permission', $menu_id)
                                                <a class="btn btn-secondary btn-sm" type="button" href="{{ route('admin_transactions.dpofx.dpo_out') }}">{{ trans('labels.back_action') }}</a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-8">
                            <div class="card">
                                <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                    <span class="text-lg font-bold p-2 text-black">
                                        <i class="bx bx-list-check"></i>&nbsp;{{ trans('labels.dpo_transact_out') }}
                                    </span>
                                </div>

                                <div class="col-12">
                                    <table class="table table-bordered table-hover py-1" id="dpoin-transacts-table">
                                        <thead class="sticky-header">
                                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">
                                                <input class="form-check-input" type="checkbox" id="dpoin-select-all" name="dpoin-select-all">
                                            </th>
                                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Company</th>
                                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.dpo_transact_mtcn') }}</th>
                                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Dollar Amount</th>
                                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Selling Rate</th>
                                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Rate Used</th>
                                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Exch. Amount</th>
                                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.dpo_transact_amnt') }}</th>
                                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Gain/Loss</th>
                                        </thead>
                                        <tbody id="dpoin-transacts-table-tbody">

                                        </tbody>
                                        <tfoot class="sticky-footer">
                                            <tr>
                                                <td colspan="3"></td>
                                                <td class="text-right text-sm py-1 pe-2">
                                                    <strong><span class="text-sm font-extrabold text-black">&#36;</span>&nbsp; <span id="total-dpofx-amount">0.00</span></strong>
                                                    <input type="hidden" id="true-total-dpofx-amnt" value="">
                                                </td>
                                                <td colspan="2"></td>
                                                <td class="text-right text-sm py-1 pe-2">
                                                    <strong><span class="text-sm font-extrabold text-black">PHP</span>&nbsp; <span id="total-peso-amount">0.00</span></strong>
                                                    <input type="hidden" id="true-total-peso-amount" value="">
                                                </td>
                                                <td class="text-right text-sm py-1 pe-2">
                                                    <strong><span class="text-sm font-extrabold text-black">PHP</span>&nbsp; <span id="total-exhc-amount">0.00</span></strong>
                                                    <input type="hidden" id="true-total-exhc-amount" value="">
                                                </td>
                                                <td class="text-right text-sm py-1 pe-2" id="total-gain-loss">

                                                </td>
                                                <input type="hidden" id="true-total-gain-loss" value="">
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                    <div class="row">
                                        <div class="col-lg-6">

                                        </div>
                                        <div class="col-lg-6 text-end">
                                            @can('add-permission', $menu_id)
                                                <button class="btn btn-primary btn-sm" type="button" id="save-dpo-out-transact" disabled>{{ trans('labels.confirm_action') }}</button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="col-5">
                            @include('UI.UX.customer_info_card')
                        </div> --}}
                    </div>
                </form>

                @include('UI.UX.security_code')
                @include('UI.UX.customer_searching')
            </div>
        </div>
    </div>
@endsection

@section('dpo_scripts')
    @include('script.dpo_out_transact_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
