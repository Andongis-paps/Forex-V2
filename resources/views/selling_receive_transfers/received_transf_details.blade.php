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
                                <hr>
                            </div>

                            <div class="col-5">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="col-6">
                                            <span class="text-lg font-bold text-black">
                                                <i class='bx bx-list-ul' ></i>&nbsp;{{ trans('labels.selling_admin_received_transf_deets') }}
                                            </span>
                                        </div>
                                    </div>
                                    @if (count($result['transfer_forex']) > 0)
                                        @foreach ($result['transfer_forex'] as $transfer_deets)
                                            <div class="col-12 p-1 pb-0 border border-gray-300" id="buying-container">
                                                <div class="row align-items-center px-3 mt-1">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.transfer_deets_transf_no') }} &nbsp; :
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" id="transfer-deets-transfer-number" name="transfer-deets-transfer-number" value="{{ $transfer_deets->TransferForexNo }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 mt-2">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.transfer_deets_transf_date') }} &nbsp; :
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" id="transfer-deets-transfer-date" name="transfer-deets-transfer-date" value="{{ $transfer_deets->TransferDate }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 mt-2 mb-2">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.transfer_deets_transf_type') }} &nbsp; :
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" id="transfer-deets-transfer-type" name="transfer-deets-transfer-type" value="{{ $transfer_deets->Remarks }}" readonly>
                                                        <input type="hidden" class="form-control" id="transfer-deets-branch" value="{{ $transfer_deets->BranchCode }}" readonly>
                                                    </div>
                                                </div>

                                                <input type="hidden" id="transfer-details-tfid" value="{{ $transfer_deets->TransferForexID }}" readonly>
                                            </div>
                                        @endforeach
                                    @endif
                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="col-12 text-end">
                                            @can('access-permission', $menu_id)
                                                <a class="btn btn-secondary btn-sm" href="{{ route('admin_transactions.receive_transfer_forex') }}">{{ trans('labels.back_action') }}</a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <div class="card">
                                        <div class="col-12  p-2 border border-gray-300 rounded-tl rounded-tr">
                                            <div class="row">
                                                <div class="col-12">
                                                    <span class="text-lg font-bold text-black">
                                                        <i class='bx bx-list-ul' ></i>&nbsp;{{ trans('labels.transfer_deets_transfer_summary') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <table class="table table-bordered table-hover" id="transfer-summary-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_deets_summary_currency') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_deets_summary_amnt_curr') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($result['serial_count_per_currency']) > 0)
                                                    @foreach ($result['serial_count_per_currency'] as $serial_count_per_currency)
                                                        <tr>
                                                            <td class="text-center text-sm p-1">
                                                                <span id="trans-count text-sm">
                                                                    {{ $serial_count_per_currency->Currency }}
                                                                </span>
                                                                <input type="hidden" class="transfer-summ-currency" value="{{ $serial_count_per_currency->Currency }}">
                                                            </td>

                                                            <td class="text-right text-sm py-1 px-3">
                                                                <span class="font-medium text-sm" id="bill-amount">
                                                                    <strong>
                                                                        {{ number_format($serial_count_per_currency->total_bill_amount, 2, '.' , ',') }}
                                                                    </strong>
                                                                </span>
                                                                <input type="hidden" class="transfer-summ-total-amount" value="{{ number_format($serial_count_per_currency->total_bill_amount, 2, '.' , ',') }}">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td class="text-center text-td-buying p-1" colspan="3"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <div class="card">
                                        <div class="col-12  p-2 border border-gray-300 rounded-tl rounded-tr">
                                            <div class="row">
                                                <div class="col-12">
                                                    <span class="text-lg font-bold text-black">
                                                        <i class='bx bx-list-ul' ></i>&nbsp;{{ trans('labels.transfer_deets_transfer_breakdown') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <table class="table table-bordered table-hover" id="transfer-breakd-down-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_deets_summary_currency') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_forex_bill_amnt') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_deets_summary_curr_count') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_deets_break_d_total_amnt') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($result['serial_breakdown']) > 0)
                                                    @php
                                                        $original_count = count($result['serial_breakdown']);
                                                        $spliced_count = count($result['serial_breakdown']->slice(0, 5));
                                                        $display_count = $original_count - $spliced_count;
                                                    @endphp

                                                    @foreach ($result['serial_breakdown']->slice(0, 5) as $serial_breakdown)
                                                        <tr>
                                                            <td class="text-center text-sm p-1">
                                                                <span class="">
                                                                    {{ $serial_breakdown->Currency }}
                                                                </span>
                                                                <input type="hidden" class="transfer-break-d-currency" value="{{ $serial_breakdown->Currency }}">
                                                            </td>
                                                            <td class="text-right text-sm py-1 pe-3">
                                                                <span class=" font-medium">
                                                                    {{-- <strong> --}}
                                                                        {{ number_format($serial_breakdown->BillAmount, 2, '.', ',') }}
                                                                    {{-- </strong> --}}
                                                                </span>
                                                                <input type="hidden" class="transfer-break-d-bill-amnt" value="{{ $serial_breakdown->BillAmount }}">
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                <span class=" font-medium">
                                                                    {{ $serial_breakdown->bill_amount_count }}
                                                                </span>
                                                                <input type="hidden" class="transfer-break-d-bill-count" value="{{ $serial_breakdown->bill_amount_count }}">
                                                            </td>
                                                            <td class="text-right text-sm py-1 pe-3">
                                                                <span class=" font-medium">
                                                                    <strong>
                                                                        {{ number_format($serial_breakdown->total_bill_amount, 2, '.' , ',') }}
                                                                    </strong>
                                                                </span>
                                                                <input type="hidden" class="transfer-break-d-total-amount" value="{{ number_format($serial_breakdown->total_bill_amount, 2, '.' , ',') }}">
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                    @if ($original_count > $spliced_count)
                                                        <tr>
                                                            <td class="text-center text-sm py-1" colspan="4">
                                                                <a class="cursor-pointer" data-bs-toggle="modal" data-bs-target="#transfer-breakdown-modal">
                                                                    <span class="text-decoration-none text-gray-400 hover:text-black">
                                                                        <strong>{{ $original_count - $spliced_count }}</strong>&nbsp;more items...
                                                                    </span>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td class="text-center text-td-buying p-1" colspan="3"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-7">
                                <div class="col-12">
                                    <form action="{{ route('admin_transactions.receive_transfer_forex.unreceive_bills') }}" method="post" id="unreceive-serials-form">
                                        @csrf
                                        <div class="card">
                                            <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                                <div class="row align-items-center">
                                                    <div class="col-12">
                                                        <span class="text-lg font-bold text-black">
                                                            <i class='bx bx-list-ul' ></i>&nbsp;{{ trans('labels.transfer_deets_bills_transferred') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <table class="table table-hover table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center text-xs font-extrabold text-black p-0">
                                                            <input class="form-check-input m-0 mb-1" type="checkbox" id="unreceive-transfer-serial-select-all" name="unreceive-transfer-serial-select-all">
                                                        </th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">Transaction Date</th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">Invoice No.</th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_admin_r_deets_currency') }}</th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_admin_r_deets_serials') }}</th>
                                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_admin_r_deets_bill_amnt') }}</th>
                                                        {{-- <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_admin_r_deets_tag') }}</th> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (count($result['transfer_forex_deet']) > 0)
                                                        @foreach ($result['transfer_forex_deet'] as $transfer_forex_deet)
                                                            <tr>
                                                                <td class="text-center text-sm p-1">
                                                                    <input class="form-check-input unreceive-transfer-serial-select-one" type="checkbox" data-fsid="{{ $transfer_forex_deet->FSID }}">
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    {{ $transfer_forex_deet->TransactionDate }}
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    {{ $transfer_forex_deet->ORNo }}
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    {{ $transfer_forex_deet->Currency }}
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    {{ $transfer_forex_deet->Serials }}
                                                                </td>
                                                                <td class="text-right text-sm p-1 px-3">
                                                                    <span>
                                                                        {{-- <strong> --}}
                                                                            {{ number_format($transfer_forex_deet->BillAmount, 2, '.', ',') }}
                                                                        {{-- </strong> --}}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        <tr>
                                                            <td class="text-end text-sm py-1 px-3" colspan="6">
                                                                {{ $result['transfer_forex_deet']->links() }}
                                                            </td>
                                                        </tr>
                                                    @else
                                                        <tr>
                                                            <td class="text-center text-sm py-3" colspan="13">
                                                                <span class="buying-no-transactions text-lg">
                                                                    <strong>NO AVAILABLE BILLS</strong>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>

                                            <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                                <div class="row align-items-center">
                                                    <div class="col-4 offset-8 text-end">
                                                        @can('edit-permission', $menu_id)
                                                            <button class="btn btn-primary btn-sm" id="unreceive-selected-serials" type="button" @if (count($result['transfer_forex_deet']) < 0) disabled @endif>{{ trans('labels.selling_admin_r_deets_unreceive_bills') }}&nbsp;<i class='bx bx-undo'></i></button>
                                                        @endcan
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
        </div>
    </div>

    @include('UI.UX.transf_breakdown_modal')
    @include('UI.UX.security_code')

    {{-- Serial stock via AJAX --}}
    <div class="modal fade" id="serial-stock-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content serial-stock">
                @include('selling_transact.serial_stock_modal')
            </div>
        </div>
    </div>

@endsection

@section('receive_transf_fx_scripts')
    @include('script.received_transfer_deets')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
