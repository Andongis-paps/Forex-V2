@extends('template.layout')
@section('content')

   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-cog'></i>&nbsp;{{ trans('labels.trans_type_meintenance') }}
                                                </span>
                                            </div>

                                            <div class="col-6 text-end">
                                                @can('add-permission', $menu_id)
                                                    <a class="btn btn-primary text-white btn-sm" data-bs-toggle="modal" data-bs-target="#transact-type-maint-add-modal">
                                                        {{ trans('labels.w_transact_type_mainte_add') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0 mb-1'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-hovered table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap">Transaction Type</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap">Status</th>
                                                @can('edit-permission', $menu_id)
                                                    <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap"></th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($result['transact_types']  as $transact_types)
                                                <tr>
                                                    <td class="text-sm text-center p-2 text-black">
                                                        {{ $transact_types->TransType }}
                                                    </td>
                                                    <td class="text-xs text-center p-2 text-black">
                                                        @if ($transact_types->Active == 1)
                                                            <span class="badge rounded-pill primary-badge-custom pt-2 font-bold">
                                                                <strong>
                                                                    Active
                                                                </strong>
                                                            </span>
                                                        @else
                                                            <span class="badge rounded-pill warning-badge-custom font-bold">
                                                                <strong>
                                                                    Inactive
                                                                </strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center text-sm p-1 w-25">
                                                        @can('edit-permission', $menu_id)
                                                            <a class="btn btn-primary button-edit button-edit-trans-type" id="button-edit-bill-tag-series" data-ttid="{{ $transact_types->TTID }}" data-bs-toggle="modal" data-bs-target="#trans-type-edit-modal">
                                                                <i class='menu-icon tf-icons bx bx-edit-alt text-white'></i>
                                                            </a>
                                                        @endcan
                                                        {{-- @can('delete-permission', $menu_id)
                                                            <a class="btn btn-primary button-delete button-delete-trans-type" data-ttid="{{ $transact_types->TTID }}">
                                                                <i class='menu-icon tf-icons bx bx-trash text-white'></i>
                                                            </a>
                                                        @endcan --}}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-td-buying text-xs py-3" colspan="13">
                                                        <span class="buying-no-transactions text-lg">
                                                            <strong>NO EXISTING BILL TAGS</strong>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <div class="col-12 p-1 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                {{ $result['transact_types']->links() }}
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

    <div class="modal fade" id="transact-type-maint-add-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content add-branch-mainte">
                @include('window.transact_types_mainte.add_transact_type_modal')
            </div>
        </div>
    </div>

    <div class="modal fade" id="trans-type-edit-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content update-trans-type-details">

            </div>
        </div>
    </div>

    @section('transact_type_mainte_scripts')
        @include('script.transact_type_mainte_scripts')
    @endsection

@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
