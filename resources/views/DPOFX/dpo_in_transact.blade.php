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

                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="card">
                            <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <span class="text-lg font-bold p-2 text-black">
                                            <i class='bx bxs-badge-dollar'></i>&nbsp;{{ trans('labels.dpo_transact_in') }}
                                        </span>
                                    </div>
                                    <div class="col-6 text-end">

                                    </div>
                                </div>
                            </div>

                            <div class="col-12 p-2 border border-gray-300">
                                <form class="mb-0" method="post" id="dpofx-in-form">
                                    @csrf
                                    <div class="row align-items-center justify-content-center px-2">
                                        <div class="col-4">
                                            <label class="mb-1" for="dpo-reference-number">
                                                <strong>
                                                    {{ trans('labels.dpo_add_company') }} :
                                                </strong>
                                            </label>

                                            <select class="form-select" name="select-company" id="select-company">
                                                <option value="">Select a company</option>
                                                @foreach ($result['company'] as $company)
                                                    <option value="{{ $company->CompanyID }}">{{ $company->CompanyName }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- <div class="col-4">
                                            <label class="mb-1" for="dpo-reference-number">
                                                <strong>
                                                    {{ trans('labels.dpo_add_transact_ref_no') }} :
                                                </strong>
                                            </label>

                                            <input class="form-control" name="dpo-reference-number" type="number" disabled>
                                        </div> --}}

                                        {{-- <div class="col-3">
                                            <label class="mb-1" for="dpo-reference-number">
                                                <strong>
                                                    {{ trans('labels.dpo_add_comms_amnt') }} :
                                                </strong>
                                            </label>

                                            <input class="form-control text-right" name="dpo-commission" type="number" readonly>
                                        </div> --}}

                                        <div class="col-4">
                                            <label class="mb-1" for="dpo-reference-number">
                                                <strong>
                                                    {{ trans('labels.dpo_add_transact_date') }} :
                                                </strong>
                                            </label>

                                            <input class="form-control" name="dpo-transact-date" id="dpo-transact-date" type="text" disabled>
                                        </div>
                                    </div>

                                    <div class="row align-items-center justify-content-center px-3 mt-3">
                                        <div class="col-3">
                                            <div class="row">
                                                @can('add-permission', $menu_id)
                                                    <button class="btn btn-primary" type="button" id="generate-dpo-transacts" disabled>{{ trans('labels.dpo_add_transact_generate') }}</button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row align-items-center justify-content-center px-3 mb-2 mt-3">
                                        <div class="col-12 dpo-transact-container p-0">
                                            <table class="table table-hover py-1" id="dpofx-transacts-table">
                                                <thead class="sticky-header">
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">
                                                        <input class="form-check-input" type="checkbox" id="dpofx-select-all" name="dpofx-select-all" checked>
                                                    </th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.dpo_transact_branch') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Company</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.dpo_transact_date') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Receipt Set</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.dpo_transact_mtcn') }}</th>
                                                    {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Commission</th> --}}
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Dollar Payout (FX)</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Rate</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.dpo_transact_amnt') }}</th>
                                                </thead>
                                                <tbody id="dpofx-transacts-table-tbody">

                                                </tbody>
                                                <tfoot class="sticky-footer">
                                                    <tr>
                                                        <td colspan="6"></td>
                                                        <td class="text-right text-sm py-1 px-3">
                                                            <strong><span class="text-sm font-extrabold text-black">&#36;</span>&nbsp; <span id="total-dpofx-amount">0.00</span></strong>
                                                            <input type="hidden" id="true-total-dpofx-amnt" value="">
                                                        </td>
                                                        <td colspan="1"></td>
                                                        <td class="text-right text-sm py-1 px-3">
                                                            <strong><span class="text-sm font-extrabold text-black">PHP</span>&nbsp; <span id="total-peso-amount">0.00</span></strong>
                                                            <input type="hidden" id="true-total-peso-amount" value="">
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                <div class="row">
                                    <div class="col-lg-6">

                                    </div>
                                    <div class="col-lg-6 text-end">
                                        @can('access-permission', $menu_id)
                                            <a class="btn btn-secondary" type="button" href="{{ route('admin_transactions.dpofx.dpo_in') }}">{{ trans('labels.back_action') }}</a>
                                        @endcan
                                        @can('add-permission', $menu_id)
                                            <button class="btn btn-primary" type="button" id="save-dpo-transact" disabled>{{ trans('labels.confirm_action') }}</button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @include('UI.UX.security_code')
            </div>
        </div>
    </div>
@endsection

@section('dpo_scripts')
    @include('script.dpo_transact_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
