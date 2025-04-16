{{-- Modal - Confirm using security code --}}
<div class="modal fade" id="untag-missing-bill-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header px-4">
                <strong>
                    <span class="text-xl font-bold">{{ trans('labels.selling_admin_tag_found_status') }}</span>
                </strong>
            </div>
            <div class="modal-body">
                <form method="post" id="missing-bill-status-form">
                    @csrf
                    <div class="row align-items-center px-2 mt-1">
                        <label for="">
                            <strong>
                                {{ trans('labels.selling_admin_tag_found_stat') }}: &nbsp;
                            </strong>
                        </label>

                        <div class="btn-group mt-2" role="group" aria-label="Basic radio toggle button group">
                            <input type="radio" class="btn-check" name="radio-found-status" id="found" value="1">
                            <label class="btn btn-outline-primary" for="found">
                                <strong>{{ trans('labels.selling_admin_tag_found') }}</strong>
                            </label>

                            <input type="radio" class="btn-check" name="radio-found-status" id="not-found" value="2">
                            <label class="btn btn-outline-primary" for="not-found">
                                <strong>{{ trans('labels.selling_admin_tag_not_found') }}</strong>
                            </label>
                        </div>
                    </div>

                    <div class="row align-items-center px-2 my-3" id="found-at-row">
                        <label for="">
                            <strong>
                                {{ trans('labels.selling_admin_tag_found_place') }}: &nbsp;
                            </strong>
                        </label>

                        <div class="btn-group mt-2" role="group" aria-label="Basic radio toggle button group">
                            <input type="radio" class="btn-check" name="radio-found-place" id="found-office" value="1" disabled="true">
                            <label class="btn btn-outline-primary" for="found-office">
                                <strong>{{ trans('labels.selling_admin_tag_found_p_h_off') }}</strong>
                            </label>

                            <input type="radio" class="btn-check" name="radio-found-place" id="found-branch" value="2" disabled="true">
                            <label class="btn btn-outline-primary" for="found-branch">
                                <strong>{{ trans('labels.selling_admin_tag_found_p_branch') }}</strong>
                            </label>
                        </div>
                    </div>
                </form>
                {{-- I miss you! ♥ --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('labels.cancel_action') }}</button>
                <button type="button" class="btn btn-primary" id="proceed-untag-missing-bills" disabled>{{ trans('labels.proceed_action') }}</button>
            </div>
        </div>
    </div>
</div>
