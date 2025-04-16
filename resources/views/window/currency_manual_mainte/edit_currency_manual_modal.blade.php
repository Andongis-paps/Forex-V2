
    <div class="modal-header ps-4">
        <span class="text-lg text-black font-bold">Edit Currency Manual Details</span>
    </div>

    <div class="modal-body px-4 pb-2">
        <div class="row">
            <form class="form m-0" enctype="multipart/form-data" method="POST" id="update-currency-manual-form">
                @csrf
                <div class="col-lg-12">
                    <div class="row align-items-center">
                        <div class="col-4">
                            <label for="description">
                                <strong>
                                    Currency: <span class="required-class">*</span>
                                </strong>
                            </label>
                        </div>
                        <div class="col-8">
                            <select class="form-select" name="currency-update" id="currency-update" disabled>
                                <option value="{{ $result['currency_manual'][0]->CurrencyID }}" >{{ $result['currency_manual'][0]->Currency }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="row align-items-center mt-3">
                        <div class="col-4">
                            <label for="description">
                                <strong>
                                    Denomination: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
                        </div>
                        <div class="col-8">
                            <select class="form-select" name="denomination-update" id="denomination-update">
                                {{-- <option value="{{ $result['currency_manual'][0]->DenominationID }}" >{{ $result['currency_manual'][0]->BillAmount }}</option> --}}
                            </select>
                        </div>
                    </div>

                    <div class="row align-items-center mt-3">
                        <div class="col-4">
                            <label for="description">
                                <strong>
                                    Manual Type: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
                        </div>
                        <div class="col-8">
                            <div class="row text-center">
                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                    @foreach ($result['manual_tags'] as $manual_tags)
                                        <input type="radio" class="btn-check radio-button" name="radio-manual-type-update" id="radio-button-{{ $manual_tags->CMTID }}" value="{{ $manual_tags->CMTID }}" @if ($result['currency_manual'][0]->CMTID == $manual_tags->CMTID) checked @endif>
                                        <label class="btn btn-outline-primary" for="radio-button-{{ $manual_tags->CMTID }}">
                                            <strong>{{ $manual_tags->ManualTag }}</strong>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center mt-3" id="stop-buying-containe-update">
                        <div class="col-4">
                            <label for="description">
                                <strong>
                                    Stop Buying: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
                        </div>
                        <div class="col-8">
                            <div class="row text-center">
                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                    <input type="radio" class="btn-check radio-button" name="radio-stop-buying-update" id="radio-button-stop-yes-{{ $result['currency_manual'][0]->CMID }}" value="1" @if ($result['currency_manual'][0]->StopBuying == 1) checked @endif>
                                    <label class="btn btn-outline-primary" for="radio-button-stop-yes-{{ $result['currency_manual'][0]->CMID }}">
                                        <strong>Yes</strong>
                                    </label>

                                    <input type="radio" class="btn-check radio-button" name="radio-stop-buying-update" id="radio-button-stop-no-{{ $result['currency_manual'][0]->CMID }}" value="0" @if ($result['currency_manual'][0]->StopBuying == 0) checked @endif>
                                    <label class="btn btn-outline-primary" for="radio-button-stop-no-{{ $result['currency_manual'][0]->CMID }}">
                                        <strong>No</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center mt-3">
                        <div class="col-4">
                            <label for="description">
                                <strong>
                                    Upload Image: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
                        </div>
                        <div class="col-8">
                            <input class="form-control" name="manual-image-update" id="manual-image-update" accept="images/jpeg, image/png, image/jpg" type="file" >
                        </div>
                    </div>

                    <div class="row mt-3" id="remarks-container-update" style="display: none;">
                        <div class="col-4">
                            <label for="description">
                                <strong>
                                    Remarks: &nbsp;
                                </strong>
                            </label>
                        </div>
                        <div class="col-8">
                            <textarea class="form-control" id="manual-remarks-update" name="manual-remarks-update" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 px-3 my-2">
                    <hr>
                </div>
            </form>

            <div class="col-lg-12 px-3">
                <div class="row justify-content-center align-items-center">
                    <div class="col-5">
                        <input class="form-control" step="any" autocomplete="false" id="update-curr-manual-security-code" type="password">
                    </div>

                    <div class="col-12 text-center mt-2">
                        <label for="description">
                            <strong>
                                {{ trans('labels.enter_security_code') }} &nbsp; <span class="required-class">*</span>
                            </strong>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        @can('edit-permission', $menu_id)
            <button type="button" class="btn btn-primary btn-sm" id="currency-manual-update-button">Update</button>
        @endcan
    </div>

<script>
    $(document).ready(function() {
        $.ajax({
            url: "{{ route('maintenance.currency_manual.get_denominations') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                currency_id: "{{ $result['currency_manual'][0]->CurrencyID }}"
            },
            success: function(data) {
                // clearAll();
                var denominations = data.denominations;

                console.log(denominations);

                $('#denomination-update').empty();
                $('#denomination-update').removeAttr('disabled', 'disabled');

                var default_option = $(`<option value="Select a currency" id="buying-default-currency">Select a denomination</option>`);
                $('#denomination-update').append(default_option);

                denominations.forEach(function(data) {
                    denoms(data.DenominationID, data.BillAmount);
                });
            }
        });

        function denoms(DenominationID, BillAmount) {
            var selected_denom_id = "{{ $result['currency_manual'][0]->DenominationID }}";

            var is_selected = (DenominationID == selected_denom_id) ? 'selected' : '';

            var option_element = $(`<option value="${DenominationID}" data-billamount="${BillAmount}" name="selected-currency" ${is_selected}>${BillAmount}</option>`);
            $('#denomination-update').append(option_element);
        }

        $('#denomination-update').change(function() {
            // clearAll();
            $('input[name="radio-manual-type"]').prop('disabled', false);
            $('#denomination-update').trigger('change');
        });

        $('input[name="radio-manual-type-update"]').change(function() {
            $('#manual-image').prop('disabled', false);
            $('input[name="radio-stop-buying-update"]').prop('disabled', false);

            var manual_type = $('input[name="radio-manual-type-update"]:checked').val();

            if (manual_type == 3) {
                $('#stop-buying-container-update').hide();
                $('#remarks-container-update').show().fadeIn("fast");
                $('input[name="radio-stop-buying-update"]').prop('checked', false);
            } else {
                $('#stop-buying-container-update').show();
                $('#remarks-container-update').hide().fadeOut("fast");
                $('input[name="radio-stop-buying-update"]').prop('checked', false);
            }
        });

        $('input[name="radio-stop-buying-update"]').change(function() {
            $('#manual-image').prop('disabled', false);
        });

        function clearAll() {
            $('#manual-image').prop('disabled', true);
            $('input[name="radio-manual-type-update"]').prop('checked', false);
            $('input[name="radio-stop-buying-update"]').prop('checked', false);
            $('input[name="radio-manual-type-update"]').prop('disabled', true);
            $('input[name="radio-stop-buying-update"]').prop('disabled', true);
        }
    });

    $(document).ready(function() {
        $('#currency-manual-update-button').click(function() {
            var currency = $('#currency-update').val();
            var denomination = $('#denomination-update').val();
            var bill_amount = $('#denomination-update option:selected').data('billamount');
            var manual_type = $('input[name="radio-manual-type-update"]:checked').val();
            var stop_buying = $('input[name="radio-stop-buying-update"]:checked').val();
            var manual_image = $('#manual-image-update').val();
            var manual_remarks = $('#manual-remarks-update').val();
            var user_sec_onpage = $('#update-curr-manual-security-code').val();

            if (currency == '' || denomination == '' || manual_type == '') {
                Swal.fire({
                    text: 'All fields are required.',
                    icon: 'warning',
                    showConfirmButton: true
                });
            } else {
                if (sec_code_array.includes(user_sec_onpage)) {
                    $('#update-curr-manual-security-code').prop('disabled', true);

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

                        var form_data = new FormData($('#update-currency-manual-form')[0]);
                        form_data.append('matched_user_id', matched_user_id);
                        form_data.append('currency', currency);
                        form_data.append('denomination', denomination);
                        form_data.append('bill_amount', parseFloat(bill_amount));
                        form_data.append('manual_type', manual_type);
                        form_data.append('stop_buying', stop_buying == undefined ? 'null' : stop_buying);
                        form_data.append('manual_image', manual_image);
                        form_data.append('manual_remarks', manual_remarks);
                        form_data.append('CMID', "{{ $result['currency_manual'][0]->CMID }}")

                        $.ajax({
                            url: "{{ route('maintenance.currency_manual.update') }}",
                            type: "post",
                            data: form_data,
                            contentType: false,
                            processData: false,
                            cache: false,
                            success: function(data) {
                                var route = "{{ route('maintenance.currency_manual.view', ['id' => ':id']) }}";
                                var url = route.replace(':id', "{{ $result['currency_manual'][0]->CurrencyID }}");

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
        });
    });
</script>
