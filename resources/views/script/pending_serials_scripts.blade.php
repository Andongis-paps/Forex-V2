{{-- Redirect - After successful adding of serials --}}
<script>
    $(document).ready(function() {
        $('.serials-input').on('input', function() {
            this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');

            if (this.value.length > 12) {
                this.value = this.value.slice(0, 12);
            }

            if (/^([a-zA-Z0-9]*)$/.test(this.value) && this.value.length >= 6) {
                $(this).css('border', '');
            }
        });

        var saving_success_serial = $('#success-message-saving-pending').attr('data-successpending');

        function refreshPage(delay) {
            setTimeout(function() {
                var pending_serial_url = $('#forex-serial-url').attr('data-forexserials');
                window.location.href = pending_serial_url;
            }, delay);
        }

        $('#submit-peding-serials').click(function() {
            var input = $('[name^="serials"]');
            var serials = [];
            var serials_fsids = [];

            $('.serials-input').each(function() {
                $(this).css('border', '');
            });

            var input_fields_val = input.map(function() {
                return $(this).val();
            }).get();

            $('.serials-input').each(function() {
                if ($(this).val() != '') {
                    serials.push($(this).val());
                }
            });

            function duplicateSerials(input_fields_val) {
                var serial_array_values = {};

                for (var i = 0; i < input_fields_val.length; i++) {
                    var index_value = input_fields_val[i];

                    if (serial_array_values.hasOwnProperty(index_value) && index_value !== '') {
                        return true;
                    }

                    serial_array_values[index_value] = true;
                }
                return false;
            }

            var array_data = input_fields_val.some(function(field_vlues) {
                return field_vlues !== '';
            });

            if (duplicateSerials(input_fields_val) == true) {
                Swal.fire({
                    icon: 'error',
                    text: 'Duplicate serials are not allowed.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });

                var seen = {};

                $('.serials-input').each(function() {
                    var value = $(this).val();

                    if (value) {
                        if (seen[value]) {
                            seen[value].push($(this));
                        } else {
                            seen[value] = [$(this)];
                        }
                    }
                });

                $.each(seen, function(key, fields) {
                    if (fields.length > 1) {
                        fields.forEach(function(field) {
                            field.css('border', '2px solid red');
                        });
                    }
                });
            } else {
                var empty_field = false;
                var serial_validity = true;
                var duplicate_db = true;
                var serial_min_length = [];

                $('.serials-input').each(function() {
                    var serial_fields = $(this).val() === '';

                    if (serial_fields) {
                        empty_field = true;
                        $(this).css('border', '1px solid #D9DEE3');
                    } else {
                        serials_fsids.push($(this).attr('data-fsid'));
                    }

                    var pattern_validity = /^([a-zA-Z0-9]*)$/.test($(this).val());

                    if (!pattern_validity) {
                        serial_validity = false;
                        $(this).css('border', '2px solid red');
                        $(this).focus();
                    }

                    if ($(this).val().length < 6 && $(this).val().length != 0) {
                        serial_min_length.push($(this).val().length);
                        $(this).css('border', '2px solid red');
                        $(this).focus();
                    }
                });

                switch (array_data) {
                    case true:
                        if (serial_validity == false) {
                            Swal.fire({
                                icon: 'error',
                                text: 'Incorrect serial format.',
                                customClass: {
                                    popup: 'my-swal-popup',
                                }
                            });

                            $('#add-pending-serials-modal').modal("hide");
                        } else if (serial_min_length.length > 0) {
                            Swal.fire({
                                icon: 'error',
                                text: 'Serial number must be at least 6 characters long.',
                                customClass: {
                                    popup: 'my-swal-popup',
                                }
                            });
                        } else {
                            var parsed_fsid = serials_fsids.join(',');
                            var parsed_serials = serials.join(',');

                            // serialSubmission(parsed_fsid, parsed_serials);

                            $.ajax({
                                url: "{{ route('branch_transactions.pending_serials.dupe_serials') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    FSIDs: parsed_fsid,
                                    serials: parsed_serials
                                },
                                success: function (data) {
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
                                            serialSubmission(parsed_fsid, parsed_serials);
                                        }
                                    });
                                }
                            });

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
                                }).then(() => {
                                    $('.serials-input').each(function() {
                                        dupe_serials.forEach((value, index) => {
                                            if (value == $(this).val()) {
                                                $(this).css('border', '2px solid red');
                                                $(this).focus();
                                            }
                                        });
                                    });
                                });
                            }
                        }
                        break;
                    case false:
                        Swal.fire({
                            icon: 'error',
                            text: 'Serials are required.',
                            customClass: {
                                popup: 'my-swal-popup',
                            }
                        });

                        break;

                    default:
                        console.log("Nope.");
                }
            }

            function serialSubmission(parsed_fsid, parsed_serials) {
                $('#security-code-modal').modal("show");

                var user_id_array = [];
                var sec_code_array = [];

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

                        $('#proceed-transaction').click(function() {
                            $(this).prop('disabled', true);

                            var user_sec_onpage = $('#security-code').val();

                            if (sec_code_array.includes(user_sec_onpage)) {
                                $(this).prop('disabled', true);

                                var index = sec_code_array.indexOf(user_sec_onpage);
                                var matched_user_id = user_id_array[index];

                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Denominations Updated!',
                                    icon: 'success',
                                    timer: 900,
                                    showConfirmButton: false
                                }).then(() => {
                                    $('#container-test').fadeIn("slow");
                                    $('#container-test').css('display', 'block');

                                    var form_data = new FormData($('#pending-serials-form')[0]);
                                    form_data.append('matched_user_id', matched_user_id);
                                    form_data.append('parsed_fsid', parsed_fsid);
                                    form_data.append('parsed_serials', parsed_serials);

                                    $.ajax({
                                        url: "{{ route('branch_transactions.buying_transaction.add_serials') }}",
                                        type: "post",
                                        data: form_data,
                                        contentType: false,
                                        processData: false,
                                        cache: false,
                                        success: function(data) {
                                            var route = "{{ route('branch_transactions.buying_transaction') }}";

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
                                }).then(() => {
                                    $(this).prop('disabled', false);
                                });
                            }
                        });
                    }
                });
            }
        });
    });

    $(document).ready(function() {
        $('.bxs-toggle-right').hide();

        $('.enable-serial-field').change(function() {
            var checkbox = $(this);
            var checked_icon = checkbox.siblings('.bx-toggle-left');
            var unchecked_icon = checkbox.siblings('.bxs-toggle-right');

            if ($(this).is(':checked')) {
                checked_icon.hide();
                unchecked_icon.show();
                $(this).closest('.input-group-serials').find('.serials-input').removeAttr('disabled', 'disabled');
            } else {
                checked_icon.show();
                unchecked_icon.hide();
                $(this).closest('.input-group-serials').find('.serials-input').attr('disabled', true);
            }
        });
    });
</script>
