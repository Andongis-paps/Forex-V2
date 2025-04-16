@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-12">


                    </div>

                    <div class="col-12">
                        <hr>
                    </div>

                    {{-- Admin Currency Stocks --}}
                    <div class="col-lg-12">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-archive-in'></i>&nbsp;Reserved Stocks
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <table class="table table-hovered table-bordered" id="">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.curr_stocks_admin_stocks_curr') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Quantity</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.curr_stocks_admin_stocks_total_curr_amnt') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Principal</th>
                                                    {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Date Held</th> --}}
                                                    {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Held By</th> --}}
                                                    @can('access-permission', $menu_id)
                                                        <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap"></th>
                                                    @endcan
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($result['held_bills'] as $held_bills)
                                                    <tr>
                                                        <td class="text-center text-xs whitespace-nowrap p-1">
                                                            {{ $held_bills->Currency }}
                                                        </td>
                                                        <td class="text-center text-xs whitespace-nowrap p-1">
                                                            {{ $held_bills->total_bill_count }}
                                                        </td>
                                                        <td class="text-right text-xs whitespace-nowrap py-1 px-3">
                                                            <strong>
                                                                {{ number_format($held_bills->total_onhold_amount, 2, '.', ',') }}
                                                            </strong>
                                                        </td>
                                                        <td class="text-right text-xs whitespace-nowrap py-1 px-3">
                                                            <strong>
                                                                {{ number_format($held_bills->total_principal, 2, '.', ',') }}
                                                            </strong>
                                                        </td>
                                                        {{-- <td class="text-center text-xs whitespace-nowrap py-1 px-3">
                                                            {{ $held_bills->OnholdDate }}
                                                        </td> --}}
                                                        {{-- <td class="text-center text-xs whitespace-nowrap py-1 px-3">
                                                            {{ $held_bills->Name }}
                                                        </td> --}}

                                                        @can('access-permission', $menu_id)
                                                            <td class="text-center text-xs whitespace-nowrap p-1">
                                                                <button class="btn btn-primary button-edit held-stocks-details p-1 text-white" data-currencyid="{{ $held_bills->CurrencyID }}" data-bs-toggle="modal" data-bs-target="#held-stock-details-modal">
                                                                    <i class='bx bx-show-alt'></i>
                                                                </button>
                                                                {{-- <button class="btn btn-warning button-danger revert-stocks p-1 text-white" type="button" data-currencyid="{{ $held_bills->CurrencyID }}" data-bs-toggle="modal" data-bs-target="#security-code-modal">
                                                                    <i class='bx bx-revision'></i>
                                                                </button> --}}
                                                            </td>
                                                        @endcan
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="text-center text-td-buying text-sm py-3" colspan="13">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO HELD STOCKS</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-12 px-3 border border-gray-300 rounded-bl rounded-br py-2">
                                        {{ $result['held_bills']->links() }}
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

    {{-- Admin stock details breakdown via AJAX --}}
    <div class="modal fade" id="held-stock-details-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content admin-stock-details-modal-body">
                @include('onhold_bills_admin.held_stocks_details_modal')
            </div>
        </div>
    </div>

@endsection

@section('curr_stocks_scripts')
    @include('script.onhold_stocks_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
