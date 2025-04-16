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
                                    <hr>
                                </div>

                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center ps-2">
                                            <div class="col-6">
                                                <span class="text-lg font-bold text-black">
                                                    <i class='bx bx-cog'></i>&nbsp;Edit Currency Manual Details
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                @can('access-permission', $menu_id)
                                                    <a class="btn btn-secondary text-white btn-sm" href="{{ route('maintenance.currency_manual') }}">
                                                        Back
                                                    </a>
                                                @endcan
                                                &nbsp;
                                                @can('add-permission', $menu_id)
                                                    <a class="btn btn-primary text-white btn-sm" id="button-add-currency-manual" data-bs-toggle="modal" data-bs-target="#currency-manual-add-modal">
                                                        Add Manual Details <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1">Image</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">Currency Abbrv.</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">Denomination</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">Manual Tag</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">Stop Buying</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">Remarks</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1">Added By</th> --}}
                                                <th class="text-center text-xs font-extrabold text-black p-1">Entry Date</th>
                                                @can('access-permission', $menu_id)
                                                    <th class="text-center text-xs font-extrabold text-black p-1"></th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($result['currency_manual'] as $currency_manual)
                                                <tr>
                                                    <td class="text-xs text-center p-1">
                                                        <div class="rounded item-details mx-auto">
                                                            @if ($currency_manual->BillAmountImage == null)
                                                                <img src=" {{ asset('uploads/images/default-img.png') }}">
                                                            @else
                                                                <img src="{{ asset('storage/'. $currency_manual->BillAmountImage) }}" alt="Item Image" class="responsive-image bill_img_show" id="ItemCategoryImg" data-billimage="{{ asset('storage/'. $currency_manual->BillAmountImage) }}">
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="text-sm text-center p-1">
                                                        <strong>
                                                            {{ $currency_manual->CurrAbbv }}
                                                        </strong>
                                                        <input class="curr-abbv" type="hidden" value="{{ $currency_manual->CurrAbbv }}">
                                                    </td>
                                                    <td class="text-sm text-right py-1 px-3">
                                                        {{ $currency_manual->BillAmount }}
                                                        <input class="bill-amount" type="hidden" value="{{ $currency_manual->BillAmount }}">
                                                    </td>
                                                    <td class="text-xs text-center p-1">
                                                        @if ($currency_manual->CMTID == 1)
                                                            <span class="badge rounded-pill success-badge-custom font-bold">
                                                                <strong>
                                                                    {{ $currency_manual->ManualTag }}
                                                                </strong>
                                                            </span>
                                                        @elseif ($currency_manual->CMTID == 2)
                                                            <span class="badge rounded-pill warning-badge-custom font-bold">
                                                                <strong>
                                                                    {{ $currency_manual->ManualTag }}
                                                                </strong>
                                                            </span>
                                                        @elseif ($currency_manual->CMTID == 3)
                                                            <span class="badge rounded-pill primary-badge-custom font-bold">
                                                                <strong>
                                                                    {{ $currency_manual->ManualTag }}
                                                                </strong>
                                                            </span>
                                                        @endif
                                                        <input class="manual-tag" type="hidden" value="{{ $currency_manual->ManualTag }}">
                                                    </td>
                                                    <td class="text-xs text-center p-1">
                                                        @if ($currency_manual->StopBuying == 1)
                                                            <span class="badge rounded-pill danger-badge-custom font-bold">
                                                                <strong>
                                                                    Yes
                                                                </strong>
                                                            </span>
                                                        @else
                                                            <span class="badge rounded-pill success-badge-custom font-bold">
                                                                <strong>
                                                                    No
                                                                </strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-xs text-center p-1">
                                                        @if ($currency_manual->Remarks == null)
                                                            <strong>
                                                                -
                                                            </strong>
                                                        @else
                                                            {{ $currency_manual->Remarks }}
                                                        @endif
                                                    </td>
                                                    {{-- <td class="text-xs text-center p-1">
                                                        {{ $currency_manual->Name }}
                                                    </td> --}}
                                                    <td class="text-xs text-center p-1">
                                                        {{ \Carbon\Carbon::parse($currency_manual->EntryDate)->format('Y-m-d') }}
                                                    </td>
                                                    <td class="text-center text-xs p-0">
                                                        @can('edit-permission', $menu_id)
                                                            <a class="btn btn-primary button-edit button-edit-curr-manual text-white pe-2" data-cmid="{{ $currency_manual->CMID }}" data-bs-toggle="modal" data-bs-target="#currency-manual-edit-modal">
                                                                <i class='bx bx-detail'></i>
                                                            </a>
                                                        @endcan
                                                        @can('delete-permission', $menu_id)
                                                            <a class="btn btn-primary button-delete button-delete-curr-manual text-white pe-2" data-cmid="{{ $currency_manual->CMID }}" data-bs-toggle="modal" data-bs-target="#del-curr-manual-security-code-modal">
                                                                <i class='bx bx-trash'></i>
                                                            </a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-td-buying text-sm py-3" colspan="13">
                                                        <span class="buying-no-transactions text-lg">
                                                            <strong>NO AVAILABLE </strong>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <div class="col-span-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        {{-- {{ $result['currencies']->links() }} --}}
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
    @include('UI.UX.del_curr_manual_security_code')

    <div class="modal fade" id="currency-manual-add-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content add-currency-manual">
                @include('window.currency_manual_mainte.add_currency_manual_modal')
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
                    <div class="col-12 px-4">
                        <div class="row">
                            <div class="card shadow-none border border-3 border-gray-300 rounded-3 p-2">
                                <div class="card-body img-zoom p-0" id="image-zoom">
                                    <div class="zoom" style="position: relative; overflow: hidden;">
                                        <img src="" alt="Item Image" class="bill-image p-0">
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

    @if (count($result['currency_manual']) > 0)
        <div class="modal fade" id="currency-manual-edit-modal" tabindex="-1" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content edit-currency-manual">
                    @include('window.currency_manual_mainte.edit_currency_manual_modal')
                </div>
            </div>
        </div>
    @endif

@endsection

@section('currency_manual_scripts')
    @include('script.currency_manual_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
