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
                                                    <i class='bx bx-cog'></i>&nbsp;{{ trans('labels.selling_limit_maintenance') }}
                                                </span>
                                            </div>

                                            <div class="col-6 text-end">
                                                @can('add-permission', $menu_id)
                                                    <a class="btn btn-primary text-white btn-sm" data-bs-toggle="modal" data-bs-target="#selling-limit-maint-add-modal">
                                                        {{ trans('labels.w_sell_limit_mainte_add') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0 mb-1'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-hovered table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap w-50">Company</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap w-25">Selling Limit</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap w-25">Status</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap w-25"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($result['selling_limits']  as $selling_limits)
                                                <tr>
                                                    <td class="text-sm text-center p-2 text-black">
                                                        {{ $selling_limits->CompanyName }}
                                                    </td>
                                                    <td class="text-sm text-right py-2 px-3 text-black">
                                                        <strong>{{ number_format($selling_limits->Limit, 2, ',', '.') }}</strong>
                                                    </td>
                                                    <td class="text-xs text-center p-2 text-black">
                                                        @if ($selling_limits->Active == 1)
                                                            <span class="badge rounded-pill primary-badge-custom font-bold">
                                                                <strong>
                                                                    Limited
                                                                </strong>
                                                            </span>
                                                        @else
                                                            <span class="badge rounded-pill success-badge-custom font-bold">
                                                                <strong>
                                                                    No Limit
                                                                </strong>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center text-sm p-1 w-25">
                                                        @can('edit-permission', $menu_id)
                                                            <a class="btn btn-primary button-edit button-edit-selling-limit" id="button-edit-selling-limit" data-slid="{{ $selling_limits->SLID }}" data-bs-toggle="modal" data-bs-target="#selling-limit-edit-modal">
                                                                <i class='menu-icon tf-icons bx bx-edit-alt text-white'></i>
                                                            </a>
                                                        @endcan
                                                        {{-- @can('add-permission', $menu_id)
                                                            <a class="btn btn-primary button-delete button-delete-selling-limit" data-slid="{{ $selling_limits->SLID }}">
                                                                <i class='menu-icon tf-icons bx bx-trash text-white'></i>
                                                            </a>
                                                        @endcan --}}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-td-buying text-xs py-3" colspan="13">
                                                        <span class="buying-no-transactions text-lg">
                                                            <strong>NO EXISTING SELLING LIMIT</strong>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <div class="col-12 p-1 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                {{ $result['selling_limits']->links() }}
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

    <div class="modal fade" id="selling-limit-maint-add-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content add-branch-mainte">
                @include('window.bulk_limit_selling_mainte.add_bill_tag_modal')
            </div>
        </div>
    </div>

    <div class="modal fade" id="selling-limit-edit-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content update-selling-limit-details">

            </div>
        </div>
    </div>

    @section('selling_limit_mainte_scripts')
        @include('script.selling_limit_mainte_scripts')
    @endsection

@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
