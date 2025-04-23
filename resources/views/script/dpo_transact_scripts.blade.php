{{-- DPO Transact Scripts --}}
<script>
    $(document).ready(function() {
        $('.dpo-in-details').click(function() {
            $.ajax({
                url: "{{ route('admin_transactions.dpofx.dpo_in_details') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    DPDID: $(this).attr('data-dpdid')
                },
                success: function(data) {
                    resetTables();

                    var dpo_in_details = data.dpo_in_details;

                    dpo_in_details.forEach(function(gar) {
                        dpoInDetails(gar.BranchCode, gar.MTCN, gar.DollarAmount, gar.RateUsed, gar.Amount, gar.Rset, gar.EntryDate);
                    });
                }
            });
        });

        function dpoInDetails(BranchCode, MTCN, DollarAmount, RateUsed, Amount, Rset, EntryDate) {
            var table = $('#dpo-in-details-table');
            var row = $('<tr>');
            var branch = $('<td class="text-center text-sm p-1">'+ BranchCode +'</td>');
            var mtcn = $('<td class="text-center font-bold text-sm p-1">'+ MTCN +'</td>');
            var dollar_amnt = $('<td class="text-right text-sm py-1 pe-2">'+ DollarAmount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
            var rate = $('<td class="text-right text-sm py-1 pe-2">'+ RateUsed.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
            var peso_amnt = $('<td class="text-right text-sm py-1 pe-2">'+ Amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
            var receipt_set = $('<td class="text-center text-sm p-1">'+ Rset +'</td>');
            var entry_date = $('<td class="text-center text-sm p-1">'+ EntryDate +'</td>');

            row.append(entry_date);
            row.append(branch);
            row.append(receipt_set);
            row.append(mtcn);
            row.append(dollar_amnt);
            row.append(rate);
            row.append(peso_amnt);

            table.find('tbody').append(row);
        }

        function resetTables() {
            $('#dpo-in-details-table tbody').empty();
        }
    });

    $(document).ready(function() {
        $('#dpo-transact-date').flatpickr({
            mode: "range",
            maxDate: "today",
            allowInput: false,
            dateFormat: "Y-m-d",
        });

        $('#select-company').change(function() {
            if ($(this).val() != '') {
                $('input[name="dpo-reference-number"]').removeAttr('disabled');
                $('input[name="dpo-commission"]').removeAttr('disabled');
                $('input[name="dpo-transact-date"]').removeAttr('disabled');
            } else {
                $('input[name="dpo-reference-number"]').attr('disabled', 'disabled');
                $('input[name="dpo-commission"]').attr('disabled', 'disabled');
                $('input[name="dpo-transact-date"]').attr('disabled', 'disabled');
            }
        });

        $('#dpo-transact-date').change(function() {
            $('#generate-dpo-transacts').removeAttr('disabled');
        });

        $('#dpofx-select-all').click(function() {
            var rate_conf_check_stat = $(this).prop('checked');

            if (rate_conf_check_stat == true) {
                $('.dpofx-select-one').each(function() {
                    $(this).prop('checked', true);
                });
            } else {
                $('.dpofx-select-one').each(function() {
                    $(this).prop('checked', false);
                });
            }
        });

        $('#generate-dpo-transacts').click(function() {
            var raw_dates =  $('#dpo-transact-date').val();
            var dates = raw_dates.split(" TO ");
            var date_from = dates[0];
            var date_to = dates[1];

            $.ajax({
                url: "{{ route('admin_transactions.dpofx.DPOFXS') }}",
                type: "POST",
                data: {
                    company_id: $('#select-company').val(),
                    date_to: date_to,
                    date_from: date_from,
                    raw_dates: raw_dates,
                    _token: "{{ csrf_token() }}",
                },
                success: function(gar) {
                    var dpo_trans = gar.DPO_transacts

                    if (dpo_trans.length <= 0) {
                        Swal.fire({
                            icon: 'error',
                            text: 'No DPOFX available.',
                        });
                    } else {
                        $('#container-test').fadeIn("slow");
                        $('#container-test').css('display', 'block');

                        setTimeout(() => {
                            emptyDPOTable();

                            var total_dpo_amnt = 0;
                            var total_peso_amnt = 0;

                            $('#container-test').fadeOut("slow");
                            $('#save-dpo-transact').removeAttr('disabled');

                            dpo_trans.forEach(function(dpo) {
                                total_peso_amnt += dpo.Amount;
                                total_dpo_amnt += dpo.CurrencyAmount;

                                DPOFXtransacts(dpo.FTDID, dpo.BranchCode, dpo.CompanyName, dpo.MTCN, dpo.CurrencyAmount, dpo.Amount, dpo.TransactionDate, dpo.SinagRateBuying, dpo.Rset, total_peso_amnt, total_dpo_amnt);
                            });
                        }, 200);
                    }
                }
            });
        });

        function DPOFXtransacts(FTDID, BranchCode, CompanyName, MTCN, CurrencyAmount, Amount, TransactionDate, SinagRateBuying, Rset, total_peso_amnt, total_dpo_amnt) {
            var dpo_table = $('#dpofx-transacts-table');
            var new_row = $('<tr class="text-center text-sm">');
            var select_dpo = $('<td class="p-1"><div class="row align-items-center"><div class="text-rate-maintenance col-12 px-0"><input class="form-check-input dpofx-select-one" type="checkbox" id="dpofx-select-one" name="dpofx-select-one" data-ftdid="'+ FTDID +'" checked></div></div></td>');
            var branch = $('<td class="text-center text-sm p-1">'+ BranchCode +'</td>');
            var company = $('<td class="text-center text-sm p-1">'+ CompanyName +'</td>');
            var curr_amount = $('<td class="text-right text-sm py-1 px-3">'+ CurrencyAmount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
            var amount = $('<td class="text-right text-sm py-1 px-3"><strong>'+ Amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</strong></td>');
            var mtcn = $('<td class="text-center text-sm p-1">'+ MTCN +'</td>');
            var transact_date = $('<td class="text-center text-sm p-1">'+ TransactionDate +'</td>');
            var rate = $('<td class="text-right text-sm py-1 px-3">'+ SinagRateBuying.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
            // var commission = $('<td class="text-right text-sm p-2 commission-cells"><input class="form-control text-right" id="commission-field" name="commission-field[]" type="number" value="0" placeholder="0.00"></td>');
            var receipt_set = $('<td class="text-center text-sm p-1 commission-cells">'+ Rset +'<input type="hidden" name="receipt-set[]" value="'+ Rset +'"></td>');

            new_row.append(select_dpo);
            new_row.append(transact_date);
            new_row.append(branch);
            new_row.append(company);
            new_row.append(receipt_set);
            new_row.append(mtcn);
            // new_row.append(commission);
            new_row.append(curr_amount);
            new_row.append(rate);
            new_row.append(amount);

            $('#true-total-dpofx-amnt').val(total_dpo_amnt);
            $('#true-total-peso-amount').val(total_peso_amnt);

            $('#total-peso-amount').text(total_peso_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            $('#total-dpofx-amount').text(total_dpo_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));

            dpo_table.find('tbody').append(new_row);

            // commission.find('input').on('change', function() {
            //     commissionsFields();
            // });
        }

        // function commissionsFields() {
        //     var total_commision_amnt = 0;
        //     var table = $('#dpofx-transacts-table');
        //     var commission_inputs = table.find('.commission-cells');

        //     commission_inputs.each(function() {
        //         var commission_fields = $(this).closest('tr').find('.form-control#commission-field');

        //         total_commision_amnt += parseFloat(commission_fields.val());
        //     });

        //     $('input[name="dpo-commission"]').val(total_commision_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
        // }

        function emptyDPOTable() {
            $('#total-dpofx-amnt').text('');
            $('#total-peso-amount').text('');
            $('#true-total-dpofx-amnt').val('');
            $('#true-total-peso-amount').val('');

            $('#dpofx-transacts-table #dpofx-transacts-table-tbody').empty();
        }

        $('#save-dpo-transact').click(function() {
            $('#security-code-modal').modal("show");

            var FTDIDs = [];

            $('.dpofx-select-one').each(function() {
                var selected = $(this).prop('checked') == true;

                if (selected) {
                    FTDIDs.push($(this).attr('data-ftdid'));
                }
            });

            saveDPOIn(FTDIDs.join(", "));
        });

        function saveDPOIn(FTDIDs) {
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
                                text: 'DPO In Added!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                $('#container-test').fadeIn("slow");
                                $('#container-test').css('display', 'block');

                                var form_data = new FormData($('#dpofx-in-form')[0]);
                                form_data.append('matched_user_id', matched_user_id);
                                form_data.append('FTDIDs', FTDIDs);
                                form_data.append('total_dpo_amnt', $('#true-total-dpofx-amnt').val());
                                form_data.append('total_peso_amnt',  $('#true-total-peso-amount').val());

                                $.ajax({
                                    url: "{{ route('admin_transactions.dpofx.save_dpo_in') }}",
                                    type: "post",
                                    data: form_data,
                                    contentType: false,
                                    processData: false,
                                    cache: false,
                                    success: function(data) {
                                        var url = "{{ route('admin_transactions.dpofx.dpo_in') }}";

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
        }
    });
</script>
