{{-- Redirect - After successful adding of serials --}}
<script>
    $(document).ready(function() {
        $('.serials-input').on('input', function() {
            this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');

            if (this.value.length > 12) {
                this.value = this.value.slice(0, 12);
            }

            if (/^([a-zA-Z0-9]*)$/.test(this.value) && this.value.length >= 6) {
                $(this).css('border', '');
            }

            if ($(this).val() == '') {
                $(this).css('border', '1px solid #D9DEE3');
            }
        });

        var saving_success_serial = $('#success-message-saving-pending').attr('data-successpending');

        function refreshPage(delay) {
            setTimeout(function() {
                var pending_serial_url = $('#forex-serial-url').attr('data-forexserials');
                window.location.href = pending_serial_url;
            }, delay);
        }

        $('#submit-peding-serials').click(function() {
            var input = $('[name^="serials"]');
            var serials = [];
            var serials_fsids = [];

            var input_fields_val = input.map(function() {
                return $(this).val();
            }).get();

            $('.serials-input').each(function() {
                if ($(this).val() != '') {
                    serials.push($(this).val());
                }
            });

            function duplicateSerials(input_fields_val) {
                var serial_array_values = {};

                for (var i = 0; i < input_fields_val.length; i++) {
                    var index_value = input_fields_val[i];

                    if (serial_array_values.hasOwnProperty(index_value) && index_value !== '') {
                        return true;
                    }

                    serial_array_values[index_value] = true;
                }
                return false;
            }

            var array_data = input_fields_val.some(function(field_vlues) {
                return field_vlues !== '';
            });

            if (duplicateSerials(input_fields_val) == true) {
                Swal.fire({
                    icon: 'error',
                    text: 'Duplicate serials are not allowed.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });

                var seen = {};

                $('.serials-input').each(function() {
                    var value = $(this).val();

                    if (value) {
                        if (seen[value]) {
                            seen[value].push($(this));
                        } else {
                            seen[value] = [$(this)];
                        }
                    }
                });

                $.each(seen, function(key, fields) {
                    if (fields.length > 1) {
                        fields.forEach(function(field) {
                            field.css('border', '2px solid red');
                        });
                    }
                });
            } else {
                var empty_field = false;
                var serial_validity = true;
                var serial_min_length = [];

                $('.serials-input').each(function() {
                    var serial_fields = $(this).val() === '';

                    if (serial_fields) {
                        empty_field = true;
                        $(this).css('border', '1px solid #D9DEE3');
                    } else {
                        serials_fsids.push($(this).attr('data-fsid'));
                    }

                    var pattern_validity = /^([a-zA-Z0-9]*)$/.test($(this).val());

                    if (!pattern_validity) {
                        serial_validity = false;
                        $(this).css('border', '2px solid red');
                        $(this).focus();
                    }

                    if ($(this).val().length < 6 && $(this).val().length != 0) {
                        serial_min_length.push($(this).val().length);
                        $(this).css('border', '2px solid red');
                        $(this).focus();
                    }
                });

                switch (array_data) {
                    case true:
                        if (serial_validity == false) {
                            Swal.fire({
                                icon: 'error',
                                text: 'Incorrect serial format.',
                                customClass: {
                                    popup: 'my-swal-popup',
                                }
                            });

                            $('#add-pending-serials-modal').modal("hide");
                        } else if (serial_min_length.length > 0) {
                            Swal.fire({
                                icon: 'error',
                                text: 'Serial number must be at least 6 characters long.',
                                customClass: {
                                    popup: 'my-swal-popup',
                                }
                            });
                        } else {
                            var parsed_fsid = serials_fsids.join(',');
                            var parsed_serials = serials.join(',');
                            serialSubmission(parsed_fsid, parsed_serials);
                        }
                        break;
                    case false:
                        Swal.fire({
                            icon: 'error',
                            text: 'Serials are required.',
                            customClass: {
                                popup: 'my-swal-popup',
                            }
                        });
                        break;

                    default:
                        console.log("tae mo bulok");
                }
            }

            function serialSubmission(parsed_fsid, parsed_serials) {
                $('#security-code-modal').modal("show");

                var user_id_array = [];
                var sec_code_array = [];

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

                        $('#proceed-transaction').click(function() {
                            var user_sec_onpage = $('#security-code').val();

                            $('#proceed-transaction').prop('disabled', true);

                            if (sec_code_array.includes(user_sec_onpage)) {
                                $(this).prop('disabled', true);

                                var index = sec_code_array.indexOf(user_sec_onpage);
                                var matched_user_id = user_id_array[index];

                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Denominations Updated!',
                                    icon: 'success',
                                    timer: 900,
                                    showConfirmButton: false
                                }).then(() => {
                                    $('#container-test').fadeIn("slow");
                                    $('#container-test').css('display', 'block');

                                    var form_data = new FormData($('#admin-pending-serials-form')[0]);
                                    form_data.append('matched_user_id', matched_user_id);
                                    form_data.append('parsed_fsid', parsed_fsid);
                                    form_data.append('parsed_serials', parsed_serials);

                                    $.ajax({
                                        url: "{{ route('admin_transactions.admin_b_transaction.save_serials') }}",
                                        type: "post",
                                        data: form_data,
                                        contentType: false,
                                        processData: false,
                                        cache: false,
                                        success: function(data) {
                                            var url = "{{ route('admin_transactions.buying_transaction') }}";

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
                                }).then(() => {
                                    $('#proceed-transaction').prop('disabled', true);
                                });
                            }
                        });
                    }
                });
            }
        });
    });

    $(document).ready(function() {
        $('.bxs-toggle-right').hide();

        $('.enable-serial-field').change(function() {
            var checkbox = $(this);
            var checked_icon = checkbox.siblings('.bx-toggle-left');
            var unchecked_icon = checkbox.siblings('.bxs-toggle-right');

            if ($(this).is(':checked')) {
                checked_icon.hide();
                unchecked_icon.show();
                $(this).closest('.input-group-serials').find('.serials-input').removeAttr('disabled', 'disabled');
            } else {
                checked_icon.show();
                unchecked_icon.hide();
                $(this).closest('.input-group-serials').find('.serials-input').attr('disabled', true);
            }
        });
    });
</script>
