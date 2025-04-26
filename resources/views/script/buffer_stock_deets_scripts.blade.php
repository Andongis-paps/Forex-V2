{{-- Buffer Declaration --}}
<script>
    $(document).ready(function() {
        const socketserver = "{{ config('app.socket_io_server') }}";
        const socket = io(socketserver);

        var selected_bill_total_amount = 0;
        var selected_bill_total_principal = 0;
        var selected_bill_count = [];
        var total_bills = [];

        $('.radio-buffer-type').change(function() {
            $('#selling-rate-container').toggleClass('d-none');
            $('#fields-container').toggleClass('d-none');

            if ($(this).val() == 1) {
                $('#proceed-transfer').prop('disabled', false);
            } else {
                $('#proceed-transfer').prop('disabled', true);
            }
        });

        $('#transfer-forex-buffer-select-all').click(function() {
            var sold_serials_check_stat = $(this).prop('checked');

            if (sold_serials_check_stat == true) {
                $('#selling-rate').prop('disabled', false);

                $('.transfer-forex-buffer-select-one').each(function() {
                    $(this).prop('checked', true);

                    selected_bill_total_amount += parseFloat($(this).attr('data-billamount'));
                    selected_bill_total_principal += parseFloat($(this).attr('data-principal'));
                    selected_bill_count.push($(this).attr('data-billamount'));
                });
            } else {
                $('#selling-rate').prop('disabled', true);

                $('.transfer-forex-buffer-select-one').each(function() {
                    $(this).prop('checked', false);

                    selected_bill_total_amount -= parseFloat($(this).attr('data-billamount'));
                    selected_bill_total_principal -= parseFloat($(this).attr('data-principal'));
                    var delete_index = selected_bill_count.indexOf($(this).attr('data-billamount'));

                    if (delete_index !== -1) {
                        selected_bill_count.splice(delete_index, 1);
                    }
                });
            }

            $('#selected-bill-count').text(selected_bill_count.length);

            if (selected_bill_total_amount <= 0) {
                $('#selling-rate').val('');
                $('#selling-rate').attr('placeholder', "0.00")
                // $('#selected-bill-total-amount').val(0).text('');

                $('#income').val("0.00");
                $('#true-income').val("0.00").attr('placeholder', "0.00");

                $('#exch-amount').val("0.00");
                $('#true-exch-amount').val("0.00").attr('placeholder', "0.00");

                $('#principal').val("0.00");
                $('#true-principal').val("0.00").attr('placeholder', "0.00");
            } else {
                $('#principal').val(selected_bill_total_principal.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                $('#true-principal').val(selected_bill_total_principal.toFixed(2));

                $('#selected-bill-total-amount').text(selected_bill_total_principal.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                $('#selected-bill-total-amount').val(selected_bill_total_amount.toFixed(2));
            }
        });

        $('.transfer-forex-buffer-select-one').click(function() {
            if ($(this).prop('checked') == true) {
                $('#selling-rate').prop('disabled', false);

                selected_bill_total_amount += parseFloat($(this).attr('data-billamount'));
                selected_bill_total_principal += parseFloat($(this).attr('data-principal'));
                selected_bill_count.push($(this).attr('data-billamount'));
            } else if ($(this).prop('checked') == false) {
                selected_bill_total_amount -= parseFloat($(this).attr('data-billamount'));
                selected_bill_total_principal -= parseFloat($(this).attr('data-principal'));
                var delete_index = selected_bill_count.indexOf($(this).attr('data-billamount'));

                if (delete_index !== -1) {
                    selected_bill_count.splice(delete_index, 1);
                }
            }

            $('#selected-bill-count').text(selected_bill_count.length);

            if (selected_bill_total_amount <= 0) {
                $('#selling-rate').val('');
                $('#selling-rate').attr('placeholder', "0.00")
                // $('#selected-bill-total-amount').val(0).text('');

                $('#income').val("0.00");
                $('#true-income').val("0.00").attr('placeholder', "0.00");

                $('#exch-amount').val("0.00");
                $('#true-exch-amount').val("0.00").attr('placeholder', "0.00");

                $('#principal').val("0.00");
                $('#true-principal').val("0.00").attr('placeholder', "0.00");
            } else {
                $('#principal').val(selected_bill_total_principal.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                $('#true-principal').val(selected_bill_total_principal.toFixed(2));

                $('#selected-bill-total-amount').text(selected_bill_total_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                $('#selected-bill-total-amount').val(selected_bill_total_amount.toFixed(2));
            }

            if (selected_bill_total_amount <= 0) {
                $('#selling-rate').prop('disabled', true);
            }

            // Check/uncheck select all checkbox script
            var all_checked = true;

            $('.transfer-forex-buffer-select-one').each(function() {
                if (!$(this).prop('checked')) {
                    all_checked = false;
                    return false;
                }
            });

            if (all_checked) {
                $('#transfer-forex-buffer-select-all').prop('checked', true);
            } else {
                $('#transfer-forex-buffer-select-all').prop('checked', false);
            }
        });

        // =========================================================================
            // $('#selling-rate').keyup(function() {
            //     var exchange_amnt = $(this).val() * $('#selected-bill-total-amount').val();
            //     var exch_amnt = exchange_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2});
            //     var true_income = exchange_amnt - $('#true-principal').val();
            //     var income = true_income.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2});

            //     if ($('#selected-bill-total-amount').val() > 0) {
            //         $('#exch-amount').val(exch_amnt);
            //         $('#true-exch-amount').val(exchange_amnt);

            //         $('#income').val(income);
            //         $('#true-income').val(true_income);
            //     } else {
            //         if ($(this).val() <= 0) {
            //             $('#income').val("0.00");
            //             $('#true-income').val("0.00").attr('placeholder', "0.00");
            //         }

            //         console.log($(this).val() <= 0);

            //         $('#principal').val("0.00");
            //         $('#true-principal').val("0.00").attr('placeholder', "0.00");

            //         $('#exch-amount').val("0.00");
            //         $('#true-exch-amount').val("0.00").attr('placeholder', "0.00");
            //     }
            // });
        // =========================================================================

        var FSIDs = '';
        var currency = '';
        var branch_id = '';
        var bill_amnt = '';
        var currency_id = '';

        $('.process-buffer').click(function() {
            // var parsed_serials_for_buffer = '';
            // var parse_bill_amounts = '';
            // var parse_bill_serials = '';
            FSIDs = $(this).attr('data-fsids');
            bill_amnt = $(this).attr('data-billamount');
            currency = $(this).attr('data-currency');
            currency_id = $(this).attr('data-currencyid');
            branch_id = $(this).attr('data-branchid');
            var selling_rate = $('#selling-rate').val();
            $('#selected-bill-total-amount').val(parseFloat(bill_amnt).toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));

            clear();
            clearTable();
            clearTransferSummary();

            bufferSummary($(this).attr('data-currency'), $(this).attr('data-fsids').split(",").length, $(this).attr('data-billamount'));

            // =======================================================================
                // var select_bill_count = selected_bill_currency.length;
                // billForBuffer(selected_fsid, selected_bills, selected_bill_serial, selected_bill_currency, select_bill_count);

                // parsed_serials_for_buffer = selected_fsid.join(", ");
                // parse_bill_amounts = selected_bills.join(", ");
                // parse_bill_serials = selected_bill_serial.join(", ");

                // if (selected_fsid.length == 0) {
                //     Swal.fire({
                //         text: 'No serials selected.',
                //         icon: 'error',
                //         showConfirmButton: true
                //     });
                // } else if (selling_rate <= 0) {
                //     Swal.fire({
                //         text: 'Selling rate is required.',
                //         icon: 'error',
                //         showConfirmButton: true
                //     });
                // } else {
                //     $.ajax({
                //         url: "{{ route('admin_transactions.buffer.cut_validation') }}",
                //         type: "POST",
                //         data: {
                //             _token: "{{ csrf_token() }}",
                //             parse_bill_amounts: parse_bill_amounts,
                //             parsed_serials_for_buffer: parsed_serials_for_buffer,
                //         },
                //         success: function(data) {
                //             if (data.validity == 0) {
                //                 Swal.fire({
                //                     text: 'No available stock in buffer stocks.',
                //                     icon: 'error',
                //                     showConfirmButton: true
                //                 });
                //             } else {
                //                 $('#buffer-cut-details').modal("show");
                //                 // $('#bill-for-buffer-modal').modal("show");
                //                 // bufferSerials(parsed_serials_for_buffer, parse_bill_amounts, branch_id);
                //             }
                //         }
                //     });
                // }
            // =======================================================================
        });

        // $('#declare-buffer-confirm-button').click(function() {
        //     var selected_fsid = [];
        //     var selected_bills = [];
        //     var selected_bill_serial = [];
        //     var selected_bill_currency = [];
        //     var selling_rate = $('#selling-rate').val();

        //     $('.transfer-forex-buffer-select-one:checked').each(function() {
        //         selected_fsid.push($(this).attr('data-fsid'));
        //         selected_bills.push($(this).attr('data-billamount'));
        //         selected_bill_serial.push($(this).attr('data-serials'));
        //         selected_bill_currency.push($(this).attr('data-currency'));
        //     });

        //     clearTable();
        //     clearTransferSummary();

        //     var select_bill_count = selected_bill_currency.length;

        //     billForBuffer(selected_fsid, selected_bills, selected_bill_serial, selected_bill_currency, select_bill_count);

        //     parsed_serials_for_buffer = selected_fsid.join(", ");
        //     parse_bill_amounts = selected_bills.join(", ");
        //     parse_bill_serials = selected_bill_serial.join(", ");

        //     if (selected_fsid.length == 0) {
        //         Swal.fire({
        //             text: 'No serials selected.',
        //             icon: 'error',
        //             showConfirmButton: true
        //         });
        //     } else if (selling_rate <= 0) {
        //         Swal.fire({
        //             text: 'Selling rate is required.',
        //             icon: 'error',
        //             showConfirmButton: true
        //         });
        //     } else {
        //         $.ajax({
        //             url: "{{ route('admin_transactions.buffer.cut_validation') }}",
        //             type: "POST",
        //             data: {
        //                 _token: "{{ csrf_token() }}",
        //                 parse_bill_amounts: parse_bill_amounts,
        //                 parsed_serials_for_buffer: parsed_serials_for_buffer,
        //             },
        //             success: function(data) {
        //                 if (data.validity == 0) {
        //                     Swal.fire({
        //                         text: 'No available stock in buffer stocks.',
        //                         icon: 'error',
        //                         showConfirmButton: true
        //                     });
        //                 } else {
        //                     $('#buffer-cut-details').modal("show");
        //                     // $('#bill-for-buffer-modal').modal("show");
        //                     // bufferSerials(parsed_serials_for_buffer, parse_bill_amounts, branch_id);
        //                 }
        //             }
        //         });
        //     }
        // });

        // $('#proceed-transfer').click(function() {
        //     $('#buffer-cut-details').modal("hide");
        //     $('#security-code-modal').modal("show");
        //     // $('#bill-for-buffer-modal').modal("hide");
        // });

        $('#selling-rate').change(function() {
            $('#proceed-transfer').prop('disabled', true);
        });

        $('#selected-bill-total-amount').change(function() {
            if (parseFloat($(this).val()) > parseFloat(bill_amnt)) {
                $('#proceed-transfer').prop('disabled', true);

                Swal.fire({
                    text: 'Buffer amount cannot be greater than the amount of stocks.',
                    icon: 'error',
                    showConfirmButton: true
                }).then(() => {
                    clear();

                    $(this).val('');
                    $('#proceed-transfer').prop('disabled', false);
                });
            }
        });

        $('#proceed-transfer').click(function() {
            var amount_to_cut = isNaN(parseFloat($('#selected-bill-total-amount').val())) ? 0 : parseFloat($('#selected-bill-total-amount').val());

            if (amount_to_cut == 0 || amount_to_cut == '') {
                $('#proceed-transfer').prop('disabled', true);

                Swal.fire({
                    text: 'Buffer amount is required.',
                    icon: 'error',
                    showConfirmButton: true
                }).then(() => {
                    clear();

                    $(this).val('');
                });
            } else {
                if ($('input[name="buffer-type"]:checked').val() == 1) {
                    validation(amount_to_cut, branch_id, currency_id);
                } else {
                    $('#buffer-cut-details').modal("hide");
                    $('#security-code-modal').modal("show");
                }
            }
        });

        $('#compute-buffer').click(function() {
            var amount_to_cut = isNaN(parseFloat($('#selected-bill-total-amount').val())) ? 0 : parseFloat($('#selected-bill-total-amount').val());
            var selling_rate = isNaN(parseFloat($('#selling-rate').val())) ? 0 :  parseFloat($('#selling-rate').val());

            if (amount_to_cut <= 0 || selling_rate <= 0) {
                Swal.fire({
                    text: 'Buffer amount and selling rate are required.',
                    icon: 'error',
                    showConfirmButton: true
                }).then(() => {
                    clear();

                    $('#proceed-transfer').prop('disabled', true);
                });
            } else {
                validation(amount_to_cut, branch_id, currency_id, selling_rate);
            }
        });
        

        function BranchPrompt($mgs, $branchid) {
            socket.emit('branchPrompt', {msg: $mgs, branchid: $branchid});
        }

        function clear() {
            var row = $(`<tr><td class="text-center p-1 text-sm py-2" colspan="13"><span class="text-sm"><strong>NOT AVAILABLE</strong></span></td></tr>`);

            $('#by-rate-breakdown').find('tbody').empty().append(row);
            $('#gain-loss-container').html('');

            $('#income').val("0.00").attr('placeholder', "0.00");
            $('#true-income').val(0);

            $('#principal').val("0.00").attr('placeholder', "0.00");
            $('#true-principal').val(0);

            $('#exch-amount').val("0.00").attr('placeholder', "0.00");
            $('#true-exch-amount').val(0);
        }

        function validation(amount_to_cut, branch_id, currency_id, selling_rate) {
            $('#container-test').fadeIn("fast");
            $('#container-test').css('display', 'block');

            setTimeout(function() {
                $.ajax({
                    url: "{{ route('admin_transactions.buffer.b_cut_validation') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        buffer_type: $('input[name="buffer-type"]:checked').val(),
                        amount: amount_to_cut,
                        branch_id: branch_id,
                        currency_id: currency_id,
                        selling_rate: selling_rate,
                    },
                    success: function(data) {
                        $('#container-test').fadeOut("fast");
                        $('#proceed-transfer').prop('disabled', false);

                        var total_gain_loss = 0;
                        const response = data[0];
                        const response_1 = data[1];

                        if (data.validity == 0 && data.buffer_type == 1) {
                            Swal.fire({
                                text: 'No exact amount in buffer stocks.',
                                icon: 'error',
                                showConfirmButton: true
                            }).then(() => {
                                clear();

                                $('#proceed-transfer').prop('disabled', false);
                            });
                        } else if (data.validity_branch == 0 && data.buffer_type == 1) {
                            Swal.fire({
                                text: 'No exact amount in branch stocks.',
                                icon: 'error',
                                showConfirmButton: true
                            }).then(() => {
                                clear();

                                $('#proceed-transfer').prop('disabled', false);
                            });
                        } else if (data.validity == 0 && data.buffer_type == 2) {
                            Swal.fire({
                                text: 'No exact amount in branch stocks.',
                                icon: 'error',
                                showConfirmButton: true
                            }).then(() => {
                                clear();

                                $('#proceed-transfer').prop('disabled', false);
                            });
                        } else {
                            if (data.buffer_type == 1) {
                                $('#proceed-transfer').prop('disabled', false);
                            } else {
                                $('#by-rate-breakdown').find('tbody').empty();

                                Object.values(response_1.grouped_by_rates).forEach(function(gar) {
                                    breakdownByRate(gar.SinagRateBuying, gar.gain_loss, gar.principal, gar.selling_rate, gar.total_bill_amount, gar.total_bill_count, gar.total_exchange_amount);

                                    total_gain_loss += gar.gain_loss;

                                    breakdownFooter(total_gain_loss);
                                });

                                $('#income').val(response.income.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                                $('#true-income').val(response.income.toFixed(2));

                                $('#principal').val(response.principal.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                                $('#true-principal').val(response.principal.toFixed(2));

                                $('#exch-amount').val(response.exchange_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                                $('#true-exch-amount').val(response.exchange_amnt.toFixed(2));
                            }
                        }
                    }
                });
            }, 700);
        }

        $('#halt-transaction').click(function() {
            $('#buffer-cut-details').modal("show");
            $('#security-code-modal').modal("hide");
        });

        $('#proceed-transaction').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#security-code').val();
            var amount_to_cut = isNaN(parseFloat($('#selected-bill-total-amount').val())) ? 0 : parseFloat($('#selected-bill-total-amount').val());

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

                    var index = sec_code_array.indexOf(user_sec_onpage);
                    var matched_user_id = user_id_array[index];

                    if (sec_code_array.includes(user_sec_onpage)) {
                        $('#proceed-transaction').prop('disabled', true);

                        Swal.fire({
                            title: 'Success',
                            text: 'Serials selected!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("fast");
                            $('#container-test').css('display', 'block');

                            var form_data = new FormData($('#declare-buffer-form')[0]);

                            form_data.append('matched_user_id', matched_user_id);
                            form_data.append('branch_id', branch_id);
                            form_data.append('currency_id', currency_id);
                            form_data.append('parsed_bill_amounts', amount_to_cut);
                            form_data.append('parsed_serials_for_buffer', FSIDs);
                            form_data.append('selling_rate', $('#selling-rate').val());
                            form_data.append('principal', $('#true-principal').val());
                            form_data.append('exch_amount', $('#true-exch-amount').val());
                            form_data.append('income', $('#true-income').val());
                            form_data.append('buffer_type', $('input[name="buffer-type"]:checked').val());

                            $.ajax({
                                url: "{{ route('admin_transactions.buffer.save') }}",
                                type: "POST",
                                data: form_data,
                                contentType: false,
                                processData: false,
                                cache: false,
                                success: function(data) {
                                    // BranchPrompt('Admin marked your stocks as buffer. Please reload the page.', branch_id);
                                    BranchPrompt('Stocks updated as Buffer. Please reload the page.', branch_id);

                                    setTimeout(function() {
                                        $('#container-test').fadeIn("slow");
                                        $('#container-test').css('display', 'block');
                                        
                                        var url = "{{ route('admin_transactions.buffer.buffer') }}";

                                        window.location.href = url;
                                    }, 500);
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

        function breakdownByRate(SinagRateBuying, gain_loss, principal, selling_rate, total_bill_amount, total_bill_count, total_exchange_amount) {
            var gain_loss_formatted = parseFloat(gain_loss).toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            console.log(gain_loss);

            if (gain_loss > 1) {
                gain_loss_formatted = '+' + gain_loss_formatted;
            } else if (gain_loss < 0) {
                gain_loss_formatted = '-' + gain_loss_formatted.substring(1);
            }

            var text_color = gain_loss >= 0 ? 'text-[#00A65A] font-bold text-xs' : 'text-[#DC3545] font-bold text-xs';
            var icon_gain_loss = gain_loss >= 0 ? `<i class='bx bxs-up-arrow pb-1' style="font-size: .5rem;"></i>` : `<i class='bx bxs-down-arrow pb-1' style="font-size: .5rem;"></i>`;

            var table = $('#by-rate-breakdown');
            var row = $('<tr>');
            var r_bill_amount = $('<td class="text-black text-right text-xs py-1 px-2">'+ total_bill_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
            var r_bill_count = $('<td class="text-black text-center text-xs p-1">'+ total_bill_count +'</td>');
            var r_selling_rate = $('<td class="text-black text-right text-xs py-1 px-2">'+ selling_rate.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
            var r_exchange_amount = $('<td class="text-black text-right text-xs py-1 px-2">'+ total_exchange_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
            var r_buying_rate = $('<td class="text-black text-right text-xs py-1 px-2">'+ SinagRateBuying.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
            var r_principal= $('<td class="text-black text-right text-xs py-1 px-2">'+ principal.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
            var r_gain_loss= $('<td class="text-black text-right text-xs py-1 px-2"><span class="'+ text_color +'"><text>'+ gain_loss_formatted +' '+ icon_gain_loss +'</text></span></td>');

            row.append(r_bill_amount);
            row.append(r_bill_count);
            row.append(r_selling_rate);
            row.append(r_exchange_amount);
            row.append(r_buying_rate);
            row.append(r_principal);
            row.append(r_gain_loss);

            table.find('tbody').append(row);
            row.hide().fadeIn(250);
        }

        function breakdownFooter(total_gain_loss) {
            var gain_loss_formatted = total_gain_loss.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            if (total_gain_loss > 1) {
                gain_loss_formatted = '+' + gain_loss_formatted;
            } else if (total_gain_loss < 0) {
                gain_loss_formatted = '-' + gain_loss_formatted.substring(1);
            }

            var badgeColor = total_gain_loss >= 0 ? 'success-badge-custom' : 'danger-badge-custom';
            var icon_gain_loss = total_gain_loss >= 0 ? `<i class='bx bxs-up-arrow pb-1' style="font-size: .5rem;"></i>` : `<i class='bx bxs-down-arrow pb-1' style="font-size: .5rem;"></i>`;

            var transfers_to_sell_total_gain_loss = $('<span class="badge '+ badgeColor +' py-1 text-xs">'+ gain_loss_formatted +' '+ icon_gain_loss +'</span>');

            $('#gain-loss-container').html(transfers_to_sell_total_gain_loss);
        }

        function bufferSummary(currency, quantity, total_amount) {
            var row_footer = $('#bill-cash-count');
            var cash_count_row = $('<tr>');
            var currency = $('<td class="text-black text-center text-sm p-1">'+ currency +'</td>');
            var count = $('<td class="text-black text-center text-sm p-1">'+ quantity +'</td>');
            var total_amount = $('<td class="text-black text-right text-sm py-1 px-3 w-50"><strong>'+ parseFloat(total_amount).toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</strong><input id="available-buffer" value="'+ parseFloat(total_amount) +'" type="hidden"></td>');
            // var breakdown_by_rate = $(`<td class="text-black text-center text-xs py-1 px-3"><button class="btn btn-primary button-edit text-white pe-2 break-d-rate"><i class='bx bx-detail'></i></button></td>`);

            cash_count_row.append(currency);
            cash_count_row.append(count);
            cash_count_row.append(total_amount);
            // cash_count_row.append(breakdown_by_rate);

            row_footer.find('tbody').append(cash_count_row);
            cash_count_row.hide().fadeIn(250);
        }

        // function bufferSerials(parsed_serials_for_buffer, parse_bill_amounts, branch_id) {
        // }

        // function billForBuffer(selected_fsid, selected_bills, selected_bill_serial, selected_bill_currency, select_bill_count) {
        //    var total_amount_transfer = 0;
        //    var bill_count = 0;

        //    var bills_for_transfer_table = $('#bills-for-buffer-table');

        //    var transferable_bill_details = selected_bill_serial.map(function(serials_val, serials_index) {
        //        return {
        //            currency: selected_bill_currency[serials_index],
        //            serials: serials_val,
        //            bill_amount: selected_bills[serials_index],
        //        };
        //    });

        //    transferable_bill_details.forEach(function(gar) {
        //        var new_row_modal = $('<tr class="text-center text-td-buying">');
        //        var transferable_currency = $('<td class="text-center text-xs p-1">'+ gar.currency +'</td>');
        //        var transferable_bill_amnt = $('<td class="text-right text-xs py-1 px-3"><strong>'+ parseFloat(gar.bill_amount).toLocaleString("en", {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</strong></td>');
        //        var transferable_serials = $('<td class="text-center text-xs p-1">'+ gar.serials +'</td>');

        //        total_amount_transfer += parseInt(gar.bill_amount);

        //        bill_count = selected_bill_currency.length;

        //        new_row_modal.append(transferable_currency);
        //        new_row_modal.append(transferable_serials);
        //        new_row_modal.append(transferable_bill_amnt);

        //        bills_for_transfer_table.find('tbody').append(new_row_modal);
        //        new_row_modal.hide().fadeIn(250);
        //    });

        //    var transfer_fx_cash_count = transferable_bill_details.reduce((result, bill_details) => {
        //        const currency = bill_details.currency;
        //        const bill_amount = parseInt(bill_details.bill_amount);

        //        if (!result[currency]) {
        //            result[currency] = {
        //                currency: currency,
        //                count: 0,
        //                total_amount: 0,
        //            };
        //        }

        //        result[currency].count++;
        //        result[currency].total_amount += bill_amount;

        //        return result;
        //    }, {});

        //    Object.keys(transfer_fx_cash_count).forEach(function(currency) {
        //        const cash_count = transfer_fx_cash_count[currency];

        //        var row_footer = $('#bill-cash-count');
        //        var cash_count_row = $('<tr>');
        //        var currency = $('<td class="text-black text-center text-xs p-1">'+ cash_count.currency +'</td>');
        //        var count = $('<td class="text-black text-center text-xs p-1">'+ cash_count.count +'</td>');
        //        var total_amount = $('<td class="text-black text-right text-xs py-1 px-3"><strong>'+ cash_count.total_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</strong></td>');

        //        cash_count_row.append(currency);
        //        cash_count_row.append(count);
        //        cash_count_row.append(total_amount);

        //        row_footer.find('tbody').append(cash_count_row);
        //        cash_count_row.hide().fadeIn(250);
        //    });

        //    if (bill_count < 10) {
        //         $('#transfer-summary-container').css({
        //             height: 'auto'
        //         });
        //    } else if (bill_count > 10) {
        //     $('#transfer-summary-container').css({
        //             height: 250
        //         });
        //    }
        // }

        function clearTransferSummary() {
            $('#bill-cash-count #bill-cash-count-body').empty();
        }

        function clearTable() {
            $('#bills-for-buffer-table #bill-for-transfer-table-body').empty();
        }
    });
</script>

<script>
    $(document).ready(function() {
        $('#buffer-cut-details').on('shown.bs.modal', function () {
            var available_buffer = parseFloat($('#available-buffer').val());
            var admin_buffer_balance = parseFloat($('#current-buffer-balance').val());

            if (admin_buffer_balance > available_buffer) {
                $('#selected-bill-total-amount').val(available_buffer.toFixed(2));
            } else {
                $('#selected-bill-total-amount').val(admin_buffer_balance.toFixed(2));
            }
            
            $('#selling-rate').val('');
            // $('#selected-bill-total-amount').val('');

            $('#income').val("0.00").attr('placeholder', "0.00");
            $('#true-income').val(0);

            $('#principal').val("0.00").attr('placeholder', "0.00");
            $('#true-principal').val(0);

            $('#exch-amount').val("0.00").attr('placeholder', "0.00");
            $('#true-exch-amount').val(0);
        });
    });
</script>
