<div class="modal-header ps-4">
    <span class="text-lg">
        <strong>
            {{ trans('labels.buffer_stocks_summary') }}
        </strong>
    </span>
    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
</div>

<div class="modal-body px-4 py-2">
    <div class="col-12 text-center">
        <strong>
            <span class="text-lg">Cash Count</span>
        </strong>
    </div>

    <div class="col-12 text-center mt-2">
        <table class="table table-hovered table-bordered " id="bill-cash-count">
            <thead>
                <tr>
                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_forex_currency') }}</th>
                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_forex_count') }}</th>
                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_forex_total_amnt') }}</th>
                </tr>
            </thead>
            <tbody id="bill-cash-count-body">

            </tbody>
        </table>
    </div>

    <div class="col-lg-12 mb-2 border border-solid border-gray-300" id="transfer-summary-container">
        <div class="row align-items-center">
            <div class="col-12">
                <table class="table table-hover mb-0 " id="bills-for-buffer-table">
                    <thead>
                        <tr>
                            <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_forex_currency') }}</th>
                            <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_forex_serials') }}</th>
                            <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_forex_bill_amnt') }}</th>
                        </tr>
                    </thead>
                    <tbody id="bill-for-transfer-table-body">

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- <div class="col-12">
        <hr class="mb-2 mt-3">
    </div> --}}

    {{-- <div class="col-12">
        <label for="">
            <strong>
                Remarks: &nbsp;
            </strong>
        </label>

        <textarea class="form-control my-2" id="remarks" rows="4"></textarea>
    </div> --}}
</div>

<div class="modal-footer">
    <div class="row align-items-center">
        <div class="col-6 text-end pe-0">
            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
        </div>
        <div class="col-6 text-end pe-2">
            <button class="btn btn-primary" type="button" id="proceed-transfer">Proceed</button>
        </div>
    </div>
</div>



