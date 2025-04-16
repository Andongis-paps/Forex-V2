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
                                                    <i class='bx bx-cog'></i>&nbsp;Company Limit Maintenance
                                                </span>
                                            </div>

                                            <div class="col-6 text-end">
                                                @can('add-permission', $menu_id)
                                                    <a class="btn btn-primary text-white btn-sm" href="{{ route('maintenance.company_limit.add_company_limit') }}">
                                                        Add Limit<i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0 mb-1'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-hovered table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap">Company</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap">Annual Limit</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap">R-Set Series (O)</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap">Status</th>
                                                <th class="text-center text-xs font-extrabold text-black py-1 px-1 whitespace-nowrap"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($result['company_limit'] as $company_limit)
                                                <tr>
                                                    <td class="text-xs text-center p-2 text-black whitespace-nowrap">
                                                        {{ $company_limit->CompanyName }}
                                                    </td>
                                                    <td class="text-xs text-right p-2 text-black">
                                                        {{ number_format($company_limit->AnnualLimit, 2) }}
                                                    </td>
                                                    <td class="text-xs text-center p-2 text-black">
                                                        {{ str_pad($company_limit->SeriesO, 6, '0', STR_PAD_LEFT) }}
                                                    </td>
                                                    <td class="text-xs text-center p-2 text-black">
                                                        @if ($company_limit->Status == 1)
                                                            <span class="badge rounded-pill primary-badge-custom font-bold">
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
                                                    <td class="text-center text-sm p-1">
                                                        @can('edit-permission', $menu_id)
                                                            <a class="btn btn-primary button-edit edit-company-limit" id="edit-company-limit" href="{{ route('maintenance.company_limit.edit_company_limit', ['CLDID' => $company_limit->CLDID]) }}">
                                                                <i class='menu-icon tf-icons bx bx-edit-alt text-white'></i>
                                                            </a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-td-buying text-xs py-3" colspan="30">
                                                        <span class="buying-no-transactions text-lg">
                                                            <strong>NO AVAILABLE DATA</strong>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <div class="col-12 p-1 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                {{ $result['company_limit']->links() }}
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
{{-- 
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
    </div> --}}

    {{-- @section('selling_limit_mainte_scripts')
        @include('script.selling_limit_mainte_scripts')
    @endsection --}}

@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
