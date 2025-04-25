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
                                <div class="col-12">
                                    @if(session()->has('message'))
                                        <div class="alert alert-success alert-dismissible" role="alert">
                                            {!! session()->get('message') !!}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-5">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-list-ul' ></i>&nbsp;{{ trans('labels.receive_buffer') }}
                                                </span>
                                            </div>

                                            <div class="col-6 text-end">
                                                <button class="btn btn-primary btn-sm" type="button" id="print-buffer-transfer">{{ trans('labels.receive_buffer_print_buffer_details') }}  &nbsp; <i class='bx bx-receipt'></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    @if (count($result['buffer_transfer']) > 0)
                                        @foreach ($result['buffer_transfer'] as $buffer_transfer)
                                            <div class="col-12 px-1 border border-gray-300 rounded-tr rounded-tl" id="buying-container">
                                                <div class="row align-items-center px-3 mt-2">
                                                    <div class="col-4">
                                                        <strong>
                                                            Buffer Number:
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" id="buffer-transfer-transfer-number" value="{{ $buffer_transfer->BufferNo }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 mt-2">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.transfer_deets_transf_date') }}:
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" id="buffer-transfer-transfer-date" value="{{ $buffer_transfer->BufferDate }}" readonly>
                                                    </div>
                                                </div>

                                                <div class="row align-items-center px-3 my-2">
                                                    <div class="col-4">
                                                        <strong>
                                                            {{ trans('labels.receive_buffer_branch') }}:
                                                        </strong>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" class="form-control" id="buffer-transfer-branch" value="{{ $buffer_transfer->BranchCode }}" readonly>
                                                        <input type="hidden" class="form-control" id="transfer-forex-id" value="{{ $buffer_transfer->TFID }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                    <div class="card-footer p-1 border border-gray-300">
                                        <div class="row aling-items-center">
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="col-12  p-2 ps-3 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row">
                                            <div class="col-12">
                                                <span class="text-lg font-bold p-1 text-black">
                                                    <i class='bx bx-list-ul'></i>&nbsp;{{ trans('labels.transfer_deets_transfer_summary') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover" id="bufffer-transfer-summary-table">
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
                                                        <td class="text-center text-sm py-1">
                                                            <span id="trans-count">
                                                                {{ $serial_count_per_currency->Currency }}
                                                            </span>
                                                            <input type="hidden" class="transfer-summ-currency" value="{{ $serial_count_per_currency->Currency }}">
                                                        </td>

                                                        <td class="text-right text-sm py-1">
                                                            <span id="bill-amount">
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
                                                <td class="text-center text-sm py-1" colspan="3"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="col-12 mt-3">
                                    <div class="card">
                                        <div class="col-12  p-2 ps-3 border border-gray-300 rounded-tl rounded-tr">
                                            <div class="row">
                                                <div class="col-12">
                                                    <span class="text-lg font-bold p-1 text-black">
                                                        <i class='bx bx-list-ul'></i>&nbsp;{{ trans('labels.transfer_deets_transfer_breakdown') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <table class="table table-bordered table-hover" id="buffer-transfer-breakd-down-table">
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
                                                    @foreach ($result['serial_breakdown'] as $serial_breakdown)
                                                        <tr>
                                                            <td class="text-center text-sm py-1">
                                                                <span id="trans-count">
                                                                    {{ $serial_breakdown->Currency }}
                                                                </span>
                                                                <input type="hidden" class="transfer-break-d-currency" value="{{ $serial_breakdown->Currency }}">
                                                            </td>
                                                            <td class="text-right text-sm py-1">
                                                                <span id="trans-count">
                                                                    <strong>
                                                                        {{ $serial_breakdown->BillAmount }}
                                                                    </strong>
                                                                </span>
                                                                <input type="hidden" class="transfer-break-d-bill-amnt" value="{{ $serial_breakdown->BillAmount }}">
                                                            </td>
                                                            <td class="text-center text-sm py-1">
                                                                <span id="trans-count">
                                                                    {{ $serial_breakdown->bill_amount_count }}
                                                                </span>
                                                                <input type="hidden" class="transfer-break-d-bill-count" value="{{ $serial_breakdown->bill_amount_count }}">
                                                            </td>
                                                            <td class="text-right text-sm py-1">
                                                                <span id="trans-count">
                                                                    <strong>
                                                                        {{ number_format($serial_breakdown->total_bill_amount, 2, '.' , ',') }}
                                                                    </strong>
                                                                </span>
                                                                <input type="hidden" class="transfer-break-d-total-amount" value="{{ number_format($serial_breakdown->total_bill_amount, 2, '.' , ',') }}">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td class="text-center text-td-buying py-1" colspan="4"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-7">
                                <form action="{{ route('admin_transactions.buffer.revert') }}" method="post" id="unreceive-buffer-serials-form">
                                    @csrf
                                    <div class="card">
                                        <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                            <div class="row align-items-center">
                                                <span class="text-lg font-bold py-0 ps-4 text-black">
                                                    <i class='bx bx-select-multiple' ></i>&nbsp;Unreceive Bills
                                                </span>
                                            </div>
                                        </div>

                                        <table class="table table-hover table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">
                                                        <input class="form-check-input" type="checkbox" id="unreceive-transfer-serial-select-all-b" name="unreceive-transfer-serial-select-all-b">
                                                    </th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_admin_r_deets_currency') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_admin_r_deets_serials') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_admin_r_deets_bill_amnt') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($result['buffer_transfer_forex_deet']) > 0)
                                                    @foreach ($result['buffer_transfer_forex_deet'] as $buffer_transfer_forex_deet)
                                                        <tr>
                                                            <td class="text-center text-sm p-1">
                                                                <input class="form-check-input unreceive-transfer-serial-select-one-b" type="checkbox" data-fsid="{{ $buffer_transfer_forex_deet->FSID }}" data-billamount=" {{ $buffer_transfer_forex_deet->BillAmount }}">
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                {{ $buffer_transfer_forex_deet->Currency }}
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                {{ $buffer_transfer_forex_deet->Serials }}
                                                            </td>
                                                            <td class="text-right text-sm py-1 px-3">
                                                                <strong>
                                                                    {{ number_format($buffer_transfer_forex_deet->BillAmount, 2, '.', ',') }}
                                                                </strong>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td class="py-1 px-2" colspan="4">
                                                        {{ $result['buffer_transfer_forex_deet']->links() }}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>

                                        <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                            <div class="row align-items-center">
                                                <div class="col-4 offset-8 text-end">
                                                    <a class="btn btn-secondary btn-sm" href="{{ route('admin_transactions.buffer.buffer_transfers') }}">{{ trans('labels.back_action') }}</a>
                                                    <button class="btn btn-primary btn-sm" id="unreceive-selected-buffer-serials" type="button">Unreceive&nbsp;<i class='bx bx-undo'></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            @include('UI.UX.security_code')
                            @include('UI.UX.buff_transf_deets_security_code')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('buffer_transfer_scripts')
    @include('script.unreceive_buff_t_scripts')
    @include('script.qz_tray_buff_transf_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
