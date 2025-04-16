<script>
    $(window).on('load', function() {
        $(".addressed-tags").select2();
    });

    $(document).ready(function() {
        $('#serial-search-field').keyup(function() {
            var serial_search_val = $(this).val();

            if (serial_search_val) {
                $.ajax({
                    url: "{{ route('admin_transactions.bill_tagging.search') }}",
                    method: "POST",
                    data: {
                        serial_search_val: serial_search_val,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        clearTable();

                        var serial_results = data.serial_results;
                        var bill_tags = data.bill_tags;

                        bill_tags.forEach(function(gar) {
                            var option_element = $('<option value="'+ gar.BillStatID +'">'+ gar.BillStatus +'</option>');

                            $('#tag-selection').append(option_element);
                        });

                        if (serial_results.length > 0) {
                            serial_results.forEach(function(gar) {
                                searchResults(gar.ID, gar.Currency, gar.BillAmount, gar.CMRUsed, gar.Serials, gar.BranchCode, gar.DateSold, gar.BID, gar.STMDID, gar.source_type, gar.BFID);
                            });

                            $('#serial-search-results').fadeIn(10);
                        } else {
                            $('#serial-search-results').fadeOut(10);
                        }

                        selectSerial();
                    }
                });
            } else {
                $('#serial-search-results').fadeOut(10);
            }
        });

        function searchResults(ID, Currency, BillAmount, CMRUsed, Serials, BranchCode, DateSold, BID, STMDID, source_type, BFID) {
            var result_table = $('#serial-search-result-table');
            var table_row = $('<tr>');
            var currency = $('<td class="text-xs text-center whitespace-nowrap p-1">'+ Currency +'</td>');
            var bill_amount = $('<td class="text-xs text-right whitespace-nowrap py-1 pe-3">'+ BillAmount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>');
            var serials = $('<td class="text-xs text-center whitespace-nowrap p-1">'+ Serials +'</td>');
            var branch = $('<td class="text-xs text-center whitespace-nowrap p-1">'+ BranchCode +'</td>');
            var transact_date = $('<td class="text-xs text-center whitespace-nowrap p-1">'+ DateSold +'</td>');
            var select_button =
                $(`<td class="text-xs text-center whitespace-nowrap p-1">
                    <button class="btn btn-primary button-edit button-select-serial" data-fsid="`+ ID +`" data-currency="`+ Currency +`" data-billamount="`+ BillAmount +`" data-cmrused="`+ CMRUsed +`" data-serial="`+ Serials +`" data-ftdid="`+ BID +`" data-stmdid="`+ STMDID +`" data-bfid="`+ BFID +`" data-source="`+ source_type +`">
                        <i class='menu-icon tf-icons bx bx-purchase-tag'></i>
                    </button>
                </td>`);

            table_row.append(currency);
            table_row.append(bill_amount);
            table_row.append(serials);
            table_row.append(branch);
            table_row.append(transact_date);
            table_row.append(select_button);

            result_table.find('tbody').append(table_row);
        }

        function clearTable() {
            $('#tag-selection').empty();
            $('#serial-search-result-table tbody').empty();
        }

        var selected_ids_array = [];
        var selected_curr_array = [];
        var selected_bill_amnt_array = [];
        var selected_serial_array = [];

        function selectSerial(bill_tags) {
            $('.button-select-serial').click(function() {
                var IDs = $(this).attr('data-fsid');
                var currency = $(this).attr('data-currency');
                var bill_amnt = $(this).attr('data-billamount');
                var cmr_used = $(this).attr('data-cmrused');
                var serial = $(this).attr('data-serial');
                var BIDs = $(this).attr('data-ftdid');
                var BFID = $(this).attr('data-bfid');
                var STMDID = $(this).attr('data-stmdid');
                var source_type = $(this).attr('data-source');

                selected_ids_array.push(IDs);
                selected_curr_array.push(currency);
                selected_bill_amnt_array.push(bill_amnt);
                selected_serial_array.push(serial);

                Swal.fire({
                    icon: 'success',
                    text: 'Bill selected!',
                    timer: 700,
                    showConfirmButton: false
                }).then(() => {
                    $('#serial-search-field').val('');
                    $('#serial-search-results').fadeOut(10);

                    appendSelectedBill(IDs, currency, bill_amnt, cmr_used, serial, BIDs, STMDID, source_type, BFID);
                });
            });
        }

        function appendSelectedBill(IDs, currency, bill_amnt, cmr_used, serial, BIDs, STMDID, source_type, BFID) {
            $('#selected-id').val(IDs);
            $('#currency').val(currency);
            $('#bill-amount').val(bill_amnt);
            $('#selling-rate').val(cmr_used);
            $('#serial').val(serial);
            $('#IDs').val(BIDs);
            $('#BFID').val(BFID);
            $('#STMDID').val(STMDID);
            $('#source-type').val(source_type);

            $('#currency-cell').text(currency);
            $('#bill-amount-cell').text(parseFloat(bill_amnt).toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            $('#serial-cell').text(serial).addClass('font-semibold');

            $("#tag-bill-modal").modal("show");
        }
    });

    $(document).on('click', '.bill_img_show', function() {
        var frontImage = $(this).attr('data-frontimage');
        var backImage = $(this).attr('data-backimage');

        $('.front-image').attr('src', frontImage);
        $('.back-image').attr('src', backImage);

        $('#modal-image').modal('show');

        // Remove any previous zoom instances to avoid duplication
        $('#image-zoom .zoom').trigger('zoom.destroy');

        // Initialize zoom on the image
        $('#image-zoom .zoom').zoom({ on: 'click' });
    });

    $(document).ready(function() {
        $('#download-images').click(function() {
            var front_img_src = $('.front-image').attr('src');
            var back_img_src = $('.back-image').attr('src');
            var currency = $('.currency').val();
            var serial = $('.serial').val();
            var transact_date = $('.transact-date').val();

            if (front_img_src) {
                downloadImages(front_img_src, 'FRONT' + '-' + currency + '-' + serial + '-' + transact_date + '.jpg');
            }
            if (back_img_src) {
                downloadImages(back_img_src, 'BACK' + '-' + currency + '-' + serial + '-' + transact_date + '.jpg');
            }
        });

        function downloadImages(url, filename) {
            var link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    });

    $(document).ready(function() {
        $('#tag-bills').click(function() {
            if ($('#tag-selection').val().length == 0) {
                Swal.fire({
                    icon: 'error',
                    text: 'Select a tag.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else {
                $('#tag-bill-modal').modal("hide");  
                $('#security-code-modal').modal("show");
            }
        });

        $('#halt-transaction').click(function() {
            $('#tag-bill-modal').modal("show");
        });

        $('#proceed-transaction').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#security-code').val();
            var parsed_tags = $('#tag-selection').val().join(", ");

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
                            text: 'Bill tagged!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            var form_data = new FormData($('#bill-tagging-form')[0]);
                            form_data.append('matched_user_id', matched_user_id);
                            form_data.append('parsed_tags', parsed_tags);

                            $.ajax({
                                url: "{{ route('admin_transactions.bill_tagging.save') }}",
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
                        });
                    }
                }
            });
        });
    });

    $(document).ready(function() {
        var FSID = '';
        var TBTID = '';

        $('.untag-missing-bill').click(function() {
            FSID = $(this).attr('data-fsid');
            TBTID = $(this).attr('data-tbtid');
        });

        $('input[name="radio-found-status"]').change(function() {
            $('#proceed-untag-missing-bills').removeAttr('disabled');

            if ($(this).val() == 2) {
                $('#found-at-row').fadeOut("fast");
            } else {
                $('#found-at-row').fadeIn("fast");
                $('input[name="radio-found-place"]').removeAttr('disabled', true);
            }
        });

        $('#proceed-untag-missing-bills').click(function() {
            $('#untag-missing-bill-modal').modal("hide");
            $('#untag-r-tranf-security-code-modal').modal("show");
        });

        $('#cancel-untag-missing-bill').click(function() {
            $('#untag-missing-bill-modal').modal("show");
        });

        $('#proceed-untag-missing-bill').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#untag-missing-bill-security-code').val();

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
                        $('#proceed-untag-missing-bill').prop('disabled', true);

                        var index = sec_code_array.indexOf(user_sec_onpage);
                        var matched_user_id = user_id_array[index];

                        Swal.fire({
                            title: 'Success!',
                            text: 'Bill untagged!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false,
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            var form_data = new FormData($('#missing-bill-status-form')[0]);
                            form_data.append('matched_user_id', matched_user_id);
                            form_data.append('FSID', FSID);
                            form_data.append('TBTID', TBTID);

                            $.ajax({
                                url: "{{ route('admin_transactions.bill_tagging.untag') }}",
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
                        });
                    }
                }
            });
        });
    });

    let DNO = '';
    let TBTID = '';
    let currency = '';
    let branch_id = '';
    let hr_user_id = '';
    let atd_amount = '';
    let bill_amount = '';
    let branch_code = '';
    let selling_rate = '';
    let transact_date = '';
    let pawnshop_user_id = '';

    $(document).ready(function() {
        var TBTID = 0;
        var employee_id = 0;

        $('#tagged-bill-date').flatpickr({
            mode: "range",
            maxDate: "today",
            allowInput: false,
            dateFormat: "Y-m-d",
        });

        $('.address-employee').click(function() {
            TBTID = $(this).attr('data-tbtid');
            currency =  $(this).attr('data-currency');
            branch_id =  $(this).attr('data-branch-id');
            branch_code =  $(this).attr('data-branch-code');
            bill_amount =  $(this).attr('data-bill-amount');
            tbx_branch_id =  $(this).attr('data-tbxbranchid');

            $.ajax({
                url: "{{ route('admin_transactions.bill_tagging.employees') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    tbx_branch_id: tbx_branch_id
                },success: function(data) {
                    var employees = data.appraisers;

                    employees.forEach(function(poy) {
                        list(poy.BranchID, poy.FullName, poy.UserID);
                    });
                }
            });

            function list(BranchID, FullName, UserID) {
                $('#appraisers-select').empty();
                
                var default_opt = $('<option>Select employee</option>');
                var options = $(`<option value="${UserID}" data-branchid="${BranchID}">${FullName}</option>`);

                $('#appraisers-select').append(default_opt);
                $('#appraisers-select').append(options);
            }
        });

        $('#add-employee').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#add-employee-security-code').val();
            var appraiser_id = $('#appraisers-select').val();
            // var atd_no = $('#atd-select').val();

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

                    if (appraiser_id == null) {
                        Swal.fire({
                            icon: 'error',
                            text: 'Fields are required.',
                            customClass: {
                                popup: 'my-swal-popup',
                            }
                        });
                    } else {
                        if (sec_code_array.includes(user_sec_onpage)) {
                            $('#proceed-transaction').prop('disabled', true);

                            var index = sec_code_array.indexOf(user_sec_onpage);
                            var matched_user_id = user_id_array[index];

                            Swal.fire({
                                title: 'Success!',
                                text: 'Bill tagged!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                $('#container-test').fadeIn("slow");
                                $('#container-test').css('display', 'block');

                                $.ajax({
                                    url: "{{ route('admin_transactions.bill_tagging.save_atd_emp') }}",
                                    type: "post",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        TBTID: TBTID,
                                        appraiser_id: appraiser_id,
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
                }
            });
        });

        $('.address-atd').click(function() {
            employee_id = $(this).attr('data-employeeid');
            TBTID = $(this).attr('data-tbtid');

            ATDNos(employee_id)
        });

        function ATDNos(employee_id) {
            $.ajax({
                url: "{{ route('admin_transactions.bill_tagging.select_atd_no') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    employee_id: employee_id
                },
                success: function(data) {
                    var ATD_numbers = data.ATD_numbers;

                    ATD_numbers.forEach(function(gar) {
                        appendATDNumbers(gar.DNO, gar.Employee);
                    });
                }
            });
        }

        function appendATDNumbers(DNO, Employee) {
            $('#atd-select').empty();
            var default_option = $('<option>Select ATD number</option>');
            var available_atd_numbers = $('<option value="'+ DNO +'" data-employee="'+ Employee +'">'+ DNO +'</option>');

            $('#atd-select').append(default_option);
            $('#atd-select').append(available_atd_numbers);
        }

        $('#add-atd').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#add-atd-security-code').val();
            var atd_no = $('#atd-select').val();

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

                    if (atd_no == null) {
                        Swal.fire({
                            icon: 'error',
                            text: 'Fields are required.',
                            customClass: {
                                popup: 'my-swal-popup',
                            }
                        });
                    } else {
                        if (sec_code_array.includes(user_sec_onpage)) {
                            $('#proceed-transaction').prop('disabled', true);

                            var index = sec_code_array.indexOf(user_sec_onpage);
                            var matched_user_id = user_id_array[index];

                            Swal.fire({
                                title: 'Success!',
                                text: 'Bill tagged!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                $('#container-test').fadeIn("slow");
                                $('#container-test').css('display', 'block');

                                $.ajax({
                                    url: "{{ route('admin_transactions.bill_tagging.save_atd_no') }}",
                                    type: "post",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        TBTID: TBTID,
                                        atd_no: atd_no,
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
                }
            });
        });

        // $('#print-atd').click(function() {
        //     var date_range = $('#tagged-bill-date').val();
        //     var dates = date_range.split(" TO ");
        //     var user_id_array = [];
        //     var sec_code_array = [];
        //     var user_sec_onpage = $('#print-atd-security-code').val();

        //     var date_from = dates[0];
        //     var date_to = dates[1];

        //     $.ajax({
        //         url: "{{ route('user_info') }}",
        //         type: "GET",
        //         data: {
        //             _token: "{{ csrf_token() }}",
        //         },
        //         success: function(get_user_info) {
        //             var user_info = get_user_info.security_codes;

        //             user_info.forEach(function(gar) {
        //                 sec_code_array.push(gar.SecurityCode);
        //                 user_id_array.push(gar.UserID);
        //             });

        //             if (sec_code_array.includes(user_sec_onpage)) {
        //                 $.ajax({
        //                     url: "{{ route('user_info') }}",
        //                     type: "GET",
        //                     data: {
        //                         _token: "{{ csrf_token() }}",
        //                     },
        //                     success: function() {

        //                     }
        //                 });
        //             } else {
        //                 Swal.fire({
        //                     icon: 'error',
        //                     text: 'Invalid or mismatched security code.',
        //                     customClass: {
        //                         popup: 'my-swal-popup',
        //                     }
        //                 });
        //             }
        //         }
        //     });
        // });
    });

    $(document).ready(function() {
        const socketserver = "{{ config('app.socket_io_server') }}";
        const socket = io(socketserver);

        $('.address-atds').click(function() {
            hr_user_id =  $(this).attr('data-hr-user-id');
            bill_amount =  $(this).attr('data-bill-amount');
            selling_rate =  $(this).attr('data-selling-rate');
            atd_amount =  $(this).attr('data-atd-amount');
            pawnshop_user_id =  $(this).attr('data-pawnshop-user-id');
            branch_code =  $(this).attr('data-branch-code');
            transact_date =  $(this).attr('data-transact-date');
            TBTID =  $(this).attr('data-a-tbtid');
            currency =  $(this).attr('data-currency');
            branch_id =  $(this).attr('data-branch-id');
        });

        $('#proceed-atd').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#address-atd-sec-code').val();

            $('#proceed-transaction').prop('disabled', true);

            function ATDPrompt($mgs, $branch_id, $atd_amount, $currency, $DNO) {
                socket.emit('ATDEPS', {msg: $mgs, branchid: $branch_id, atd_amount: $atd_amount, curr: $currency, disc_no: $DNO});
            }

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
                            text: 'ATD added!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            $.ajax({
                                url: "{{ route('admin_transactions.bill_tagging.eps_atd') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    hr_user_id: hr_user_id,
                                    currency: currency,
                                    bill_amount: bill_amount,
                                    selling_rate: selling_rate,
                                    atd_amount: atd_amount,
                                    pawnshop_user_id: matched_user_id,
                                    branch_code: branch_code,
                                    transact_date: transact_date,
                                    TBTID: TBTID,
                                },
                                success: function(data) {
                                    DNO = data;

                                    ATDPrompt(`An ATD has been addressed to an employee within this branch <b>(${branch_code})</b>.`, branch_id, atd_amount, currency, DNO);

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
</script>
