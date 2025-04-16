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
                        {{ trans('labels.transact_date') }}: 
                    </strong>
                </div>
                <div class="col-8">
                    <span id="transact-date"></span>
                </div>
            </div>
            <div class="row align-items-center my-3">
                <div class="col-4">
                    <strong>
                        Transaction Number:
                    </strong>
                </div>
                <div class="col-8">
                    <span id="transact-number"></span>
                </div>
            </div>
            <div class="row align-items-center my-3">
                <div class="col-4">
                    <strong>
                        {{ trans('labels.transact_invoice_#') }}: 
                    </strong>
                </div>
                <div class="col-8">
                    <span id="transact-receipt-number"></span>
                </div>
            </div>
            {{-- <div class="row align-items-center my-3 @if ($trans_deets->Rset == 'B') d-none @endif">
                <div class="col-4">
                    <strong>
                        {{ trans('labels.transact_rset') }}: 
                    </strong>
                </div>
                <div class="col-8">
                    <span id="transact-rset"></span>
                </div>
            </div> --}}
            {{-- <div class="row align-items-center my-3 @if ($trans_deets->Rset == 'B') d-none @endif">
                <div class="col-4">
                    <strong>
                        {{ trans('labels.transact_or_number') }}: 
                    </strong>
                </div>
                <div class="col-8">
                    <span id="transact-or-number"></span>
                </div>
            </div> --}}
            <div class="row align-items-center my-3">
                <div class="col-4">
                    <strong>
                        {{ trans('labels.transact_customer') }}: 
                    </strong>
                </div>
                <div class="col-8">
                    <span id="transact-customer"></span>
                </div>
            </div>
            <div class="row align-items-center my-3">
                <div class="col-4">
                    <strong>
                        {{ trans('labels.transact_curr') }}: 
                    </strong>
                </div>
                <div class="col-8">
                    <span id="transact-currency"></span>
                </div>
            </div>
            <div class="row align-items-center my-3">
                <div class="col-4">
                    <strong>
                        {{ trans('labels.transact_type') }}:
                    </strong>
                </div>
                <div class="col-8">
                    <span id="transact-type"></span>
                </div>
            </div>
            <div class="row align-items-center my-3">
                <div class="col-4">
                    <strong>
                        {{ trans('labels.transact_curr_amnt') }}: 
                    </strong>
                </div>
                <div class="col-8">
                    <strong>
                        <span class="text-xl">{{ $trans_deets->CurrAbbv }}</span> <span class="text-xl" id="transact-currency-amount"></span>
                    </strong>
                </div>
            </div>
            <div class="row align-items-center mt-3">
                <div class="col-4">
                    <strong>
                        {{ trans('labels.transact_amnt') }}:
                    </strong>
                </div>
                <div class="col-8">
                    <strong>
                        <span class="text-xl">PHP</span> <span class="text-xl" id="transact-amount"></span>
                    </strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <div class="row align-items-center">
        <div class="col-12 p-0 text-end">
            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>

            <button class="btn btn-primary" type="button" id="print-test" data-bs-toggle="modal" data-bs-target="#security-code-modal">Print</button>
        </div>
    </div>
</div>



