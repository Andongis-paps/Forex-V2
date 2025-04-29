{{-- DPOFX Rate Maintenance scripts --}}
<script>
    $(document).ready(function() {
        $('#dpofx-rate').change(function() {
            $('#dpofx-rate-select-all').removeAttr('disabled', 'disabled');

            $('.dpofx-rate-select-one').each(function() {
                $(this).removeAttr('disabled', 'disabled');
            });

            $('#update-dpo-rate-button').removeAttr('disabled', 'disabled');
        });

        $('#dpofx-rate-select-all').click(function() {
            var transfer_forex_check_stat = $(this).prop('checked');

            if (transfer_forex_check_stat == true) {
                $('.dpofx-rate-select-one').each(function() {
                    $(this).prop('checked', true);
                });
            } else {
                $('.dpofx-rate-select-one').each(function() {
                    $(this).prop('checked', false);
                });
            }
        });

        $('#update-dpo-rate-button').click(function(){
            var selected_branch_id = [];
            var dpofx_rate = $('#dpofx-rate').val();

            $('.dpofx-rate-select-one:checked').each(function() {
                selected_branch_id.push($(this).attr('data-branchid'));
            });

            var parsed_branch = selected_branch_id.join(', ');

            $('#selected-branches').val(parsed_branch);

            if (selected_branch_id == '') {
                Swal.fire({
                    icon: 'error',
                    text: 'Select a branch.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else if (dpofx_rate  == '') {
                Swal.fire({
                    icon: 'error',
                    text: 'Enter a DPOFX rate.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else {
                $('#security-code-modal').modal("show");
                $('#dpo-rate-maint-update-modal').modal("hide");
            }
        });
    });

    $(document).ready(function() {
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
                            text: 'DPOFX Rate updated!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            var form_data = new FormData($('#update-dpo-rate-form')[0]);

                            $.ajax({
                                url: "{{ route('maintenance.rate_maintenance.update_dpo_rate') }}",
                                type: "post",
                                data: form_data,
                                contentType: false,
                                processData: false,
                                cache: false,
                                success: function(data) {
                                    window.location.reload();
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
    });
</script>
