@extends('template.layout')
@section('content')

   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-lg-12 mb-4">
                        <div class="row">
                            <div class="col-12">
                                <hr>
                            </div>

                            @can('add-permission', $menu_id)
                                <div class="col-12">
                                    <div class="card">
                                        <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                            <div class="col-12 border-3 border-green-600 rounded relative p-2">
                                                <input class="form-control serial-search-field" type="text" id="serial-search-field" placeholder="Search serial">
                                                @include('bill_tagging_admin.serial_search_result')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endcan

                            <div class="col-12 mt-2">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6 text-left">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-purchase-tag' ></i>&nbsp;{{ trans('labels.bill_tagging_create') }}
                                                </span>
                                            </div>
                                            {{-- <div class="col-6 text-end">
                                                @can('add-permission', $menu_id)
                                                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#print-atds-modal" id="print-atds">
                                                        <i class='bx bxs-printer'></i>&nbsp; Print ATDs
                                                    </button>
                                                    <button class="btn btn-primary btn-sm" type="button" id="address-atds">
                                                        <i class='bx bx-edit-alt'></i>&nbsp; Address ATDs
                                                    </button>
                                                @endcan
                                            </div> --}}
                                        </div>
                                    </div>
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-black font-bold text-xs p-1 whitespace-nowrap">Bill Image</th>
                                                <th class="text-center text-black font-bold text-xs p-1 whitespace-nowrap">Branch</th>
                                                <th class="text-center text-black font-bold text-xs p-1 whitespace-nowrap">STM No.</th>
                                                <th class="text-center text-black font-bold text-xs p-1 whitespace-nowrap">Currency</th>
                                                {{-- <th class="text-center text-black font-bold text-xs p-1 whitespace-nowrap">Bill Amount</th> --}}
                                                <th class="text-center text-black font-bold text-xs p-1 whitespace-nowrap">ATD Amount</th>
                                                <th class="text-center text-black font-bold text-xs p-1 whitespace-nowrap">Serial</th>
                                                <th class="text-center text-black font-bold text-xs p-1 whitespace-nowrap">Transact. Date</th>
                                                <th class="text-center text-black font-bold text-xs p-1 whitespace-nowrap w-25">Tags</th>
                                                <th class="text-center text-black font-bold text-xs p-1 whitespace-nowrap">Date Tagged</th>
                                                @can('add-permission', $menu_id)
                                                    <th class="text-center text-black font-bold text-xs p-1 whitespace-nowrap">Employee</th>
                                                @endcan
                                                <th class="text-center text-black font-bold text-xs p-1 whitespace-nowrap">Disc. No.</th>
                                                {{-- <th class="text-center text-black font-bold text-xs p-1 whitespace-nowrap">ATD No.</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($result['tagged_bills'] as $tagged_bills)
                                                <tr>
                                                    <td class="text-xs text-center p-1">
                                                        <div class="rounded item-details mx-auto">
                                                            @if ($tagged_bills->FrontBillImage == null && $tagged_bills->BackBillImage == null)
                                                                <img src="{{ asset('uploads/images/default-img.png') }}">
                                                            @else
                                                                <img src="{{ asset('storage/'. $tagged_bills->FrontBillImage) }}" alt="Item Image" class="responsive-image bill_img_show" id="ItemCategoryImg" data-frontimage="{{ asset('storage/'. $tagged_bills->FrontBillImage) }}" data-backimage="{{ asset('storage/'. $tagged_bills->BackBillImage) }}">
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="text-black text-center text-xs p-1">
                                                        {{ $tagged_bills->BranchCode }}
                                                    </td>
                                                    <td class="text-black text-center text-xs p-1">
                                                        {{ $tagged_bills->STMNo }}
                                                    </td>
                                                    <td class="text-black text-center text-xs p-1">
                                                        {{ $tagged_bills->CurrAbbv }}
                                                        <input class="currency" type="hidden" value="{{ $tagged_bills->Currency }}">
                                                    </td>
                                                    {{-- <td class="text-black text-right text-xs py-1 pe-3">
                                                        <strong>
                                                            {{ $tagged_bills->ATDAmount }}
                                                        </strong>
                                                    </td> --}}
                                                    <td class="text-black text-right text-xs py-1 pe-3">
                                                        <strong>
                                                            {{ number_format($tagged_bills->ATDAmount, 2, ',', '.') }}
                                                        </strong>
                                                    </td>
                                                    <td class="text-black text-center text-xs p-1">
                                                        <strong>
                                                            {{ $tagged_bills->Serials }}
                                                        </strong>
                                                        <input class="serial" type="hidden" value="{{ $tagged_bills->Serials }}">
                                                    </td>
                                                    <td class="text-black text-center text-xs p-1">
                                                        {{ \Carbon\Carbon::parse($tagged_bills->DateSold)->format('F d, Y') }}
                                                    </td>
                                                    <td class="text-black text-center text-xs p-1 w-25">
                                                        <select class="select2 form-select tags select addressed-tags"  multiple disabled>
                                                            @foreach ($tagged_bills->BillTags as $saved_bill_tags)
                                                                @foreach ($result['bill_tags'] as $bill_tags)
                                                                    <option class="select-options font-bold" @if($saved_bill_tags->BillStatID == $bill_tags->BillStatID) selected @endif>
                                                                        @if($saved_bill_tags->BillStatID == $bill_tags->BillStatID) {{ $bill_tags->BillStatus }} @endif
                                                                    </option>
                                                                @endforeach
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="text-black text-center text-xs p-1">
                                                        {{ \Carbon\Carbon::parse($tagged_bills->DateAdded)->format('F d, Y') }}
                                                        {{-- <input class="transact-date" type="hidden" value="{{ $tagged_bills->DateAdded }}"> --}}
                                                    </td>
                                                    @can('add-permission', $menu_id)
                                                        <td class="text-black text-center text-xs p-1">
                                                            @if ($tagged_bills->EmployeeID == null)
                                                                <button class="btn btn-primary btn-edit-details btn-sm address-employee" type="button"  type="button" data-bs-toggle="modal" data-bs-target="#address-employee-modal" data-tbtid="{{ $tagged_bills->TBTID }}" data-tbxbranchid="{{ $tagged_bills->TXBranchID }}">
                                                                    Add Employee
                                                                </button>

                                                                {{-- <button class="btn btn-primary btn-edit-details btn-sm address-employee" type="button"  type="button" data-bs-toggle="modal" data-bs-target="#address-employee-modal" data-tbtid="{{ $tagged_bills->TBTID }}" data-bill-amount="{{ $tagged_bills->ATDAmount }}" data-branch-id="{{ $tagged_bills->BranchID }}" data-branch-code="{{ $tagged_bills->BranchCode }}" data-currency="{{ $tagged_bills->Currency }}"> --}}
                                                            @else
                                                                {{ $tagged_bills->FullName }}
                                                            @endif

                                                            <input class="hr-user-id" type="hidden" value="{{ $tagged_bills->HRUserID }}">
                                                            <input class="atd-amount" type="hidden" value="{{ $tagged_bills->ATDAmount }}">
                                                            <input class="pawnshop-user-id" type="hidden" value="{{ $tagged_bills->UserID }}">
                                                            <input class="branch-code" type="hidden" value="{{ $tagged_bills->BranchCode }}">
                                                            <input class="transact-date" type="hidden" value="{{ $tagged_bills->DateSold }}">
                                                            <input class="branch-id" type="hidden" value="{{ $tagged_bills->BranchID }}">
                                                        </td>
                                                    @endcan
                                                    <td class="text-black text-center text-xs p-1">
                                                        @if ($tagged_bills->EmployeeID != null && $tagged_bills->DNO == null)
                                                            @can('add-permission', $menu_id)
                                                                <button class="btn btn-primary btn-sm address-atds" type="button" data-bs-toggle="modal" data-bs-target="#address-atd-sec-code-modal" data-a-tbtid="{{ $tagged_bills->TBTID }}" data-hr-user-id="{{ $tagged_bills->HRUserID }}" data-atd-amount="{{ $tagged_bills->ATDAmount }}" data-pawnshop-user-id="{{ $tagged_bills->UserID }}" data-branch-code="{{ $tagged_bills->BranchCode }}" data-transact-date="{{ $tagged_bills->DateSold }}" data-branch-id="{{ $tagged_bills->BranchID }}" data-currency="{{ $tagged_bills->Currency }}" data-bill-amount="{{ $tagged_bills->BillAmount }}" data-selling-rate="{{ $tagged_bills->SellingRate }}">
                                                                    Address ATD
                                                                </button>
                                                            @endcan
                                                        @elseif ($tagged_bills->EmployeeID != null && $tagged_bills->DNO != null)
                                                            <strong>
                                                                {{ $tagged_bills->DNO }}
                                                            </strong>
                                                        @endif
                                                    </td>
                                                    {{-- <td class="text-black text-center text-xs p-1">
                                                        @if ($tagged_bills->EmployeeID != null && $tagged_bills->ATDNo == null)
                                                            <button class="btn btn-primary button-edit address-atd pe-2" type="button" data-bs-toggle="modal" data-bs-target="#address-atd-modal" data-employeeid="{{ $tagged_bills->EmployeeID }}" data-tbtid="{{ $tagged_bills->TBTID }}">
                                                                Add ATD
                                                            </button>
                                                        @else
                                                            {{ $tagged_bills->ATDNo }}
                                                        @endif

                                                        <input class="transact-date" type="hidden" value="{{ $tagged_bills->DateAdded }}">
                                                    </td> --}}
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-td-buying text-sm py-3" colspan="13">
                                                        <span class="buying-no-transactions text-lg">
                                                            <strong>NO TAGGED BILLS</strong>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12 text-end">
                                                {{ $result['tagged_bills']->links() }}
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

    <div class="modal fade" id="modal-image" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-content modal-content-image modal-lg">
            <div class="modal-header py-2 ps-3">
                <h5 class="modal-title" id="modalTopTitle"><i class="bx bx-image-alt bx-sm me-2"></i>Image Preview</h5>
            </div>
            <div class="modal-body justify-content-center">
                <div class="row px-2">
                    <div class="col-6 px-4">
                        <div class="row">
                            <div class="col-12 text-center mb-1">
                                <span class="front-face text-lg font-bold text-black">Bill Image (Front)</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="card shadow-none border border-3 border-gray-300 rounded-3 p-2">
                                <div class="card-body img-zoom p-0" id="image-zoom">
                                    <div class="zoom" style="position: relative; overflow: hidden;">
                                        <img src="" alt="Item Image" class="front-image p-0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 px-4">
                        <div class="row">
                            <div class="col-12 text-center mb-1">
                                <span class="front-face text-lg font-bold text-black">Bill Image (Back)</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="card shadow-none border border-3 border-gray-300 rounded-3 p-2">
                                <div class="card-body p-0" id="image-zoom">
                                    <div class="zoom" style="position: relative; overflow: hidden;">
                                        <img src="" alt="Item Image" class="back-image p-0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 pt-3">
                        <a class="text-black text-sm" id="download-images"><i class='bx bx-download'></i>&nbsp; Download Images</a>
                    </div>
                </div>
            </div>
            <div class="modal-footer p-1">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="address-employee-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-content modal-content-image modal-md">
            <div class="modal-header py-2 pe-2 ps-3">
                <span class="modal-title font-bold text-lg"></i>Address Employee</span>
            </div>
            <div class="modal-body justify-content-center">
                <div class="row px-2">
                    <div class="col-12 px-2">
                        <label class="mb-1" for="appraisers-select">
                            <strong>Employee:</strong>
                        </label>
                        <select class="form-select" name="appraisers-select" id="appraisers-select">
                            <option >Select employee</option>
                            {{-- @foreach ($result['appraisers'] as $appraisers)
                                <option value="{{ $appraisers->UserID }}" data-branchid="{{ $appraisers->BranchID }}">{{ $appraisers->FullName }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                </div>

                <div class="col-lg-12 px-3">
                    <hr>
                </div>

                <div class="col-lg-12 px-3">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-6 text-center">
                            <label class="mb-2" for="description">
                                <strong>
                                    {{ trans('labels.enter_security_code') }} &nbsp; <span class="required-class">*</span>
                                </strong>
                            </label>

                            <input class="form-control" step="any" autocomplete="false" id="add-employee-security-code" type="password">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer p-1">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm" id="add-employee">Confirm</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="address-atd-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-content modal-content-image modal-md">
            <div class="modal-header py-2 pe-2 ps-3">
                <span class="modal-title font-bold text-lg"></i>Address ATD</span>
            </div>
            <div class="modal-body justify-content-center">
                <div class="row px-2">
                    <div class="col-12 px-2">
                        <label class="my-1" for="atd-select">
                            <strong>ATD Number:</strong>
                        </label>
                        <select class="form-select" name="atd-select" id="atd-select">
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

                            <input class="form-control" step="any" autocomplete="false" id="add-atd-security-code" type="password">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer p-1">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="add-atd">Confirm</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="print-atds-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-content modal-content-image modal-md">
            <div class="modal-header py-2 pe-2 ps-3">
                <span class="modal-title font-bold text-lg"></i>Print ATDs</span>
            </div>
            <div class="modal-body justify-content-center">
                <div class="row px-2">
                    <div class="col-12 px-2">
                        <label class="my-1" for="atd-select">
                            <strong>Date (From - To):</strong>
                        </label>
                        <input class="form-control" name="tagged-bill-date" id="tagged-bill-date" type="text">
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

                            <input class="form-control" step="any" autocomplete="false" id="print-atd-security-code" type="password" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer p-1">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="print-atd-confirm" disabled>Print</button>
            </div>
        </div>
    </div>

    @include('UI.UX.tag_bill')
    @include('UI.UX.security_code')
    @include('UI.UX.address_atd_sec_code')
    {{-- @include('UI.UX.untag_missing_bill_modal') --}}
    {{-- @include('UI.UX.untag_bills_r_transf_security_code') --}}

@endsection

@section('bill_tagging_scripts')
    @include('script.bill_tagging_admin_scripts')
@endsection

@section('qz_tray_scripts')
    @include('script.qz_tray_tagged_bills_atds_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
