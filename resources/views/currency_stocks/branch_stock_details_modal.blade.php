<!-- Modal -->
<div class="modal-header px-4 py-2">
    <span class="text-lg font-bold text-black">{{ trans('labels.curr_stocks_stock_details') }}</span>
</div>

<div class="modal-body px-4 py-2">
    <div class="row justify-content-center">
        <div class="col-12 text-center mt-2">
            <table class="table table-hover table-bordered mb-0" id="branch-avail-stocks">
                <thead>
                    <tr>
                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Currency</th>
                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Quantity</th>
                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Total Amount</th>
                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Total Principal</th>
                        <th class="text-center text-xs font-extrabold text-black p-1 w-0">Action</th>
                    </tr>
                </thead>
                <tbody id="branch-avail-stocks-body">

                </tbody>
            </table>
        </div>

        <div class="col-12 text-center">
            <hr class="my-2">
        </div>
    </div>

    <div class="col-12 d-none" id="cash-count-container">
        <div class="row align-items-center">
            <div class="col-12 text-left">
                <span class="text-lg font-bold text-black">Cash Count</span>&nbsp;
                <strong><span class="text-md font-semibold text-black" id="currency"></span></strong>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-12 text-center mt-2">
                <table class="table mb-2 table-hover table-bordered" id="cash-count-breakdown">
                    <thead>
                        <tr>
                            <th class="text-center text-xs font-extrabold text-black p-1 w-25">Bill Amount</th>
                            <th class="text-center text-xs font-extrabold text-black p-1 w-25">Quantity</th>
                            <th class="text-center text-xs font-extrabold text-black p-1 w-25">Amount</th>
                        </tr>
                    </thead>
                    <tbody id="cash-count-breakdown-body">
                        <tr>
                            <td class="text-black text-sm text-center p-2" colspan="3">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- <div class="row justify-content-center">
            <div class="col-12 text-center">
                <table class="table table-hover table-bordered mb-0" id="bill-cash-count">
                    <thead>
                        <tr>
                            <th class="text-center text-xs font-extrabold text-black p-1 w-25">{{ trans('labels.transfer_forex_total_amnt') }}</th>
                            <th class="text-center text-xs font-extrabold text-black p-1 w-25">{{ trans('labels.transfer_forex_count') }}</th>
                            <th class="text-center text-xs font-extrabold text-black p-1 w-25">{{ trans('labels.curr_stocks_curr_total_principal') }}</th>
                        </tr>
                    </thead>
                    <tbody id="bill-cash-count-body">
                        <tr>
                            <td class="text-black text-sm text-center p-2" colspan="3">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div> --}}

        <div class="col-lg-12 mt-1 border border-solid border-gray-300" id="stock-summary-container">
            <div class="row">
                <table class="table table-hover mb-0" id="branch-stock-details-table">
                    <thead class="sticky-header">
                        <tr>
                            <th class="text-th-buying text-center text-xs font-extrabold text-black py-1 px-1">{{ trans('labels.curr_stocks_curr_bill_amnt') }}</th>
                            <th class="text-th-buying text-center text-xs font-extrabold text-black py-1 px-1">Total Amount</th>
                            <th class="text-th-buying text-center text-xs font-extrabold text-black py-1 px-1">Transaction Date</th>
                            {{-- <th class="text-th-buying text-center text-xs font-extrabold text-black py-1 px-1">{{ trans('labels.curr_stocks_curr_serials') }}</th> --}}
                            {{-- <th class="text-th-buying text-center text-xs font-extrabold text-black py-1 px-1">{{ trans('labels.curr_stocks_curr_transact_type') }}</th> --}}
                            {{-- <th class="text-th-buying text-center text-xs font-extrabold text-black py-1 px-1">{{ trans('labels.curr_stocks_curr_rate_used') }}</th> --}}
                            {{-- <th class="text-th-buying text-center text-xs font-extrabold text-black py-1 px-1">{{ trans('labels.curr_stocks_curr_principal') }}</th> --}}
                            <th class="text-th-buying text-center text-xs font-extrabold text-black py-1 px-1">{{ trans('labels.curr_stocks_curr_days_in_stock') }}</th>
                        </tr>
                    </thead>
                    <tbody id="branch-stock-details-table-body">
                        <tr>
                            <td class="text-black text-sm text-center p-2" colspan="8">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
</div>
