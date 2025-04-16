<!-- Navbar -->
<nav class=" border border-gray-300 layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme z-40" id="layout-navbar" style="z-index: 2;">
    <!-- ! Not required for layout-without-menu -->
    {{-- <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0  d-xl-none ">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div> --}}

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <div class="navbar-nav align-items-center">
            <div class="nav-item navbar-search-wrapper mb-0">
                {{-- <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
                    <span class="d-none d-md-inline-block text-lg"  id="toggle-rset"><strong>Hello! </strong> {{ Auth::user()->Name }}</span>
                </a> --}}
                <span class="d-none d-md-inline-block text-lg"><strong>Hello! </strong> {{ Auth::user()->Name }}</span>
            </div>
        </div>

        <ul class="navbar-nav flex-row align-items-center ms-auto">

        <!-- Rset Toggle Switcher -->
        <li class="nav-item dropdown-style-switcher dropdown px-2" style="overflow: hidden;">
            <div class="btn-group rset-btn btn-group-xs" role="group" >
                <input type="radio" class="btn-check" name="rsetradio" id="on" value="1" {!! session('time_toggle_status') == 1? 'checked' : '' !!}>
                <label class="btn btn-outline-primary" for="on">Online</label>
                <input type="radio" class="btn-check" name="rsetradio" id="off" value="0" {!! session('time_toggle_status') == 0? 'checked' : '' !!}>
                <label class="btn btn-outline-primary" for="off">Offline</label>
            </div>
        </li>
        <!-- Rset Toggle Switcher -->

        <li>
            <button type="button" class="btn text-nowrap d-inline-block btn-xs" data-bs-toggle="dropdown" data-bs-auto-close="outside" id="notification-count">
                <span class="bx bx-bell bx-sm" id="bell-icon"></span>
                    {{-- notification count --}}
            </button>

            <ul class="dropdown-menu dropdown-menu-end py-0 w-25 z-50" data-bs-popper="static" id="dropdown">
                <li class="dropdown-menu-header border-bottom">
                    <div class="dropdown-header d-flex align-items-center py-2 text">
                        <h5 class="text-body mb-0 me-auto !font-semibold">
                            Notifications
                        </h5>
                        <h6 class="text-body mb-0 me-0 text-end" id="notif-count">
                        </h6>
                    </div>
                </li>

                <li class="dropdown-notifications-list scrollable-container">
                    <ul class="list-group notification-dropdown" id="notification-body">

                    </ul>
                    <li class="dropdown-menu-footer border-top p-2" id="notification-footer">
                        <div class="col-12 text-center">
                            <div class="row px-3">
                                <a class="btn btn-primary btn-sm" type="button" href="{{ route('notif.show') }}">
                                    See All
                                </a>
                            </div>
                        </div>
                    </li>
                </li>
            </ul>
                <div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; right: 0px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div></div></li>
                {{-- <button class="btn btn-primary text-uppercase w-100">view all notifications</button> --}}
            </li>
        </li>

        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
            {{-- <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar  {!! session('time_toggle_status') == 1 ? 'avatar-online' : '' !!}">
                <img src="{{ asset('images/profile 1.png') }}" alt="" class="w-px-40 h-auto rounded-circle">
                </div>
            </a> --}}

            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar" id="rsetAvatar">
                    @php
                        $imageLink = \App\Helpers\UserManagement::getProfilePicture(auth()->user()->UserProfile);
                    @endphp

                    <img src="{{ $imageLink }}" alt="{{ auth()->user()->Name }} Avatar" class="w-px-40 h-auto rounded-circle shadow-sm" />
                </div>
            </a>

        <ul class="dropdown-menu dropdown-menu-end z-50 custom-width">
            <li>
                <span class="d-none d-md-inline-block text-lg  w-100 px-3 mx-1"><i class="bx bx-pen me-2"></i> <small>{{   Auth::user()->getPosition() ?? 'No Rank Yet' }}</small></span>
            </li>
            <li>
                <span class="d-none d-md-inline-block text-lg  w-100 px-3 mx-1"><i class="bx bx-home-alt me-2 "></i><small> {{   Auth::user()->getBranch()->BranchCode.' - '.Auth::user()->getMunicipality()->province_name.' - '.Auth::user()->getBranch()->OM  }}</small></span>
            </li>
            @if(!in_array(Auth::user()->PositionID,[1,2,3,5,78]))
                <li>
                    <div class="dropdown-divider "></div>
                </li>
                <li>
                    <div class="col-12 text-center">
                        <div class="row px-4">
                            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#chng-branch-security-code-modal">
                                Change Branch
                            </button>
                        </div>
                    </div>
                </li>
            @endif
            <li>

            <li>
                <div class="dropdown-divider mb-0"></div>
            </li>
            {{-- <li>
            <a class="dropdown-item  py-0 " href="{{ route('user',['id' => Auth::user()->UserID]) }}">
                <i class="bx bx-user me-2"></i>
                <span class="align-middle">My Profile</span>
            </a>
            </li>  --}}
            {{-- <li>
            <span class="d-none d-md-inline-block text-lg  w-100 px-3 mx-1  "><i class="bx bx-map me-2 "></i><small > {{   Auth::user()->getBranch()->Address  }}</small></span>
            </li>
            <li>
            <div class="dropdown-divider "></div>
            </li> --}}
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="mb-0">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="bx bx-power-off me-2"></i>
                            <span class="align-middle">LOGOUT</span>
                        </button>
                    </form>
                </li>
            </ul>
            </li>
        <!--/ User -->
        </ul>
    </div>
</nav>

@include('UI.UX.change_branch')
<!-- / Navbar -->
