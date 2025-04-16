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

                            <div class="col-7">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-archive-in'></i>&nbsp;{{ trans('labels.selling_admin_received_transf') }}
                                                </span>
                                            </div>

                                            <div class="col-6 text-end">
                                                @can('add-permission', $menu_id)
                                                    <a class="btn btn-primary text-white btn-sm" href="{{ route('admin_transactions.receive_transfer_forex.add') }}">
                                                        {{ trans('labels.selling_admin_receive_transf') }} <i class='menu-icon tf-icons bx bx-archive-in text-white ms-1 me-0'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.selling_admin_transf_branch') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.add_new_transfer_forex_no') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Transf. Date</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Tracking No.</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.selling_admin_transf_date') }}</th> --}}
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.selling_admin_transf_type') }}</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.selling_admin_transf_remarks') }}</th> --}}
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.selling_admin_transf_date_received') }}</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.selling_admin_transf_received_remarks') }}</th> --}}
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.selling_admin_transf_received_by') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.selling_admin_transf_received_stat') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div id="transaction-details">
                                                @if (count($result['received_transfers']) > 0)
                                                    @foreach ($result['received_transfers'] as $received_transfers)
                                                        <tr class="selling-transact-details-list-table" id="selling-transact-details-list-table">
                                                            <td class="text-center text-xs p-1">
                                                                {{ $received_transfers->BranchCode }}
                                                            </td>
                                                            <td class="text-center text-xs p-1">
                                                                {{ $received_transfers->TransferForexNo }}
                                                            </td>
                                                            <td class="text-center text-xs p-1">
                                                                {{ $received_transfers->TransferDate}}
                                                            </td>
                                                            @if ($received_transfers->TrackingNo != null)
                                                                <td class="text-center text-xs p-1">
                                                                    {{ $received_transfers->TrackingNo }}
                                                                </td>
                                                            @else
                                                                <td class="text-center text-xs cursor-not-allowed p-1">
                                                                    <span class="cursor-not-allowed">
                                                                        <strong>
                                                                            No tracking no.
                                                                        </strong>
                                                                    </span>
                                                                </td>
                                                            @endif
                                                            <td class="text-center text-xs p-1">
                                                                {{ $received_transfers->TransfeRemarks }}
                                                            </td>
                                                            {{-- <td class="text-center text-xs p-1">
                                                                {{ $received_transfers->TransfeRemarks }}
                                                            </td> --}}
                                                            <td class="text-center text-xs p-1">
                                                                {{ $received_transfers->ReceivedDate }}
                                                            </td>
                                                            {{-- <td class="text-center text-xs p-1">
                                                                @if ($received_transfers->ReceivedRemarks == '')
                                                                    N/A
                                                                @else
                                                                    {{ Str::title($received_transfers->ReceivedRemarks) }}
                                                                @endif
                                                            </td> --}}
                                                            <td class="text-center text-xs p-1">
                                                                {{ $received_transfers->Name }}
                                                            </td>
                                                            <td class="text-center text-xs p-1">
                                                                @if ($received_transfers->ReceivedStatus == 1)
                                                                    <span class="badge rounded-pill success-badge-custom pt-2 font-bold">
                                                                        <strong>
                                                                            {{ trans('labels.status_received') }}
                                                                        </strong>
                                                                    </span>
                                                                @else
                                                                    <span class="badge rounded-pill bg-warning warning-badge-custom font-bold">
                                                                        <strong>
                                                                            {{ trans('labels.status_pending') }}
                                                                        </strong>
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center whitespace-nowrap p-1">
                                                                @can('access-permission', $menu_id)
                                                                    <a class="btn btn-primary button-edit pe-2 ps-2 text-xs text-white transfer-details" href="{{ route('admin_transactions.receive_transfer_forex.details', ['id' => $received_transfers->TransferForexID]) }}">
                                                                        <i class='bx bx-detail'></i>
                                                                    </a>
                                                                @endcan

                                                                @can('delete-permission', $menu_id)
                                                                    <a class="btn btn-warning button-warning ps-2 pe-2 text-xs text-white unreceive-transfer" data-bs-toggle="modal" data-bs-target="#security-code-modal" data-rtid="{{ $received_transfers->RTID }}" data-tfxid="{{ $received_transfers->TransferForexID }}">
                                                                        <i class='bx bx-revision'></i>
                                                                    </a>
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-xs py-3" colspan="9">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO AVAILABLE RECEIVED TRANSFERS FOR TODAY</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </div>
                                        </tbody>
                                    </table>

                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                {{ $result['received_transfers']->links() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-5 ps-0">
                                <div class="card">
                                    <div class="col-12 py-2 ps-1 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                <span class="text-lg font-bold py-2 ps-3 text-black">
                                                    <i class='bx bxs-truck'></i>&nbsp;{{ trans('labels.transfer_deets_title_tracking') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr class="align-items-center">
                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.transfer_forex_branch') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">Transf. Date</th>
                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.add_new_transfer_forex_no') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">Tracking No.</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.add_new_transfer_date') }}</th> --}}
                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.transfer_deets_title_track_status') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div id="transaction-details">
                                                @if (count($result['transfers']) > 0)
                                                    @foreach ($result['transfers'] as $tranfer_forex)
                                                        <tr class="transact-details-list-table" id="transact-details-list-table">
                                                            <td class="text-center text-xs p-1">
                                                                {{ $tranfer_forex->BranchCode }}
                                                            </td>
                                                            <td class="text-center text-xs p-1">
                                                                {{ $tranfer_forex->TFDate }}
                                                            </td>
                                                            <td class="text-center text-xs p-1">
                                                                {{ $tranfer_forex->TFNO }}
                                                            </td>
                                                            @if ($tranfer_forex->TrackingNo != null)
                                                                <td class="text-center text-xs p-1">
                                                                    {{ $tranfer_forex->TrackingNo }}
                                                                </td>
                                                            @else
                                                                <td class="text-center text-xs cursor-not-allowed p-1">
                                                                    <span class="cursor-not-allowed">
                                                                        <strong>
                                                                            No tracking no.
                                                                        </strong>
                                                                    </span>
                                                                </td>
                                                            @endif
                                                            {{-- <td class="text-center text-xs p-1">
                                                                {{ $tranfer_forex->TFDate }}
                                                            </td> --}}
                                                            <td class="text-center text-xs p-1">
                                                                @php
                                                                    $date = \Carbon\Carbon::now('Asia/Manila');
                                                                    $date_now = $date->toDateString();
                                                                    $date_now_parse = \Carbon\Carbon::parse($date_now);
                                                                @endphp

                                                                @if (($tranfer_forex->RTReceived === null) && ($tranfer_forex->TFDate == $date_now))
                                                                    <span class="badge rounded-pill primary-badge-custom pt-2">
                                                                        <strong>
                                                                            {{ trans('labels.status_transfer_forex_in_transit') }} &nbsp; <i class='bx bxs-truck badge-icons bx-fade-right'></i>
                                                                        </strong>
                                                                    </span>
                                                                @elseif (($tranfer_forex->RTReceived === null) && ($tranfer_forex->TFDate != $date_now))
                                                                    <span class="badge rounded-pill warning-badge-custom pt-2">
                                                                        {{-- <strong> --}}
                                                                            {{-- {{ trans('labels.status_pending') }} &nbsp; <i class='bx bx-time-five badge-icons bx-tada'></i> --}}
                                                                            {{-- {{ trans('labels.status_transfer_forex_in_transit') }} &nbsp; <i class='bx bxs-truck badge-icons'></i> --}}
                                                                            {{ trans('labels.status_transfer_forex_in_transit') }} &nbsp; <i class='bx bxs-truck badge-icons'></i>

                                                                            @if ($date_now_parse->diffInDays($tranfer_forex->TFDate) == 1)
                                                                                <span class="badge rounded-pill danger-badge-custom">
                                                                                    <span class="text-number-of-days-pending">
                                                                                        {{ $date_now_parse->diffInDays($tranfer_forex->TFDate) }}&nbsp;{{ trans('labels.selling_admin_received_pending_day') }}
                                                                                    </span>
                                                                                </span>
                                                                            @elseif ($date_now_parse->diffInDays($tranfer_forex->TFDate) > 1)
                                                                                <span class="badge rounded-pill danger-badge-custom">
                                                                                    <span class="text-number-of-days-pending">
                                                                                        {{ $date_now_parse->diffInDays($tranfer_forex->TFDate) }}&nbsp;{{ trans('labels.selling_admin_received_pending_days') }}
                                                                                    </span>
                                                                                </span>
                                                                            @endif
                                                                        {{-- </strong> --}}
                                                                    </span>
                                                                @else
                                                                    <span class="badge rounded-pill primary-badge-custom pt-2">
                                                                        <strong>
                                                                            {{ trans('labels.status_received') }} &nbsp; <i class='bx bx-archive-in badge-icons'></i>
                                                                        </strong>
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center text-xs p-1">
                                                                @can('access-permission', $menu_id)
                                                                    <a class="btn btn-primary button-edit pe-2 ps-2 text-xs text-white incoming-transfer-details" data-bs-toggle="modal" data-bs-target="#incoming-transfer-forex" data-tfxid="{{ $tranfer_forex->TFID }}">
                                                                        <i class='bx bx-detail'></i>
                                                                    </a>
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-xs py-3" colspan="13">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO TRANSFER FOREX IN TRANSIT</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </div>
                                        </tbody>
                                    </table>

                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                {{ $result['transfers']->links() }}
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

    <div class="modal fade" id="incoming-transfer-forex" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content add-denom">
                @include('selling_receive_transfers.incoming_transf_fx')
            </div>
        </div>
    </div>

@endsection

@section('received_transf_fx_scripts')
    @include('script.received_transf_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
