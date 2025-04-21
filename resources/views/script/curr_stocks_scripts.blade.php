<script>
    // Currency Stocks (Branch)
    $(document).ready(function() {
        var current_date = new Date();
        var year = current_date.getFullYear();
        var month = String(current_date.getMonth() + 1).padStart(2, '0');
        var day = String(current_date.getDate()).padStart(2, '0');
        var formatted_date = year + '-' + month + '-' + day;

        if ($('.overdue-branches').val()) {
            let gar = '';
            let branch = '';
            let plural_form = '';
            let height = '';

            $('.overdue-branches').each(function() {
                $(this).val().split(',').forEach(function(test) {
                    branch +=   `<tr><td class="p-2 text-center text-xs border-t-gray-300">`+ test + `</td></tr>`;
                })

                gar = $(this).val().split(',').length > 1? 'are' : 'is';
                plural_form = $(this).val().split(',').length > 1? '(es)' : '';
                height = $(this).val().split(',').length > 9? 'height: 300px!important;' : 'height: auto; ';
            });

            Swal.fire({
                html:
                `<div class="mb-3">
                    <span class="text-sm text-black">Overdue stocks `+ gar +` seen on the following branch`+ plural_form +`</span>:
                </div>
                <div class="row px-2">
                    <div class="col-12 border border-gray-300 p-0" style="${height} overflow: hidden; overflow-y: scroll;">
                        <table class="table table-hover mb-0">
                            <thead style="position: sticky; top: 0; background: #fff; z-index: 3;">
                                <tr>
                                    <th class="border-gray-300 py-1 text-black font-bold text-center">Branch</th>
                                </tr>
                            </thead>
                            <tbody>
                                `+ branch +`
                            </tbody>
                        </table>
                    </div>
                </div>`,
                icon: 'warning',
                showConfirmButton: true,
                confirmButtonText: 'Proceed',
                showClass: {
                    popup: 'swal2-zoom-in'
                },
            });
        }

        $('.stock-details-button').click(function(){
            var BranchID = $(this).attr('data-branchid');
            $('#cash-count-container').addClass("d-none");

            $.ajax({
                url: "{{ route('admin_transactions.stocks.branch_stock_details') }}",
                method: "POST",
                data: {
                    BranchID: BranchID,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    clear();

                    var stock_deets = data.branch_stocks;

                    $('#container-test').fadeIn("fast");
                    $('#container-test').css('display', 'block');

                    setTimeout(function() {
                        $('#container-test').fadeOut("fast");

                        stock_deets.forEach(function(gar) {
                            branchStocks(gar.CurrencyID, gar.Currency, gar.total_principal, gar.total_bill_amount, gar.total_bill_count, BranchID, gar.InStockFor3DaysOrMore);
                        });

                        currencyDetails();
                    }, 500);

                    clearTransferSummary();
                }
            });
        });

        function branchStocks(CurrencyID, Currency, total_principal, total_bill_amount, total_bill_count, BranchID, InStockFor3DaysOrMore) {
            console.log(InStockFor3DaysOrMore);
            var table = $('#branch-avail-stocks');
            var row = $('<tr data-branchid="'+ BranchID +'">');
            var currency = $('<td class="text-black text-center text-xs p-1">'+ Currency +'</td>');
            var count = $('<td class="text-black text-center text-xs p-1">'+ total_bill_count +'</td>');
            var total_amount = $('<td class="text-black text-right text-xs py-1 px-3"><strong>'+ total_bill_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</strong></td>');
            var total_principal_php = $('<td class="text-black text-right text-xs py-1 px-3"><strong>'+ total_principal.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</strong></td>');
            var view_details = $(`<td class="text-black text-center text-xs py-1 px-3 position-relative">
                <button class="btn btn-primary button-edit currency-stock-details p-1 text-white" data-currencyid="${CurrencyID}" data-branchid="${BranchID}" data-currency="${Currency}">
                    <i class='bx bx-detail'></i>
                    ${InStockFor3DaysOrMore >= 1 ? `<span class="position-absolute top-0 start-90 translate-middle badge badge-center warning-badge-custom text-white"><i class='bx bxs-error bx-flashing badge-icon'></i></span>` : ``}
                </button>
            </td>`);

            row.append(currency);
            row.append(count);
            row.append(total_amount);
            row.append(total_principal_php);
            row.append(view_details);

            table.find('tbody').append(row);
            row.hide().fadeIn(250);
        }

        function clearTransferSummary() {
            $('#branch-avail-stocks #branch-avail-stocks-body').empty();
        }

        function clear() {
            $('#bill-cash-count #bill-cash-count-body').empty();
            $('#branch-breakdown #branch-breakdown-body').empty();
            $('#cash-count-breakdown #cash-count-breakdown-body').empty();
            $('#branch-stock-details-table #branch-stock-details-table-body').empty();
        }

        $('.view-branches').click(function() {
            var curr_id = $(this).attr('data-currencyid');
            var branch_ids = $(this).attr('data-branchids');

            clear();
            populateBranches(curr_id, branch_ids);
        });

        function populateBranches(curr_id, branch_ids) {
            $.ajax({
                url: "{{ route('admin_transactions.stocks.branches_of_currency') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    curr_id: curr_id,
                    branch_ids: branch_ids,
                },
                success: function(data) {
                    $('#container-test').fadeIn("fast");
                    $('#container-test').css('display', 'block');

                    var branches_of_currency = data.branches_of_currency;
                    var data_count = branches_of_currency;
                    
                    console.log(branches_of_currency);

                    setTimeout(function() {
                        $('#container-test').fadeOut("fast");

                        branches_of_currency.forEach(function(gar) {
                            branchesOfCurrency(gar.BranchCode, gar.total_count_per_branch, gar.total_amount_per_branch, data_count, gar.has_pending, gar.pending_count_per_branch, gar.pending_amount_per_branch);
                        });
                    }, 500);
                }
            });

            function branchesOfCurrency(BranchCode, total_count_per_branch, total_amount_per_branch, data_count, has_pending, pending_count_per_branch, pending_amount_per_branch) {
                var table = $('#branch-breakdown');
                var row = $('<tr>');

                var t_amnt_per_branch = total_amount_per_branch != null ? parseFloat(total_amount_per_branch).toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) : 0.00;
                var p_amnt_per_branch = pending_amount_per_branch != null ? parseFloat(pending_amount_per_branch).toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) : 0.00;
                var status_badge = has_pending == 1 ? '<span class="badge bg-label-red text-xs font-bold p-1">Yes</span>' : '<span class="badge bg-label-blue text-xs font-bold p-1">No</span>';

                var branch_code = $('<td class="text-black text-center text-sm p-1">'+ BranchCode +'</td>');
                var count_per_branch = $('<td class="text-black text-center text-sm p-1">'+ total_count_per_branch +'</td>');
                var total_amnt = $('<td class="text-black text-right text-sm py-1 px-3"><strong>'+ t_amnt_per_branch +'</strong></td>');
                var status = $(`<td class="text-black text-center text-xs py-1 px-3">${status_badge}</td`);
                var p_count_per_branch = $('<td class="text-black text-center text-sm p-1">'+ pending_count_per_branch +'</td>');
                var p_total_amnt = $('<td class="text-black text-right text-sm py-1 px-3"><strong>'+ p_amnt_per_branch +'</strong></td>');

                row.append(branch_code);
                row.append(count_per_branch);
                row.append(total_amnt);
                row.append(status);
                row.append(p_count_per_branch);
                row.append(p_total_amnt);

                table.find('tbody').append(row);
                row.hide().fadeIn(250);

                if (data_count >= 15) {
                    $('#branch-breakdown-container').css({
                        height: data_count * 15,
                        overflow: hidden,
                        overflow-y: scroll
                    });
                } else if (data_count < 15) {
                    $('#branch-breakdown-container').css({
                        height: 'auto'
                    });
                }
            }
        }
    });

    function currencyDetails() {
        $('.currency-stock-details').click(function() {
            $('#container-test').fadeIn("fast");
            $('#container-test').css('display', 'block');

            var BranchID = $(this).attr('data-branchid');
            var CurrencyID = $(this).attr('data-currencyid');
            var Currency = $(this).attr('data-currency');

            $('#branch-avail-stocks-body tr').removeClass('search-highlight');
            $(this).closest('tr').addClass('search-highlight');

            $.ajax({
                url: "{{ route('admin_transactions.stocks.curr_stock_details') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    BranchID: BranchID,
                    CurrencyID: CurrencyID,
                },
                success: function(data) {
                    var curr_stocks_by_bill = data.curr_stocks_by_bill;
                    var curr_stocks_total = data.curr_stocks_total;
                    var curr_stocks_by_serial = data.curr_stocks_by_serial;
                    var stock_count = curr_stocks_by_serial.length;

                    // $('#container-test').fadeIn("fast");
                    // $('#container-test').css('display', 'block');

                    setTimeout(function() {
                        clear();
                        $('#currency').text('('+ Currency +')');

                        $('#container-test').fadeOut("fast");
                        $('#cash-count-container').removeClass("d-none");

                        curr_stocks_by_bill.forEach(function(gar) {
                            stockByBill(gar.BillAmount, gar.total_bill_amount, gar.total_bill_count);
                        });

                        curr_stocks_total.forEach(function(gar) {
                            stockTotal(gar.total_bill_amount, gar.total_bill_count, gar.total_principal);
                        });

                        curr_stocks_by_serial.forEach(function(gar) {
                            stockBySerial(gar.BillAmount, gar.total_bill_amount, gar.TransactionDate, gar.days_in_stock, stock_count);
                            // stockBySerial(gar.FSID, gar.BillAmount, gar.Serials, gar.TransType, gar.SinagRateBuying, gar.total_principal, gar.days_in_stock, stock_count);
                        });
                    }, 500);
                }
            });
        });

        function stockByBill(BillAmount, total_bill_amount, total_bill_count) {
            var table = $('#cash-count-breakdown');
            var row = $('<tr>');
            var bill_amount = $('<td class="text-black text-right text-xs py-2 px-4">'+ BillAmount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
            var count = $('<td class="text-black text-center text-xs p-2">'+ total_bill_count +'</td>');
            var total_amount = $('<td class="text-black text-right text-xs py-2 px-4"><strong>'+ total_bill_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</strong></td>');

            row.append(bill_amount);
            row.append(count);
            row.append(total_amount);

            table.find('tbody').append(row);
            row.hide().fadeIn(250);
        }

        function stockTotal(total_bill_amount, total_bill_count, total_principal) {
            var table = $('#bill-cash-count');
            var row = $('<tr>');
            var total_bill_amnt = $('<td class="text-black text-right text-xs py-2 px-4">'+ total_bill_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
            var count = $('<td class="text-black text-center text-xs p-2">'+ total_bill_count +'</td>');
            var total_principal_amnt = $('<td class="text-black text-right text-xs py-2 px-4"><strong>'+ total_principal.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</strong></td>');

            row.append(total_bill_amnt);
            row.append(count);
            row.append(total_principal_amnt);

            table.find('tbody').append(row);
            row.hide().fadeIn(250);
        }

        // function stockBySerial(FSID, BillAmount, Serials, TransType, SinagRateBuying, total_principal, days_in_stock, stock_count) {
        function stockBySerial(BillAmount, total_bill_amount, TransactionDate, days_in_stock, stock_count) {
            $('#stock-summary-container').css({
                height: 'auto'
            });

            let day = '';
            let badge_color = '';

            if (days_in_stock == 0) {
                day = '';
            } else if (days_in_stock <= 1 || days_in_stock == 2) {
                day = 'DAY';
                badge_color = 'primary-badge-custom';
            } else if (days_in_stock > 2) {
                day = 'DAYS';
                badge_color = 'warning-badge-custom';
            }

            var table = $('#branch-stock-details-table');
            var row = $('<tr>');
            var bill_amnt = $('<td class="text-black text-right text-xs py-2 px-4">'+ BillAmount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
            var tota_bill_amnt = $('<td class="text-black text-right text-xs py-2 px-4"><strong>'+ total_bill_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</strong></td>');
            var transaction_date = $('<td class="text-black text-center text-xs p-2">'+ TransactionDate +'</td>');
            // var serials = $('<td class="text-black text-center text-xs p-2">'+ Serials +'</td>');
            // var trans_type = $('<td class="text-black text-center text-xs p-2">'+ TransType +'</td>');
            // var rate = $('<td class="text-black text-center text-xs p-2">'+ SinagRateBuying.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
            // var principal_amnt = $('<td class="text-black text-right text-xs py-2 px-4"><strong>'+ total_principal.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</strong></td>');
            var diff_days_in_stock = $('<td class="text-black text-center text-xs p-2"><strong><span class="badge rounded-pill text-white '+ badge_color +'">'+ days_in_stock +' '+ day +'</span></strong></td>');

            row.append(bill_amnt);
            row.append(tota_bill_amnt);
            row.append(transaction_date);
            // row.append(serials);
            // row.append(trans_type);
            // row.append(rate);
            // row.append(principal_amnt);
            row.append(diff_days_in_stock);

            table.find('tbody').append(row);
            row.hide().fadeIn(250);

            if (stock_count >= 13) {
                $('#stock-summary-container').css({
                    height: stock_count * 15
                });
            } else if (stock_count < 13) {
                $('#stock-summary-container').css({
                    height: 'auto'
                });
            }
        }

        function clear() {
            $('#bill-cash-count #bill-cash-count-body').empty();
            $('#cash-count-breakdown #cash-count-breakdown-body').empty();
            $('#branch-stock-details-table #branch-stock-details-table-body').empty();
        }
    }

    // Currency Stocks (Admin)
    $(document).ready(function() {
        $('.admin-stocks-details').click(function() {
            var CurrencyID = $(this).attr('data-currencyid');
            var currency = $(this).attr('data-currency');
            var currency_abbv = $(this).attr('data-currabbv');
            var total_bills = [];
            var total_bill_serial = [];
            var total_bills_cash_count = [];
            var total_bill_serial_cash_count = [];
            var total_rate_used = [];

            clearTables();
            clearTransferSummary();

            $.ajax({
                url: "{{ route('admin_transactions.stocks.admin_stock_details') }}",
                method: "POST",
                data: {
                    CurrencyID: CurrencyID,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    var total_amount_rset = 0;
                    var total_amount_buff = 0;
                    var onhold_total_amnt = 0;
                    var stock_deets = data.admin_stock_details_s;
                    var by_rset = data.sorted_by_r_set;
                    var by_rset_buffer = data.sorted_by_r_set_buffer;
                    var by_buffer = data.sorted_by_buffer;
                    var stock_deets_count = stock_deets.length;
                    var stock_breakdown = data.stock_breakdown;
                    var stock_breakdown_buffer = data.stock_breakdown_buffer;

                    $('#container-test').fadeIn(100);
                    $('#container-test').css('display', 'block');

                    setTimeout(function() {
                        $('#container-test').fadeOut(100);
                        $('#currency').text('('+ currency +')');
                        $('#currency-abbrv').text(currency_abbv);

                        stock_deets.forEach(function(gar) {
                            stockDeets(gar.ID, gar.Buffer, gar.Rset, gar.BillAmount, gar.Serials, gar.TransType, gar.SinagRateBuying, gar.source_type, stock_deets_count);
                        });

                        if (by_rset.length > 0) {
                            by_rset.forEach(function(gar) {
                                total_amount_rset += gar.total_bil_amount;

                                sortedByRset(gar.Rset, gar.total_bill_count, gar.total_bil_amount);

                                $('#by-rset-reg-total').text(total_amount_rset.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                            });
                        } else {
                            var table = $('#admin-rset-count');

                            var no_available_buffer = $(`
                                <tr>
                                    <td class="text-center font-bold text-sm" colspan="4">
                                        NOT AVAILABLE
                                    </td>
                                </tr>
                            `);

                            table.find('tbody').append(no_available_buffer);
                        }

                        if (by_rset_buffer.length > 0) {
                            by_rset_buffer.forEach(function(gar) {
                                total_amount_buff += gar.total_bil_amount;

                                sortedByRsetBuffer(gar.Rset, gar.total_bill_count, gar.total_bil_amount);

                                $('#by-rset-buff-total').text(total_amount_buff.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
                            });
                        } else {
                            var table = $('#admin-rset-count-buffer');

                            var no_available_buffer = $(`
                                <tr>
                                    <td class="text-center font-bold text-sm" colspan="4">
                                        NOT AVAILABLE
                                    </td>
                                </tr>
                            `);

                            table.find('tbody').append(no_available_buffer);
                        }

                        var bill_amnt_sum = 0;
                        var principal_sum = 0;
                        var bill_amnt_sum_buffer = 0;
                        var principal_sum_buffer = 0;

                        if (stock_breakdown.length > 0) {
                            stock_breakdown.forEach(function(gar) {
                                bill_amnt_sum += parseFloat(gar.total_bill_amount);
                                principal_sum += parseFloat(gar.principal);

                                stockBreakdown(gar.BillAmount, gar.denom_count, gar.total_bill_amount, gar.principal, bill_amnt_sum, principal_sum);
                            });
                        } else {
                            var table = $('#admin-cash-count-breakdown');

                            var no_available_buffer = $(`
                                <tr>
                                    <td class="text-center font-bold text-sm" colspan="4">
                                        NOT AVAILABLE
                                    </td>
                                </tr>
                            `);

                            table.find('tbody').append(no_available_buffer);
                        }

                        if (stock_breakdown_buffer.length > 0) {
                            stock_breakdown_buffer.forEach(function(gar) {
                                bill_amnt_sum_buffer += parseFloat(gar.total_bill_amount);
                                principal_sum_buffer += parseFloat(gar.principal);

                                stockBreakdownBuff(gar.BillAmount, gar.denom_count, gar.total_bill_amount, gar.principal, bill_amnt_sum_buffer, principal_sum_buffer);
                            });
                        } else {
                            var table = $('#admin-buffer-breakdown');

                            var no_available_buffer = $(`
                                <tr>
                                    <td class="text-center font-bold text-sm" colspan="4">
                                        NOT AVAILABLE
                                    </td>
                                </tr>
                            `);

                            table.find('tbody').append(no_available_buffer);
                        }

                        denominations(data.denoms[0]);
                        selectAll(onhold_total_amnt);
                    }, 500);
                }
            });
        });

        var table_height = '';

        function stockDeets(ID, Buffer, Rset, BillAmount, Serials, TransType, SinagRateBuying, source_type, stock_deets_count) {
            var formatted_bill_amount = BillAmount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2});
            $('#stocks-select-all-serial').attr('disabled', 'disabled');
            $('#onhold-serials').removeAttr('disabled', 'disabled').prop('checked', false);

            var buffer_filter = Buffer == 0 ? 1 : 2;
            var buffer_status = Buffer == 1 ? $(`<span class="badge success-badge-custom">Buffer</span>`).prop('outerHTML') : $(`<span class="badge primary-badge-custom">Regular</span>`).prop('outerHTML');

            var stock_details_table = $('#admin-stock-details-table');
            var stock_details_table = stock_details_table.find('#admin-stock-details-table-body');
            var principal = BillAmount * parseFloat(SinagRateBuying);


            var testing_lang = `
                <tr class="data-serials-tr" data-serials="${Serials}" data-billamount="${BillAmount}" data-status="${buffer_filter}">
                    <td class="text-xs text-center p-2"><input class="form-check-input stocks-select-one-serial" id="stocks-select-one-serial" type="checkbox" value="${ID}" data-serialamount="${BillAmount.toFixed(2)}" data-source="${source_type}" disabled></td>
                    <td class="text-xs text-center p-2">${Rset}</td>
                    <td class="text-xs text-center p-2">${buffer_status}</td>
                    <td class="text-sm text-center p-2">${Serials}</td>
                    <td class="text-xs text-right p-2">${formatted_bill_amount}</td>
                    <td class="text-xs text-center p-2">${parseFloat(SinagRateBuying).toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</td>
                    <td class="text-xs text-right py-2 px-4"><strong><span>${principal.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</span></strong></td>
                </tr>
            `;

            // <td class="text-xs text-center p-2">${TransType}</td>

            stock_details_table.append(testing_lang);

            if (stock_deets_count > 10) {
                $('#admin-stock-summary-container').css({
                    height: 500
                });

                table_height = stock_deets_count * 10;
            } else if (stock_deets_count < 10) {
                $('#admin-stock-summary-container').css({
                    height: 'auto'
                });
            }
        }

        function selectAll(onhold_total_amnt) {
            $('#onhold-serials').click(function() {
                var select_all_val = $(this).prop('checked');

                $('#container-test').fadeIn("fast");
                $('#container-test').css('display', 'block');

                setTimeout(function() {
                    $('#container-test').fadeOut("fast");

                    if (select_all_val == true) {
                        var serials_table = $('#admin-stock-details-table');

                        $('#proceed-onhold').removeClass('d-none');
                        $('#searial-search').removeAttr('disabled', false);
                        $('#denomination-filter').removeAttr('disabled', false);
                        $('#transaction-filter').removeAttr('disabled', false);
                        $('#onhold-sec-code-container').removeClass('d-none');
                        $('#stocks-select-all-serial').removeAttr('disabled', false);
                        $('.stocks-select-one-serial').removeAttr('disabled', false);
                    } else {
                        $('#proceed-onhold').addClass('d-none');
                        $('#onhold-sec-code-container').addClass('d-none');
                        $('#stocks-select-all-serial').attr('disabled', true).prop('checked', false);
                        $('.stocks-select-one-serial').attr('disabled', true).prop('checked', false);

                        $('#onhold-total-amnt-input').val(0);
                        $('#onhold-total-amount').text('0.00');

                        $('#searial-search').attr('disabled', true).val('').keyup();
                        $('#transaction-filter').val('default').change().attr('disabled', true);
                        $('#denomination-filter').val('default').change().attr('disabled', true);
                    }
                }, 500);
            });

            $('#stocks-select-all-serial').click(function() {
                var select_all_val = $(this).prop('checked');

                if (select_all_val == true) {
                    onhold_total_amnt = 0;

                    $('.stocks-select-one-serial').each(function() {
                        var row = $(this).closest('tr');

                        if (row.find('td:visible').length > 0) {
                            $(this).prop('checked', true);
                        }

                        if ($(this).prop('checked') == true) {
                            onhold_total_amnt += parseFloat($(this).attr('data-serialamount'));
                        }
                    });
                } else {
                    $('.stocks-select-one-serial').each(function() {
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

            $('.stocks-select-one-serial').click(function() {
                if ($(this).prop('checked') == true) {
                    onhold_total_amnt += parseFloat($(this).attr('data-serialamount'));
                } else if ($(this).prop('checked') == false) {
                    onhold_total_amnt -= parseFloat($(this).attr('data-serialamount'));
                }

                $('#onhold-total-amnt-input').val(onhold_total_amnt);
                $('#onhold-total-amount').text(onhold_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            });
        }

        function stockBreakdown(BillAmount, denom_count, total_bill_amount, principal, bill_amnt_sum, principal_sum) {
            var table = $('#admin-cash-count-breakdown');
            var row = $('<tr>');
            var bill_amnt = $(`<td class="text-xs text-right px-3 py-1">${BillAmount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</td>`)
            var quantity = $(`<td class="text-xs text-center px-3 py-1">${denom_count}</td>`)
            var total_b_amnt = $(`<td class="text-xs text-right px-3 py-1"><strong>${total_bill_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</strong></td>`)
            var principal_per_denom = $(`<td class="text-xs text-right px-3 py-1"><strong>${principal.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</strong></td>`)

            row.append(bill_amnt);
            row.append(quantity);
            row.append(total_b_amnt);
            row.append(principal_per_denom);

            table.find('tbody').append(row);

            $('#bill-amount-sum').text(bill_amnt_sum.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            $('#principal-sum').text(principal_sum.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
        }

        function stockBreakdownBuff(BillAmount, denom_count, total_bill_amount, principal, bill_amnt_sum_buffer, principal_sum_buffer) {
            var table = $('#admin-buffer-breakdown');
            var row = $('<tr>');
            var bill_amnt = $(`<td class="text-xs text-right px-3 py-1">${BillAmount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</td>`)
            var quantity = $(`<td class="text-xs text-center px-3 py-1">${denom_count}</td>`)
            var total_b_amnt = $(`<td class="text-xs text-right px-3 py-1"><strong>${total_bill_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</strong></td>`)
            var principal_per_denom = $(`<td class="text-xs text-right px-3 py-1"><strong>${principal.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</strong></td>`)

            row.append(bill_amnt);
            row.append(quantity);
            row.append(total_b_amnt);
            row.append(principal_per_denom);

            table.find('tbody').append(row);

            $('#bill-amount-sum-buff').text(bill_amnt_sum_buffer.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            $('#principal-sum-buff').text(principal_sum_buffer.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
        }

        function sortedByRset(Rset, total_bill_count, total_bil_amount) {
            var table = $('#admin-rset-count');
            var row = $('<tr>');
            var r_set = $(`<td class="text-xs text-center p-1">${Rset}</td>`);
            var count = $(`<td class="text-xs text-center p-1">${total_bill_count}</td>`);
            var amount = $(`<td class="text-xs text-right p-1 px-3"><strong>${total_bil_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</strong></td>`);

            row.append(r_set);
            row.append(count);
            row.append(amount);

            table.find('tbody').append(row);
        }

        function sortedByRsetBuffer(Rset, total_bill_count, total_bil_amount) {
            var table = $('#admin-rset-count-buffer');
            var row = $('<tr>');
            var r_set = $(`<td class="text-xs text-center p-1">${Rset}</td>`);
            var count = $(`<td class="text-xs text-center p-1">${total_bill_count}</td>`);
            var amount = $(`<td class="text-xs text-right p-1 px-3"><strong>${total_bil_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</strong></td>`);

            row.append(r_set);
            row.append(count);
            row.append(amount);

            table.find('tbody').append(row);
        }

        // function sortedByBuffer(total_buffer_amount, total_bill_count) {
        //     var table = $('#admin-buffer-details');
        //     var row = $('<tr>');
        //     var count = $(`<td class="text-xs text-center p-1">${total_bill_count}</td>`);
        //     var r_set = $(`<td class="text-xs text-right py-1 px-3"><strong>${total_buffer_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</strong></td>`);

        //     row.append(count);
        //     row.append(r_set);

        //     table.find('tbody').append(row);
        // }

        function denominations(denoms) {
            var denoms_array = denoms.denominations.split(',');

            denoms_array.forEach(function(pang_lolo) {
                var options = $(`<option valuE="${pang_lolo}">${pang_lolo}</option>`);

                $('#denomination-filter').append(options);
            });
        }

        function clearTransferSummary() {
            $('#admin-rset-count tbody').empty();
            $('#admin-rset-count-buffer tbody').empty();
            $('#admin-buffer-breakdown tbody').empty();
            $('#admin-buffer-details #admin-buffer-details-body').empty();
            $('#admin-bill-cash-count #admin-bill-cash-count-body').empty();
            $('#admin-cash-count-breakdown #admin-cash-count-breakdown-body').empty();
        }

        function clearTables() {
            $('#denomination-filter').empty().append('<option value="default">All</option>');
            $('#admin-stock-details-table #admin-stock-details-table-body').empty();
        }

        let transact_type = '';
        let denomination = '';
        let search_serial = '';

        $('#transaction-filter').change(function(){
            transact_type = $(this).val();

            searchSerials(search_serial, denomination, transact_type);
        });

        $('#denomination-filter').change(function() {
            denomination = $(this).val();

            searchSerials(search_serial, denomination, transact_type);
        });

        $('#searial-search').keyup(function(){
            search_serial = $(this).val();

            searchSerials(search_serial, denomination, transact_type);
        });

        let regex_serial = '';
        let regex_denom = '';
        let regex_transact_type = '';

        function searchSerials(search_serial, denomination, transact_type) {
            regex_serial = new RegExp(search_serial, 'i');
            regex_denom = new RegExp('^' + parseInt(denomination) + '$', 'i');
            regex_transact_type = new RegExp('^' + parseInt(transact_type), 'i');

            $('#stocks-select-all-serial').prop('checked', false);
            var serials_table = $('#admin-stock-details-table');

            serials_table.find("tbody .data-serials-tr").each(function() {
                var by_serial = regex_serial.test(search_serial) == true;
                var by_denom = regex_denom.test(denomination) == true;
                var by_transact_type = regex_denom.test(transact_type) == true;

                var serial = $(this).attr('data-serials');
                var denom = $(this).attr('data-billamount');
                var status = parseInt($(this).attr('data-status'));

                if ((denomination == 'default' || denomination == '') && (transact_type == 'default' || transact_type == '')) {
                    if (regex_serial.test(serial)) {
                        $(this).show();
                        $(this).addClass("search-highlight");

                        $('#admin-stock-summary-container').css({
                            height: 'auto'
                        });

                        if ($(this).find('td').is(':visible')) {
                            var searched_bill_amount = parseFloat($(this).find('.stocks-select-one-serial').data('serialamount'));
                        }
                    } else {
                        $(this).hide();
                    }

                    if (search_serial == '') {
                        $(this).removeClass("search-highlight");

                        $('#admin-stock-summary-container').css({
                            height: table_height
                        });
                    }
                } else {
                    if (regex_transact_type.test(status) && (denomination === 'default' || denomination === '')) {
                        $(this).show();
                        $(this).addClass("search-highlight");

                        $('#admin-stock-summary-container').css({
                            height: table_height
                        });

                        if (regex_serial.test(serial)) {
                            $(this).show();
                            $(this).addClass("search-highlight");

                            $('#admin-stock-summary-container').css({
                                height: 'auto'
                            });

                            if ($(this).find('td').is(':visible')) {
                                var searched_bill_amount = parseFloat($(this).find('.stocks-select-one-serial').data('serialamount'));
                            }
                        } else {
                            $(this).hide();
                        }
                    } else if (regex_denom.test(denom) && (transact_type === 'default' || transact_type === '')) {
                         $(this).show();
                        $(this).addClass("search-highlight");

                        $('#admin-stock-summary-container').css({
                            height: table_height
                        });

                        if (regex_serial.test(serial)) {
                            $(this).show();
                            $(this).addClass("search-highlight");

                            $('#admin-stock-summary-container').css({
                                height: 'auto'
                            });

                            if ($(this).find('td').is(':visible')) {
                                var searched_bill_amount = parseFloat($(this).find('.stocks-select-one-serial').data('serialamount'));
                            }
                        } else {
                            $(this).hide();
                        }
                    } else if (regex_denom.test(denom) && regex_transact_type.test(status)) {
                         $(this).show();
                        $(this).addClass("search-highlight");

                        $('#admin-stock-summary-container').css({
                            height: table_height
                        });

                        if (regex_serial.test(serial)) {
                            $(this).show();
                            $(this).addClass("search-highlight");

                            $('#admin-stock-summary-container').css({
                                height: 'auto'
                            });

                            if ($(this).find('td').is(':visible')) {
                                var searched_bill_amount = parseFloat($(this).find('.stocks-select-one-serial').data('serialamount'));
                            }
                        } else {
                            $(this).hide();
                        }
                    } else {
                        $(this).hide();
                    }

                    // if (transact_type == 'default') {
                    //     $(this).show();

                    //     $(this).removeClass("search-highlight");

                    //     $('#admin-stock-summary-container').css({
                    //         height: table_height
                    //     });
                    // }

                    // if (denomination == 'default') {
                    //     $(this).show();

                    //     $(this).removeClass("search-highlight");

                    //     $('#admin-stock-summary-container').css({
                    //         height: table_height
                    //     });
                    // }
                }
            });
        }

        $('#proceed-onhold').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#onhold-security-code').val();

            var from_branch = [];
            var from_admin = [];

            $('.stocks-select-one-serial').each(function() {
                var fsids = $(this).prop('checked') == true;

                if (fsids) {
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
                        $(this).prop('disabled', true);

                        var index = sec_code_array.indexOf(user_sec_onpage);
                        var matched_user_id = user_id_array[index];

                        Swal.fire({
                            title: 'Success!',
                            text: 'Onhold successful!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            $.ajax({
                                url: "{{ route('admin_transactions.stocks.save') }}",
                                type: "post",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    FSIDs: from_branch.join(", "),
                                    AFSIDs: from_admin.join(", "),
                                    matched_user_id: matched_user_id
                                },
                                success: function(data) {
                                    var url = "{{ route('admin_transactions.reserved_stocks') }}";

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
                    }
                }
            });
        });
    });
</script>

