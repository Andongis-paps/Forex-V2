<div class="modal fade" id="add-buffer-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content add-denom">
            <div class="modal-header ps-4 py-2">
                <span class="text-lg">
                    <strong>
                        Add Buffer
                    </strong>
                </span>
            </div>

            <div class="modal-body px-3 py-2">
                <div class="col-12">
                    <form class="m-0" method="post" id="add-buffer-form">
                        @csrf

                        <div class="col-12">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <label class="mb-2" for="buffer-amount">
                                        <span class="text-black text-sm font-bold">
                                            Entry Date:
                                        </span>
                                    </label>

                                    <input class="form-control text-left" type="text" value="{{ now()->toDateString() }}" readonly>
                                </div>
                            </div>
                            <div class="row align-items-center mt-2">
                                <div class="col-12">
                                    <label class="mb-2" for="currency">
                                        <span class="text-black text-sm font-bold">
                                            Currency:
                                        </span>
                                    </label>

                                    <select class="form-select" id="currency" name="currency">
                                        <option value="default">Select a currency</option>
                                        @foreach ($result['currency'] as $currency)
                                            <option value="{{ $currency->CurrencyID }}" @if ($currency->CurrencyID == 11) selected @endif>{{ $currency->Currency }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row align-items-center mt-2">
                                <div class="col-12">
                                    <label class="mb-2" for="buffer-amount">
                                        <span class="text-black text-sm font-bold">
                                            Buffer Amount:
                                        </span>
                                    </label>

                                    <input class="form-control text-right" type="number" id="buffer-amount" name="buffer-amount" placeholder="0.00">
                                </div>
                            </div>
                            <div class="row align-items-center mt-2">
                                <div class="col-12">
                                    <label class="mb-2" for="buffer-amount">
                                        <span class="text-black text-sm font-bold">
                                            Principal Amount (PHP):
                                        </span>
                                    </label>

                                    <input class="form-control text-right" type="number" id="principal-amount" name="principal-amount" placeholder="0.00">
                                </div>
                            </div>
                            <div class="row align-items-center mt-2">
                                <div class="col-12">
                                    <label class="mb-2" for="remarks">
                                        <span class="text-black text-sm font-bold">
                                            Remarks:
                                        </span>
                                    </label>

                                    <textarea class="form-control" id="remarks" name="remarks" rows="3" disabled></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal-footer">
                <div class="row align-items-center">
                    <div class="col-6 text-end pe-0">
                        <button class="btn btn-secondary" type="button" id="halt-add-buffer" data-bs-dismiss="modal">Cancel</button>
                    </div>
                    <div class="col-6 text-end pe-2">
                        <button class="btn btn-primary" type="button" id="proceed-financing" disabled>Proceed</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#currency').change(function() {
            var boolean = $(this).val() != 'default' == true;

            $('#remarks').attr('disabled', 'disabled');
            $('#proceed-financing').attr('disabled', 'disabled');
            $('#buffer-amount').val('').attr('placeholder', "0.00");

            if (boolean == true) {
                $('#buffer-amount').removeAttr('disabled', 'disabled');
            } else {
                $('#buffer-amount').attr('disabled', 'disabled');
            }
        });

        $('#buffer-amount').change(function() {
            if (parseFloat($(this).val()) >= 0) {
                $('#principal-amount').removeAttr('disabled', 'disabled');
            } else {
                $('#principal-amount').attr('disabled', 'disabled');
            }
        });

        $('#principal-amount').change(function() {
            if (parseFloat($(this).val()) >= 0) {
                $('#remarks').removeAttr('disabled', 'disabled');
            } else {
                $('#remarks').attr('disabled', 'disabled');
            }
        });

        $('#remarks').change(function() {
            if ($(this).val() != '') {
                $('#proceed-financing').removeAttr('disabled', 'disabled');
            } else {
                $('#proceed-financing').attr('disabled', 'disabled');
            }
        });
    });
</script>
