{{-- Modal - Confirm using security code --}}
<div class="modal fade" id="unqueue-over-cap-bills-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header px-4">
                <div class="col-6">
                    <strong>
                        <span class="text-lg font-bold">Select Bills to Sell</span>
                    </strong>
                </div>
            </div>
            <div class="modal-body py-3">
                <div class="col-12 py-1 px-3 border-3 border-gray-300 rounded-3 queued-bills-parent-container" id="over-cap-bill-modal-body">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('labels.cancel_action') }}</button>
                @can('edit-permission', $menu_id)
                    <button type="button" class="btn btn-primary" id="proceed-unqueue-missing-bills">{{ trans('labels.proceed_action') }}</button>
                @endcan
            </div>
        </div>
    </div>
</div>
