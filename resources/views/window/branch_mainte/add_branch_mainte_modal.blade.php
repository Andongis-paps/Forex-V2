<!-- Modal -->
    <div class="modal-header px-4">
        <div class="row">
            <span class="text-lg font-bold">{{ trans('labels.w_branch_add') }}</span>
        </div>
    </div>

    <div class="modal-body px-4">
        <div class="row">
            <div class="col-lg-12">
                <form class="form m-0" enctype="multipart/form-data" method="POST" id="add-new-branch-form">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-4">
                            <label for="description">
                                <strong>
                                    {{ trans('labels.w_branch_code') }}: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
                        </div>
                        <div class="col-8">
                            <input type="text" name="branch_code" class="form-control" step="any" required>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-4">
                            <label for="description">
                                <strong>
                                    {{ trans('labels.w_branch_name') }}: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
                        </div>
                        <div class="col-8">
                            <input type="text" name="branch_name" class="form-control" step="any" required>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-4">
                            <label for="description">
                                <strong>
                                    {{ trans('labels.w_branch_address') }}:
                                </strong>
                            </label>
                        </div>
                        <div class="col-8">
                            <input type="text" name="branch_address" class="form-control" step="any" required>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-4">
                            <label for="description">
                                <strong>
                                    {{ trans('labels.w_branch_tel_no') }}:
                                </strong>
                            </label>
                        </div>
                        <div class="col-8">
                            <input type="number" name="branch_telno" class="form-control" step="any" required>
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
                        <input class="form-control" step="any" autocomplete="false" id="add-branch-security-code" type="password">
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
            <button type="button" class="btn btn-primary" id="branch-add-button">Add</button>
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
