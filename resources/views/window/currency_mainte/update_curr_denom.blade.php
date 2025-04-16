<div class="modal fade" id="update-curr-denom-modal" tabindex="-1" aria-labelledby="buying-transact" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header px-4">
                <strong>
                    <span class="text-xl font-bold">Update Denomination</span>
                </strong>
            </div>
            <div class="modal-body py-2">
                <form class="m-0" method="post" action="{{ route('maintenance.currency_maintenance.update_one_denom') }}" id="update-denom">
                    @csrf
                    <input class="form-control" type="hidden" value="{{ $result['currency']->CurrencyID }}" name="currency">

                    <div class="row">
                        <div class="col-12">
                            <label class="text-sm text-black font-bold mb-1" for="denomination">Denomination:</label>

                            <input class="form-control" type="number" name="denomination" id="denomination">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <label class="text-sm text-black font-bold mb-1" for="trans-type">Transaction Type:</label>

                            <select class="form-select trans-type" name="trans-type" id="trans-type">
                                <option value="">Select transaction type</option>

                                @foreach ($result['transact_type'] as $transact_type)
                                    <option value="{{ $transact_type->TTID }}">{{ $transact_type->TransType }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <label class="text-sm text-black font-bold mb-1" for="switch-input">Status:</label>
                        </div>

                        <div class="col-12">
                            <label class="switch switch-success switch-square">
                                <input type="checkbox" class="switch-input" name="switch-input">
                                <span class="switch-toggle-slider">
                                    <span class="switch-on"></span>
                                    <span class="switch-off"></span>
                                </span>
                                <span class="switch-label cursor-pointer">
                                    {{-- @if ($transact_type_details[0]->Active == 1)
                                        <strong>
                                            Active
                                        </strong>
                                    @else
                                        <strong>
                                            Inactive
                                        </strong>
                                    @endif --}}
                                </span>
                            </label>
                        </div>
                    </div>
                </form>

                <div class="col-12">
                    <hr class="my-2">
                </div>

                <div class="col-lg-12 my-1">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-12 text-center">
                            <label class="text-sm mb-1" for="description">
                                <strong>
                                    {{ trans('labels.enter_security_code') }}:
                                </strong>
                            </label>
        
                            <input class="form-control password" step="any" autocomplete="false" id="update-curr-denom-sec-code" type="password">
                        </div>
                    </div>
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('labels.cancel_action') }}</button>
                <button type="button" class="btn btn-primary" id="proceed-update">{{ trans('labels.proceed_action') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#del-denom-security-code-modal').on('shown.bs.modal', function () {
            $('#del-denom-security-code').focus();
        });

        $('.switch-input').change(function() {
            if ($(this).is(':checked')) {
                $(this).attr('checked', 'checked');
                $(this).siblings('.switch-label').fadeOut(100, function() {
                    $(this).empty().html('<strong>Active</strong>').fadeIn(100);
                });
            } else {
                $(this).removeAttr('checked');
                $(this).siblings('.switch-label').fadeOut(100, function() {
                    $(this).empty().html('<strong>Inactive</strong>').fadeIn(100);
                });
            }
        });
    });
</script>
