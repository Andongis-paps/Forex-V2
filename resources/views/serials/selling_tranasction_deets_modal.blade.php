<div class="modal-header py-2 ps-3">
    <span class="text-lg font-bold">
        <i class='bx bx-detail me-2'></i>{{ trans('labels.serials_transact_details') }}
    </span>
</div>

<div class="modal-body px-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="row align-items-center mb-3">
                <div class="col-4">
                    <strong>
                        {{ trans('labels.sold_curr_date_sold') }}:
                    </strong>
                </div>
                <div class="col-8">
                    <span id="selling-trans-date"></span>
                </div>
            </div>

            <div class="row align-items-center my-3">
                <div class="col-4">
                    <strong>
                        Transaction Number:
                    </strong>
                </div>
                <div class="col-8">
                    <span id="selling-number"></span>
                </div>
            </div>

            <div class="row align-items-center my-3">
                <div class="col-4">
                    <strong>
                        Invoice Number:
                    </strong>
                </div>
                <div class="col-8">
                    <span id="selling-or-number"></span>
                </div>
            </div>

            <div class="row align-items-center my-3">
                <div class="col-4">
                    <strong>
                        {{ trans('labels.sold_curr_customer') }}:
                    </strong>
                </div>
                <div class="col-8">
                    <span id="selling-customer"></span>
                </div>
            </div>

            {{-- <div class="row align-items-center my-3">
                <div class="col-4">
                    <strong>
                        {{ trans('labels.sold_curr_receipt_no') }}:
                    </strong>
                </div>
                <div class="col-8">
                    <span id="selling-receipt-number"></span>
                </div>
            </div> --}}

            {{-- <div class="row align-items-center my-3 @if($soldcurr_deets->Rset == 'B') d-none @endif">
                <div class="col-4">
                    <strong>
                        {{ trans('labels.sold_curr_rset') }}:
                    </strong>
                </div>
                <div class="col-8">
                    <span id="selling-rset"></span>
                </div>
            </div> --}}

            <div class="row align-items-center my-3">
                <div class="col-4">
                    <strong>
                        {{ trans('labels.sold_curr_currency') }}:
                    </strong>
                </div>
                <div class="col-8">
                    <span id="selling-currency"></span>
                </div>
            </div>

            <div class="row align-items-center my-3">
                <div class="col-4">
                    <strong>
                        {{ trans('labels.sold_curr_rate_used') }}:
                    </strong>
                </div>
                <div class="col-8">
                    <span id="selling-rate-used"></span>
                </div>
            </div>

            <div class="row align-items-center my-3">
                <div class="col-4">
                    <strong>
                        {{ trans('labels.sold_curr_curr_amnt') }}:
                    </strong>
                </div>
                <div class="col-8">
                    <strong>
                        <span class="text-xl">{{ $soldcurr_deets->CurrAbbv }}</span>&nbsp;<span class="text-xl" id="sell-transact-currency-amount"></span>
                    </strong>
                </div>
            </div>

            <div class="row align-items-center mt-3">
                <div class="col-4">
                    <strong>
                        {{ trans('labels.sold_curr_amnt') }}:
                    </strong>
                </div>
                <div class="col-8">
                    <strong>
                        <span class="text-xl">PHP</span>&nbsp;<span class="text-xl" id="sell-transact-amount"></span>
                    </strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <div class="row align-items-center">
        <div class="col-6 text-end pe-0">
            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
        </div>
        <div class="col-6 text-end pe-0">
            <button class="btn btn-primary" type="button" id="print-test-selling" data-bs-toggle="modal" data-bs-target="#security-code-modal">Print</button>
        </div>
    </div>
</div>




