<script>
    // Unreceive Buffer
    $(document).ready(function() {
        $('#unreceive-transfer-serial-select-all-b').click(function() {
            var sold_serials_check_stat = $(this).prop('checked');

            if (sold_serials_check_stat == true) {
                $('.unreceive-transfer-serial-select-one-b').each(function() {
                    $(this).prop('checked', true);
                });

            } else {
                $('.unreceive-transfer-serial-select-one-b').each(function() {
                    $(this).prop('checked', false);
                });
            }
        });

        $('#unreceive-selected-buffer-serials').click(function() {
            var selected_fsid = [];
            var selected_bill_amount = [];

            $('.unreceive-transfer-serial-select-one-b:checked').each(function() {
                selected_fsid.push($(this).attr('data-fsid'));
                selected_bill_amount.push($(this).attr('data-billamount'));
            });

            var parsed_unreceive_serials = selected_fsid.join(", ");
            var parsed_unreceive_bill_amnt = selected_bill_amount.join(", ");
            var buffer_transfer_no = $('#buffer-transfer-transfer-number').val();
            var transfer_forex_id = $('#transfer-forex-id').val();

            if (selected_fsid.length === 0) {
                Swal.fire({
                    icon: 'error',
                    text: 'No bill(s) selected.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else {
                $('#buff-transf-deets-security-code-modal').modal("show");
                unreceiveBufferSerials(parsed_unreceive_serials, buffer_transfer_no, transfer_forex_id, parsed_unreceive_bill_amnt);
            }
        });

        function unreceiveBufferSerials(parsed_unreceive_serials, buffer_transfer_no, transfer_forex_id, parsed_unreceive_bill_amnt) {
            $('#proceed-unreceive-buffer').click(function() {
                var user_id_array = [];
                var sec_code_array = [];
                var user_sec_onpage = $('#buff-deets-security-code').val();

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
                                var form_data = new FormData($('#unreceive-serials-form')[0]);

                                form_data.append('parsed_unreceive_serials', parsed_unreceive_serials);
                                form_data.append('parsed_unreceive_bill_amnt', parsed_unreceive_bill_amnt);
                                form_data.append('_token', "{{ csrf_token() }}");
                                form_data.append('buffer_transfer_no', buffer_transfer_no)
                                form_data.append('transfer_forex_id', transfer_forex_id)
                                form_data.append('matched_user_id', matched_user_id);

                                $.ajax({
                                    url: "{{ route('admin_transactions.buffer.revert') }}",
                                    type: "post",
                                    data: form_data,
                                    contentType: false,
                                    processData: false,
                                    cache: false,
                                    success: function(data) {
                                        setTimeout(function() {
                                            $('#container-test').fadeIn("slow");
                                            $('#container-test').css('display', 'block');

                                            var url = "{{ route('admin_transactions.buffer.buffer_transfers') }}";

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
                            });
                        }
                    }
                });
            });
        }
    });
</script>
