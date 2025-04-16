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
                                                    <i class='bx bx-cog'></i>&nbsp;{{ trans('labels.w_bill_tags_mainte') }}
                                                </span>
                                            </div>

                                            <div class="col-6 text-end">
                                                @can('add-permission', $menu_id)
                                                    <a class="btn btn-primary text-white btn-sm" data-bs-toggle="modal" data-bs-target="#bill-tag-maint-add-modal">
                                                        {{ trans('labels.w_bill_tags_mainte_add') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0 mb-1'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-hovered table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-sm font-extrabold text-black py-1 px-1 whitespace-nowrap">Tag Description</th>
                                                <th class="text-center text-sm font-extrabold text-black py-1 px-1 whitespace-nowrap"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($result['bill_tags'] as $bill_tags)
                                                <tr>
                                                    <td class="text-sm text-center p-2 text-black">
                                                        {{ $bill_tags->BillStatus }}
                                                    </td>
                                                    <td class="text-center text-sm p-1 w-25">
                                                        @can('edit-permission', $menu_id)
                                                            <a class="btn btn-primary button-edit button-edit-bill-tag-series" id="button-edit-bill-tag-series" data-billstatid="{{ $bill_tags->BillStatID }}" data-bs-toggle="modal" data-bs-target="#bill-tag-edit-modal">
                                                                <i class='menu-icon tf-icons bx bx-edit-alt text-white'></i>
                                                            </a>
                                                        @endcan

                                                        @can('delete-permission', $menu_id)
                                                            <a class="btn btn-primary button-delete button-delete-fc-form-series" data-billstatid="{{ $bill_tags->BillStatID }}">
                                                                <i class='menu-icon tf-icons bx bx-trash text-white'></i>
                                                            </a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-td-buying text-sm py-3" colspan="13">
                                                        <span class="buying-no-transactions text-lg">
                                                            <strong>NO EXISTING BILL TAGS</strong>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                {{-- {{ $result['bill_tags']->links() }} --}}
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

    <div class="modal fade" id="bill-tag-maint-add-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content add-branch-mainte">
                @include('window.bill_tags_mainte.add_bill_tags_modal')
            </div>
        </div>
    </div>

    <div class="modal fade" id="bill-tag-edit-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content update-bill-tag-details">

            </div>
        </div>
    </div>

    @section('bill_tag_mainte_scripts')
        @include('script.bill_tag_mainte_scripts')
    @endsection

@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
