<div class="layout-wrapper layout-content-navbar z-40">
    {{-- <div class="layout-container"> --}}
        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme sidenav-header-banner shadow-lg">
            <div class="col-12">
                <div class="app-brand d-flex justify-content-center mt-3">
                    {{-- <a href="{{ route('branch_transactions.dashboard') }}" class="app-brand-link"> --}}
                    <a class="app-brand-link">
                        <span class="forex-login-icon-navbar justify-content-center">
                            <img src="{{ asset('images/sinag-logo-full.png') }}" alt="forex-web" width="200">
                        </span>
                    </a>
                </div>

                <div class="app-brand mt-3">
                    <a href="javascript:void(0);" class="custom-menu-toggle text-large ms-auto" id="test-click">
                        <i class="bx bx-chevron-left bx-sm align-middle" id="collapse-nav-chevron-left-icon"></i>
                    </a>
                </div>

                <div class="px-4">
                    <hr>
                </div>
            </div>

            <div class="menu-inner-shadow"></div>

            <ul class="menu-inner overflow-y-auto overflow-x-hidden">
                @foreach (App\Helpers\MenuManagement::getAdminMenuItems() as $menu)
                    @php
                        $menuLabel = str_replace('_', ' ', $menu['name']);
                        $icon = '';
                        $menuName = ucwords(strtolower($menuLabel));

                        switch ($menuName) {
                            case 'Dashboard':
                                $icon = 'bxs-dashboard';
                                break;
                            case 'Branch Transactions':
                                $icon = 'bx bxs-dollar-circle';
                                break;
                            case 'Admin Transactions':
                                $icon = 'bx bxs-dollar-circle';
                                break;
                            default:
                                $icon = 'bx-cog';
                                break;
                        }
                    @endphp
                    @if (isset($menu['children']) && count($menu['children']) > 0)
                        <li class="menu-item text-xxs {{ request()->is(strtolower($menu['name']) . '/*') ? 'active open' : '' }}">
                            <a href="javascript:void(0);" class="menu-link py-2 menu-toggle">
                                <i class="menu-icon tf-icons bx {{ $icon }}"></i>
                                <div class="text-truncate">{{ ucwords(strtolower($menuLabel)) }}</div>
                            </a>
                            <ul class="menu-sub">
                                @foreach ($menu['children'] as $submenu)
                                    @php
                                        $submenuLabel = str_replace('_', ' ', $submenu['name'])
                                    @endphp

                                    @if (isset($submenu['children']) && count($submenu['children']) > 0)
                                        <li class="menu-item text-xxs {{ request()->is(strtolower($menu['name']) . '/' . strtolower($submenu['name']) . '/*') ? 'active open' : '' }}">
                                            <a href="javascript:void(0);" class="menu-link py-2 menu-toggle">
                                                <div class="text-truncate">{{ ucwords(strtolower($submenuLabel )) }}</div>
                                            </a>
                                            <ul class="menu-sub ">
                                                @foreach ($submenu['children'] as $secondsubmenu)
                                                    @php
                                                        $secondsubmenuLabel = str_replace('_', ' ', $secondsubmenu['name'])
                                                    @endphp
                                                    <li class="menu-item text-xxs {{ request()->is(strtolower($menu['name']) . '/' . strtolower($submenu['name']) . '/' . strtolower($secondsubmenu['name'])) || request()->is(strtolower($menu['name']) . '/' . strtolower($submenu['name']) . '/' . strtolower($secondsubmenu['name']) . '/*') ? 'active' : '' }}">
                                                        <a href="{{ route($secondsubmenu['route_name']) }}" class="menu-link py-2">
                                                            <div class="text-truncate">{{ ucwords(strtolower($secondsubmenuLabel)) }}</div>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @else
                                        <li class="menu-item text-xxs {{ request()->is(strtolower($menu['name']) . '/' . strtolower($submenu['name'])) || request()->is(strtolower($menu['name']) . '/' . strtolower($submenu['name']) . '/*') ? 'active' : '' }}">
                                            <a href="{{ route($submenu['route_name']) }}" class="menu-link py-2">
                                                <div class="text-truncate">{{ ucwords(strtolower($submenuLabel)) }}</div>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li class="menu-item text-xxs {{ request()->is(strtolower($menu['name'])) || request()->is(strtolower($menu['name']) . '/*') ? 'active' : '' }}">
                            <a href="{{ route($menu['route_name']) }}" class="menu-link py-2">
                                <i class="menu-icon tf-icons bx {{ $icon }}"></i>
                                <div class="text-truncate">{{ ucwords(strtolower($menuLabel)) }}</div>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </aside>
    {{-- </div> --}}

    <input type="hidden" id="base-url" value="{{ url('/') }}">
</div>
