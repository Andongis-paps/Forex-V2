{{-- Window based - Receipt Series Scripts --}}
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

        fieldFormat();

        $('#button-add-branch').click(function(){
            $('#branch-maint-add-modal').modal('show');
        });

        $('#company-id').change(function() {
            $('#r-set-series').removeAttr('disabled', 'disabled');

        });
    });

    // AJAX request for edit branch details - Window Based - Branch
    $(document).ready(function() {
        $('.edit-r-set-series').click(function(){
            var RSID = $(this).attr('data-rsid');

            $.ajax({
                url: "{{ route('maintenance.r_set_series.edit') }}",
                method: "POST",
                data: {
                    RSID: RSID,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('.update-r-set-series-details').html(data);

                    // $('#r-set-series-test').on('input', function () {
                    //     var input_val = $(this).val().replace(/\D/g, '');

                    //     if (input_val.length > 6) {
                    //         input_val = input_val.slice(-6);
                    //     }

                    //     var formatted_series = input_val.padStart(6, '0');

                    //     $(this).val(formatted_series);
                    // });
                }
            });
        });
    });

    function fieldFormat() {
        $('#r-set-series').on('input', function() {
            var input_val = $(this).val().replace(/\D/g, '');

            if (input_val.length > 6) {
                input_val = input_val.slice(-6);
            }

            var formatted_series = input_val.padStart(6, '0');
            $(this).val(formatted_series);
        });
    }

    // Add Receipt Series
    $(document).ready(function() {
        $('#r-set-series-add-button').click(function() {
            var current_companies = [];
            var company_id = $('#company-id').val();
            var series = $('#r-set-series').val();
            var user_sec_onpage = $('#add-r-set-series-security-code').val();

            $.ajax({
                url: "{{ route('maintenance.r_set_series.exisisting') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(data) {
                    var check = data.exisisting_series;

                    check.forEach(function(gar) {
                        current_companies.push(gar.CompanyID);
                    });

                    if (current_companies.includes(parseInt(company_id))) {
                        Swal.fire({
                            text: 'Series for this company already exists.',
                            icon: 'warning',
                            showConfirmButton: true
                        }).then(() => {
                            setTimeout(function() {
                                $('#company-id').val('');
                                $('#r-set-series').val('');
                            }, 200);
                        });
                    } else if (series == '' || company_id == '') {
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
                                text: 'Receipt set series added!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                $('#container-test').fadeIn("slow");
                                $('#container-test').css('display', 'block');

                                var form_data = new FormData($('#add-r-set-series-form')[0]);
                                form_data.append('matched_user_id', matched_user_id);

                                setTimeout(() => {
                                    $.ajax({
                                        url: "{{ route('maintenance.r_set_series.add_r_set_series') }}",
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

    // Delete currency - Window Based - Currency
    $(document).ready(function(){
        $('.delete-r-set-series').on('click', function() {
            var RSID = $(this).attr('data-rsid');
            $('#security-code-modal').modal("show");

            deleteItem(RSID);
        });

        function deleteItem(RSID) {
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
                                text: 'Receipt set series deleted!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                $('#container-test').fadeIn("slow");
                                $('#container-test').css('display', 'block');

                                setTimeout(function() {
                                    $.ajax({
                                        type: 'POST',
                                        url: "{{ route('maintenance.r_set_series.delete') }}",
                                        data: {
                                            _token: "{{ csrf_token() }}",
                                            RSID: RSID
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
</script>
