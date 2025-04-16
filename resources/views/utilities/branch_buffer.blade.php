@extends('template.layout')
@section('content')

    {{-- csrf token retrieveal per user login --}}
    {{session('csrf_token')}}

    <div class="layout-page">
        <!-- Navbar -->
        <div class="content-wrapper">
            {{-- <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                        <i class="bx bx-menu bx-sm"></i>
                    </a>
                </div>
            </nav> --}}
            <!-- Content -->
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <div class="row justify-content-center">
                            <div class="col-4">
                                <div class="card">
                                    <div class="col-span-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="d-flex align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-semibold p-2 text-black">
                                                    {{ trans('labels.buffer_branch') }}
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                <form action="{{ route('branchBuffer') }}" method="GET" class="form p-0 m-0" id="form-toggle-orderby">
                                                    <div class="d-flex align-items-center">
                                                        <div class="col-5">
                                                            <span class="text-sm">Order by: </span>
                                                        </div>
                                                        <div class="col-7 px-1">
                                                            <select class="form-select" id="toggle_select_submit" name="toggle">
                                                                <option value="" class="selected-order-by" type="hidden">{{ $order ===  'ASC' ? 'Ascending' : 'Descending'}}</option>
                                                                <option @if ($order ===  'ASC') disabled @endif>Ascending</option>
                                                                <option @if ($order ===  'DESC') disabled @endif>Descending</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>{{ trans('labels.branch') }}</th>
                                                <th class="text-center">{{ trans('labels.action_data') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div id="buffered-branch">
                                                @if (count($result['branch_buffer']) > 0)
                                                    @foreach ($result['branch_buffer'] as $branch_buffer)
                                                        <tr>
                                                            <td>{{ $branch_buffer->BranchCode }}</td>
                                                            <td class="text-center text-sm">
                                                                <a href="" class="btn btn-danger button-delete">
                                                                    <i class='menu-icon tf-icons bx bx-trash'></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </tbody>
                                    </table>

                                    <div class="col-span-12 p-3 border border-gray-300 rounded-bl rounded-br">
                                        {{ $result['branch_buffer']->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
