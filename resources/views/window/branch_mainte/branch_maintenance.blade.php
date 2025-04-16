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

                                @if(session()->has('message'))
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        {{ session()->get('message') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <div class="col-12">
                                    <hr>
                                </div>

                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-cog'></i>&nbsp;{{ trans('labels.w_based_branch') }}
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                @can('add-permission', $menu_id)
                                                    <a class="btn btn-primary btn-sm text-white button-add-branch" id="button-add-branch" data-bs-toggle="modal" data-bs-target="#branch-maint-add-modal">
                                                        {{ trans('labels.w_branch_add') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1">Branch</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.w_branch_address') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1">Distant</th>
                                                {{-- @can('edit-permission', $menu_id)
                                                    <th class="text-center text-xs font-extrabold text-black p-1"></th>
                                                @endcan --}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div id="buffered-branch">
                                                @if (count($result['branches']) > 0)
                                                    @foreach ($result['branches'] as $branches)
                                                        <tr class="branch-list-table" id="branch-list-table" data-branchcode="{{ $branches->BranchCode }}">
                                                            <td class="text-center text-sm p-1">
                                                                <strong>
                                                                    {{ $branches->BranchCode }}
                                                                </strong>
                                                            </td>
                                                            <td class="text-center text-sm p-1">
                                                                {{ $branches->Address }}
                                                            </td>
                                                            <td class="text-center text-xs p-1">
                                                                @if (intval($branches->DistantLocation) == 1)
                                                                    <span class="badge success-badge-custom">
                                                                        <strong>Yes</strong>
                                                                    </span>
                                                                @else
                                                                    <span class="badge primary-badge-custom">
                                                                        <strong>No</strong>
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            {{-- @can('edit-permission', $menu_id)
                                                                <td class="text-center text- p-1">
                                                                    <a class="btn btn-primary button-edit button-edit-branch" data-branchmaintid="{{ $branches->BranchID }}" id="button-edit-branch" data-bs-toggle="modal" data-bs-target="#branch-maint-edit-modal">
                                                                        <i class='menu-icon tf-icons bx bx-edit-alt text-white'></i>
                                                                    </a>
                                                                </td>
                                                            @endcan --}}
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </tbody>
                                    </table>

                                    <div class="col-12 p-3 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                {{ $result['branches']->links() }}
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

    {{-- Add new branch via AJAX --}}
    <div class="modal fade" id="branch-maint-add-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content add-branch-mainte">
                @include('window.branch_mainte.add_branch_mainte_modal')
            </div>
        </div>
    </div>

    {{-- Update branch details via AJAX --}}
    <div class="modal fade" id="branch-maint-edit-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content update-branch-details">

            </div>
        </div>
    </div>

@endsection

@section('branch_mainte_scripts')
    @include('script.branch_mainte_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
