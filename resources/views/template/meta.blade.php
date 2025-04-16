{{-- <head> --}}
    {{-- Note: all of the plugins, css frameworks and other cdns used for the
    project is declared here This meta file serves as a global variable for the project --}}

    {{-- For boostrap and other front end plugin links --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-5/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons-1.9.1/bootstrap-icons.css') }}"/>
    {{-- <link rel="stylesheet" href="{{ asset('css/swiper-bundle.min.css') }}"/> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="{{ asset('css/forex-css-v2.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/forex.png') }}"/>
    {{-- JqueryUI css --}}
    <link rel="stylesheet" href="{{ asset('plugins/jquery-ui-1.13.2.custom/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/jquery-ui-1.13.2.custom/jquery-ui.structure.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/jquery-ui-1.13.2.custom/jquery-ui.theme.css') }}">

    {{-- Include Flatpickr CSS --}}
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/flatpickr.css') }}"/>

    {{-- Jquery loader --}}
    <link rel="stylesheet" href="{{ asset('css/loader-css/jquery-loading.css') }}"/>
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>

    {{-- @if (url()->full() != URL::to('/').'/logout' && url()->full() != URL::to('/')) --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('images/forex.png') }}"/>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet"/>

        <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">

        <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/fonts/boxicons.css') }}" />
        <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
        <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
        <link rel="stylesheet" href="{{ asset('sneat/assets/css/demo.css') }}" />

        <script type="text/javascript" src="{{ asset('sneat/assets/vendor/js/helpers.js') }}"></script>
        <script type="text/javascript" src="{{ asset('sneat/assets/js/config.js') }}"></script>

        <link rel="stylesheet" href="{{asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />
        <!-- Core JS -->
        <!-- build:js assets/vendor/js/core.js -->
        <script type="text/javascript" src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
        <script type="text/javascript" src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
        <script type="text/javascript" src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
        <script type="text/javascript" src="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
        <script type="text/javascript" src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>
        <!-- Vendors JS -->
        <script type="text/javascript" src="{{ asset('plugins/apex-charts/dist/apexcharts.min.js') }}"></script>
        <link rel="stylesheet" href="{{asset('plugins/apex-charts/dist/apexcharts.css')}}" />
        {{-- <link rel="stylesheet" href="{{asset('sneat/assets/vendor/libs/dist/apex-charts/apex-charts.css') }}"/> --}}
        {{-- <script type="text/javascript" src="{{ asset('sneat/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script> --}}
        <!-- Main JS -->
        <script type="text/javascript" src="{{ asset('sneat/assets/js/main.js')}}"></script>
        <!-- Page JS -->
        <script type="text/javascript" src="{{ asset('sneat/assets/js/ui-popover.js') }}"></script>
        <!-- Place this tag in your head or just before your close body tag. -->
        <script async defer src="https://buttons.github.io/buttons.js"></script>

        {{-- <script src="https://cdn.jsdelivr.net/npm/swiper@11.1.14/swiper-bundle.min.js"></script> --}}
        {{-- <link href="https://cdn.jsdelivr.net/npm/swiper@11.1.14/swiper-bundle.min.css" rel="stylesheet"> --}}
        <script src="{{ asset('sneat/assets/vendor/libs/swiper-11.1.4/swiper-bundle.min.js') }}"></script>
        <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/swiper-11.1.4/swiper-bundle.min.css') }}" />

        {{-- master scripts --}}

        <!-- scripts -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/4.0.0/noframework.waypoints.min.js"></script>

        {{-- Al Jhune jQuery cdn --}}
        {{-- <script type="text/javascript" src="http://localhost/Forex/resources/plugins/jQuery/jquery-3.5.1.min.js"></script> --}}
        {{-- <script type="text/javascript" src="{{ asset('plugins/jQueryUI/jquery-ui.js') }}"></script> --}}
        <script type="text/javascript" src="{{ asset('plugins/jQuery/jquery-3.5.1.min.js') }}"></script>
        {{-- <script type="text/javascript" src="{{ asset('js/misc/swiper-bundle.min.js') }}"></script> --}}
        <script type="text/javascript" src="{{ asset('plugins/jquery-validation-1.19.5/dist/jquery.validate.js') }}"></script>
        <script type="text/javascript" src="{{ asset('plugins/jquery-validation-1.19.5/dist/additional-methods.min.js') }}"></script>
        {{-- JqueryUI scripts --}}
        <script type="text/javascript" src="{{ asset('plugins/jquery-ui-1.13.2.custom/jquery-ui.js') }}"></script>
        {{-- Zoom js --}}
        <script type="text/javascript" src="{{ asset('plugins/zoom-js/zooml.js') }}"></script>
        <script type="text/javascript" src="{{ asset('plugins/zoom-master/jquery.zoom.js') }}"></script>
        {{-- Scanner JS --}}
        {{-- <script type="text/javascript" src="{{ asset('plugins/scannerjs/scanner.js') }}"></script> --}}
        {{-- <script type="text/javascript" src="{{ asset('plugins/scannerjs/scanner.css') }}"></script> --}}
        {{-- Datatables --}}
        <script type="text/javascript" src="{{ asset('DataTables-1.13.4/js/semantic.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('DataTables-1.13.4/js/jquery.dataTables.js') }}"></script>
        <script type="text/javascript" src="{{ asset('DataTables-1.13.4/js/dataTables.semanticui.min.js') }}"></script>
        {{-- Swal --}}
        {{-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Include SheetJS (XLSX) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.mini.min.js" integrity="sha512-NDQhXrK2pOCL18FV5/Nc+ya9Vz+7o8dJV1IGRwuuYuRMFhAR0allmjWdZCSHFLDYgMvXKyN2jXlSy2JJEmq+ZA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        {{-- File saver plugin --}}
        <script type="text/javascript" src="{{ asset('plugins/FileSaver.js-master/src/FileSaver.js') }}"></script>
        {{-- Jquery Loader --}}
        <script type="text/javascript" src="{{ asset('plugins/loader-js/jquery-loading.js') }}"></script>
        {{-- JSPdf --}}
        <script type="text/javascript" src="{{ asset('plugins/jsPDF-master/dist/jspdf.umd.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('plugins/jsPDF-AutoTable-master/dist/jspdf.plugin.autotable.min.js') }}"></script>

        <!-- Include jQuery UI CSS (Default ThemeRoller) -->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        {{-- Include Flatpickr JS --}}
        <script type="text/javascript" src="{{ asset('sneat/assets/vendor/js/flatpickr.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-ui-datepicker-range/jquery-ui-datepicker-range.js"></script>
        {{-- Include Select2 JS --}}
        <script type="text/javascript" src="{{ asset('sneat/assets/vendor/libs/select2/select2.js') }}"></script>
        {{-- Include Select2 CSS --}}
        <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/select2/select2.css') }}"/>

    {{-- @endif --}}
{{-- </head> --}}
