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
        $('#currency').change(function() {
            $.ajax({
                url: "{{ route('maintenance.currency_manual.get_denominations') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    currency_id: $(this).val()
                },
                success: function(data) {
                    clearAll();
                    var denominations = data.denominations;

                    $('#denomination').empty();
                    $('#denomination').removeAttr('disabled', 'disabled');

                    var default_option = $(`<option value="Select a currency" id="buying-default-currency">Select a denomination</option>`);
                    $('#denomination').append(default_option);

                    denominations.forEach(function(data) {
                        denoms(data.DenominationID, data.BillAmount);
                    });
                }
            });
        });

        function denoms(DenominationID, BillAmount) {
            var option_element = $(`<option value="${DenominationID}" data-billamount="${BillAmount}" name="selected-currency">${BillAmount}</option>`);
            $('#denomination').append(option_element);
        }

        $('#denomination').change(function() {
            clearAll();
            $('input[name="radio-manual-type"]').prop('disabled', false);

            $('#denomination').trigger('change');
        });

        $('input[name="radio-manual-type"]').change(function() {
            $('#manual-image').prop('disabled', false);
            $('input[name="radio-stop-buying"]').prop('disabled', false);

            var manual_type = $('input[name="radio-manual-type"]:checked').val();

            if (manual_type == 3) {
                $('#stop-buying-container').hide();
                $('#remarks-container').show().fadeIn("fast");
                $('input[name="radio-stop-buying"]').prop('checked', false);
            } else {
                $('#stop-buying-container').show();
                $('#remarks-container').hide().fadeOut("fast");
                $('input[name="radio-stop-buying"]').prop('checked', false);
            }
        });

        $('input[name="radio-stop-buying"]').change(function() {
            $('#manual-image').prop('disabled', false);
        });

        function clearAll() {
            $('#manual-image').prop('disabled', true);
            $('input[name="radio-manual-type"]').prop('checked', false);
            $('input[name="radio-stop-buying"]').prop('checked', false);
            $('input[name="radio-manual-type"]').prop('disabled', true);
            $('input[name="radio-stop-buying"]').prop('disabled', true);
        }

        if ($('#currency-manual-add-modal').hasClass('show')) {
            $('#currency').trigger('change');
        } else {
            $('#currency').trigger('change');
        }
    });

    $(document).ready(function() {
        $('#currency-manual-add-button').click(function() {
            var currency = $('#currency').val();
            var denomination = $('#denomination').val();
            var bill_amount = $('#denomination option:selected').data('billamount');
            var manual_type = $('input[name="radio-manual-type"]:checked').val();
            var stop_buying = $('input[name="radio-stop-buying"]:checked').val();
            var manual_image = $('#manual-image').val();
            var manual_remarks = $('#manual-remarks').val();
            var user_sec_onpage = $('#add-curr-manual-security-code').val();


            if (currency == '' || denomination == '' || manual_type == '' || manual_image == '') {
                Swal.fire({
                    text: 'All fields are required.',
                    icon: 'warning',
                    showConfirmButton: true
                });
            } else {
                $.ajax({
                    url: "{{ route('maintenance.currency_manual.existing') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        currency: currency,
                        bill_amount: bill_amount,
                        manual_type: manual_type,
                    },
                    success: function(data) {
                        if (data.boolean) {
                            Swal.fire({
                                text: 'The entry already exists.',
                                icon: 'warning',
                                showConfirmButton: true
                            });
                        } else {
                            if (sec_code_array.includes(user_sec_onpage)) {
                                $('#add-curr-manual-security-code').prop('disabled', true);

                                var index = sec_code_array.indexOf(user_sec_onpage);
                                var matched_user_id = user_id_array[index];

                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Currency manual added!',
                                    icon: 'success',
                                    timer: 900,
                                    showConfirmButton: false
                                }).then(() => {
                                    $('#container-test').fadeIn("slow");
                                    $('#container-test').css('display', 'block');

                                    var form_data = new FormData($('#add-new-currency-manual-form')[0]);
                                    form_data.append('matched_user_id', matched_user_id);
                                    form_data.append('currency', currency);
                                    form_data.append('denomination', denomination);
                                    form_data.append('bill_amount', parseFloat(bill_amount));
                                    form_data.append('manual_type', manual_type);
                                    form_data.append('stop_buying', stop_buying == undefined ? 'null' : stop_buying);
                                    form_data.append('manual_image', manual_image);
                                    form_data.append('manual_remarks', manual_remarks);

                                    $.ajax({
                                        url: "{{ route('maintenance.currency_manual.add') }}",
                                        type: "post",
                                        data: form_data,
                                        contentType: false,
                                        processData: false,
                                        cache: false,
                                        success: function(data) {
                                            var url = "{{ route('maintenance.currency_manual') }}";

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
                                });
                            }
                        }
                    }
                });
            }
        });
    });

    $(document).on('click', '.bill_img_show', function() {
        var bill_image = $(this).attr('data-billimage');

        $('.bill-image').attr('src', bill_image);

        $('#modal-image').modal('show');

        $('#image-zoom .zoom').trigger('zoom.destroy');

        $('#image-zoom .zoom').zoom({ on: 'click' });
    });

    $(document).ready(function() {
        $('#download-images').click(function() {
            var front_img_src = $('.bill-image').attr('src');
            var curr_abbv = $('.curr-abbv').val();
            var bill_amnt = $('.bill-amount').val();
            var manual_tag = $('.manual-tag').val();

            if (front_img_src) {
                downloadImages(front_img_src, 'BILL-IMAGE' + '-' + curr_abbv + '-' + bill_amnt + '-' + manual_tag + '.jpg');
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
        $('.button-edit-curr-manual').click(function() {
            $.ajax({
                url: "{{ route('maintenance.currency_manual.edit') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    cmid: $(this).attr('data-cmid')
                },
                success: function(data) {
                    $('.edit-currency-manual').html(data);
                }
            });
        });
    });

     // Delete currency - Window Based - Currency
     $(document).ready(function(){
        $('.button-delete-curr-manual').on('click', function() {
            var CMID = $(this).attr('data-cmid');

            deleteCurrManual(CMID);
        });

        function deleteCurrManual(CMID) {
            $('#proceed-delete').click(function() {
                var user_sec_onpage = $('#del-curr-manual-security-code').val();

                if (sec_code_array.includes(user_sec_onpage)) {
                    $('#proceed-delete').prop('disabled', true);

                    var index = sec_code_array.indexOf(user_sec_onpage);
                    var matched_user_id = user_id_array[index];

                    Swal.fire({
                        title: 'Success!',
                        text: 'Manual detail successfully deleted!',
                        icon: 'success',
                        timer: 900,
                        showConfirmButton: false
                    }).then(() => {
                        $('#container-test').fadeIn("slow");
                        $('#container-test').css('display', 'block');

                        setTimeout(function() {
                            $.ajax({
                                type: 'POST',
                                url: "{{ route('maintenance.currency_manual.delete') }}",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    CMID: CMID
                                },
                                success: function(response) {
                                    var url = "{{ route('maintenance.currency_manual') }}";

                                    window.location.href = url;
                                }
                            });
                        }, 1000);
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
    });
</script>
