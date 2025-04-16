{{-- DPO Transact Scripts --}}
<script>
    $(document).ready(function() {
        $('#dpoin-select-all').click(function() {
            var rate_conf_check_stat = $(this).prop('checked');

            if (rate_conf_check_stat == true) {
                $('.dpoin-select-one').each(function() {
                    $(this).prop('checked', true);
                });
            } else {
                $('.dpoin-select-one').each(function() {
                    $(this).prop('checked', false);
                });
            }
        });

        $('input[name="radio-rset"]').change(function() {
            resetTables();
            $('#petnet-selling-rate').removeAttr('disabled', 'disabled');
        });

        $('#generate-dpo-in-data').click(function() {
            $.ajax({
                url: "{{ route('admin_transactions.dpofx.DPOFXINS') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    receipt_set: $('input[name="radio-rset"]:checked').val(),
                    selling_rate: $('#petnet-selling-rate').val(),
                },
                success: function(data) {
                    var total_dollar_amnt = 0;
                    var total_gain_loss = 0;
                    var total_capital = 0;
                    var total_exchange_amnt  = 0;
                    var DPO_in_transacts = data.DPO_in_transacts;

                    if (DPO_in_transacts.length === 0) {
                        resetTables();

                        Swal.fire({
                            icon: 'warning',
                            text: 'No available DPOs.',
                        });

                        $('#remarks').attr('disabled', 'disabled');
                    } else if ($('#petnet-selling-rate').val() == '') {
                        resetTables();

                        Swal.fire({
                            icon: 'warning',
                            text: 'Selling rate is required.',
                        });

                        $('#remarks').attr('disabled', 'disabled');
                    } else {
                        resetTables();

                        $('#container-test').fadeIn(300);
                        $('#container-test').css('display', 'block');

                        setTimeout(function() {
                            DPO_in_transacts.forEach(function(gar) {
                                dpoInDetails(gar.DPOIID, gar.CompanyName, gar.MTCN, gar.DollarAmount, gar.RateUsed, gar.Amount, gar.exchange_amount, gar.gain_loss);

                                total_dollar_amnt += parseFloat(gar.DollarAmount);
                                total_gain_loss += parseFloat(gar.gain_loss);

                                total_capital += parseFloat(gar.Amount);
                                total_exchange_amnt += parseFloat(gar.exchange_amount);
                            });

                            $('#container-test').fadeOut(300);
                            $('#save-dpo-out-transact').removeAttr('disabled');
                            $('#remarks').removeAttr('disabled', 'disabled');

                            tableFooterDetails(total_dollar_amnt, total_gain_loss, total_capital, total_exchange_amnt);
                        },200);
                    }
                }
            });
        });

        function dpoInDetails(DPOIID, CompanyName, MTCN, DollarAmount, RateUsed, Amount, exchange_amount, gain_loss) {
            var gain_loss_formatted = gain_loss.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            if (gain_loss > 1) {
                gain_loss_formatted = '+' + gain_loss_formatted;
            } else if (gain_loss < 0) {
                gain_loss_formatted = '-' + gain_loss_formatted.substring(1);
            }

            var badgeColor = gain_loss >= 0 ? 'success-badge-custom' : 'danger-badge-custom';
            var icon_gain_loss = gain_loss >= 0 ? `<i class='bx bxs-up-arrow' style="font-size: .5rem;"></i>` : `<i class='bx bxs-down-arrow' style="font-size: .5rem;"></i>`;

            var table = $('#dpoin-transacts-table');
            var row = $('<tr>')
            var select_dpoin = $('<td class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap"><input class="form-check-input dpoin-select-one" type="checkbox" id="dpoin-select-one" name="dpoin-select-one" data-dpoiid="'+ DPOIID +'" checked></td>');
            var company = $('<td class="text-center text-sm p-1">'+ CompanyName +'</td>');
            var mtcn = $('<td class="text-center text-sm p-1">'+ MTCN +'</td>');
            var dollar_amnt = $('<td class="text-right text-sm py-1 px-3">'+ DollarAmount.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) +'</td>');
            var rate_used = $('<td class="text-right text-sm py-1 px-3">'+ RateUsed.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) +'</td>');
            var peso_amnt = $('<td class="text-right text-sm py-1 px-3">'+ Amount.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) +'</td>');
            var exchange_amnt = $('<td class="text-right text-sm py-1 px-3">'+ exchange_amount.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) +'</td>');
            var processed_gain_loss = $('<td class="text-right text-sm py-1 px-3"><span class="badge '+ badgeColor +'"><text>'+ gain_loss_formatted +' &nbsp; '+ icon_gain_loss +'</text></span></td>');

            row.append(select_dpoin);
            row.append(company);
            row.append(mtcn);
            row.append(dollar_amnt);
            row.append(rate_used);
            row.append(peso_amnt);
            row.append(exchange_amnt);
            row.append(processed_gain_loss);

            table.find('tbody').append(row);
        }

        function tableFooterDetails(total_dollar_amnt, total_gain_loss, total_capital, total_exchange_amnt) {
            console.log(total_gain_loss);
            // $('#total-generated-gain-loss').empty();
            var gain_loss_formatted = total_gain_loss.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            if (total_gain_loss > 1) {
                gain_loss_formatted = '+' + gain_loss_formatted;
            } else if (total_gain_loss < 0) {
                gain_loss_formatted = '-' + gain_loss_formatted.substring(1);
            }

            var badgeColor = total_gain_loss >= 0 ? 'success-badge-custom' : 'danger-badge-custom';
            var icon_gain_loss = total_gain_loss >= 0 ? `<i class='bx bxs-up-arrow' style="font-size: .5rem;"></i>` : `<i class='bx bxs-down-arrow' style="font-size: .5rem;"></i>`;

            var transfers_to_sell_total_gain_loss = $('<span class="badge '+ badgeColor +'">'+ gain_loss_formatted +' &nbsp; '+ icon_gain_loss +'</span>');
            var transfers_to_sell_total_ex_ex_r = $('<strong>' + total_exchange_amnt.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong>');

            $('#total-dpofx-amount').text(total_dollar_amnt.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#total-peso-amount').text(total_capital.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#total-exhc-amount').text(total_exchange_amnt.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#total-gain-loss').html(transfers_to_sell_total_gain_loss);

            $('#true-total-dpofx-amnt').val(total_dollar_amnt);
            $('#true-total-peso-amount').val(total_capital);
            $('#true-total-exhc-amount').val(total_exchange_amnt);
            $('#true-total-gain-loss').val(total_gain_loss);
        }

        function resetTables() {
            $('#buying-transact-banner').hide();
            $('#dpoin-transacts-table tbody').empty();

            $('#total-dpofx-amount').text('0.00');
            $('#total-exhc-amount').text('0.00');
            $('#total-peso-amount').text('0.00');
            $('#total-gain-loss').html('');

            $('#true-total-dpofx-amnt').val(0);
            $('#true-total-peso-amount').val(0);
            $('#true-total-exhc-amount').val(0);
            $('#true-total-gain-loss').val(0);
        }

        $('#save-dpo-out-transact').click(function() {
            $('#security-code-modal').modal("show");
        });

        $('#proceed-transaction').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var selected_dpoins = [];
            var user_sec_onpage = $('#security-code').val();

            $('#proceed-transaction').prop('disabled', true);

            $('.dpoin-select-one').each(function() {
                var selected = $(this).prop('checked') == true;

                if (selected) {
                    selected_dpoins.push($(this).attr('data-dpoiid'));
                }
            });

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
                            text: 'DPO Out Added!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            var form_data = new FormData($('#dpofx-out-form')[0]);
                            form_data.append('matched_user_id', matched_user_id);
                            form_data.append('selected_dpoins', selected_dpoins.join(","));
                            form_data.append('dollar_amnt', $('#true-total-dpofx-amnt').val());
                            form_data.append('selling_rate', $('#petnet-selling-rate').val());
                            form_data.append('exch_amnt', $('#true-total-exhc-amount').val());
                            form_data.append('gain_loss', $('#true-total-gain-loss').val());
                            form_data.append('amount', $('#true-total-peso-amount').val());
                            form_data.append('recept_set', $('input[name="radio-rset"]:checked').val())

                            $.ajax({
                                url: "{{ route('admin_transactions.dpofx.save_dpo_out') }}",
                                type: "post",
                                data: form_data,
                                contentType: false,
                                processData: false,
                                cache: false,
                                success: function(data) {
                                    var url = "{{ route('admin_transactions.dpofx.dpo_out') }}";

                                    window.location.href = url;
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
