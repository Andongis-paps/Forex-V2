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

                    <div class="col-lg-12">
                        <div class="row justify-content-center">
                            <div class="col-8">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-1 text-black">
                                                    <i class='bx bx-select-multiple' ></i>&nbsp;{{ trans('labels.buffer_stocks') }}
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">

                                            </div>
                                        </div>
                                    </div>
                                    <form class="m-0" method="post" id="declare-buffer-form">
                                        @csrf
                                        <div class="col-12">
                                            <table class="table table-bordered table-hover" id="transfers-result-table">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center font-extrabold text-black p-1">
                                                            <input class="form-check-input" type="checkbox" id="transfer-forex-buffer-select-all" name="transfer-forex-buffer-select-all">
                                                        </th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.buffer_stocks_entry_date') }}</th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.buffer_stocks_transact_no') }}</th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.buffer_stocks_rset') }}</th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.buffer_stocks_serials') }}</th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.buffer_stocks_bill_amnt') }}</th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">Rate Used</th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">Principal</th>
                                                    </tr>
                                                </thead>

                                                <tbody id="transfers-result-table-tbody">
                                                    @php
                                                        $principal = 0;
                                                    @endphp

                                                    @forelse ($result['branch_stock_details'] as $branch_stock_details)
                                                        @php
                                                            $principal += $branch_stock_details->Principal;
                                                        @endphp

                                                        <tr>
                                                            <td class="text-center text-td-buying text-xs p-2">
                                                                <input class="form-check-input transfer-forex-buffer-select-one" type="checkbox" id="transfer-forex-buffer-select-one" name="transfer-forex-buffer-select-one" data-fsid="{{ $branch_stock_details->FSID }}" data-billamount="{{ $branch_stock_details->BillAmount }}" data-serials="{{ $branch_stock_details->Serials }}" data-currency="{{ $branch_stock_details->Currency }}" data-principal="{{ $branch_stock_details->Principal }}"">
                                                            </td>
                                                            <td class="text-center text-td-buying text-xs p-2">
                                                                {{ $branch_stock_details->TransactionDate }}
                                                            </td>
                                                            <td class="text-center text-td-buying text-xs p-2">
                                                                {{ $branch_stock_details->TransactionNo }}
                                                            </td>
                                                            <td class="text-center text-td-buying text-xs p-2">
                                                                {{ $branch_stock_details->Rset }}
                                                            </td>
                                                            <td class="text-center text-td-buying text-xs p-2">
                                                                {{ $branch_stock_details->Serials }}
                                                            </td>
                                                            <td class="text-right text-td-buying text-xs py-1 px-3">
                                                                <strong>
                                                                    {{ number_format($branch_stock_details->BillAmount, 2, '.', ',') }}
                                                                </strong>
                                                            </td>
                                                            <td class="text-right text-td-buying text-xs py-1 px-3">
                                                                <strong>
                                                                    {{ number_format($branch_stock_details->SinagRateBuying, 2, '.', ',') }}
                                                                </strong>
                                                            </td>
                                                            <td class="text-right text-td-buying text-xs py-1 px-3">
                                                                <strong>
                                                                    {{ number_format($branch_stock_details->Principal, 2, '.', ',') }}
                                                                </strong>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td class="text-center text-td-buying text-xs py-3" colspan="12" id="empty-receive-transf-table">
                                                                <span class="buying-no-transactions text-lg">
                                                                    <strong>NO AVAILABLE BILLS FOR BUFFER</strong>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                                <input name="branch-id" id="branch-id" type="hidden" value="{{ $branch_id }}">
                                                {{-- <table class="table table-bordered">
                                                    <tbody>
                                                        <tr>
                                                            <td class="p-2" colspan="10">
                                                                {{ $result['branch_stock_details']->links() }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table> --}}
                                            </table>
                                        </div>
                                    </form>

                                    <div class="col-12 p-1 border border-gray-300 rounded-bl rounded-br py-2">
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-1 text-black">
                                                    <i class='bx bx-detail'></i>&nbsp; Details
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 border border-gray-300 px-3 py-2">
                                        <div class="row align-items-center">
                                            <div class="col-4">
                                                <span class="text-black text-sm font-bold">
                                                    Buffer Amount:
                                                </span>
                                            </div>
                                            <div class="col-8">
                                                <input class="form-control text-right" type="number" id="selected-bill-total-amount" readonly placeholder="0.00">
                                                <input type="hidden" id="currency-id" value="{{ $result['branch_stock_details'][0]->CurrencyID }}">
                                                {{-- <span class="text-sm font-extrabold">&#36;</span>&nbsp;<span class="text-sm font-extrabold" id="selected-bill-total-amount"> 0.00</span> --}}
                                            </div>
                                        </div>
                                        <div class="row align-items-center mt-2">
                                            <div class="col-4">
                                                <span class="text-black text-sm font-bold">
                                                    Selling Rate:
                                                </span>
                                            </div>
                                            <div class="col-8">
                                                <input class="form-control text-right" type="number" id="selling-rate" disabled placeholder="0.00">
                                            </div>
                                        </div>
                                        <div class="row align-items-center mt-2">
                                            <div class="col-4">
                                                <span class="text-black text-sm font-bold">
                                                    Exchange Amount:
                                                </span>
                                            </div>
                                            <div class="col-8">
                                                <input class="form-control pe-4 text-right font-bold" type="text" id="exch-amount" readonly placeholder="0.00">
                                                <input type="hidden" id="true-exch-amount">
                                            </div>
                                        </div>
                                        <div class="row align-items-center mt-2">
                                            <div class="col-4">
                                                <span class="text-black text-sm font-bold">
                                                    Total Principal:
                                                </span>
                                            </div>
                                            <div class="col-8">
                                                <input class="form-control pe-4 text-right font-bold" type="text" id="principal" readonly placeholder="0.00" value="">
                                                <input type="hidden" id="true-principal" value="">
                                            </div>
                                        </div>
                                        <div class="row align-items-center mt-2">
                                            <div class="col-4">
                                                <span class="text-black text-sm font-bold">
                                                    Income:
                                                </span>
                                            </div>
                                            <div class="col-8">
                                                <input class="form-control pe-4 text-right font-bold" type="text" id="income" readonly placeholder="0.00">
                                                <input type="hidden" id="true-income">
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <tfoot>
                                        <tr>
                                            <td colspan="4"></td>
                                            <td class="text-center text-black text-td-buying text-sm p-1">
                                                <span class="text-sm">Bill Count:</span>
                                                <strong>
                                                    <span class="text-sm font-extrabold" id="selected-bill-count">0</span>
                                                </strong>
                                            </td>
                                            <td class="text-right text-td-buying text-sm py-1 px-3">
                                                <span class="text-sm">Total Amount:</span>
                                                <strong>
                                                    <span class="text-sm font-extrabold">&#36;</span>&nbsp;<span class="text-sm font-extrabold" id="selected-bill-total-amount"> 0.00</span>
                                                </strong>
                                            </td>
                                        </tr>
                                    </tfoot> --}}
                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br py-2">
                                        <div class="row">
                                            <div class="col-12 text-end">
                                                @can('access-permission', $menu_id)
                                                    <a class="btn btn-secondary btn-sm" type="button" href="{{ route('admin_transactions.buffer.buffer') }}">{{ trans('labels.back_action') }}</a>
                                                @endcan

                                                @can('add-permission', $menu_id)
                                                    <button class="btn btn-primary btn-sm @if (count($result['branch_stock_details']) < 0) disabled @endif" type="button" id="declare-buffer-confirm-button">{{ trans('labels.declare_as_buffer') }}</button>
                                                @endcan
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

    <div class="modal fade" id="bill-for-buffer-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content add-denom">
                @include('buffer.bills_for_buffer_modal')
            </div>
        </div>
    </div>

@endsection

@section('buffer_transfer_scripts')
    @include('script.buffer_stock_deets_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
