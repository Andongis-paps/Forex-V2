
    <div class="modal-header ps-4">
        <span class="text-lg text-black font-bold">{{ trans('labels.w_currency_add') }}</span>
    </div>

    <div class="modal-body px-4 py-2">
        <div class="row">
            <form class="form m-0" enctype="multipart/form-data" method="POST" id="add-new-currency-form">
                @csrf
                <div class="col-lg-12">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <label class="text-sm mb-1" for="description">
                                <strong>
                                    {{ trans('labels.w_currency_name') }}: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
             
                            <input type="text" name="currency-name" class="form-control" step="any" required autocomplete="false" id="currency-name">
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
                                @foreach ($result['counrties'] as $counrties)
                                    <option value="{{ $counrties->CountryID }}">{{ $counrties->Country }}</option>
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
               
                            <input type="text" name="currency-sign" class="form-control" step="any" required autocomplete="false" id="currency-sign">
                        </div>
                    </div>

                    <div class="row align-items-center mt-2">
                        <div class="col-12">
                            <label class="text-sm mb-1" for="description">
                                <strong>
                                    {{ trans('labels.w_currency_abbrev') }}: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
               
                            <input type="text" name="currency-abbrev" class="form-control" step="any" required autocomplete="false" id="currency-abbrev">
                        </div>
                    </div>

                    <div class="row align-items-center mt-2">
                        <div class="col-12">
                            <label class="text-sm mb-1" for="description">
                                <strong>
                                    RIB Variance: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
               
                            <input type="number" name="rib-variance" class="form-control" step="any" required autocomplete="false" id="rib-variance">
                        </div>
                    </div>

                    <div class="row align-items-center mt-2">
                        <div class="col-12">
                            <label class="text-sm mb-1" for="description">
                                <strong>
                                    {{ trans('labels.w_currency_serial') }}: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
               
                            <select class="form-select select-serial" name="currency-serial-stat" id="select-serial">
                                <option value="1">With Serial</option>
                                <option value="0">Without Serial</option>
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
                                <input type="checkbox" class="switch-input switch-r-set-o" name="switch-r-set-o">
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
                                <input type="checkbox" class="switch-input switch-r-set-b" name="switch-r-set-b">
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
                </div>

                <div class="col-lg-12">
                    <hr class="my-2">
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
          
                        <input class="form-control" step="any" autocomplete="false" id="add-curr-security-code" type="password">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        @can('add-permission', $menu_id)
            <button type="button" class="btn btn-primary btn-sm" id="currency-add-button">Add</button>
        @endcan
    </div>

<script>
    $(document).ready(function() {
        $('.switch-r-set-o, .switch-r-set-b').change(function() {
            if ($(this).is(':checked')) {
                $(this).attr('checked', 'checked');
        
            } else {
                $(this).removeAttr('checked');
            }
        });

        $('#add-new-currency-form').validate({
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
    });
</script>
