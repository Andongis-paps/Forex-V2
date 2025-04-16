@extends('template.layout')
@section('content')

   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <!-- Content -->
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-lg-4 mb-4">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                @if(session()->has('message'))
                                    <div class="alert alert-success alert-dismissible" role="alert" id="success-message-saving-success">
                                        {{ session()->get('message') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif


                                @if(count($errors) > 0)
                                    @foreach($errors->all() as $error)
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <span> {{ $error }} </span>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endforeach
                                @endif

                                <div class="col-12">
                                    <div class="card p-0" id="new-buying-transaction-header">
                                        <div class="card-body p-3">
                                            <span class="text-lg font-semibold p-2 text-white">
                                                {{ trans('labels.w_denom_mainte_add_denoms') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                            </div>

                            <form class="m-0" action="{{ route('maintenance.currency_maintenance.update_denom') }}" method="post" id="denomination-form">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                                <div class="row">
                                                    <div class="col-7 ps-3">
                                                        <span class="text-lg font-semibold text-black">
                                                            {{ trans('labels.w_currency_curr_denom') }}
                                                        </span>
                                                    </div>
                                                    <div class="col-5 text-end">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 p-2 border border-gray-300 border-r border-l">
                                                <div class="row px-2 py-0 mt-1">
                                                    <div class="col-12">
                                                        <div class="col-12 mt-2 mb-3">
                                                            <div class="row align-items-center">
                                                                <div class="col-4 ps-3">
                                                                    <strong>
                                                                        <span>{{ trans('labels.w_denom_mainte_curr_select') }}:</span>
                                                                    </strong>
                                                                </div>
                                                                <div class="col-8">
                                                                    <select class="form-control" name="currency" id="currency">
                                                                        <option value="">Select a currency</option>
                                                                        @foreach ($result['currencies'] as $currencies)
                                                                            <option value="{{ $currencies->CurrencyID }}">{{ Str::title($currencies->Currency) }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12 mb-3">
                                                            <div class="row align-items-center">
                                                                <div class="col-5 ps-3">
                                                                    <strong>
                                                                        <span>{{ trans('labels.w_denom_mainte_tranasct_type_select') }}:</span>
                                                                    </strong>
                                                                </div>
                                                                <div class="col-7">
                                                                    <select class="form-control" name="transact-type" id="transact-type" disabled>
                                                                        <option value="">Select a transaction type</option>
                                                                        @foreach ($result['transact_type'] as $transact_type)
                                                                            <option value="{{ $transact_type->TTID }}">{{ Str::title($transact_type->TransType) }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12 mb-3">
                                                            <div class="row px-3">
                                                                <button class="btn btn-primary button-add-denom" id="button-add-denom" type="button" disabled>Add Denom &nbsp; <i class="bx bx-plus"></i></button>
                                                            </div>
                                                        </div>

                                                        <div class="col-12">
                                                            <table class="table table-hover table-bordered" id="currency-denominatiom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-th-buying text-center text-xl font-extrabold text-black py-1 px-1">{{ trans('labels.w_currency_denom') }}</th>
                                                                        <th class="text-th-buying text-center text-xl font-extrabold text-black py-1 px-1">{{ trans('labels.action_data') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="curr-denom-table-new-denom-container">
                                                                    <tr>
                                                                        <td class="text-center text-td-buying text-sm py-3" colspan="2" id="add-denomination-label">
                                                                            <span class="text-lg">
                                                                                <strong>ADD DENOMINATIONS</strong>
                                                                            </span>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr>
                                                                        <td id="td-footer" colspan="2"></td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-span-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                                <div class="row">
                                                    <div class="col-lg-6 offset-6 text-end">
                                                        {{-- <a class="btn btn-secondary" type="button" href="{{ route('denominations') }}">{{ trans('labels.back_action') }}</a> --}}
                                                        <a class="btn btn-secondary" type="button" href="{{ route('maintenance.currency_maintenance') }}">{{ trans('labels.back_action') }}</a>
                                                        <button class="btn btn-primary" type="submit" id="save-denominations" disabled>{{ trans('labels.confirm_action') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Modal - Confirm using security code --}}
                                    {{-- <div class="modal fade" id="buyingTransactModal" tabindex="-1" aria-labelledby="buying-transact" aria-hidden="true">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header px-4">
                                                    <h4 class="modal-title" id="buying-transact">{{ trans('labels.buying_save_action') }}</h4>
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
                                                            <input class="form-control password" id="security_code" name="security_code">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('labels.cancel_action') }}</button>
                                                    <button type="button" class="btn btn-primary" id="save-buying-transaction">{{ trans('labels.proceed_action') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <script>
        $(document).ready(function() {
            $("#denomination-form").validate();

            $('[name^="denominations"]').each(function() {
                if ($(this).hasClass('denominations-input')) {
                    $(this).rules('add', {
                        required: true,
                        pattern: /^\d+(\.\d{1,2})?$/,
                        messages: {
                            required: "Please enter a serial.",
                            pattern: "Invalid bill amount format.",
                        },
                    });
                }
            });
        });
    </script> --}}

@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
