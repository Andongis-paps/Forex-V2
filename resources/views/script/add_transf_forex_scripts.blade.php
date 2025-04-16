<script>
    // Transfer Forex - Validate Pending Serials
    $(document).ready(function() {
        $('#validate-pending-serials-button').click(function() {
            $.ajax({
                url: "{{ route('branch_transactions.transfer_forex.validation') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#container-test').fadeIn("slow");
                    $('#container-test').css('display', 'block');

                    setTimeout(function() {
                        $('#container-test').fadeOut("slow");

                        var pending_serials = data.pending_serials;

                        if (pending_serials.length === 0) {
                            Swal.fire({
                                title: 'No Pending Serials!',
                                text: "You can now proceed with Transfer Forex .",
                                icon: 'success',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                timer: 1000,
                            }).then(() => {
                                setTimeout(function() {
                                    $('#add-transfer-button').removeClass('d-none');
                                }, 200);
                            });

                        } else {
                            Swal.fire({
                                html: `Pending serials detected. Please complete them before proceeding.`,
                                icon: 'warning',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                confirmButtonText: 'Confirm',
                            }).then((result) => {
                                clearData();

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
                                        var url = "{{ route('branch_transactions.pending_serials') }}";

                                        window.location.href = url;
                                    }, 500);
                                });
                            });
                        }
                    }, 2000);
                }
            })
        });

        // Clear input fields for currency
        function clearData() {
            $('#pending-serials-table tbody').empty();
        }
    });

    $(document).ready(function() {
        $('.report-error-modal-btn').click(function() {
            $('#ID').val($(this).attr('data-transactid'));
            $('#menu-id').val($(this).attr('data-menuid'));

            $.ajax({
                url: "{{ route('report_error') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    menu_id: $(this).attr('data-menuid'),
                },
                success: function (data) {
                    clear();

                    var ticket_concern = data.css_concerns;

                    ticket_concern.forEach(function(gar) {
                        appendConcerns(gar.SConcernID, gar.SConcern);
                    });
                }
            });
        });

        function appendConcerns(SConcernID, SConcern) {
            var options = $(`<option value="${SConcernID}">${SConcern}</option>`);

            $('#sconcernid').append(options);
        }

        function clear() {
            $('#sconcernid').empty();
            $('#sconcernid').append(`<option value="">Select Concern</option>`);
        }
    });

    //Acknowledge buffer details
    $(document).ready(function() {
        var buffer_type = '';
        var transfer_forex_id = '';

        $('.acknowledge-buffer-transf').click(function(){
            transfer_forex_id = $(this).attr('data-transferforexid');

            $.ajax({
                url: "{{ route('branch_transactions.transfer_forex.buffer_details') }}",
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    transfer_forex_id: transfer_forex_id
                },
                success: function(data) {
                    var buffer_amount = 0;

                    $('#buffer-transfer-details').modal("show");
                    $('.buffer-transfer-details-breakdown').html(data);

                    // acknowledgeBuffer(transfer_forex_id);
                }
            });
        });

        $('#halt-acknowledment').click(function() {
            $('#buffer-transfer-details').modal("show");
            $('#ack-buff-security-code-modal').modal("hide");
        });

        // function acknowledgeBuffer(transfer_forex_id) {
        $('#proceed-acknowledgement').click(function() {
            var FSIDs = '';
            var selected_ids_array = [];

            $('.select-one-buffer').each(function() {
                if ($(this).prop('checked')) {
                    if (!selected_ids_array.includes($(this).attr('data-fsid'))) {
                        selected_ids_array.push($(this).attr('data-fsid'));
                    }
                }
            });

            $('#proceed-acknowledgement').prop('disabled', true);

            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#ack-security-code').val();

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
                        $('#proceed-acknowledgement').prop('disabled', true);

                        var index = sec_code_array.indexOf(user_sec_onpage);
                        var matched_user_id = user_id_array[index];

                        Swal.fire({
                            title: 'Success!',
                            text: 'Buffer Acknowledged!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            console.log($('#total-buffer-amount-text').val());

                            $.ajax({
                                url: "{{ route('branch_transactions.transfer_forex.acknowledge') }}",
                                type: "post",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    matched_user_id: matched_user_id,
                                    FSIDs: selected_ids_array.join(","),
                                    transfer_forex_id: transfer_forex_id,
                                    buffer_type: $('#buffer-type').val(),
                                    total_buffer_amount: $('#total-buffer-amount-text').val(),
                                },
                                success: function(response) {
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
                            $('#proceed-acknowledgement').prop('disabled', false);
                        });
                    }
                }
            });
        });
        // }
    });

    // Delete Transfer Forex
    $(document).ready(function() {
        $('.button-delete-transfer').on('click',function() {
            var transfer_forex_id = $(this).attr('data-transferforexid');

            $('#security-code-modal').modal("show");
            deleteTransfer(transfer_forex_id);
        });

        function deleteTransfer(transfer_forex_id) {
            $('#proceed-transaction').click(function() {
                var user_id_array = [];
                var sec_code_array = [];
                var user_sec_onpage = $('#security-code').val();

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
                                text: 'Transfer Forex Deleted!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                $('#container-test').fadeIn("slow");
                                $('#container-test').css('display', 'block');

                                $.ajax({
                                    type: 'POST',
                                    url: "{{ route('branch_transactions.transfer_forex.delete') }}",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        transfer_forex_id: transfer_forex_id
                                    },
                                    success: function(response) {
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
        }
    });

    $(document).ready(function() {
        $('.tracking-no-span').each(function() {
            $(this).click(function() {
                $(this).closest('tr').find('.custom-popper-tracking').toggleClass('d-none').fadeIn("fast");
            });
        });

        $('.custom-popper-tracking').on('mouseleave', function() {
            $(this).closest('tr').find('.custom-popper-tracking').toggleClass('d-none').fadeOut("fast");
        });

        var transf_fx_id = '';

        $('.remove-tracking-button').click(function() {
            transf_fx_id = $(this).attr('data-transferforexid');
        });

        $('#proceed-remove-tracking').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#remove-tracking-security-code').val();

            $('#proceed-remove-tracking').prop('disabled', true);

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
                        $('#proceed-remove-tracking').prop('disabled', true);

                        var index = sec_code_array.indexOf(user_sec_onpage);
                        var matched_user_id = user_id_array[index];

                        Swal.fire({
                            title: 'Success!',
                            text: 'Tracking number removed!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            $.ajax({
                                type: 'POST',
                                url: "{{ route('branch_transactions.transfer_forex.remove_tracking_no') }}",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    transf_fx_id: transf_fx_id
                                },
                                success: function(response) {
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
                            $('#proceed-remove-tracking').prop('disabled', false);
                        });
                    }
                }
            });
        });

        $('#select-all-transfers').click(function() {
            var value = $(this).prop('checked');

            if (value == true) {
                $('.select-transfers').each(function() {
                    if (!$(this).prop('disabled')) {
                        $(this).prop('checked', value);
                    }
                });
            } else {
                $('.select-transfers').each(function() {
                    $(this).prop('checked', false);
                });
            }
        });

        $('#tracking-number').change(function() {
            $('#address-tracking-no').removeAttr('disabled', 'disabled');
            $('#address-tracking-no-security-code').removeAttr('disabled', 'disabled');
        });

        $('#address-tracking-no').click(function() {
            var transf_types = [];
            var transfer_forex_id_array = [];

            $('#address-tracking-no').prop('disabled', true);

            $('.select-transfers').each(function() {
                var TFIDs = $(this).prop('checked') == true;

                if (TFIDs) {
                    transf_types.push($(this).attr('data-types'));
                    transfer_forex_id_array.push($(this).attr('data-tfid'));
                }
            });

            var types = $('#tracking-number').find('option:selected').data('trackingtype').split(", ");

            function arraysMatch(types, transf_types) {
                if (types.length !== transf_types.length) {
                    return false;
                }

                types.sort();
                transf_types.sort();

                for (let i = 0; i < types.length; i++) {
                    if (types[i] !== transf_types[i]) {
                        return false;
                    }
                }

                return true;
            }

            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#address-tracking-no-security-code').val();

            if (!arraysMatch(types, transf_types)) {
                Swal.fire({
                    icon: 'error',
                    html: `<span class="text-sm text-black">Transfers doesn't match the items in the selected tracking number.</span>`,
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                }).then(() => {
                    $('#address-tracking-no').prop('disabled', false);
                });
            } else {
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
                                text: 'Tracking number added!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                $('#container-test').fadeIn("slow");
                                $('#container-test').css('display', 'block');

                                $.ajax({
                                    type: 'POST',
                                    url: "{{ route('branch_transactions.transfer_forex.add_tracking_no') }}",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        parse_tfids: transfer_forex_id_array.join(", "),
                                        tracking_id: $('#tracking-number').val(),
                                        tracking_no: $('#tracking-number').find('option:selected').data('trackingno'),
                                    },
                                    success: function(response) {
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
                                $('#address-tracking-no').prop('disabled', false);
                            });
                        }
                    }
                });
            }
        });
    });
</script>

