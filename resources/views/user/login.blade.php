{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> --}}
    @include('template.meta')
    @include('modals.toast')

    <div class="authentication-wrapper authentication-cover d-flex justify-content-center align-items-center">
        <div class="authentication-inner row m-0 d-flex justify-content-center" id="components-container">
            {{-- <div class="d-lg-flex col-lg-6 col-md-6 align-items-center">
                <div class="w-100 d-flex justify-content-center">
                    <img src="{{ asset('assets/img/login_banner.png') }}" class="img-fluid" alt="Login image" width="1000">
                </div>
            </div> --}}

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

                    <form id="goldlogin" class="mt-5 mb-3 fv-plugins-bootstrap5 fv-plugins-framework" action="{{ route('authenticate') }}" method="POST" novalidate="novalidate">
                        @csrf
                        <div class="mb-3 ">
                            <label for="email" class="form-label">Username</label>
                            <input type="text" class="form-control focus timeout" id="email" name="username"
                                placeholder="Enter username" autofocus="" autocomplete="off" required>
                        </div>
                        <div class="mb-3 form-password-toggle fv-plugins-icon-container">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">Password</label>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control password timeout" name="password"
                                    placeholder="••••••••" aria-describedby="password" autocomplete="off" required>
                                {{-- <span class="input-group-text cursor-pointer" id="togglePassword"><i
                                        class="bx bx-hide" id="pass-eye-admin-pass"></i></span> --}}
                            </div>
                        </div>
                        <div class="divider"></div>
                        <div class="col-lg-12 mb-lg-2">
                            <small class="text-danger"></small>
                        </div>
                        <button class="btn btn-primary d-grid w-100 text-white" id="login-btn">
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
    {{-- @include('script.scripts') --}}

    <script>
        $(document).ready(function() {
            // focus in input on load.
            $(".focus").focus();

            // Capital all letters for the input type text, search and textarea
            $(document).on('input', 'input[type="text"]:not([name="password"]), input[type="search"], textarea', function() {
                var $this = $(this);
                var cursorPosition = this.selectionStart;

                // Convert the input to uppercase
                $this.val($this.val().toUpperCase());

                // Restore the cursor position
                this.setSelectionRange(cursorPosition, cursorPosition);
            });

            // Set a timer to fade out the element after a specific time
            setTimeout(function() {
                $(".toast-custom-gold-fade").fadeOut("slow"); // Fades out the element slowly
            }, 3000); // 3000 milliseconds = 3 seconds
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
    {{-- </div>
</html> --}}
