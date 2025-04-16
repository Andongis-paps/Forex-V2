<!-- Modal -->
<div class="modal-header px-4 py-2">
    <span class="text-lg font-bold text-black">{{ trans('labels.curr_stocks_stock_details') }}&nbsp;<span class="text-lg font-bold text-black" id="currency"></span></span>
</div>

<div class="modal-body px-4 pb-2 pt-1">
    {{-- <div class="col-12 text-center">
        <span class="text-lg font-bold text-black">Cash Count</span>
    </div> --}}

    <div class="row justify-content-center">
        <div class="accordion-item active">
            <h2 class="accordion-header" id="headingOne">
                <button type="button" class="accordion-button py-2 px-0" data-bs-toggle="collapse" data-bs-target="#accordionOne" aria-expanded="true" aria-controls="accordionOne" role="tabpanel">
                    <strong>Details</strong>
                </button>
            </h2>

            <div id="accordionOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                <div class="accordion-body py-0 px-0">
                    <div class="row justify-content-center">
                        <div class="col-6 text-center mt-1">
                            <table class="table table-hover table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center text-xs font-extrabold p-1 !bg-[#0D6EFD] text-white" colspan="3">Regular Stock Details</th>
                                    </tr>
                                </thead>
                            </table>

                            <table class="table table-hover table-bordered mb-0 mt-2" id="admin-rset-count">
                                <thead>
                                    <tr>
                                        <th class="text-center text-xs font-extrabold p-1 text-black" colspan="3">Summary</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Receipt Set</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Quantity</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="admin-rset-count-body">
                                </tbody>
                                <tfoot>
                                    <td class="p-1 text-center" colspan="2"></td>
                                    <td class="p-1 px-3 text-right" colspan="1">
                                        <span class="text-xs font-bold" id="by-rset-reg-total"></span>
                                    </td>
                                </tfoot>
                            </table>

                            <table class="table table-hover table-bordered mb-0 mt-2" id="admin-cash-count-breakdown">
                                <thead>
                                    <tr>
                                        <th class="text-center text-xs font-extrabold p-1 text-black" colspan="4">Breakdown</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Bill Amount</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Quantity</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Amount</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Principal</th>
                                    </tr>
                                </thead>
                                <tbody id="admin-cash-count-breakdown-body">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td class="text-xs text-right px-3 py-1">
                                            <strong>
                                                <span id="bill-amount-sum"></span>
                                            </strong>
                                        </td>
                                        <td class="text-xs text-right px-3 py-1">
                                            <strong>
                                                <span id="principal-sum"></span>
                                            </strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="col-6 mt-1 text-center">
                            <table class="table table-hover table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center text-xs font-extrabold p-1 !bg-[#00A65A] text-white" colspan="3">Buffer Stock Details</th>
                                    </tr>
                                </thead>
                            </table>

                            <table class="table table-hover table-bordered mb-0 mt-2" id="admin-rset-count-buffer">
                                <thead>
                                    <tr>
                                        <th class="text-center text-xs font-extrabold p-1 text-black" colspan="3">Summary</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Receipt Set</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Quantity</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="admin-rset-count-buffer-body">
                                </tbody>
                                <tfoot>
                                    <td class="p-1 text-center" colspan="2"></td>
                                    <td class="p-1 px-3 text-right" colspan="1">
                                        <span class="text-xs font-bold" id="by-rset-buff-total"></span>
                                    </td>
                                </tfoot>
                            </table>

                            <table class="table table-hover table-bordered mb-0 mt-2" id="admin-buffer-breakdown">
                                <thead>
                                    <tr>
                                        <th class="text-center text-xs font-extrabold p-1 text-black" colspan="4">Breakdown</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Bill Amount</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Quantity</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Amount</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1 w-25">Principal</th>
                                    </tr>
                                </thead>
                                <tbody id="admin-buffer-breakdown">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td class="text-xs text-right px-3 py-1">
                                            <strong>
                                                <span id="bill-amount-sum-buff"></span>
                                            </strong>
                                        </td>
                                        <td class="text-xs text-right px-3 py-1">
                                            <strong>
                                                <span id="principal-sum-buff"></span>
                                            </strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 text-center">
        <hr class="my-2">
    </div>

    <div class="col-12 mt-2 text-start">
        <div class="row align-items-center">
            <div class="col-2">
                <input class="form-check-input" id="onhold-serials" type="checkbox" value="1" disabled>&nbsp;&nbsp;<span class="font-bold">Onhold Bills</span>
            </div>
            <div class="col-3">
                <select class="form-select" id="transaction-filter" disabled>
                    <option value="default">All</option>
                    <option value="1">Regular</option>
                    <option value="2">Buffer</option>
                </select>
            </div>
            <div class="col-3">
                <select class="form-select" id="denomination-filter" disabled>
                </select>
            </div>
            <div class="col-4">
                <input class="form-control" id="searial-search" type="text" placeholder="Search serial" disabled>
            </div>
        </div>
    </div>

    <div class="col-lg-12 mt-2 border border-solid border-gray-300" id="admin-stock-summary-container">
        <div class="row">
            <table class="table table-hover mb-0" id="admin-stock-details-table">
                <thead class="sticky-header">
                    <tr>
                        <th class="text-center text-xs font-extrabold text-black py-1">
                            <input class="form-check-input" id="stocks-select-all-serial" type="checkbox" disabled>
                        </th>
                        <th class="text-center text-xs font-extrabold text-black p-1">Receipt Set</th>
                        {{-- <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.curr_stocks_curr_transact_type') }}</th> --}}
                        <th class="text-center text-xs font-extrabold text-black p-1">Type</th>
                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.curr_stocks_curr_serials') }}</th>
                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.curr_stocks_curr_bill_amnt') }}</th>
                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.curr_stocks_curr_rate_used') }}</th>
                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.curr_stocks_curr_principal') }}</th>
                    </tr>
                </thead>
                <tbody id="admin-stock-details-table-body">
                </tbody>
            </table>
        </div>
    </div>

    <div class="row justify-content-center d-none" id="onhold-sec-code-container">
        <div class="row align-items-center">
            <div class="col-9 mt-2 text-end">
                <span class="text-md text-black font-bold">Total Amount:</span>
            </div>
            <div class="col-3 mt-2 text-end">
                <span class="text-lg text-black font-extrabold" id="currency-abbrv"></span>&nbsp;<span class="text-lg text-black font-extrabold" id="onhold-total-amount">0.00</span>
                <input type="hidden" id="onhold-total-amnt-input" value="0">
            </div>
        </div>

        <div class="col-lg-12 px-3">
            <hr class="my-2">
        </div>

        <div class="col-7">
            <div class="col-lg-12 px-3">
                <div class="row justify-content-center align-items-center">
                    <div class="col-12 text-center mb-1">
                        <label for="description">
                            <strong>
                                {{ trans('labels.enter_security_code') }} &nbsp;:
                            </strong>
                        </label>
                    </div>

                    <div class="col-8 mb-1">
                        <input class="form-control" step="any" autocomplete="false" id="onhold-security-code" type="password">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
    @can('add-permission', $menu_id)
        <button type="button" class="btn btn-primary d-none btn-sm" id="proceed-onhold">Proceed</button>
    @endcan
</div>
