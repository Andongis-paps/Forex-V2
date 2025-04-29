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
                                            <i class='bx bx-cog'></i>&nbsp;{{ trans('labels.w_based_rate_config') }}
                                        </span>
                                    </div>
                                    <div class="col-12 py-2 px-3 border border-gray-300">
                                        <div class="row align-items-center justify-content-center">
                                            <div class="col-3">
                                                <label class="mb-1" for="currency-rate-config">
                                                    <strong>Currency:</strong>
                                                </label>
                                                <select class="form-control" name="currency-rate-config" id="currency-rate-config">
                                                    <option value=""></option>
                                                    @foreach($result['currency'] as $currency)
                                                        <option value="{{ $currency->CurrencyID }}" data-currencyrate="{{ number_format($currency->Rate, 4, '.', ',') }}" data-currencyabbrv="{{ $currency->CurrAbbv }}" data-ribvariance="{{ $currency->RIBVariance }}">{{ $currency->Currency }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-2">
                                                <label class="mb-1" for="currency-rate-config">
                                                    <strong>Transaction Type:</strong>
                                                </label>
                                                <select class="form-control" name="transact-type-rate-config" id="transact-type-rate-config" disabled>
                                                    <option value="" id=""></option>
                                                    @foreach ($result['transact_type'] as $transact_type)
                                                        {{-- <option value="{{ $transact_type->TTID }}" >{{ Str::title($transact_type->TransType) }}</option> --}}
                                                        <option value="{{ $transact_type->TTID }}" @if ($transact_type->TTID == 4) disabled @endif>{{ $transact_type->TransType }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-7">
                                                <div class="row align-items-center">
                                                    <div class="col-3">
                                                        <label class="mb-1" for="currency-rate-config">
                                                            <strong>Manila Rate:</strong>
                                                        </label>
                                                        <strong>
                                                            <input type="text" class="form-control text-right font-bold" id="rate-config-selected-rate" name="rate-config-selected-rate" autocomplete="off" readonly>
                                                        </strong>
                                                    </div>
                                                    <div class="col-3">
                                                        <label class="mb-1" for="currency-rate-config">
                                                            <strong>RIB Variance:</strong>
                                                        </label>
                                                        <input type="number" class="form-control text-right font-bold" id="rib-variance" name="rib-variance" autocomplete="off" readonly>
                                                        {{-- <label class="mb-1" for="currency-rate-config">
                                                            <strong>Currency Abbreviation:</strong>
                                                        </label>
                                                        <input type="text" class="form-control font-bold" id="rate-config-selected-curr-abbrv" name="rate-config-selected-curr-abbrv" autocomplete="off" readonly> --}}
                                                    </div>
                                                    <div class="col-3">
                                                        <label class="mb-1" for="currency-rate-config">
                                                            <strong>RIB Buying Rate:</strong>
                                                        </label>
                                                        <strong>
                                                            <input type="text" class="form-control text-right font-bold" id="rib-buying-rate" name="rib-buying-rate" autocomplete="off" readonly>
                                                        </strong>
                                                    </div>
                                                    <div class="col-3">
                                                        <label class="mb-1" for="currency-rate-config">
                                                            <strong>RIB Selling Rate:</strong>
                                                        </label>
                                                        <strong>
                                                            <input type="text" class="form-control text-right font-bold" id="rib-selling-rate" name="rib-selling-rate" autocomplete="off" readonly>
                                                        </strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        @can('edit-permission', $menu_id)
                                            <div class="row align-items-center justify-content-center mt-3 mb-1">
                                                <div class="col-3">
                                                    <div class="row px-1">
                                                        <button class="btn btn-primary btn-sm search-rate-config-button" id="search-rate-config-button" type="button"><i class='bx bx-search-alt'></i> {{ trans('labels.w_rate_config_filter') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcan
                                    </div>
                                    <div class="col-12 border border-gray-300 p-2">
                                        <div class="row align-items-center justify-content-center">
                                            <div class="col-12">
                                                <form class="m-0" method="post" id="rate-config-form">
                                                    @csrf
                                                    <table class="table table-bordered table-hover" id="rate-config-table">
                                                        <thead class="sticky-header">
                                                            <tr>
                                                                <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.w_rate_config_denom') }}</th>
                                                                <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.w_rate_config_variance_b') }}<strong>&nbsp;<span class="required-class">*</span></strong></th>
                                                                <th class="text-center text-sm font-extrabold text-white p-1 !bg-[#00A65A]">{{ trans('labels.w_rate_config_fnl_rate_buying') }}</th>
                                                                {{-- <th class="text-center text-sm font-extrabold text-white p-1 !bg-[#00A65A]">RIB Buying Rate</th> --}}
                                                                <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.w_rate_config_variance_s') }}<strong>&nbsp;<span class="required-class">*</span></strong></th>
                                                                <th class="text-center text-sm font-extrabold  text-white p-1 !bg-[#00A65A]">{{ trans('labels.w_rate_config_fnl_rate_selling') }}</strong></th>
                                                                {{-- <th class="text-center text-sm font-extrabold text-white p-1 !bg-[#00A65A]">RIB Selling Rate</th> --}}
                                                            </tr>
                                                        </thead>
                                                        <tbody id="rate-config-table-body">
                                                            <tr id="update-rate-config-banner">
                                                                <td class="text-center text-td-buying text-sm py-2" colspan="13">
                                                                    <span class="buying-no-transactions">
                                                                        <strong>UPDATE RATE CONFIGURATION</strong>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="13"></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                    <input type="hidden" class="form-control" id="rate-config-currid" name="rate-config-currid" value="">
                                                    <input type="hidden" class="form-control" id="transact-type-id" name="transact-type-id" value="">
                                                    <input type="hidden" class="form-control" id="rate-config-selected-branch" name="rate-config-selected-branch" value="">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 border p-1 border-gray-300 rounded-br rounded-bl">
                                    </div>
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
                                                    <button class="btn btn-primary btn-sm" type="button" id="update-rate-config-button" disabled>{{ trans('labels.w_rate_config_update_rate_config') }}</button>
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
                                                    <th class="text-center text-xs font-extrabold text-black p-1">
                                                        <input class="form-check-input" type="checkbox" id="rate-config-branch-select" name="rate-config-branch-select" disabled>
                                                    </th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.w_rate_config_branches') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1"></th>
                                                </tr>
                                            </thead>
                                            <tbody class="branch-list-container-list-table-body">
                                                @foreach ($result['branches'] as $branch)
                                                    <tr class="rate-config-rows" data-areaom="{{ $branch->OMID }}" data-branchcode="{{ $branch->BranchCode }}">
                                                        <td class="text-center text-sm p-1">
                                                            <input class="form-check-input rate-config-branch-select-one" type="checkbox" value="{{ $branch->BranchCode }}" data-omid="{{ $branch->OMID }}" data-rconfigbranchid="{{ $branch->BranchID }}" disabled>
                                                        </td>
                                                        <td class="text-center text-sm p-1">
                                                            {{ $branch->BranchCode }}
                                                        </td>
                                                        <td class="text-center text-sm p-1">
                                                            <button class="btn btn-primary button-edit current-config pe-2" class="button" data-bs-toggle="modal" data-bs-target="#current-config-modal" data-branchid="{{ $branch->BranchID }}" disabled>
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

    <div class="modal fade" id="current-config-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content add-branch-mainte">
                @include('window.rate_config.current_applied')
            </div>
        </div>
    </div>

@endsection

@section('rate_config_scripts')
    @include('script.rate_config_scripts')
@endsection

{{-- <script>
    $(document).ready(function() {
        $('#branch-list-container-list').css({
            height: 600,
            overflow: 'hidden',
            overflow-y: 'auto'
        });
    });
</script> --}}

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
