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

        $('#fc-form-series').on('input', function() {
            var input_val = $(this).val().replace(/\D/g, '');

            if (input_val.length > 6) {
                input_val = input_val.slice(-6);
            }

            var formatted_series = input_val.padStart(6, '0');
            $(this).val(formatted_series);
        });

        $('#button-add-branch').click(function(){
            $('#branch-maint-add-modal').modal('show');
        });

        $('#company-id').change(function() {
            $('input[name="radio-rset"]').removeAttr('disabled', 'disabled');
        });

        $('input[name="radio-rset"]').change(function() {
            $('#fc-form-series').removeAttr('disabled', 'disabled');
        });
    });

    // AJAX request for edit branch details - Window Based - Branch
    $(document).ready(function() {
        $('.button-edit-fc-form-series').click(function(){
            var FCFSID = $(this).attr('data-fcfsid');

            $.ajax({
                url: "{{ route('maintenance.form_series.edit') }}",
                method: "POST",
                data: {
                    FCFSID: FCFSID,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('.update-fc-form-series-details').html(data);
                }
            });
        });
    });

    // Add Receipt Series
    $(document).ready(function() {
        $('#r-series-add-button').click(function() {
            var current_rsets = [];
            var current_companies = [];
            var company_id = $('#company-id').val();
            var series = $('#fc-form-series').val();
            var r_set = $('input[name="radio-rset"]:checked').val();
            var user_sec_onpage = $('#add-r-series-security-code').val();

            $.ajax({
                url: "{{ route('maintenance.form_series.exisiting') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(data) {
                    var check = data.exisiting_series;

                    check.forEach(function(gar) {
                        current_rsets.push(gar.RSet);
                        current_companies.push(gar.CompanyID);
                    });

                    if (current_companies.includes(parseInt(company_id)) && current_rsets.includes(r_set)) {
                        Swal.fire({
                            text: 'Series for this company already exists.',
                            icon: 'warning',
                            showConfirmButton: true
                        }).then(() => {
                            setTimeout(function() {
                                $('#company-id').val('');
                                $('#fc-form-series').val('');
                                $('input[name="radio-rset"]').prop('checked', false);
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
                                text: 'Receipt series added!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                $('#container-test').fadeIn("slow");
                                $('#container-test').css('display', 'block');

                                var form_data = new FormData($('#add-r-series-form')[0]);
                                form_data.append('matched_user_id', matched_user_id);

                                setTimeout(() => {
                                    $.ajax({
                                        url: "{{ route('maintenance.form_series.add') }}",
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
        $('.button-delete-fc-form-series').on('click', function() {
            var FCFSID = $(this).attr('data-fcfsid');
            $('#security-code-modal').modal("show");

            deleteFCFSeries(FCFSID);
        });

        function deleteFCFSeries(FCFSID) {
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
                                text: 'FC Form series deleted!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                $('#container-test').fadeIn("slow");
                                $('#container-test').css('display', 'block');

                                setTimeout(function() {
                                    $.ajax({
                                        type: 'POST',
                                        url: "{{ route('maintenance.form_series.delete') }}",
                                        data: {
                                            _token: "{{ csrf_token() }}",
                                            FCFSID: FCFSID
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
