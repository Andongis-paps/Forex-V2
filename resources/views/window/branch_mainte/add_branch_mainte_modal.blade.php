<!-- Modal -->
    <div class="modal-header px-3">
        <div class="row">
            <span class="text-lg font-bold"><i class='bx bx-save me-1'></i>{{ trans('labels.w_branch_add') }}</span>
        </div>
    </div>

    <div class="modal-body px-4 py-2">
        <div class="row">
            <div class="col-lg-12">
                <form class="form m-0" enctype="multipart/form-data" method="POST" id="add-new-branch-form">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-12">
                            <label class="text-sm mb-1 p-0" for="branch_code">
                                <strong>
                                    {{ trans('labels.w_branch_code') }}: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
                        
                            <input type="text" name="branch_code" class="form-control" step="any" required>
                        </div>
                    </div>
                    <div class="row align-items-center mt-2">
                        <div class="col-12">
                            <label class="text-sm mb-1 p-0" for="branch_name">
                                <strong>
                                    {{ trans('labels.w_branch_name') }}: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
                        
                            <input type="text" name="branch_name" class="form-control" step="any" required>
                        </div>
                    </div>
                    <div class="row align-items-center mt-2">
                        <div class="col-12">
                            <label class="text-sm mb-1 p-0" for="dpofx-rate">
                                <strong>
                                    {{ trans('labels.w_branch_address') }}:
                                </strong>
                            </label>
                        
                            <input type="text" name="branch_address" class="form-control" step="any" required>
                        </div>
                    </div>
                    <div class="row align-items-center mt-2">
                        <div class="col-12">
                            <label class="text-sm mb-1 p-0" for="branch_telno">
                                <strong>
                                    {{ trans('labels.w_branch_tel_no') }}:
                                </strong>
                            </label>
                        
                            <input type="number" name="branch_telno" class="form-control" step="any" required>
                        </div>
                    </div>
                </form>
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
                    </div>

                    <div class="col-12">
                        <input class="form-control" step="any" autocomplete="false" id="add-branch-security-code" type="password">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        @can('add-permission', $menu_id)
            <button type="button" class="btn btn-primary btn-sm" id="branch-add-button">Add</button>
        @endcan
    </div>

<script>
    $(document).ready(function() {
        $('#add-new-branch-form').validate({
            rules: {
                branch_code: {
                    required: true,
                    pattern: /^[A-Z0-9\s]*(?<![\[{}\]])[A-Z0-9\s]+(?![\]{}\]])$/
                },
                branch_name: 'required',
                branch_address: 'required',
                branch_telno: 'required'
            },
            messages: {
                branch_code: {
                    required: 'Enter a branch code. ',
                    pattern: 'Branch code incorrect format. (Example: S999).'
                },
                branch_name: 'Enter a branch name.',
                branch_address: 'Enter a branch address.',
                branch_telno: 'Enter a branch telephone number.'
            },
            // submitHandler: function(form) {
            //     form.submit();
            // }
        });
    });
</script>

{{-- @include('script.scripts') --}}
