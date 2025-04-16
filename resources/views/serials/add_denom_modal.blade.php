<!-- Modal -->
<form class="form m-0" action="{{ URL::to('/addDenom') }}" enctype="multipart/form-data" method="POST" id="add-new-denom-form">
    <div class="modal-header ps-4">
        <h4 class="modal-title" id="exampleModalLabel">{{ trans('labels.serials_denom_add') }}</h4>
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
                                {{ trans('labels.serials_denom_amount') }}: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </label>
                    </div>
                    <div class="col-8">
                        <select class="form-control" name="denom-bill-amount" id="denom-bill-amount">
                            <option value="">Select a bill amount</option>
                            @foreach ($result['bill_amount'] as $bill_amount)
                                <option value="{{ $bill_amount->BillAmount }}" data-currencyid="{{ $bill_amount->CurrencyID }}">{{ $bill_amount->BillAmount }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="forex-ftdid-denom" value="{{ $forex_serials->FTDID }}">
                </div>
                <div class="row align-items-center mt-3">
                    <div class="col-4">
                        <label for="description">
                            <strong>
                                {{ trans('labels.serials_denom_multiplier') }}: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </label>
                    </div>
                    <div class="col-8">
                        <input type="text" id="denom-multiplier" name="denom-multiplier" class="form-control" step="any" required autocomplete="false">
                    </div>
                </div>

                <div class="row align-items-center mt-3">
                    <div class="col-4">
                        <label for="description">
                            <strong>
                                {{ trans('labels.serials_denom_total') }}: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </label>
                    </div>
                    <div class="col-8">
                        <input type="hidden" name="get-transact-current-amount" value="{{ number_format($trans_deets->CurrencyAmount, 2) }}">
                        <input type="text" id="denom-total-amount" name="denom-total-amount" class="form-control" step="any" value="" readonly>
                        <input type="hidden" id="true-denom-total-amount" name="true-denom-total-amount" class="form-control" value="" readonly>
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
