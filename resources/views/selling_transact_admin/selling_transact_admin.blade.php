@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-12">
                        @if(session()->has('message'))
                            <div class="alert alert-success alert-dismissible" role="alert" id="selling-admin-to-manila-success-message" data-successexistence="1">
                                {{ session()->get('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>

                    <div class="col-12">
                        <hr>
                    </div>

                    <div class="col-lg-12 mb-4">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <form class="m-0" method="POST" id="selling-transact-admin-form">
                                    @csrf
                                    <div class="card selling-to-manila-set-o">
                                        <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                            <div class="row align-items-center justify-content-center">
                                                <div class="col-6">
                                                    <span class="text-lg font-bold p-2 text-black">
                                                        <i class='bx bxs-dollar-circle'></i>&nbsp;{{ trans('labels.selling_admin_select_transfers_to_manila') }}
                                                    </span>
                                                </div>

                                                <div class="col-6 text-end">
                                                    @can('add-permission', $menu_id)
                                                        <a class="btn btn-primary text-white btn-sm" href="{{ route('admin_transactions.bulk_selling.queue') }}">
                                                            {{ trans('labels.selling_admin_add_forex_to_sell') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                        </a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 pb-0 border border-l border-r border-gray-400 border-opacity-100" id="selling-container-set-o">
                                            <div class="row align-items-center justify-content-center mt-2">
                                                {{-- <div class="col-2 offset-2">
                                                    <strong>
                                                        {{ trans('labels.selling_trans_date') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div> --}}
                                                <div class="col-5">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="selling-transact-date-manila" name="transact-date-selling" autocomplete="off" placeholder="yyyy-mm-dd" readonly>
                                                        <span class="input-group-text bg-slate-950"><i class='bx bx-calendar'></i></span>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Buying Transaction - Customer Details --}}
                                            <div class="row align-items-center justify-content-center mt-2">
                                                {{-- <div class="col-2 offset-2">
                                                    <strong>
                                                        {{ trans('labels.buying_customer_name') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div> --}}
                                                <div class="col-5">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="customer-name-selected" value="{{ !empty($result->FullName) ? $result->FullName : '' }}" readonly>
                                                        <input type="hidden" class="form-control" id="customer-id-selected" name="customer-id-selected" value="{{ !empty($result->CustomerID) ? $result->CustomerID : '' }}" readonly>
                                                        <input type="hidden" class="form-control" id="customer-no-selected" name="customer-no-selected" value="{{ !empty($result->CustomerNo) ? $result->CustomerNo : '' }}" readonly>
                                                        <input type="hidden" class="form-control" id="customer-entry-id" name="customer-entry-id" value="{{ !empty($result->CustomerID) ? $result->CustomerID : '' }}" readonly>
                                                        <button class="btn btn-primary" id="customer-detail" type="button" disabled data-bs-toggle="modal" data-bs-target="#customerDeetsModal">Customer</button>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Buying Transaction - Receipt Set --}}
                                            <div class="row align-items-center justify-content-center mt-2 @if(session('time_toggle_status') == 1) d-none @endif">
                                                {{-- <div class="col-2 offset-2">
                                                    <strong>
                                                        {{ trans('labels.buying_rset') }}: &nbsp;<span class="required-class">*</span>
                                                    </strong>
                                                </div> --}}

                                                <div class="col-5">
                                                    <div class="row">
                                                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                            <input type="radio" class="btn-check" name="radio-rset" id="r-set-o" value="{{ trans('labels.buying_rset_o') }}" @if(session('time_toggle_status') == 1) @endif @if(empty($result->CustomerID)) disabled @else @endif>
                                                            <label class="btn btn-outline-primary" for="r-set-o">
                                                                <strong>{{ trans('labels.buying_rset_o') }}</strong>
                                                            </label>

                                                            <input type="radio" class="btn-check" name="radio-rset" id="r-set-b" value="{{ trans('labels.buying_rset_b') }}" @if(empty($result->CustomerID)) disabled @else @endif>
                                                            <label class="btn btn-outline-primary" for="r-set-b">
                                                                <strong>{{ trans('labels.buying_rset_b') }}</strong>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row align-items-center justify-content-center  mt-2">
                                                <div class="col-5">
                                                    {{-- <label class="mb-2" for="remarks">
                                                        <span class="text-black text-sm font-bold">
                                                            Remarks:
                                                        </span>
                                                    </label> --}}

                                                    <textarea class="form-control" id="remarks" name="remarks" rows="2"  placeholder="Remarks" @if(empty($result->CustomerID)) disabled @else @endif></textarea>
                                                </div>
                                            </div>

                                            <div class="row justify-content-center px-4 mt-2">
                                                <div class="col-1">
                                                    <div class="row">
                                                        @can('add-permission', $menu_id)
                                                            <button class="btn btn-primary btn-sm get-bills" id="get-bills" type="button" @if(empty($result->CustomerID)) disabled @else @endif>
                                                                {{ trans('labels.selling_admin_transact_generate_bills') }}
                                                            </button>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row align-items-center px-4">
                                                <hr class="my-2">
                                            </div>

                                            <div class="row align-items-center px-2 mb-2">
                                                <div class="col-12 px-3" id="selling-admin-container">
                                                    <table class="table table-bordered table-bordered selling-to-manila-table" id="selling-to-manila-table">
                                                        <thead class="sticky-header">
                                                            <tr>
                                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.selling_admin_s_comp') }}</th>
                                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.selling_admin_s_currency') }}</th>
                                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">Type</th>
                                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.selling_admin_s_cur_amnt') }}</th>
                                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.selling_admin_s_rate_used') }}</th>
                                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.selling_admin_s_selling_rate') }}</th>
                                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.selling_admin_s_exchange_amnt') }}</th>
                                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.selling_admin_s_capital') }}</th>
                                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.selling_admin_s_gain_loss') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="selling-to-manila-set-o-tbody">
                                                            <td class="text-center text-td-buying text-xs py-3" colspan="12" id="empty-selling-to-manila-table">
                                                                <span class="selling-to-manila-set-o-no-transactions text-lg">
                                                                    <strong>GENERATE BILLS TO SELL</strong>
                                                                </span>
                                                            </td>
                                                        </tbody>
                                                        <tfoot id="selling-admin-table-foot">
                                                            <tr>
                                                                <td class="text-center text-sm py-1 px-2" colspan="1"></td>
                                                                <td class="text-center text-sm py-1 px-2" colspan="1"></td>
                                                                <td class="text-center text-sm whitespace-nowrap py-1 px-2" colspan="1">
                                                                </td>
                                                                <td class="text-right text-sm whitespace-nowrap py-1 px-2" colspan="1">
                                                                    <strong>
                                                                        <span id="total-generated-amount"></span>
                                                                    </strong>

                                                                    <input id="total-generated-amount-input" type="hidden" value="">
                                                                </td>
                                                                <td class="text-center text-sm py-1 px-2" colspan="2">
                                                                </td>
                                                                <td class="text-right text-sm whitespace-nowrap py-1 px-2" colspan="1">
                                                                    <strong>
                                                                        <span id="total-generated-ex-ex-r">0.00</span>
                                                                    </strong>

                                                                    <input name="total-generated-ex-ex-r-input" id="total-generated-ex-ex-r-input" type="hidden" value="">
                                                                    <input name="total-excluded-add-funds" id="total-excluded-add-funds" type="hidden" value="">
                                                                </td>
                                                                <td class="text-right text-sm py-1 px-2" colspan="1">
                                                                    <strong>
                                                                        <span id="total-generated-capital">0.00</span>
                                                                    </strong>

                                                                    <input name="total-generated-capital-input" id="total-generated-capital-input" type="hidden" value="">
                                                                </td>
                                                                <td class="text-right text-sm whiespace-nowrap py-1 px-2" colspan="1" id="total-generated-gain-loss">
                                                                    {{-- <strong>
                                                                        <span class="badge" id="total-generated-gain-loss">0.00</span>
                                                                    </strong> --}}
                                                                </td>

                                                                <input name="total-generated-gain-loss-input" id="total-generated-gain-loss-input" type="hidden" value="">
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <table hidden class="table" id="total-company-sales-table">
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="card-footer border border-gray-300 rounded-bl rounded-br pe-2 pb-2 pt-2">
                                            <div class="row">
                                                <div class="col-6 offset-6 text-end">
                                                    @can('access-permission', $menu_id)
                                                        <a class="btn btn-secondary btn-sm" type="button" href="{{ route('admin_transactions.bulk_selling') }}">{{ trans('labels.back_action') }}</a>
                                                    @endcan

                                                    @can('add-permission', $menu_id)
                                                        <button class="btn btn-primary btn-sm" id="confirm-selling-to-mnl" type="button" disabled>
                                                            {{ trans('labels.confirm_action') }}
                                                        </button>
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

    @include('UI.UX.security_code')
    @include('UI.UX.customer_searching')
    @include('UI.UX.unqueue_over_cap_bills_modal')
    @include('UI.UX.unqueue_security_code_modal')

@endsection

@section('selling_admin_scripts')
    @include('script.selling_admin_mnl_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
