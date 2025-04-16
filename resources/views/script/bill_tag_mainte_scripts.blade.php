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
    });

    // AJAX request for edit branch details - Window Based - Branch
    $(document).ready(function() {
        $('.button-edit-bill-tag-series').click(function(){
            var BillStatID = $(this).attr('data-billstatid');

            console.log(BillStatID);

            $.ajax({
                url: "{{ route('maintenance.bill_tags.edit') }}",
                method: "POST",
                data: {
                    BillStatID: BillStatID,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('.update-bill-tag-details').html(data);
                }
            });
        });
    });

    // Add Receipt Series
    $(document).ready(function() {
        $('#bill-tag-add-button').click(function() {
            var tag_description = $('#tag-description').val();
            var user_sec_onpage = $('#add-bill-tag-security-code').val();

            if (tag_description == '') {
                Swal.fire({
                    text: 'Tag description is required.',
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

                        var form_data = new FormData($('#add-bill-tag-form')[0]);
                        form_data.append('matched_user_id', matched_user_id);

                        setTimeout(() => {
                            $.ajax({
                                url: "{{ route('maintenance.bill_tags.add') }}",
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
        });
    });

    // Delete currency - Window Based - Currency
    $(document).ready(function(){
        $('.button-delete-fc-form-series').on('click', function() {
            var BillStatID = $(this).attr('data-billstatid');
            $('#security-code-modal').modal("show");

            deleteBillTag(BillStatID);
        });

        function deleteBillTag(BillStatID) {
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
                                text: 'Bill tag deleted!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                $('#container-test').fadeIn("slow");
                                $('#container-test').css('display', 'block');

                                setTimeout(function() {
                                    $.ajax({
                                        type: 'POST',
                                        url: "{{ route('maintenance.bill_tags.delete') }}",
                                        data: {
                                            _token: "{{ csrf_token() }}",
                                            BillStatID: BillStatID
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
