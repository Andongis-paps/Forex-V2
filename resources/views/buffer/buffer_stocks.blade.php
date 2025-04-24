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
                            <div class="col-7 pe-0">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-archive-in' ></i>&nbsp;{{ trans('labels.buffer_receive') }}
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                @can('add-permission', $menu_id)
                                                    <a class="btn btn-primary text-white btn-sm" href="{{ route('admin_transactions.buffer.receive_buffer') }}">
                                                        {{ trans('labels.selling_admin_receive_transf') }} <i class='menu-icon tf-icons bx bx-archive-in text-white ms-1 me-0'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover" id="transfers-result-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.receive_buffer_branch') }}</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.receive_buffer_buffer_no') }}</th> --}}
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Currency</th> --}}
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Transf. Fx. No.</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Transf. Date</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Tracking No.</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Type</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Date Received</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Received By</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.receive_buffer_dollar_amnt') }}</th> --}}
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.receive_buffer_status') }}</th>
                                                @can('access-permission', $menu_id)
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Action</th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody id="transfers-result-table-tbody">
                                            @php
                                                $total_received_amnt = 0;
                                            @endphp

                                            @forelse ($result['received_buffers'] as $received_buffers)
                                                @php
                                                    $total_received_amnt += $received_buffers->DollarAmount;
                                                @endphp

                                                <tr>
                                                    <td class="text-center text-xs p-1 whitespace-nowrap">
                                                        {{ $received_buffers->BranchCode }}
                                                    </td>
                                                    <td class="text-center text-xs p-1 whitespace-nowrap">
                                                        {{ $received_buffers->TransferForexNo }}
                                                    </td>
                                                    <td class="text-center text-xs p-1 whitespace-nowrap">
                                                        {{ $received_buffers->BufferDate }}
                                                    </td>
                                                    {{-- <td class="text-center text-xs p-1 whitespace-nowrap">
                                                        {{ $received_buffers->BufferNo }}
                                                    </td> --}}
                                                    {{-- <td class="text-center text-xs p-1 whitespace-nowrap">
                                                        <strong>
                                                            {{ $received_buffers->CurrAbbv }}
                                                        </strong>
                                                    </td> --}}
                                                    <td class="text-center text-xs p-1 whitespace-nowrap">
                                                        @if ($received_buffers->ITNo == null || $received_buffers->ITNo == '')
                                                            <strong>
                                                                {{-- <span>No Tracking No.</span> --}}
                                                                <span>-</span>
                                                            </strong>
                                                        @else
                                                            {{ $received_buffers->ITNo }}
                                                        @endif
                                                    </td>
                                                    <td class="text-center text-xs p-1 whitespace-nowrap">
                                                        @if ($received_buffers->BufferType == 1)
                                                            <span class="text-black">
                                                                For Selling
                                                            </span>
                                                        @else
                                                            <span class="text-black">
                                                                Addt'l Buffer
                                                            </span>
                                                        @endif
                                                    </td>
                                                    {{-- <td class="text-right text-xs p-1 px-3 whitespace-nowrap">
                                                        <span>
                                                            {{ number_format($received_buffers->DollarAmount, 2, '.', ',') }}
                                                        </span>
                                                    </td> --}}
                                                    <td class="text-center text-xs p-1 whitespace-nowrap">
                                                        {{ $received_buffers->RDate }}
                                                    </td>
                                                    <td class="text-center text-xs p-1 whitespace-nowrap">
                                                        {{ $received_buffers->Name }}
                                                    </td>
                                                    <td class="text-center text-xs p-1 whitespace-nowrap">
                                                        @if ($received_buffers->BufferTransfer == 0)
                                                            <span class="badge rounded-pill bg-warning warning-badge-custom font-bold">
                                                                <strong>
                                                                    {{ trans('labels.status_pending') }}
                                                                </strong>
                                                            </span>
                                                        @elseif ($received_buffers->BufferTransfer == 1)
                                                            <span class="badge rounded-pill primary-badge-custom font-bold">
                                                                <strong>
                                                                    {{ trans('labels.status_acknowledged') }}
                                                                </strong>
                                                            </span>
                                                        @elseif ($received_buffers->BufferTransfer == 2)
                                                            <span class="badge rounded-pill success-badge-custom font-bold">
                                                                <strong>
                                                                    Received
                                                                </strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    @can('access-permission', $menu_id)
                                                        <td class="text-center text-xs p-1 whitespace-nowrap">
                                                            @if ($received_buffers->BufferTransfer == 0)
                                                                {{-- <span class="badge rounded-pill bg-warning warning-badge-custom font-bold">
                                                                    <strong>
                                                                        {{ trans('labels.status_pending') }}
                                                                    </strong>
                                                                </span> --}}
                                                            @elseif ($received_buffers->BufferTransfer == 2)
                                                                <a class="btn btn-primary button-edit pe-2 text-white receive-buffer-transfer-details" type="button" href="{{ route('admin_transactions.buffer.details', ['id' => $received_buffers->TransferForexID]) }}">
                                                                    <i class='bx bx-detail'></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    @endcan
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-xs py-3" id="empty-receive-transf-table" colspan="12">
                                                        <span class="buying-no-transactions text-lg">
                                                            <strong>NO AVAILABLE BUFFER TRANSFERS</strong>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        {{-- <tfoot>
                                            <tr>
                                                <td class="text-right text-xs p-1 whitespace-nowrap" colspan="6">
                                                    <span class="font-bold">
                                                        Total Amount:
                                                    </span>
                                                </td>
                                                <td class="text-right text-xs py-1 px-3 whitespace-nowrap">
                                                    <strong>
                                                        <span>
                                                            &#36;&nbsp;{{ number_format($total_received_amnt, 2, '.', ',') }}
                                                        </span>
                                                    </strong>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tfoot> --}}
                                    </table>

                                    <div class="col-12 px-3 border border-gray-300 rounded-bl rounded-br py-2">
                                        {{ $result['received_buffers']->links() }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-5">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <span class="text-lg font-bold ps-2 text-black">
                                                    <i class='bx bxs-truck' ></i>&nbsp;Incoming Buffers
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover" id="transfers-result-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.receive_buffer_branch') }}</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.receive_buffer_tf_no') }}</th> --}}
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Transf. Date</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Tracking No.</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.receive_buffer_dollar_amnt') }}</th> --}}
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Type</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.receive_buffer_status') }}</th>
                                                @can('add-permission', $menu_id)
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Action</th>
                                                @endcan
                                            </tr>
                                        </thead>

                                        <tbody id="transfers-result-table-tbody">
                                            @php
                                                $total_incoming_amnt = 0;
                                            @endphp

                                            @forelse ($result['incoming_buffers'] as $incoming_buffers)
                                                @php
                                                    $total_incoming_amnt += $incoming_buffers->DollarAmount;
                                                @endphp

                                                <tr>
                                                    <td class="text-center text-xs px-1 py-2 whitespace-nowrap">
                                                        {{ $incoming_buffers->BranchCode }}
                                                    </td>
                                                    <td class="text-center text-xs px-1 py-2 whitespace-nowrap">
                                                        {{ $incoming_buffers->BufferDate }}
                                                    </td>
                                                    <td class="text-center text-xs px-1 py-2 whitespace-nowrap">
                                                        @if ($incoming_buffers->ITNo == null || $incoming_buffers->ITNo == '')
                                                            <strong>
                                                                <span>-</span>
                                                            </strong>
                                                        @else
                                                            {{ $incoming_buffers->ITNo }}
                                                        @endif
                                                    </td>
                                                    {{-- <td class="text-center text-xs px-1 py-2 whitespace-nowrap">
                                                        {{ $incoming_buffers->TransferForexNo }}
                                                    </td> --}}
                                                    {{-- <td class="text-right text-xs px-1 py-2 whitespace-nowrap">
                                                        <strong>
                                                            <span>
                                                                {{ number_format($incoming_buffers->DollarAmount, 2, '.', ',') }}
                                                            </span>
                                                        </strong>
                                                    </td> --}}
                                                    <td class="text-center text-xs px-1 py-2 whitespace-nowrap">
                                                        @if ($incoming_buffers->BufferType == 1)
                                                            <span class="text-black">
                                                                For Selling
                                                            </span>
                                                        @else
                                                            <span class="text-black">
                                                                Addt'l Buffer
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center text-xs px-1 py-2 whitespace-nowrap">
                                                        @if ($incoming_buffers->BufferTransfer == 0)
                                                            <span class="badge rounded-pill bg-warning warning-badge-custom font-bold text-xs">
                                                                <strong>
                                                                    {{ trans('labels.status_pending') }}
                                                                </strong>
                                                            </span>
                                                        @elseif ($incoming_buffers->BufferTransfer == 1)
                                                            <span class="badge rounded-pill primary-badge-custom font-bold text-xs">
                                                                <strong>
                                                                    {{ trans('labels.status_acknowledged') }}
                                                                </strong>
                                                            </span>
                                                        @elseif ($incoming_buffers->BufferTransfer == 2)
                                                            <span class="badge rounded-pill success-badge-custom font-bold text-xs">
                                                                <strong>
                                                                    {{ trans('labels.status_transferred') }}
                                                                </strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    @can('add-permission', $menu_id)
                                                        <td class="text-center text-xs py-1 whitespace-nowrap">
                                                            @if ($incoming_buffers->BufferTransfer == 0)
                                                                <button class="btn btn-primary button-edit pe-2 text-white incoming-buffer-details" type="button" data-bufftfid="{{ $incoming_buffers->TransferForexID }}" data-branchid="{{ $incoming_buffers->BranchID }}" data-bufftransfno="{{ $incoming_buffers->TransferForexNo }}">
                                                                    <i class='bx bx-detail'></i>
                                                                </button>
                                                            @elseif ($incoming_buffers->BufferTransfer == 1)
                                                                {{-- <a class="btn btn-primary button-update-denom pe-2 text-white receive-buffer-transfer" type="button" data-bufferid="{{ $incoming_buffers->BufferID }}">
                                                                    <i class='bx bx-package'></i>
                                                                </a> --}}
                                                            @elseif ($incoming_buffers->BufferTransfer == 2)
                                                                {{-- <a class="btn btn-primary button-edit pe-2 text-white receive-buffer-transfer-details" type="button" href="{{ route('received_buffer_details', ['id' => $incoming_buffers->TransferForexID]) }}">
                                                                    <i class='bx bx-detail'></i>
                                                                </a> --}}
                                                            @endif
                                                        </td>
                                                    @endcan
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-xs py-3" colspan="12" id="empty-receive-transf-table">
                                                        <span class="buying-no-transactions text-lg">
                                                            <strong>NO AVAILABLE BUFFER TRANSFERS</strong>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        {{-- <tfoot>
                                            <tr>
                                                <td class="text-right text-xs py-1 whitespace-nowrap" colspan="2">
                                                    <span class="font-bold">
                                                        Total Amount:
                                                    </span>
                                                </td>
                                                <td class="text-right text-xs py-1 px-3 whitespace-nowrap">
                                                    <strong>
                                                        <span>
                                                            &#36;&nbsp;{{ number_format($total_incoming_amnt, 2, '.', ',') }}
                                                        </span>
                                                    </strong>
                                                </td>
                                                <td colspan="3"></td>
                                            </tr>
                                        </tfoot> --}}
                                    </table>

                                    <div class="col-12 px-3 border border-gray-300 rounded-bl rounded-br p-2">
                                        {{ $result['incoming_buffers']->links() }}
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

    {{-- Buffer transfer details via AJAX --}}
    <div class="modal fade" id="incoming-buff-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content incoming-buff-details">

            </div>
        </div>
    </div>

@endsection

@section('buffer_transfer_scripts')
    @include('script.received_buff_transfer')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
