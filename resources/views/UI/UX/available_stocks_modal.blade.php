<div class="modal fade" id="availalbe-stocks-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header px-4">
                <strong>
                    <span class="text-xl font-bold">Available Stocks</span>
                </strong>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-hover m-0" id="available-stocks-table">
                    <thead>
                        <tr>
                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.curr_stocks_admin_stocks_curr') }}</th>
                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.curr_stocks_admin_stocks_pieces') }}</th>
                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.curr_stocks_admin_stocks_total_curr_amnt') }}</th>
                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Queued (PCS)</th>
                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Amount (Queued)</th>
                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Principal</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ trans('labels.action_close') }}</button>
            </div>
        </div>
    </div>
</div>
