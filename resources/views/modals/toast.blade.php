
@if (session('login_failed'))
    <div class="bg-toast-custom-gold toast-custom-gold-fade">
        <div class="bs-toast toast bounceInDown show toast-custom-gold m-3 bg-label-danger-custom border" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
            <i class="bx bx-bug bx-flashing  me-2"></i>
            <div class="me-auto fw-semibold">{{ session('title') }}</div>
            </div>
            <div class="toast-body">
                {{ session('message') }}
            </div>
        </div>
    </div>
@endif

@if (session('login_success'))
    <div class="bg-toast-custom-gold toast-custom-gold-fade">
        <div class="bs-toast toast bounceInDown show toast-custom-gold m-3 bg-label-success-custom border" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
            <i class="bx bx-check-square bx-flashing  me-2"></i>
            <div class="me-auto fw-semibold">{{ session('title') }}</div>
            </div>
            <div class="toast-body">
                {{ session('message') }}
            </div>
        </div>
    </div>
@endif

@if (session('login_warning'))
    <div class="bg-toast-custom-gold toast-custom-gold-fade">
        <div class="bs-toast toast bounceInDown show toast-custom-gold m-3 bg-label-warning-custom border" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
            <i class="bx bx-error-alt bx-flashing  me-2"></i>
            <div class="me-auto fw-semibold">{{ session('title') }}</div>
            </div>
            <div class="toast-body">
                {{ session('message') }}
            </div>
        </div>
    </div>
@endif
{{--
@if (session('login_attemps') || session('attemps') >= session('attemps_limit'))
    <div class="bg-toast-custom-gold-attemps">
        <div class="bs-toast toast bounceInDown show toast-custom-gold m-3 bg-label-warning-custom border" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-progress"></div>
            <div class="toast-header">
                <i class="bx bx-error-alt bx-flashing  me-2"></i>
                <div class="me-auto fw-semibold">{{ session('title') }}</div>
            </div>
            <div class="toast-body">
                {{ session('message') }}
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            var endTime =" {{ session('end_time') }}";
            var attemps =" {{ session('attemps') }}";
            var fixedSecond = 0;

            $('#login-btn').attr('disabled', true);
            $('.timeout').attr('disabled', true);
            $('#togglePassword').addClass('disable-fisheye');

            // Function to update the time
            function updateClock() {
                var currentTime = new Date();
                var hours = currentTime.getHours();
                var minutes = currentTime.getMinutes();
                var seconds = currentTime.getSeconds();

                // Determine AM or PM suffix and convert to 12-hour format
                var meridiem = hours >= 12 ? "PM" : "AM";
                hours = (hours % 12) || 12; // Convert to 12-hour format, handle midnight

                // Add leading zeros if necessary
                hours = hours < 10 ? "0" + hours : hours;
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                // Format the current time as HH:MM:SS AM/PM
                var formattedTime = hours + ":" + minutes + ":" + seconds + " " + meridiem;


                // Function to calculate remaining seconds
                function calculateRemainingSeconds(currentTimeStr, endTimeStr) {
                    // Parse current time string
                    var currentTimeParts = currentTimeStr.split(/[: ]/);
                    var currentMeridiem = currentTimeParts.pop().toUpperCase();
                    var currentHour = parseInt(currentTimeParts[0]);
                    var currentMinute = parseInt(currentTimeParts[1]);
                    var currentSecond = parseInt(currentTimeParts[2]);
                    if (currentMeridiem === "PM" && currentHour < 12) {
                        currentHour += 12;
                    }

                    // Parse end time string
                    var endTimeParts = endTimeStr.split(/[: ]/);
                    var endMeridiem = endTimeParts.pop().toUpperCase();
                    var endHour = parseInt(endTimeParts[0]);
                    var endMinute = parseInt(endTimeParts[1]);
                    var endSecond = parseInt(endTimeParts[2]);
                    if (endMeridiem === "PM" && endHour < 12) {
                        endHour += 12;
                    }
                    // Calculate remaining time in seconds
                    var currentTimeMs = currentTime.getTime();
                    var endTimeMs = new Date().setHours(endHour, endMinute, endSecond, 0);
                    var remainingTimeMs = endTimeMs - currentTimeMs;

                    // Convert remaining milliseconds to seconds
                    var remainingSeconds = Math.floor(remainingTimeMs / 1000);

                    return remainingSeconds < 0 ? 0 : remainingSeconds;
                }


                // Get the end time from the session
                var endTime = "{{ session('end_time') }}";

                // Calculate remaining seconds
                var remainingSeconds = calculateRemainingSeconds(formattedTime, endTime);
                if(fixedSecond == 0 || localStorage.getItem("fixedSecond") === null) {
                    fixedSecond = remainingSeconds;
                    localStorage.setItem("fixedSecond", fixedSecond);
                    // alert(fixedSecond);

                } else {
                    fixedSecond = localStorage.getItem("fixedSecond");
                }

                var width = (remainingSeconds / fixedSecond) * 100;

                // Update toast message with remaining seconds
                $('.toast-header').html("<i class='bx bx-error-alt bx-flashing  me-2'></i><div class='me-auto fw-semibold'>Login failed</div>");
                $('.toast-body').html("Too many login attemps! Please try again in " + remainingSeconds + " seconds.");
                $('.toast-progress').css('width',width+'%');


                // Check if the current time matches the end time
                if (remainingSeconds <= 0) {
                    // console.log('Resetting attemps...');
                    localStorage.removeItem("fixedSecond");
                    $('#login-btn').attr('disabled', false);
                    $('.timeout').attr('disabled', false);
                    $('#togglePassword').removeClass('disable-fisheye');

                    $.ajax({
                        url:  "{{ route('resetattemp') }}",
                        type: "GET",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            // console.log(res);
                            if(res){
                                $('#login-btn').attr('disabled', false);
                                $('.timeout').attr('disabled', false);
                                $('#togglePassword').removeClass('disable-fisheye');

                                setTimeout(function() {
                                    $('.bg-toast-custom-gold-attemps').fadeOut('slow');
                                }, 3000);
                                location.reload();
                            }
                        },
                    });
                }
            }

            // Call updateClock() initially and every second thereafter
            updateClock();
            setInterval(updateClock, 1000);
        });
    </script>
@endif --}}
