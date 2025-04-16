<script>
    $(document).ready(function() {
        $('.selling-rate').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');

            // if ((this.value.match(/\./g) || []).length > 1) {
            //     this.value = this.value.substring(0, this.value.lastIndexOf('.'));
            // }

            // if (this.value.length > 11) {
            //     this.value = this.value.slice(0, 11);
            // }

            if (/^[0-9]*\.?[0-9]*$/.test(this.value) && this.value.length >= 0) {
                $(this).css('border', '');
            }
        });

        $('#bills-to-sell-select-all').click(function() {
            var rate_conf_check_stat = $(this).prop('checked');

            if (rate_conf_check_stat == true) {
                $('.bills-to-sell-select-one').each(function() {
                    $(this).prop('checked', true);
                });
            } else {
                $('.bills-to-sell-select-one').each(function() {
                    $(this).prop('checked', false);
                });
            }
        });

        $('#consolidate-bills-select-all').click(function() {
            var rate_conf_check_stat = $(this).prop('checked');

            if (rate_conf_check_stat == true) {
                $('.consolidate-bills-select-one').each(function() {
                    if (!$(this).prop('checked') && !$(this).prop('disabled')) {
                        $(this).prop('checked', true);
                    }
                });
            } else {
                $('.consolidate-bills-select-one').each(function() {
                    if ($(this).prop('checked') && !$(this).prop('disabled')) {
                        $(this).prop('checked', false);
                    }
                });
            }
        });

        $('#transact-date-transfers-to-sell').datepicker({
            minDate: 0,
            maxDate: 0,
            dateFormat: "yy-mm-dd"
        });

        var current_date = new Date();
        var year = current_date.getFullYear();
        var month = String(current_date.getMonth() + 1).padStart(2, '0');
        var day = String(current_date.getDate()).padStart(2, '0');

        var formatted_date = year + '-' + month + '-' + day;

        var set_date = $('#transact-date-transfers-to-sell').val(formatted_date).text(formatted_date);

        let selected_value = $('input[name="radio-transact-type"]:checked').val();

        if (selected_value) {
            $('#container-test').fadeIn(300);
            $('#container-test').css('display', 'block');

            currencies(selected_value);
        }

        $('.radio-button').on('click', function() {
            $('#currency-tables tbody').empty();

            $('#container-test').fadeIn(300);
            $('#container-test').css('display', 'block');

            let selected_value = '';
            let clicked_button = $(this);
            let button_value = clicked_button.val();


            if (clicked_button.attr('name') === 'radio-transact-type') {
                $('input[name="radio-transact-type"]:checked').each(function() {
                    selected_value = $(this).val();
                });
            } else if (clicked_button.attr('name') === 'radio-transact-type-buffer') {
                $('input[name="radio-transact-type-buffer"]:checked').each(function() {
                    selected_value = $(this).val();
                });
            }

            currencies(selected_value);
        });

        function currencies(selected_value) {
            $.ajax({
                url: "{{ route('admin_transactions.bulk_selling.currencies') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    selected_value: selected_value
                },
                success: function(data) {
                    var results = data.currencies;
                    $('#container-test').fadeOut(300);

                    results.forEach(function(poy) {
                        table(poy.CRID, poy.CurrAbbv, poy.Currency, poy.CurrencyID, poy.MaxEntryDateTime, poy.Rate[0]);
                    });
                }
            });

            function table(CRID, CurrAbbv, Currency, CurrencyID, MaxEntryDateTime, Rate) {
                var table = $('#currency-tables');
                var row = $('<tr>');
                var currency = $(`<td class="text-black text-center text-xs whitespace-nowrap p-1"><span>${Currency}</span></td>`);
                var rate = $(`<td class="text-black text-center text-xs whitespace-nowrap p-1"><input class="form-control selling-rate text-right" id="selling-rate-${CurrencyID}" data-currencyid="${CurrencyID}" type="number" value="${parseFloat(Rate.Rate).toLocaleString("en" , {minimumFractionDigits: 4 , maximumFractionDigits: 4})}"></td>`);

                row.append(currency);
                row.append(rate);

                table.find('#empty-banner').remove();

                table.find('tbody').append(row);
                row.hide().fadeIn(250);
            }
        }

        $('#currencies-select-selling').on('change', function() {
            clearTransfertoSellTable();

            $('#total-generated-amount').text('0.00');
            $('#total-generated-gain-loss').text('');
            $('#total-generated-capital').text('0.00');
            $('#total-generated-ex-ex-r').text('0.00');

            $('#total-generated-amount-input').val('');
            $('#total-generated-gain-loss-input').val('');
            $('#total-generated-capital-input').val('');
            $('#total-generated-ex-ex-r-input').val('');

            $('#container-test').fadeIn("slow");
            $('#container-test').css('display', 'block');

            setTimeout(function() {
                $('#container-test').fadeOut("slow");
            },200);

            $('#rate-used-transfers-to-sell').attr('disabled', 'disabled').val('');
            $('input[name="radio-transact-type"]').removeAttr('disabled').prop('checked', false);
            $('input[name="radio-transact-type-buffer"]').removeAttr('disabled').prop('checked', false);

            if ($(this).val() == 11) {
                $('input[name="radio-transact-type-buffer"]').removeAttr('disabled');
            } else {
                $('input[name="radio-transact-type-buffer"]').attr('disabled', 'disabled');
            }
        });

        $('input[name="radio-transact-type"]').change(function() {
            clearTransfertoSellTable();

            // $('.selling-rate').attr('readonly', false);

            $('input[name="radio-transact-type-buffer"]').prop('checked', false);

            $('#bills-to-sell-select-all').prop('checked', false);
            $('#rate-used-transfers-to-sell').removeAttr('disabled').val('');
        });

        $('input[name="radio-transact-type-buffer"]').change(function() {
            clearTransfertoSellTable();

            // $('.selling-rate').attr('readonly', false);
 
            $('input[name="radio-transact-type"]').prop('checked', false);

            $('#bills-to-sell-select-all').prop('checked', false);
            $('#rate-used-transfers-to-sell').removeAttr('disabled').val('');
        });

        $('#consolidate-transfers-button').click(function() {
            var curreny_ids = [];
            var selling_rates = [];
            var currency_id = $('#currencies-select-selling').val();
            var selling_rate = $('#rate-used-transfers-to-sell').val();
            var TTID = $('input[name="radio-transact-type"]:checked').val();
            var if_buffer = $('input[name="radio-transact-type-buffer"]:checked').val();

            $('.selling-rate').each(function() {
                curreny_ids.push($(this).attr('data-currencyid'));
            });

            $('.selling-rate').each(function() {
                if ($(this).val() != '' && $(this).val() > 0) {
                    selling_rates.push($(this).val());
                }
            });
            
            if (selling_rates.length != curreny_ids.length) {
                Swal.fire({
                    icon: 'error',
                    text: 'Selling rate field is required.',
                }).then(() => {
                    var empty_field = false;

                    $('.selling-rate').each(function() {
                        var rate_fields = $(this).val() === '';

                        $(this).css('border', '');

                        if (rate_fields) {
                            empty_field = true;

                            $(this).css('border', '1px solid red');
                            $(this).focus();
                        } else {
                            $(this).css('border', '1px solid #D9DEE3');
                        }
                    });
                });
            } else {
                $.ajax({
                    url: "{{ route('admin_transactions.bulk_selling.get_bills') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        currency_ids: curreny_ids.join(", "),
                        TTID: TTID,
                        if_buffer: if_buffer,
                        selling_rates: selling_rates.join(", "),
                    },
                    success: function(data) {
                        var results = data.admin_stock_details_s;
                        var total_curr_amnt = 0;
                        var total_gain_loss = 0;
                        var total_capital = 0;
                        var total_expected_exchange_rate = 0;

                        if (results.length === 0) {
                            Swal.fire({
                                icon: 'warning',
                                text: 'No available stocks.',
                            });
                        } else {
                            clearTransfertoSellTable();

                            $('.selling-rate').css('border', '');

                            $('#container-test').fadeIn(300);
                            $('#container-test').css('display', 'block');

                            setTimeout(function() {
                                results.forEach(function(gar) {
                                    stockResults(gar.All_FSIDs, gar.All_AFSIDs, gar.CurrencyID, gar.Currency, gar.total_bill_amount, gar.total_bill_count, gar.selling_rate, gar.SinagRateBuying, gar.total_exchange_amount, gar.total_principal, gar.gain_loss);

                                    total_curr_amnt += gar.total_bill_amount;
                                    total_gain_loss += gar.gain_loss;

                                    total_capital += gar.total_principal;
                                    total_expected_exchange_rate += gar.total_exchange_amount;
                                });

                                $('#container-test').fadeOut(300);

                                billsToSellTableFooter(total_curr_amnt, total_gain_loss, total_capital, total_expected_exchange_rate);
                            },500);
                        }
                    }
                });
            }

            // if (selling_rates.length != curreny_ids.length) {
            //     console.log("test");
            // }

            // if (selling_rate == null || selling_rate == '') {
            //     Swal.fire({
            //         icon: 'error',
            //         text: 'Manila Rate field is empty.',
            //     });
            // } else {
            //     $.ajax({
            //         url: "{{ route('admin_transactions.bulk_selling.get_bills') }}",
            //         type: "POST",
            //         data: {
            //             _token: "{{ csrf_token() }}",
            //             currency_id: currency_id,
            //             TTID: TTID,
            //             if_buffer: if_buffer,
            //             selling_rate: selling_rate
            //         },
            //         success: function(data) {
            //             var results = data.admin_stock_details_s;
            //             var total_curr_amnt = 0;
            //             var total_gain_loss = 0;
            //             var total_capital = 0;
            //             var total_expected_exchange_rate = 0;

            //             if (results.length === 0) {
            //                 Swal.fire({
            //                     icon: 'warning',
            //                     text: 'No available stocks.',
            //                 });
            //             } else {
            //                 clearTransfertoSellTable();

            //                 $('#container-test').fadeIn(300);
            //                 $('#container-test').css('display', 'block');

            //                 setTimeout(function() {
            //                     results.forEach(function(gar) {
            //                         stockResults(gar.All_FSIDs, gar.All_AFSIDs, gar.Currency, gar.total_bill_amount, gar.total_bill_count, selling_rate, gar.SinagRateBuying, gar.total_exchange_amount, gar.total_principal, gar.gain_loss);

            //                         total_curr_amnt += gar.total_bill_amount;
            //                         total_gain_loss += gar.gain_loss;

            //                         total_capital += gar.total_principal;
            //                         total_expected_exchange_rate += gar.total_exchange_amount;
            //                     });

            //                     $('#container-test').fadeOut(300);

            //                     billsToSellTableFooter(total_curr_amnt, total_gain_loss, total_capital, total_expected_exchange_rate);
            //                 },200);
            //             }
            //         }
            //     });
            // }
        });

        function stockResults(All_FSIDs, All_AFSIDs, CurrencyID, Currency, total_bill_amount, total_bill_count, selling_rate, SinagRateBuying, total_exchange_amount, total_principal, gain_loss) {
            var capital_used = total_bill_amount * SinagRateBuying;
            var exchange_rate = total_bill_amount * selling_rate;
            var gain_loss_formatted = gain_loss.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            if (gain_loss > 1) {
                gain_loss_formatted = '+' + gain_loss_formatted;
            } else if (gain_loss < 0) {
                gain_loss_formatted = '-' + gain_loss_formatted.substring(1);
            }

            // var badgeColor = gain_loss >= 0 ? 'success-badge-custom' : 'danger-badge-custom';
            var text_color = gain_loss >= 0 ? 'text-[#00A65A] font-bold text-xs' : 'text-[#DC3545] font-bold text-xs';
            var icon_gain_loss = gain_loss >= 0 ? `<i class='bx bxs-up-arrow pb-1' style="font-size: .5rem;"></i>` : `<i class='bx bxs-down-arrow pb-1' style="font-size: .5rem;"></i>`;

            var transfers_to_sell_table = $('#transfer-forex-to-sell-table');
            var transfers_to_sell_row = $('<tr>');
            var select_transfers_to_sell = $('<td class="text-center text-sm py-1 px-2"><div class="row align-items-center"><div class="text-rate-maintenance col-12 px-0"><input class="form-check-input bills-to-sell-select-one" type="checkbox" data-serialfsid="'+ All_FSIDs +'" data-serialafsid="'+ All_AFSIDs +'" data-currency="'+ Currency +'" data-totalbillamount="'+ total_bill_amount +'" data-currencyids="'+ CurrencyID +'" data-sellingrate="'+ selling_rate +'" data-buyingrate="'+ SinagRateBuying +'" data-capital="'+ capital_used +'" data-expctdxchngrate="'+ exchange_rate +'" data-gainloss="'+ gain_loss_formatted +'" data-rawgainloss="'+ gain_loss +'" checked></div></div></td>');
            var currency = $('<td class="text-center text-sm py-1 px-2">'+ Currency +'</td>');
            var total_curr_amnt = $('<td class="text-right text-sm py-1 px-2">' + total_bill_amount.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>');
            var total_curr_bill_count = $('<td class="text-right text-sm py-1 px-2">' + total_bill_count + '</td>');
            var selling_rate_used = $('<td class="text-right text-sm py-1 px-2">' + selling_rate.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>');
            var rate_used = $('<td class="text-right text-sm py-1 px-2">' + SinagRateBuying + '</td>');
            var total_exchange_rate = $('<td class="text-right text-sm py-1 px-2">' + exchange_rate.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>');
            var total = $('<td class="text-right text-sm py-1 px-2">' + capital_used.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>');
            // var transfers_to_sell_gain_loss = $('<td class="text-right text-sm py-1 px-2"><span class="badge '+ badgeColor +'"><text>'+ gain_loss_formatted +' &nbsp; '+ icon_gain_loss +'</text></span></td>');
            var transfers_to_sell_gain_loss = $('<td class="text-right text-sm py-1 px-2"><span class="'+ text_color +'"><text>'+ gain_loss_formatted +' '+ icon_gain_loss +'</text></span></td>');

            transfers_to_sell_row.append(select_transfers_to_sell);
            transfers_to_sell_row.append(currency);
            transfers_to_sell_row.append(total_curr_amnt);
            transfers_to_sell_row.append(total_curr_bill_count);
            transfers_to_sell_row.append(selling_rate_used);
            transfers_to_sell_row.append(rate_used);
            transfers_to_sell_row.append(total_exchange_rate);
            transfers_to_sell_row.append(total);
            transfers_to_sell_row.append(transfers_to_sell_gain_loss);

            $('#selling-add-to-queueing').removeAttr('disabled');
            $('#bills-to-sell-select-all').removeAttr('disabled').prop('checked', true);

            transfers_to_sell_table.find('#transfer-forex-to-sell-table-body').append(transfers_to_sell_row);
            transfers_to_sell_row.hide().fadeIn(250);
        }

        function billsToSellTableFooter(total_curr_amnt, total_gain_loss, total_capital, total_expected_exchange_rate) {
            $('#total-generated-gain-loss').empty();

            var gain_loss_formatted = total_gain_loss.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            if (total_gain_loss > 1) {
                gain_loss_formatted = '+' + gain_loss_formatted;
            } else if (total_gain_loss < 0) {
                gain_loss_formatted = '-' + gain_loss_formatted.substring(1);
            }

            var badgeColor = total_gain_loss >= 0 ? 'success-badge-custom' : 'danger-badge-custom';
            var icon_gain_loss = total_gain_loss >= 0 ? `<i class='bx bxs-up-arrow pb-1' style="font-size: .5rem;"></i>` : `<i class='bx bxs-down-arrow pb-1' style="font-size: .5rem;"></i>`;

            var transfers_to_sell_total_curr_amnt = $('<strong>' + total_curr_amnt.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong>');
            var transfers_to_sell_total_gain_loss = $('<span class="badge '+ badgeColor +'">'+ gain_loss_formatted +' '+ icon_gain_loss +'</span>');
            var transfers_to_sell_total_capital = $('<strong>' + total_capital.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong>');
            var transfers_to_sell_total_ex_ex_r = $('<strong>' + total_expected_exchange_rate.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong>');

            // $('#total-generated-amount').html(transfers_to_sell_total_curr_amnt);
            $('#total-generated-gain-loss').html(transfers_to_sell_total_gain_loss);
            $('#total-generated-capital').html(transfers_to_sell_total_capital);
            $('#total-generated-ex-ex-r').html(transfers_to_sell_total_ex_ex_r);

            $('#total-generated-amount-input').val(total_curr_amnt);
            $('#total-generated-gain-loss-input').val(total_gain_loss);
            $('#total-generated-capital-input').val(total_capital);
            $('#total-generated-ex-ex-r-input').val(total_expected_exchange_rate);
        }

        function clearTransfertoSellTable() {
            $('#buying-transact-banner').hide();
            $('#transfer-forex-to-sell-table tbody').empty();

            $('#total-generated-amount').text('0.00');
            $('#total-generated-ex-ex-r').text('0.00');
            $('#total-generated-capital').text('0.00');
            $('#total-generated-gain-loss').html('');

            $('#total-generated-amount-input').val(0);
            $('#total-generated-ex-ex-r-input').val(0);
            $('#total-generated-capital-input').val(0);
            $('#total-generated-gain-loss-input').val(0);
        }

        // Selling Transaction Table
        var selling_total_admin_curr_amount = 0;

        $('.total-amount-bill-selling-admin').each(function() {
            var curr_amount_selling_admin = parseFloat($(this).val());

            selling_total_admin_curr_amount += curr_amount_selling_admin;
        });

        $('#selling-trans-admin-amount').text(selling_total_admin_curr_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
    });

    // var grouped_fsids = [];
    // var grouped_afsids = [];

    $('#selling-add-to-queueing').click(function() {
        var selected_total_amnt = 0;
        var selected_total_gain_loss = 0;
        var selected_total_capital = 0;
        var selected_total_ex_ex_r = 0;

        let boolean_val = false;
        let has_loss = false;

        $('.bills-to-sell-select-one').each(function() {
            if ($(this).prop('checked')) {
                boolean_val = true;

                if (parseFloat($(this).attr('data-rawgainloss')) <= 0) {
                    has_loss = true;

                    Swal.fire({
                        icon: 'warning',
                        text: 'You are about to queue loss entries. Continue queueing?',
                        showConfirmButton: true,
                        showCancelButton: true,
                        cancelButtonColor: '#8592A3',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            testLang(boolean_val);
                        }
                    });
                    return false;
                }
            }
        });

        if (boolean_val) {
            if (!has_loss) {
                testLang(boolean_val);
            }
        } else {
            testLang(boolean_val);
        }

        function testLang(gar) {
            if (gar) {
                proceedTransaction();
            } else {
                Swal.fire({
                    icon: 'error',
                    text: 'No bills selected.',
                });
            }
        }

        function proceedTransaction() {
            var currency = [];
            var amount = [];
            var rate = [];
            var capital = [];
            var exchange_rate = [];
            var gain_loss = [];

            var test_boolean = '';
            var test_1_boolean = '';

            var from_branch = [];
            var from_admin = [];
            var curreny_ids = [];
            var selling_rates = [];

            $('.bills-to-sell-select-one').each(function() {
                var selected_bills  = $(this).prop('checked') == true;

                if (selected_bills) {
                    // currency.push($(this).attr('data-currency'));
                    // amount.push($(this).attr('data-totalbillamount'));
                    // rate.push($(this).attr('data-buyingrate'));
                    // capital.push($(this).attr('data-capital'));
                    // exchange_rate.push($(this).attr('data-expctdxchngrate'));
                    // gain_loss.push($(this).attr('data-gainloss'));

                    if ($(this).attr('data-serialfsid') != 'null') {
                        from_branch.push($(this).attr('data-serialfsid'));
                    }

                    if ($(this).attr('data-serialafsid') != 'null') {
                        from_admin.push($(this).attr('data-serialafsid'));
                    }

                    if ($(this).attr('data-currencyids') != 'null') {
                        curreny_ids.push($(this).attr('data-currencyids'));
                    }

                    if ($(this).attr('data-sellingrate') != 'null') {
                        selling_rates.push($(this).attr('data-sellingrate'));
                    }
                }
            });

            setTimeout(function() {
                $('#container-test').fadeOut("fast");
                    $('.bills-to-sell-select-one:checked').each(function() {
                        // concatinated_fsids_array.push($(this).attr('data-serialfsid'));
                        selected_total_gain_loss += parseFloat($(this).attr('data-gainloss'));
                        selected_total_amnt += parseFloat($(this).attr('data-totalbillamount'));
                        selected_total_capital += parseFloat($(this).attr('data-capital'));
                        selected_total_ex_ex_r += parseFloat($(this).attr('data-expctdxchngrate'));
                    });

                    $.ajax({
                        url: "{{ route('admin_transactions.bulk_selling.queue_bills') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            FSIDs: from_branch.join(", "),
                            AFSIDs: from_admin.join(", "),
                            // selling_rate: $('#rate-used-transfers-to-sell').val(),
                            curreny_ids: curreny_ids.join(", "),
                            selling_rates: selling_rates.join(", ")
                        },
                        success: function(data) {
                            $('#container-test').css('display', 'block');
                            window.location.reload();
                        }
                    });
            }, 500)
        }
    });

    $(document).ready(function() {
        $('#for-selling-unselect-bills').click(function() {
            var from_branch = [];
            var from_admin = [];

            $('.consolidate-bills-select-one').each(function() {
                var selected_bills  = $(this).prop('checked') && !$(this).prop('disabled');

                if (selected_bills) {
                    // currency.push($(this).attr('data-currency'));
                    // amount.push($(this).attr('data-totalbillamount'));
                    // rate.push($(this).attr('data-buyingrate'));
                    // capital.push($(this).attr('data-capital'));
                    // exchange_rate.push($(this).attr('data-expctdxchngrate'));
                    // gain_loss.push($(this).attr('data-gainloss'));

                    if ($(this).attr('data-serialfsid') != '') {
                        from_branch.push($(this).attr('data-serialfsid'));
                    }

                    if ($(this).attr('data-serialafsid') != '') {
                        from_admin.push($(this).attr('data-serialafsid'));
                    }
                }
            });

            if (from_admin.length == 0 && from_branch.length == 0) {
                Swal.fire({
                    icon: 'error',
                    text: 'No bills selected.',
                });
            } else {
                $.ajax({
                    url: "{{ route('admin_transactions.bulk_selling.unselect') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        FSIDs: from_branch.join(", "),
                        AFSIDs: from_admin.join(", "),
                    },
                    success: function() {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Bills unqueued!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");

                            setTimeout(function() {
                                $('#container-test').css('display', 'block');
                                window.location.reload();
                            }, 500);
                        });
                    }
                });
            }
        });
    });

    $(document).ready(function() {
        $('#availalbe-stocks-button').click(function() {
            $('#container-test').fadeIn("fast");

            $.ajax({
                url: "{{ route('admin_transactions.bulk_selling.availabe_bills') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#available-stocks-table tbody').empty();

                    var available_serials = data.available_serials;

                    if (available_serials.length == 0) {
                        var banner =
                        ` <tr id="selling-pool-banner">
                            <td class="text-center text-td-buying text-sm py-3" colspan="13">
                                <span class="buying-no-transactions text-lg">
                                    <strong>NO AVAILABLE STOCKS</strong>
                                </span>
                            </td>
                        </tr>`;

                        $('#available-stocks-table tbody').append(banner);
                    } else {
                        $('#available-stocks-table tbody').empty();

                        setTimeout(function() {
                            $('#container-test').fadeOut("fast");

                            available_serials.forEach(function(gar) {
                                unqueuedStocks(gar.Currency, gar.total_principal, gar.total_curr_amount, gar.total_bill_count, gar.queued_total_bill_count, gar.queued_total_curr_amnt);
                            });
                        }, 300);
                    }
                }
            });
        });

        function unqueuedStocks(Currency, total_principal, total_curr_amount, total_bill_count, queued_total_bill_count, queued_total_curr_amnt) {
            var total_amnt = total_curr_amount == null ? "0.00" : total_curr_amount.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            var queued_amnt = queued_total_curr_amnt == null ? "0.00" : queued_total_curr_amnt.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            var table = $('#available-stocks-table');
            var table_row = $('<tr>');
            var currency = $('<td class="text-black text-sm text-center whitespace-nowrap p-1">' + Currency + '</td>');
            var bill_count = $('<td class="text-black text-sm text-center whitespace-nowrap p-1">' + total_bill_count + '</td>');
            var total_bill_amount = $('<td class="text-black text-sm text-right whitespace-nowrap py-1 px-3"><strong>' + total_amnt + '</strong></td>');
            var queued_bill_count = $('<td class="text-black text-sm text-center whitespace-nowrap p-1">' + queued_total_bill_count + '</td>');
            var queued_total_bill_amnt = $('<td class="text-black text-sm text-right whitespace-nowrap py-1 px-3"><strong>' + queued_amnt + '</strong></td>');
            var total_principal_amnt = $('<td class="text-black text-sm text-right whitespace-nowrap py-1 px-3"><strong>' + total_principal.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong></td>');

            table_row.append(currency);
            table_row.append(bill_count);
            table_row.append(total_bill_amount);
            table_row.append(queued_bill_count);
            table_row.append(queued_total_bill_amnt);
            table_row.append(total_principal_amnt);

            $('#available-stocks-table tbody').append(table_row);
        }
    });

    $(document).ready(function() {
        $('#test-print').click(function() {
            $.ajax({
                url: "{{ route('admin_transactions.bulk_selling.print_queued') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(data) {
                    console.log(data.bills_rset);
                }
            });
        });
    });

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
        var admin_serials = [];
        var branch_serials = [];

        $('.consolidate-bills-select-one').each(function() {
            if ($(this).attr('data-buffer')) {
                branch_serials.push($(this).attr('data-serialfsid'));
                admin_serials.push($(this).attr('data-serialafsid'));
            }
        });

        $('#set-buff-rate').click(function() {
            $('#set-buff-rate').prop('disabled', true);
            var transaction_type = $('#transaction-type').val();
            var user_sec_onpage = $('#set-buff-rate-security-code').val();

            if ($('#buffer-rate').val() == 0) {
                Swal.fire({
                    icon: 'error',
                    html: `<span class="text-sm">Rate for buffer is required.</span>`,
                }).then(() => {
                    $('#set-buff-rate').prop('disabled', false);
                });
            } else {
                if (sec_code_array.includes(user_sec_onpage)) {
                    $('#set-buff-rate').prop('disabled', true);

                    var index = sec_code_array.indexOf(user_sec_onpage);
                    var matched_user_id = user_id_array[index];

                    $.ajax({
                        url: "{{ route('admin_transactions.bulk_selling.add_buff_rate') }}",
                        type: "post",
                        data: {
                            _token: "{{ csrf_token() }}",
                            FSIDs: branch_serials,
                            AFSIDs: admin_serials,
                            buffer_rate: $('#buffer-rate').val(),
                        },
                        success: function(data) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Buffer rate updated!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                $('#container-test').fadeIn("slow");
                                $('#container-test').css('display', 'block');

                                window.location.reload();
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        text: 'Invalid or mismatched security code.',
                        customClass: {
                            popup: 'my-swal-popup',
                        }
                    }).then(() => {
                        $('#set-buff-rate').prop('disabled', false);
                    });
                }
            }
        });
    });
</script>
