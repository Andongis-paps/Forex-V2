<script>
    $(document).ready(function() {
        $('#transfer-forex-select-all').click(function() {
            var transfer_forex_check_stat = $(this).prop('checked');

            if (transfer_forex_check_stat == true) {
                $('.receive-transf-select-one').each(function() {
                    $(this).prop('checked', true);
                });
            } else {
                $('.receive-transf-select-one').each(function() {
                    $(this).prop('checked', false);
                });
            }
        });

        var search_type = 1;
        var selected_transf_fx_no = [];

        $('input[name="radio-search-type"]').change(function() {
            search_type = parseInt($(this).val());

            $('#search-tracking-number').val('');
            $('#search-transfer-forex-number').val('');
            $('#transfer-forex-select-all').prop('checked', false);

            if ($(this).val() == 1) {
                $('#search-by-transfer-no').addClass('d-none').fadeOut(500);
                $('#search-by-trans-type').removeClass('d-none').hide().fadeIn(500);
                $('#search-by-tracking-no').addClass('d-none').fadeOut(500);
            } else if ($(this).val() == 2) {
                $('#search-by-transfer-no').removeClass('d-none').hide().fadeIn(500);
                $('#search-by-trans-type').addClass('d-none').fadeOut(500);
                $('#search-by-tracking-no').addClass('d-none').fadeOut(500);
            } else if ($(this).val() == 3) {
                $('#search-by-transfer-no').addClass('d-none').fadeOut(500);
                $('#search-by-trans-type').addClass('d-none').fadeOut(500);
                $('#search-by-tracking-no').removeClass('d-none').hide().fadeIn(500);
            }

            selected_transf_fx_no = [];
            $('#transfers-result-table-tbody').empty(200);

            var test = `<tr>
                    <td class="text-center text-td-buying text-sm py-3" colspan="12" id="empty-receive-transf-table">
                        <span class="buying-no-transactions text-lg">
                            <strong>SEARCH FOR TRANSFERS</strong>
                        </span>
                    </td>
                </tr>`;

            $('#transfers-result-table-tbody').append(test);
        });

        $('input[name="radio-receive-transfer-type"]').change(function() {
            selected_transf_fx_no = [];
            $('#transfers-result-table-tbody').empty(200);

            var test = `<tr>
                    <td class="text-center text-td-buying text-sm py-3" colspan="12" id="empty-receive-transf-table">
                        <span class="buying-no-transactions text-lg">
                            <strong>SEARCH FOR TRANSFERS</strong>
                        </span>
                    </td>
                </tr>`;

            $('#transfers-result-table-tbody').append(test);
        });

        let barcode = '';
        let barcodeTimeout;

        $(document).on('keyup', function(event) {
            clearTimeout(barcodeTimeout);

            if (event.which === 13) {
                if (barcode.length > 0) {
                    console.log('Barcode scanned:', barcode);
                    searchByTrackingNo(barcode);
                    barcode = '';
                }
            } else {
                barcode += String.fromCharCode(event.which);

                barcodeTimeout = setTimeout(function() {
                    barcode = '';
                }, 100);
            }
        });

        function searchByTrackingNo(barcode) {
            $('#tracking-no').prop('checked', true);
            $('input[name="radio-search-type"]').click();

            $('#search-tracking-number').val(barcode);

            // $('#search-tracking-number').on('change', function () {
            // var tracking_no = $(this).val();
            var tracking_no = barcode;
            var transf_forex_no = $('#search-transfer-forex-number').val();
            var currency = $('#currency').val();

            selected_transf_fx_no.push(transf_forex_no);
            console.log(selected_transf_fx_no.length);
            var parsed_transf_fx_no = selected_transf_fx_no.join(", ");

            searchResult(currency, transf_forex_no, parsed_transf_fx_no, tracking_no);

            $(this).val('');
            // });
        }

        $('#search-buffer-transf').click(function() {
            var tracking_no = $('#search-tracking-number').val();
            var transf_forex_no = $('#search-transfer-forex-number').val();
            var transact_type = $('input[name="radio-receive-transfer-type"]:checked').val();

            if (search_type == 1) {
                searchResult(transact_type, transf_forex_no);
            } else if (search_type == 2) {
                if (transf_forex_no == '') {
                    Swal.fire({
                        icon: 'error',
                        text: 'No transfer(s) found.',
                    });
                } else if (selected_transf_fx_no.includes(transf_forex_no)) {
                    Swal.fire({
                        icon: 'error',
                        text: 'Transfer is already on the list.',
                    });
                } else {
                    selected_transf_fx_no.push(transf_forex_no);
                    var parsed_transf_fx_no = selected_transf_fx_no.join(", ");

                    searchResult(transact_type, transf_forex_no, parsed_transf_fx_no);
                }
            } else if (search_type == 3) {
                searchResult(transact_type, transf_forex_no, parsed_transf_fx_no, tracking_no);
            }
        });

        function searchResult(transact_type, transf_forex_no, parsed_transf_fx_no, tracking_no) {
            $.ajax({
                url: "{{ route('admin_transactions.buffer.search_buffer') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    search_type: search_type,
                    tracking_no: tracking_no,
                    transact_type: transact_type,
                    parsed_transf_fx_no: parsed_transf_fx_no
                },
                success: function(data) {
                    var results = data.buffer_transf_search;
                    var result_length = results.length;

                    if (result_length === 0) {
                        Swal.fire({
                            icon: 'error',
                            text: 'No transfer(s) found.',
                        });
                    } else {
                        clearTransferTable();

                        $('#container-test').fadeIn("slow");
                        $('#container-test').css('display', 'block');

                        $('#transfer-forex-select-all').prop('checked', false).attr('disabled', false);

                        setTimeout(function() {
                            results.forEach(function(gar) {
                                transfResults(gar.TFID, gar.TFDate, gar.BranchCode, gar.CurrencyID, gar.Currency, gar.TFNO, gar.TrackingNo, gar.TFEntryDate, gar.TFRemarks, gar.RTReceived, gar.RTDate, gar.RTRemarks, gar.RTReceivedBy, gar.DollarAmount, gar.BufferType);
                            });

                            $('#container-test').fadeOut("slow");
                            $('#receive-buffer-btn').removeAttr('disabled');
                            $('#search-transfer-forex-number').removeAttr('disabled');
                        },2000);
                    }
                }
            });
        }

        function transfResults(TFID, TFDate, BranchCode, CurrencyID, Currency, TFNO, TrackingNo, TFEntryDate, TFRemarks, RTReceived, RTDate, RTRemarks, RTReceivedBy, DollarAmount, BufferType) {
            $('#empty-receive-transf-table').hide();

            var table = $('#transfers-result-table');
            var row = $('<tr>')

            if (search_type == 1) {
                var select_tf = $('<td class="text-center text-xs p-2 tf-check-box"><div class="row align-items-center"><div class="text-rate-maintenance col-12 px-0"><input class="form-check-input receive-transf-select-one" type="checkbox" name="transfer-forex-bill-select" data-tfid="'+ TFID +'" data-buffertype="'+ BufferType +'"></div></div></td>');
            } else if (search_type == 2) {
                var select_tf = $('<td class="text-center text-xs p-2 tf-check-box"><div class="row align-items-center"><div class="text-rate-maintenance col-12 px-0"><input class="form-check-input receive-transf-select-one" type="checkbox" name="transfer-forex-bill-select" data-tfid="'+ TFID +'" data-buffertype="'+ BufferType +'"></div></div></td>');
            } else if (search_type == 3) {
                var select_tf = $('<td class="text-center text-xs p-2 tf-check-box"><div class="row align-items-center"><div class="text-rate-maintenance col-12 px-0"><input class="form-check-input receive-transf-select-one" type="checkbox" name="transfer-forex-bill-select" data-tfid="'+ TFID +'" data-buffertype="'+ BufferType +'"></div></div></td>');
            }

            var date = $('<td class="text-center text-xs p-2">'+ TFDate +'</td>')
            var branch_code = $('<td class="text-center text-xs p-2">'+ BranchCode +'</td>')
            var currency = $('<td class="text-center text-xs p-2">'+ Currency +'</td>')
            var tfno = $('<td class="text-center text-xs p-2">'+ TFNO +'</td>')
            var tfno_hidden = $('<td class="text-center text-xs p-2 tfno-row" hidden><input class="transfer-forex-number-input" type="hidden" value="'+ TFNO +'"></td>')
            var tracking_no = $('<td class="text-center text-xs p-2">' + (TrackingNo ? TrackingNo : ' ') + '</td>');
            var buffer_type = $('<td class="text-center text-xs p-2">' + (BufferType == 1 ? "For Selling" : "Additional Buffer") + '</td>');
            var amount = $('<td class="text-center text-xs p-2"><strong>' + DollarAmount.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong></td>');

            row.append(select_tf);
            row.append(date);
            row.append(branch_code);
            row.append(currency);
            row.append(tfno);
            row.append(tfno_hidden);
            row.append(tracking_no);
            row.append(buffer_type);
            row.append(amount);

            table.find('tbody').append(row);
            row.hide().fadeIn(250);
        }

        function clearTransferTable() {
            $('#item-count-serials').empty();
            $('#transfers-result-table tbody').empty();
        }

        var TFIDs = '';

        $('#receive-buffer-btn').click(function() {
            var tfid_array = [];

            $('.receive-transf-select-one:checked').each(function() {
                if (!tfid_array.includes($(this).attr('data-tfid'))) {
                    tfid_array.push($(this).attr('data-tfid'));
                }
            });

            TFIDs = tfid_array.join(",");

            if (tfid_array.length === 0) {
                Swal.fire({
                    icon: 'error',
                    text: 'No transfer forex selected.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else {
                $('#security-code-modal').modal("show");
            }
        });

        $('#proceed-transaction').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var buff_types_array = [];
            var user_sec_onpage = $('#security-code').val();

            $('.receive-transf-select-one:checked').each(function() {
                buff_types_array.push($(this).attr('data-buffertype'));
            });

            buffer_types = buff_types_array.join(",");

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
                            text: 'Buffer Received!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            setTimeout(function() {
                                $.ajax({
                                    url: "{{ route('admin_transactions.buffer.receive') }}",
                                    type: "post",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        TFIDs: TFIDs,
                                        buffer_types: buffer_types,
                                        matched_user_id: matched_user_id
                                    },
                                    success: function(response) {
                                        $('#container-test').fadeIn("slow");
                                        $('#container-test').css('display', 'block');

                                        var url = "{{ route('admin_transactions.buffer.buffer_transfers') }}";

                                        window.location.href = url;
                                    }
                                });
                            }, 200);
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

        // Receiving section scripts
        // $('.receive-buffer-transfer').click(function() {
        //     $('#security-code-modal').modal("show");
        //     var buffer_transfer_id = $(this).attr('data-bufferid');

        //     receiveBuffer(buffer_transfer_id)
        // });

        // function receiveBuffer(buffer_transfer_id) {

        // }
    });
</script>
