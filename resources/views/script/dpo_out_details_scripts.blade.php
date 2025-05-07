{{-- DPO Transact Scripts --}}
<script>
    $(document).ready(function() {
        let remarks_field = $('textarea[name="remarks"]');
        let rate_used_field = $('input[name="selling-rate"]');

        $('#update-dpo-out-details').click(function() {
            $('#container-test').fadeIn("fast");
            $('#container-test').css('display', 'block');

            setTimeout(function() {
                $('#container-test').fadeOut("fast");

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
            var new_total_amnt = $(this).val() * $('input[name="dollar-amount"]').val();
            var actual_principal = $('input[name="principal"]').attr('data-actualprincipal');
            var new_gain_loss = new_total_amnt - actual_principal;

            var formatted_gain_loss = '';

            if (new_gain_loss >= 0) {
                formatted_gain_loss = '+ ' + new_gain_loss.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ▲';
                $('input[name="transact-total-gain-loss"]').removeClass('danger-badge-custom').addClass('success-badge-custom');
            } else {
                formatted_gain_loss = '- ' + Math.abs(new_gain_loss).toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ▼';
                $('input[name="transact-total-gain-loss"]').removeClass('success-badge-custom').addClass('danger-badge-custom');
            }

            $('input[name="true-exchange-amount"]').val(new_total_amnt);
            $('input[name="exchange-amount"]').val(new_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));

            $('input[name="true-total-gain-loss"]').val(new_gain_loss);
            $('input[name="transact-total-gain-loss"]').val(formatted_gain_loss);
        });
    });

    $(document).ready(function() {
        var trans_id = '';

        $('#update-transction-btn').on('click', function(){
            trans_id = $('#DPODOID').val();
        });

        $('#update-transaction').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#update-dpo-out-security-code').val();

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

                                var form_data = new FormData($('#update-dpo-out-details-form')[0]);
                                form_data.append('matched_user_id', matched_user_id);
                                form_data.append('trans_id', trans_id);

                                $.ajax({
                                    url: "{{ route('admin_transactions.dpofx.update') }}",
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

        // $('input[name="radio-rset"]').change(function() {
        //     console.log($('#sold-currency-or-number').val());

        //     if ($(this).val() == 'O') {
        //         $('#or-number-selling').prop('readonly', false).val($('#sold-currency-or-number').val());
        //     } else if ($(this).val() == 'B') {
        //         $('#or-number-selling').prop('readonly', true).val('');
        //     }
        // });
    });
</script>
