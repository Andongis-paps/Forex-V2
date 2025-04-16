@extends('template.layout')
@section('content')

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
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <div class="row justify-content-center">
                            {{-- <div class="col-12">
                                <div class="col-12 my-3">
                                    <span class="text-lg font-semibold p-2 text-black">
                                        {{ trans('labels.buying_trans_title') }}
                                    </span>
                                </div>
                            </div> --}}



                            <div class="col-12">
                                <div class="card p-0" id="new-buying-transaction-header">
                                    <div class="card-body p-3">
                                        <span class="text-lg font-semibold p-2 text-white">
                                            {{ trans('labels.sold_serials_reports_title') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-2 pe-0">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-12 my-1 ">
                                                <span class="text-xl ps-2 font-semibold text-black">
                                                    {{ trans('labels.rate_reports_advanced_search') }} <i class='bx bx-file-find pb-1'></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 border border-gray-300">
                                        <div class="row px-3 mt-3">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="search-rate-date-from-sold-serial" name="search-rate-date-from-sold-serial" placeholder="Select date from (yyyy-mm-dd)">
                                                <span class="input-group-text bg-slate-950"><i class='bx bx-calendar'></i></span>
                                            </div>
                                        </div>

                                        <div class="row px-3 my-3">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="search-rate-date-to-sold-serial" name="search-rate-date-to-sold-serial" placeholder="Select date to (yyyy-mm-dd)">
                                                <span class="input-group-text bg-slate-950"><i class='bx bx-calendar'></i></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 branch-list-container-list-sold-serials border border-gray-300">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-xl font-extrabold text-black py-1 px-1">
                                                        <div class="row align-items-center ps-1">
                                                            <div class="col-3 ps-4">
                                                                <div class="text-rate-maintenance col-4 offset-2 px-0">
                                                                    <input class="form-check-input" type="checkbox" id="sold-serials-branch-select" name="sold-serials-branch-select" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="col-3">
                                                                <label for="" class="ms-1">All</label>
                                                            </div>
                                                        </div>
                                                    </th>
                                                    <th class="text-center text-xl font-extrabold text-black py-1">
                                                        <div class="row align-items-center ps-1">
                                                            {{ trans('labels.w_rate_config_branches') }}
                                                        </div>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="branch-list-container-list-table-body">
                                                @foreach ($result['branches'] as $branch)
                                                    <tr>
                                                        <td class="text-center text-sm rate-config-cell">
                                                            <input class="form-check-input sold-serials-branch-select-one" type="checkbox" value="{{ $branch->BranchCode }}" data-soldserialsbranchid="{{ $branch->BranchID }}" disabled>
                                                        </td>
                                                        <td class="text-center text-sm">
                                                            {{ $branch->BranchCode }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <input type="hidden" class="form-control" id="sold-serials-selected-branch" name="sold-serials-selected-branch" value="">
                                    </div>

                                    <div class="card-footer border px-4 py-2 border-gray-300 rounded-bl rounded-br">
                                        <div class="row">
                                            <button class="btn btn-primary" id="button-search-sold-serial-report" type="button">
                                                {{ trans('labels.rate_reports_search') }}&nbsp;<i class='bx bx-search-alt ps-1'></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-10">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-5">
                                                <span class="text-lg font-semibold p-2 text-black">
                                                    {{ trans('labels.sold_serials_title_gen') }}
                                                </span>
                                            </div>
                                            <div class="col-5">
                                                {{-- <div class="row align-items-center">
                                                    @csrf

                                                    <div class="row">
                                                        <div class="col-4">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" id="search-rate-date-from" name="search-rate-date-from" placeholder="Select date from">
                                                                <span class="input-group-text bg-slate-950"><i class='bx bx-calendar'></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" id="search-rate-date-to" name="search-rate-date-to" placeholder="Select date to">
                                                                <span class="input-group-text bg-slate-950"><i class='bx bx-calendar'></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <button class="btn btn-primary" id="button-search-rate-report" type="button">Search&nbsp;<i class='bx bx-search-alt ps-2'></i></button>
                                                        </div>
                                                    </div>
                                                </div> --}}
                                            </div>
                                            <div class="col-2 text-end pe-4">
                                                {{-- <button class="btn btn-primary" id="serial-stock-modal-button" type="button" data-bs-toggle="modal" data-bs-target="#searching-rate-reports-modal"><i class='bx bx-search-alt pb-1'></i> &nbsp;{{ trans('labels.rate_reports_advanced_search') }}</button> --}}
                                                <button class="btn btn-primary" id="export-sold-serial-report" type="button" data-bs-toggle="modal" data-bs-target="#export-sold-serials-report-modal" disabled>{{ trans('labels.rate_reports_export') }}&nbsp;<i class='bx bxs-file-doc ps-2 pb-1'></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover" id="sold-serials-report-table">
                                        <thead>
                                            <tr>
                                                <th class="text-th-selling text-center text-xl font-extrabold text-black py-1 px-1">{{ trans('labels.serials_reports_branch') }}</th>
                                                <th class="text-th-selling text-center text-xl font-extrabold text-black py-1 px-1">{{ trans('labels.serials_reports_currency') }}</th>
                                                <th class="text-th-selling text-center text-xl font-extrabold text-black py-1 px-1">{{ trans('labels.serials_reports_serial') }}</th>
                                                <th class="text-th-selling text-center text-xl font-extrabold text-black py-1 px-1">{{ trans('labels.serials_reports_bill_amnt') }}</th>
                                                <th class="text-th-selling text-center text-xl font-extrabold text-black py-1 px-1">{{ trans('labels.serials_reports_rate_used') }}</th>
                                                <th class="text-th-selling text-center text-xl font-extrabold text-black py-1 px-1">{{ trans('labels.serials_reports_amount_to_peso') }}</th>
                                                <th class="text-th-selling text-center text-xl font-extrabold text-black py-1 px-1">{{ trans('labels.serials_reports_entry_date') }}</th>
                                                <th class="text-th-selling text-center text-xl font-extrabold text-black py-1 px-1">{{ trans('labels.serials_reports_sold_by') }}</th>
                                                <th class="text-th-selling text-center text-xl font-extrabold text-black py-1 px-1">{{ trans('labels.serials_reports_date_sold') }}</th>
                                                <th class="text-th-selling text-center text-xl font-extrabold text-black py-1 px-1">{{ trans('labels.serials_reports_time_sold') }}</th>
                                                <th class="text-th-selling text-center text-xl font-extrabold text-black py-1 px-1">{{ trans('labels.serials_reports_sold_to') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div id="rate-report-details">
                                                <td class="text-center text-td-buying text-sm py-3" colspan="12" id="empty-serial-report-table">
                                                    <span class="buying-no-transactions text-lg">
                                                        <strong>START SEARCHING FOR SOLD SERIALS</strong>
                                                    </span>
                                                </td>
                                            </div>
                                        </tbody>
                                    </table>

                                    <div class="col-12 p-1 px-3 border border-gray-300 rounded-bl rounded-br" id="pagination-container">
                                        <div class="row align-items-center">
                                            <div class="col-6 my-3 text-start">
                                                <div id="item-count-serials"></div>
                                            </div>

                                            <div class="col-6 my-3 pagination-links">
                                                <div id="pagination-buttons-sold-serial" class="text-end">
                                                    {{-- <span></span>
                                                    <button id="pagination-previous" class="btn btn-primary mr-2">Previous</button>
                                                    <button id="pagination-next" class="btn btn-primary">Next</button> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Modal - Confirm using security code --}}
                                    <div class="modal fade" id="export-sold-serials-report-modal" tabindex="-1" aria-labelledby="selling-transact" aria-hidden="true">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header px-4">
                                                    <h4 class="modal-title" id="selling-transact">{{ trans('labels.serials_reports_export_modal') }}</h4>
                                                </div>
                                                <div class="modal-body">
                                                    {{-- <div class="row px-2 text-center">
                                                        <div class="col-12">
                                                            <span class="text-lg font-medium">
                                                                {{ trans('labels.buying_delete_message') }}
                                                            </span>
                                                        </div>
                                                    </div> --}}
                                                    <input type="hidden" id="sold-serial-report-sec-code" data-soldserialreportseccode="{{$result['user_data']->SecurityCode}}">
                                                    <div class="row px-2">
                                                        <div class="col-12 mb-2 mt-2">
                                                            <span>
                                                                <strong>
                                                                    {{ trans('labels.buying_enter_sec_code') }}: &nbsp;<span class="required-class">*</span>
                                                                </strong>
                                                            </span>
                                                        </div>
                                                        <div class="col-12 mb-3">
                                                            <input type="password" class="form-control password" id="export-sold-serial-rep-sec-code" name="export-sold-serial-rep-sec-code">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('labels.cancel_action') }}</button>
                                                    <button type="button" class="btn btn-primary" id="button-sold-serial-report-export-to-excel">{{ trans('labels.proceed_action') }}</button>
                                                    {{-- <a class="btn btn-primary button-delete button-delete-trans-details" id="button-delete-trans-details" href="{{ route('deletebuyingtransact', ['id' => $transact_details->FTDID]) }}"> --}}
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
    </div>


    {{-- Seaching rate for report via AJAX --}}
    {{-- <div class="modal fade" id="searching-rate-reports-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content searching-rate-reports">
                @include('reports.rate_reports_search_modal')
            </div>
        </div>
    </div> --}}

@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
