@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
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
                                    <hr>
                                </div>

                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center ps-2">
                                            <div class="col-6">
                                                <span class="text-lg font-bold text-black">
                                                    <i class='bx bx-cog'></i>&nbsp;{{ trans('labels.w_based_currency') }}
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                @can('add-permission', $menu_id)
                                                    <a class="btn btn-primary text-white button-add-branch btn-sm" id="button-add-currency" data-bs-toggle="modal" data-bs-target="#currency-maint-add-modal">
                                                        {{ trans('labels.w_currency_add') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.w_currency_name') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.w_rate_curr_country') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.w_currency_abbrev') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">Receipt Set</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">RIB Variance</th>
                                                @can('access-permission', $menu_id)
                                                    <th class="text-center text-xs font-extrabold text-black p-1"></th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div id="buffered-branch">
                                                @if (count($result['currencies']) > 0)
                                                    @foreach ($result['currencies'] as $currencies)
                                                        <tr class="branch-list-table" id="branch-list-table" data-currencyname="{{ $currencies->Currency }}">
                                                            <td class="text-center text-sm p-1">
                                                                {{ $currencies->Currency }}
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                {{ $currencies->Country }}
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                {{ $currencies->CurrAbbv }}
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                @if ($currencies->WithSetO == 1)
                                                                    <span class="badge primary-badge-custom">
                                                                        O
                                                                    </span>
                                                                @endif

                                                                @if ($currencies->WithSetB == 1)
                                                                    <span class="badge primary-badge-custom">
                                                                        B
                                                                    </span>
                                                                @endif

                                                                @if ($currencies->WithSetO == 0  && $currencies->WithSetB == 0)
                                                                    <strong>Not set</strong>
                                                                @endif
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                <strong>{{ $currencies->RIBVariance }}</strong>
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                @can('add-permission', $menu_id)
                                                                    <a class="btn btn-primary button-update-denom pe-1 btn-sm text-white" href="{{ route('maintenance.currency_maintenance.edit_denom', ['currency_id' => $currencies->CurrencyID]) }}">
                                                                        <span class="me-1">{{ trans('labels.w_currency_update_denom') }}</span>
                                                                    </a>
                                                                @endcan
                                                                @can('edit-permission', $menu_id)
                                                                    <a class="btn btn-primary button-edit button-edit-currency" data-currencymaintid="{{ $currencies->CurrencyID }}" id="button-edit-currency" data-bs-toggle="modal" data-bs-target="#currency-maint-edit-modal">
                                                                        <i class='menu-icon tf-icons bx bx-edit-alt text-white'></i>
                                                                    </a>
                                                                @endcan
                                                                @can('delete-permission', $menu_id)
                                                                    <a class="btn btn-primary button-delete button-delete-currency" data-currencyid="{{ $currencies->CurrencyID }}">
                                                                        <i class='menu-icon tf-icons bx bx-trash text-white'></i>
                                                                    </a>
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </tbody>
                                    </table>

                                    <div class="col-span-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        {{ $result['currencies']->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('UI.UX.security_code')

    {{-- Add new currency via AJAX --}}
    <div class="modal fade" id="currency-maint-add-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content add-currency-mainte">
                @include('window.currency_mainte.add_currency_mainte_modal')
            </div>
        </div>
    </div>

    {{-- Update currency details via AJAX --}}
    <div class="modal fade" id="currency-maint-edit-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content update-currency-details">

            </div>
        </div>
    </div>

@endsection

@section('currency_mainte_scripts')
    @include('script.currency_mainte_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
