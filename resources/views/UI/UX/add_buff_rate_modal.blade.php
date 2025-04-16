{{-- Modal - Confirm using security code --}}
<div class="modal fade" id="set-buff-rate-modal" tabindex="-1" aria-labelledby="buying-transact" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header px-4 py-2">
                <strong>
                    <span class="text-lg font-bold">Set Buffer Rate</span>
                </strong>
            </div>
            <div class="modal-body">
                <div class="row px-2">
                    <div class="col-12">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <label class="text-sm mb-1" for="buffer-rate">
                                    <strong>
                                        Rate: &nbsp;<span class="required-class">*</span>
                                    </strong>
                                </label>

                                <input class="form-control text-right" id="buffer-rate" name="buffer-rate" type="number" placeholder="0.0000" value="{{ number_format($result['buffer_rate'], 4, '.', ',') }}">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <hr class="my-2">
                    </div>
        
                    <div class="col-lg-12 py-1">
                        <div class="row justify-content-center align-items-center">
                            <div class="col-12 text-center">
                                <label class="text-sm mb-1" for="description">
                                    <strong>
                                        {{ trans('labels.enter_security_code') }}:
                                    </strong>
                                </label>
        
                                <input class="form-control password" id="set-buff-rate-security-code" name="security-code" type="password">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancel-transaction" data-bs-dismiss="modal">{{ trans('labels.cancel_action') }}</button>
                <button type="button" class="btn btn-primary" id="set-buff-rate">{{ trans('labels.proceed_action') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#set-buff-rate-modal').on('shown.bs.modal', function () {
            $('#set-buff-rate-security-code').focus();
        });
    });
</script>
