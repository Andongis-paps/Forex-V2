{{-- Integrated Denomination Maintenance scripts - Window Based - Currency --}}
<script>
    var user_id_array = [];
    var sec_code_array = [];

    $(document).ready(function() {
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
            }
        });
    });

    $(document).ready(function() {
        var counter = 0;

        $('#button-add-denom').click(function() {
            $.ajax({
                url: "{{ route('maintenance.currency_maintenance.trans_type') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    var TTID = [];
                    var TransType = [];
                    var transact_type = data.transact_type;

                    transact_type.forEach(function(gar) {
                        TTID.push(gar.TTID);
                        TransType.push(gar.TransType);
                    });

                    var parsed_ttids = TTID.join(", ");
                    var parsed_trans_types = TransType.join(", ");

                    addDenom(parsed_ttids, parsed_trans_types)
                }
            });
        });

        function addDenom(parsed_ttids, parsed_trans_types) {
            var ttid_array = parsed_ttids.split(", ");
            var trans_type_array = parsed_trans_types.split(", ");

            var test = `<select class="form-select transact-type" name="transact-type[]" id="transact-type"><option value="0">Select transaction type</option>`;

            var merged_trans_details = ttid_array.map(function(ttid, ttid_index) {
                return {
                    TTID: ttid,
                    TransType: trans_type_array[ttid_index],
                };
            });

            merged_trans_details.forEach(function(gar) {
                test += `<option value="${gar.TTID}">${gar.TransType}</option>`;
            });

            test += `</select>`;

            var table = $('#denomination-table');
            var row = $(`<tr class="new-curr-denom-container">`);
            var denom_input = $('<td class="text-center text-sm p-1"><input class="form-control denominations-input text-right" name="denominations[]" type="number" placeholder="0.00" value="0" step="any"></td>');
            var transact_type = $(`<td class="text-center text-sm p-1">${test}</td>`);
            var fillers = $(`<td class="text-center text-sm p-1"></td>`);
            var delete_row = $(`<td class="text-center text-sm p-1"><a class="btn btn-primary button-delete button-delete-trans-details delete-appended-new-curr-denom"><i class='menu-icon tf-icons bx bx-trash text-white'></i></a></td>`);

            if (counter <= counter++) {
                row.append(denom_input);
                row.append(transact_type);
                row.append(fillers);
                row.append(delete_row);

                table.find('tbody').append(row);

                $('#empty-banner').fadeOut(100);
                $('#update-denominations-button').removeAttr('disabled', 'disabled');
            }
        }

        $('body').on('click', '.delete-appended-new-curr-denom', function deleteNewCurrDenom() {
            if (counter-- == 1) {
                $('#empty-banner').fadeIn(100);
                $('#save-denominations').attr('disabled', 'disabled');
                $('#update-denominations-button').attr('disabled', 'disabled');
                $('#save-denominations-new-curr').attr('disabled', 'disabled');
            }

            $(this).parents(".new-curr-denom-container").remove();
        });

        function denominationValidation() {
            var transact_type_id = $('#transact-type').val();
            var currency_id = $('#currency').val();

            switch (currency_id) {
                case "11":
                    switch (transact_type_id) {
                        case "1":
                            $('.denominations-input').change(function() {
                                if ($(this).val() > 100) {
                                    Swal.fire({
                                        text: "Denomination cannot be higher.",
                                        icon: 'error',
                                        showConfirmButton: true,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                    }).then(() => {
                                        $(this).val('').attr('placeholder', '0.00');
                                    });

                                    $('#save-denominations').attr('disabled', 'disabled');
                                } else if ($(this).val() < 1) {
                                    Swal.fire({
                                        text: "Denomination cannot be lower.",
                                        icon: 'error',
                                        showConfirmButton: true,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                    }).then(() => {
                                        $(this).val('').attr('placeholder', '0.00');
                                    });

                                    $('#save-denominations').attr('disabled', 'disabled');
                                } else {
                                    $('#save-denominations').removeAttr('disabled');
                                }
                            });
                        break;

                        case "2":
                            $('.denominations-input').change(function() {
                                if ($(this).val() > 20) {
                                    Swal.fire({
                                        text: "Denomination cannot be higher.",
                                        icon: 'error',
                                        showConfirmButton: true,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                    }).then(() => {
                                        $(this).val('').attr('placeholder', '0.00');
                                    });

                                    $('#save-denominations').attr('disabled', 'disabled');
                                } else if ($(this).val() < 1) {
                                    Swal.fire({
                                        text: "Denomination cannot be lower.",
                                        icon: 'error',
                                        showConfirmButton: true,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                    }).then(() => {
                                        $(this).val('').attr('placeholder', '0.00');
                                    });

                                    $('#save-denominations').attr('disabled', 'disabled');
                                } else {
                                    $('#save-denominations').removeAttr('disabled');
                                }
                            });
                        break;

                        case "3":
                            $('.denominations-input').change(function() {
                                if ($(this).val() > 1) {
                                    Swal.fire({
                                        text: "Denomination cannot be higher.",
                                        icon: 'error',
                                        showConfirmButton: true,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                    }).then(() => {
                                        $(this).val('').attr('placeholder', '0.00');
                                    });

                                    $('#save-denominations').attr('disabled', 'disabled');
                                } else if ($(this).val() < .05) {
                                    Swal.fire({
                                        text: "Denomination cannot be lower.",
                                        icon: 'error',
                                        showConfirmButton: true,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                    }).then(() => {
                                        $(this).val('').attr('placeholder', '0.00');
                                    });

                                    $('#save-denominations').attr('disabled', 'disabled');
                                } else {
                                    $('#save-denominations').removeAttr('disabled');
                                }
                            });
                        break;

                        case "4":
                            Swal.fire({
                                text: "No available denom for DPOFX",
                                icon: 'error',
                                showConfirmButton: true,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                            }).then(() => {
                                $(this).val('').attr('placeholder', '0.00');
                            });
                        break;
                    }
                break;

                case "13":
                    switch (transact_type_id) {
                        case "1":
                            $('.denominations-input').keyup(function() {
                                if ($(this).val() < 5) {
                                    Swal.fire({
                                        text: "Denomination cannot be lower.",
                                        icon: 'error',
                                        showConfirmButton: true,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                    }).then(() => {
                                        $(this).val('').attr('placeholder', '0.00');
                                    });
                                }
                            });

                            break;
                        case "3":
                            $('.denominations-input').keyup(function() {
                                if ($(this).val() > 2) {
                                    Swal.fire({
                                        text: "Denomination cannot be higher.",
                                        icon: 'error',
                                        showConfirmButton: true,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                    }).then(() => {
                                        $(this).val('').attr('placeholder', '0.00');
                                    });
                                }
                            });

                            break;

                    }
                break;
                    default:
                        console.log("break it down yow");
            }
        }
    });

    // Update denoms - existing curr
    $(document).ready(function() {
        $('#update-denominations-button').click(function() {
            var empty_fields_denom = false;
            var empty_fields_transact_type = false;

            var denom_array = [];
            var transact_type_array = [];

            var table = $('#denomination-table');

            table.find('.new-curr-denom-container').each(function() {
                var denom_field = parseFloat($(this).closest('tr').find('.denominations-input').val());
                var transact_type = parseInt($(this).closest('tr').find('.transact-type').val());

                if (denom_field == 0) {
                    empty_fields_denom = true;
                }

                if (transact_type == 0) {
                    empty_fields_transact_type = true;
                }

                denom_array.push(denom_field);
                transact_type_array.push(transact_type);
            });

            var valid = true;

            for (var i = 0; i < denom_array.length; i++) {
                for (var j = i + 1; j < denom_array.length; j++) {
                    if (transact_type_array[i] === transact_type_array[j] && denom_array[i] === denom_array[j]) {
                        valid = false;
                        break;
                    }
                }
                if (!valid) {
                    break;
                }
            }

            // if (valid) {
            //     alert("Denominations are valid. Proceeding with insertion.");
            // }

            if (empty_fields_denom) {
                Swal.fire({
                    icon: 'error',
                    text: 'Denomination is required.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else if (empty_fields_transact_type) {
                Swal.fire({
                    icon: 'error',
                    text: 'Transaction type is required.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else if (!valid) {
                Swal.fire({
                    icon: 'error',
                    text: 'Duplicate entries detected.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            } else {
                $('#security-code-modal').modal('show');
            }
        });
    });

    // Save / update curr
    $(document).ready(function() {
        $('#proceed-transaction').click(function() {
            var user_sec_onpage = $('#security-code').val();

            $('#proceed-transaction').prop('disabled', true);

            if (sec_code_array.includes(user_sec_onpage)) {
                $('#proceed-transaction').prop('disabled', true);

                var index = sec_code_array.indexOf(user_sec_onpage);
                var matched_user_id = user_id_array[index];

                Swal.fire({
                    title: 'Success!',
                    text: 'Denominations added!',
                    icon: 'success',
                    timer: 900,
                    showConfirmButton: false
                }).then(() => {
                    $('#container-test').fadeIn("slow");
                    $('#container-test').css('display', 'block');

                    var form_data = new FormData($('#update-denomination-form')[0]);
                    form_data.append('matched_user_id', matched_user_id);

                    setTimeout(() => {
                        $.ajax({
                            url: "{{ route('maintenance.currency_maintenance.update_denom') }}",
                            type: "post",
                            data: form_data,
                            contentType: false,
                            processData: false,
                            cache: false,
                            success: function(data) {
                                var route = "{{ route('maintenance.currency_maintenance') }}";

                                window.location.href = route;
                            }
                        });
                    }, 400);
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
        });

        // $.ajax({
        //     url: "{{ route('user_info') }}",
        //     type: "GET",
        //     data: {
        //         _token: "{{ csrf_token() }}",
        //     },
        //     success: function(data) {
        //         var user_security_code = data.user_info.user_security_code;

        //         $('#update-denominations-button-sec-code').click(function() {
        //             var user_sec_onpage = $('#security-code').val();

        //             if (user_sec_onpage == user_security_code) {
        //                 Swal.fire({
        //                     title: 'Success!',
        //                     text: 'Denominations Updated!',
        //                     icon: 'success',
        //                     timer: 900,
        //                     showConfirmButton: false
        //                 }).then(() => {
        //                     setTimeout(function() {
        //                         $('#update-denomination-form').submit();
        //                     }, 200);
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
        //         });
        //     }
        // });
    });

    $(document).ready(function() {
        $('.enable-disable-denom').click(function() {
            $('.switch-label').empty();
            $('#update-curr-denom-modal').modal('show');

            var trans_type = $(this).attr('data-type');
            var denom_status = $(this).attr('data-status');
            var denom_id = $(this).attr('data-denominationid');

            $('#trans-type').val(trans_type).trigger('change');
            $('#denomination').val($(this).attr('data-denom'));

            var check_box = $('input[name="switch-input"]');

            if (denom_status == 1) {
                check_box.prop('checked', true);

                var label = $(`<strong>Active</strong>`);
                $('.switch-label').append(label);
            } else {
                check_box.prop('checked', false);

                var label = $(`<strong>Inactive</strong>`);
                $('.switch-label').append(label);
            }

            updateDenom(denom_id);
        });
    });

    function updateDenom(denom_id) {
        $('#proceed-update').click(function() {
            var user_sec_onpage = $('#update-curr-denom-sec-code').val();

            if (sec_code_array.includes(user_sec_onpage)) {
                $('#proceed-update').prop('disabled', true);

                var index = sec_code_array.indexOf(user_sec_onpage);
                var matched_user_id = user_id_array[index];

                Swal.fire({
                    title: 'Success!',
                    text: 'Denomination updated!',
                    icon: 'success',
                    timer: 900,
                    showConfirmButton: false
                }).then(() => {
                    $('#container-test').fadeIn("slow");
                    $('#container-test').css('display', 'block');

                    var form_data = new FormData($('#update-denom')[0]);
                    form_data.append('denom_id', denom_id);
                    form_data.append('matched_user_id', matched_user_id);
                    form_data.append('status', $('.switch-input').is(':checked') ? 1 : 0);

                    $.ajax({
                        url: "{{ route('maintenance.currency_maintenance.update_one_denom') }}",
                        type: "post",
                        data: form_data,
                        contentType: false,
                        processData: false,
                        cache: false,
                        success: function(data) {
                            var route = "{{ route('maintenance.currency_maintenance') }}";

                            window.location.href = route;
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
                }).then(()=> {
                    $('#proceed-update').prop('disabled', false);
                });
            }
        });
    }

    $(document).ready(function() {
        $('.delete-denomination').click(function() {
            $('#del-denom-security-code-modal').modal('show');
            var denom_id = $(this).attr('data-denominationid');

            deleteDenom(denom_id);
        });
    });

    function deleteDenom(denom_id) {
        $('#proceed-delete').click(function() {
            var user_sec_onpage = $('#del-denom-security-code').val();

            if (sec_code_array.includes(user_sec_onpage)) {
                $('#proceed-delete').prop('disabled', true);

                var index = sec_code_array.indexOf(user_sec_onpage);
                var matched_user_id = user_id_array[index];

                Swal.fire({
                    title: 'Success!',
                    text: 'Denomination deleted!',
                    icon: 'success',
                    timer: 900,
                    showConfirmButton: false
                }).then(() => {
                    $('#container-test').fadeIn("slow");
                    $('#container-test').css('display', 'block');

                    setTimeout(() => {
                        $.ajax({
                            url: "{{ route('maintenance.currency_maintenance.delete_denom') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                denomination_id: denom_id,
                            },
                            success: function() {
                                var route = "{{ route('maintenance.currency_maintenance') }}";

                                window.location.href = route;
                            }
                        });
                    }, 500);
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
        });
    }

    // Password
    $(document).ready(function() {
        $('#security-code').prop('type', 'text').on('focus', function() {
            $(this).prop('type', 'password');
        }).on('blur', function() {
            $(this).prop('type', 'password');
        });

    });
</script>
