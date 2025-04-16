<script>
    var user_id_array = [];
    var sec_code_array = [];

    $(document).ready(function() {
        $.ajax({
            url: "{{ route('user_info') }}",
            type: "GET",
            data: {
                _token: "{{ csrf_token() }}",
            },
            success: function(get_user_info) {
                var user_info = get_user_info.security_codes;

                user_info.forEach(function(gar) {
                    sec_code_array.push(gar.SecurityCode);
                    user_id_array.push(gar.UserID);
                });
            }
        });
    });

    // AJAX request for add new currency - Window Based - Currency
    $(document).ready(function(){
        $('#button-add-currency').click(function(){
            $('#currency-maint-add-modal').modal('show');
        });
    });

    // AJAX request for edit currency details - Window Based - Currency
    $(document).ready(function(){
        $('.button-edit-currency').click(function(){
            var CurrencyID = $(this).attr('data-currencymaintid');

            $.ajax({
                url: "{{ route('maintenance.currency_maintenance.edit') }}",
                method: "POST",
                data: {
                    CurrencyID: CurrencyID,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('.update-currency-details').html(data);
                }
            });
        });
    });

    // Delete currency - Window Based - Currency
    $(document).ready(function(){
        $('.button-delete-currency').on('click', function() {
            var currency_id = $(this).attr('data-currencyid');
            $('#security-code-modal').modal("show");

            deleteCurrency(currency_id);
        });

        function deleteCurrency(currency_id) {
            $('#proceed-transaction').click(function() {
                var user_id_array = [];
                var sec_code_array = [];
                var user_sec_onpage = $('#security-code').val();

                $('#proceed-transaction').prop('disabled', true);

                $.ajax({
                    url: "{{ route('user_info') }}",
                    type: "GET",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(get_user_info) {
                        var user_info = get_user_info.security_codes;

                        user_info.forEach(function(gar) {
                            sec_code_array.push(gar.SecurityCode);
                            user_id_array.push(gar.UserID);
                        });

                        if (sec_code_array.includes(user_sec_onpage)) {
                            $('#proceed-transaction').prop('disabled', true);

                            var index = sec_code_array.indexOf(user_sec_onpage);
                            var matched_user_id = user_id_array[index];

                            Swal.fire({
                                title: 'Success!',
                                text: 'Currency successfully deleted!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                $('#container-test').fadeIn("slow");
                                $('#container-test').css('display', 'block');

                                setTimeout(function() {
                                    $.ajax({
                                        type: 'POST',
                                        url: "{{ route('maintenance.currency_maintenance.delete') }}",
                                        data: {
                                            _token: "{{ csrf_token() }}",
                                            currency_id: currency_id
                                        },
                                        success: function(response) {
                                            window.location.reload();
                                        }
                                    });
                                }, 1000);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                text: 'Invalid or mismatched security code.',
                                customClass: {
                                    popup: 'my-swal-popup',
                                }
                            }).then(() => {
                                $('#proceed-transaction').prop('disabled', false);
                            });
                        }
                    }
                });
            });
        }
    });

    // Currency Maintenance - duplicate prevention and validations
    $(document).ready(function() {
        $('#currency-add-button').click(function() {
            var _token = $('input[name="_token"]').val();
            var new_curr_name = $('#currency-name').val();
            var country_origin = $('#currency-country-origin').val();
            var curr_sign = $('#currency-sign').val();
            var curr_abbrv = $('#currency-abbrev').val();
            var rib_variance = $('#rib-variance').val();
            var serial_status = $('#select-serial').val();
            var user_sec_onpage = $('#add-curr-security-code').val();
            var with_set_o = $('input[name="switch-r-set-o"]').is(':checked') ? 1 : 0;
            var with_set_b = $('input[name="switch-r-set-b"]').is(':checked') ? 1 : 0;
            // var with_set_o = $('input[name="receipt-set-o"]').is(':checked') ? 1 : 0;
            // var with_set_b = $('input[name="receipt-set-b"]').is(':checked') ? 1 : 0;

            var curr_array = [];

            $.ajax({
                url: "{{ route('maintenance.currency_maintenance.existing') }}",
                type: "POST",
                data: {
                    _token: _token,
                },
                success: function(data) {
                    var check = data.exisiting_curr;

                    check.forEach(function(curr_details) {
                        curr_array.push(curr_details.Currency);
                    });

                    if (curr_array.includes(new_curr_name)) {
                        Swal.fire({
                            title: 'Duplicate entry!',
                            text: 'Duplicate currency is not allowed.',
                            icon: 'warning',
                            showConfirmButton: true
                        }).then(() => {
                            setTimeout(function() {
                                $('#currency-name').val('');
                            }, 200);
                        });
                    } else if (new_curr_name == '' || country_origin == '' || curr_sign == '' || curr_abbrv == '' || serial_status == '' || rib_variance == '') {
                        Swal.fire({
                            text: 'All fields are required.',
                            icon: 'warning',
                            showConfirmButton: true
                        });
                    } else {
                        if (sec_code_array.includes(user_sec_onpage)) {
                            $('#proceed-transaction').prop('disabled', true);

                            var index = sec_code_array.indexOf(user_sec_onpage);
                            var matched_user_id = user_id_array[index];

                            Swal.fire({
                                title: 'Success!',
                                text: 'Currency successfully added!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                $('#container-test').fadeIn("slow");
                                $('#container-test').css('display', 'block');

                                var form_data = new FormData($('#add-new-currency-form')[0]);
                                form_data.append('matched_user_id', matched_user_id);
                                form_data.append('for_set_o', with_set_o);
                                form_data.append('for_set_b', with_set_b);

                                $.ajax({
                                    url: "{{ route('maintenance.currency_maintenance.add') }}",
                                    type: "post",
                                    data: form_data,
                                    contentType: false,
                                    processData: false,
                                    cache: false,
                                    success: function(data) {
                                        var currency_id = data.currency_id;

                                        var route = "{{ route('maintenance.currency_maintenance.edit_denom', ['currency_id' => ':currency_id']) }}";
                                        var url = route.replace(':currency_id', currency_id);

                                        window.location.href = url;
                                    }
                                });
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                text: 'Invalid or mismatched security code.',
                                customClass: {
                                    popup: 'my-swal-popup',
                                }
                            });
                        }
                    }
                }
            });
        });
    });
</script>
