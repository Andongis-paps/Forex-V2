
{{-- Generate bills to sell --}}
<script>
    let selling_caps = [];

    $(document).ready(function() {
        var current_date = new Date();
        var year = current_date.getFullYear();
        var month = String(current_date.getMonth() + 1).padStart(2, '0');
        var day = String(current_date.getDate()).padStart(2, '0');

        var formatted_date = year + '-' + month + '-' + day;
        var set_date = $('#selling-transact-date-manila').val(formatted_date).text(formatted_date);

        if(set_date.val() != '') {
            $('#customer-detail').removeAttr('disabled');
            $('#customer-detail-selling').removeAttr('disabled');
        }

        $('input[name="radio-rset"]').change(function() {
            clearDateChange();
        });

        var from_admin_ids = [];
        var from_branch_ids = [];

        $('#get-bills').click(function() {
            var r_set = $('input[name="radio-rset"]:checked').val();

            console.log(r_set);

            $.ajax({
                url: "{{ route('admin_transactions.bulk_selling.queued') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    r_set: r_set,
                },
                success: function(data) {
                    var bills_details = data.bills_rset;
                    var total_sales_per_company = data.total_sales_per_company;
                    selling_caps = data.sales_limit;

                    if (bills_details.length == 0) {
                        clearData();
                        clearFooter();

                        Swal.fire({
                            icon: 'error',
                            text: 'No bills consolidated.',
                        });
                    } else if (data.no_rates == 1) {
                        clearData();
                        clearFooter();

                        Swal.fire({
                            icon: 'error',
                            html: `<span class="text-sm text-black">Selling rates are missing for some entries. Complete them first to continue.</span>`,
                        });
                    } else {
                        var total_curr_amnt = 0;
                        var total_gain_loss = 0;
                        var total_capital = 0;
                        var total_expected_exchange_rate = 0;
                        var total_excluded_add_funds = 0;

                        clearData();

                        from_admin_ids = [];
                        from_branch_ids = [];

                        $('#container-test').fadeIn("slow");
                        $('#container-test').css('display', 'block');
                        $('#confirm-selling-to-mnl').removeAttr('disabled');
                        $('#empty-selling-to-manila-table').fadeOut("fast");

                        total_sales_per_company.forEach(function(gar) {
                            salesPerCompany(gar.CompanyID, gar.CompanyName, gar.total_exchange_amount, gar.Active)
                        });

                        bills_details.forEach(function(gar) {
                            billForSelling(gar.CompanyID, gar.CompanyName, gar.Currency, gar.CurrencyID, gar.CMRUsed,  gar.SinagRateBuying, gar.total_bill_amount, gar.total_exchange_amount, gar.total_principal, gar.All_FSIDs, gar.All_AFSIDs, gar.gain_loss, gar.Buffer, gar.BufferType, gar.Received);

                            total_curr_amnt += gar.total_bill_amount;
                            total_gain_loss += gar.gain_loss;

                            total_capital += gar.total_principal;

                            if (gar.BufferType == 1 && gar.Received == 1) {
                                total_excluded_add_funds += gar.total_exchange_amount
                            }

                            total_expected_exchange_rate += gar.total_exchange_amount;

                            if (gar.All_FSIDs != null) {
                                from_branch_ids.push(gar.All_FSIDs);
                            }

                            if (gar.All_AFSIDs != null) {
                                from_admin_ids.push(gar.All_AFSIDs);
                            }

                            billsToSellTableFooter(total_curr_amnt, total_gain_loss, total_capital, total_expected_exchange_rate, total_excluded_add_funds);
                        });

                        setTimeout(function() {
                            $('#container-test').fadeOut("slow");
                            $('#confirm-selling-to-mnl').removeAttr('disabled');
                        }, 1000);
                    }
                }
            });
        });

        function billForSelling(CompanyID, CompanyName, Currency, CurrencyID, CMRUsed, SinagRateBuying, total_bill_amount, total_exchange_amount, total_principal, All_FSIDs, All_AFSIDs, gain_loss, Buffer, BufferType) {
            var gain_loss_formatted = gain_loss.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            if (gain_loss > 1) {
                gain_loss_formatted = '+' + gain_loss_formatted;
            } else if (gain_loss < 0) {
                gain_loss_formatted = '-' + gain_loss_formatted.substring(1);
            }

            var badgeColor = gain_loss >= 0 ? 'text-[#00A65A] font-bold text-xs' : 'text-[#DC3545] font-bold text-xs';
            var type = Buffer >= 1 ? '<span class="badge success-badge-custom">Buffer</span>' : '<span class="badge primary-badge-custom">Regular</span>';

            // var badgeColor = gain_loss >= 0 ? 'success-badge-custom' : 'danger-badge-custom';
            var icon_gain_loss = gain_loss >= 0 ? `<i class='bx bxs-up-arrow' style="font-size: .5rem;"></i>` : `<i class='bx bxs-down-arrow' style="font-size: .5rem;"></i>`;

            var to_manila_table = $('#selling-to-manila-table');
            var new_row = $('<tr class="text-center text-td-buying">');
            var company_name = $('<td class="text-center text-sm p-1">'+ CompanyName +'<input name="company-id[]" value="'+ CompanyID +'" type="hidden"></td>');
            var currency = $('<td class="text-center text-sm p-1">'+ Currency +'<input name="currency-id[]" value="'+ CurrencyID +'" type="hidden"></td>');
            var buffer = $('<td class="text-center text-xs p-1">'+ type +'</td>');
            var total_bill_amnt = $('<td class="text-right text-sm py-1 px-2">'+ total_bill_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'<input name="total-bill-amnt[]" value="'+ total_bill_amount +'" type="hidden"></td>');
            var sinag_rate_buying = $('<td class="text-right text-sm py-1 px-2">'+ SinagRateBuying.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'<input name="rate-used[]" value="'+ SinagRateBuying +'" type="hidden"></td>');
            var cmr_used = $('<td class="text-right text-sm py-1 px-2">'+ CMRUsed +'<input name="selling-rate[]" value="'+ CMRUsed +'" type="hidden"></td>');
            var exchange_amnt = $('<td class="text-right text-sm py-1 px-2">'+ total_exchange_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'<input name="exchange-amnt[]" value="'+ total_exchange_amount +'" type="hidden"></td>');
            var principal_amnt = $('<td class="text-right text-sm py-1 px-2">'+ total_principal.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'<input name="principal-amnt[]" value="'+ total_principal +'" type="hidden"></td>');
            var gain_loss = $('<td class="text-right text-sm py-1 px-2"><span class="'+ badgeColor +'"><text>'+ gain_loss_formatted +' &nbsp; '+ icon_gain_loss +'</text></span><input name="gain-loss[]" value="'+ gain_loss_formatted +'" type="hidden"></td>');

            new_row.append(company_name);
            new_row.append(currency);
            new_row.append(buffer);
            new_row.append(total_bill_amnt);
            new_row.append(sinag_rate_buying);
            new_row.append(cmr_used);
            new_row.append(exchange_amnt);
            new_row.append(principal_amnt);
            new_row.append(gain_loss);

            if (BufferType == 1) {
                var excluded_amnt = $('<td hidden><input name="excluded-amnt[]" value="'+ total_bill_amount +'" type="hidden"></td>');

                new_row.append(excluded_amnt);
            }

            if (Buffer == 1) {
                var buffer_only = $('<td hidden><input name="buffer-only-amnt[]" value="'+ total_bill_amount +'" type="hidden"></td>');

                new_row.append(buffer_only);
            }

            to_manila_table.find('tbody').append(new_row);
        }

        function billsToSellTableFooter(total_curr_amnt, total_gain_loss, total_capital, total_expected_exchange_rate, total_excluded_add_funds) {
            $('#total-generated-gain-loss').empty();

            var gain_loss_formatted = total_gain_loss.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            if (total_gain_loss > 1) {
                gain_loss_formatted = '+' + gain_loss_formatted;
            } else if (total_gain_loss < 0) {
                gain_loss_formatted = '-' + gain_loss_formatted.substring(1);
            }

            var badgeColor = total_gain_loss >= 0 ? 'success-badge-custom' : 'danger-badge-custom';
            var icon_gain_loss = total_gain_loss >= 0 ? `<i class='bx bxs-up-arrow' style="font-size: .5rem;"></i>` : `<i class='bx bxs-down-arrow' style="font-size: .5rem;"></i>`;

            var transfers_to_sell_total_curr_amnt = $('<strong>' + total_curr_amnt.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong>');
            var transfers_to_sell_total_gain_loss = $('<span class="badge '+ badgeColor +'">'+ gain_loss_formatted +' &nbsp; '+ icon_gain_loss +'</span>');
            var transfers_to_sell_total_capital = $('<strong>' + total_capital.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong>');
            var transfers_to_sell_total_ex_ex_r = $('<strong>' + total_expected_exchange_rate.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong>');

            // $('#total-generated-amount').html(transfers_to_sell_total_curr_amnt);
            $('#total-generated-gain-loss').html(transfers_to_sell_total_gain_loss);
            $('#total-generated-capital').html(transfers_to_sell_total_capital);
            $('#total-generated-ex-ex-r').html(transfers_to_sell_total_ex_ex_r);

            // $('#total-generated-amount-input').val(total_curr_amnt);
            $('#total-generated-gain-loss-input').val(total_gain_loss);
            $('#total-generated-capital-input').val(total_capital);
            $('#total-generated-ex-ex-r-input').val(total_expected_exchange_rate);
            $('#total-excluded-add-funds').val(total_excluded_add_funds);
        }

        function salesPerCompany(CompanyID, CompanyName, total_exchange_amount, Active) {
            var total_comp_sales_table = $('#total-company-sales-table');
            var new_row = $('<tr>');
            // var company = $('<td class="text-center text-sm">'+ CompanyName +'<input class="company-id" value="'+ CompanyID +'" type="hidden"></td>');
            var total_sales = $('<td class="text-center text-sm"><input class="company-total-sales" value="'+ total_exchange_amount +'" data-companyname="'+ CompanyName +'" data-limitstatus="'+ Active +'" data-companyid="'+ CompanyID +'" type="hidden"></td>');

            // new_row.append(company);
            new_row.append(total_sales);

            total_comp_sales_table.find('tbody').append(new_row);
        }

        function clearData() {
            $('#total-selling-amount').empty();
            $('#true-total-selling-amount').val(0.00);
            $('#selling-to-manila-table tbody').empty();
            $('#total-company-sales-table tbody').empty();
        }

        function clearFooter() {
            $('#selling-to-manila-table tfoot').empty();
            $('#total-generated-gain-loss-input').val('');
            $('#total-generated-capital-input').val('');
            $('#total-generated-ex-ex-r-input').val('');
        }

        function clearDateChange() {
            $('#selling-to-manila-table tbody').empty();
            $('#total-company-sales-table tbody').empty();

            $('#total-generated-gain-loss').empty();
            $('#total-generated-capital').empty();
            $('#total-generated-ex-ex-r').empty();

            $('#total-generated-gain-loss-input').val('');
            $('#total-generated-capital-input').val('');
            $('#total-generated-ex-ex-r-input').val('');

            $('#confirm-selling-to-mnl').attr('disabled', 'disabled');
        }

        $('#confirm-selling-to-mnl').click(function() {
            var company_id = [];
            var company_name = [];
            var sales_per_company = [];
            var over_limit_comp_id = [];
            var over_limit_comp_name = [];
            var capped_comp_limit = [];

            if (parseFloat($('#total-generated-gain-loss-input').val()) <= 0) {
                Swal.fire({
                    icon: 'warning',
                    text: 'You are about to sell loss entries. Continue selling?',
                    showConfirmButton: true,
                    showCancelButton: true,
                    cancelButtonColor: '#8592A3',
                }).then((result) => {
                    if (result.isConfirmed) {
                        proceedTransaction();
                    }
                });
            } else {
                proceedTransaction();
            }

            function proceedTransaction() {
                $('.company-total-sales').each(function() {
                    var limited_sales = $(this).attr('data-limitstatus') == 1;

                    if (limited_sales == true) {
                        company_id.push($(this).attr('data-companyid'));
                        company_name.push($(this).attr('data-companyname'));
                        sales_per_company.push(parseFloat($(this).val()));
                    }
                });

                transaction();
            }

            function transaction() {
                selling_caps.forEach(function(gar, index) {
                    var overcap = gar.Limit < sales_per_company[index];
                    var receipt_set = $('input[name="radio-rset"]:checked').val() == 'O';

                    if (receipt_set) {
                        if (overcap == true) {
                            over_limit_comp_id.push(company_id[index]);
                            over_limit_comp_name.push(company_name[index]);
                            capped_comp_limit.push(selling_caps[index].Limit);
                        }
                    }
                });

                var parsed_company = '';
                var parsed_company_id = '';

                if (over_limit_comp_name.length >= 2) {
                    parsed_company = over_limit_comp_name.join(" & ");
                    parsed_company_id = over_limit_comp_id.join(", ");
                } else if (over_limit_comp_name.length > 2) {
                    parsed_company = over_limit_comp_name.join(", ");
                    parsed_company_id = over_limit_comp_id.join(", ");
                } else if (over_limit_comp_name.length == 1) {
                    parsed_company = over_limit_comp_name[0];
                    parsed_company_id = over_limit_comp_id[0];
                }

                var company_array = [];

                if (parsed_company_id != '') {
                    let company = '';
                    let plural_form = '';
                    let more_than_one_company = '';
                    let container_height = '';

                    over_limit_comp_name.forEach(function(gar) {
                        company +=   `<tr><td class="p-2 text-center text-xs border-t-gray-300">`+ gar + `</td></tr>`;
                    });

                    more_than_one_company = over_limit_comp_name.length > 1? 'companies' : 'company';
                    plural_form = over_limit_comp_name.length > 1? 'following' : '';
                    container_height = over_limit_comp_name.length > 1? 'max-height: auto;' : 'max-height: auto;';

                    Swal.fire({
                        icon: 'warning',
                        html:  `<div class="mb-3">
                                    <span class="text-sm text-black">The sales limit is met by the `+ plural_form +` `+ more_than_one_company +` listed below</span>:
                                </div>
                                <div class="row px-2">
                                    <div class="col-12 border border-gray-300 p-0" style="`+ container_height +` overflow: hidden; overflow-y: scroll;">
                                        <table class="table table-hover mb-0">
                                            <thead style="position: sticky; top: 0; background: #fff; z-index: 3;">
                                                <tr>
                                                    <th class="border-gray-300 py-1 text-black font-bold text-center">Company</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                `+ company +`
                                            </tbody>
                                        </table>
                                    </div>
                                </div>`,
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: "Proceed",
                        customClass: {
                            popup: 'my-swal-popup',
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ route('admin_transactions.bulk_selling.capped_bills') }}",
                                type: "POST",
                                data: {
                                    r_set: $('input[name="radio-rset"]:checked').val(),
                                    parsed_company_id: parsed_company_id,
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(gar) {
                                    clearData();
                                    var over_cap_companies = gar.over_cap_companies;

                                    over_cap_companies.forEach(function(data) {
                                        // switches(data.CompanyID);

                                        queuedBills(data.CompanyID, data.CompanyName, data.bill_count, data.selling_limit, data.Serials);
                                    });
                                }
                            });
                        }
                    });
                } else {
                    $('#security-code-modal').modal("show");
                }
            }

            function queuedBills(CompanyID, CompanyName, bill_count, Limit, Serials) {
                var modal_body = $('#over-cap-bill-modal-body');
                var company_name = '';
                var test_val = 0;

                var display_none = parseFloat(test_val - Limit) < 0;

                console.log(parseFloat(test_val - Limit) < 0);

                company_name += `
                    <div class="row align-items-center my-1 p-1">
                        <div class="col-6 text-left">
                            <span class="text-lg font-bold">${CompanyName}</span>
                        </div>
                        <div class="col-6 text-end">
                            <div class="row align-items-center mt-2">
                                <div class="col-12 selling-limits-${CompanyID} text-end">
                                    Selling Limit: &nbsp;
                                    <strong>
                                        <span class="text-lg font-bold text-green-600 selling-limit-${CompanyID}" id="selling-limit">${Limit.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</span>
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center px-2">
                        <div class="col-12 queued-bills-container border border-gray-300 rounded-3 p-0">
                            <table class="table table-hover" id="queued-bills-per-company-${CompanyID}">
                                <thead class="sticky-header">
                                    <tr>
                                        <th class="text-center text-sm font-extrabold text-black whitespace-nowrap p-1">
                                            <input class="form-check-input" type="checkbox" id="select-all-bills-${CompanyID}" checked>
                                        </th>
                                        <th class="text-center text-sm font-extrabold text-black whitespace-nowrap py-1 px-1">Currency</th>
                                        <th class="text-center text-sm font-extrabold text-black whitespace-nowrap py-1 px-1">Bill Amount</th>
                                        <th class="text-center text-sm font-extrabold text-black whitespace-nowrap py-1 px-1">Selling Rate</th>
                                        <th class="text-center text-sm font-extrabold text-black whitespace-nowrap py-1 px-1">Exchange Amount</th>
                                    </tr>
                                </thead>`;
                    company_name +=
                                `<tbody>`;
                                Serials.forEach(function(gar, index) {
                                    test_val += gar.exchange_amount;

                                    company_name += `
                                    <tr class="data-serials-tr" data-currencyid="${gar.CurrencyID}">
                                        <td class="text-center text-sm p-2">
                                            <input class="form-check-input select-queued-bills select-bills-`+ CompanyID +`" type="checkbox" checked data-fsid="${gar.ID}" data-source="${gar.source_type}" data-billamount="${gar.BillAmount}" data-companyid="`+ CompanyID +`" data-exchangeamount="${gar.exchange_amount}">
                                        </td>
                                        <td class="text-center text-sm p-2">
                                            ${gar.Currency}
                                        </td>
                                        <td class="text-right text-sm p-2">
                                            ${gar.BillAmount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}
                                        </td>
                                        <td class="text-right text-sm p-2">
                                            ${gar.CMRUsed.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}
                                        </td>
                                        <td class="text-right text-sm p-2">
                                            <strong>${gar.exchange_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</strong>
                                        </td>
                                    <tr>`;
                                });
                    company_name +=`
                                </tbody>
                            </table>
                        </div>
                    </div>

                <div class="row align-items-center mt-2 pe-0">
                     <div class="col-4 text-start">
                        <span class="text-sm">Selected Bills</span>:
                        <strong><span class="text-sm bill-count-${CompanyID}">${bill_count}</span></strong>
                    </div>
                    <div class="col-4 px-0 text-end">
                        <span class="text-sm">Total Exchange Amount</span>:
                    </div>

                    <div class="col-3 px-0 text-end">
                        <strong><span class="text-lg text-red-600 ttl-exchg-amnt-per-company-${CompanyID}">`+ test_val.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +`</strong>
                    </div>

                    <div class="col-1 text-start icon-indicators-${CompanyID}">
                        <span class="badge warning-badge-custom pe-2 pt-2"><i class='bx bxs-error bx-flashing badge-icon-limit'></i>
                    </div>

                    <input class="total-exchange-amount-per-company-${CompanyID}" type="hidden" value="${test_val}" data-companyname="${CompanyName}">
                </div>

                <div class="row align-items-center pe-0 mt-1 mb-2">
                    <div class="col-4 offset-4 px-0 text-end">
                    </div>

                    <div class="col-3 ps-3 pe-0 text-end">
                        <hr class="m-0">
                    </div>
                </div>

                <div class="row align-items-center mb-2 mt-1 pe-0 to-less-per-company-${CompanyID} ">
                    <div class="col-4 offset-4 px-0 text-end">
                        <span class="text-sm">Amount to Less</span>:
                    </div>

                    <div class="col-3 px-0 text-end">
                        <strong><span class="text-lg text-muted ttl-amnt-to-less-per-company-${CompanyID}">`+ parseFloat(test_val - Limit).toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +`</strong>
                    </div>
                </div>`;

                modal_body.append(company_name);
                $('#unqueue-over-cap-bills-modal').modal("show");

                // switches(CompanyID);
                unselectBills(CompanyID, test_val, Limit, bill_count);
            }

            let regex_denom = '';

            function unselectBills(CompanyID, test_val, Limit, bill_count) {
                $('#currency-filter').change(function() {
                    test_val = 0;
                    bill_count = 0;

                    currency = $(this).val();
                    regex_denom = new RegExp('^' + currency, 'i');

                    var serials_table = $('#queued-bills-per-company-'+ CompanyID);

                    serials_table.find("tbody .data-serials-tr").each(function() {
                        var curr_id = $(this).attr('data-currencyid');

                        if (regex_denom.test(curr_id)) {
                            $(this).show();
                            $(this).addClass("search-highlight");

                            // $('#queued-bills-container').css({
                            //     height: 'auto'
                            // });

                            if ($(this).find('td').is(':visible')) {
                                $(this).find('.select-bills-'+ CompanyID).prop('checked', false);

                                // test_val += parseFloat($(this).find('.select-bills-'+ CompanyID).data('exchangeamount'));
                            }
                        } else {
                            $(this).hide();
                            // $(this).find('.select-bills-'+ CompanyID).prop('checked', true);
                        }

                        if (currency == 'default') {
                            $(this).show();
                            $(this).removeClass("search-highlight");

                            if ($(this).find('td').is(':visible') == false) {
                                $(this).find('.select-bills-'+ CompanyID).prop('checked', true);
                            }

                            // if ($(this).find('.select-bills-'+ CompanyID)) {
                            //     test_val += parseFloat($(this).find('.select-bills-'+ CompanyID).data('exchangeamount'));
                            // }

                            // $('#admin-stock-summary-container').css({
                            //     height: table_height
                            // });

                        }

                        if ($(this).find('.select-bills-'+ CompanyID).prop('checked') ==  true) {
                            test_val += parseFloat($(this).find('.select-bills-'+ CompanyID).data('exchangeamount'));
                        }
                    });

                    $('#select-all-bills-'+ CompanyID).prop('checked', false);

                    exchangeAmount(test_val, CompanyID, Limit)
                });

                $('#select-all-bills-'+ CompanyID).click(function() {
                    test_val;
                    bill_count;

                    var prop_check = $(this).prop('checked');

                    if (prop_check == true) {
                        test_val = 0;
                        bill_count = 0;

                        $('.select-bills-'+ CompanyID).each(function() {
                            var row = $(this).closest('tr');

                            if (row.find('td:visible').length > 0) {
                                $(this).prop('checked', true);
                            }

                            if ($(this).prop('checked') == true) {
                                bill_count += $(this).length;
                                test_val += parseFloat($(this).attr('data-exchangeamount'));
                            }
                        });
                    } else {
                        $('.select-bills-'+ CompanyID).each(function() {
                            $(this).prop('checked', false);

                            bill_count -= $(this).length;
                            test_val -= parseFloat($(this).attr('data-exchangeamount'));
                        });
                    }

                    exchangeAmount(test_val, CompanyID, Limit, bill_count)
                });

                $('.select-bills-'+ CompanyID).click(function() {
                    if ($(this).prop('checked') == true) {
                        bill_count += $(this).length;
                        test_val += parseFloat($(this).attr('data-exchangeamount'));
                    } else if ($(this).prop('checked') == false) {
                        bill_count -= $(this).length;
                        test_val -= parseFloat($(this).attr('data-exchangeamount'));
                    }

                    // Check/uncheck select all checkbox script
                    var all_checked = true;

                    $('.select-bills-'+ CompanyID).each(function() {
                        if (!$(this).prop('checked')) {
                            all_checked = false;
                            return false;
                        }
                    });

                    if (all_checked) {
                        $('#select-all-bills-'+ CompanyID).prop('checked', true);
                    } else {
                        $('#select-all-bills-'+ CompanyID).prop('checked', false);
                    }

                    exchangeAmount(test_val, CompanyID, Limit, bill_count);
                });
            }

            function exchangeAmount(test_val, CompanyID, Limit, bill_count) {
                var amount_to_less = test_val - Limit;
                var display_none = amount_to_less < 0;

                var text_color = test_val > Limit ? 'text-red-600' : 'text-green-600';

                var badge_element = test_val > Limit ?
                    `<span class="badge warning-badge-custom pe-2 pt-2"><i class='bx bxs-error bx-flashing badge-icon-limit'></i></span>` :
                    `<span class="badge success-badge-custom pe-2 pt-2"><i class='bx bx-check-circle badge-icon-limit'></i></span>`;

                $('.ttl-exchg-amnt-per-company-'+ CompanyID).text(test_val.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})).removeClass('text-green-600 text-red-600').addClass(text_color);
                $('.icon-indicators-'+ CompanyID ).empty().hide().append(badge_element).fadeIn("fast");
                $('.total-exchange-amount-per-company-'+ CompanyID).val(test_val);
                $('.bill-count-'+ CompanyID).empty().text(bill_count);

                if (display_none == true) {
                    $('.to-less-per-company-'+ CompanyID).fadeOut("fast");
                } else {
                    $('.to-less-per-company-'+ CompanyID).fadeIn("fast");
                }

                $('.ttl-amnt-to-less-per-company-'+ CompanyID).text(amount_to_less.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            }

            function clearData() {
                $('#over-cap-bill-modal-body').empty();
            }

            $('#proceed-unqueue-missing-bills').click(function() {
                var result = '';
                var exchange_amnts = [];

                company_id.forEach(function(gar) {
                    $('.total-exchange-amount-per-company-'+ gar).each(function() {
                        exchange_amnts.push(parseFloat($(this).val()));
                    });
                });

                capped_comp_limit.forEach(function(gar, index) {
                    var overcap_entries = exchange_amnts[index] > gar;

                    if (overcap_entries == true) {
                        result = true;
                    } else {
                        result = false;
                    }
                });

                if (result == true) {
                    Swal.fire({
                        icon: 'error',
                        html: `There are entries who are still over the selling limit.`,
                        customClass: {
                            popup: 'my-swal-popup',
                        }
                    });
                } else {
                    // var unselected_fsids_array = [];
                    var from_branch = [];
                    var from_admin = [];

                    $('#unqueue-over-cap-bills-modal').modal("hide");
                    $('#unselect-bills-security-code-modal').modal("show");

                    $('.select-queued-bills').each(function() {
                        var selected = $(this).prop('checked') == false;

                        // if (selected == true) {
                        //     if (!unselected_fsids_array.includes($(this).attr('data-fsid'))) {
                        //         unselected_fsids_array.push($(this).attr('data-fsid'));
                        //     }
                        // }

                        // var fsids = $(this).prop('checked') == true;

                        if (selected) {
                            if ($(this).attr('data-source') == 2) {
                                from_branch.push($(this).attr('data-fsid'));
                            }

                            if ($(this).attr('data-source') == 1) {
                                from_admin.push($(this).attr('data-fsid'));
                            }
                        }
                    });

                    testFunction(from_branch.join(","), from_admin.join(","));

                    // setTimeout(function() {
                    //     console.log(unselected_fsids_array.length);
                    // }, 500);
                }
            });
        });

        function testFunction(FSIDs, AFSIDs) {
            $('#proceed-unselect-bills-bill').click(function() {
                var user_id_array = [];
                var sec_code_array = [];
                var user_sec_onpage = $('#unselect-bills-bill-security-code').val();

                $('#proceed-unselect-bills-bill').prop('disabled', true);

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
                            $('#proceed-unselect-bills-bill').prop('disabled', true);

                            $.ajax({
                                url: "{{ route('admin_transactions.bulk_selling.unqueue_capped_bills') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    FSIDs: FSIDs,
                                    AFSIDs: AFSIDs,
                                },
                                success: function(data) {
                                    if (data == true) {
                                        Swal.fire({
                                            text: 'Unqueue Success!',
                                            icon: 'success',
                                            timer: 900,
                                            showConfirmButton: false
                                        }).then(() => {
                                            // $('#container-test').fadeIn("fast");
                                            // $('#container-test').css('display', 'block');
                                            $('#unselect-bills-security-code-modal').modal("hide");

                                            setTimeout(function() {
                                                $('#get-bills').click();
                                                $('#container-test').fadeOut("fast");
                                            }, 500);
                                        });
                                    }
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
                                $('#proceed-unselect-bills-bill').prop('disabled', false);
                            });
                        }
                    }
                });
            });
        }

        $('#proceed-transaction').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#security-code').val();
            var total_amnt_php = $('#true-total-selling-amount').val();
            // var parsed_fsids = concatinated_fsids_array.join(", ");

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
                            text: 'Selling Transaction Success!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            var form_data = new FormData($('#selling-transact-admin-form')[0]);
                            form_data.append('matched_user_id', matched_user_id);
                            form_data.append('AFSIDs', from_admin_ids.join(", "))
                            form_data.append('FSIDs', from_branch_ids.join(", "))
                            // form_data.append('parsed_fsids', parsed_fsids);
                            // form_data.append('total_amnt_php', total_amnt_php);


                            $.ajax({
                                url: "{{ route('admin_transactions.bulk_selling.save') }}",
                                type: "post",
                                data: form_data,
                                contentType: false,
                                processData: false,
                                cache: false,
                                success: function(data) {
                                    console.log(data.id);

                                    var route = "{{ route('admin_transactions.bulk_selling.details', ['id' => ':id']) }}";
                                    var url = route.replace(':id', data.id);

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
