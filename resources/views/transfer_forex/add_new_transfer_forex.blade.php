@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-lg-12 mb-4">
                        <div class="row justify-content-center">

                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-3">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-transfer' ></i>&nbsp;{{ trans('labels.transfer_forex_title') }}
                                                </span>
                                            </div>

                                           <div class="col-9 text-end">
                                                @can('add-permission', $menu_id)
                                                    <a class="btn btn-secondary text-white btn-sm" id="validate-pending-serials-button">
                                                        @csrf
                                                        {{ trans('labels.validate_pending_serials') }} <i class='menu-icon tf-icons bx bx-check-double ms-1 me-0'></i>
                                                    </a>
                                                @endcan
                                                @can('add-permission', $menu_id)
                                                    &nbsp;
                                                    <a class="text-white btn-primary btn-sm d-none" id="add-transfer-button" type="button" href="{{ route('branch_transactions.transfer_forex.add') }}" >
                                                        {{ trans('labels.add_new_transfer_forex') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                    </a>
                                                @endcan
                                           </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.add_new_transfer_type') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.add_new_transfer_forex_no') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">Transfer Status</th>
                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">Tracking No.</th>
                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">Tracking Status</th>
                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.add_new_transfer_date') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div id="transaction-details">
                                                @if (count($result['transfer_forex']) > 0)
                                                    @php
                                                        $tracking_array = [];
                                                    @endphp

                                                    @foreach ($result['transfer_forex'] as $tranfer_forex)
                                                        @php
                                                            if ($tranfer_forex->ITNo != null) {
                                                                $tracking_array[] = $tranfer_forex->ITNo;
                                                            }

                                                            $report_status = $tranfer_forex->HasTicket == 0 && $tranfer_forex->Voided == 0;
                                                        @endphp
                                                        <tr class="@if($tranfer_forex->Voided == 1) !bg-red-100 @endif">
                                                            <td class="text-center text-sm p-1">
                                                                {{ $tranfer_forex->Remarks }}
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                {{ $tranfer_forex->TransferForexNo }}
                                                            </td>
                                                            <td class="text-center text-xs p-1">
                                                                @if ($tranfer_forex->Voided == 1)
                                                                    <span class="badge bg-label-danger font-bold text-sm">
                                                                        Void
                                                                    </span>
                                                                @elseif ($tranfer_forex->TrackingStatus == 0)
                                                                    <span class="badge rounded-pill warning-badge-custom font-bold">
                                                                        <strong>
                                                                            Pending
                                                                        </strong>
                                                                    </span>
                                                                @elseif ($tranfer_forex->TrackingStatus == 1)
                                                                    <span class="badge rounded-pill primary-badge-custom font-bold">
                                                                        <strong>
                                                                            for pickup
                                                                        </strong>
                                                                    </span>
                                                                @elseif ($tranfer_forex->TrackingStatus == 3)
                                                                    <span class="badge rounded-pill warning-badge-custom font-bold">
                                                                        <strong>
                                                                            Pending
                                                                        </strong>
                                                                    </span>
                                                                @elseif ($tranfer_forex->TrackingStatus == 2 || $tranfer_forex->TrackingStatus == 4)
                                                                    <span class="badge rounded-pill primary-badge-custom font-bold">
                                                                        {{ trans('labels.status_transfer_forex_in_transit') }}
                                                                    </span>
                                                                @elseif ($tranfer_forex->TrackingStatus == 5)
                                                                    <span class="badge rounded-pill success-badge-custom font-bold">
                                                                        <strong>
                                                                            Received
                                                                        </strong>
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            @if ($tranfer_forex->ITNo == null)
                                                                <td class="text-center text-xs p-1">
                                                                    <strong>
                                                                        No tracking no.
                                                                    </strong>
                                                                </td>
                                                            @else
                                                                <td class="text-center text-sm p-1">
                                                                    <span class="tracking-no-span cursor-pointer">
                                                                        {{ $tranfer_forex->ITNo }}
                                                                    </span>
                                                                </td>
                                                            @endif
                                                            <td class="text-center text-xs p-1">
                                                                @if ($tranfer_forex->Voided == 1)
                                                                    <span class="badge bg-label-danger font-bold text-sm">
                                                                        Void
                                                                    </span>
                                                                @elseif ($tranfer_forex->TrackingStatus == 0)
                                                                    {{-- <span class="badge rounded-pill warning-badge-custom font-bold"> --}}
                                                                        <strong>
                                                                            N/A
                                                                        </strong>
                                                                    {{-- </span> --}}
                                                                @elseif ($tranfer_forex->TrackingStatus == 1)
                                                                    <span class="badge rounded-pill primary-badge-custom font-bold">
                                                                        <strong>
                                                                            Tracking Created
                                                                        </strong>
                                                                    </span>
                                                                @elseif ($tranfer_forex->TrackingStatus == 2)
                                                                    <span class="badge rounded-pill success-badge-custom font-bold">
                                                                        <strong>
                                                                            Received (Receptionist)
                                                                        </strong>
                                                                    </span>
                                                                @elseif ($tranfer_forex->TrackingStatus == 3)
                                                                    <span class="badge rounded-pill success-badge-custom font-bold">
                                                                        <strong>
                                                                            Received (Bahay)
                                                                        </strong>
                                                                    </span>
                                                                @elseif ($tranfer_forex->TrackingStatus == 4)
                                                                    <span class="badge rounded-pill primary-badge-custom font-bold">
                                                                        <strong>
                                                                            To Head Office
                                                                        </strong>
                                                                    </span>
                                                                @elseif ($tranfer_forex->TrackingStatus == 5)
                                                                    <span class="badge rounded-pill success-badge-custom font-bold">
                                                                        <strong>
                                                                            Delivered
                                                                        </strong>
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                {{ $tranfer_forex->TransferDate }}
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                @if ($tranfer_forex->Voided == 0)
                                                                    @can('access-permission', $menu_id)
                                                                        <a class="btn btn-primary button-edit text-white btn-popover btn-details pe-2 @if ($tranfer_forex->ITNo == null) d-none @endif @if ($tranfer_forex->BufferTransfer === 0) d-none @endif" href="{{ route('branch_transactions.transfer_forex.detail', ['id' => $tranfer_forex->TransferForexID]) }}">
                                                                            <i class='bx bx-detail' ></i>
                                                                        </a>
                                                                    @endcan

                                                                    {{-- @if ($tranfer_forex->BufferTransfer === 1 || $tranfer_forex->BufferTransfer === 0 || ($tranfer_forex->ITNo) && ($report_status)) --}}
                                                                        <button class="btn btn-primary pe-2 btn-warning report-error-modal-btn btn-popover btn-css-report" data-transactid="{{ $tranfer_forex->TransferForexID }}" data-menuid="{{ $menu_id }}" type="button" data-bs-toggle="modal" data-bs-target="#report-error-modal">
                                                                            <i class='bx bx-error'></i>
                                                                        </button>
                                                                    {{-- @endif --}}

                                                                    @if ($tranfer_forex->ITNo)
                                                                        @can('edit-permission', $menu_id)
                                                                            <button class="btn btn-warning button-warning remove-tracking-button text-white btn-popover btn-revert-tracking pe-2" data-transferforexid="{{ $tranfer_forex->TransferForexID }}" data-bs-toggle="modal" data-bs-target="#remove-tracking-modal">
                                                                                <i class='bx bx-revision'></i>
                                                                            </button>
                                                                        @endcan
                                                                    @endif

                                                                    @if ($tranfer_forex->BufferTransfer === 1 || ($tranfer_forex->ITNo))
                                                                        @can('delete-permission', $menu_id)
                                                                            <a class="btn btn-primary button-delete button-delete-transfer btn-popover btn-delete pe-2" data-bs-toggle="modal" data-bs-target="#delete-transfer-forex" data-transferforexid="{{ $tranfer_forex->TransferForexID }}">
                                                                                <i class='bx bx-trash text-white'></i>
                                                                            </a>
                                                                        @endcan
                                                                    @endif

                                                                    @if ($tranfer_forex->BufferTransfer === 0)
                                                                        @can('add-permission', $menu_id)
                                                                            <a class="btn btn-primary button-update-denom pe-2 text-white acknowledge-buffer-transf btn-popover btn-acknowledge" type="button" data-transferforexid="{{ $tranfer_forex->TransferForexID }}" data-tfxno="{{ $tranfer_forex->TransferForexNo }}" data-branchcode="{{ $tranfer_forex->BranchCode }}" id="acknowledge-buffer-transf" >
                                                                                {{-- {{ trans('labels.acknowledge') }} --}}
                                                                                <i class="bx bx-check-double"></i>
                                                                            </a>
                                                                        @endcan
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            {{-- @if ($tranfer_forex->ITNo != null)
                                                                <td class="text-center text-xs p-1">
                                                                    <a class="btn btn-danger button-danger text-white pe-2 ps-0 @if ($tranfer_forex->BufferTransfer === 0) d-none @endif" href="{{ route('branch_transactions.transfer_forex_deets', ['id' => $tranfer_forex->TransferForexID]) }}">
                                                                         &nbsp; <i class='bx bx-trash'></i>
                                                                    </a>
                                                                </td>
                                                            @endif --}}
                                                            {{-- <td class="text-center text-sm p-2">
                                                                <div class="input-group input-group-serials">
                                                                    <input class="form-control serials-input" name="serials[]" id="" type="text" >
                                                                    <div class="input-group-text p-2">
                                                                        <label class="checkbox-label checkbox-enable">
                                                                            <input class="form-check-input mt-0 enable-serial-field" id="enable-serial-field" type="checkbox" data-fxfsid="">
                                                                            <i class="bx bx-sm bx-toggle-left text-red ms-1"></i>
                                                                            <i class="bx bx-sm bxs-toggle-right text-green ms-1"></i>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </td> --}}
                                                            {{-- <td class="text-center text-sm p-2">
                                                                @if ($tranfer_forex->BufferTransfer === 0)

                                                                @elseif ($tranfer_forex->Received === 1)

                                                                @else
                                                                    <a class="btn btn-primary button-delete button-delete-transfer" data-bs-toggle="modal" data-bs-target="#delete-transfer-forex" data-transferforexid="{{ $tranfer_forex->TransferForexID }}">
                                                                        <i class='menu-icon tf-icons bx bx-trash text-white'></i>
                                                                    </a>
                                                                @endif
                                                            </td> --}}
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center p-1 text-sm py-3" colspan="13">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO AVAILABLE TRANSFER FOREX FOR TODAY</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </div>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="py-2 px-3" colspan="10">
                                                    {{ $result['transfer_forex']->links() }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12 text-end @if (count($result['transfer_forex']) == 0) d-none @endif">
                                                @can('add-permission', $menu_id)
                                                    <a class="btn btn-info btn-sm"  type="button" href="{{ config('app.tracking_url') }}" target="_blank">Go to Tracking System&nbsp; <i class='bx bxs-truck'></i></a>
                                                @endcan
                                                {{-- count($tracking_array) == 0 ||  --}}
                                                &nbsp;
                                                @can('add-permission', $menu_id)
                                                    <button class="btn btn-primary btn-sm @if (count($result['tracking_number']) == 0) d-none @endif" type="button" data-bs-toggle="modal" data-bs-target="#add-tracking-number">Add Tracking Number&nbsp; <i class='bx bx-plus'></i></button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    @include('UI.UX.security_code')
                                    @include('UI.UX.ack_buff_security_code')
                                    @include('UI.UX.rev_buff_security_code')
                                    @include('buying_transact.report_error_modal')
                                    @include('UI.UX.remove_tracking_security_code')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Buffer transfer details via AJAX --}}
    <div class="modal fade" id="buffer-transfer-details" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content buffer-transfer-details-breakdown">

            </div>
        </div>
    </div>

    <div class="modal fade" id="add-tracking-number" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-content modal-content-image modal-md">
            <div class="modal-header py-2 pe-2 ps-3">
                <span class="modal-title font-bold text-lg"></i>Add Tracking Number</span>
            </div>
            <div class="modal-body pt-2 justify-content-center">
                <div class="row px-2 mb-2">
                    <div class="col-12 px-2">
                        <span class="font-bold">List of Transfers:</span>
                    </div>
                </div>

                <div class="col-12 transfer-forex-container border border-solid border-gray-300 rounded-md p-0">
                    <table class="table table-hover mb-0">
                        <thead class="sticky-header">
                            <tr>
                                <th class="text-center text-sm font-extrabold text-black whitespace-nowrap p-1">
                                    <input class="form-check-input" type="checkbox" id="select-all-transfers" checked>
                                </th>
                                <th class="text-center text-sm font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.add_new_transfer_date') }}</th>
                                <th class="text-center text-sm font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.add_new_transfer_forex_no') }}</th>
                                <th class="text-center text-sm font-extrabold text-black whitespace-nowrap p-1">{{ trans('labels.add_new_transfer_type') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($result['transfer_forex'] as $tranfer_forex)
                                @if (is_null($tranfer_forex->ITNo))
                                    <tr>
                                        <td class="text-center text-sm p-1">
                                            <input class="form-check-input select-transfers" type="checkbox" data-tfid="{{ $tranfer_forex->TransferForexID }}" data-types="{{ $tranfer_forex->Remarks }}" @if ($tranfer_forex->BufferTransfer === 0 || $tranfer_forex->Voided == 1) disabled @else checked @endif>
                                        </td>
                                        <td class="text-center text-sm p-1">
                                            {{ $tranfer_forex->TransferDate }}
                                        </td>
                                        <td class="text-center text-sm p-1">
                                            {{ $tranfer_forex->TransferForexNo }}
                                        </td>
                                        <td class="text-center text-sm p-1">
                                            {{ $tranfer_forex->Remarks }}
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td class="text-center p-1 text-sm py-3" colspan="4">
                                        <span class="font-bold text-lg">
                                            <strong>NO AVAILABLE TRANSFER FOREX FOR TODAY</strong>
                                        </span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="row px-2 mt-3">
                    <div class="col-12 px-2">
                        <label class="my-1" for="tracking-number">
                            <strong>Tracking Number:</strong>
                        </label>

                        <select class="form-control" name="tracking-number" id="tracking-number">
                            <option>Select a tracking number</option>

                            @forelse ($result['tracking_number'] as $key => $tracking_number)
                                @php
                                    $hidden_element = false;

                                    foreach ($result['ITIDs'] as $ITIDs) {
                                        if ($ITIDs->ITID == $tracking_number->TrackingID) {
                                            $hidden_element = true;

                                            break;
                                        }
                                    }
                                @endphp

                                <option class="{{ $hidden_element ? 'd-none' : '' }}" value="{{ $tracking_number->TrackingID }}" data-trackingno="{{ $tracking_number->TrackingNumber }}" data-trackingtype="{{ $tracking_number->ItemType }}">
                                    <strong>{{ $tracking_number->TrackingNumber }}</strong>
                                    &nbsp;-&nbsp;{{ $tracking_number->ItemDesc }}
                                </option>
                            @empty

                            @endforelse
                        </select>
                    </div>
                </div>

                <div class="col-lg-12 px-3">
                    <hr>
                </div>

                <div class="col-lg-12 px-3">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-8 text-center">
                            <label class="mb-2" for="description">
                                <strong>
                                    {{ trans('labels.enter_security_code') }} &nbsp; <span class="required-class">*</span>
                                </strong>
                            </label>

                            <input class="form-control" step="any" autocomplete="false" id="address-tracking-no-security-code" type="password" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer p-1">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                @can('add-permission', $menu_id)
                    <button type="button" class="btn btn-primary" id="address-tracking-no" disabled>Proceed</button>
                @endcan
            </div>
        </div>
    </div>

@endsection

@section('transf_forex_scripts')
    @include('script.received_buff_transfer')
    @include('script.add_transf_forex_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
