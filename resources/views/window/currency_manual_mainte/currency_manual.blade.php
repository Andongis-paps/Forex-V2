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
                                                    <i class='bx bx-cog'></i>&nbsp;Currency Manual
                                                </span>
                                            </div>

                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.w_currency_name') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.w_rate_curr_country') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.w_currency_abbrev') }}</th>
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
                                                            <td class="text-center text-xs p-1">
                                                                {{ $currencies->Currency }}
                                                            </td>
                                                            <td class="text-center text-xs p-1">
                                                                {{ $currencies->Country }}
                                                            </td>
                                                            <td class="text-center text-xs p-1">
                                                                {{ $currencies->CurrAbbv }}
                                                            </td>
                                                            @can('access-permission', $menu_id)
                                                                <td class="text-center text-xs p-1">
                                                                    <a class="btn btn-primary button-edit button-edit-curr-manual text-white pe-2" href="{{ route('maintenance.currency_manual.view', ['id' => $currencies->CurrencyID]) }}">
                                                                        <i class='bx bx-detail'></i>
                                                                    </a>
                                                                </td>
                                                            @endcan
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

@endsection

@section('currency_manual_scripts')
    @include('script.currency_manual_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
