@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row">
                    <div class="col-12">

                    </div>

                    <div class="col-12">
                        <hr>
                    </div>

                    <div class="col-lg-12">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <form class="m-0" method="post" id="receive-transfers-form">
                                    @csrf
                                    <div class="card">
                                        <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                            <div class="row align-items-center">
                                                <div class="col-6 ps-3">
                                                    <span class="text-lg font-bold  text-black">
                                                        <i class='bx bx-archive-in' ></i>&nbsp;{{ trans('labels.selling_admin_receive_transf') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="accordion border" id="accordion-receive-transfer-advance-search">
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="flush-headingOne">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-receive-transf-search" aria-expanded="false" aria-controls="flush-receive-transf-search">
                                                            <span class="text-base font-medium">
                                                                <strong>
                                                                    {{ trans('labels.rate_reports_advanced_search') }}
                                                                </strong>
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="flush-receive-transf-search" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordion-receive-transfer-advance-search">
                                                        <div class="accordion-body" style="display: block;">
                                                            <div class="row align-items-center justify-content-center px-3 mb-3">
                                                                {{-- <div class="col-2">
                                                                    <strong>
                                                                        {{ trans('labels.selling_admin_search_filter') }}:
                                                                    </strong>
                                                                </div> --}}
                                                                <div class="col-4">
                                                                    <div class="row text-center">
                                                                        <div class="btn-group btn-group-sm" role="group" aria-label="Basic radio toggle button group">
                                                                            {{-- <input type="radio" class="btn-check" name="radio-search-type" id="transf-type" value="1" checked>
                                                                            <label class="btn btn-outline-primary" for="transf-type">
                                                                                <strong>{{ trans('labels.selling_admin_filter_transf_type') }}</strong>
                                                                            </label> --}}

                                                                            <input type="radio" class="btn-check" name="radio-search-type" id="transf-no" value="2" checked>
                                                                            <label class="btn btn-outline-primary" for="transf-no">
                                                                                <strong>{{ trans('labels.selling_admin_filter_transf_no') }}</strong>
                                                                            </label>

                                                                            <input type="radio" class="btn-check" name="radio-search-type" id="tracking-no" value="3">
                                                                            <label class="btn btn-outline-primary" for="tracking-no">
                                                                                <strong>{{ trans('labels.selling_admin_filter_tracking_no') }}</strong>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- <div class="row align-items-center justify-content-center px-3" id="search-by-transfer-type">
                                                                <div class="col-4 text-center">
                                                                    <div class="btn-group btn-group-sm" role="group" aria-label="Basic radio toggle button group">
                                                                        <input type="radio" class="btn-check" name="radio-receive-transfer-type" id="transf-type-regular" value="BILLS" checked>
                                                                        <label class="btn btn-outline-primary" for="transf-type-regular">
                                                                            <strong>{{ trans('labels.transfer_forex_type_to_mo') }}</strong>
                                                                        </label>

                                                                        <input type="radio" class="btn-check" name="radio-receive-transfer-type" id="transf-type-loose-b" value="LOOSE BILL">
                                                                        <label class="btn btn-outline-primary" for="transf-type-loose-b">
                                                                            <strong>{{ trans('labels.transfer_forex_type_loose') }}</strong>
                                                                        </label>

                                                                        <input type="radio" class="btn-check" name="radio-receive-transfer-type" id="transf-type-coins" value="COINS">
                                                                        <label class="btn btn-outline-primary" for="transf-type-coins">
                                                                            <strong>{{ trans('labels.transfer_forex_type_coins') }}</strong>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div> --}}

                                                            <div class="row align-items-center justify-content-center px-3 mb-3" id="search-by-transfer-no">
                                                                <div class="col-4">
                                                                    <input class="form-control search-transfer-forex-number" id="search-transfer-forex-number" name="search-transfer-forex-number" type="number" placeholder="Search by transfer forex number">
                                                                </div>
                                                            </div>

                                                            <div class="row align-items-center justify-content-center px-3 mb-3 d-none" id="search-by-tracking-no">
                                                                <div class="col-4">
                                                                    <input class="form-control search-tracking-number" id="search-tracking-number" name="search-tracking-number" type="number" placeholder="Search by tracking number">
                                                                </div>
                                                            </div>

                                                            <div class="row justify-content-center px-3 mt-3">
                                                                <div class="col-1 text-center">
                                                                    <div class="row">
                                                                        @can('add-permission', $menu_id)
                                                                            <button class="btn btn-primary btn-sm" id="button-search-transfer-forex" type="button">
                                                                                {{ trans('labels.selling_admin_generate_transf') }}&nbsp;<i class='menu-icon tf-icons bx bx-search-alt-2 text-white ms-1 me-0'></i>
                                                                            </button>
                                                                        @endcan
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <table class="table table-bordered table-hover" id="transfers-result-table">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">
                                                            <input class="form-check-input" type="checkbox" id="transfer-forex-select-all" name="transfer-forex-select-all">
                                                        </th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_reports_branch') }}</th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_admin_transf_type') }}</th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_admin_transf_no') }}</th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_admin_tracking_no') }}</th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_admin_transf_date') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="transfers-result-table-tbody">
                                                    <div id="transf-results-details">
                                                        <td class="text-center text-td-buying text-sm py-3" colspan="12" id="empty-receive-transf-table">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>SEARCH FOR TRANSFERS</strong>
                                                            </span>
                                                        </td>
                                                    </div>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="col-12 px-2 border border-gray-300 rounded-bl rounded-br py-2">
                                            <div class="row align-items-center">
                                                <div class="col-3 my-2 text-start">
                                                    <div id="item-count-serials"></div>
                                                </div>

                                                <div class="col-4 pe-5 my-2 pagination-links">
                                                    <div id="pagination-buttons-receive-transf" class="text-end">
                                                    </div>
                                                </div>

                                                <div class="col-5 text-end">
                                                    @can('access-permission', $menu_id)
                                                        <a class="btn btn-secondary btn-sm" type="button" href="{{ route('admin_transactions.receive_transfer_forex') }}">{{ trans('labels.back_action') }}</a>
                                                    @endcan

                                                    @can('add-permission', $menu_id)
                                                        <button class="btn btn-primary receive-transfers-button btn-sm" id="receive-transfers-button" type="button" disabled>
                                                            Proceed
                                                        </button>
                                                    @endcan
                                                </div>
                                            </div>

                                            <input type="hidden" id="received-transfers-tfid" name="received-transfers-tfid" value="">
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
@endsection

@section('receive_transf_fx_scripts')
    @include('script.receive_transfer_f_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
