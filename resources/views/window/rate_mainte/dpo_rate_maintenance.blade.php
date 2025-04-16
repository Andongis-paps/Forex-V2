@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <hr>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-12 mb-4">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold ps-2 text-black">
                                                    <i class='bx bx-cog'></i>&nbsp;{{ trans('labels.dpofx_maintenance') }}
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                @can('edit-permission', $menu_id)
                                                    <a class="btn btn-primary btn-sm text-white update-dpofx-button" id="update-dpofx-button" data-bs-toggle="modal" data-bs-target="#dpo-rate-maint-update-modal">
                                                        {{ trans('labels.dpofx_update_all') }} <i class='menu-icon tf-icons bx bx-up-arrow-circle text-white ms-1 me-0'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.dpofx_branch') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.dpofx_rate') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div id="buffered-branch">
                                                @if (count($result['dpofx_rate']) > 0)
                                                    @foreach ($result['dpofx_rate'] as $dpofx_rate)
                                                        <tr class="branch-list-table" id="branch-list-table">
                                                            <td class="text-center text-sm p-1">
                                                                {{ $dpofx_rate->BranchCode }}
                                                            </td>
                                                            <td class="text-right font-bold text-sm py-1 px-3">
                                                                {{ $dpofx_rate->Rate }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </tbody>
                                    </table>

                                    <div class="card-footer p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="col-span-12">
                                            <div class="row align-items-center">
                                                <div class="col-12">
                                                    {{ $result['dpofx_rate']->links() }}
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
    </div>

    @include('UI.UX.security_code')

    {{-- Add new currency via AJAX --}}
    <div class="modal fade" id="dpo-rate-maint-update-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content update-dpo-rate">
                @include('window.rate_mainte.update_dpo_rate_modal')
            </div>
        </div>
    </div>

    {{-- Update rate details via AJAX --}}
    {{-- <div class="modal fade" id="dpo-rate-maint-edit-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content update-dpo-rate-details">

            </div>
        </div>
    </div> --}}

@endsection

@section('dpofx_rate_mainte_scripts')
    @include('script.dpofx_rate_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
