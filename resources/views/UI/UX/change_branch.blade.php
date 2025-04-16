{{-- Modal - Confirm using security code --}}
<div class="modal fade" id="chng-branch-security-code-modal" tabindex="-1" aria-labelledby="buying-transact" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header px-4">
                <strong>
                    <span class="text-lg font-bold">
                        Change Branch
                    </span>
                </strong>
            </div>
            <div class="modal-body pt-2 pb-3">
                <div class="row">
                    <form class="p-0 mb-0" method="post" action="{{ route('change_branch') }}" id="change-branch-form" enctype="multipart/form-data">
                        @csrf
                        <div class="col-12 px-3">
                            <label class="mb-2" for="description">
                                <strong>
                                    Branch:
                                </strong>
                            </label>

                            <select class="form-select" name="branch-list" id="branch-list">
                                {{-- <option value="default">Select branch</option> --}}
                                @forelse (Auth::user()->branchList() as $branch_list)
                                    <option value="{{ $branch_list->BranchCode }}" @if (Auth::user()->BranchCode == $branch_list->BranchCode ) selected @endif>{{ $branch_list->BranchCode }}</option>
                                @empty
                                    <option value="0">No branch available</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="col-lg-12 px-3">
                            <hr class="mb-3">
                        </div>

                        <div class="col-lg-12">
                            <div class="row justify-content-center align-items-center">
                                <div class="col-12 text-center mb-2">
                                    <label for="description">
                                        <strong>
                                            {{ trans('labels.enter_security_code') }}: &nbsp;
                                        </strong>
                                    </label>
                                </div>

                                <div class="col-10">
                                    <input class="form-control" step="any" autocomplete="false" id="change-branch-sec-code" type="password">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('labels.cancel_action') }}</button>
                <button type="button" class="btn btn-primary" id="proceed-change-branch">{{ trans('labels.proceed_action') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#chng-branch-security-code-modal').on('shown.bs.modal', function () {
            $('#change-branch-sec-code').focus();
        });
    });

    // Change branch scripts
    var user_id_array = [];
    var sec_code_array = [];

    $(document).ready(function() {
        $.ajax({
            url: "{{ route('user_info') }}",
            type: "GET",
            data: {
                _token: "{{ csrf_token() }}",
            },
            success: function(get_user_info) {
                var user_info = get_user_info.security_codes;

                user_info.forEach(function(gar) {
                    sec_code_array.push(gar.SecurityCode);
                    user_id_array.push(gar.UserID);
                });
            }
        });
    });

    $(document).ready(function() {
        $('#proceed-change-branch').click(function() {
            var branch_code = $('#branch-list').val();
            var user_sec_onpage = $('#change-branch-sec-code').val();

            $('#proceed-change-branch').prop('disabled', true);

            if (sec_code_array.includes(user_sec_onpage)) {
                $('#proceed-change-branch').prop('disabled', true);

                var index = sec_code_array.indexOf(user_sec_onpage);
                var matched_user_id = user_id_array[index];

                Swal.fire({
                    title: 'Success!',
                    text: 'Branch changed!',
                    icon: 'success',
                    timer: 900,
                    showConfirmButton: false
                }).then(() => {
                    $('#container-test').fadeIn("slow");
                    $('#container-test').css('display', 'block');

                    var form_data = new FormData($('#change-branch-form')[0]);
                    form_data.append('branch_code', branch_code);
                    form_data.append('matched_user_id', matched_user_id);

                    setTimeout(() => {
                        $.ajax({
                            url: "{{ route('change_branch') }}",
                            type: "post",
                            data: form_data,
                            contentType: false,
                            processData: false,
                            cache: false,
                            success: function(data) {
                                window.location.reload();
                            }
                        });
                    }, 200);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    text: 'Invalid or mismatched security code.',
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                }).then(() => {
                    $('#proceed-change-branch').prop('disabled', false);
                });
            }
        });
    });
</script>
