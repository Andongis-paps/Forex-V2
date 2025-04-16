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
        $('.button-edit-selling-limit').click(function(){
            var SLID = $(this).attr('data-slid');

            $.ajax({
                url: "{{ route('maintenance.bulk_limit.edit') }}",
                method: "POST",
                data: {
                    SLID: SLID,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('.update-selling-limit-details').html(data);
                }
            });
        });
    });

    $(document).ready(function() {
        $('input[name="selling_limit"]').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value.length > 11) {
                this.value = this.value.slice(0, 11);
            }
        });

        $('#selling-limit-add-button').click(function() {
            var current_limits = [];
            var company_id = $('#company-id').val();
            var selling_limit = $('#selling-limit').val();
            var user_sec_onpage = $('#add-selling-limit-security-code').val();

            $.ajax({
                url: "{{ route('maintenance.bulk_limit.exisiting') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(data) {
                    var check = data.exisiting_limits;

                    check.forEach(function(gar) {
                        current_limits.push(gar.CompanyID);
                    });

                    if (current_limits.includes(parseInt(company_id))) {
                        Swal.fire({
                            text: 'Limit for this company already exists.',
                            icon: 'warning',
                            showConfirmButton: true
                        }).then(() => {
                            setTimeout(function() {
                                $('#company-id').val('');
                                $('#selling-limit').val('');
                            }, 200);
                        });
                    } else if (selling_limit == '') {
                        Swal.fire({
                            text: 'All fields are required.',
                            icon: 'warning',
                            showConfirmButton: true
                        });
                    } else {
                        if (sec_code_array.includes(user_sec_onpage)) {
                            $('#selling-limit-add-button').prop('disabled', true);

                            var index = sec_code_array.indexOf(user_sec_onpage);
                            var matched_user_id = user_id_array[index];

                            Swal.fire({
                                title: 'Success!',
                                text: 'Selling limit added!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                $('#container-test').fadeIn("slow");
                                $('#container-test').css('display', 'block');

                                var form_data = new FormData($('#add-selling-limit-form')[0]);
                                form_data.append('matched_user_id', matched_user_id);

                                setTimeout(() => {
                                    $.ajax({
                                        url: "{{ route('maintenance.bulk_limit.add') }}",
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
                            });
                        }
                    }
                }
            });
        });
    });

    $(document).ready(function(){
        $('.button-delete-selling-limit').on('click', function() {
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
                                        url: "{{ route('maintenance.bulk_limit.delete') }}",
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
