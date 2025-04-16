<script>
    $(document).ready(function() {
        let sessionLifetime = {{ config('session.lifetime') }} * 60 * 1000;

        var isAuthenticated = @json(Auth::check());

        // Function to reset the session timeout
        function resetSessionTimeout() {
            localStorage.setItem('lastActivity', Date.now());
        }

        // Listen for activity in the current tab
        $(document).on('mousemove keydown click scroll', resetSessionTimeout);

        // Set an interval to check for session expiratios
        setTimeout(function() {
            let lastActivity = parseInt(localStorage.getItem('lastActivity') || Date.now());
            let timeSinceLastActivity = Date.now() - lastActivity;

            if (isAuthenticated && timeSinceLastActivity >= sessionLifetime) {
                Swal.fire({
                    title: "",
                    text: "You have been logged out due to inactivity.",
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    confirmButtonColor: '#03c3ec',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('meta[name="csrf-token"]').attr('content', '{{ csrf_token() }}');
                        location.href = "{{ route('login') }}";
                    }
                });

                $.ajax({
                    url: "{{ route('logout') }}",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        console.log('You have been logged out due to inactivity.');
                    }
                });
            }
        }, sessionLifetime + 3000);
    });
</script>
