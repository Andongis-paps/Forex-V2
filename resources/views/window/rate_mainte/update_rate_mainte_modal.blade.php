<!-- Modal -->
    <div class="modal-header ps-4">
        <span class="text-lg text-black font-bold">{{ trans('labels.w_rate_update') }}</span>
    </div>

    <input type="hidden" name="rate_id" value="{{ $rate_details[0]->CRID }}">

    <div class="modal-body px-4 py-2">
        <div class="row">
            <div class="col-lg-12">
                <form class="form m-0"  enctype="multipart/form-data" method="POST" id="update-rate-form">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-12">
                            <label class="text-sm mb-1" for="description">
                                <strong>
                                    {{ trans('labels.w_rate_rate_currency') }}: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
             
                            <input type="text" class="form-control" id="currency" value="{{ $rate_details[0]->Currency }}" step="any" required autocomplete="false" readonly>
                        </div>
                    </div>
                    <div class="row align-items-center mt-2">
                        <div class="col-12">
                            <label class="text-sm mb-1" for="description">
                                <strong>
                                    {{ trans('labels.w_rate_rate_country') }}: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
            
                            <select class="form-select" name="currency-mainte-country" id="currency-mainte-country">
                                <option value="">Select a country</option>
                                @foreach ($result['countries'] as $countries)
                                    <option value="{{ $countries->CountryID }}" @if ($countries->CountryID == $rate_details[0]->CountryID) selected @endif>{{ $countries->Country }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row align-items-center mt-2">
                        <div class="col-12">
                            <label class="text-sm mb-1" for="description">
                                <strong>
                                    {{ trans('labels.w_rate_curr_rate') }}: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
            
                            <input type="number" name="currency-rate-mainte" id="currency-rate-mainte" class="form-control" value="{{ number_format($rate_details[0]->Rate, 4, '.', ',') }}" step="any" required autocomplete="false">
                        </div>
                        <input type="hidden" id="old-rate" value="{{ number_format($rate_details[0]->Rate, 4, '.', ',') }}">
                        <input type="hidden" name="currency-id" id="currency-id" value="{{ $rate_details[0]->CurrencyID }}">
                    </div>
                </form>
            </div>

            <div class="col-lg-12">
                <hr class="my-2">
            </div>

            <div class="col-lg-12 py-1">
                <div class="row justify-content-center align-items-center">
                    <div class="col-12 text-center">
                        <label class="text-sm mb-1" for="description">
                            <strong>
                                {{ trans('labels.enter_security_code') }}:
                            </strong>
                        </label>

                        <input class="form-control" step="any" autocomplete="false" id="update-rate-security-code" type="password">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary btn-sm" id="update-rate-button">Save changes</button>
    </div>

<script>
    $(document).ready(function() {
        const socketserver = "{{ config('app.socket_io_server') }}";
        const socket = io(socketserver);
        
        $('#update-rate-button').click(function() {
            $('#update-rate-button').prop('disabled', true);
            var has_denom = "{{ $rate_details[0]->DenomStatus }}" == false;

            var old_rate = $('#old-rate').val();
            var currency = $('#currency').val();
            var currency_id = $('#currency-id').val();
            var new_rate = $('#currency-rate-mainte').val();
            var user_sec_onpage = $('#update-rate-security-code').val();

            function updateRatePrompt($mgs, $currency, $currency_id, $old_rate, $new_rate) {
                socket.emit('updateRate', {msg: $mgs, currency: currency, currency_id: currency_id, old_rate: old_rate, new_rate: new_rate});
            }

            if (sec_code_array.includes(user_sec_onpage)) {
                $('#update-rate-button').prop('disabled', true);

                var index = sec_code_array.indexOf(user_sec_onpage);
                var matched_user_id = user_id_array[index];

                var form_data = new FormData($('#update-rate-form')[0]);
                form_data.append('matched_user_id', matched_user_id);

                if (has_denom == true) {
                    // loaderOut();

                    Swal.fire({
                        icon: 'error',
                        text: "This currency has no denominations yet.",
                        customClass: {
                            popup: 'my-swal-popup',
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Rate Updated!',
                        icon: 'success',
                        timer: 900,
                        showConfirmButton: false
                    }).then(() => {
                        loader();

                        $.ajax({
                            url: "{{ route('maintenance.rate_maintenance.update') }}",
                            type: "post",
                            data: form_data,
                            contentType: false,
                            processData: false,
                            cache: false,
                            success: function(data) {
                                setTimeout(function() {
                                    updateRatePrompt(`The admin has updated the rate of the currency below.`, currency, currency_id, old_rate, new_rate);

                                    window.location.reload();
                                }, 500);
                            }
                        });
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    text: 'Invalid or mismatched security code.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                }).then(() => {
                    $('#update-rate-button').prop('disabled', false);
                });
            }
        });

        function loader() {
            $('#container-test').fadeIn("slow");
            $('#container-test').css('display', 'block');
        }

        function loaderOut() {
            $('#container-test').fadeOut("slow");
        }
    });
</script>
