@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-lg-12 mb-4">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <hr>
                            </div>

                            <form class="m-0" action="{{ route('maintenance.currency_maintenance.update_denom') }}" method="post" id="update-denomination-form">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                                <div class="row ps-2">
                                                    <div class="col-12">
                                                        <span class="text-lg font-bold text-black">
                                                            <i class='bx bx-cog'></i>&nbsp;{{ trans('labels.w_currency_curr_denom') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 p-2 border border-gray-300 border-r border-l">
                                                <div class="row px-2 py-0 mt-1">
                                                    <div class="col-12 mb-2">
                                                        <div class="row justify-content-center align-items-center">
                                                            {{-- <div class="col-1 ps-3">
                                                                <strong>
                                                                    <span>{{ trans('labels.w_denom_mainte_curr') }}:</span>
                                                                </strong>
                                                            </div> --}}
                                                            <div class="col-4">
                                                                <input class="form-control" type="text" value="{{ $result['currency']->Currency }}" disabled>
                                                                <input class="form-control" type="hidden" value="{{ $result['currency']->CurrencyID }}" name="currency">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="row justify-content-center">
                                                            <div class="col-2 text-center mb-3">
                                                                <div class="row">
                                                                    @can('add-permission', $menu_id)
                                                                        <button class="btn btn-primary button-add-denom btn-sm" id="button-add-denom" type="button">Add Denom&nbsp;<i class='bx bx-list-plus' ></i></button>
                                                                    @endcan
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="col-12">
                                                            <table class="table table-hover mb-1 table-bordered" id="denomination-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.w_currency_denom') }}</th>
                                                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.w_denom_mainte_trans_type') }}</th>
                                                                        <th class="text-center text-xs font-extrabold text-black p-1">Status</th>
                                                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.action_data') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="denomination-table-body">
                                                                    @forelse ($result['denominations'] as $denominations)
                                                                        <tr class="new-curr-denom-container">
                                                                            <td class="p-1">
                                                                                <input class="form-control denominations-input text-right" name="denominations-exisiting" type="text" value="{{ $denominations->BillAmount }}" disabled>
                                                                            </td>
                                                                            <td class="text-center text-sm text-black p-1">
                                                                                <select class="form-select transact-type" name="transact-type[]" id="transact-type" disabled>
                                                                                    <option value="">Select transaction type</option>
                                                                                    @foreach ($result['transact_type'] as $transact_type)
                                                                                        <option value="{{ $transact_type->TTID }}" @if ( $transact_type->TTID == $denominations->TTID) selected @endif>{{ $transact_type->TransType }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </td>
                                                                            <td class="text-center text-xs p-1">
                                                                                @if ($denominations->Status == 1)
                                                                                    <span class="badge rounded-pill primary-badge-custom font-bold">
                                                                                        <strong>
                                                                                            Active
                                                                                        </strong>
                                                                                    </span>
                                                                                @else
                                                                                    <span class="badge rounded-pill warning-badge-custom font-bold">
                                                                                        <strong>
                                                                                            Inactive
                                                                                        </strong>
                                                                                    </span>
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-center p-1">
                                                                                <a class="btn btn-primary button-edit enable-disable-denom" data-denominationid="{{ $denominations->DenominationID }}" data-denom="{{ $denominations->BillAmount }}" data-type="{{ $denominations->TTID }}" data-status="{{ $denominations->Status }}">
                                                                                    <i class='menu-icon tf-icons bx bx-edit-alt text-white'></i>
                                                                                </a>
                                                                                {{-- <a class="btn btn-primary button-delete delete-denomination" data-denominationid="{{ $denominations->DenominationID }}"><i class='menu-icon tf-icons bx bx-trash text-white'></i></a> --}}
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr id="empty-banner">
                                                                            <td class="text-center p-1 text-sm py-3" colspan="13">
                                                                                <span class="buying-no-transactions text-lg">
                                                                                    <strong>NO CURRENT DENOMINATIONS</strong>
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-span-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                                <div class="row">
                                                    <div class="col-lg-6 offset-6 text-end">
                                                        @can('access-permission', $menu_id)
                                                            <a class="btn btn-secondary btn-sm" type="button" href="{{ route('maintenance.currency_maintenance') }}">{{ trans('labels.back_action') }}</a>.
                                                        @endcan
                                                        @can('add-permission', $menu_id)
                                                            <button class="btn btn-primary btn-sm" type="button" id="update-denominations-button" disabled>{{ trans('labels.confirm_action') }}</button>
                                                        @endcan
                                                    </div>
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

    @include('UI.UX.security_code')
    @include('UI.UX.del_denom_security_code')
    @include('window.currency_mainte.update_curr_denom')

@endsection

@section('currency_mainte_scripts')
    @include('script.curr_denom_maintenance_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
