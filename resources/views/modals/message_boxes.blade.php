@if (session('success'))
    <script>
        $(document).ready(function() {
               Swal.fire({
                    title: "{{ session('title') }}",
                    html: {!! json_encode(session('html')) !!},
                    text: "{{ session('message') }}",
                    icon: 'success',
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
               }).then(() => {
                    console.log('Alert closed');
               });
        });
    </script>
@endif

@if (session('warning'))
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                Swal.fire({
                    // title: "{{ session('title') }}",
                    html: {!! json_encode(session('html')) !!},  // Safely encode the HTML
                    text: "{{ session('message') }}",
                    icon: 'warning',
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showClass: {
                        popup: 'swal2-zoom-in'
                    },
                }).then(() => {
                    console.log('Alert closed');
                });
            }, 600)
        });
    </script>
@endif

@if (session('info'))
    <script>
        $(document).ready(function() {
               Swal.fire({
                    title: "{{ session('title') }}",
                    html: {!! json_encode(session('html')) !!},
                    text: "{{ session('message') }}",
                    icon: 'info',
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
               }).then(() => {
                    console.log('Alert closed');
               });
        });
    </script>
@endif

@if (session('failed'))
    <script>
        $(document).ready(function() {
               Swal.fire({
                    title: "{{ session('title') }}",
                    html: {!! json_encode(session('html')) !!},
                    text: "{{ session('message') }}",
                    icon: 'error',
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
               }).then(() => {
                    console.log('Alert closed');
               });
        });
    </script>
@endif

@if (session('error'))
    <script>
        $(document).ready(function() {
               Swal.fire({
                    title: "{{ session('title') }}",
                    text: "Please Contact Sinag IT Department",
                    html: {!! json_encode(session('html')) !!},
                    icon: 'error',
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
               }).then(() => {
                    console.log('Alert closed');
               });
        });
    </script>
@endif
