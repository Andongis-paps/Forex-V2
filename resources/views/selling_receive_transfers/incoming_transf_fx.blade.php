<!-- Modal -->
<div class="modal-header px-4">
    <div class="row">
        <span class="modal-title text-lg font-bold" id="exampleModalLabel">Incoming Transfer Forex Details</span>
    </div>
</div>

<div class="modal-body p-2">
    <div class="row justify-content-center">
        {{-- <div class="col-12 mb-2 text-center">
            <strong>
                <span class="text-lg">Cash Count</span>
            </strong>
        </div> --}}

        <div class="col-12 text-center">
            <table class="table table-hover table-bordered mb-2" id="bill-cash-count">
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
            <div class="col-lg-12 mb-0 border border-solid border-gray-300 p-0" id="transfer-summary-container">
                <table class="table table-hover mb-0" id="bill-for-transfer-table">
                    <thead class="sticky-header">
                        <tr>
                            <th class="text-center text-xs font-extrabold text-black p-1">Transaction Date</th>
                            <th class="text-center text-xs font-extrabold text-black p-1">Invoice No.</th>
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
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
