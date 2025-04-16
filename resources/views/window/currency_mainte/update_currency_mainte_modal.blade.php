<!-- Modal -->
<div class="modal-header ps-4">
    <span class="text-lg text-black font-bold">{{ trans('labels.w_currency_update') }}</span>
</div>

<div class="modal-body px-4 py-2">
    <div class="row">
        <form class="form m-0" enctype="multipart/form-data" method="POST" id="update-currency-form">
            @csrf
            <div class="col-lg-12">
                <input type="hidden" name="currency_id" value="{{ $currency_details[0]->CurrencyID }}">

                <div class="row align-items-center">
                    <div class="col-12">
                        <label class="text-sm mb-1" for="description">
                            <strong>
                                {{ trans('labels.w_currency_name') }}: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </label>
             
                        <input type="text" name="currency_name" class="form-control" value="{{ $currency_details[0]->Currency }}" step="any" required>
                    </div>
                </div>

                <div class="row align-items-center mt-2">
                    <div class="col-12">
                        <label class="text-sm mb-1" for="description">
                            <strong>
                                {{ trans('labels.w_currency_origin') }}: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </label>
           
                        <select class="form-select" name="currency-country-origin" id="currency-country-origin">
                            <option value="">Select a country</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->CountryID }}" @if ($currency_details[0]->CountryID == $country->CountryID) selected @endif>{{ $country->Country }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row align-items-center mt-2">
                    <div class="col-12">
                        <label class="text-sm mb-1" for="description">
                            <strong>
                                {{ trans('labels.w_currency_sign') }}: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </label>
                    
                        <input type="text" name="currency_sign" class="form-control" value="{{ $currency_details[0]->CurrencySign }}" step="any" required>
                    </div>
                </div>

                <div class="row align-items-center mt-2">
                    <div class="col-12">
                        <label class="text-sm mb-1" for="description">
                            <strong>
                                {{ trans('labels.w_currency_abbrev') }}: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </label>
                    
                        <input type="text" name="currency_abbrev" class="form-control" value="{{ $currency_details[0]->CurrAbbv }}" step="any" required>
                    </div>
                </div>

                <div class="row align-items-center mt-2">
                    <div class="col-12">
                        <label class="text-sm mb-1" for="description">
                            <strong>
                                RIB Variance: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </label>
           
                        <input type="number" name="rib_variance" class="form-control" step="any" required value="{{ $currency_details[0]->RIBVariance }}" autocomplete="false" id="rib_variance">
                    </div>
                </div>

                {{-- <div class="row align-items-center mt-2">
                    <div class="col-12">
                        <label class="text-sm mb-1" for="description">
                            <strong>
                                {{ trans('labels.w_currency_percentage') }}: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </label>
                    
                        <input type="text" name="currency_percent" class="fo-rmcontrol" value="{{ $currency_details[0]->CoinsPercentage }}00" step="any" required>
                    </div>
                </div> --}}

                <div class="row align-items-center mt-2">
                    <div class="col-12">
                        <label class="text-sm mb-1" for="description">
                            <strong>
                                {{ trans('labels.w_currency_serial') }}: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </label>
                    
                        <select class="form-select" name="currency_serial_stat" id="currency_serial_stat">
                            <option value="1" @if($currency_details[0]->WithSerials == '1') selected @endif>{{ trans('labels.w_serial_yes') }}</option>
                            <option value="0" @if($currency_details[0]->WithSerials == '0') selected @endif>{{ trans('labels.w_serial_no') }}</option>
                        </select>
                    </div>
                </div>

                <div class="row align-items-center mt-2">
                    <div class="col-12">
                        <label class="text-sm mb-1" for="description">
                            <strong>
                                Receipt Set: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </label>
                    </div>
                    <div class="col-6">
                        <label class="switch switch-success switch-square">
                            <input type="checkbox" class="switch-input switch-r-set-o" @if ($currency_details[0]->WithSetO == 1) checked @else  @endif name="switch-r-set-o">
                            <span class="switch-toggle-slider">
                                <span class="switch-on"></span>
                                <span class="switch-off"></span>
                            </span>
                            <span class="switch-label cursor-pointer">
                                <strong>
                                    O
                                </strong>
                            </span>
                        </label>
                    </div>
                    <div class="col-6">
                        <label class="switch switch-success switch-square">
                            <input type="checkbox" class="switch-input switch-r-set-b" @if ($currency_details[0]->WithSetB == 1) checked @else  @endif name="switch-r-set-b">
                            <span class="switch-toggle-slider">
                                <span class="switch-on"></span>
                                <span class="switch-off"></span>
                            </span>
                            <span class="switch-label cursor-pointer">
                                <strong>
                                    B
                                </strong>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="col-lg-12">
                    <hr class="my-2">
                </div>
            </div>
        </form>

        <div class="col-lg-12 my-1">
            <div class="row justify-content-center align-items-center">
                <div class="col-12 text-center">
                    <label class="text-sm mb-1" for="description">
                        <strong>
                            {{ trans('labels.enter_security_code') }}:
                        </strong>
                    </label>

                    <input class="form-control" step="any" autocomplete="false" id="update-curr-security-code" type="password">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary btn-sm" id="update-expense-button">Save changes</button>
</div>

<script>
   $(document).ready(function() {
        $('#update-currency-form').validate({
            rules: {
                currency_name: {
                    required: true,
                    pattern: /^[a-zA-z]\s*/,
                },
                currency_sign: {
                    required: true,
                    pattern: /(?:\p{Sc})?[a-zA-Z]\s*/,
                },
                currency_abbrev: 'required',
                currency_precent: {
                    required: true,
                    pattern: /^[^a-zA-Z0-9.]*$/,
                },
                currency_serial_stat: 'required',
            },
            messages: {
                currency_name: {
                    required: 'Enter a currency name.',
                    pattern: 'Invalid currency name format.'
                },
                currency_sign: {
                    required: 'Enter a currency sign.',
                    pattern: 'Invalid currency symbol. (Example: $, ¥, ₱)',
                },
                currency_abbrev: 'Enter a currency abbreviation.',
                currency_precent: {
                    required: true,
                    pattern: 'Enter a decimal value.',
                },
                currency_serial_stat: 'Enter a serial status.',
            },
            // submitHandler: function(form) {
            //     form.submit();
            // }
        });

        $('#update-expense-button').click(function(){
            var user_sec_onpage = $('#update-curr-security-code').val();
            var with_set_o = $('input[name="switch-r-set-o"]').is(':checked') ? 1 : 0;
            var with_set_b = $('input[name="switch-r-set-b"]').is(':checked') ? 1 : 0;

            if (sec_code_array.includes(user_sec_onpage)) {
                $(this).prop('disabled', true);

                var index = sec_code_array.indexOf(user_sec_onpage);
                var matched_user_id = user_id_array[index];

                Swal.fire({
                    title: 'Success!',
                    text: 'Currency updated!',
                    icon: 'success',
                    timer: 1000,
                    showConfirmButton: false
                }).then(() => {
                    var form_data = new FormData($('#update-currency-form')[0]);
                    form_data.append('matched_user_id', matched_user_id);
                    form_data.append('currency_id', $('input[name="currency_id"]').val());
                    form_data.append('currency_name', $('input[name="currency_name"]').val());
                    form_data.append('currency_sign', $('input[name="currency_sign"]').val());
                    form_data.append('rib_variance', $('input[name="rib_variance"]').val());
                    form_data.append('currency_abbrev', $('input[name="currency_abbrev"]').val());
                    form_data.append('currency_serial_stat', $('#currency_serial_stat').val());
                    form_data.append('for_set_o', with_set_o);
                    form_data.append('for_set_b', with_set_b);

                    setTimeout(() => {
                        $('#container-test').fadeIn("slow");
                        $('#container-test').css('display', 'block');

                        $.ajax({
                            url: "{{ route('maintenance.currency_maintenance.update') }}",
                            type: "post",
                            data: form_data,
                            contentType: false,
                            processData: false,
                            cache: false,
                            success: function(data) {
                                window.location.reload();
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
                });
            }
        });
    });
</script>
