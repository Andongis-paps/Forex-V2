<script>
    $(document).ready(function() {
        // $('.branch-list-container-list').css({
        //     'overflow-y': 'scroll',
        //     'height': '600px'
        // });

        $('#rate-config-branch-select').click(function() {
            var rate_conf_check_stat = $(this).prop('checked');

            if (rate_conf_check_stat == true) {
                $('.rate-config-branch-select-one').each(function() {
                    var row = $(this).closest('tr');

                    if (row.find('td:visible').length > 0) {
                        $(this).prop('checked', true);
                    }
                });
            } else {
                $('.rate-config-branch-select-one').each(function() {
                    $(this).prop('checked', false);
                });
            }
        });

        $('#transact-type-rate-config').change(function() {
            $('#rate-config-table-body').empty().fadeOut("fast");

            var empty_row = `<tr><td colspan="13"><span>&nbsp;</span></td></tr>`;

            $('#rate-config-table-body').append(empty_row).fadeIn("fast");
        });

        $('#currency-rate-config').change(function() {
            $('#rate-config-table-body').empty().fadeOut(250);
            $('#transact-type-rate-config').removeAttr('disabled', 'disabled');

            var default_val = $('#default-transtype').val();
            $('#transact-type-rate-config').val(default_val);

            var empty_row = `<tr><td colspan="5"><span>&nbsp;</span></td></tr>`;

            $('#rate-config-table-body').append(empty_row).fadeIn(250);

            disabledFields();
        });

        $('#search-rate-config-button').click(function() {
            var rate_conf_selected_curr_id = $('#currency-rate-config').val();
            var transact_type_id = $('#transact-type-rate-config').val();
            var rate_conf_selected_curr_text = $('#currency-rate-config option:selected').text();
            var rate_conf_selected_curr_rate = $('#currency-rate-config option:selected').attr('data-currencyrate');
            var rate_conf_selected_curr_abbrv = $('#currency-rate-config option:selected').attr('data-currencyabbrv');
            var rib_variance = $('#currency-rate-config option:selected').attr('data-ribvariance');

            if (rate_conf_selected_curr_text == 'Select a currency') {
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
                var rib_buying_rate = rate_conf_selected_curr_rate - rib_variance;
                var rib_selling_rate = parseFloat(rate_conf_selected_curr_rate) + parseFloat(rib_variance);

                $('.current-config').removeAttr('disabled', 'disabled');
                $('#om-code').removeAttr('disabled', 'disabled');
                $('#branch-search').removeAttr('disabled', 'disabled');
                $('#apply-rate-config-button').removeAttr('disabled');
                $('#rate-config-branch-select').removeAttr('disabled');
                $('.rate-config-branch-select-one').removeAttr('disabled');
                $('#update-rate-config-button').removeAttr('disabled', 'disabled');
                $('#clear-search-filter').removeAttr('disabled', 'disabled');
                $('#rate-config-selected-rate').val(rate_conf_selected_curr_rate).fadeIn("fast");
                $('#rib-variance').val(rib_variance).fadeIn("fast");
                $('#rib-buying-rate').val(rib_buying_rate.toLocaleString("en" , {minimumFractionDigits: 4, maximumFractionDigits: 4})).fadeIn("fast");
                $('#rib-selling-rate').val(rib_selling_rate.toLocaleString("en" , {minimumFractionDigits: 4, maximumFractionDigits: 4})).fadeIn("fast");
                // $('#rate-config-selected-curr-abbrv').val(rate_conf_selected_curr_abbrv).fadeIn("fast");
                $('#rate-config-selected-currency').val(rate_conf_selected_curr_text).fadeIn("fast");
                $('#container-test').fadeIn("fast");

                $.ajax({
                    url: "{{ route('maintenance.rate_configuration.denom') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        transact_type_id: transact_type_id,
                        rate_conf_selected_curr_id: rate_conf_selected_curr_id,
                    },
                    success: function(data) {
                        $('#update-rate-config-banner').hide();
                        $('#rate-config-currid').val(rate_conf_selected_curr_id);
                        $('#transact-type-id').val(transact_type_id);
                        var new_curr_denom = data.curr_denom;

                        if (new_curr_denom == '') {

                            Swal.fire({
                                icon: 'error',
                                text: 'No available denomination for this transact type. Proceed to redirect to Currency Maintenance.',
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
                                        window.location.href = "{{ URL::to('/') }}" + '/' + "editDenom" +'/'+ rate_conf_selected_curr_id;
                                    }, 500);
                                });
                            });
                        } else if (parseFloat(rate_conf_selected_curr_rate) == .0) {
                            Swal.fire({
                                icon: 'error',
                                text: 'No available rate for this currency. Proceed to redirect to Rate Maitenance.',
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
                                });s
                            });
                        } else {
                            clearStock();
                            $('#container-test').fadeOut("fast");

                            var rib_buying_rate = parseFloat($('#rate-config-selected-rate').val()) - parseFloat($('#rib-variance').val());
                            var rib_selling_rate = parseFloat($('#rate-config-selected-rate').val()) + parseFloat($('#rib-variance').val());

                            new_curr_denom.forEach(function(curr_denom) {
                                var table = $('#rate-config-table');
                                var new_row_rate_config = $('<tr id="appended-tbody-element">');
                                var denomination_cell = $('<td class="text-center p-1 text-sm"><input class="form-control text-right" value="'+ parseFloat(curr_denom.BillAmount) +'" name="rate-config-denominations[]" readonly><input class="form-control rate-config-var-mnl-rate right" type="hidden" id="" name="rate-config-var-mnl-rate[]" value="'+ rate_conf_selected_curr_rate +'" readonly></td></td>');
                                var mnl_rate_cell = $('<td class="text-center p-1 text-sm"><input class="form-control rate-config-var-mnl-rate text-right" type="number" id="" name="rate-config-var-mnl-rate[]" value="'+ rate_conf_selected_curr_rate +'" readonly></td>');
                                var var_buying_cell = $('<td class="text-center p-1 text-sm"><input class="form-control rate-config-var-buying text-right" type="number" id="" name="rate-config-var-buying[]" value="'+ (curr_denom.VarianceBuying !== null ? curr_denom.VarianceBuying : '0.0000') + '"></td>');
                                var var_selling_cell = $('<td class="text-center p-1 text-sm"><input class="form-control rate-config-var-selling text-right" type="number" id="" name="rate-config-var-selling[]" value="'+ (curr_denom.VarianceSelling !== null ? curr_denom.VarianceSelling : '0.0000') + '"></td>');
                                var coins_cell = $('<td class="text-center p-1 text-sm"><input class="form-control rate-config-var-coins text-right" type="number" id="" name="rate-config-var-coins[]" value="'+ (curr_denom.Coins !== null ? curr_denom.Coins : '0.0000') + '"></td>');
                                var sinag_rate_buying_cell = $('<td class="text-center p-1 text-sm rate-config-buying-cell"><input class="form-control rate-config-sinag-rate-buying text-right" type="number" id="" name="rate-config-sinag-rate-buying[]" value="'+ (curr_denom.SinagRateBuying !== null ? curr_denom.SinagRateBuying.toLocaleString("en" , {minimumFractionDigits: 4, maximumFractionDigits: 4}) : '0.0000') + '" readonly></td>');
                                var sinag_rate_selling_cell = $('<td class="text-center p-1 text-sm rate-config-selling-cell"><input class="form-control rate-config-sinag-rate-selling text-right" type="number" id="" name="rate-config-sinag-rate-selling[]" value="'+ (curr_denom.SinagRateSelling !== null ? curr_denom.SinagRateSelling.toLocaleString("en" , {minimumFractionDigits: 4, maximumFractionDigits: 4}) : '0.0000') + '" readonly></td>');

                                new_row_rate_config.append(denomination_cell);
                                new_row_rate_config.append(var_buying_cell);
                                new_row_rate_config.append(sinag_rate_buying_cell);
                                new_row_rate_config.append(var_selling_cell);
                                new_row_rate_config.append(sinag_rate_selling_cell);

                                table.find('tbody').append(new_row_rate_config);
                                new_row_rate_config.hide().fadeIn(250);

                                var_buying_cell.find('.rate-config-var-buying').on('keyup', function() {
                                    var var_buying = parseFloat($(this).val());
                                    var sinag_rate_buying = parseFloat(rate_conf_selected_curr_rate) - var_buying;

                                    $(this).closest('tr').find('.rate-config-sinag-rate-buying').val(sinag_rate_buying.toLocaleString("en" , {minimumFractionDigits: 4, maximumFractionDigits: 4}));
                                });

                                var_selling_cell.find('.rate-config-var-selling').on('keyup', function() {
                                    var var_selling = parseFloat($(this).val());
                                    var sinag_rate_selling = parseFloat(rate_conf_selected_curr_rate) + var_selling;

                                    $(this).closest('tr').find('.rate-config-sinag-rate-selling').val(sinag_rate_selling.toLocaleString("en" , {minimumFractionDigits: 4, maximumFractionDigits: 4}));
                                });
                            });
                        }
                    }
                });
            }
        });

        function disabledFields() {
            $('#rate-config-table tbody tr').empty();
            $('#appended-tbody-element').empty();
            $('#rate-config-selected-rate').val('');
            $('#rate-config-selected-curr-abbrv').val('');
            $('#rib-variance').val('');
            $('#update-rate-config-banner').show();
            $('#apply-rate-config-button').attr('disabled', 'disabled');
            $('#rate-config-branch-select').attr('disabled', 'disabled');
            $('.rate-config-branch-select-one').attr('disabled', 'disabled');
            $('#update-rate-config-button').attr('disabled', 'disabled');
            $('#om-code').attr('disabled', 'disabled');
            $('#branch-search').attr('disabled', 'disabled');
            $('#clear-search-filter').attr('disabled', 'disabled');
            $('#rib-variance').val('');
            $('#rib-buying-rate').val('');
            $('#rib-selling-rate').val('');
        }

        $('#apply-rate-config-button').click(function() {
            var selected_branch = [];
            var selected_branch_id = [];

            $('.rate-config-branch-select-one:checked').each(function() {
                selected_branch.push($(this).val());
                selected_branch_id.push($(this).attr('data-rconfigbranchid'));
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
                $('#update-rate-config-button').attr('disabled', 'disabled');
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

                            $('#update-rate-config-button').removeAttr('disabled');
                        })
                    }
                });

                var processed_branch_array = selected_branch_id.join(", ");
                $('#rate-config-selected-branch').val(processed_branch_array);
            }
        });

        $('#update-rate-config-button').click(function() {
            var selected_branch = [];
            var selected_branch_id = [];
            var var_buying_fields = [];
            var var_selling_fields = [];

            $('.rate-config-branch-select-one:checked').each(function() {
                selected_branch.push($(this).val());
                selected_branch_id.push($(this).attr('data-rconfigbranchid'));
            });

            $('.rate-config-var-buying').each(function() {
                var_buying_fields.push(parseFloat($(this).val()));
            });

            $('.rate-config-var-selling').each(function() {
                var_selling_fields.push(parseFloat($(this).val()));
            });

            var parsed_branch = selected_branch.join(', ');
            var user_sec_code_rate_config = $('#user-security-code').val();

            if (var_buying_fields.includes(parseFloat(0.00))) {
                Swal.fire({
                    icon: 'error',
                    text: 'All fields for variance (buying) is required.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else if (var_selling_fields.includes(parseFloat(0.00))) {
                Swal.fire({
                    icon: 'error',
                    text: 'All fields for variance (selling) is required.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else {
                if (selected_branch == '') {
                    Swal.fire({
                        icon: 'error',
                        text: 'Select a branch.',
                        customClass: {
                            popup: 'my-swal-popup',
                        }
                    });
                } else {
                    var processed_branch_array = selected_branch_id.join(", ");
                    $('#rate-config-selected-branch').val(processed_branch_array);

                    Swal.fire({
                        title: 'Are you sure?',
                        html: 'Apply rates to branches selected?<br>You can still change branches before applying.',
                        icon: 'question',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#8592a3',
                        showCancelButton: true,
                        confirmButtonText: 'Proceed',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#security-code-modal').modal('show');
                        }
                    });
                }
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
            $('#rate-config-table tbody tr').empty();
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

        function searchSerials(search_branch, om_code) {
            $('#branch-list-container-list').css('height', 0);

            regex_branch = new RegExp('^' + search_branch, 'i');
            regex_om = new RegExp('^' + om_code + '$', 'i');

            $('#rate-config-branch-select').prop('checked', false);
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
                            var searched_branch = parseFloat($(this).find('.rate-config-branch-select-one').data('omid'));
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
                                var searched_branch = parseFloat($(this).find('.rate-config-branch-select-one').data('omid'));
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
    });

    $(document).ready(function() {
        $('#proceed-transaction').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#security-code').val();

            $('#proceed-transaction').prop('disabled', true);

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
                            text: 'Rate configuration updated!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            var form_data = new FormData($('#rate-config-form')[0]);
                            form_data.append('matched_user_id', matched_user_id);

                            $.ajax({
                                url: "{{ route('maintenance.rate_configuration.update') }}",
                                type: "post",
                                data: form_data,
                                contentType: false,
                                processData: false,
                                cache: false,
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
                        }).then(() => {
                            $('#proceed-transaction').prop('disabled', false);
                        });
                    }
                }
            });
        });
    });

    $(document).ready(function() {
        $('.current-config').click(function() {
            $.ajax({
                url: "{{ route('maintenance.rate_configuration.config_history') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    branch_id: $(this).attr('data-branchid')
                },
                success: function(data) {
                    clear();

                    const config_details = data.config_details;

                    var item_length = config_details.length;
                    var Currency = config_details.Currency;
                    var CurrencyID = config_details.CurrencyID;
                    var BillAmount = config_details.BillAmount;
                    var ManilaRate = config_details.ManilaRate;
                    var SinagRateBuying = config_details.SinagRateBuying;
                    var SinagRateSelling = config_details.SinagRateSelling;
                    var VarianceBuying = config_details.VarianceBuying;
                    var VarianceSelling = config_details.VarianceSelling;

                    config_details.forEach(function(gar) {
                        currencies(gar.Currency, gar.CurrencyID);
                        currentApplied(gar.Currency, gar.CurrencyID, gar.BillAmount, gar.ManilaRate, gar.SinagRateBuying, gar.SinagRateSelling, gar.VarianceBuying, gar.VarianceSelling, item_length);
                        // currentApplied(gar.Currency, gar.BillAmount.split(','), gar.ManilaRate ? gar.ManilaRate.split(',') : [], gar.SinagRateBuying.split(','), gar.SinagRateSelling.split(','), gar.VarianceBuying.split(','), gar.VarianceSelling.split(','));
                    });

                }
            });
        });

        var table_height = '';

        function currentApplied(Currency, CurrencyID, BillAmount, ManilaRate, SinagRateBuying, SinagRateSelling, VarianceBuying, VarianceSelling, item_length) {
            var bill_amount = BillAmount.split(',');
            var manila_rate = ManilaRate == null ? 0 : ManilaRate.split(',');
            var variance_buying = VarianceBuying.split(',');
            var variance_selling = VarianceSelling.split(',');
            var sinag_rate_buying = SinagRateBuying.split(',');
            var sinag_rate_selling = SinagRateSelling.split(',');

            var currently_applied_config_table = `
                <div class="row curr-row currency-config-row-${CurrencyID}" data-currency="${CurrencyID}">
                    <div class="col-12 currency-name mt-2">
                        <div class="row"><span class=" font-bold">${Currency}</span></div>
                    </div>

                    <div class="col-12 current-config-table mb-0 mt-1">
                        <table class="table table-bordered table-hover mb-2" id="rate-config-table">
                            <thead>
                                <tr>
                                    <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.w_rate_config_denom') }}</th>
                                    <th class="text-center text-sm font-extrabold text-black p-1">Manila Rate</th>
                                    <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.w_rate_config_variance_b') }}</th>
                                    <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.w_rate_config_variance_s') }}</th>
                                    <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.w_rate_config_fnl_rate_buying') }}</th>
                                    <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.w_rate_config_fnl_rate_selling') }}</th>
                                </tr>
                            </thead>
                            <tbody>`;

                            var merged_data = bill_amount.map(function(bills_val, bills_index) {
                                function toFixedTruncate(num, decimalPlaces) {
                                    const factor = Math.pow(10, decimalPlaces);
                                    return Math.floor(num * factor) / factor;
                                }

                                return {
                                    bill_amount: toFixedTruncate(parseFloat(bills_val), 4),
                                    mnl_rate: toFixedTruncate(parseFloat(manila_rate[bills_index]), 4),
                                    variance_b: toFixedTruncate(parseFloat(variance_buying[bills_index]), 4),
                                    variance_s: toFixedTruncate(parseFloat(variance_selling[bills_index]), 4),
                                    sinag_rate_b: toFixedTruncate(parseFloat(sinag_rate_buying[bills_index]), 4),
                                    sinag_rate_s: toFixedTruncate(parseFloat(sinag_rate_selling[bills_index]), 4),
                                };
                            });

                            merged_data.forEach(function(gar) {
                                currently_applied_config_table +=
                                    `<tr>
                                        <td class="text-sm py-1 pe-2 text-right">${gar.bill_amount}</td>
                                        <td class="text-sm py-1 pe-2 text-right">${gar.mnl_rate.toLocaleString("en" , {minimumFractionDigits: 4, maximumFractionDigits: 4})}</td>
                                        <td class="text-sm py-1 pe-2 text-right">${gar.variance_b.toLocaleString("en" , {minimumFractionDigits: 4, maximumFractionDigits: 4})}</td>
                                        <td class="text-sm py-1 pe-2 text-right">${gar.variance_s.toLocaleString("en" , {minimumFractionDigits: 4, maximumFractionDigits: 4})}</td>
                                        <td class="text-sm py-1 pe-2 text-right">${gar.sinag_rate_b.toLocaleString("en" , {minimumFractionDigits: 4, maximumFractionDigits: 4})}</td>
                                        <td class="text-sm py-1 pe-2 text-right">${gar.sinag_rate_s.toLocaleString("en" , {minimumFractionDigits: 4, maximumFractionDigits: 4})}</td>
                                    </tr>`;
                            });

                    currently_applied_config_table += `
                            </tbody>
                        </table>
                    </div>
                </div>
            `;

            $('#config-container').append(currently_applied_config_table);

            if (item_length >= 13) {
                $('#config-container').css({
                    height: item_length * 22
                });

                table_height = item_length * 22;
            } else if (item_length < 2) {
                $('#config-container').css({
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
                    $('.currency-config-row-' + currency_id).show();

                    $('#config-container').css({
                        height: 'auto'
                    });
                } else if (currency_id == 'default') {
                    $('#container-test').fadeOut("fast");
                    $('.currency-config-row-' + curr_id).show();

                    $('#config-container').css({
                        height: table_height
                    });
                } else {
                    $('#container-test').fadeOut("fast");
                    $('.currency-config-row-' + curr_id).hide();
                }
            });
        }

        function clear() {
            $('#config-container').empty();
        }
    });
</script>
