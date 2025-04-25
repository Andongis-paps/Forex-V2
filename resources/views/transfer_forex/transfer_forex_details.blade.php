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
                                <input type="hidden" id="full-url-tranfer-forex-details" value="{{ URL::to('/').'/'.'transferForexDeets' }}">

                                <span class="counter-autoprint-transfer-details d-none" id="counter-transfer">0</span>
                            </div>

                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-5">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                            <div class="row align-items-center">
                                                <div class="col-6">
                                                    <span class="text-lg font-bold p-2 text-black">
                                                        <i class='bx bx-list-ul' ></i>&nbsp;{{ trans('labels.transfer_deets_title') }}
                                                    </span>
                                                </div>

                                                <div class="col-6 text-end">
                                                    @can('edit-permission', $menu_id)
                                                        <button class="btn btn-primary btn-sm" type="button" id="print-transfer-forex">{{ trans('labels.transfer_deets_print_transfer') }}  &nbsp; <i class='bx bx-receipt'></i></button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                        @if (count($result['transfer_forex']) > 0)
                                            @foreach ($result['transfer_forex'] as $transfer_deets)
                                                <div class="col-12 border px-3 border-gray-300" id="buying-container">
                                                    <div class="row align-items-center mt-2">
                                                        <div class="col-4">
                                                            <strong>
                                                                {{ trans('labels.transfer_deets_transf_date') }}:
                                                            </strong>
                                                        </div>
                                                        <div class="col-8">
                                                            <input type="text" class="form-control" id="transfer-deets-transfer-date" name="transfer-deets-transfer-date" value="{{ $transfer_deets->TransferDate }}" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center mt-2">
                                                        <div class="col-4">
                                                            <strong>
                                                                {{ trans('labels.transfer_deets_tracking_no') }}:
                                                            </strong>
                                                        </div>
                                                        <div class="col-8">
                                                            <input type="text" class="form-control" id="transfer-forex-tracking-no" value="{{ $transfer_deets->ITNo }}" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center mt-2">
                                                        <div class="col-4">
                                                            <strong>
                                                                {{ trans('labels.transfer_deets_transf_no') }}:
                                                            </strong>
                                                        </div>
                                                        <div class="col-8">
                                                            <input type="text" class="form-control" id="transfer-deets-transfer-number" name="transfer-deets-transfer-number" value="{{ $transfer_deets->TransferForexNo }}" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="row align-items-center mt-2 mb-2">
                                                        <div class="col-4">
                                                            <strong>
                                                                {{ trans('labels.transfer_deets_transf_type') }}:
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

                                        <div class="card-footer p-2 border border-gray-300">
                                            <div class="row aling-items-center">
                                                <div class="col-6">
                                                </div>

                                                <div class="col-6 text-end">
                                                    @can('add-permission', $menu_id)
                                                        <a class="btn btn-secondary btn-sm" type="button" href="{{ route('branch_transactions.transfer_forex') }}">Back</a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <div class="card">
                                        <div class="col-12  p-2 ps-3 border border-gray-300 rounded-tl rounded-tr">
                                            <div class="row">
                                                <div class="col-12">
                                                    <span class="text-lg font-bold p-1 text-black">
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

                                                            <td class="text-right text-sm py-1 pe-3">
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
                                                    <td class="text-center text-td-buying py-1" colspan="3"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                     </div>
                                </div>

                                <div class="col-12 mt-3">
                                    <div class="card">
                                        <div class="col-12  p-2 ps-3 border border-gray-300 rounded-tl rounded-tr">
                                            <div class="row">
                                                <div class="col-12">
                                                    <span class="text-lg font-bold p-1 text-black">
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
                                                            <td class="text-center text-sm py-1">
                                                                <span class="">
                                                                    {{ $serial_breakdown->Currency }}
                                                                </span>
                                                                <input type="hidden" class="transfer-break-d-currency" value="{{ $serial_breakdown->Currency }}">
                                                            </td>
                                                            <td class="text-right text-sm py-1">
                                                                <span class=" font-medium">
                                                                    <strong>
                                                                        {{ $serial_breakdown->BillAmount }}
                                                                    </strong>
                                                                </span>
                                                                <input type="hidden" class="transfer-break-d-bill-amnt" value="{{ $serial_breakdown->BillAmount }}">
                                                            </td>
                                                            <td class="text-center text-sm py-1">
                                                                <span class=" font-medium">
                                                                    {{ $serial_breakdown->bill_amount_count }}
                                                                </span>
                                                                <input type="hidden" class="transfer-break-d-bill-count" value="{{ $serial_breakdown->bill_amount_count }}">
                                                            </td>
                                                            <td class="text-right text-sm py-1">
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
                                                    <td class="text-center text-td-buying py-2" colspan="4">
                                                        {{-- {{ $result['serial_breakdown']->links() }} --}}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-7">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="col-12  p-2 ps-3 border border-gray-300 rounded-tl rounded-tr">
                                            <div class="row">
                                                <div class="col-6">
                                                    <span class="text-lg font-bold p-1 text-black">
                                                        <i class='bx bx-list-ul' ></i>&nbsp;{{ trans('labels.transfer_deets_bills_transferred') }}
                                                    </span>
                                                </div>
                                                <div class="col-6 text-end">
                                                    @can('add-permission', $menu_id)
                                                        <a class="btn btn-primary text-white btn-sm" href="{{ route('branch_transactions.transfer_forex') }}">
                                                            {{ trans('labels.add_new_transfer_forex') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                        </a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>

                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_forex_currency') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_forex_bill_amnt') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_forex_serials') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_forex_date') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">Transaction No.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <div id="">
                                                    @if (count($result['serials_transferred']) > 0)
                                                        @foreach ($result['serials_transferred'] as $serials_transferred)
                                                            <tr class="transact-details-list-table" id="transact-details-list-table">
                                                                <td class="text-center text-sm py-1">
                                                                    {{ $serials_transferred->Currency }}
                                                                </td>
                                                                <td class="text-right text-sm py-1">
                                                                    <strong>
                                                                        {{ $serials_transferred->BillAmount }}
                                                                    </strong>
                                                                </td>
                                                                <td class="text-center text-sm py-1">
                                                                    {{ $serials_transferred->Serials }}
                                                                </td>
                                                                <td class="text-center text-sm py-1">
                                                                    {{ $serials_transferred->TransactionDate }}
                                                                </td>
                                                                <td class="text-center text-sm py-1">
                                                                    {{ $serials_transferred->TransactionNo }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td class="text-center text-sm py-1" colspan="1">
                                                        <span class="transaction-count" id="transaction-count">
                                                            {{ trans('labels.transfer_deets_bills_count') }}: <span class=" font-semibold" id="trans-count">{{ count($result['serials_transferred']) }}</span>
                                                        </span>
                                                    </td>
                                                    <td class="text-center text-sm py-2" colspan="6"></td>
                                                </tr>
                                            </tfoot>
                                        </table>

                                        <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                            <div class="row align-items-center">
                                                <div class="col-12">
                                                    {{ $result['serials_transferred']->links() }}
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
    </div>

    @include('UI.UX.security_code')
    @include('UI.UX.transf_breakdown_modal')

@endsection

@section('qz_tray_scripts')
    @include('script.qz_tray_transfer_f_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>

