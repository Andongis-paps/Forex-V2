<!-- Modal -->
<form class="form m-0" action="{{ route('add_new_rate') }}" enctype="multipart/form-data" method="POST" id="add-new-rate-form">
    <div class="modal-header ps-4">
        <h4 class="modal-title" id="exampleModalLabel">{{ trans('labels.w_rate_add') }}</h4>
        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
    </div>

    @csrf

    <div class="modal-body px-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="row align-items-center">
                    <div class="col-4">
                        <label for="description">
                            <strong>
                                {{ trans('labels.w_rate_rate_currency') }}: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </label>
                    </div>
                    <div class="col-8">
                        <select class="form-select" name="currency-name" id="currency-name">
                            <option value="">Select a currency</option>
                            @foreach ($result['currencies'] as $currencies)
                                <option value="{{ $currencies->CurrencyID }}" data-countryid="{{ $currencies->CountryID }}">{{ Str::title($currencies->Currency) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row align-items-center mt-3">
                    <div class="col-4">
                        <label for="description">
                            <strong>
                                {{ trans('labels.w_currency_origin') }}: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </label>
                    </div>
                    <div class="col-8">
                        <select class="form-select" id="currency-country-origin" disabled>
                            <option value="">Select a country</option>
                            {{-- @foreach ($result['countries'] as $countries)
                                <option value="{{ $countries->CountryID }}">{{ $countries->Country }}</option>
                            @endforeach --}}
                        </select>
                        <input type="hidden" value="" name="currency-country-origin" id="currency-country-origin-true">
                    </div>
                </div>
                <div class="row align-items-center mt-3">
                    <div class="col-4">
                        <label for="description">
                            <strong>
                                {{ trans('labels.w_rate_curr_rate') }}: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </label>
                    </div>
                    <div class="col-8">
                        <input type="text" name="currency-rate" id="currency-rate" class="form-control" placeholder="0.0000">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="branch-add-button">Add</button>
    </div>
</form>

<script>
    $(document).ready(function() {
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
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script>

{{-- @include('script.scripts') --}}
