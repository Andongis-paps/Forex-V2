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
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        {{ session()->get('message') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                {{-- <div class="col-12 my-3">
                                    <span class="text-lg font-semibold p-2 text-black">
                                        {{ trans('labels.edit_buying_trans_title') }}
                                    </span>
                                </div> --}}

                                <div class="col-12">
                                    <div class="card p-0" id="new-buying-transaction-header">
                                        <div class="card-body p-3">
                                            <span class="text-lg font-semibold p-2 text-white">
                                                {{ trans('labels.edit_buying_trans_title') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-12">
                                <form class="m-0" action="{{ URL::to('/updateBuyingTrans') }}" method="post" id="edit-buying-transaction">
                                    <div class="card">
                                        @csrf
                                        @foreach ($transact_details as $trans_deets)
                                            <div class="col-12 p-3 pb-0" id="buying-container">
                                                <div class="row align-items-center px-3">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.transact_date') }}: &nbsp;<span class="required-class">*</span>
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" name="transact-date" value="{{ $trans_deets->TransactionDate }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 mt-3">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.transact_time') }}: &nbsp;<span class="required-class">*</span>
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" name="transact-time" value="{{ $trans_deets->TransactionTime }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 mt-3">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.transact_#') }}&nbsp;: &nbsp;<span class="required-class">*</span>
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" name="transact-number" value="{{ $trans_deets->TransactionNo }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 mt-3">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.transact_receipt_#') }}&nbsp;: &nbsp;<span class="required-class">*</span>
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" name="transact-receipt-no" value="{{ $trans_deets->ReceiptNo }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 mt-3">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.transact_curr') }}: &nbsp;<span class="required-class">*</span>
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" name="transact-currency" value="{{ $trans_deets->Currency }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 mt-3">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.transact_type') }}: &nbsp;<span class="required-class">*</span>
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" name="transact-type" value="{{ $trans_deets->TransType }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 mt-3">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.transact_curr_amnt') }}: &nbsp;<span class="required-class">*</span>
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="hidden" class="form-control" name="transact-currency-true" id="transact-currency-true" value="">
                                                        <input type="text" class="form-control" name="transact-currency" id="transact-currency" value="{{ number_format($trans_deets->CurrencyAmount, 2, '.', ',') }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 mt-3">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.transact_rate') }}: &nbsp;<span class="required-class">*</span>
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" name="transact-rate-used" id="transact-rate-used" value="{{ $trans_deets->RateUsed }}">
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 my-3">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.transact_amnt') }}: &nbsp;<span class="required-class">*</span>
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="hidden" class="form-control" name="transact-total-amount-true" id="transact-total-amount-true" value="">
                                                        <input type="text" class="form-control" name="transact-total-amount" id="transact-total-amount" value="{{ number_format($trans_deets->Amount, 2, '.', ',') }}" readonly>
                                                    </div>
                                                </div>

                                                <input type="hidden" name="buying-transact-ftdid" value="{{ $trans_deets->FTDID }}">
                                                <input type="hidden" id="user-security-code" value="{{ $trans_deets->SecurityCode }}">
                                            </div>
                                        @endforeach

                                        <input type="hidden" id="url-edit-buying-trans" data-editbuyingurl="{{ route('editbuyingtransact', ['id' => $trans_deets->FTDID]) }}">

                                        <div class="card-footer pe-4 pb-3 pt-0">
                                            <div class="row">
                                                <div class="col-lg-6">

                                                </div>
                                                <div class="col-lg-6 text-end pe-4">
                                                    <a class="btn btn-secondary text-white" type="button" href="{{ URL::to('/addNewBuyingTrans') }}">{{ trans('labels.cancel_action') }}</a>
                                                    <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#editBuyingTransactModal">{{ trans('labels.confirm_action') }}</button>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Modal - Confirm using security code --}}
                                        <div class="modal fade" id="editBuyingTransactModal" tabindex="-1" aria-labelledby="buying-transact" aria-hidden="true">
                                            <div class="modal-dialog modal-sm">
                                                <div class="modal-content">
                                                    <div class="modal-header px-4">
                                                        <h4 class="modal-title" id="buying-transact">{{ trans('labels.buying_update_action') }}</h4>
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
                                                                <input class="form-control password" id="security-code-edit-trans" name="security-code-edit-trans">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('labels.cancel_action') }}</button>
                                                        <button type="button" class="btn btn-primary" id="save-buying-">{{ trans('labels.proceed_action') }}</button>
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
