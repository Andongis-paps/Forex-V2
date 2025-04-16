@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <hr>
                    </div>

                    <div class="col-lg-12">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-coin-stack'></i>&nbsp;{{ trans('labels.buffer_stocks') }}
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <table class="table table-bordered table-hover" id="transfers-result-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.buffer_stocks_branch') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">Distant</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.buffer_stocks_currency') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 w-25">{{ trans('labels.buffer_stocks_avl_amnt_stocks') }}</th>
                                                    @can('add-permission', $menu_id)
                                                        <th class="text-center text-xs font-extrabold text-black p-1"></th>
                                                    @endcan
                                                </tr>
                                            </thead>

                                            <tbody id="transfers-result-table-tbody">
                                                @if (count($result['branch_stocks']) > 0)
                                                    @foreach ($result['branch_stocks'] as $branch_stocks)
                                                        <tr>
                                                            <td class="text-center text-td-buying text-xs p-1">
                                                                {{ $branch_stocks->BranchCode }}
                                                            </td>
                                                            <td class="text-center text-xs p-1">
                                                                @if (intval($branch_stocks->DistantLocation) == 1)
                                                                    <span class="badge success-badge-custom">
                                                                        <strong>Yes</strong>
                                                                    </span>
                                                                @else
                                                                    <span class="badge primary-badge-custom">
                                                                        <strong>No</strong>
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center text-td-buying text-xs p-1">
                                                                {{ $branch_stocks->Currency }}
                                                            </td>
                                                            <td class="text-right text-td-buying text-xs py-1 px-3">
                                                                <strong>
                                                                    {{ number_format($branch_stocks->total_stock_amount, 2, '.', ',') }}
                                                                </strong>
                                                            </td>
                                                            @can('add-permission', $menu_id)
                                                                <td class="text-center text-td-buying text-xs p-1">
                                                                    {{-- <a class="btn btn-primary button-edit" href="{{ route('admin_transactions.buffer.stocks', ['branch_id' => $branch_stocks->BranchID]) }}">
                                                                        <i class='bx bx-detail text-white pe-2'></i>
                                                                    </a> --}}
                                                                    <button class="btn btn-primary button-edit text-white pe-2 process-buffer" data-fsids="{{ $branch_stocks->FSIDs }}" data-billamount="{{ $branch_stocks->total_stock_amount }}" data-currency="{{ $branch_stocks->Currency }}" data-currencyid="{{ $branch_stocks->CurrencyID }}" data-branchid="{{ $branch_stocks->BranchID }}" data-bs-toggle="modal" data-bs-target="#buffer-cut-details">
                                                                        <i class='bx bx-detail'></i>
                                                                    </button>
                                                                </td>
                                                            @endcan
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-td-buying text-xs py-3" colspan="12" id="empty-receive-transf-table">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO AVAILABLE STOCKS</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-12 px-3 border border-gray-300 rounded-bl rounded-br py-2">
                                        {{ $result['branch_stocks']->links() }}
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
    @include('buffer.buffer_cut_modal')

@endsection

@section('buffer_transfer_scripts')
    @include('script.buffer_stock_deets_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
