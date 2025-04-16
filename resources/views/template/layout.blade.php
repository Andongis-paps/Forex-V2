<!DOCTYPE html>
    <html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="http://localhost/Forex/resources/plugins/sneat/assets/" data-template="vertical-menu-template-free">
    <head>
        @include('template.meta')
    </head>
        @include('modals.toast')
    <body id="forex-html-body">
        @include('modals.message_boxes')

        <div class="col-12" id="container-test">
            <div class="card shadow-none" id="card-test">
                <svg viewBox="0 0 100 100" id="loader-test">
                    <g fill="none" stroke="#00A65A" stroke-linecap="round"stroke-linejoin="round" stroke-width="6">
                        <!-- left line -->
                        <path d="M 21 40 V 59">
                            <animateTransform attributeName="transform" attributeType="XML" type="rotate" values="0 21 59; 180 21 59" dur=".8s" repeatCount="indefinite"/>
                        </path>
                        <!-- right line -->
                        <path d="M 79 40 V 59">
                            <animateTransform attributeName="transform" attributeType="XML" type="rotate" values="0 79 59; -180 79 59" dur=".8s" repeatCount="indefinite"/>
                        </path>
                        <!-- top line -->
                        <path d="M 50 21 V 40">
                            <animate attributeName="d" values="M 50 21 V 40; M 50 59 V 40" dur=".8s" repeatCount="indefinite"/>
                        </path>
                        <!-- btm line -->
                        <path d="M 50 60 V 79">
                            <animate attributeName="d" values="M 50 60 V 79; M 50 98 V 79" dur=".8s" repeatCount="indefinite"/>
                        </path>
                        <!-- top box -->
                        <path d="M 50 21 L 79 40 L 50 60 L 21 40 Z">
                            <animate attributeName="stroke" values="rgba(0,166,90,1); rgba(100,100,100,0)" dur=".8s" repeatCount="indefinite"/>
                        </path>
                        <!-- mid box -->
                        <path d="M 50 40 L 79 59 L 50 79 L 21 59 Z" />
                        <!-- btm box -->
                        <path d="M 50 59 L 79 78 L 50 98 L 21 78 Z">
                            <animate attributeName="stroke" values="rgba(100,100,100,0); rgba(0,166,90,1)" dur=".8s" repeatCount="indefinite"/>
                        </path>
                        <animateTransform attributeName="transform" attributeType="XML" type="translate" values="0 0; 0 -19" dur=".8s" repeatCount="indefinite"/>
                    </g>
                </svg>
            </div>
        </div>

        <div id="components-container">
            @yield('content')

            @yield('qz_tray_scripts')

            @yield('buying_scripts')

            @yield('selling_scripts')

            @yield('transf_forex_scripts')

            @yield('receive_transf_fx_scripts')

            @yield('received_transf_fx_scripts')

            @yield('buffer_transfer_scripts')

            @yield('selling_admin_scripts')

            @yield('bill_tagging_scripts')

            @yield('currency_mainte_scripts')

            @yield('rate_config_scripts')

            @yield('rate_mainte_scripts')

            @yield('dpofx_rate_mainte_scripts')

            @yield('branch_mainte_scripts')

            @yield('r_series_mainte_scripts')

            @yield('bill_tag_mainte_scripts')

            @yield('transact_type_mainte_scripts')

            @yield('selling_limit_mainte_scripts')

            @yield('curr_stocks_scripts')

            @yield('dpo_scripts')

            @yield('admin_buying_scripts')

            @yield('admin_serials')

            @yield('admin_selling_scripts')

            @yield('stop_buying_scripts')

            @yield('currency_manual_scripts')

            @yield('dasboard_scripts')

            @yield('trans_c_scripts')

            @yield('comp_limit_scripts')

            @yield('r_set_series_scripts')

            @include('template.navbar')

            {{-- @include('template.footer') --}}

            @include('script.scripts')

            @auth
                @include('script.socketio')

                @include('script.configurations_scripts')
            @endauth
        </div>
    </body>
</html>
