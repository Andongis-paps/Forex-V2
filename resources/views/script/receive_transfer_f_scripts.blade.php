{{-- Receive Transfer Forex module scripts --}}
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

        var selected_tracking_no = [];
        var selected_transf_fx_no = [];

        $('input[name="radio-search-type"]').change(function() {
            $('#search-tracking-number').val('');
            $('#search-transfer-forex-number').val('');

            if ($(this).val() == 1) {
                $('#search-by-transfer-no').addClass('d-none').fadeOut(500);
                $('#search-by-transfer-type').removeClass('d-none').hide().fadeIn(500);
                $('#search-by-tracking-no').addClass('d-none').fadeOut(500);
            } else if ($(this).val() == 2) {
                $('#search-by-transfer-no').removeClass('d-none').hide().fadeIn(500);
                $('#search-by-transfer-type').addClass('d-none').fadeOut(500);
                $('#search-by-tracking-no').addClass('d-none').fadeOut(500);
            } else if ($(this).val() == 3) {
                $('#search-by-transfer-no').addClass('d-none').fadeOut(500);
                $('#search-by-transfer-type').addClass('d-none').fadeOut(500);
                $('#search-by-tracking-no').removeClass('d-none').hide().fadeIn(500);
            }

            selected_tracking_no = [];
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
            selected_tracking_no = [];
            selected_transf_fx_no = [];
            $('#transfers-result-table-tbody').empty(200);

            var test = `<tr>
                    <td class="text-center text-td-buying text-sm py-3" colspan="12" id="empty-receive-transf-table">
                        <span class="buying-no-transactions text-lg">
                            <strong>START SEARCHING FOR TRANSFERS</strong>
                        </span>
                    </td>
                </tr>`;

            $('#transfers-result-table-tbody').append(test);
        });

        let barcode = '';
        let barcodeTimeout;

        var tracking_nos = [];

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

            var tracking_no = barcode;
            var transf_forex_no = $('#search-transfer-forex-number').val();
            var transfer_type = $('input[name="radio-receive-transfer-type"]:checked').val();

            tracking_nos.push(tracking_no);
            var parsed_tracking_no = tracking_nos.join(", ");

            selected_transf_fx_no.push(transf_forex_no);
            var parsed_transf_fx_no = selected_transf_fx_no.join(", ");

            transferSearchResult(transfer_type, transf_forex_no, parsed_transf_fx_no, parsed_tracking_no);
            // transferSearchResult(transfer_type, transf_forex_no, parsed_transf_fx_no, tracking_no);

            $(this).val('');
        }

        $('#button-search-transfer-forex').click(function() {
            var tracking_no = $('#search-tracking-number').val();
            var transf_forex_no = $('#search-transfer-forex-number').val();
            var transfer_type = $('input[name="radio-receive-transfer-type"]:checked').val();

            if ($('input[name="radio-search-type"]:checked').val() == 1) {
                transferSearchResult(transfer_type, transf_forex_no);
            } else if ($('input[name="radio-search-type"]:checked').val() == 2) {
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

                    transferSearchResult(transfer_type, transf_forex_no, parsed_transf_fx_no);
                }
            } else if ($('input[name="radio-search-type"]:checked').val() == 3) {
                if (tracking_no == '') {
                    Swal.fire({
                        icon: 'error',
                        text: 'No transfer(s) found.',
                    });
                } else if (selected_tracking_no.includes(tracking_no)) {
                    Swal.fire({
                        icon: 'error',
                        text: 'Transfer is already on the list.',
                    });
                } else {
                    selected_tracking_no.push(tracking_no);
                    var parsed_tracking_no = selected_tracking_no.join(", ");

                    transferSearchResult(transfer_type, transf_forex_no, parsed_transf_fx_no, parsed_tracking_no);
                }
            }
        });

        function transferSearchResult(transfer_type, transf_forex_no, parsed_transf_fx_no, parsed_tracking_no) {
            $.ajax({
                url: "{{ route('admin_transactions.receive_transfer_forex.search') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    search_type: $('input[name="radio-search-type"]:checked').val(),
                    parsed_tracking_no: parsed_tracking_no,
                    transfer_type: transfer_type,
                    parsed_transf_fx_no: parsed_transf_fx_no
                },
                success: function(data) {
                    var transf_results = data.transfer_forex_search;
                    var total_results_transf_results = transf_results.length;

                    if (total_results_transf_results === 0) {
                        Swal.fire({
                            icon: 'error',
                            text: 'No transfer(s) found.',
                        });

                        // $('#transfer-forex-select-all').prop('checked', false);
                    } else {
                        clearTransferTable();
                        // $('#transfer-forex-select-all').prop('checked', true);

                        // var starting_index_transfers = (r_transf_current_page - 1) * r_transf_items_per_page;
                        // var ending_index_transf = Math.min(starting_index_transfers + r_transf_items_per_page, total_results_transf_results);
                        // var r_transf_search_results = transf_results.slice(starting_index_transfers, ending_index_transf);

                        $('#container-test').fadeIn("slow");
                        $('#container-test').css('display', 'block');

                        setTimeout(function() {
                            transf_results.forEach(function(transfer_data) {
                                transferForexResults(transfer_data.BranchCode, transfer_data.TrackingNo, transfer_data.TFNO, transfer_data.TFDate, transfer_data.TFID, transfer_data.TFRemarks, transfer_data.TFEntryDate, transfer_data.RTReceived,
                                transfer_data.RTDate, transfer_data.RTRemarks, transfer_data.RTReceivedBy);
                            });

                            // paginationReceiveTransf(total_results_transf_results);
                            // itemCount(starting_index_transfers + 1, ending_index_transf, total_results_transf_results);

                            $('#container-test').fadeOut("slow");
                            $('#receive-transfers-button').removeAttr('disabled');
                            $('#search-transfer-forex-number').removeAttr('disabled');
                        },2000);
                    }
                }
            });
        }

        // var r_transf_current_page = 1;
        // var r_transf_items_per_page = 15;

        // function paginationReceiveTransf(total_transfers) {
        //     var total_pages_r_transf = Math.ceil(total_transfers / r_transf_items_per_page);

        //     var pagination_buttons_r_transf = $('#pagination-buttons-receive-transf');
        //     pagination_buttons_r_transf.empty();

        //     var display_limit_r_transf = 15;

        //     var start_page_r_transf = Math.max(1, r_transf_current_page - Math.floor(display_limit_r_transf / 2));
        //     var end_page_r_transf = Math.min(total_pages_r_transf, start_page_r_transf + display_limit_r_transf - 1);

        //     // Add "Previous" button
        //     var prev_button_container_r_transf = $('<span class="font-semibold text-sm py-2 px-2 border rounded-tl rounded-bl cursor-pointer"></span>');
        //     var prev_button_r_transf = $('<a id="previous-button-receive-transfer"><i class="bx bx-chevron-left"></i></a>');

        //     prev_button_container_r_transf.append(prev_button_r_transf);
        //     pagination_buttons_r_transf.append(prev_button_container_r_transf);

        //     if (r_transf_current_page == 1) {
        //         prev_button_r_transf.attr('disabled', 'disabled');
        //         prev_button_container_r_transf.css('cursor', 'context-menu');
        //     } else {
        //         prev_button_r_transf.click(function() {
        //             r_transf_current_page--;
        //             $('#button-search-transfer-forex').trigger('click');
        //         });
        //     }

        //     for (var r_transf = start_page_r_transf; r_transf <= end_page_r_transf; r_transf++) {
        //         var span_r_transf = $('<span class="span-pagination-conatiner font-semibold text-sm py-2 px-2 border">').css('cursor', 'pointer');
        //         var button_r_transf = $('<a class="span-pagination-button py-2 px-2">').css('cursor', 'pointer');

        //         if (r_transf === r_transf_current_page) {
        //             button_r_transf.addClass('active-pagination');
        //             button_r_transf.css('color', '#fff');
        //             button_r_transf.css('font-weight', '700');
        //             span_r_transf.css('background-color', '#00A65A');
        //             span_r_transf.css('border', '1px solid #00A65A');
        //             span_r_transf.removeClass('border');
        //         }

        //         button_r_transf.text(r_transf);

        //         button_r_transf.click(function(page_r_transf) {
        //             return function() {
        //                 r_transf_current_page = page_r_transf;
        //                 $('#button-search-transfer-forex').trigger('click');
        //             };
        //         }(r_transf));

        //         span_r_transf.append(button_r_transf);
        //         pagination_buttons_r_transf.append(span_r_transf);
        //     }

        //     // Add "Next" button - Serials
        //     var next_button_container_r_transf= $('<span class="font-semibold text-sm py-2 px-2 border rounded-tr rounded-br cursor-pointer"></span>');
        //     var next_button_r_transf = $('<a id="next-button-receive-transfer"><i class="bx bx-chevron-right"></i></i></a>');

        //     next_button_container_r_transf.append(next_button_r_transf);
        //     pagination_buttons_r_transf.append(next_button_container_r_transf);

        //     if (r_transf_current_page < total_pages_r_transf) {
        //         next_button_r_transf.click(function() {
        //             r_transf_current_page++;
        //             $('#button-search-transfer-forex').trigger('click');
        //         });

        //     } else {
        //         next_button_r_transf.attr('disabled', 'disabled');
        //         next_button_container_r_transf.css('cursor', 'context-menu');
        //     }
        // }

        function transferForexResults(BranchCode, TrackingNo, TFNO, TFDate, TFID, TFRemarks, TFEntryDate, RTReceived, RTDate, RTRemarks, RTReceivedBy) {
            $('#empty-receive-transf-table').hide();

            var r_transf_table = $('#transfers-result-table');
            var r_transf_row = $('<tr>')

            if ($('input[name="radio-search-type"]:checked').val() == 1) {
                var select_tranferable = $('<td class="text-center text-sm p-1 tf-check-box"><div class="row align-items-center"><div class="text-rate-maintenance col-12 px-0"><input class="form-check-input receive-transf-select-one" type="checkbox" id="receive-transf-select" name="transfer-forex-bill-select" data-receivetfid="'+ TFID +'"></div></div></td>');
            } else if ($('input[name="radio-search-type"]:checked').val() == 2) {
                var select_tranferable = $('<td class="text-center text-sm p-1 tf-check-box"><div class="row align-items-center"><div class="text-rate-maintenance col-12 px-0"><input class="form-check-input receive-transf-select-one" type="checkbox" id="receive-transf-select" name="transfer-forex-bill-select" data-receivetfid="'+ TFID +'" checked></div></div></td>');
            } else if ($('input[name="radio-search-type"]:checked').val() == 3) {
                var select_tranferable = $('<td class="text-center text-sm p-1 tf-check-box"><div class="row align-items-center"><div class="text-rate-maintenance col-12 px-0"><input class="form-check-input receive-transf-select-one" type="checkbox" id="receive-transf-select" name="transfer-forex-bill-select" data-receivetfid="'+ TFID +'" checked></div></div></td>');
            }

            var r_transf_branch_code = $('<td class="text-center text-sm p-1">'+ BranchCode +'</td>')
            var r_transf_tfno = $('<td class="text-center text-sm p-1">'+ TFNO +'</td>')
            var r_transf_tracking_no = $('<td class="text-center text-sm p-1">' + (TrackingNo ? TrackingNo : ' ') + '</td>');
            var r_transf_tfdate = $('<td class="text-center text-sm p-1">'+ TFDate +'</td>')
            var r_transf_tfremarks = $('<td class="text-center text-sm p-1">'+ TFEntryDate +'</td>')
            var r_transf_tftrans_type = $('<td class="text-center text-sm p-1"><strong>'+ TFRemarks +'</strong></td>')
            var r_transf_tfentry_date = $('<td class="text-center text-sm p-1">'+ TFEntryDate +'</td>')
            var r_transf_tfno_hidden = $('<td class="text-center text-sm p-1 tfno-row" hidden><input class="transfer-forex-number-input" type="hidden" value="'+ TFNO +'"></td>')

            r_transf_row.append(select_tranferable);
            r_transf_row.append(r_transf_branch_code);
            r_transf_row.append(r_transf_tftrans_type);
            r_transf_row.append(r_transf_tfno);
            r_transf_row.append(r_transf_tracking_no);
            // r_transf_row.append(r_transf_tfdate);
            r_transf_row.append(r_transf_tfentry_date);
            r_transf_row.append(r_transf_tfno_hidden);

            r_transf_table.find('tbody').append(r_transf_row);
            r_transf_row.hide().fadeIn(250);
        }

        // function itemCount(starting_index, ending_index, total_items) {
        //     var item_count = $('#item-count-serials');
        //     var count_container = $('<div class="col-12">Showing <span class="text-sm font-bold report-count-text">'+ starting_index +'</span> to <span class="text-sm font-bold report-count-text"> '+ ending_index +'</span> of <span class="text-sm font-semibold">'+ total_items +' Results</span>');
        //     count_container.find('.report-count-text').css('color', '#4F5971');
        //     item_count.append(count_container);
        // }

        function clearTransferTable() {
            $('#item-count-serials').empty();
            $('#transfers-result-table tbody').empty();
        }

        $('#receive-transfers-button').click(function() {
            var selected_transfer_forex_tfid = [];

            $('.receive-transf-select-one:checked').each(function() {
                selected_transfer_forex_tfid.push($(this).attr('data-receivetfid'));
            });

            var processed_receive_transf_tfid = selected_transfer_forex_tfid.join(", ");
            $('#received-transfers-tfid').val(processed_receive_transf_tfid);

            if (selected_transfer_forex_tfid.length === 0) {
                Swal.fire({
                    icon: 'error',
                    text: 'No transfer forex selected.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else {
                // ajax dupe
                $.ajax({
                    url: "{{ route('admin_transactions.receive_transfer_forex.dupe_check') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        TFIDS: processed_receive_transf_tfid
                    },
                    success: function(data) {
                        Swal.fire({
                            title: "Checking for duplicate serial...",
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
                                dupeAlert(data.dupe_serials);
                            } else {
                                $('#security-code-modal').modal("show");
                            }
                        });
                    }
                });

            }

            function dupeAlert(dupe_serials) {
                let rows = '';
                let height = '';
                let serials = '';
                let plural = '';

                dupe_serials.forEach((value, index) => {
                    rows += `
                        <tr>
                            <td class="p-2 text-center text-sm border-t-gray-300">${value.Serials}</td>
                        </tr>`;
                });

                height = dupe_serials.length > 9? 'height: 300px!important;' : 'height: auto; ';
                serials = dupe_serials.length > 1? 'serials' : 'serial ';
                plural = dupe_serials.length > 1? 'are' : 'is ';
                plural_ulet = dupe_serials.length > 1? 'They are' : 'It is';

                // <div class="col-12">
                //     <span class="text-lg text-black">
                //         Duplicate entry alert!
                //     </span?
                // </div>

                Swal.fire({
                    title: 'Duplicate entry alert!',
                    icon: 'error',
                    html: `
                        <div class="col-12">
                            <span class="text-sm text-black">
                                The ${serials} listed below currently exist and haven't yet been sold.
                            </span?
                        </div>
                        <div class="col-12 mt-2 border border-gray-300 p-0" style="${height} overflow: hidden; overflow-y: scroll;">
                            <table class="table table-hover mb-0">
                                <thead style="position: sticky; top: 0; background: #fff; z-index: 3;">
                                    <tr>
                                        <th class="border-gray-300 py-1 text-black font-bold text-center" colspan="2">Serials</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rows}
                                </tbody>
                            </table>
                        </div>
                    `,
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            }
        });
    });

    $(document).ready(function() {
        var current_date = new Date();
        var year = current_date.getFullYear();
        var month = String(current_date.getMonth() + 1).padStart(2, '0');
        var day = String(current_date.getDate()).padStart(2, '0');

        var formatted_date = year + '-' + month + '-' + day;

        var set_date_from = $('#search-date-from-receive-transf').val(formatted_date).text(formatted_date);
        var set_date_to = $('#search-date-to-receive-transf').val(formatted_date).text(formatted_date);

        if(set_date_from.val() != '' && set_date_to.val() != '') {
            $('#receive-transf-branch-select').removeAttr('disabled').attr('checked', 'checked');
            $('.receive-transf-branch-select-one').removeAttr('disabled').attr('checked', 'checked');
        }
    });

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
                            text: 'Transfer(s) received!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            var form_data = new FormData($('#receive-transfers-form')[0]);
                            form_data.append('matched_user_id', matched_user_id);

                            $.ajax({
                                url: "{{ route('admin_transactions.receive_transfer_forex.save') }}",
                                type: "post",
                                data: form_data,
                                contentType: false,
                                processData: false,
                                cache: false,
                                success: function(data) {
                                    var url = "{{ route('admin_transactions.receive_transfer_forex') }}";

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

    // Receive Transfer Forex module - Search by Transfer Forex Number
    // $(document).ready(function() {
    //     $('#search-transfer-forex-number').keyup(function(){
    //         var search_word = $(this).val();
    //         var transfer_forex_table = $('#transfers-result-table');
    //         var t_forex_table_tbody = transfer_forex_table.find('#transfers-result-table-tbody');
    //         var t_forex_row_val = t_forex_table_tbody.find('.tfno-row .transfer-forex-number-input');
    //         var test = t_forex_table_tbody.find('.tf-check-box .receive-transf-select-one');

    //         t_forex_row_val.each(function() {
    //             if ($(this).val() == search_word) {
    //                 var closest_table_row = $(this).closest('tr')[0];
    //                 $(closest_table_row).addClass("search-highlight");

    //                 $(closest_table_row).find('.receive-transf-select-one').prop('checked', true);
    //                 closest_table_row.scrollIntoView({ behavior: "smooth", block: "center" });
    //             } else {
    //                 var closest_table_row = $(this).closest('tr')[0];
    //                 $(closest_table_row).find('.receive-transf-select-one').prop('checked', false);

    //                 $(this).closest('tr').removeClass("search-highlight");
    //             }
    //         });
    //     });

    //     $('#search-transfer-forex-number').keyup(function() {
    //         var search_word = $(this).val();
    //         var transfer_forex_table = $('#transfers-result-table');
    //         var t_forex_table_tbody = transfer_forex_table.find('#transfers-result-table-tbody');
    //         var t_forex_row_val = t_forex_table_tbody.find('.tfno-row .transfer-forex-number-input');

    //         if(search_word == '') {
    //             t_forex_row_val.each(function() {
    //                 $(this).removeClass("search-highlight").scrollTop();
    //             });
    //         }
    //     });
    // });
</script>
