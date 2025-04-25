{{-- Selling Transaction Module --}}
<script>
    $(document).ready(function() {
        var serials_to_be_sold = [];

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

        $('#serial-stock-modal-appended').on('hidden.bs.modal', function () {
            $('.serials-selling-selected').each(function() {
                $(this).prop('checked', false);
            });

            $('#available-bills-select-all').prop('checked', false);
            $('#denomination-filter option[value="default"]').prop('selected', true).trigger('change');
        });

        $('input[name="radio-rset"]').change(function() {
            clearStock();

            var rset_value = $(this).val();
            $('#new-serial-container').empty();
            $('#or-number-selling').removeAttr('disabled');
            $('#button-add-serial').attr('disabled', 'disabled');

            if (rset_value == 'O') {
                $('#or-number-container').fadeIn(250).show();
                $('#currencies-select-selling').attr('disabled', true);
            } else if (rset_value == 'B') {
                $('#or-number-selling').val('');
                $('#or-number-container').fadeOut(200);
                $('#currencies-select-selling').removeAttr('disabled');
            }

            $('#serial-stock-table-body').empty();
            var default_val = $('#currencies-selling-default-val').val();

            var choose_currency_banner =
                `<tr id="buying-transact-banner">
                    <td class="text-center text-td-buying text-sm py-3" colspan="13">
                        <span class="buying-no-transactions text-lg">
                            <strong>CHOOSE A CURRENCY</strong>
                        </span>
                    </td>
                </tr>`;

            $('#serial-stock-table-body').append(choose_currency_banner).fadeIn(200);
            $('#currencies-select-selling').val(default_val);

            $('#rate-used-true').val('');
            $('#rate-used-selling').val('');

            $.ajax({
                url: "{{ route('branch_transactions.selling_transaction.available_curr') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    rset_value: rset_value
                },
                success: function(data) {
                    var available_curr = data.currency;

                    $('#currencies-select-selling').empty();

                    var default_test = $('<option id="currencies-selling-default-val" value="Select a currency">Select a currency</option>');
                    $('#currencies-select-selling').append(default_test);

                    available_curr.forEach(function(gar) {
                        currencies(gar.CurrencyID, gar.Currency)
                    });
                }
            });

            function currencies(CurrencyID, Currency) {
                var available_currencies = $('<option value="'+ CurrencyID +'">'+ Currency +'</option>');

                $('#currencies-select-selling').append(available_currencies);
            }
        });

        $('#transact-date-button').click(function() {
            $('#ui-datepicker-div').toggle();
            $('#ui-datepicker-div').css('z-index' , '4');
            $('#ui-datepicker-div').css('position' , 'absolute');
            $('#ui-datepicker-div').css('top' , '145');
        });

        $('#or-number-selling').change(function() {
            $.ajax({
                url: "{{ route('branch_transactions.selling_transaction.or_number_duplicate_s') }}",
                type: "post",
                data: {
                    _token: "{{ csrf_token() }}",
                    current_or_number: $(this).val()
                }, success: function(data) {
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
                        if (data.boolean) {
                            dupeAlert();
                        } else {
                            $('#currencies-select-selling').removeAttr('disabled','disabled');
                        }
                    });
                }
            });

            function dupeAlert() {
                Swal.fire({
                    title: 'Duplicate entry',
                    icon: 'error',
                    text: 'Invoice mumber is already existing.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                }).then(() => {
                    $('#rate-used-true').val('');
                    $('#rate-used-selling').val('');
                    $('#or-number-selling').val('').attr('placeholder', 'OR Number');
                    $('#currencies-select-selling').attr('disabled','disabled');
                });
            }
        });

        $('#or-number-selling').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value.length > 11) {
                this.value = this.value.slice(0, 11);
            }
        });


        $('#available-bills-select-all').click(function() {
            var available_bills_total_amnt = 0;
            var rate_conf_check_stat = $(this).prop('checked');

            if (rate_conf_check_stat == true) {
                $('.serials-selling-selected').each(function() {
                    var row = $(this).closest('tr');

                    if (row.find('td:visible').length > 0) {
                        $(this).prop('checked', true);
                    }
                });
            } else {
                $('.serials-selling-selected').each(function() {
                    $(this).prop('checked', false);
                });
            }

            $('.serials-selling-selected:checked').each(function() {
                var serialAmount = parseFloat($(this).attr('data-serialamount')) || 0;
                available_bills_total_amnt += serialAmount;
            });

            $('#available-bills-total-amount-input').val(available_bills_total_amnt);
            $('#available-bills-total-amount').text(available_bills_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));

            available_bills_total_amnt = 0;
        });

        $(document).on('change', '.serials-selling-selected', function() {
            var available_bills_total_amnt = 0;

            $('.serials-selling-selected:checked').each(function() {
                var serialAmount = parseFloat($(this).attr('data-serialamount')) || 0;
                available_bills_total_amnt += serialAmount;
            });

            $('#available-bills-total-amount-input').val(available_bills_total_amnt);
            $('#available-bills-total-amount').text(available_bills_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));

            available_bills_total_amnt = 0;
        });

        // Fetch values for the ajax request for the populate currencies
        $('#currencies-select-selling').change(function() {
            var selected_curr_id = $(this).val();
            
            clearTotalCurrAmnt();
            getSerialDetails(selected_curr_id);
        });

        // AJAX request for the serialDetails function - SellingTransactController
        function getSerialDetails(selected_curr_id = null, rset_value) {
            // var selected_r_set = $('input[name="radio-rset"]:checked').val();

            $.ajax({
                url: "{{ route('branch_transactions.selling_transaction.serial_detials') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    selected_curr_id: selected_curr_id,
                    selected_r_set: 'B'
                },
                success: function(data) {
                    var rate_used_selling = data.rate_used_selling;
                    var serial_stock_selling = data.avaible_serials_currency;
                    var selling_variance = data.rate_config[0].VarianceSelling;

                    if (serial_stock_selling == '') {
                        Swal.fire({
                            icon: 'info',
                            text: 'No stock for this currency.',
                            customClass: {
                                popup: 'my-swal-popup',
                            }
                        });

                        $('#rate-used-true').val('');
                        $('#rate-used-selling').val('');
                        $('#button-add-serial').attr('disabled', 'disabled');
                    } else {
                        clearStock();

                        $('#container-test').fadeIn("fast");
                        $('#container-test').css('display', 'block');

                        setTimeout(function() {
                            $('#container-test').fadeOut("fast");

                            serial_stock_selling.forEach(function(avail_serials_appended) {
                                var table = $('#serial-stock-table-appended');
                                var new_row_selling = $('<tr class="data-serials-tr" data-serials="'+ avail_serials_appended.Serials +'" data-billamount="'+ avail_serials_appended.BillAmount +'">');
                                var action_cell = $('<td class="text-center text-sm p-1"><input class="form-check-input serials-selling-selected" type="checkbox" name="select-serials-radio-appended" value="'+ avail_serials_appended.Serials +'" data-serialamount="'+ avail_serials_appended.BillAmount.toFixed(2) +'" data-serialfsid="'+ avail_serials_appended.FSID +'" readonly></td>');
                                // var serials_cell = $('<td class="text-center text-sm"><span class="serials-appended-span">'+ avail_serials_appended.Serials +'</span> Rset: <strong>('+ avail_serials_appended.Rset +')</strong></td>');
                                var serials_cell = $('<td class="text-center text-sm p-1"><span class="serials-appended-span">'+ avail_serials_appended.Serials +'</span> </td>');
                                var bill_amount_cell = $('<td class="text-right text-sm py-1 px-3">'+ avail_serials_appended.BillAmount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');

                                // available_bills_total_amnt += avail_serials_appended.BillAmount;

                                new_row_selling.append(action_cell);
                                new_row_selling.append(serials_cell);
                                new_row_selling.append(bill_amount_cell);

                                table.find('tbody').append(new_row_selling);
                                new_row_selling.hide().fadeIn(250);
                            });

                            if (serial_stock_selling.length >= 10) {
                                $('#serial-stock-container').css({
                                    height: '320px'
                                });

                            // table_height = serial_stock_selling.length * 10;
                            } else if (serial_stock_selling.length <= 10) {
                                $('#serial-stock-container').css({
                                    height: 'auto'
                                });
                            }

                            rate_used_selling.forEach(function(rate_selling) {
                                rateSelling(rate_selling.CRID, rate_selling.CurrencyID, rate_selling.Rate, rate_selling.EntryDateTime, rate_selling.EntryDate, selling_variance);
                            });

                            var get_rate_val = $('#rate-used-selling').val();

                            if (get_rate_val != '') {
                                $('#button-add-serial').removeAttr('disabled', 'disabled');
                                $('#serial-stock-modal-button').removeAttr('disabled', 'disabled');
                            }

                            denominations(data.denoms[0]);
                        });
                    }
                }
            });
        }
        
        // UI/UX - Clear the modal for stock
        function clearStock() {
            $('#total-amnt-selling').text('0.00');
            $('#true-total-amnt-selling').val('');
            $('#currency-amnt-selling-new').val('');
            $('#true-currency-amnt-selling').val('');
            $('#serial-stock-table tbody tr').empty();
            $('#available-bills-total-amount').text('');
            $('#serial-stock-table-appended tbody tr').empty();
            $('#transaction-confirm-button').prop('disabled', true)
            $('#denomination-filter').empty().append('<option value="default">All</option>');
        }

        // Rate fecthing of the currency selected
        function rateSelling(CRID, CurrencyID, Rate, EntryDateTime, EntryDate, selling_variance) {
            var rate_selling_trans = Rate;
            var rate_currency_id = CurrencyID;
            var rate_used_text_formatted = parseFloat(rate_selling_trans) + parseFloat(selling_variance);

            $('#rate-used-selling').val(rate_used_text_formatted);
            $('#rate-used-selling-curr-id').val(rate_currency_id);
            $('#rate-used-true').val(parseFloat(rate_selling_trans) + parseFloat(selling_variance));
        }

        var selected_row_element = $('input[name="select-serials-radio"]:checked').closest('tr');
        var total_curr_amount = 0;
        var computed_total_amount_selling = 0;
        var counter = 0;
        var serial_count = 0;

        // UI/UX - selecting a bill to sell thru modal
        $('body').on('click', '#button-add-serial', function() {
            serial_count = $('.new-serial-container').length;
            var selected_serial_bills = [];

            var row = $(this).closest('.new-serial-container');
            var serial_stock_element = row.find('.serial-stock-appended-data');
            var serial_stock_data = serial_stock_element.attr("data-newserialstock");
            var selected_row_element_appnd = $('input[name="select-serials-radio-appended"]:checked').closest('tr');

            $('#select-serial-stock-appended').off("click").on("click", function() {
                var selected_serial_serial = [];

                $('#transaction-confirm-button').removeAttr('disabled');
                var selected_serial_amount = $('input[name="select-serials-radio-appended"]:checked').attr('data-serialamount');
                var selected_serial_fsid = $('input[name="select-serials-radio-appended"]:checked').attr('data-serialfsid');
                var selected_radio_appnd = $('input[name="select-serials-radio-appended"]:checked');

                var serial_input = row.find('.serial-input-appended-data');
                var serial_fsid_selling = row.find('.bill-serial-fsid-selling');
                var serial_bill_amnt_selling = row.find('.bill-amnt-selling');
                var serial_curr_amnt_selling = row.find('.currency-amnt-selling');
                var bill_serials_delete = row.find('.btn-danger-serial');
                var serial_input_data = serial_input.attr("data-serialinputcount");

                $('input[name="select-serials-radio-appended"]:checked').each(function() {
                    selected_serial_serial.push($(this).val());
                });

                if (selected_serial_serial.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        text: 'Select a serial to sell.',
                        customClass: {
                            popup: 'my-swal-popup',
                        }
                    });
                } else if (selected_serial_serial.length > 40) {
                    Swal.fire({
                        icon: 'error',
                        text: 'Selected serials limit has been reached.',
                        customClass: {
                            popup: 'my-swal-popup',
                        }
                    });
                } else if (serial_count + selected_serial_serial.length > 40) {
                    Swal.fire({
                        icon: 'error',
                        text: 'Selected serials limit has been reached.',
                        customClass: {
                            popup: 'my-swal-popup',
                        }
                    });
                } else {
                    if (selected_row_element_appnd) {
                        var selected_row_appnd = selected_radio_appnd.closest('tr');
                        selected_row_appnd.find('td').fadeOut("fast");
                    }

                    Swal.fire({
                        title: 'Serial Selected!',
                        icon: 'success',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        timer: 750,
                    }).then(() => {
                        var selected_serials_data = [];
                        $('#available-bills-total-amount-input').val(0);
                        $('#available-bills-total-amount').text("0.00");

                        $('.serials-selling-selected').each(function() {
                            var checked_items = $(this).prop('checked');

                            if (checked_items == true) {
                                var serial = $(this).val();
                                var fsid = $(this).attr('data-serialfsid');
                                var bill_amount = $(this).attr('data-serialamount');

                                selected_serials_data.push({
                                    serial: serial,
                                    fsid: fsid,
                                    bill_amount: bill_amount,
                                });

                                selected_serial_bills.push(bill_amount);
                            }

                            $(this).prop('checked', false);
                        });

                        if (counter <= counter++) {
                            selected_serials_data.forEach(function(values) {
                                var new_serial_container = $('#new-serial-container');
                                var new_serial_row =
                                    `<div class="row align-items-center px-3 my-3 new-serial-container" id="new-serial-container">
                                        <div class="col-12">
                                            <div class="row align-items-center">
                                                <div class="col-3">
                                                    <strong>
                                                        {{ trans('labels.selling_trans_serial_sell') }}:
                                                    </strong>
                                                </div>
                                                <div class="col-5">
                                                    <input type="text" class="form-control form-control-sm serial-input-appended-data text-center" name="bill-serial-selling[]" autocomplete="off" placeholder="Select a serial" data-serialinputcount="`+ counter +`" readonly value="`+ values.serial +`">
                                                    <input type="hidden" class="form-control bill-serial-fsid-selling" name="bill-serial-fsid-selling[]" readonly value="`+ values.fsid +`">
                                                </div>
                                                <div class="col-3">
                                                    <input type="text" class="form-control form-control-sm currency-amnt-selling text-right" name="currency-amnt-selling[]" autocomplete="off" placeholder="0.00" readonly value="`+ values.bill_amount +`">
                                                    <input type="hidden" class="form-control bill-amnt-selling" name="bill-amnt-selling[]" autocomplete="off" placeholder="0.00" readonly value="`+ values.bill_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +`">
                                                </div>
                                                <div class="col-1 text-end">
                                                    <button class="btn btn-danger btn-danger-serial pe-2" id="button-delete-serial" type="button" value="`+ values.serial +`"><i class='bx bx-trash'></i></button>
                                                    <input type="hidden" class="serial-stock-appended-data" data-newserialstock="`+ counter +`">
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;

                                new_serial_container.append(new_serial_row).fadeIn("fast");
                            });

                            serial_input.val(selected_serial_serial);

                            selected_serial_bills.forEach(function(bill_val) {
                                total_curr_amount += parseInt(bill_val);
                            });

                            var bill_stocks_amnt = $('#available-bills-total-amount-input').val() - total_curr_amount;

                            $('#true-currency-amnt-selling').val(total_curr_amount);
                            $('#currency-amnt-selling-new').val(total_curr_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})).text(total_curr_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                            // $('#available-bills-total-amount-input').val(bill_stocks_amnt);
                            // $('#available-bills-total-amount').text(bill_stocks_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));

                            setTimeout(function() {
                                $('#search-serials-available').val('');
                                $('#serial-stock-modal-appended').modal('hide');

                                setInterval(function() {
                                    var e = $.Event('keyup');
                                    e.which = 8;

                                    $('#search-serials-available').trigger(e);
                                }, 200);
                            });

                            var rate_used_selling = $('#rate-used-selling').val();
                            var new_total_amount = total_curr_amount * rate_used_selling;

                            var integer_part = Math.floor(new_total_amount);
                            var decim_part = new_total_amount - integer_part;

                            if (decim_part > 0.1 && decim_part <= 0.25) {
                                decim_part = 0.25;
                            } else if (decim_part > 0.25 && decim_part <= 0.50) {
                                decim_part = 0.50;
                            } else if (decim_part > 0.50 && decim_part <= 0.75) {
                                decim_part = 0.75;
                            } else if (decim_part > 0.75 && decim_part < 1) {
                                decim_part = 1;
                            }

                            var rounded_total_amnt = integer_part + decim_part;

                            $('#total-amnt-selling').val(rounded_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})).text(rounded_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                            $('#true-total-amnt-selling').val(rounded_total_amnt);
                        }
                    });

                    selected_serials_data = [];
                }

                selected_serial_bills = [];
            });
        });

        function clearTotalCurrAmnt() {
            total_curr_amount = 0;
        }

        var counter = 0;

        // UI/UX - remove appended element and implementation of auto compute after removing the appended element (bill to sell)
        $('body').on('click', '.btn-danger-serial', function deleteSerial() {
            counter-= 1;
            selected_serial_bills = [];

            $('#available-bills-total-amount-input').val(0);
            $('#available-bills-total-amount').text("0.00");

            var container = $(this).closest('.new-serial-container');
            var bill_amtn_selling = container.find('.bill-amnt-selling').val();
            var curr_amnt_selling = container.find('.currency-amnt-selling').val();
            var bills_serials_appnd = container.find('.serial-input-appended-data').val();
            var rate_used_true = $('#rate-used-true').val();

            var bill_serials = container.find('.btn-danger-serial').val();

            var table = $('#serial-stock-table-appended');
            var serialRow = table.find('tr[data-serials="'+ bill_serials +'"]');

            if (serialRow.length > 0) {
                serialRow.find('[style="display: none;"]').removeAttr('style').fadeIn("fast");
                serialRow.find('input[name="select-serials-radio-appended"]').removeAttr('checked');
            }

            var total_amnt_selling_delete = $('#true-total-amnt-selling').val();
            var computed_amnt_selling = rate_used_true * bill_amtn_selling;
            var new_subtracted_total_amnt = total_amnt_selling_delete - computed_amnt_selling;

            if (new_subtracted_total_amnt < 0) {
                $('#true-total-amnt-selling').attr('placeholder', '0.00');
                $('#total-amnt-selling').text('0.00');
            } else {
                $('#true-total-amnt-selling').val(new_subtracted_total_amnt);
                $('#total-amnt-selling').val(new_subtracted_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})).text(new_subtracted_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            }

            if (isNaN(new_subtracted_total_amnt)) {
                $('#true-total-amnt-selling').attr('placeholder', '0.00');
                $('#total-amnt-selling').text('0.00');
            } else {
                $('#true-total-amnt-selling').val(new_subtracted_total_amnt);
                $('#total-amnt-selling').val(new_subtracted_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})).text(new_subtracted_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            }

            var curr_amnt_selling_new = $('#true-currency-amnt-selling').val();
            var subtracted_curr_bill_amnt = parseFloat(curr_amnt_selling_new) - parseFloat(bill_amtn_selling);

            function testFlush() {
                var subtracted_curr_bill_amnt = parseFloat(curr_amnt_selling_new) - parseFloat(bill_amtn_selling);
            }

            if (isNaN(subtracted_curr_bill_amnt)) {
                $('#currency-amnt-selling-new').attr('placeholder', '0.00');
            } else {
                var get = $('#currency-amnt-selling-new').val(subtracted_curr_bill_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                total_curr_amount = parseFloat(curr_amnt_selling_new) - parseFloat(bill_amtn_selling);

                $('#true-currency-amnt-selling').val(total_curr_amount);
            }

            $(this).parents(".new-serial-container").remove();

            var get_total_curr_amnt = $('#true-currency-amnt-selling').val();

            if (parseInt(get_total_curr_amnt) == 0) {
                $('#transaction-confirm-button').attr('disabled', 'disabled');
            }

            var bill_stocks_amnt = parseFloat($('#available-bills-total-amount-input').val()) + parseFloat(bill_amtn_selling);

            // $('#available-bills-total-amount-input').val(bill_stocks_amnt);
            // $('#available-bills-total-amount').text(bill_stocks_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
        });

        // UI/UX - clear fields on change and clear all when value is set to default
        $('#currencies-select-selling').change(function() {
            var selected_curr_id = $(this).val();

            $('#bill-amnt-selling').val('');
            $('#total-amnt-selling').val('');
            $('#bill-serial-selling').val('');
            $('#currency-amnt-selling').val('');
            $('#true-total-amnt-selling').val('');

            if (selected_curr_id == 'default-val') {
                $('#rate-used-selling').val('').text('');
                $('.serial-input-appended-data').val('');
                $('#button-add-serial').attr('disabled', 'disabled');
                $('#transaction-confirm-button').attr('disabled', 'disabled');
                $('#serial-stock-modal-button').attr('disabled', 'disabled');
            }

            $('.bill-amnt-selling').val('');
            $('.currency-amnt-selling').val('');
            $('.bill-serial-fsid-selling').val('');
            $('.serial-input-appended-data').val('');

            $('.new-serial-container').remove();
            $('#total-amnt-selling').val('');
        });

        // Final amount auto compute when rate is changed
        $('#rate-used-selling').keyup(function() {
            var rate_used_keyup_selling = $(this).val();
            var bill_amnt_selling = $('#bill-amnt-selling').val();

            var new_total_amount_selling = parseFloat(rate_used_keyup_selling) * bill_amnt_selling;

            $('#total-amnt-selling').val(new_total_amount_selling.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})).text(new_total_amount_selling.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            $('#true-total-amnt-selling').val(new_total_amount_selling.toFixed(2));
        });

        var new_total_amnt_selling = 0;

        // Rate change restriction
        $('#rate-used-selling').on('change', function() {
            var rate_used_selling_change = parseFloat($(this).val());
            var get_rate_used_true = parseFloat($('#rate-used-true').val());

            if (rate_used_selling_change < get_rate_used_true) {
                Swal.fire({
                    text: "Rate change can't be lower than the Sinag Rate.",
                    icon: 'warning',
                    showCancelButton: false,
                }).then(() => {
                    var final_total_amnt_selling = 0;

                    $('.currency-amnt-selling').each(function() {
                        var each_total_amnt = parseFloat($(this).val());
                        final_total_amnt_selling += each_total_amnt;
                    });

                    var used_rate = parseFloat($('#rate-used-true').val());
                    var total_amnt_selling_computed = final_total_amnt_selling * used_rate;

                    $('#total-amnt-selling').val(total_amnt_selling_computed.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 })).text(total_amnt_selling_computed.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#true-total-amnt-selling').val(total_amnt_selling_computed).text(total_amnt_selling_computed.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                });

                $('#rate-used-true').val(get_rate_used_true);
                $('#rate-used-selling').val(get_rate_used_true);
            }
        });

        $('.get-customer-details-selling').click(function() {
            $('.radio-button-selling').removeAttr('disabled');
        });

        // Verification - Checking of available stocks
        var availalbe_stocks_count = $('#available-serials-count').val();
        var redirect_pending_serials = $('#full-url-addnewbuying').val();

        if (availalbe_stocks_count == 0) {
            Swal.fire({
                icon: 'warning',
                text: 'No available bills to sell. Complete pending serials or create a buying transaction.',
                customClass: {
                    popup: 'my-swal-popup',
                },
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#container-test').fadeIn("slow");
                    $('#container-test').css('display', 'block');

                    var url = "{{ route('branch_transactions.pending_serials') }}";

                    window.location.href = url;
                }
            });
        } else {
            return;
        }

        function denominations(denoms) {
            var denoms_array = denoms.denominations.split(',');

            denoms_array.forEach(function(pang_lolo) {
                var options = $(`<option valuE="${pang_lolo}">${parseFloat(pang_lolo).toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</option>`);

                $('#denomination-filter').append(options);
            });
        }
    });

    $(document).ready(function() {
        let denomination = '';
        let search_serial = '';

        $('#denomination-filter').change(function() {
            denomination = $(this).val();

            searchSerial(search_serial, denomination);
        });

        $('#search-serials-available').keyup(function(){
            var search_serial = $(this).val();

            searchSerial(search_serial, denomination);
        });

        let regex_serial = '';
        let regex_denom = '';

        function searchSerial(search_serial, denomination) {
            regex_serial = new RegExp(search_serial, 'i');
            regex_denom = new RegExp('^' + parseInt(denomination) + '$', 'i');

            var serials_table = $('#serial-stock-table-appended');
            var available_bills_total_amnt = 0;

            serials_table.find("tbody .data-serials-tr").each(function() {
                var serial = $(this).attr('data-serials');
                var denom = $(this).attr('data-billamount');

                if ((denomination == 'default' || denomination == '')) {
                    if (regex_serial.test(serial)) {
                        $(this).show();
                        $(this).addClass("search-highlight");

                        $('#serial-stock-container').css({
                            height: 'auto'
                        });

                        if ($(this).find('td').is(':visible')) {
                            var searched_bill_amount = parseFloat($(this).find('.serials-selling-selected').data('serialamount'));
                            available_bills_total_amnt += searched_bill_amount;
                        }
                    } else {
                        $(this).hide();
                    }

                    if (search_serial == '') {
                        $(this).removeClass("search-highlight");

                        $('#serial-stock-container').css({
                            height: 'auto'
                        });
                    }
                } else {
                    if (regex_denom.test(denom)) {
                        $(this).show();
                        $(this).addClass("search-highlight");

                        $('#serial-stock-container').css({
                            height: 'auto'
                        });

                        if (regex_serial.test(serial)) {
                            $(this).show();
                            $(this).addClass("search-highlight");

                            $('#serial-stock-container').css({
                                height: 'auto'
                            });

                            if ($(this).find('td').is(':visible')) {
                                var searched_bill_amount = parseFloat($(this).find('.serials-selling-selected').data('serialamount'));
                                available_bills_total_amnt += searched_bill_amount;
                            }
                        } else {
                            $(this).hide();
                        }
                    } else {
                        $(this).hide();
                    }
                }

                // if (regex.test(serial)) {
                //     $(this).show();

                //     if ($(this).find('td').is(':visible')) {
                //         var searched_bill_amount = parseFloat($(this).find('.serials-selling-selected').data('serialamount'));
                //         available_bills_total_amnt += searched_bill_amount;
                //     }
                // } else {
                //     $(this).hide();
                // }
            });

            // $('#available-bills-total-amount').text(available_bills_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
        }
        
        $('#transaction-confirm-button').on('click',function() {
            if (!$('#true-total-amnt-selling').val()) {
                $(this).prop('disabled',  true);
            } else {
                $('#security-code-modal').modal("show");
            }
        });
    });

    // Verification of selling transaction through security code
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
                            text: 'Selling transaction successfully added!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            var form_data = new FormData($('#selling-transact-form')[0]);
                            form_data.append('matched_user_id', matched_user_id);

                            $.ajax({
                                url: "{{ route('branch_transactions.selling_transaction.save') }}",
                                type: "post",
                                data: form_data,
                                contentType: false,
                                processData: false,
                                cache: false,
                                success: function(data) {
                                    var route = "{{ route('branch_transactions.selling_transaction.details', ['id' => ':id']) }}";
                                    var url = route.replace(':id', data.latest_scid);

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

    // Redirect - Auto print of the buying transaction receipt
    // $(document).ready(function() {
    //     var forex_serials_base_url = $('#full-url-serials').val();
    //     var forex_serials_ftdid = $('#serials-ftdid').val();
    //     var forex_serials_full_url = forex_serials_base_url + '/' + forex_serials_ftdid;

    //     if (window.location.href == forex_serials_full_url) {
    //         $("#printing-receipt-buying").click();
    //     }
    // });
</script>
