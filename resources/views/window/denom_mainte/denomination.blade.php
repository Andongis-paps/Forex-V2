@extends('template.layout')
@section('content')

    <div class="layout-page">
        <!-- Navbar -->
        <div class="content-wrapper">
            <!-- Content -->
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <div class="row justify-content-center">
                            <div class="col-12">

                                @if(session()->has('message'))
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        {{ session()->get('message') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <div class="col-12">
                                    <div class="card p-0" id="new-buying-transaction-header">
                                        <div class="card-body row align-items-center p-3">
                                            <div class="col-4">
                                                <span class="text-lg font-semibold p-2 text-white">
                                                    {{ trans('labels.w_denom_mainte') }}
                                                </span>
                                            </div>

                                            <div class="col-5">
                                                <div class="d-flex align-items-center">
                                                    <div class="col-12">
                                                        {{-- <input type="text" class="form-control search-currency" id="search-currency" name="search-currency-word" value="{{ app('request')->input('search') }}" placeholder="Search for a currency name">
                                                        {{ csrf_field() }} --}}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-3 text-end">
                                                <a class="btn btn-primary text-white button-add-branch" href="{{ route('add_denominations') }}">
                                                    {{ trans('labels.w_denom_mainte_add_denoms') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <hr>
                                </div>

                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-sm font-extrabold text-black">{{ trans('labels.w_denom_mainte_curr') }}</th>
                                                <th class="text-center text-sm font-extrabold text-black">{{ trans('labels.w_denom_mainte_country') }}</th>
                                                <th class="text-center text-sm font-extrabold text-black">{{ trans('labels.w_denom_mainte_curr_abbrv') }}</th>
                                                <th class="text-center text-sm font-extrabold text-black">{{ trans('labels.w_denom_mainte_denom') }}</th>
                                                <th class="text-center text-sm font-extrabold text-black">{{ trans('labels.action_data') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div id="buffered-branch">
                                                @if (count($result['currency_denom']) > 0)
                                                    @foreach ($result['currency_denom'] as $currency_denom)
                                                        <tr>
                                                            <td class="text-center text-sm text-black">
                                                                {{ Str::title($currency_denom->Currency) }}
                                                            </td>
                                                            <td class="text-center text-sm text-black">
                                                                {{ $currency_denom->Country }}
                                                            </td>
                                                            <td class="text-center text-sm text-black">
                                                                {{ $currency_denom->CurrAbbv }}
                                                            </td>
                                                            <td class="text-center text-sm text-black">
                                                                <a class="btn btn-primary button-edit button-edit-trans-details button-update-denom text-white" href="{{ route('maintenance.currency_maintenance.edit_denom', ['currency_id' => $currency_denom->CurrencyID]) }}">
                                                                    <span class="me-1">{{ trans('labels.w_denom_mainte_update_denom') }}</span>
                                                                    <i class='menu-icon tf-icons bx bx-edit-alt text-white'></i>
                                                                </a>
                                                            </td>
                                                            <td class="text-center text-sm text-black">
                                                                <a class="btn btn-primary button-delete button-delete-currency" data-bs-toggle="modal" data-bs-target="#delete-currency-modal" data-currencyid="{{ $currency_denom->CurrencyID }}">
                                                                    <i class='menu-icon tf-icons bx bx-trash text-white'></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </tbody>
                                    </table>

                                    <div class="col-span-12 p-3 border border-gray-300 rounded-bl rounded-br">
                                        {{ $result['currency_denom']->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal - Confirm using security code --}}
    <div class="modal fade" id="delete-currency-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header px-4">
                    <h4 class="modal-title" id="buying-transact">{{ trans('labels.w_delete_currency') }}</h4>
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
                            <input type="password" class="form-control password" id="security-code-delete-currency" name="security-code-delete-currency">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('labels.cancel_action') }}</button>
                    <button type="button" class="btn btn-primary" id="button-delete-currency">{{ trans('labels.proceed_action') }}</button>
                    {{-- <a class="btn btn-primary button-delete button-delete-trans-details" id="button-delete-trans-details" href="{{ route('deletebuyingtransact', ['id' => $transact_details->FTDID]) }}"> --}}
                </div>
            </div>
        </div>
    </div>

@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
