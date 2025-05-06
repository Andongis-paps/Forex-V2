@extends('template.layout')
@section('content')

<div class="layout-page">
    <div class="content-wrapper">
        @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <div class="row">
                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bxs-badge-dollar' ></i>&nbsp;{{ trans('labels.selling_admin_title') }}
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                @can('add-permission', $menu_id)
                                                    <a class="btn btn-primary text-white btn-sm" href="{{ route('admin_transactions.bulk_selling.sell') }}">
                                                        {{ trans('labels.selling_admin_sell_to_manila') }}
                                                    </a>
                                                @endcan

                                                &nbsp;

                                                @can('add-permission', $menu_id)
                                                    <a class="btn btn-primary text-white btn-sm" href="{{ route('admin_transactions.bulk_selling.queue') }}">
                                                        {{ trans('labels.selling_admin_add_forex_to_sell') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-hovered table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Transaction No.</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Date Sold</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Customer</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Rset</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Time Sold</th> --}}
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Exchange Amount</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Principal Amount</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Gain/Loss</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($result['bills_sold_to_mnl'] as $bills_sold_to_mnl)
                                                <tr>
                                                    <td class="text-sm text-center p-1 text-black">
                                                        {{ $bills_sold_to_mnl->STMNo }}
                                                    </td>
                                                    <td class="text-sm text-center p-1 text-black">
                                                        {{ $bills_sold_to_mnl->DateSold }}
                                                    </td>
                                                    <td class="text-sm text-center p-1 text-black">
                                                        {{ $bills_sold_to_mnl->FullName }}
                                                    </td>
                                                    <td class="text-sm text-center p-1 text-black">
                                                        {{ $bills_sold_to_mnl->Rset }}
                                                    </td>
                                                    {{-- <td class="text-sm text-center p-1 text-black">
                                                        {{ $bills_sold_to_mnl->TimeSold }}
                                                    </td> --}}
                                                    <td class="text-sm text-right center py-1 px-3 text-black">
                                                        <strong>
                                                            PHP&nbsp;{{ number_format($bills_sold_to_mnl->TotalExchangeAmount, 2, '.', ',') }}
                                                        </strong>
                                                    </td>
                                                    <td class="text-sm text-right center py-1 px-3 text-black">
                                                        <strong>
                                                            PHP&nbsp;{{ number_format($bills_sold_to_mnl->TotalPrincipal, 2, '.', ',') }}
                                                        </strong>
                                                    </td>
                                                    <td class="text-sm text-right center p-1 text-black">
                                                        <span class="badge @if ($bills_sold_to_mnl->TotalGainLoss >= 0) success-badge-custom @else danger-badge-custom @endif">
                                                            @if ($bills_sold_to_mnl->TotalGainLoss >= 0){{ trans('labels.gain_symbol') }}@else{{ trans('labels.loss_symbol') }}@endif{{ number_format(str_replace('-', '', $bills_sold_to_mnl->TotalGainLoss), 2, '.', ',') }}  @if ($bills_sold_to_mnl->TotalGainLoss >= 0) <i class='bx bxs-up-arrow pb-1' style="font-size: .5rem;"></i> @else <i class='bx bxs-down-arrow pb-1' style="font-size: .5rem;"></i> @endif
                                                        </span>
                                                    </td>
                                                    {{-- <td class="text-sm text-center p-1 text-black">
                                                        {{ $bills_sold_to_mnl->Name }}
                                                    </td> --}}
                                                    @can('access-permission', $menu_id)
                                                        <td class="text-sm text-center p-1 text-black">
                                                            <a class="btn btn-primary button-edit button-edit-trans-details pe-2" id="button-trans-details" href="{{ route('admin_transactions.bulk_selling.details', ['id' => $bills_sold_to_mnl->STMDID]) }}">
                                                                <i class='bx bx-detail'></i>
                                                            </a>
                                                            {{-- <a class="btn btn-primary button-delete button-delete-trans-details" data-bs-toggle="modal" data-bs-target="#deleteBuyingTransactModal" data-transdetailsid="{{ $bills_sold_to_mnl->STMDID }}">
                                                                <i class='menu-icon tf-icons bx bx-trash text-white'></i>
                                                            </a> --}}
                                                        </td>
                                                    @endcan
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-td-buying text-sm py-3" colspan="13">
                                                        <span class="buying-no-transactions text-lg">
                                                            <strong>NO AVAILABLE TRANSACTIONS FOR TODAY</strong>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                {{ $result['bills_sold_to_mnl']->links() }}
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

@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
