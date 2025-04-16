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
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <div class="card">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ trans('labels.user_level_id') }}</th>
                                        <th>{{ trans('labels.user_level_description') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($result['user_levels']) > 0)
                                        @foreach($result['user_levels'] as $user_levels)
                                            <tr>
                                                <td>
                                                    {{ $user_levels->userlevelid }}
                                                </td>
                                                <td>
                                                    {{ $user_levels->userlevelname }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
