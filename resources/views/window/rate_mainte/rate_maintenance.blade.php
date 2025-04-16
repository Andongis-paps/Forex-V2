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

                    {{-- Rate Maintenance --}}
                    <div class="col-lg-12 mb-4">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row justify-content-center">
                                            <div class="col-6">
                                                <span class="text-lg font-semibold p-2 text-black">
                                                    <i class='bx bx-cog'></i>&nbsp;{{ trans('labels.w_based_rate') }}
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                {{-- @can('add-permission', $menu_id)
                                                    <a class="btn btn-primary text-white add-rate-button btn-sm" id="add-rate-button" data-bs-toggle="modal" data-bs-target="#rate-maint-add-modal">
                                                        {{ trans('labels.w_rate_add') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                    </a>
                                                @endcan --}}
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.w_rate_rate_currency') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.w_rate_curr_country') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.w_currency_abbrev') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.w_rate_date') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.w_rate_time') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.w_rate_manila_rate') }}</th>
                                                @can('edit-permission', $menu_id)
                                                    <th class="text-center text-xs font-extrabold text-black p-1"></th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div id="buffered-branch">
                                                @forelse ($result['current_rate'] as $current_rate)
                                                    <tr class="branch-list-table" id="branch-list-table">
                                                        <td class="text-center text-sm p-1">
                                                            {{ $current_rate->Currency }}
                                                        </td>
                                                        <td class="text-center text-sm p-1">
                                                            {{ $current_rate->Country }}
                                                        </td>
                                                        <td class="text-center text-sm p-1">
                                                            {{ $current_rate->CurrAbbv }}
                                                        </td>
                                                        <td class="text-center text-sm p-1">
                                                            {{ \Carbon\Carbon::parse($current_rate->MaxEntryDateTime)->toDateString() }}
                                                        </td>
                                                        <td class="text-center text-sm p-1">
                                                            {{ \Carbon\Carbon::parse($current_rate->MaxEntryDateTime)->toTimeString() }}
                                                        </td>
                                                        <td class="text-right text-sm py-1 pe-3">
                                                            @foreach ($current_rate->Rate as $actual_rate)
                                                                <strong class="text-black">
                                                                    {{ number_format($actual_rate->Rate, 4, '.', ',') }}
                                                                </strong>
                                                            @endforeach
                                                        </td>
                                                        <td class="text-center text-sm p-1">
                                                            @can('edit-permission', $menu_id)
                                                                <a class="btn btn-primary button-edit button-edit-rate pe-2" data-expensemaintid="{{ $current_rate->CRID }}" id="button-edit-rate" data-bs-toggle="modal" data-bs-target="#rate-maint-edit-modal">
                                                                    <i class='bx bx-edit-alt text-white'></i>
                                                                </a>
                                                            @endcan

                                                            @can('access-permission', $menu_id)
                                                                <a class="btn btn-primary button-infos rate-history text-white pe-2" data-currencyid="{{ $current_rate->CurrencyID }}" id="rate-history" data-bs-toggle="modal" data-bs-target="#rate-history-modal">
                                                                    <i class='bx bx-history'></i>
                                                                </a>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="text-center text-td-buying text-sm py-3" colspan="13">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO RATE AVAILABLE</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </div>
                                        </tbody>
                                    </table>

                                    <div class="card-footer p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="col-span-12">
                                            <div class="row align-items-center">
                                                <div class="col-12">
                                                    {{ $result['current_rate']->links() }}
                                                </div>
                                            </div>
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
    </div>

    {{-- Add new currency via AJAX --}}
    <div class="modal fade" id="rate-maint-add-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content add-currency-mainte">
                @include('window.rate_mainte.add_rate_mainte_modal')
            </div>
        </div>
    </div>

    {{-- Update rate details via AJAX --}}
    <div class="modal fade" id="rate-maint-edit-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content update-rate-details">

            </div>
        </div>
    </div>

    <div class="modal fade" id="rate-history-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog ">
            <div class="modal-content">
                @include('window.rate_mainte.rate_history_modal')
            </div>
        </div>
    </div>

@endsection

@section('rate_mainte_scripts')
    @include('script.rate_mainte_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
