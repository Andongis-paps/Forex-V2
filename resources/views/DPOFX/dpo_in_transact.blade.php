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
                                    <div class="row align-items-center justify-content-center">
                                        <div class="col-5">
                                            <input class="form-control" name="dpo-transact-date" id="dpo-transact-date" type="text" placeholder="Date from 'YYYY-MM-DD' to 'YYYY-MM-DD'">
                                        </div>
                                    </div>

                                    {{-- Buying Transaction - Receipt Set --}}
                                    <div class="row align-items-center justify-content-center mt-2 @if(session('time_toggle_status') == 1) d-none @endif">
                                        <div class="col-5">
                                            <div class="row">
                                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                    <input type="radio" class="btn-check" name="radio-rset" id="r-set-o" value="{{ trans('labels.buying_rset_o') }}" @if(session('time_toggle_status') == 1) @endif disabled>
                                                    <label class="btn btn-outline-primary" for="r-set-o">
                                                        <strong>{{ trans('labels.buying_rset_o') }}</strong>
                                                    </label>

                                                    <input type="radio" class="btn-check" name="radio-rset" id="r-set-b" value="{{ trans('labels.buying_rset_b') }}" disabled>
                                                    <label class="btn btn-outline-primary" for="r-set-b">
                                                        <strong>{{ trans('labels.buying_rset_b') }}</strong>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row align-items-center justify-content-center  mt-2">
                                        <div class="col-5">
                                            <textarea class="form-control" id="remarks" name="remarks" rows="2"  placeholder="Remarks" disabled></textarea>
                                        </div>
                                    </div>

                                    <div class="row align-items-center justify-content-center px-3 mt-2">
                                        <div class="col-1">
                                            <div class="row">
                                                @can('add-permission', $menu_id)
                                                    <button class="btn btn-primary btn-sm" type="button" id="generate-dpo-transacts" disabled>{{ trans('labels.dpo_add_transact_generate') }}</button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row align-items-center px-3">
                                        <hr class="my-2">
                                    </div>

                                    <div class="row align-items-center justify-content-center px-3">
                                        <div class="col-12 p-0">
                                            <table class="table table-bordered table-hover mb-0" id="dpofx-transacts-table">
                                                <thead>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">
                                                        <input class="form-check-input" type="checkbox" id="dpofx-select-all" name="dpofx-select-all">
                                                    </th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.dpo_transact_date') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.dpo_transact_branch') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Company</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Receipt Set</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.dpo_transact_mtcn') }}</th>
                                                    {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Commission</th> --}}
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Dollar Payout (FX)</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Rate</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.dpo_transact_amnt') }}</th>
                                                </thead>
                                                <tbody id="dpofx-transacts-table-tbody">

                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="6"></td>
                                                        <td class="text-right text-sm py-1 px-3">
                                                            <strong><span class="text-sm font-extrabold text-black">&#36;</span> <span id="total-dpofx-amount">0.00</span></strong>
                                                            <input type="hidden" id="true-total-dpofx-amnt" value="">
                                                        </td>
                                                        <td colspan="1"></td>
                                                        <td class="text-right text-sm py-1 px-3">
                                                            <strong><span class="text-sm font-extrabold text-black">PHP</span> <span id="total-peso-amount">0.00</span></strong>
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
                                            <a class="btn btn-secondary btn-sm" type="button" href="{{ route('admin_transactions.dpofx.dpo_in') }}">{{ trans('labels.back_action') }}</a>
                                        @endcan
                                        @can('add-permission', $menu_id)
                                            <button class="btn btn-primary btn-sm" type="button" id="save-dpo-transact" disabled>{{ trans('labels.confirm_action') }}</button>
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
