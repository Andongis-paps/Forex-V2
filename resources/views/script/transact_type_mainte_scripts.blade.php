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

    $(document).ready(function() {
        $('.button-edit-trans-type').click(function(){
            var TTID = $(this).attr('data-ttid');

            $.ajax({
                url: "{{ route('maintenance.transaction_types.edit') }}",
                method: "POST",
                data: {
                    TTID: TTID,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('.update-trans-type-details').html(data);
                }
            });
        });
    });

    $(document).ready(function() {
        $('#transact-type-add-button').click(function() {
            var transaction_type = $('#transaction-type').val();
            var user_sec_onpage = $('#add-transact-type-security-code').val();

            $('#transact-type-add-button').prop('disabled', true);

            if (transaction_type == '') {
                Swal.fire({
                    text: 'Tag description is required.',
                    icon: 'warning',
                    showConfirmButton: true
                });
            } else {
                if (sec_code_array.includes(user_sec_onpage)) {
                    $('#transact-type-add-button').prop('disabled', true);

                    var index = sec_code_array.indexOf(user_sec_onpage);
                    var matched_user_id = user_id_array[index];

                    Swal.fire({
                        title: 'Success!',
                        text: 'Transaction type added!',
                        icon: 'success',
                        timer: 900,
                        showConfirmButton: false
                    }).then(() => {
                        $('#container-test').fadeIn("slow");
                        $('#container-test').css('display', 'block');

                        var form_data = new FormData($('#add-transact-type-form')[0]);
                        form_data.append('matched_user_id', matched_user_id);

                        setTimeout(() => {
                            $.ajax({
                                url: "{{ route('maintenance.transaction_types.add') }}",
                                type: "post",
                                data: form_data,
                                contentType: false,
                                processData: false,
                                cache: false,
                                success: function(data) {
                                    window.location.reload();
                                }
                            });
                        }, 200);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        text: 'Invalid or mismatched security code.',
                        customClass: {
                            popup: 'my-swal-popup',
                        }
                    }).then(() => {
                        $('#transact-type-add-button').prop('disabled', false);
                    });
                }
            }
        });
    });

    $(document).ready(function(){
        $('.button-delete-trans-type').on('click', function() {
            var TTID = $(this).attr('data-ttid');
            $('#security-code-modal').modal("show");

            deleteTransType(TTID);
        });

        function deleteTransType(TTID) {
            $('#proceed-transaction').click(function() {
                var user_id_array = [];
                var sec_code_array = [];
                var user_sec_onpage = $('#security-code').val();

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
                                text: 'Transaction type deleted!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                $('#container-test').fadeIn("slow");
                                $('#container-test').css('display', 'block');

                                setTimeout(function() {
                                    $.ajax({
                                        type: 'POST',
                                        url: "{{ route('maintenance.transaction_types.delete') }}",
                                        data: {
                                            _token: "{{ csrf_token() }}",
                                            TTID: TTID
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
                            });
                        }
                    }
                });
            });
        }
    });
</script>
