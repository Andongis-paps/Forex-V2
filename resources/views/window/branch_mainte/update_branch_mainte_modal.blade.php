<!-- Modal -->
    <div class="modal-header px-4">
        <div class="row">
            <h4 class="modal-title" id="exampleModalLabel">{{ trans('labels.w_branch_update') }}</h4>
        </div>
    </div>

    <div class="modal-body px-4">
        <div class="row">
            <div class="col-lg-12">
                <form class="form m-0" enctype="multipart/form-data" method="POST" id="update-branch-form">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ $branch_details[0]->BranchID }}">

                    <div class="row align-items-center">
                        <div class="col-4">
                            <label for="description">
                                <strong>
                                    {{ trans('labels.w_branch_code') }}: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
                        </div>
                        <div class="col-8">
                            <input type="text" name="branch_code" class="form-control" value="{{ $branch_details[0]->BranchCode }}" step="any" id="branch_code">
                        </div>
                    </div>
                    {{-- <div class="row align-items-center mt-3">
                        <div class="col-4">
                            <label for="description">
                                <strong>
                                    {{ trans('labels.w_branch_name') }}: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
                        </div>
                        <div class="col-8">
                            <input type="text" name="branch_name" class="form-control" value="{{ $branch_details[0]->BranchName }}" step="any" id="branch_name">
                        </div>
                    </div> --}}
                    <div class="row mt-3">
                        <div class="col-4">
                            <label for="description">
                                <strong>
                                    {{ trans('labels.w_branch_address') }}:
                                </strong>
                            </label>
                        </div>
                        <div class="col-8">
                            <textarea class="form-control" id="branch_address" name="branch_address"  rows="3" readonly>{{ $branch_details[0]->Address }}</textarea>
                            {{-- <input type="text" name="branch_address" class="form-control" value="{{ $branch_details[0]->Address }}" step="any" id="branch_address" readonly> --}}
                        </div>
                    </div>
                    {{-- <div class="row align-items-center mt-3">
                        <div class="col-4">
                            <label for="description">
                                <strong>
                                    {{ trans('labels.w_branch_tel_no') }}:
                                </strong>
                            </label>
                        </div>
                        <div class="col-8">
                            <input type="text" name="branch_telno" class="form-control" value="{{ $branch_details[0]->Telno }}" step="any" id="branch_telno">
                        </div>
                    </div> --}}
                </form>
            </div>

            <div class="col-lg-12 px-3 my-2">
                <hr>
            </div>

            <div class="col-lg-12 px-3">
                <div class="row justify-content-center align-items-center">
                    <div class="col-8">
                        <input class="form-control" step="any" autocomplete="false" id="update-branch-security-code" type="password">
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
        <button type="button" class="btn btn-primary" id="branch-details-update-button">Save changes</button>
    </div>

<script>
    $(document).ready(function() {
        $('#update-branch-form').validate({
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
                    required: 'Enter the branch code',
                    pattern: 'Branch code incorrect format. (Example: S999).'
                },
                branch_name: 'Enter the branch name.',
                branch_address: 'Enter the branch address.',
                branch_telno: 'Enter the branch telephone number.'
            },
            // submitHandler: function(form) {
            //     form.submit();
            // }
        });
    });

    // AJAX request for update branch details - Window Based - Branch
    $(document).ready(function() {
        $('#branch-details-update-button').click(function(){
            var branch_id = $('input[name="branch_id"]').val();
            var branch_code = $('input[name="branch_code"]').val();
            var branch_name = $('input[name="branch_name"]').val();
            var branch_address = $('input[name="branch_address"]').val();
            var branch_telno = $('input[name="branch_telno"]').val();
            var user_sec_onpage = $('#update-branch-security-code').val();

            if (sec_code_array.includes(user_sec_onpage)) {
                $(this).prop('disabled', true);

                var index = sec_code_array.indexOf(user_sec_onpage);
                var matched_user_id = user_id_array[index];

                Swal.fire({
                    title: 'Success!',
                    text: 'Branch updated!',
                    icon: 'success',
                    timer: 900,
                    showConfirmButton: false
                }).then(() => {
                    var form_data = new FormData($('#update-branch-form')[0]);
                    form_data.append('matched_user_id', matched_user_id);

                    setTimeout(() => {
                        $('#container-test').fadeIn("slow");
                        $('#container-test').css('display', 'block');

                        $.ajax({
                            url: "{{ route('maintenance.branch_maintenance.update') }}",
                            type: "post",
                            data: form_data,
                            contentType: false,
                            processData: false,
                            cache: false,
                            success: function(data) {
                                window.location.reload();
                            }
                        });
                    }, 1000);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    text: 'Invalid or mismatched security code.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                });
            }
        });
    });
</script>

{{-- @include('script.scripts') --}}
