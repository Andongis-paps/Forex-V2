@extends('template.layout')
@section('content')

    {{-- csrf token retrieveal per user login --}}
    {{-- {{session('csrf_token')}} --}}

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
                            <div class="col-12">

                                @if(session()->has('message'))
                                    <div class="alert alert-success alert-dismissible" role="alert">
                                        {{ session()->get('message') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-5">
                                                <span class="text-lg font-semibold p-2 text-black">
                                                    {{ trans('labels.w_based_expense') }}
                                                </span>
                                            </div>
                                            <div class="col-5 text-end">
                                                <div class="row align-items-center">
                                                    <div class="col-12">
                                                        {{-- <form action="{{ URL::to('/searchFetch') }}" method="POST" autocomplete="off" name="search-branch-form" class="m-0"> --}}
                                                            <div class="d-flex align-items-center">
                                                                <div class="col-12">
                                                                    <input type="text" class="form-control search-expense" id="search-expense" name="search-expense-word" value="{{ app('request')->input('search') }}" placeholder="Search for a expense">
                                                                </div>
                                                                {{-- <div class="col-3 text-end">
                                                                    <button class="btn btn-primary" type="submit">Search</button>
                                                                </div> --}}
                                                            </div>
                                                        {{-- </form> --}}
                                                        {{ csrf_field() }}
                                                    </div>
                                                    {{-- <div class="col-2">
                                                        <span class="text-sm">Order by: </span>
                                                    </div>
                                                    <div class="col-3">
                                                        <form action="{{ route('branchMaintenance') }}" method="GET" class="form p-0 m-0" id="form-toggle-orderby">
                                                            <select class="form-select" id="toggle_select_submit" name="toggle">
                                                                <option value="" class="selected-order-by">{{ $order ===  'ASC' ? 'Ascending' : 'Descending'}}</option>
                                                                <option @if ($order ===  'ASC') disabled @endif>Ascending</option>
                                                                <option @if ($order ===  'DESC') disabled @endif>Descending</option>
                                                            </select>
                                                        </form>
                                                    </div> --}}
                                                </div>
                                            </div>
                                            <div class="col-2 text-end pe-4">
                                                <a class="btn btn-primary button-add button-add-expense" id="button-add-expense" data-bs-toggle="modal" data-bs-target="#expense-maint-add-modal">
                                                    Add new &nbsp;<i class='menu-icon tf-icons bx bx-plus text-white'></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xl font-extrabold text-black">{{ trans('labels.w_expense_id') }}</th>
                                                <th class="text-center text-xl font-extrabold text-black">{{ trans('labels.w_expense_name') }}</th>
                                                <th class="text-center text-xl font-extrabold text-black">{{ trans('labels.action_data') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div id="buffered-branch">
                                                @if (count($result['expenses']) > 0)
                                                    @foreach ($result['expenses'] as $expenses)
                                                        <tr class="branch-list-table" id="branch-list-table" data-expensendesc="{{ $expenses->ExpenseName }}">
                                                            <td class="text-center text-sm">
                                                                {{ $expenses->EID }}
                                                            </td>
                                                            <td class="text-center text-sm">
                                                                {{ $expenses->ExpenseName }}
                                                            </td>
                                                            <td class="text-center text-sm">
                                                                <a class="btn btn-primary button-edit button-edit-expense" data-expensemaintid="{{ $expenses->EID }}" id="button-edit-expense" data-bs-toggle="modal" data-bs-target="#expense-maint-edit-modal">
                                                                    <i class='menu-icon tf-icons bx bx-edit-alt text-white'></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </tbody>
                                    </table>

                                    <div class="col-span-12 p-3 border border-gray-300 rounded-bl rounded-br">
                                        {{ $result['expenses']->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add new currency via AJAX --}}
    <div class="modal fade" id="expense-maint-add-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content add-expense-mainte">
                @include('window.expense_mainte.add_expense_mainte_modal')
            </div>
        </div>
    </div>

    {{-- Update currency details via AJAX --}}
    <div class="modal fade" id="expense-maint-edit-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content update-expense-details">

            </div>
        </div>
    </div>

@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
