<script>
    let receipt_set = '';

    $(document).ready(function(){
        $('input[name="radio-rset"]').change(function() {
            $('#or-number-buying').removeAttr('disabled');
            $('#radio-button-Bills').prop('checked', true).change();

            receipt_set = $(this).val();

            if ($(this).val() == 'O') {
                $('#or-number-container').fadeIn(250).show();
                $('input[name="radio-transact-type"]').attr('disabled', true);
                $('#currencies_select').attr('disabled', true);
                $('input[name="radio-transact-type"]').prop('checked', false);
            } else if ($(this).val() == 'B') {
                $('#or-number-buying').val('');
                $('#or-number-container').fadeOut(200);
                $('.radio-button').removeAttr('disabled');
            }

            $('#buying-transact-table-body').empty();
            var default_val = $('#buying-default-currency').val();

            var choose_currency_banner =
                `<tr id="buying-transact-banner">
                    <td class="text-center text-td-buying text-sm py-3" colspan="13">
                        <span class="buying-no-transactions text-lg">
                            <strong>CHOOSE A CURRENCY</strong>
                        </span>
                    </td>
                </tr>`;

            $('#buying-transact-table-body').append(choose_currency_banner).fadeIn(200);
            $('#currencies_select').val($('#buying-default-currency').val());

            $('#remarks').attr('disabled', 'disabled').val('');
        });

        // UI/UX - Remove disabled attribute for elements
        $('#transact-date').on('click', function(){
            $('#customer-detail').removeAttr('disabled');
        })

        // UI/UX - Radio change for transaction type and currency
        function handleRadioChange() {
            var transact_type = $('input[name="radio-transact-type"]:checked').val();
            var sel_curr_id = $('#currencies_select').val();

            $('#buying-transact-table-body').empty();
            var default_val = $('#buying-default-currency').val();

            var choose_currency_banner =
                `<tr id="buying-transact-banner">
                    <td class="text-center text-td-buying text-sm py-3" colspan="13">
                        <span class="buying-no-transactions text-lg">
                            <strong>CHOOSE A CURRENCY</strong>
                        </span>
                    </td>
                </tr>`;

            $('#currencies_select').val(default_val);
            $('#currencies_select').removeAttr('disabled');
            $('#buying-transact-table-body').append(choose_currency_banner).fadeIn(200);

            $.ajax({
                url: "{{ route('admin_transactions.admin_b_transaction.currencies') }}",
                type: "post",
                data: {
                    _token: "{{ csrf_token() }}",
                    transact_type: transact_type
                },
                success: function(data) {
                    var currencies = data.currencies;
                    var select_curr_elemt = $('#currencies_select');

                    select_curr_elemt.empty();

                    var default_option = `<option value="Select a currency" id="buying-default-currency">Select a currency</option>`;
                    select_curr_elemt.append(default_option);

                    function toTitleCase(str) {
                        return str.toLowerCase().replace(/(?:^|\s)\w/g, function(match) {
                            return match.toUpperCase();
                        });
                    }

                    currencies.forEach(function(curr_data) {
                        var option_element = `<option value="`+ curr_data.CurrencyID +`" name="selected-currency">${curr_data.Currency}</option>`;
                        select_curr_elemt.append(option_element);
                    });
                }
            });
        }

        $('input[name="radio-transact-type"]').change(handleRadioChange);

        // Fetch values for the ajax request for the populate currencies
        $('#currencies_select').change(function() {
            var sel_curr_id = $(this).val();
            $('#remarks').removeAttr('disabled', 'disabled');

            var transact_type = $('input[name="radio-transact-type"]:checked').val();

            if (sel_curr_id == 'Select a currency') {
                clearData();
                $('#transaction-confirm-button').attr('disabled', 'disabled');
            }

            if (parseInt(sel_curr_id) == 11) {
                $('#buffer-option').show();
            } else {
                $('#buffer-option').hide();
            }

            getData(transact_type, sel_curr_id);
        });

        $('.switch-input').change(function() {
            $('input[name="radio-buffer-options"]').prop('checked', false);

            if ($(this).is(':checked')) {
                $(this).attr('checked', 'checked');
                $('#r-buffer-opt-1').prop('checked', true);
                $('input[name="radio-buffer-options"]').removeAttr('disabled', true);
            } else {
                $(this).removeAttr('checked');
                $('input[name="radio-buffer-options"]').attr('disabled', true);

                var buying_amount = $('#total_buying_amount_input').val();

                $('#total_buying_amount_true').val(buying_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                $('#total_buying_amount').text(parseFloat(buying_amount).toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            }
        });

        $('input[name="radio-buffer-options"]').change(function() {
            var buying_amount = $('#total_buying_amount_input').val();

            if ($(this).val() == '1') {
                $('#total_buying_amount_true').val(buying_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                $('#total_buying_amount').text(parseFloat(buying_amount).toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            } else if ($(this).val() == '2') {
                $('#total_buying_amount').text('0.00');
                $('#total_buying_amount_true').val(0);
            }
        });

        var test = '';

        // AJAX request for the currDenomination function - BuyingTransactController
        function getData(transact_type, sel_curr_id = null) {
            var _token = $('input[name="_token"]').val();

            $.ajax({
                url: "{{ route('admin_transactions.admin_b_transaction.denomination') }}",
                type: "POST",
                data: {
                    transact_type: transact_type,
                    sel_curr_id: sel_curr_id,
                    _token: _token,
                },
                success: function(data) {
                    var curr_dom = data.currency_denom;
                    var rate_used_max = data.rate_used_max;
                    var rate_config_details = data.rate_config;
                    var specific_rate = rate_used_max[0].Rate;
                    var dpofx_rate = data.dpofx_rate;
                    var true_dpo_rate = dpofx_rate.Rate;
                    var manual_deets = data.test_details;

                    if (curr_dom == '') {
                        Swal.fire({
                            icon: 'error',
                            text: 'This currency has no available denomination(s) for the transaction type',
                            customClass: {
                                popup: 'my-swal-popup',
                            }
                        }).then(() => {
                            var available_denoms =
                                `<tr id="buying-transact-banner">
                                    <td class="text-center text-td-buying text-sm py-3" colspan="13">
                                        <span class="buying-no-transactions text-lg">
                                            <strong>NO AVAILABLE DENOMINATIONS</strong>
                                        </span>
                                    </td>
                                </tr>`;

                            $('#buying-transact-table-body').append(available_denoms).fadeIn(200);
                        });
                    }

                    $('#currency-manual-modal-button').removeAttr('disabled');

                    clearData();

                    curr_dom.forEach(function(items) {
                        updateData(items.CurrencyID, items.BillAmount, items.Subtotal, items.Multiplier, items.ManilaRate, items.SinagRateBuying, items.VarianceBuying, items.VarianceSelling, transact_type, true_dpo_rate);
                    });

                    rate_used_max.forEach(function(rate_max) {
                        rate(rate_max.CRID, rate_max.CurrencyID, rate_max.Rate, rate_max.EntryDateTime, rate_max.EntryDate, rate_config_details);
                    });

                    manual_deets.forEach(function(gar) {
                        currencyManual(gar.CurrencyID, gar.Currency, gar.DenominationID, gar.BillAmount, gar.CMTID, gar.ManualTag, gar.BillAmountImage, gar.StopBuying, gar.Remarks);
                    });

                    test = manual_deets.length;
                }
            });
        }

        $('#currency-manual-modal-button').click(function() {
            if (test == 0) {
                Swal.fire({
                    icon: 'error',
                    text: 'No available manual for this currency.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else {
                $('#currency-manual-modal').modal("show");
            }
        });

        function currencyManual(CurrencyID, Currency, DenominationID, BillAmount, CMTID, ManualTag, BillAmountImage, StopBuying, Remarks) {
            let badge_color = '';

            if (CMTID == 1) {
                badge_color = 'success-badge-custom';
            } else if (CMTID == 2) {
                badge_color = 'warning-badge-custom';
            } else {
                badge_color = 'primary-badge-custom';
            }
            
            // Currency images - swiper main
            var curr_pic_main = $(`<img class="curr-swiper-main-pictures" src="{{ asset('storage/`+ BillAmountImage + `') }}" alt="">`);
            var curr_pic_wrapper = $('<div class="zoom" id="image-zoom">').append(curr_pic_main).zoom({ on: 'click' });
            var card_body_main = $('<div class="card-body img-zoom p-0" id="image-zoom">').append(curr_pic_wrapper);
            // var curr_details_main = $('<h4 class="currency-details-name-main mb-0">'+ curr_name_type.text() +'</h4>');
            var curr_remarks_main = $('<div class="currency-details-remarks-wrapper">').append(Remarks);
            var card_footer_main = $('<div class="card-footer curr-main-card-footer pt-3 p-0 px-2">').append(curr_remarks_main);

            // // Swiper Main0
            var pic_card_main = $('<div class="card shadow-none currency-card-main">').append(card_body_main, card_footer_main);
            var swiper_main_slider = $('<div class="swiper-slide swiper-slide-main">').append(pic_card_main);

            // // Currency images - swiper thumb
            var curr_pic_thumb = $(`<img class="curr-swiper-thumb-pictures" src="{{ asset('storage/`+ BillAmountImage + `') }}" alt="">`);
            var card_body_thumb = $('<div class="card-body px-1 pb-0 pt-2">').append(curr_pic_thumb);
            var curr_details_thumb = $('<span class="currency-details-name-thumb">'+ Currency +' ('+ BillAmount.toLocaleString() +')</span><br><span class="badge text-xs font-bold text-white p-1 mt-2 '+ badge_color +'">'+ManualTag+'</span>');
            var card_footer_thumb = $('<div class="card-footer pt-2 p-0 px-2 mb-2">').append(curr_details_thumb);

            // // Swiper Main
            var pic_card_thumb = $('<div class="card shadow-none">').append(card_body_thumb, card_footer_thumb);
            var swiper_thumb_slider = $('<div class="swiper-slide swiper-slide-thumb">').append(pic_card_thumb);

            $('#currency-swiper-main-wrapper').append(swiper_main_slider);
            $('#currency-swiper-thumb-wrapper').append(swiper_thumb_slider);

            $('#currencies_select').on('change', function(){
                $('#currency-swiper-main-wrapper').empty();
                $('#currency-swiper-thumb-wrapper').empty();
            });
        }

        var denom_array = [];

        // Script for appending table cells on change of option in the select option element
        function updateData(CurrencyID, billAmount, Multiplier, Subtotal, ManilaRate, SinagRateBuying, VarianceBuying, VarianceSelling, transactType, true_dpo_rate) {
            var table = $('#currency-denom-table');
            var new_row = $('<tr>');
            var amount_cell = $('<td class="p-1"><input type="text" class="form-control bill-amount text-center" id="bill-amount" name="bill-amount" value="' + parseFloat(billAmount).toLocaleString() + '" disabled></td>');
            var multiplier_cell = $('<td class="p-1"><input type="text" class="form-control multiplier text-center" id="multiplier" name="multiplier" placeholder="0"></td>');
            var subtotal_cell = $('<td class="p-1"><input type="text" class="form-control subtotal-cells text-end" id="subtotal" name="subtotal" value="0.00" data-subtotal="" disabled></td>');
            var sinag_buying_rate_cell = $('<td class="p-1"><input type="number" class="form-control sinag-buying-rate-cells text-end" id="sinag-buying-rate" name="sinag-buying-rate[]" value="'+ SinagRateBuying +'" data-origrate="'+ SinagRateBuying +'"></td>');
            var total_amount_cell = $('<td hidden><input type="hidden" class="form-control total-amount-cells text-end" id="total-amount" name="total-amount" value="0.00000" disabled></td>');
            var total_amount_cell_formatted = $('<td class="p-1"><input type="text" class="form-control total-amount-cells-formatted text-end" id="total-amount-formatted" name="total-amount" value="0.00000" disabled></td>');
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

            multiplier_cell.find('input').on('keyup', function() {
                totalAmount();
                multiplierRestriction();
            });

            sinag_buying_rate_cell.find('input').on('keyup', function() {
                rateChange();
            });

            table.find('tbody').append(new_row);
            new_row.hide().fadeIn(250);

            sinag_buying_rate_cell.find('.sinag-buying-rate-cells').on('change', function() {
                var converted_changed_rate = parseFloat($(this).val());
                var converted_used_rate = parseFloat($('#rate_used').val());
                var currency_amount = $('#current_amount_true').val();
                var sub_total = $(this).closest('tr').find('.subtotal-cells').val();
                var new_total_amnt_change = sub_total * SinagRateBuying;

                if (converted_changed_rate > converted_used_rate) {
                    Swal.fire({
                        // title: 'Ooops!',
                        text: "Rate change can't be higher than the Sinag Rate.",
                        icon: 'warning',
                        showCancelButton: false,
                    });

                    $(this).val(SinagRateBuying);
                    $(this).closest('tr').find('.total-amount-cells').val(new_total_amnt_change.toFixed(2));

                    var new_total_amount = 0;

                    var var_buying = parseFloat($(this).val());
                    var sub_total = $(this).closest('tr').find('.subtotal-cells').val();

                    var sinag_rate_buying = parseFloat(sub_total) * var_buying;
                    $(this).closest('tr').find('.total-amount-cells').val(sinag_rate_buying.toFixed(2));
                    $(this).closest('tr').find('.total-amount-cells-formatted').val(sinag_rate_buying.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));

                    var final_total_amount = table.find('.total-amount-cells');

                    final_total_amount.each(function() {
                        var get_new_total_amount = parseFloat($(this).closest('tr').find('.form-control#total-amount').val());
                        new_total_amount += get_new_total_amount;
                    });

                    setTimeout(function() {
                        $('#total_buying_amount').val(new_total_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})).text(new_total_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                        $('#total_buying_amount_input').val(new_total_amount);
                        $('#total_buying_amount_true').val(new_total_amount.toFixed(2)).text(new_total_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                    }, 100);
                }
            });

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

        // Total amount computation of DPOFX
        function totalAmountDPFOX(true_dpo_rate) {
            var totalAmount = 0;
            var bill_amnt_cell_dpofx = $('#dpofx-bill-amount').val();
            var currrent_amount = bill_amnt_cell_dpofx * 1;
            var total_amount_dpofx = bill_amnt_cell_dpofx * true_dpo_rate;

            $('#current_amount').val(currrent_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            $('#subtotal').val(currrent_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            $('#total-buying-amount-dpofx').text(total_amount_dpofx.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            $('#total-buying-amount-dpofx').val(total_amount_dpofx.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            $('#payout_amount').val(total_amount_dpofx.toFixed(2));

            var total_amnt_dpfox_val = ($('#total-buying-amount-dpofx').val()) == 0;

            if (total_amnt_dpfox_val == false) {
                $('#transaction-confirm-button').removeAttr('disabled');
            } else {
                $('#transaction-confirm-button').attr('disabled', 'disabled');
            }
        }

        // Total amount computation of regular, loosr and coin transaction type
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
                var bill_amount_value = parseFloat($(this).closest('tr').find('.form-control#bill-amount').val().toString().replace(/,/g, ""));

                var subtotal = bill_amount_value * multiplier_value;

                var multiplier_cell_count_input = $(this).closest('tr').find('.form-control#multiplier-count');
                multiplier_cell_count_input.val(multiplier_value);
                var multip_count = $(this).closest('tr').find('.form-control#multiplier-count').val();

                var sinag_buying_rate_input = parseFloat($(this).closest('tr').find('.form-control#sinag-buying-rate').val());
                var final_total_amount = subtotal * sinag_buying_rate_input;

                // Add sinag-buying-rate value to the array

                var sinag_var_buying_input = parseFloat($(this).closest('tr').find('.form-control#sinag-variance-buying').val());

                $(this).closest('tr').find('.total-amount-cells-formatted').val(final_total_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                $(this).closest('tr').find('.form-control#total-amount').val(final_total_amount.toFixed(2));

                var get_new_total_amount = parseFloat($(this).closest('tr').find('.form-control#total-amount').val());
                new_total_amount += get_new_total_amount;

                if (multip_count != '' && parseInt(multip_count) > 0) {
                    multip_array.push(parseInt(multip_count));
                    bill_amnt_array.push(bill_amount_value);
                    sinag_rate_buying_array.push(sinag_buying_rate_input);
                    sinag_variance_buying_array.push(sinag_var_buying_input);
                }

                $(this).val(subtotal.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));

                var subtotal_GET = parseFloat($(this).val().toString().replace(/,/g, ""));
                total_amount += subtotal_GET;

                if ((total_amount == 0) == false) {
                    // $('#remarks').removeAttr('disabled');
                    $('.buffer-option').removeAttr('disabled');
                    $('#transaction-confirm-button').removeAttr('disabled');
                } else {
                    // $('#remarks').attr('disabled', 'disabled');
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
            } else if (decim_part >= 0.75 && decim_part < 1) {
                decim_part = 0.75;
            }

            var rounded_total_amnt = integer_part + decim_part;

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
                        } else if (decim_part >= 0.75 && decim_part < 1) {
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
                    } else if (decim_part >= 0.75 && decim_part < 1) {
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

        // Redirect function
        function refreshPage(delay) {
            setTimeout(function() {
                location.reload(true);
            }, delay);
        }

        // Update for rate per currency
        function rate(CRID, CurrencyID, Rate, EntryDateTime, EntryDate, rate_config_details) {
            var max_crid = CRID;
            var max_rate = Rate;
            var max_ID = CurrencyID;

            $('#last-entry-crid').val(max_crid);
            $('#base-rate').val(max_rate).text(max_rate);

            totalAmount();
        }

        // UI/UX - Clear input fields for currency
        function clearData() {
            $('#currenct_amount').empty();
            $('#currency-denom-table tbody').empty();
        }

        // Input field for password
        $(document).ready(function() {
            $('#security-code').prop('type', 'text').on('focus', function() {
                $(this).prop('type', 'password');
            }).on('blur', function() {
                $(this).prop('type', 'password');
            });

        });

        // Validation for input fields in Buying Transact Form
        $(document).ready(function() {
            var rset_value = '';

            $('input[name="radio-rset"]').change(function() {
                rset_value = $(this).val();
            });

            $('#transaction-confirm-button').click(function() {
                var b_transact_date = $('#transact-date').val();
                var b_customer = $('#customer-name-selected').val();
                var b_rset = $('input[name="radio-rset"]').val();
                var b_or_number = $('#or-number-buying').val();
                var b_transact_type = $('input[name="radio-transact-type"]').val();
                var b_currency = $('#currencies_select').val();
                var b_multiplier = $('.multiplier').val();
                var get = $('#mtcn-input-field').val();
                var mtcn_number = $('#mtcn-input-field').val();

                // if (rset_value == 'O') {
                //     if (b_or_number == '') {
                //         Swal.fire({
                //             icon: 'error',
                //             text: 'Invoice number is required.',
                //             customClass: {
                //                 popup: 'my-swal-popup',
                //             }
                //         });
                //     } else {
                //         $('#security-code-modal').modal("show");
                //     }
                // } else {
                //     $('#security-code-modal').modal("show");
                // }

                if (b_or_number == '') {
                    Swal.fire({
                        icon: 'error',
                        text: 'Invoice number is required.',
                        customClass: {
                            popup: 'my-swal-popup',
                        }
                    });
                } else if (b_transact_date == '') {
                    Swal.fire({
                        icon: 'error',
                        text: 'Transact Date is required.',
                        customClass: {
                            popup: 'my-swal-popup',
                        }
                    });
                } else if (b_customer == '') {
                    Swal.fire({
                        icon: 'error',
                        text: 'Customer is required.',
                        customClass: {
                            popup: 'my-swal-popup',
                        }
                    });
                } else if (b_rset == '') {
                    Swal.fire({
                        icon: 'error',
                        text: 'Rset is required.',
                        customClass: {
                            popup: 'my-swal-popup',
                        }
                    });
                } else if (b_transact_type == '') {
                    Swal.fire({
                        icon: 'error',
                        text: 'Transact Type is required.',
                        customClass: {
                            popup: 'my-swal-popup',
                        }
                    });
                } else if (b_currency == 0) {
                    Swal.fire({
                        icon: 'error',
                        text: 'Currency is required.',
                        customClass: {
                            popup: 'my-swal-popup',
                        }
                    });
                } else if (b_transact_date && b_customer && b_or_number && b_transact_type && b_currency != '') {
                    $('#security-code-modal').modal("show");
                }
            });
        });

        $('.get-customer-details').click(function() {
            $('.radio-button-rset').removeAttr('disabled');
        });

        // UI/UX - Auto populate of buying transaction date
        $(document).ready(function() {
            var current_date = new Date();
            var year = current_date.getFullYear();
            var month = String(current_date.getMonth() + 1).padStart(2, '0');
            var day = String(current_date.getDate()).padStart(2, '0');

            var formatted_date = year + '-' + month + '-' + day;

            var set_date = $('#transact-date').val(formatted_date).text(formatted_date);

            if(set_date.val() != '') {
                $('#customer-detail').removeAttr('disabled');
                $('#customer-detail-selling').removeAttr('disabled');
            }
        });

        $(document).ready(function() {
            $('#or-number-buying').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');

                if (this.value.length > 11) {
                    this.value = this.value.slice(0, 11);
                }
            });

            // or_number_duplicate
            $('#or-number-buying').change(function() {
                var time_on_off_stat = $('#time-and-off-status').attr('data-timeonandoffstat');

                if (time_on_off_stat == 0) {
                    $('.radio-button').removeAttr('disabled', 'disabled');
                } else if (time_on_off_stat == 1) {
                    $('.radio-button').removeAttr('disabled', 'disabled');
                    $('#currencies_select').removeAttr('disabled', 'disabled');
                }

                var current_or_number = $(this).val();

                $.ajax({
                    url: "{{ route('admin_transactions.admin_b_transaction.or_number_duplicate_b') }}",
                    type: "post",
                    data: {
                        _token: "{{ csrf_token() }}",
                    }, success: function(data) {
                        var or_no_array = [];
                        var or_numbers = data.or_numbers;

                        or_numbers.forEach(function(or_numbs) {
                            or_no_array.push(or_numbs.ORNo);
                        });

                        if (or_no_array) {
                            let timerInterval;

                            Swal.fire({
                                title: "Checking for duplicates...",
                                timer: 600,
                                didOpen: () => {
                                    Swal.showLoading();
                                        const timer = Swal.getPopup().querySelector("b");
                                    timerInterval = setInterval(() => {
                                        timer.textContent = `${Swal.getTimerLeft()}`;
                                    }, 600);
                                },
                                willClose: () => {
                                    clearInterval(timerInterval);
                                }
                            }).then(() => {
                                if (or_no_array.includes(parseInt(current_or_number))) {
                                    Swal.fire({
                                        title: 'Duplicate entry',
                                        icon: 'error',
                                        text: 'OR number is already existing.',
                                        customClass: {
                                            popup: 'my-swal-popup',
                                        }
                                    }).then(() => {
                                        $('#currencies_select').attr('disabled', 'disabled');
                                        $('[name="radio-transact-type"]').attr('disabled', true);
                                        $('#or-number-buying').val('').attr('placeholder', 'OR Number');
                                    });
                                } else {
                                    $('#radio-button-Bills').prop('checked', true).change();
                                    $('.radio-button').removeAttr('disabled', 'disabled');
                                    $('#currencies_select').removeAttr('disabled', 'disabled');
                                }
                            });
                        }
                    }
                });
            });
        });

        function multiplierRestriction() {
            $('.multiplier').on('input', function() {
                $(this).val($(this).val().replace(/^(0\d+)|[^0-9]/g, ''));
            });
        }

        function dateAutoPopulate() {
            $('#transact-date').datepicker({
                minDate: 0,
                maxDate: 0,
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                changeYear: true
            });

            $('#transact-date-button').click(function() {
                $('#ui-datepicker-div').toggle();
                $('#ui-datepicker-div').css('z-index' , '4');
                $('#ui-datepicker-div').css('position' , 'absolute');
                $('#ui-datepicker-div').css('top' , '145');
            });
        }
    });

    $(document).ready(function() {
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
                            text: 'Buying transaction successfully added!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            var form_data = new FormData($('#buying-transact-form')[0]);
                            form_data.append('matched_user_id', matched_user_id);
                            form_data.append('receipt_set', receipt_set);
                            form_data.append('buffer_option', $('.switch-input').is(':checked'))

                            $.ajax({
                                url: "{{ route('admin_transactions.admin_b_transaction.save') }}",
                                type: "post",
                                data: form_data,
                                contentType: false,
                                processData: false,
                                cache: false,
                                success: function(data) {
                                    var route = "{{ route('admin_transactions.admin_b_transaction.details', ['id' => ':id']) }}";
                                    var url = route.replace(':id', data.latest_aftdid);

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
</script>
