    @include('template.meta')

<div class="authentication-wrapper authentication-cover d-flex justify-content-center align-items-center">
    <div class="authentication-inner row m-0 d-flex justify-content-center" id="components-container">
        <!-- Login -->
        <div class="card px-2 pt-4 w-100">
            <div class="p-3">
                <div class=" mx-auto">
                <!-- Logo -->
                <div class="app-brand d-flex justify-content-center">
                    <a href="index.html" class="app-brand-link gap-2">
                        <div class="app-brand">
                            <a href="#" class="app-brand-link">
                                <span class="app-brand-logo d-flex justify-content-center">
                                    <img src="{{ asset('images/sinag-logo-full.png') }}" alt="sinag-logo" height="75">
                                    {{-- <img src="{{ asset('assets/img/sinag-logo-full.png') }}" alt="sinag-logo" height="36" class="mt-1`"> --}}
                                </span>
                                {{-- <span class="app-brand-logo p-2">
                        </span> --}}
                            </a>
                        </div>
                    </a>
                </div>
                <!-- /Logo -->
                <div class="ribbon">
                    <span class="ribbon5 text-white project-ribbon">FOREX SYSTEM</span>
                </div>

                <br/>
                <br/>

                <form id="forexlogin" class="mt-5 mb-3 fv-plugins-bootstrap5 fv-plugins-framework" method="POST">
                    <div class="mb-3 ">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                            placeholder="Enter username" autofocus="" autocomplete="off" />
                            <span class="text-danger" id="error_username"></span>
                    </div>
                    <div class="mb-3 form-password-toggle fv-plugins-icon-container">
                        <div class="d-flex justify-content-between">
                            <label class="form-label" for="password">Password</label>
                        </div>
                        <div class="input-group input-group-merge">
                            <input type="password" id="password" class="form-control" name="password"
                                placeholder="••••••••" aria-describedby="password" autocomplete="off" />
                            {{-- <span class="input-group-text cursor-pointer" id="togglePassword"><i
                                    class="bx bx-hide" id="pass-eye-admin-pass"></i></span> --}}
                        </div>
                        <span class="text-danger" id="error_password"></span>
                    </div>
                    <div class="divider"></div>
                    <div class="col-lg-12 mb-lg-2">
                        <small class="text-danger"></small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 text-white" id="login-btn">
                        Login
                    </button>
                    <div class="alert alert-danger text-center mt-3" role="alert">
                        Please use your computer login.
                    </div>
                    <input type="hidden">

                    @include('template.footer')
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Capital all letters for the input type text, search and textarea
        $(document).on('input', 'input[name="username"]', function() {
            var $this = $(this);
            var cursorPosition = this.selectionStart;

            // Convert the input to uppercase
            $this.val($this.val().toUpperCase());

            // Restore the cursor position
            this.setSelectionRange(cursorPosition, cursorPosition);
        });

        $(document).on('submit', '#forexlogin', function(event) {
            event.preventDefault();

            const formData = $(this).serialize(); // Serialize form data
            const $btnLogin = $('#login-btn'); // Cache the login button
            const btnLoginOriginalContent = $btnLogin.html(); // Store original login button content

            $.ajax({
                url: "{{ route('authenticate') }}",
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: () => {
                    // Disable the login button and show a loading spinner
                    $btnLogin
                        .attr('disabled', true)
                        .attr('aria-disabled', true)
                        .html(`
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        `);
                },
                success: (response) => {
                    if (response.success) {
                        // Redirect to the provided URL
                        location.href = response.redirectUrl;
                    }
                },
                error: (xhr) => {
                    // Clear existing error messages
                    $('#error_username').empty();
                    $('#error_password').empty();

                    if (xhr.status === 422) {
                        // Handle validation errors
                        const errors = xhr.responseJSON.errors;

                        // Dynamically populate error messages based on field names
                        Object.keys(errors).forEach((field) => {
                            $(`#error_${field}`).text(errors[field][0]);
                        });
                    } else if (xhr.status === 401 || xhr.status === 403) {
                        // Handle unauthorized or forbidden access
                        swal.fire({
                            icon: 'error',
                            title: xhr.responseJSON.title,
                            text: xhr.responseJSON.message,
                            showClass: {
                                popup: 'swal2-zoom-in'
                            },
                        });

                        // Redirect if URL is provided
                        if (xhr.responseJSON.redirectUrl) {
                            location.href = xhr.responseJSON.redirectUrl;
                        }
                    } else {
                        // Handle unexpected errors
                        swal.fire({
                            icon: 'error',
                            title: 'Login Failed',
                            text: 'An unexpected error occurred while processing your login request.',
                            showClass: {
                                popup: 'swal2-zoom-in'
                            },
                        });
                    }
                },
                complete: () => {
                    // Re-enable the login button and restore its original content
                    $btnLogin
                        .attr('disabled', false)
                        .attr('aria-disabled', false)
                        .html(btnLoginOriginalContent);
                }
            });
        });
    });

    // $(document).ready(function() {
    //     let requestSent = false; // Flag to prevent multiple requests

    //     function updateDateTime() {
    //         const now = new Date();
    //         const options = {
    //             hour: '2-digit',
    //             minute: '2-digit',
    //             second: '2-digit',
    //             hour12: false
    //         };

    //         const time = now.toLocaleTimeString([], options);
    //         const matched_time = time <= '18:00:00';

    //         console.log(`Current Time: ${time}`);

    //         if (matched_time && !requestSent) {
    //             requestSent = true;
    //             runAjaxRequest();
    //         } else if (!matched_time) {
    //             requestSent = false;
    //         }
    //     }

    //     function runAjaxRequest() {
    //         $.ajax({
    //             url: "{{ route('test_auto_run_scheds') }}",
    //             method: "POST",
    //             data: {
    //                 _token: "{{ csrf_token() }}"
    //             },
    //             success: function(response) {
    //                 console.log('AJAX request successful:', response);
    //             },
    //             error: function(xhr, status, error) {
    //                 console.error('AJAX request failed:', error);
    //             }
    //         });
    //     }

    //     updateDateTime(); // Initial call to display date and time immediately
    //     setInterval(updateDateTime, 1000); // Update every second
    // });
</script>
