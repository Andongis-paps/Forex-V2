{{-- Selling Transaction Table --}}
<script>
    $(document).ready(function() {
        // Selling Transaction Table
        var selling_total_curr_amount = 0;

        $('.selling-transact-details-list-table').each(function() {
            var currency_amount_selling = parseFloat($(this).find('.total-amountpaid-selling').val());

            selling_total_curr_amount += currency_amount_selling;
        });

        $('#selling-trans-amount').text(selling_total_curr_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
    });
</script>

{{-- Delete selling transaction --}}
<script>
    var global_selling_trans_id;

    $(document).ready(function(){
        var user_sec_code = $('#user-security-code-selling').val();

        $('.button-delete-selling-trans-details').on('click', function(){
            const trans_id = $(this).attr('data-sellingtransdetailsid');
            global_selling_trans_id = trans_id;
        });

        function deleteSellingTransact(trans_id) {
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
                                text: 'Selling transaction deleted!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                setTimeout(function() {
                                    $.ajax({
                                        type: 'POST',
                                        url: "{{ route('admin_transactions.admin_s_transaction.void') }}",
                                        data: {
                                            _token: "{{ csrf_token() }}",
                                            trans_id: trans_id,
                                        },
                                        success: function(response) {
                                            window.location.reload();
                                            console.log('Transaction with ID ' + trans_id + ' deleted successfully!');
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
        }

        $('.button-delete-selling-trans-details').on('click', function() {
            const trans_id = $(this).attr('data-sellingtransdetailsid');
            deleteSellingTransact(trans_id);

            $('#security-code-modal').modal("show");
        });
    });

    $(document).ready(function() {
        let remarks_field = $('#transact-remarks');
        let rate_used_field = $('#sold-currency-rate-used');

        $('#update-admin-s-transact-details').click(function() {
            $('#container-test').fadeIn("fast");
            $('#container-test').css('display', 'block');

            setTimeout(function() {
                $('#container-test').fadeOut("fast");

                $('#or-number-container-deet').toggleClass('d-none');
                $('#or-no-details-cont').toggleClass('d-none');

                $('#rset-container').toggleClass('d-none');
                $('#rset-details-cont').toggleClass('d-none');

                $('#customer-container').toggleClass('d-none');
                $('#customer-details-cont').toggleClass('d-none');

                $('#update-transction-btn').toggleClass('d-none');

                if (rate_used_field.attr('readonly')) {
                    rate_used_field.removeAttr('readonly');
                } else {
                    rate_used_field.attr('readonly', 'readonly');
                }

                if (remarks_field.attr('readonly')) {
                    remarks_field.removeAttr('readonly');
                } else {
                    remarks_field.attr('readonly', 'readonly');
                }
            }, 500);
        });

        rate_used_field.keyup(function() {
            var new_total_amnt = $(this).val() * $('#sold-currency-curr-amnt').val();

            $('input[name="true-sold-currency-total-amnt"]').val(new_total_amnt);
            $('#sold-currency-total-amnt').val(new_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
        });
    });

    $(document).ready(function() {
        var trans_id = '';

        $('#update-transction-btn').on('click', function(){
            trans_id = $('#serials-scid').val();
        });

        $('#update-transaction').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#update-admin-s-trans-security-code').val();

            $('#update-transaction').prop('disabled', true);

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
                        $('#update-transaction').prop('disabled', true);

                        var index = sec_code_array.indexOf(user_sec_onpage);
                        var matched_user_id = user_id_array[index];

                        Swal.fire({
                            title: 'Success!',
                            text: 'Details updated!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            setTimeout(function() {
                                $('#container-test').fadeIn("fast");
                                $('#container-test').css('display', 'block');

                                var form_data = new FormData($('#update-admin-selling-trans-details')[0]);
                                form_data.append('matched_user_id', matched_user_id);
                                form_data.append('trans_id', trans_id);

                                $.ajax({
                                    url: "{{ route('admin_transactions.admin_s_transaction.update') }}",
                                    type: "post",
                                    data: form_data,
                                    contentType: false,
                                    processData: false,
                                    cache: false,
                                    success: function(data) {
                                        // var route = "{{ route('branch_transactions.buying_transaction.details', ['id' => ':id']) }}";
                                        // var url = route.replace(':id', data.latest_ftdid);

                                        // window.location.href = url;
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
                        }).then(()=> {
                            $('#update-transaction').prop('disabled', false);
                        });
                    }
                }
            });
        });

        $('input[name="radio-rset"]').change(function() {
            console.log($('#sold-currency-or-number').val());

            if ($(this).val() == 'O') {
                $('#or-number-selling').prop('readonly', false).val($('#sold-currency-or-number').val());
            } else if ($(this).val() == 'B') {
                $('#or-number-selling').prop('readonly', true).val('');
            }
        });
    });
</script>
