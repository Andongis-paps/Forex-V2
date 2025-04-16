<!-- Modal -->
<div class="modal-header px-4 py-2">
    <span class="text-lg font-bold text-black">{{ trans('labels.curr_stocks_stock_details') }}</span>
</div>

<div class="modal-body px-4 pt-2">
    <div class="row justify-content-center">
        <div class="col-12 my-1 text-start">
            <div class="row align-items-center">
                <div class="col-3">
                    <input class="form-check-input" id="unhold-serials" type="checkbox" value="1">&nbsp;&nbsp;<span class="font-bold">Unhold Bills</span>
                </div>
                <div class="col-3">
                    <select class="form-select" id="denomination-filter" disabled>
                    </select>
                </div>
                <div class="col-6">
                    <input class="form-control" id="searial-search" type="text" placeholder="Search serial" disabled>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 mt-2 border border-solid border-gray-300" id="onhold-stock-summary-container">
        <div class="row">
            <table class="table table-hover mb-0" id="held-stocks">
                <thead class="sticky-header">
                    <tr>
                        <th class="text-center text-xs font-extrabold text-black py-1">
                            <input class="form-check-input" id="select-all-held-stocks" type="checkbox" disabled>
                        </th>
                        <th class="text-center text-xs font-extrabold text-black p-1">Date Held</th>
                        <th class="text-center text-xs font-extrabold text-black p-1">Held By</th>
                        <th class="text-center text-xs font-extrabold text-black p-1">Receipt Set</th>
                        <th class="text-center text-xs font-extrabold text-black p-1">Serial</th>
                        <th class="text-center text-xs font-extrabold text-black p-1">Bill Amount</th>
                        <th class="text-center text-xs font-extrabold text-black p-1">Rate Used</th>
                        <th class="text-center text-xs font-extrabold text-black p-1">Principal</th>
                    </tr>
                </thead>
                <tbody id="held-stocks-body">
                </tbody>
            </table>
        </div>
    </div>

    <div class="row justify-content-center d-none" id="unhold-sec-code-container">
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

        <div class="col-4 mt-1">
            <div class="col-lg-12 px-3">
                <div class="row justify-content-center align-items-center">
                    <div class="col-12 text-center mb-1">
                        <label for="description">
                            <strong>
                                {{ trans('labels.enter_security_code') }} &nbsp; <span class="required-class">*</span>
                            </strong>
                        </label>
                    </div>

                    <div class="col-8 mb-1">
                        <input class="form-control" step="any" autocomplete="false" id="unhold-security-code" type="password">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    @can('add-permission', $menu_id)
        <button type="button" class="btn btn-primary d-none" id="proceed-unhold">Proceed</button>
    @endcan
</div>
