{{-- Modal - Confirm using security code --}}
<div class="modal fade" id="buff-transf-deets-security-code-modal" tabindex="-1" aria-labelledby="buying-transact" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header px-4">
                <strong>
                    <span class="text-xl font-bold">{{ trans('labels.enter_security_code') }}</span>
                </strong>
            </div>
            <div class="modal-body">
                <div class="row px-2">
                    <div class="col-12 m-2">
                        {{-- <span>
                            <strong>
                                {{ trans('labels.buying_enter_sec_code') }}: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </span> --}}
                    </div>
                    <div class="col-12 mb-3">
                        <input class="form-control password" id="buff-deets-security-code" name="buff-deets-security-code" type="password">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('labels.cancel_action') }}</button>
                <button type="button" class="btn btn-primary" id="proceed-unreceive-buffer">{{ trans('labels.proceed_action') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#buff-transf-deets-security-code-modal').on('shown.bs.modal', function () {
            $('#buff-deets-security-code').focus();
        });
    });
</script>

