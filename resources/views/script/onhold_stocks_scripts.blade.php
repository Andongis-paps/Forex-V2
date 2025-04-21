<script>
    $(document).ready(function() {
        var table_height = '';

        $('.held-stocks-details').click(function() {
            var currency_id = $(this).attr('data-currencyid');

            $.ajax({
                url: "{{ route('admin_transactions.reserved_stocks.details') }}",
                type: "post",
                data: {
                    _token: "{{ csrf_token() }}",
                    currency_id: currency_id
                },
                success: function(data) {
                    clear();
                    var onhold_total_amnt = 0;
                    var held_stock_details = data.held_stock_details;
                    var held_stock_count = held_stock_details.length;

                    $('#container-test').fadeIn("fast");
                    $('#container-test').css('display', 'block');

                    setTimeout(function() {
                        $('#container-test').fadeOut("fast");

                        held_stock_details.forEach(function(gar) {
                            details(gar.ID, gar.Rset, gar.DateHeld, gar.Name, gar.BillAmount, gar.Serials, gar.SinagRateBuying, gar.total_bill_amount, gar.total_principal, gar.source_type, held_stock_count);
                        });

                        denominations(data.denoms[0]);
                        selectAll(onhold_total_amnt);
                    }, 500);
                }
            });
        });

        function denominations(denoms) {
            var denoms_array = denoms.denominations.split(',');

            denoms_array.forEach(function(pang_lolo) {
                var options = $(`<option value="${pang_lolo}">${pang_lolo}</option>`);

                $('#denomination-filter').append(options);
            });
        }

        function details(ID, Rset, DateHeld, Name, BillAmount, Serials, SinagRateBuying, total_bill_amount, total_principal, source_type, held_stock_count) {
            $('#select-all-held-stocks').attr('disabled', 'disabled');
            $('#unhold-serials').removeAttr('disabled', 'disabled').prop('checked', false);

            var formatted_bill_amount = BillAmount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2});

            var table = $('#held-stocks');
            var row = $(`<tr class="data-serials-tr" data-serials="${Serials}" data-billamount="${BillAmount}">`);
            var input_field = $('<td class="text-xs text-center p-2"><input class="form-check-input select-one-held-stocks" id="select-one-held-stocks" type="checkbox" value="'+ ID +'" data-serialamount="'+ BillAmount +'" data-source="'+ source_type +'" disabled></td>');
            var date_held = $('<td class="text-black text-center text-xs py-2 px-4">'+ DateHeld +'</td>');
            var r_set = $('<td class="text-black text-center text-xs py-2 px-4">'+ Rset +'</td>');
            var held_by = $('<td class="text-black text-center text-xs py-2 px-4">'+ Name +'</td>');
            var bill_amount = $('<td class="text-black text-right text-xs py-2 px-4">'+ formatted_bill_amount +'</td>');
            var serials = $('<td class="text-black text-center text-xs p-2">'+ Serials +'</td>');
            var buying_rate = $('<td class="text-black text-right text-xs py-2 px-4">'+ SinagRateBuying.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
            var principal = $('<td class="text-black text-right text-xs py-2 px-4"><strong>'+ total_principal.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</strong></td>');

            row.append(input_field);
            row.append(date_held);
            row.append(held_by);
            // row.append(r_set);
            row.append(serials);
            row.append(bill_amount);
            row.append(buying_rate);
            row.append(principal);

            table.find('tbody').append(row);
            row.hide().fadeIn(250);

            if (held_stock_count > 10) {
                $('#onhold-stock-summary-container').css({
                    height: 550
                });

                table_height = held_stock_count * 8.5;
            } else if (held_stock_count < 10) {
                $('#onhold-stock-summary-container').css({
                    height: 'auto'
                });
            }
        }

        function clear() {
            $('#held-stocks #held-stocks-body').empty();
            $('#denomination-filter').empty().append('<option value="default">All</option>');
        }

        function selectAll(onhold_total_amnt) {
            $('#unhold-serials').click(function() {
                var select_all_val = $(this).prop('checked');

                $('#container-test').fadeIn("fast");
                $('#container-test').css('display', 'block');

                setTimeout(function() {
                    $('#container-test').fadeOut("fast");

                    if (select_all_val == true) {
                        $('#proceed-unhold').removeClass('d-none');
                        $('#searial-search').removeAttr('disabled', false);
                        $('#denomination-filter').removeAttr('disabled', false);
                        $('#unhold-sec-code-container').removeClass('d-none');
                        $('#select-all-held-stocks').removeAttr('disabled', false);
                        $('.select-one-held-stocks').removeAttr('disabled', false);
                    } else {
                        $('#proceed-unhold').addClass('d-none');
                        $('#searial-search').attr('disabled', true);
                        $('#denomination-filter').attr('disabled', true);
                        $('#unhold-sec-code-container').addClass('d-none');
                        $('#select-all-held-stocks').attr('disabled', true).prop('checked', false);
                        $('.select-one-held-stocks').attr('disabled', true).prop('checked', false);
                    }
                }, 500);
            });

            $('#select-all-held-stocks').click(function() {
                var checkbox_status = $(this).prop('checked');

                if (checkbox_status == true) {
                    onhold_total_amnt = 0;

                    $('.select-one-held-stocks').each(function() {
                        var row = $(this).closest('tr');

                        if (row.find('td:visible').length > 0) {
                            $(this).prop('checked', true);
                        }

                        if ($(this).prop('checked') == true) {
                            onhold_total_amnt += parseFloat($(this).attr('data-serialamount'));
                        }
                    });
                } else {
                    $('.select-one-held-stocks').each(function() {
                        $(this).prop('checked', false);

                        if ($(this).prop('checked') == false) {
                            onhold_total_amnt -= parseFloat($(this).attr('data-serialamount'));
                        }
                    });

                    onhold_total_amnt = 0;
                }

                $('#onhold-total-amnt-input').val(onhold_total_amnt);
                $('#onhold-total-amount').text(onhold_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            });

            $('.select-one-held-stocks').click(function() {
                if ($(this).prop('checked') == true) {
                    onhold_total_amnt += parseFloat($(this).attr('data-serialamount'));
                } else if ($(this).prop('checked') == false) {
                    onhold_total_amnt -= parseFloat($(this).attr('data-serialamount'));
                }

                $('#onhold-total-amnt-input').val(onhold_total_amnt);
                $('#onhold-total-amount').text(onhold_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            });
        }

        let denomination = '';
        let search_serial = '';

        $('#denomination-filter').change(function() {
            denomination = $(this).val();

            searchSerials(search_serial, denomination)
        });

        $('#searial-search').keyup(function(){
            search_serial = $(this).val();

            searchSerials(search_serial, denomination)
        });

        let regex_serial = '';
        let regex_denom = '';

        function searchSerials(search_serial, denomination) {
            regex_serial = new RegExp(search_serial, 'i');
            regex_denom = new RegExp('^' + parseInt(denomination) + '$', 'i');

            $('#select-all-held-stocks').prop('checked', false);
            var serials_table = $('#held-stocks');

            serials_table.find("tbody .data-serials-tr").each(function() {
                var by_serial = regex_serial.test(search_serial) == true;
                var by_denom = regex_denom.test(denomination) == true;

                var serial = $(this).attr('data-serials');
                var denom = $(this).attr('data-billamount');

                if (denomination == 'default' || denomination == '') {
                    if (regex_serial.test(serial)) {
                        $(this).show();
                        $(this).addClass("search-highlight");

                        $('#onhold-stock-summary-container').css({
                            height: 'auto'
                        });

                        // if ($(this).find('td').is(':visible')) {
                        //     var searched_bill_amount = parseFloat($(this).find('.stocks-select-one-serial').data('serialamount'));
                        // }
                    } else {
                        $(this).hide();
                    }

                    if (search_serial == '') {
                        $(this).removeClass("search-highlight");

                        $('#onhold-stock-summary-container').css({
                            height: table_height
                        });
                    }
                } else {
                    if (regex_denom.test(denom)) {
                        $(this).show();
                        $(this).addClass("search-highlight");

                        $('#onhold-stock-summary-container').css({
                            height: table_height
                        });

                        if (regex_serial.test(serial)) {
                            $(this).show();
                            $(this).addClass("search-highlight");

                            $('#onhold-stock-summary-container').css({
                                height: 'auto'
                            });

                            // if ($(this).find('td').is(':visible')) {
                            //     var searched_bill_amount = parseFloat($(this).find('.stocks-select-one-serial').data('serialamount'));
                            // }
                        } else {
                            $(this).hide();
                        }
                    } else {
                        $(this).hide();
                    }

                    if (denomination == 'default') {
                        $(this).show();

                        $(this).removeClass("search-highlight");

                        $('#onhold-stock-summary-container').css({
                            height: table_height
                        });
                    }
                }
            });
        }
    });

    $(document).ready(function() {
        $('#proceed-unhold').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#unhold-security-code').val();

            var from_branch = [];
            var from_admin = [];

            $('.select-one-held-stocks').each(function() {
                var selected_bills  = $(this).prop('checked') == true;

                if (selected_bills) {
                    if ($(this).attr('data-source') == 2) {
                        from_branch.push($(this).val());
                    }

                    if ($(this).attr('data-source') == 1) {
                        from_admin.push($(this).val());
                    }
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
                        $('#proceed-unhold').prop('disabled', true);

                        var index = sec_code_array.indexOf(user_sec_onpage);
                        var matched_user_id = user_id_array[index];

                        Swal.fire({
                            title: 'Success!',
                            text: 'Reversion successful!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            $.ajax({
                                url: "{{ route('admin_transactions.reserved_stocks.revert') }}",
                                type: "post",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    matched_user_id: matched_user_id,
                                    FSIDs: from_branch.join(", "),
                                    AFSIDs: from_admin.join(", "),
                                },
                                success: function(data) {
                                    window.location.reload();
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
                    }
                }
            });
        });
    });
</script>
