{{-- Transfer Forex --}}
<script>
    $(document).ready(function() {
        $('#transfer-forex-bill-select-all').click(function() {
            var rate_conf_check_stat = $(this).prop('checked');

            if (rate_conf_check_stat == true) {
                $('.transfer-forex-bill-select-one').each(function() {
                    $(this).prop('checked', true);
                });
            } else {
                $('.transfer-forex-bill-select-one').each(function() {
                    $(this).prop('checked', false);
                });
            }
        });

        $('input[name="radio-transfer-type"]').click(function() {
            var transfer_type = $('input[name="radio-transfer-type"]:checked').val();
            var transfer_type_text = $('input[name="radio-transfer-type"]:checked').attr('data-transfertype');

            $('#transfer-confirm-button').removeAttr('disabled');
            $('#transfer-forex-remarks').val(transfer_type_text).fadeIn("fast");

            $.ajax({
                url: "{{ route('branch_transactions.transfer_forex.serials') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    selected_trans_type_id: transfer_type
                },
                success: function(data) {
                    clearTable();
                    var tracking_number = data.tracking_number;
                    var transferrable_bills = data.bills_for_transfer;
                    var data_count = transferrable_bills.length;

                    if (transferrable_bills == '') {
                        Swal.fire({
                            text: "No bill(s) available for transfer.",
                            icon: 'warning',
                            showConfirmButton: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        });

                        var transferable_table = $('#transfer-forex-bill-select-table');
                        var no_bill_cell =
                        `<tr class="text-center text-td-buying no-bills-transfer-cell" id="no-bills-transfer-cell">
                            <td class="text-center text-td-buying text-sm py-3" colspan="8">
                                <span class="buying-no-transactions text-lg">
                                    <strong>NO AVAILABLE BILLS FOR THIS TRANSACTION TYPE</strong>
                                </span>
                            </td>
                        </tr>`;

                        $('#transfer-forex-bill-container').css('height', '130px');

                        transferable_table.find('tbody').append(no_bill_cell).hide().fadeIn(250);
                    } else {
                        $('#container-test').fadeIn("slow");
                        $('#container-test').css('display', 'block');

                        // var tracking_numbers = $('<option value="">Select tracking number</option>');
                        // $('#tracking-number').append(tracking_numbers);

                        setTimeout(function() {
                            empList();
                            $('#container-test').fadeOut("slow");

                            transferrable_bills.forEach(function(filtered_bills) {
                                filterBills(filtered_bills.Currency, filtered_bills.BillAmount, filtered_bills.Serials, filtered_bills.TransactionDate, filtered_bills.ReceiptNo, filtered_bills.TransactionNo, filtered_bills.TransType,
                                filtered_bills.FSID, data_count);
                            });


                            // tracking_number.forEach(function(gar) {
                            //     trackingNumber(gar.TrackingID, gar.TrackingNumber);
                            // });
                        });
                    }
                }
            });
        });

        function empList() {
            $('.transfer-forex-bill-select-one').click(function() {
                var rate_conf_check_stat = $(this).prop('checked');

                var all_checked = $('.transfer-forex-bill-select-one').length === $('.transfer-forex-bill-select-one:checked').length;

                if (all_checked) {
                    $('#transfer-forex-bill-select-all').prop('checked', true);
                } else {
                    $('#transfer-forex-bill-select-all').prop('checked', false);
                }
            });
        }

        $('#transfer-confirm-button').click(function() {
            var selected_bill = [];
            var selected_bill_fsid = [];
            var selected_bill_amount = [];
            var selected_bill_serial = [];
            var selected_bill_currency = [];
            var courier_transfer = $('#transfer-forex-courier').val();
            var transfer_type = $('input[name="radio-transfer-type"]').val();

            $('.transfer-forex-bill-select-one:checked').each(function() {
                selected_bill.push($(this).val());
                selected_bill_fsid.push($(this).attr('data-serialfsid'));
                selected_bill_serial.push($(this).attr('data-serial'));
                selected_bill_amount.push($(this).attr('data-serialbillamount'));
                selected_bill_currency.push($(this).attr('data-serialcurrency'));
            });

            var select_bill_count = selected_bill_currency.length;

            $('#bill-for-transfer-table-body').empty();
            clearTransferSummary();

            $('#bill-cash-count #bill-cash-count-body').empty();

            billForTransfer(selected_bill_currency, selected_bill_amount, selected_bill_serial, select_bill_count);

            if (selected_bill == '') {
                Swal.fire({
                    icon: 'error',
                    text: 'Select a bill to transfer.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
                $('#transfer-forex-selected-bill').val('');
            } else if (transfer_type == '') {
                Swal.fire({
                    icon: 'error',
                    text: 'Select a transfer type.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else if (courier_transfer == 'Select a courier') {
                Swal.fire({
                    icon: 'error',
                    text: 'Select a courier.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else {
                $('#bill-for-transfer-modal').modal('show');
                var processed_bill_array = selected_bill_fsid.join(",");
                $('#transfer-forex-selected-bill').val(processed_bill_array);
            }
        });

        $('#proceed-transfer').click(function() {
            $('#security-code-modal').modal("show");
            $('#bill-for-transfer-modal').modal('hide');
        });

        $('#proceed-transaction').click(function() {
            $(this).prop('disabled', true);

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
                            text: 'Transfer forex successful!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            var form_data = new FormData($('#transfer-forex-form')[0]);
                            form_data.append('matched_user_id', matched_user_id);
                            form_data.append('tracking_number', $('#tracking-number').val());
                            form_data.append('tracking_id', $('#tracking-number').find('option:selected').data('trackingid'));

                            $.ajax({
                                url: "{{ route('branch_transactions.transfer_forex.save') }}",
                                type: "post",
                                data: form_data,
                                contentType: false,
                                processData: false,
                                cache: false,
                                success: function(data) {
                                    var url = "{{ route('branch_transactions.transfer_forex') }}";

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
                        });

                        $('#proceed-transaction').prop('disabled', false);
                    }
                }
            });
        });

        var bill_for_transfer = $('#bills-for-transfer').val();
        var redirect_pending_serials = $('#full-url-addnewbuying').val();

        if (bill_for_transfer == 0) {
            Swal.fire({
                icon: 'warning',
                text: 'No available bill(s) to transfer.',
                customClass: {
                    popup: 'my-swal-popup',
                },
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
            });
        } else {
            return;
        }

        // function trackingNumber(TrackingID, TrackingNumber) {
        //     var select_option = $('#tracking-number');
        //     var tracking_numbers = $('<option value="'+ TrackingNumber +'" data-trackingid="'+ TrackingID +'">'+ TrackingNumber +'</option>');

        //     select_option.append(tracking_numbers);
        //     select_option.removeAttr('disabled', 'disabled');
        // }

        function filterBills(Currency, BillAmount, Serials, TransactionDate, ReceiptNo, TransactionNo, TransType, FSID, data_count) {
            var transferable_table = $('#transfer-forex-bill-select-table');
            var new_row = $('<tr class="text-center text-td-buying">');
            var select_tranferable = $('<td class="py-1 px-3"><div class="row align-items-center"><div class="text-rate-maintenance col-12 px-0"><input class="form-check-input transfer-forex-bill-select-one" type="checkbox" id="transfer-forex-bill-select" name="transfer-forex-bill-select" data-serialfsid="'+ FSID +'" data-serial="'+ Serials +'" data-serialcurrency="'+ Currency +'" data-serialbillamount="'+ BillAmount +'" checked></div></div></td>');
            var transferable_currency = $('<td class="text-center text-sm whitespace-nowrap p-1">'+ Currency +'</td>');
            var transferable_bill_amnt = $('<td class="text-right text-sm whitespace-nowrap py-1 px-3">'+ BillAmount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
            var transferable_serials = $('<td class="text-center text-sm whitespace-nowrap p-1"><strong>'+ Serials +'</strong></td>');
            var transferable_trans_date = $('<td class="text-center text-sm whitespace-nowrap p-1">'+ TransactionDate +'</td>');
            var transferable_receipt_no = $('<td class="text-center text-sm whitespace-nowrap p-1">'+ ReceiptNo +'</td>');
            var transferable_trans_no = $('<td class="text-center text-sm whitespace-nowrap p-1">'+ TransactionNo +'</td>');
            var transferable_trans_type = $('<td class="text-center text-sm whitespace-nowrap p-1">'+ TransType +'</td>');

            new_row.append(select_tranferable);
            new_row.append(transferable_trans_date);
            new_row.append(transferable_trans_no);
            // new_row.append(transferable_receipt_no);
            new_row.append(transferable_trans_type);
            new_row.append(transferable_currency);
            new_row.append(transferable_serials);
            new_row.append(transferable_bill_amnt);

            transferable_table.find('tbody').append(new_row);

            if (data_count > 15) {
                $('#transfer-forex-bill-container').css({
                    height: '480px'
                });
            } else if (data_count < 15) {
                $('#transfer-forex-bill-container').css({
                    height: 'auto'
                });
            }

            new_row.hide().fadeIn(250);
        }

        function billForTransfer(selected_bill_currency, selected_bill_amount, selected_bill_serial, select_bill_count) {
            var total_amount_transfer = 0;
            var bill_count = 0;

            var bills_for_transfer_table = $('#bill-for-transfer-table');

            var transferable_bill_details = selected_bill_serial.map(function(serials_val, serials_index) {
                return {
                    currency: selected_bill_currency[serials_index],
                    serials: serials_val,
                    bill_amount: selected_bill_amount[serials_index],
                };
            });

            transferable_bill_details.forEach(function(gar) {
                var new_row_modal = $('<tr class="text-center text-td-buying">');
                var transferable_currency = $('<td class="text-center text-sm p-1">'+ gar.currency +'</td>');
                var transferable_bill_amnt = $('<td class="text-right text-sm py-1 px-3">'+ parseFloat(gar.bill_amount).toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
                var transferable_serials = $('<td class="text-center text-sm p-1"><strong>'+ gar.serials +'</strong></td>');

                total_amount_transfer += parseFloat(gar.bill_amount);

                bill_count = selected_bill_currency.length;

                new_row_modal.append(transferable_currency);
                new_row_modal.append(transferable_bill_amnt);
                new_row_modal.append(transferable_serials);

                bills_for_transfer_table.find('tbody').append(new_row_modal);
                new_row_modal.hide().fadeIn(250);
            });

            var transfer_fx_cash_count = transferable_bill_details.reduce((result, bill_details) => {
                const currency = bill_details.currency;
                const bill_amount = parseFloat(bill_details.bill_amount);

                if (!result[currency]) {
                    result[currency] = {
                        currency: currency,
                        count: 0,
                        total_amount: 0,
                    };
                }

                result[currency].count++;
                result[currency].total_amount += bill_amount;

                return result;
            }, {});

            Object.keys(transfer_fx_cash_count).forEach(function(currency) {
                const cash_count = transfer_fx_cash_count[currency];

                var row_footer = $('#bill-cash-count');
                var cash_count_row = $('<tr>');
                var currency = $('<td class="text-black text-center text-sm p-1">'+ cash_count.currency +'</td>');
                var count = $('<td class="text-black text-center text-sm p-1">'+ cash_count.count +'</td>');
                var total_amount = $('<td class="text-black text-right text-sm py-1 px-3"><strong>'+ cash_count.total_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</strong></td>');

                cash_count_row.append(currency);
                cash_count_row.append(count);
                cash_count_row.append(total_amount);

                row_footer.find('tbody').append(cash_count_row);
                cash_count_row.hide().fadeIn(250);
            });

            if (bill_count > 15) {
                $('#transfer-summary-container').css({
                    height: '420px'
                });
            } else if (bill_count < 15) {
                $('#transfer-summary-container').css({
                    height: 'auto'
                });
            }
        }

        function clearTransferSummary() {
            $('#for-transfer-details').empty();
        }

        function clearTable() {
            $('#tracking-number').empty();
            $('#transfer-forex-bill-select-table #transfer-forex-bill-select-table-tbody').empty();
        }
    });

    // UI/UX - Auto populate of buying transaction date
    $(document).ready(function() {
        var current_date = new Date();
        var year = current_date.getFullYear();
        var month = String(current_date.getMonth() + 1).padStart(2, '0');
        var day = String(current_date.getDate()).padStart(2, '0');

        var formatted_date = year + '-' + month + '-' + day;

        var set_date = $('#transfer-forex-date').val(formatted_date).text(formatted_date);
    });
</script>
