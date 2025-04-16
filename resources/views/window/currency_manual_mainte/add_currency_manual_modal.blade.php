
    <div class="modal-header ps-4">
        <span class="text-lg text-black font-bold">Add Currency Manual Details</span>
    </div>

    <div class="modal-body px-4 pb-2">
        <div class="row">
            <form class="form m-0" enctype="multipart/form-data" method="POST" id="add-new-currency-manual-form">
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
                            <select class="form-select" name="currency" id="currency" disabled>
                                <option value="">Select a currency</option>
                                @foreach ($result['currencies'] as $currencies)
                                    <option value="{{ $currencies->CurrencyID }}" @if (request()->segment(4) == $currencies->CurrencyID) selected @endif>{{ $currencies->Currency }}</option>
                                @endforeach
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
                            <select class="form-select" name="denomination" id="denomination">
                                <option value="">Select a denomination</option>
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
                                        <input type="radio" class="btn-check radio-button" name="radio-manual-type" id="radio-button-{{ $manual_tags->ManualTag }}" value="{{ $manual_tags->CMTID }}" disabled>
                                        <label class="btn btn-outline-primary" for="radio-button-{{ $manual_tags->ManualTag }}">
                                            <strong>{{ $manual_tags->ManualTag }}</strong>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center mt-3" id="stop-buying-container">
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
                                    <input type="radio" class="btn-check radio-button" name="radio-stop-buying" id="radio-button-stop-yes" value="1" disabled>
                                    <label class="btn btn-outline-primary" for="radio-button-stop-yes">
                                        <strong>Yes</strong>
                                    </label>

                                    <input type="radio" class="btn-check radio-button" name="radio-stop-buying" id="radio-button-stop-no" value="0" disabled>
                                    <label class="btn btn-outline-primary" for="radio-button-stop-no">
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
                            <input class="form-control" name="manual-image" id="manual-image" accept="images/jpeg, image/png, image/jpg" type="file" disabled>
                        </div>
                    </div>

                    <div class="row mt-3" id="remarks-container" style="display: none;">
                        <div class="col-4">
                            <label for="description">
                                <strong>
                                    Remarks: &nbsp;
                                </strong>
                            </label>
                        </div>
                        <div class="col-8">
                            <textarea class="form-control" id="manual-remarks" name="manual-remarks" rows="3"></textarea>
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
                        <input class="form-control" step="any" autocomplete="false" id="add-curr-manual-security-code" type="password">
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
        <button type="button" class="btn btn-primary btn-sm" id="currency-manual-add-button">Add</button>
    </div>

<script>
    $(document).ready(function() {
        $('#currency').trigger('change');
    });
</script>

{{-- <script>
    $(document).ready(function() {
        $('#add-new-currency-manual-form').validate({
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
        });
    });
</script> --}}
