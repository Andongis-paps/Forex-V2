<!-- Modal -->
    <div class="modal-header px-4">
        <div class="row">
            <span class="modal-title text-lg font-bold" id="exampleModalLabel">Add New Transact Type</span>
        </div>
    </div>

    <div class="modal-body px-4">
        <div class="row">
            <div class="col-lg-12">
                <form class="form m-0" action="{{ route('maintenance.transaction_types.add') }}" enctype="multipart/form-data" method="POST" id="add-transact-type-form">
                    @csrf

                    <div class="row align-items-center">
                        <div class="col-12">
                            <label class="mb-2" for="description">
                                <strong>
                                    {{ trans('labels.w_transact_type_mainte_descr') }}:&nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>

                            <input type="text" name="transact_type" id="transact-type" class="form-control">
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-12 px-3 my-2">
                <hr>
            </div>

            <div class="col-lg-12 px-3">
                <div class="row justify-content-center align-items-center">
                    <div class="col-8">
                        <input class="form-control" step="any" autocomplete="false" id="add-transact-type-security-code" type="password">
                    </div>

                    <div class="col-12 text-center mt-2">
                        <label for="description">
                            <strong>
                                {{ trans('labels.enter_security_code') }} &nbsp; <span class="required-class">*</span>
                            </strong>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        @can('add-permission', $menu_id)
            <button type="button" class="btn btn-primary" id="transact-type-add-button">Add</button>
        @endcan
    </div>

<script>
    $(document).ready(function() {
        $('#add-transact-type-form').validate({
            rules: {
                transact_type: 'required',
            },
            messages: {
                transact_type: 'Transaction type is required.',
            },
        });
    });
</script>
