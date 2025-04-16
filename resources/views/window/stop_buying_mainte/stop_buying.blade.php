@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row">
                    <div class="col-12">
                        @if(session()->has('message'))
                            <div class="alert alert-success alert-dismissible" role="alert">
                                {{ session()->get('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>

                    <div class="col-12">
                        <hr>
                    </div>

                    <div class="col-12">
                        <div class="row">
                            <div class="col-9 pe-0">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tr rounded-tl">
                                        <span class="text-lg font-bold p-2 text-black">
                                            <i class='bx bx-cog'></i>&nbsp;Denomination Configuration
                                        </span>
                                    </div>
                                    <div class="col-12 py-2 px-3 border border-gray-300">
                                        <div class="row align-items-center justify-content-center">
                                            <div class="col-4">
                                                <label class="text-sm mb-1" for="currency-stop-buying">
                                                    <strong>Currency:</strong>
                                                </label>
                                                <select class="form-control" name="currency-stop-buying" id="currency-stop-buying">
                                                    <option value=""></option>
                                                    @foreach($result['currency'] as $currency)
                                                        <option value="{{ $currency->CurrencyID }}" data-currencyrate="{{ $currency->Rate }}" data-currencyabbrv="{{ $currency->CurrAbbv }}" data-currency="{{ $currency->Currency }}">{{ $currency->Currency }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            {{-- <div class="col-3">
                                                <label class="mb-1" for="currency-rate-config">
                                                    <strong>Transaction Type:</strong>
                                                </label>
                                                <select class="form-control" name="transact-type-stop-buying" id="transact-type-stop-buying" disabled>
                                                    <option value="" id=" "></option>
                                                    @foreach ($result['transact_type'] as $transact_type)
                                                        <option value="{{ $transact_type->TTID }}" @if ($transact_type->TTID == 4) disabled @endif>{{ Str::title($transact_type->TransType) }}</option>
                                                    @endforeach
                                                </select>
                                            </div> --}}
                                            <div class="col-4">
                                                <label class="text-sm mb-1" for="currency-rate-config">
                                                    <strong>Currency Abbreviation:</strong>
                                                </label>
                                                <input type="text" class="form-control font-bold" id="stop-buying-selected-curr-abbrv" name="stop-buying-selected-curr-abbrv" autocomplete="off" readonly>
                                            </div>
                                            <div class="col-4">
                                                <label class="text-sm mb-1" for="currency-rate-config">
                                                    <strong>Manila Rate:</strong>
                                                </label>
                                                <strong>
                                                    <input type="text" class="form-control text-right font-bold" id="stop-buying-selected-rate" name="stop-buying-selected-rate" autocomplete="off" readonly>
                                                </strong>
                                            </div>
                                        </div>

                                        <div class="row align-items-center justify-content-center mt-3 mb-1">
                                            <div class="col-2">
                                                <div class="row px-1">
                                                    @can('add-permission', $menu_id)
                                                        <button class="btn btn-primary search-stop-buying-button btn-sm" id="search-stop-buying-button" type="button"><i class='bx bx-search-alt'></i> {{ trans('labels.w_rate_config_filter') }}</button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 mt-2" id="c-searching-label">
                                            <span class="text-muted"><small><i class="bx bx-info-circle me-1"></i>Note: The selected denominations will no longer be available for both buying and selling transactions at branches.</small></span>
                                        </div>
                                    </div>

                                    <div class="col-12 border border-gray-300 p-2 rounded-br rounded-bl">
                                        <div class="row align-items-center justify-content-center">
                                            <div class="col-12">
                                                <form class="m-0" method="post" id="stop-buying-form">
                                                    @csrf
                                                    <table class="table table-bordered table-hover" id="stop-buying-table">
                                                        <thead class="sticky-header">
                                                            <tr>
                                                                <th class="text-center text-xs font-extrabold text-black p-1">
                                                                    <input class="form-check-input" type="checkbox" id="stop-buying-select-all" name="stop-buying-select-all" disabled>
                                                                </th>
                                                                <th class="text-center text-xs font-extrabold text-black p-1">Transaction Type</th>
                                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.w_rate_config_denom') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="stop-buying-table-body">
                                                            <tr id="update-stop-buying-banner">
                                                                <td class="text-center text-td-buying text-sm py-3" colspan="13">
                                                                    <span class="buying-no-transactions text-lg">
                                                                        <strong>UPDATE STOP BUYING</strong>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td class="p-1" colspan="7"></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>

                                                    <input type="hidden" class="form-control" id="stop-buying-currid" name="stop-buying-currid" value="">
                                                    <input type="hidden" class="form-control" id="transact-type-id" name="transact-type-id" value="">
                                                    <input type="hidden" class="form-control" id="stop-buying-selected-branch" name="stop-buying-selected-branch" value="">
                                                    <input type="hidden" class="form-control" id="stop-buying-selected-b-codes" name="stop-buying-selected-b-codes" value="">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-12 border p-1 border-gray-300 rounded-br rounded-bl">
                                    </div> --}}
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tr rounded-tl">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-cog'></i>&nbsp;Branches
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                @can('edit-permission', $menu_id)
                                                    <button class="btn btn-primary btn-sm" type="button" id="update-stop-buying-button" disabled>Update</button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 p-2 border border-gray-300">
                                        <div class="input-group input-group-sm">
                                            <input class="form-control" type="text" id="branch-search" placeholder="Search branch" disabled>

                                            <select class="form-select" name="om-code" id="om-code" disabled>
                                                <option value="default" id="default">Select area</option>
                                                @foreach ($result['area'] as $area)
                                                    <option value="{{ $area->OMID }}">{{ $area->OMCode }}</option>
                                                @endforeach
                                            </select>

                                            {{-- <select class="form-select" name="province" id="province">
                                                <option value="default">Select province</option>
                                                @foreach ($result['provinces'] as $provinces)
                                                    <option value="{{ $provinces->province_id }}">{{ $provinces->province_name }}</option>
                                                @endforeach
                                            </select> --}}

                                            <button class="btn btn-secondary" type="button" id="clear-search-filter" disabled>Clear</button>
                                        </div>
                                    </div>

                                    <div class="col-12 border border-gray-300 " id="branch-list-container-list">
                                        <table class="table table-hover" id="branch-list-table">
                                            <thead class="sticky-header">
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 w-25">
                                                        <input class="form-check-input" type="checkbox" id="stop-buying-branch-select" name="stop-buying-branch-select" disabled>
                                                    </th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 w-25">{{ trans('labels.w_rate_config_branches') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 w-25"></th>
                                                </tr>
                                            </thead>
                                            <tbody class="branch-list-container-list-table-body">
                                                @foreach ($result['branches'] as $branch)
                                                    <tr class="rate-config-rows" data-areaom="{{ $branch->OMID }}" data-branchcode="{{ $branch->BranchCode }}">
                                                        <td class="text-center text-sm p-1">
                                                            <input class="form-check-input stop-buying-branch-select-one" type="checkbox" value="{{ $branch->BranchCode }}" data-omid="{{ $branch->OMID }}" data-sbuyingbranchid="{{ $branch->BranchID }}" data-sbuyingbranchcode="{{ $branch->BranchCode }}" disabled>
                                                        </td>
                                                        <td class="text-center text-sm p-1">
                                                            {{ $branch->BranchCode }}
                                                        </td>
                                                        <td class="text-center text-sm p-1">
                                                            <button class="btn btn-primary button-edit current-stop-buying pe-2" class="button" data-bs-toggle="modal" data-bs-target="#current-stop-config-modal" data-branchid="{{ $branch->BranchID }}" disabled>
                                                                <i class='bx bx-detail'></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-12 border border-gray-300 rounded-br rounded-bl p-1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('UI.UX.security_code')
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="current-stop-config-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content add-branch-mainte">
                @include('window.stop_buying_mainte.current_applied_s_buying')
            </div>
        </div>
    </div>

@endsection

@section('stop_buying_scripts')
    @include('script.stop_buying_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
