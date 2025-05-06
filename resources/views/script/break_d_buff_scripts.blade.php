<script>
    $(document).ready(function() {
        $('#add-breakdown').click(function() {
            $('#container-test').fadeIn("fast");
            $('#container-test').css('display', 'block');

            $.ajax({
                url: "{{ route('admin_transactions.buffer.denominations') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    currency_id: $('#currency').attr('data-currencyid')
                },
                success: function(data) {
                    show();
                    clearData();

                    $('#container-test').fadeOut("fast");

                    var curr_denom = data.currency_denom;

                    curr_denom.forEach(function(gar) {
                        denoms(gar.CurrencyID, gar.BillAmount, gar.Subtotal, gar.Multiplier, gar.ManilaRate, gar.SinagRateBuying, gar.VarianceBuying, gar.VarianceSelling);
                    });

                }
            });
        });

        var denom_array = [];

        function clearData() {
            $('#currenct_amount').empty();
            $('#currency-denom-table tbody').empty();
        }

        function show() {
            $('#save-break-d').removeClass("d-none").fadeIn(500).attr('disabled', false);
            $('#denom-t-container').removeClass("d-none").fadeIn(500);
            $('#total-amount-buying').removeClass("d-none").fadeIn(500);
            $('#currency-amount-buying').removeClass("d-none").fadeIn(500);
        }

        function denoms(CurrencyID, billAmount, Multiplier, Subtotal, ManilaRate, SinagRateBuying, VarianceBuying, VarianceSelling) {
            var table = $('#currency-denom-table');
            var new_row = $('<tr>');
            var amount_cell = $('<td class="p-1"><input type="text" class="form-control bill-amount text-center" id="bill-amount" name="bill-amount" value="' + parseFloat(billAmount).toLocaleString() + '" disabled></td>');
            var multiplier_cell = $('<td class="p-1"><input type="text" class="form-control multiplier text-center" id="multiplier" name="multiplier" placeholder="0"></td>');
            var subtotal_cell = $('<td class="p-1"><input type="text" class="form-control subtotal-cells text-end" id="subtotal" name="subtotal" value="0.00" data-subtotal="" data-sub="1" disabled></td>');
            // var sinag_buying_rate_cell = $('<td class="p-1"><input type="number" class="form-control sinag-buying-rate-cells text-end" id="sinag-buying-rate" name="sinag-buying-rate[]" value="'+ SinagRateBuying +'" data-origrate="'+ SinagRateBuying +'" readonly></td>');
            var sinag_buying_rate_cell = $('<td class="p-1"><input type="number" class="form-control sinag-buying-rate-cells text-end" id="sinag-buying-rate" name="sinag-buying-rate[]" value="0" data-origrate="'+ SinagRateBuying +'" readonly></td>');
            var total_amount_cell = $('<td hidden><input type="hidden" class="form-control total-amount-cells text-end" id="total-amount" name="total-amount" value="0.00" disabled></td>');
            var total_amount_cell_formatted = $('<td class="p-1"><input type="number" class="form-control total-amount-cells-formatted text-end" id="total-amount-formatted" name="total-amount" value="0.00" data-true-peso-amnt="" placeholder="0.00"></td>');
            var multiplier_cell_count = $('<td hidden><input type="hidden" class="form-control text-center" id="multiplier-count" name="multiplier-count" value=""></td>');
            var sinag_variance_buying = $('<td hidden><input type="hidden" class="form-control text-center" id="sinag-variance-buying" name="sinag-variance-buying[]" value="'+ VarianceBuying +'"></td>');

            new_row.append(amount_cell);
            new_row.append(multiplier_cell);
            new_row.append(subtotal_cell);
            new_row.append(sinag_buying_rate_cell);
            new_row.append(total_amount_cell);
            new_row.append(total_amount_cell_formatted);
            new_row.append(multiplier_cell_count);
            new_row.append(sinag_variance_buying);

            table.find('tbody').append(new_row);
            new_row.hide().fadeIn(250);

            multiplier_cell.find('input').on('keyup', function() {
                totalAmount();
                multiplierRestriction();

                // $('.total-amount-cells-formatted').on('input', function() {
                //     this.value = this.value.replace(/[^0-9]/g, '');
                //     if (/^[0-9]*\.?[0-9]*$/.test(this.value) && this.value.length >= 0) {
                //         $(this).css('border', '');
                //     }
                // });

                // this.value = this.value.replace(/[^0-9]/g, '');
                // if (/^[0-9]*\.?[0-9]*$/.test(this.value) && this.value.length >= 0) {
                //     $(this).css('border', '');
                // }
            });

            // sinag_buying_rate_cell.find('input').on('keyup', function() {
            //     rateChange();
            // });

            total_amount_cell_formatted.find('input').on('keyup', function() {
                pesoAmount();
            });

            table.find('tbody').append(new_row);
            new_row.hide().fadeIn(250);

            // sinag_buying_rate_cell.find('.sinag-buying-rate-cells').on('change', function() {
            //     var converted_changed_rate = parseFloat($(this).val());
            //     var converted_used_rate = parseFloat($('#rate_used').val());
            //     var currency_amount = $('#current_amount_true').val();
            //     var sub_total = $(this).closest('tr').find('.subtotal-cells').val();
            //     var new_total_amnt_change = sub_total * SinagRateBuying;

            //     if (converted_changed_rate > converted_used_rate) {
            //         Swal.fire({
            //             // title: 'Ooops!',
            //             text: "Rate change can't be higher than the Sinag Rate.",
            //             icon: 'warning',
            //             showCancelButton: false,
            //         });

            //         $(this).val(SinagRateBuying);
            //         $(this).closest('tr').find('.total-amount-cells').val(new_total_amnt_change.toFixed(2));

            //         var new_total_amount = 0;

            //         var var_buying = parseFloat($(this).val());
            //         var sub_total = $(this).closest('tr').find('.subtotal-cells').val();

            //         var sinag_rate_buying = parseFloat(sub_total) * var_buying;
            //         $(this).closest('tr').find('.total-amount-cells').val(sinag_rate_buying.toFixed(2));
            //         $(this).closest('tr').find('.total-amount-cells-formatted').val(sinag_rate_buying.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));

            //         var final_total_amount = table.find('.total-amount-cells');

            //         final_total_amount.each(function() {
            //             var get_new_total_amount = parseFloat($(this).closest('tr').find('.form-control#total-amount').val());
            //             new_total_amount += get_new_total_amount;
            //         });

            //         setTimeout(function() {
            //             $('#total_buying_amount').val(new_total_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})).text(new_total_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            //             $('#total_buying_amount_input').val(new_total_amount);
            //             $('#total_buying_amount_true').val(new_total_amount.toFixed(2)).text(new_total_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            //         }, 100);
            //     }
            // });

            denom_array.push(parseFloat(VarianceBuying));

            if (denom_array.includes(0)) {
                Swal.fire({
                    icon: 'error',
                    text: 'Denomination(s) are not yet configured, ask for assistance.',
                    customClass: {
                        popup: 'my-swal-popup',
                    },
                });

                $('#buying-transact-table-body').empty();
            }

            denom_array = [];
        }

        function totalAmount() {
            var total_amount = 0;
            var new_total_amount = 0;
            var table = $('#currency-denom-table');
            var multiplier_inputs = table.find('.subtotal-cells');
            var multip_array = [];
            var bill_amnt_array = [];
            var subtotal_array = [];
            var sinag_rate_buying_array = [];
            var sinag_variance_buying_array = [];

            multiplier_inputs.each(function() {
                var multiplier_input = $(this).closest('tr').find('.form-control#multiplier');
                var multiplier_value = multiplier_input.val();
                var bill_amount_value = parseFloat($(this).closest('tr').find('.form-control#bill-amount').val().toString().split(".")[0].replace(/,/g, ""));

                var subtotal = bill_amount_value * multiplier_value;

                var multiplier_cell_count_input = $(this).closest('tr').find('.form-control#multiplier-count');
                multiplier_cell_count_input.val(multiplier_value);
                var multip_count = $(this).closest('tr').find('.form-control#multiplier-count').val();

                var sinag_buying_rate_input = parseFloat($(this).closest('tr').find('.form-control#sinag-buying-rate').val());
                var final_total_amount = subtotal * sinag_buying_rate_input;

                var final_t_amnt = isNaN(final_total_amount) ? 0 : final_total_amount;

                // Add sinag-buying-rate value to the array
                var sinag_var_buying_input = parseFloat($(this).closest('tr').find('.form-control#sinag-variance-buying').val());

                $(this).closest('tr').find('.total-amount-cells-formatted').val(final_t_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                // $(this).closest('tr').find('.total-amount-cells-formatted').val(final_total_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                $(this).closest('tr').find('.form-control#total-amount').val(final_t_amnt.toFixed(2));

                var get_new_total_amount = parseFloat($(this).closest('tr').find('.form-control#total-amount').val());
                new_total_amount += get_new_total_amount;

                if (multip_count != '' && parseInt(multip_count) > 0) {
                    multip_array.push(parseInt(multip_count));
                    bill_amnt_array.push(bill_amount_value);
                    sinag_rate_buying_array.push(sinag_buying_rate_input);
                    sinag_variance_buying_array.push(sinag_var_buying_input);
                }

                $(this).val(subtotal.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));

                var subtotal_GET = parseFloat($(this).val().toString().split(".")[0].replace(/,/g, ""));
                total_amount += subtotal_GET;

                if (total_amount != 0) {
                    if (total_amount > $('#amount').val()) {
                        Swal.fire({
                            text: "Total bill amount should not exceed the buffer amount.",
                            icon: 'warning',
                            showCancelButton: false,
                        }).then(() => {
                            var new_sub = total_amount -= subtotal_GET;
                            var new_total_amnt = new_total_amount -= final_total_amount;

                            $(this).closest('tr').find('.form-control#multiplier').val('').attr('placeholder', "0");
                            $(this).closest('tr').find('.form-control#subtotal').val('0.00');
                            $(this).closest('tr').find('.form-control#total-amount').val(0).attr('placeholder', "0.00");
                            $(this).closest('tr').find('.form-control#total-amount-formatted').val(0).attr('placeholder', "0.00");

                            // $(this).closest('tr').find('.total-amount-cells').val("0.00");
                            // $(this).closest('tr').find('.total-amount-cells-formatted').val("0.00");

                            // $('#add-breakdown').trigger("click");
                            $('#current_amount').val(new_sub);
                            $('#current_amount_true').val(new_sub);

                            $('#total_buying_amount').val(new_total_amnt).text(new_total_amnt);
                            $('#total_buying_amount_input').val(new_total_amnt);
                            $('#total_buying_amount_true').val(new_total_amnt);

                            // $('#bill-amount-count').val('');
                            // $('#multiplier-total-count').val('');
                            // $('#subtotal-count').val('');
                            // $('#sinag-buying-rate-count').val('');
                            // $('#sinag-var-buying').val('');
                        });
                    }

                    $('.buffer-option').removeAttr('disabled');
                    $('#transaction-confirm-button').removeAttr('disabled');
                } else {
                    $('.buffer-option').attr('disabled', 'disabled');
                    $('#transaction-confirm-button').attr('disabled', 'disabled');
                }
            });

            if (multip_array.length === bill_amnt_array.length) {
                for (var i = 0; i < multip_array.length; i++) {
                    subtotal_array.push(multip_array[i] * bill_amnt_array[i]);
                }
            }

            var multip_array_parse = JSON.stringify(multip_array);
            var multip_array_converted = multip_array_parse.substring(1, multip_array_parse.length - 1);
            var bill_amnt_array_parse = JSON.stringify(bill_amnt_array);
            var subtotal_array_parse = JSON.stringify(subtotal_array);
            var sinag_rate_buying_array_parse = JSON.stringify(sinag_rate_buying_array);
            var sinag_rate_buying_array_converted = sinag_rate_buying_array_parse.substring(1, sinag_rate_buying_array_parse.length - 1);
            var sinag_variance_buying_parse = JSON.stringify(sinag_variance_buying_array);
            var sinag_variance_buying_converted = sinag_variance_buying_parse.substring(1, sinag_variance_buying_parse.length - 1);

            $('#multiplier-total-count').val(multip_array_converted);
            $('#bill-amount-count').val(bill_amnt_array);
            $('#subtotal-count').val(subtotal_array);
            $('#sinag-buying-rate-count').val(sinag_rate_buying_array_converted);
            $('#sinag-var-buying').val(sinag_variance_buying_converted);

            $('#current_amount').val(total_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})).text(total_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            $('#current_amount_true').val(total_amount.toFixed(2)).text(total_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));

            var integer_part = Math.floor(new_total_amount);
            var decim_part = new_total_amount - integer_part;

            if (decim_part < 0.25) {
                decim_part = 0;
            } else if (decim_part >= 0.25 && decim_part < 0.50) {
                decim_part = 0.25;
            } else if (decim_part >= 0.50 && decim_part < 0.75) {
                decim_part = 0.50;
            } else if (decim_part >= 0.75 && decim_part < .94) {
                decim_part = 0.75;
            }

            var rounded_total_amnt = integer_part + decim_part;
            var final_t_amnt = isNaN(rounded_total_amnt) ? 0 : rounded_total_amnt;

            $('#total_buying_amount').val(rounded_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})).text(rounded_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            $('#total_buying_amount_input').val(rounded_total_amnt);
            $('#total_buying_amount_true').val(rounded_total_amnt.toFixed(2)).text(rounded_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
        }

        function rateChange() {
            var sinag_rate_buying_array = [];
            var table = $('#currency-denom-table');
            var sinag_buying_rate_cell = table.find('.sinag-buying-rate-cells');

            // sinag_buying_rate_cell.find('.sinag-buying-rate-cells').on('change', function() {
            sinag_buying_rate_cell.each(function() {
                var new_total_amount = 0;
                var var_buying = parseFloat($(this).val());
                var sub_total = $(this).closest('tr').find('.subtotal-cells').val();
                var sinag_buying_rate_input = parseFloat($(this).closest('tr').find('.form-control#sinag-buying-rate').val());
                var original_rate = parseFloat($(this).closest('tr').find('.form-control#sinag-buying-rate').attr('data-origrate'));
                var multip_count = $(this).closest('tr').find('.form-control#multiplier-count').val();

                var sinag_rate_buying = parseFloat(sub_total) * var_buying;
                $(this).closest('tr').find('.total-amount-cells').val(sinag_rate_buying.toFixed(2));
                $(this).closest('tr').find('.total-amount-cells-formatted').val(sinag_rate_buying.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));

                var final_total_amount = table.find('.total-amount-cells');

                if (sinag_buying_rate_input > original_rate) {
                    Swal.fire({
                        text: "New rate shouldn't be higher than the current rate.",
                        icon: 'warning',
                        showCancelButton: false,
                    }).then(() => {
                        var sinag_rate_buying = parseFloat(sub_total) * original_rate;

                        $(this).closest('tr').find('.total-amount-cells').val(sinag_rate_buying.toFixed(2));
                        $(this).closest('tr').find('.total-amount-cells-formatted').val(sinag_rate_buying.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));

                        $(this).closest('tr').find('.form-control#sinag-buying-rate').val(original_rate);

                        if (multip_count != '' && parseInt(multip_count) > 0) {
                            sinag_rate_buying_array.push(original_rate);
                        }

                        final_total_amount.each(function() {
                            var get_new_total_amount = parseFloat($(this).closest('tr').find('.form-control#total-amount').val());
                            new_total_amount += get_new_total_amount;
                        });

                        var integer_part = Math.floor(new_total_amount);
                        var decim_part = new_total_amount - integer_part;

                        if (decim_part < 0.25) {
                            decim_part = 0;
                        } else if (decim_part >= 0.25 && decim_part < 0.50) {
                            decim_part = 0.25;
                        } else if (decim_part >= 0.50 && decim_part < 0.75) {
                            decim_part = 0.50;
                        } else if (decim_part >= 0.75 && decim_part < .94) {
                            decim_part = 0.75;
                        }

                        var rounded_total_amnt = integer_part + decim_part;

                        var sinag_rate_buying_array_parse = JSON.stringify(sinag_rate_buying_array);
                        var sinag_rate_buying_array_converted = sinag_rate_buying_array_parse.substring(1, sinag_rate_buying_array_parse.length - 1);
                        $('#sinag-buying-rate-count').val(sinag_rate_buying_array_converted);
                    });
                } else {
                    final_total_amount.each(function() {
                        var get_new_total_amount = parseFloat($(this).closest('tr').find('.form-control#total-amount').val());
                        new_total_amount += get_new_total_amount;
                    });

                    var integer_part = Math.floor(new_total_amount);
                    var decim_part = new_total_amount - integer_part;

                    if (decim_part < 0.25) {
                        decim_part = 0;
                    } else if (decim_part >= 0.25 && decim_part < 0.50) {
                        decim_part = 0.25;
                    } else if (decim_part >= 0.50 && decim_part < 0.75) {
                        decim_part = 0.50;
                    } else if (decim_part >= 0.75 && decim_part < .94) {
                        decim_part = 0.75;
                    }

                    var rounded_total_amnt = integer_part + decim_part;

                    $('#total_buying_amount').val(rounded_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})).text(rounded_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                    $('#total_buying_amount_input').val(rounded_total_amnt);
                    $('#total_buying_amount_true').val(rounded_total_amnt.toFixed(2)).text(rounded_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));

                    // setTimeout(function() {
                    //     $('#total_buying_amount').val(new_total_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})).text(new_total_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                    //     $('#total_buying_amount_input').val(new_total_amount);
                    //     $('#total_buying_amount_true').val(new_total_amount.toFixed(2)).text(new_total_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                    // }, 100);

                    if (multip_count != '' && parseInt(multip_count) > 0) {
                        sinag_rate_buying_array.push(sinag_buying_rate_input);
                    }

                    var sinag_rate_buying_array_parse = JSON.stringify(sinag_rate_buying_array);
                    var sinag_rate_buying_array_converted = sinag_rate_buying_array_parse.substring(1, sinag_rate_buying_array_parse.length - 1);
                    $('#sinag-buying-rate-count').val(sinag_rate_buying_array_converted);
                }
            });
        }

        function pesoAmount() {
            var sinag_rate_buying_array = [];
            var table = $('#currency-denom-table');
            var peso_amnt = table.find('.total-amount-cells-formatted');

            // sinag_buying_rate_cell.find('.sinag-buying-rate-cells').on('change', function() {
            peso_amnt.each(function() {
                var new_total_amount = 0;
                var var_buying = parseFloat($(this).val());
                var sub_total = $(this).closest('tr').find('.form-control#subtotal').val().toString().split(".")[0].replace(/,/g, "");
                // var sinag_buying_rate_input = parseFloat($(this).closest('tr').find('.form-control#sinag-buying-rate').val());
                // var original_rate = parseFloat($(this).closest('tr').find('.form-control#sinag-buying-rate').attr('data-origrate'));
                var multip_count = $(this).closest('tr').find('.form-control#multiplier-count').val();
                var peso_amnt_test = parseFloat($(this).closest('tr').find('.form-control#total-amount-formatted').val());

                var sinag_rate_buying = peso_amnt_test / parseFloat(sub_total == 0 ? 1 : sub_total);

                $(this).closest('tr').find('.total-amount-cells').val(peso_amnt_test);
                $(this).closest('tr').find('.form-control#sinag-buying-rate').val(sinag_rate_buying);

                var final_total_amount = table.find('.total-amount-cells');

                final_total_amount.each(function() {
                    var get_new_total_amount = parseFloat($(this).closest('tr').find('.form-control#total-amount').val());
                    new_total_amount += get_new_total_amount;
                });

                var integer_part = Math.floor(new_total_amount);
                var decim_part = new_total_amount - integer_part;

                if (decim_part < 0.25) {
                    decim_part = 0;
                } else if (decim_part >= 0.25 && decim_part < 0.50) {
                    decim_part = 0.25;
                } else if (decim_part >= 0.50 && decim_part < 0.75) {
                    decim_part = 0.50;
                } else if (decim_part >= 0.75 && decim_part < .94) {
                    decim_part = 0.75;
                }

                var rounded_total_amnt = integer_part + decim_part;
                var final_t_amnt = isNaN(rounded_total_amnt) ? 0 : rounded_total_amnt;

                $('#total_buying_amount').val(final_t_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})).text(final_t_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                $('#total_buying_amount_input').val(final_t_amnt);
                $('#total_buying_amount_true').val(final_t_amnt.toFixed(2)).text(final_t_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));

                if (multip_count != '' && parseInt(multip_count) > 0) {
                    sinag_rate_buying_array.push(sinag_rate_buying);
                }

                var sinag_rate_buying_array_parse = JSON.stringify(sinag_rate_buying_array);
                var sinag_rate_buying_array_converted = sinag_rate_buying_array_parse.substring(1, sinag_rate_buying_array_parse.length - 1);
                $('#sinag-buying-rate-count').val(sinag_rate_buying_array_converted);
            });
        }

        function multiplierRestriction() {
            $('.multiplier').on('input', function() {
                $(this).val($(this).val().replace(/^(0\d+)|[^0-9]/g, ''));
            });
        }

        $('#save-break-d').click(function() {
            var multip_array = [];
            var total_amnt_array = [];

            var table = $('#currency-denom-table');
            var multiplier_inputs = table.find('.multiplier');

            multiplier_inputs.each(function() {
                var multiplier_val = $(this).val();
                var multip_field = $(this).closest('tr').find('.multiplier');
                var total_amount_field = $(this).closest('tr').find('.total-amount-cells-formatted');
                var total_amount_val = total_amount_field.val();

                if (multiplier_val != '' && multiplier_val >= 0) {
                    multip_array.push(multiplier_val);

                    if (total_amount_val != '' && total_amount_val != 0 && total_amount_val != '0.00') {
                        total_amnt_array.push(total_amount_val);
                        // total_amount_field.css('border', '1px solid #D9DEE3');
                    } else {
                        total_amount_field.css('border', '');
                        total_amount_field.css('border', '1px solid red');
                        total_amount_field.focus();
                    }
                } else if (total_amount_val != '' && total_amount_val != 0 && total_amount_val != '0.00') {
                    multip_field.css('border', '');
                    multip_field.css('border', '1px solid red');
                    multip_field.focus();
                }
            });

            if (multip_array.length != total_amnt_array.length) {
                Swal.fire({
                    icon: 'error',
                    text: 'Total amount field is required.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else if (multip_array.length == 0 && total_amnt_array.length == 0) {
                Swal.fire({
                    icon: 'error',
                    text: 'All fields are required.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else if (parseFloat($('#current_amount_true').val()) < parseFloat($('#amount').val())) {
                Swal.fire({
                    icon: 'error',
                    text: 'Currency amount must be exact with the buffer amount.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else {
                $('#security-code-modal').modal("show");
            }
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

                    if (sec_code_array.includes(user_sec_onpage)) {
                        $('#proceed-transaction').prop('disabled', true);

                        var index = sec_code_array.indexOf(user_sec_onpage);
                        var matched_user_id = user_id_array[index];

                        Swal.fire({
                            title: 'Success!',
                            text: 'Buffer breakdown added!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            var form_data = new FormData($('#buff-breakdown-form')[0]);
                            form_data.append('matched_user_id', matched_user_id);
                            form_data.append('buff_id', $('#buff-id').val());

                            $.ajax({
                                url: "{{ route('admin_transactions.buffer.save_break_d') }}",
                                type: "post",
                                data: form_data,
                                contentType: false,
                                processData: false,
                                cache: false,
                                success: function(data) {
                                    var route = "{{ route('admin_transactions.buffer.buffer_financing') }}";
                                    var url = route.replace(':id', data.latest_aftdid);

                                    window.location.href = route;
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

        // $('#proceed-transaction').click(function() {
        //     var user_id_array = [];
        //     var sec_code_array = [];
        //     var user_sec_onpage = $('#security-code').val();

        //     $('#proceed-transaction').prop('disabled', true);

        //     $.ajax({
        //         url: "{{ route('user_info') }}",
        //         type: "GET",
        //         data: {
        //             _token: "{{ csrf_token() }}",
        //         },
        //         success: function(get_user_info) {
        //             var user_info = get_user_info.security_codes;

        //             user_info.forEach(function(gar) {
        //                 sec_code_array.push(gar.SecurityCode);
        //                 user_id_array.push(gar.UserID);
        //             });

        //             var index = sec_code_array.indexOf(user_sec_onpage);
        //             var matched_user_id = user_id_array[index];

        //             if (sec_code_array.includes(user_sec_onpage)) {
        //                 $('#proceed-transaction').prop('disabled', true);

        //                 Swal.fire({
        //                     title: 'Success',
        //                     text: 'Buffer amount added!',
        //                     icon: 'success',
        //                     timer: 900,
        //                     showConfirmButton: false
        //                 }).then(() => {
        //                     var form_data = new FormData($('#add-buffer-form')[0]);

        //                     form_data.append('matched_user_id', matched_user_id);

        //                     $.ajax({
        //                         url: "{{ route('admin_transactions.buffer.save_financing') }}",
        //                         type: "POST",
        //                         data: form_data,
        //                         contentType: false,
        //                         processData: false,
        //                         cache: false,
        //                         success: function(data) {
        //                             $('#container-test').fadeIn("slow");
        //                             $('#container-test').css('display', 'block');

        //                             // BranchPrompt('Admin has processed your stocks as buffer. You are required to reload the page.', branch_id);

        //                             setTimeout(function() {
        //                                 window.location.reload();

        //                                 $('#container-test').fadeIn("slow");
        //                                 $('#container-test').css('display', 'block');
        //                             }, 500);
        //                         }
        //                     });
        //                 });
        //             } else {
        //                 Swal.fire({
        //                     icon: 'error',
        //                     text: 'Invalid or mismatched security code.',
        //                     customClass: {
        //                         popup: 'my-swal-popup',
        //                     }
        //                 }).then(() => {
        //                     $('#proceed-transaction').prop('disabled', false);
        //                 });
        //             }
        //         }
        //     });
        // });
    });
</script>
