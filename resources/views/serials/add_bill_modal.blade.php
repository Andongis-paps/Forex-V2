<!-- Modal -->
<form class="form m-0" action="{{ URL::to('/addBillSerial') }}" enctype="multipart/form-data" method="POST" id="add-new-serial-form">
    <div class="modal-header ps-4">
        <h4 class="modal-title" id="exampleModalLabel">{{ trans('labels.serials_serial_add') }}</h4>
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
                                {{ trans('labels.serials_serial_amount') }}: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </label>
                    </div>
                    <div class="col-8">
                        <select class="form-control" name="serial-bill-amount" id="serial-bill-amount">
                            <option value="Select a bill amount">Select a bill amount</option>
                            @foreach ($result['available_bills'] as $available_bills)
                                <option value="{{ number_format($available_bills->BillAmount, 2, '.', ',') }}" data-serialftdid="{{ $available_bills->FTDID }}" data-serialfsid="{{ $available_bills->FSID }}">{{ Str::title($available_bills->Currency) }}, &nbsp;{{ number_format($available_bills->BillAmount, 2, '.', ',') }}, &nbsp;  <span><strong>Serial:</strong></span>&nbsp;{{ $available_bills->Serials }}, &nbsp; <span><strong>Receipt Set:</strong></span>&nbsp;{{ $available_bills->Rset }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" id="serial-fsid" name="serial-fsid" value="">
                    <input type="hidden" id="serial-ftdid" name="serial-ftdid" value="">
                    <input type="hidden" name="forex-scid-serial" value="{{ $sold_serials->SCID }}">
                    <input type="hidden" name="serial-rate-used" value="{{ $soldcurr_deets->RateUsed }}">
                </div>

                <div class="row align-items-center mt-3">
                    <div class="col-4">
                        <label for="description">
                            <strong>
                                {{ trans('labels.serials_serial_total') }}: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </label>
                    </div>
                    <div class="col-8">
                        <input type="hidden" name="get-soldtransact-current-amount" value="{{ number_format($soldcurr_deets->CurrAmount, 2) }}">
                        <input type="text" id="serial-total-amount" name="serial-total-amount" class="form-control" step="any" value="" readonly>
                        <input type="hidden" id="true-serial-total-amount" name="true-serial-total-amount" class="form-control" value="" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="serial-add-button">Add</button>
    </div>
</form>

{{-- <script>
    $(document).ready(function() {
        $('#add-new-serial-form').validate({
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
</script> --}}

{{-- @include('script.scripts') --}}
