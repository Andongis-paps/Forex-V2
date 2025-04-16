@extends('template.layout')
@section('content')

    {{-- csrf token retrieveal per user login --}}
    {{session('csrf_token')}}

    <div class="layout-page">
        <!-- Navbar -->
        <div class="content-wrapper">
            {{-- <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                        <i class="bx bx-menu bx-sm"></i>
                    </a>
                </div>
            </nav> --}}

            <!-- Content -->
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-lg-8 mb-4">
                        <div class="row justify-content-center">
                            <div class="col-12">

                                @if(session()->has('message'))
                                    <div class="alert alert-success alert-dismissible" role="alert" id="success-mess-edit-selling" data-successeditselling="1">
                                        {{ session()->get('message') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <div class="col-12 my-3">
                                    <span class="text-lg font-semibold p-2 text-black">
                                        {{ trans('labels.edit_selling_trans_title') }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-12">
                                <form class="m-0" action="{{ URL::to('/updateSellingTrans') }}" method="post" id="edit-selling-transaction">
                                    <div class="card">
                                        @csrf
                                        @foreach ($selling_transact_details as $selling_trans_deets)
                                            <div class="col-12 p-3 pb-0" id="buying-container">
                                                <div class="row align-items-center px-3">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.add_selling_trans_date') }}: &nbsp;<span class="required-class">*</span>
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" name="sell-edit-transact-date" value="{{ $selling_trans_deets->DateSold }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center my-3 px-3">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.add_selling_trans_time') }}: &nbsp;<span class="required-class">*</span>
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" name="sell-edit-time-sold" value="{{ $selling_trans_deets->TimeSold }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center my-3 px-3">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.add_selling_trans_selling_no') }}&nbsp;: &nbsp;<span class="required-class">*</span>
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" name="sell-edit-selling-no" value="{{ $selling_trans_deets->SellingNo }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center my-3 px-3">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.add_selling_trans_receipt_no') }}&nbsp;: &nbsp;<span class="required-class">*</span>
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" name="sell-edit-receipt-no" value="{{ $selling_trans_deets->ReceiptNo }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center my-3 px-3">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.add_selling_trans_currency') }}: &nbsp;<span class="required-class">*</span>
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" name="sell-edit-currency" value="{{ $selling_trans_deets->Currency }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center my-3 px-3">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.add_selling_trans_curr_amnt') }}: &nbsp;<span class="required-class">*</span>
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" id="sell-edit-currency-amnt" name="sell-edit-currency-amnt" value="{{ number_format($selling_trans_deets->CurrAmount, 2, '.', ',') }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center my-3 px-3">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.add_selling_trans_rate_used') }}: &nbsp;<span class="required-class">*</span>
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" id="sell-edit-rate-used" name="sell-edit-rate-used" value="{{ number_format($selling_trans_deets->RateUsed, 2, '.', ',') }}">
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 my-3">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.add_selling_trans_total_amount') }}: &nbsp;<span class="required-class">*</span>
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="hidden" class="form-control" name="sell-edit-trans-total-amount-true" id="selling-trans-total-amount-true" value="{{ number_format($selling_trans_deets->AmountPaid, 2) }}">
                                                        <input type="text" class="form-control" name="sell-edit-trans-total-amount" id="selling-trans-total-amount" value="{{ number_format($selling_trans_deets->AmountPaid, 2, '.', ',') }}" readonly>
                                                    </div>
                                                </div>

                                                <input type="hidden" name="selling-transact-scid" value="{{ $selling_trans_deets->SCID }}">
                                                <input type="hidden" id="user-security-code" value="{{ $selling_trans_deets->SecurityCode }}">
                                            </div>
                                        @endforeach

                                        <input type="hidden" id="url-edit-selling-trans" data-editsellingingurl="{{ route('editsellingtransact', ['id' => $selling_trans_deets->SCID]) }}">

                                        <div class="card-footer pe-4 pb-3 pt-0">
                                            <div class="row">
                                                <div class="col-lg-6">

                                                </div>
                                                <div class="col-lg-6 text-end pe-4">
                                                    <a class="btn btn-secondary text-white" type="button" href="{{ URL::to('/addNewSellingTrans') }}">{{ trans('labels.cancel_action') }}</a>
                                                    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#editSellingTransactModal">{{ trans('labels.confirm_action') }}</button>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Modal - Confirm using security code --}}
                                        <div class="modal fade" id="editSellingTransactModal" tabindex="-1" aria-labelledby="selling-transact" aria-hidden="true">
                                            <div class="modal-dialog modal-sm">
                                                <div class="modal-content">
                                                    <div class="modal-header px-4">
                                                        <h4 class="modal-title" id="buying-transact">{{ trans('labels.edit_selling_edit_action') }}</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row px-2">
                                                            <div class="col-12 mb-2 mt-2">
                                                                <span>
                                                                    <strong>
                                                                        {{ trans('labels.buying_enter_sec_code') }}: &nbsp;<span class="required-class">*</span>
                                                                    </strong>
                                                                </span>
                                                            </div>
                                                            <div class="col-12 mb-3">
                                                                <input class="form-control password" id="security-code-edit-selling-trans" name="security-code-edit-selling-trans" type="password">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('labels.cancel_action') }}</button>
                                                        <button type="button" class="btn btn-primary" id="update-selling-transaction-details">{{ trans('labels.proceed_action') }}</button>
                                                    </div>
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

@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
