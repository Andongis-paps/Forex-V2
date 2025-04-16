<script>
    // Unreceive transfers
    $(document).ready(function() {
        $('#unreceive-transfer-serial-select-all').click(function() {
            var transfer_forex_check_stat = $(this).prop('checked');

            if (transfer_forex_check_stat == true) {
                $('.unreceive-transfer-serial-select-one').each(function() {
                    $(this).prop('checked', true);
                });
            } else {
                $('.unreceive-transfer-serial-select-one').each(function() {
                    $(this).prop('checked', false);
                });
            }
        });

        $('#unreceive-selected-serials').click(function() {
            var selected_fsid = [];

            $('.unreceive-transfer-serial-select-one:checked').each(function() {
                selected_fsid.push($(this).attr('data-fsid'));
            });

            if (selected_fsid.length == 0) {
                Swal.fire({
                    icon: 'error',
                    text: 'Select a serial.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else {
                var parsed_unreceive_serials = selected_fsid.join(", ");

                unreceiveSerials(parsed_unreceive_serials);

                $('#security-code-modal').modal("show");
            }
        });

        function unreceiveSerials(parsed_unreceive_serials) {
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
                                text: 'Serials Unreceived!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                $('#container-test').fadeIn("slow");
                                $('#container-test').css('display', 'block');

                                var form_data = new FormData($('#unreceive-serials-form')[0]);
                                form_data.append('parsed_unreceive_serials', parsed_unreceive_serials);

                                $.ajax({
                                    url: "{{ route('admin_transactions.receive_transfer_forex.unreceive_bills') }}",
                                    type: "post",
                                    data: form_data,
                                    contentType: false,
                                    processData: false,
                                    cache: false,
                                    success: function(data) {
                                        setTimeout(function() {
                                            var url = "{{ route('admin_transactions.receive_transfer_forex') }}";

                                            window.location.href = url;
                                        }, 500);
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
                                $('#proceed-transaction').prop('disabled', false);
                            });
                        }
                    }
                });
            });
        }
    });
 </script>
