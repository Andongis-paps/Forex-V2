<script>
    $(document).ready(function() {
        const socketserver = "{{ config('app.socket_io_server') }}";
        const socket = io(socketserver);

        $('#stop-buying-branch-select').click(function() {
            var rate_conf_check_stat = $(this).prop('checked');

            if (rate_conf_check_stat == true) {
                $('.stop-buying-branch-select-one').each(function() {
                    var row = $(this).closest('tr');

                    if (row.find('td:visible').length > 0) {
                        $(this).prop('checked', true);
                    }
                });
            } else {
                $('.stop-buying-branch-select-one').each(function() {
                    $(this).prop('checked', false);
                });
            }
        });

        // $('#transact-type-stop-buying').change(function() {
        //     $('#stop-buying-table-body').empty().fadeOut("fast");

        //     var empty_row = `<tr><td colspan="5"><span>&nbsp;</span></td></tr>`;

        //     $('#stop-buying-table-body').append(empty_row).fadeIn("fast");
        // });

        $('#currency-stop-buying').change(function() {
            $('#stop-buying-table-body').empty().fadeOut(250);
            // $('#transact-type-stop-buying').removeAttr('disabled', 'disabled');

            var default_val = $('#default-transtype').val();
            // $('#transact-type-stop-buying').val(default_val);

            var empty_row = `<tr><td colspan="5"><span>&nbsp;</span></td></tr>`;

            $('#stop-buying-table-body').append(empty_row).fadeIn(250);
        });

        $('#search-stop-buying-button').click(function() {
            var stop_buying_selected_curr_id = $('#currency-stop-buying').val();
            var transact_type_id = $('#transact-type-stop-buying').val();
            var stop_buying_selected_curr_text = $('#currency-stop-buying option:selected').text();
            var stop_buying_selected_curr_rate = $('#currency-stop-buying option:selected').attr('data-currencyrate');
            var stop_buying_selected_curr_abbrv = $('#currency-stop-buying option:selected').attr('data-currencyabbrv');

            if (stop_buying_selected_curr_text == 'Select a currency') {
                Swal.fire({
                    icon: 'error',
                    text: 'Select a currency.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });

                disabledFields();
            } else if (transact_type_id == '') {
                Swal.fire({
                    icon: 'error',
                    text: 'Select transaction type.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });

                disabledFields();
            } else {
                $('.current-stop-buying').removeAttr('disabled', 'disabled');
                $('#om-code').removeAttr('disabled', 'disabled');
                $('#stop-buying-select-all').removeAttr('disabled');
                $('#branch-search').removeAttr('disabled', 'disabled');
                $('#apply-rate-config-button').removeAttr('disabled');
                $('#stop-buying-branch-select').removeAttr('disabled');
                $('.stop-buying-branch-select-one').removeAttr('disabled');
                $('#clear-search-filter').removeAttr('disabled', 'disabled');
                $('#update-stop-buying-button').removeAttr('disabled', 'disabled');
                $('#stop-buying-selected-rate').val(stop_buying_selected_curr_rate).fadeIn("fast");
                $('#stop-buying-selected-curr-abbrv').val(stop_buying_selected_curr_abbrv).fadeIn("fast");
                $('#rate-config-selected-currency').val(stop_buying_selected_curr_text).fadeIn("fast");
                $('#container-test').fadeIn("fast");

                $.ajax({
                    url: "{{ route('maintenance.denom_configuration.get_denomination') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        // transact_type_id: transact_type_id,
                        stop_buying_selected_curr_id: stop_buying_selected_curr_id,
                    },
                    success: function(data) {
                        $('#update-stop-buying-banner').hide();
                        $('#stop-buying-currid').val(stop_buying_selected_curr_id);
                        // $('#transact-type-id').val(transact_type_id);
                        var new_curr_denom = data.curr_denom;

                        if (new_curr_denom == '') {

                            Swal.fire({
                                icon: 'error',
                                text: `No available denomination for this transact type. Clicking <u>Proceed</u> will redirect you to Currency Maintenance.`,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: true,
                                confirmButtonTest: 'Confirm',
                                customClass: {
                                    popup: 'my-swal-popup',
                                }
                            }).then((result) => {
                                let timerInterval;

                                Swal.fire({
                                    title: "Redirecting...",
                                    timer: 500,
                                    didOpen: () => {
                                    Swal.showLoading();
                                        const timer = Swal.getPopup().querySelector("b");
                                    timerInterval = setInterval(() => {
                                        timer.textContent = `${Swal.getTimerLeft()}`;
                                    }, 500);
                                },
                                willClose: () => {
                                    clearInterval(timerInterval);
                                }
                                }).then((result) => {
                                    $('#container-test').fadeIn("slow");
                                    $('#container-test').css('display', 'block');

                                    setTimeout(function() {
                                        window.location.href = "{{ URL::to('/') }}" + '/' + "editDenom" +'/'+ stop_buying_selected_curr_id;
                                    }, 500);
                                });
                            });
                        } else if (parseFloat(stop_buying_selected_curr_rate) == .0) {
                            Swal.fire({
                                icon: 'error',
                                text: `No available rate for this currency. Clicking <u>Proceed</u> will redirect you to Rate Maitenance.`,
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                timer: 2500,
                                customClass: {
                                    popup: 'my-swal-popup',
                                }
                            }).then(() => {
                                let timerInterval;
                                Swal.fire({
                                    title: "Redirecting...",
                                    timer: 2000,
                                    didOpen: () => {
                                    Swal.showLoading();
                                        const timer = Swal.getPopup().querySelector("b");
                                    timerInterval = setInterval(() => {
                                        timer.textContent = `${Swal.getTimerLeft()}`;
                                    }, 100);
                                },
                                willClose: () => {
                                    clearInterval(timerInterval);
                                }
                                }).then(() => {
                                    setTimeout(function() {
                                        window.location.href = "{{ URL::to('/') }}"+ "/" +"rateMaintenance";
                                    }, 200);
                                });
                            });
                        } else {
                            clearStock();

                            $('#container-test').fadeOut("fast");

                            new_curr_denom.forEach(function(curr_denom) {
                                var table = $('#stop-buying-table');
                                var new_row = $('<tr id="appended-tbody-element">');
                                var stop_buying_status_cell = $(`<td class="text-center p-1 text-sm"><input class="form-check-input stop-buying-denom" name="stop-buying-denom[]" type="checkbox" ${curr_denom.StopBuying == 1 ? 'checked' : ''} data-selected-bills="${curr_denom.BillAmount}"></td>`);
                                var denomination_cell = $('<td class="text-right py-1 pe-3 text-sm"><strong>'+ curr_denom.BillAmount.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) +'</strong><input class="currency-bill-amount" value="'+ parseFloat(curr_denom.BillAmount) +'" type="hidden"></td>');
                                var transact_type_cell = $(`<td class="text-center p-1 text-sm">${curr_denom.TransType}</td>`);

                                new_row.append(stop_buying_status_cell);
                                new_row.append(transact_type_cell);
                                new_row.append(denomination_cell);

                                table.find('tbody').append(new_row);
                                new_row.hide().fadeIn(250);
                            });

                            stopAll();
                            stopOne();
                            selectAll();
                        }
                    }
                });
            }

            function stopAll() {
                $('#stop-buying-select-all').click(function() {
                    var rate_conf_check_stat = $(this).prop('checked');

                    if (rate_conf_check_stat == true) {
                        $('.stop-buying-denom').each(function() {
                            $(this).prop('checked', true);
                        });
                    } else {
                        $('.stop-buying-denom').each(function() {
                            $(this).prop('checked', false);
                        });
                    }
                });
            }

            function stopOne() {
                $('.stop-buying-denom').click(function() {
                    var all_checked = true;

                    $('.stop-buying-denom').each(function() {
                        if (!$(this).prop('checked')) {
                            all_checked = false;
                            return false;
                        }
                    });

                    if (all_checked) {
                        $('#stop-buying-select-all').prop('checked', true);
                    } else {
                        $('#stop-buying-select-all').prop('checked', false);
                    }
                });
            }

            function selectAll() {
                var all_checked = true;

                $('.stop-buying-denom').each(function() {
                    var is_checked = $(this).is(':checked');

                    if (!is_checked) {
                        all_checked = false;
                    }
                });

                $('#stop-buying-select-all').prop('checked', all_checked);
            }

            function disabledFields() {
                $('#stop-buying-table tbody tr').empty();
                $('#appended-tbody-element').empty();
                $('#stop-buying-selected-rate').val('');
                $('#stop-buying-selected-curr-abbrv').val('');
                $('#update-stop-buying-banner').show();
                $('#apply-rate-config-button').attr('disabled', 'disabled');
                $('#stop-buying-branch-select').attr('disabled', 'disabled');
                $('.stop-buying-branch-select-one').attr('disabled', 'disabled');
                $('#update-stop-buying-button').attr('disabled', 'disabled');
                $('#om-code').attr('disabled', 'disabled');
                $('#branch-search').attr('disabled', 'disabled');
                $('#clear-search-filter').attr('disabled', 'disabled');
            }
        });

        $('#apply-rate-config-button').click(function() {
            var selected_branch = [];
            var selected_branch_id = [];

            $('.stop-buying-branch-select-one:checked').each(function() {
                selected_branch.push($(this).val());
                selected_branch_id.push($(this).attr('data-sbuyingbranchid'));
            });

            var parsed_branch = selected_branch.join(', ');

            if (selected_branch == '') {
                Swal.fire({
                    icon: 'error',
                    text: 'Select a branch.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
                $('#update-stop-buying-button').attr('disabled', 'disabled');
            } else {
                Swal.fire({
                    html: 'Update selected branches?',
                    icon: 'question',
                    confirmButtonColor: '#3085d6',
                    showCancelButton: true,
                    cancelButtonColor: '#8592A3',
                    confirmButtonText: 'Proceed',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        let timerInterval

                        Swal.fire({
                            title: 'Selecting',
                            html: 'Selecting selected branches',
                            timer: 1000,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading()
                                const b = Swal.getHtmlContainer().querySelector('b')
                                timerInterval = setInterval(() => {
                                   Swal.getTimerLeft()
                                    // b.textContent = Swal.getTimerLeft()
                                }, 100)
                            },
                            willClose: () => {
                                clearInterval(timerInterval)
                            }
                            }).then((result) => {
                                Swal.fire({
                                title: 'Success!',
                                text: 'Branches Selected!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            });

                            $('#update-stop-buying-button').removeAttr('disabled');
                        })
                    }
                });

                var processed_branch_array = selected_branch_id.join(", ");
                $('#stop-buying-selected-branch').val(processed_branch_array);
            }
        });

        let joined_bill_amounts = '';
        let joined_stop_buying = '';
        let joined_stopped_denoms = '';

        $('#update-stop-buying-button').click(function() {
            var selected_branch = [];
            var selected_branch_id = [];
            var selected_branch_codes = [];
            var bill_amounts = [];
            var stop_buying_fields = [];
            var stop_buying_denoms = [];

            $('.stop-buying-branch-select-one:checked').each(function() {
                selected_branch.push($(this).val());
                selected_branch_id.push($(this).attr('data-sbuyingbranchid'));
                selected_branch_codes.push($(this).attr('data-sbuyingbranchcode'));
            });

            $('.currency-bill-amount').each(function() {
                bill_amounts.push(parseFloat($(this).val()));
            });

            $('.stop-buying-denom').each(function() {
                stop_buying_fields.push($(this).is(':checked') ? 1 : 0);
            });

            $('.stop-buying-denom:checked').each(function() {
                stop_buying_denoms.push($(this).attr('data-selected-bills'));
            });

            var parsed_branch = selected_branch.join(', ');
            var user_sec_code_rate_config = $('#user-security-code').val();

            if (selected_branch == '') {
                Swal.fire({
                    icon: 'error',
                    text: 'Select a branch.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else {
                joined_bill_amounts = bill_amounts.join(", ");
                joined_stop_buying = stop_buying_fields.join(", ");
                joined_stopped_denoms = stop_buying_denoms.join(", ");

                var processed_branch_array = selected_branch_id.join(", ");
                $('#stop-buying-selected-branch').val(processed_branch_array);

                var joined_b_codes_aray = selected_branch_codes.join(", ");
                $('#stop-buying-selected-b-codes').val(joined_b_codes_aray);
                
                $('#security-code-modal').modal('show');
            }
        });

        $('#clear-search-filter').click(function() {
            $('#container-test').fadeIn("fast");
            $('#container-test').css('display', 'block');

            setTimeout(function() {
                $('#branch-search').val('').trigger('keyup');
                $('#om-code option[value="default"]').prop('selected', true).trigger('change');

                var branch_list = $('#branch-list-table');

                branch_list.find("tbody .rate-config-rows").each(function() {
                    $(this).removeClass("search-highlight");
                });

                $('#container-test').fadeOut("fast");
            }, 700);
        });

        function clearStock() {
            $('#stop-buying-table tbody tr').empty();
        }

        let om_code = '';
        let search_branch = '';

        $('#om-code').change(function() {
            om_code = $(this).val();

            searchSerials(search_branch, om_code);
        });

        $('#branch-search').keyup(function() {
            search_branch = $(this).val();

            searchSerials(search_branch, om_code);
        });

        let regex_branch = '';
        let regex_om = '';
        let item_count = '';
        let branch_ids = '';
        let branch_codes = '';

        function searchSerials(search_branch, om_code) {
            $('#branch-list-container-list').css('height', 0);

            regex_branch = new RegExp('^' + search_branch, 'i');
            regex_om = new RegExp('^' + om_code + '$', 'i');

            $('#stop-buying-branch-select').prop('checked', false);
            var branch_list = $('#branch-list-table');

            var visible_td_count = 0;

            branch_list.find("tbody .rate-config-rows").each(function() {
                var by_branch = regex_branch.test(search_branch) == true;
                var by_om_code = regex_om.test(om_code) == true;

                var area_om = $(this).attr('data-areaom');
                var branch_code = $(this).attr('data-branchcode');

                if (om_code == 'default' || om_code == '') {
                    if (regex_branch.test(branch_code)) {
                        $(this).show();
                        $(this).addClass("search-highlight");

                        if ($(this).find('td').is(':visible')) {
                            var searched_branch = parseFloat($(this).find('.stop-buying-branch-select-one').data('omid'));
                        }

                        var visible_tds = $(this).find('td:visible').length;
                        visible_td_count += visible_tds;

                        if (visible_td_count > 20) {
                            $('#branch-list-container-list').css({
                                height: 600,
                            });
                        } else {
                            $('#branch-list-container-list').css({
                                height: 'auto',
                            });
                        }
                    } else {
                        $(this).hide();
                    }

                    if (search_branch == '') {
                        $(this).removeClass("search-highlight");

                        $('#branch-list-container-list').css({
                            height: 600
                        });
                    }
                } else {
                    if (regex_om.test(area_om)) {
                        $(this).show();
                        $(this).addClass("search-highlight");

                        if (regex_branch.test(branch_code)) {
                            $(this).show();
                            $(this).addClass("search-highlight");

                            if ($(this).find('td').is(':visible')) {
                                var searched_branch = parseFloat($(this).find('.stop-buying-branch-select-one').data('omid'));
                            }

                            var visible_tds = $(this).find('td:visible').length;
                            visible_td_count += visible_tds;

                            if (visible_td_count > 20) {
                                $('#branch-list-container-list').css({
                                    height: 600,
                                });
                            } else {
                                $('#branch-list-container-list').css({
                                    height: 'auto',
                                });
                            }
                        } else {
                            $(this).hide();
                        }
                    } else {
                        $(this).hide();
                    }

                    if (om_code == 'default' || om_code == '') {
                        $(this).show();

                        $(this).removeClass("search-highlight");

                        $('#branch-list-container-list').css({
                            height: 600
                        });
                    }
                }
            });
        }

        $('#proceed-transaction').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#security-code').val();
            branch_ids = $('#stop-buying-selected-branch').val();
            branch_codes = $('#stop-buying-selected-b-codes').val();
            var currency = $('#currency-stop-buying option:selected').data('currency');

            $('#proceed-transaction').prop('disabled', true);

            function stopBuyingPrompt($mgs, $branch_ids, $branch_codes, $currency, $denoms) {
                socket.emit('stopBuyingChanges', {msg: $mgs, branchids: $branch_ids, branch_codes: $branch_codes, currency: currency, denom: joined_stopped_denoms});
            }

            // update_rate_config
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
                            text: 'Stop buying statuses updated!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            var form_data = new FormData($('#stop-buying-form')[0]);
                            form_data.append('matched_user_id', matched_user_id);
                            form_data.append('joined_bill_amounts', joined_bill_amounts);
                            form_data.append('joined_stop_buying', joined_stop_buying);

                            $.ajax({
                                url: "{{ route('maintenance.denom_configuration.update') }}",
                                type: "post",
                                data: form_data,
                                contentType: false,
                                processData: false,
                                cache: false,
                                success: function(data) {
                                    stopBuyingPrompt(`The admin has declared denomination(s) for stop buying within this branch.`, branch_ids, branch_codes, currency, joined_stopped_denoms);

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
                        }).then(() => {
                            $('#proceed-transaction').prop('disabled', false);
                        });
                    }
                }
            });
        });
    });

    $(document).ready(function() {
        $('.current-stop-buying').click(function() {
            $.ajax({
                url: "{{ route('maintenance.denom_configuration.config_stop_history') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    branch_id: $(this).attr('data-branchid')
                },
                success: function(data) {
                    clear();

                    const stop_buying_details = data.stop_buying_details;

                    var item_length = stop_buying_details.length;
                    var Currency = stop_buying_details.Currency;
                    var CurrencyID = stop_buying_details.CurrencyID;
                    var BillAmount = stop_buying_details.BillAmount;
                    var StopBuying = stop_buying_details.StopBuying;
                    var TransType = stop_buying_details.TransType;

                    stop_buying_details.forEach(function(gar) {
                        currencies(gar.Currency, gar.CurrencyID);
                        currentApplied(gar.Currency, gar.CurrencyID, gar.BillAmount, gar.StopBuying, gar.TransType, item_length);
                    });
                }
            });
        });

        var table_height = '';

        function currentApplied(Currency, CurrencyID, BillAmount, StopBuying, TransType, item_length) {
            var bill_amount = BillAmount.split(',');
            var stop_buying = StopBuying.split(',');
            var transaction_type = TransType.split(',');

            var currently_applied_stop_table = `
                <div class="row curr-row currency-config-stop-row-${CurrencyID}" data-currency="${CurrencyID}">
                    <div class="col-12 currency-name mt-2">
                        <div class="row"><span class=" font-bold">${Currency}</span></div>
                    </div>

                    <div class="col-12 current-config-table mb-0 mt-1">
                        <table class="table table-bordered table-hover mb-2" id="rate-config-table">
                            <thead>
                                <tr>
                                    <th class="text-center text-sm font-extrabold text-black p-1">Transaction Type</th>
                                    <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.w_rate_config_denom') }}</th>
                                    <th class="text-center text-sm font-extrabold text-black p-1">Stop Buying</th>
                                </tr>
                            </thead>
                            <tbody>`;

                            var merged_data = bill_amount.map(function(bills_val, bills_index) {
                                return {
                                    bill_amount: bills_val,
                                    stp_buying: stop_buying[bills_index],
                                    trans_type: transaction_type[bills_index],
                                };
                            });

                            merged_data.forEach(function(gar) {
                                var badge_element = gar.stp_buying == 1
                                ? `<span class="badge rounded-pill danger-badge-custom pe-2 pt-2 font-bold text-xs">Yes</span>`
                                : gar.stp_buying == 0
                                ? `<span class="badge rounded-pill primary-badge-custom pe-2 pt-2 font-bold text-xs">No</span>`
                                : '';

                                currently_applied_stop_table +=
                                    `<tr>
                                        <td class="text-sm p-1 text-center">${gar.trans_type}</td>
                                        <td class="text-sm py-1 pe-3 text-right font-semibold">${gar.bill_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</td>
                                        <td class="text-xs p-1 text-center">${badge_element}</td>
                                    </tr>`;
                            });

                    currently_applied_stop_table += `
                            </tbody>
                        </table>
                    </div>
                </div>
            `;

            $('#config-stop-container').append(currently_applied_stop_table);

            if (item_length >= 13) {
                $('#config-stop-container').css({
                    height: item_length * 20
                });

                table_height = item_length * 20;
            } else if (item_length < 2) {
                $('#config-stop-container').css({
                    height: 'auto'
                });
            }
        }

        function currencies(Currency, CurrencyID) {
            var select_element = `<option value="${CurrencyID}">${Currency}</option>`;

            $('#currency-select').append(select_element);
        }

        let currency_id = '';

        $('#currency-select').change(function() {
            currency_id = $(this).val();

            $('#container-test').fadeIn("fast");
            $('#container-test').css('display', 'block');

            filterCurrencies(currency_id);
        });

        function filterCurrencies(currency_id) {
            var row = $('.curr-row');

            row.each(function() {
                var curr_id = $(this).attr('data-currency');

                if (curr_id == currency_id) {
                    $('#container-test').fadeOut("fast");
                    $('.currency-config-stop-row-' + currency_id).show();

                    $('#config-stop-container').css({
                        height: 'auto'
                    });
                } else if (currency_id == 'default') {
                    $('#container-test').fadeOut("fast");
                    $('.currency-config-stop-row-' + curr_id).show();

                    $('#config-stop-container').css({
                        height: table_height
                    });
                } else {
                    $('#container-test').fadeOut("fast");
                    $('.currency-config-stop-row-' + curr_id).hide();
                }
            });
        }

        function clear() {
            $('#config-stop-container').empty();
        }
    });
</script>
