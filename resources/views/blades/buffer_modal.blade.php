<div class="modal fade" id="buffer-modal" tabindex="-1" aria-labelledby="buying-transact" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-12">
                    <span class="text-lg font-bold p-2">
                        <i class='bx bx-coin-stack'></i> Processed Buffer
                    </span>
                </div>
            </div>

            <div class="modal-body p-2">
                <div class="col-12" id="buffer">
                    <table class="table table-bordered table-hover m-0" id="buffer-stocks-table">
                        <thead>
                            <tr>
                                <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_currency') }}</th>
                                <th class="text-center text-xs font-extrabold text-black p-1">Denomination</th>
                                <th class="text-center text-xs font-extrabold text-black p-1">Count</th>
                                <th class="text-center text-xs font-extrabold text-black p-1">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ trans('labels.close_action') }}</button>
            </div>
        </div>
    </div>
</div>
