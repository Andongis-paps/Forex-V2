<div class="modal-header ps-4">
    <div class="col-12 text-left">
        <strong>
            <span class="text-lg">{{ trans('labels.transfer_summary') }}</span>
        </strong>
    </div>
</div>

<div class="modal-body px-4 py-2">
    <div class="row justify-content-center">
        <div class="col-12 mb-2 text-center">
            <strong>
                <span class="text-lg">Cash Count</span>
            </strong>
        </div>

        <div class="col-12 text-center">
            <table class="table table-hover table-bordered" id="bill-cash-count">
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

        <div class="row">
            <div class="col-lg-12 border border-solid border-gray-300 rounded-md p-0 mb-2" id="transfer-summary-container">
                <table class="table table-hover mb-0" id="bill-for-transfer-table">
                    <thead class="sticky-header">
                        <tr>
                            <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_forex_currency') }}</th>
                            <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_forex_bill_amnt') }}</th>
                            <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_forex_serials') }}</th>
                        </tr>
                    </thead>
                    <tbody id="bill-for-transfer-table-body">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <div class="row align-items-center">
        <div class="col-6 text-end pe-0">
            <button class="btn btn-secondary btn-sm" type="button" data-bs-dismiss="modal">Cancel</button>
        </div>
        <div class="col-6 text-end pe-2">
            <button class="btn btn-primary btn-sm" type="button" id="proceed-transfer">Proceed</button>
        </div>
    </div>
</div>



