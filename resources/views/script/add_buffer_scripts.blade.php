<script>
    $(document).ready(function() {
        $('#proceed-financing').click(function() {
            $('#add-buffer-modal').modal("hide");
            $('#security-code-modal').modal("show");
        });

        $('#halt-transaction').click(function() {
            $('#add-buffer-modal').modal("show");
            $('#security-code-modal').modal("hide");
        });

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

                    var index = sec_code_array.indexOf(user_sec_onpage);
                    var matched_user_id = user_id_array[index];

                    if (sec_code_array.includes(user_sec_onpage)) {
                        $('#proceed-transaction').prop('disabled', true);

                        Swal.fire({
                            title: 'Success',
                            text: 'Buffer amount added!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            var form_data = new FormData($('#add-buffer-form')[0]);

                            form_data.append('matched_user_id', matched_user_id);

                            $.ajax({
                                url: "{{ route('admin_transactions.buffer.save_financing') }}",
                                type: "POST",
                                data: form_data,
                                contentType: false,
                                processData: false,
                                cache: false,
                                success: function(data) {
                                    $('#container-test').fadeIn("slow");
                                    $('#container-test').css('display', 'block');

                                    // BranchPrompt('Admin has processed your stocks as buffer. You are required to reload the page.', branch_id);

                                    setTimeout(function() {
                                        console.log(data.BFID);

                                        var route = "{{ route('admin_transactions.buffer.break_d_finance', ['BFID' => ':BFID']) }}";
                                        var url = route.replace(':BFID', data.BFID);

                                        window.location.href = url;

                                        $('#container-test').fadeIn("slow");
                                        $('#container-test').css('display', 'block');
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
    });
</script>
