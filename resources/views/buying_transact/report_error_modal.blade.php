<div class="modal fade" id="report-error-modal" tabindex="-1" aria-hidden="true" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <input type="hidden" id="ID" value="0">
            <input type="hidden" id="menu-id" value="0">

            <div class="modal-header py-2">
                <span class="text-lg font-bold ms-2">
                    <i class="bx bx-comment-error me-1"></i> Create Ticket
                </span>
            </div>

            <div class="modal-body px-4 py-2">
                <form class="mb-0" method="POST" id="ticketSubmition">
                    <div class="col-12">
                        @csrf
                        <div class="col-12">
                            <label for="sconcernid mb-2" class="form-label "><strong>Concern:</strong><span class="required-class text-sm">*</span></label>
                            <select class="form-select sconcernid" name="sconcernid" id="sconcernid">
                                <option value="">Select Concern</option>
                            </select>
                        </div>

                        <div class="col-12 mt-2">
                            <label for="concern-attachement mb-2" class="form-label "><strong>Attachment:</strong></label>
                            <input type="file" class="form-control concern-attachement" name="concern_attachement" accept="image/jpeg, image/png, image/jpg, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                        </div>

                        <div class="col-12 mt-2">
                            <label for="Remarks" class="form-label"><strong>Remarks:</strong><span class="required-class text-sm">*</span></label>
                            <textarea type="text" id="Remarks"name="Remarks" class="form-control" placeholder="Remarks" autocomplete="off" rows="5"></textarea>
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
            
                                    <input class="form-control" step="any" autocomplete="false" id="report-sec-code" type="password">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer p-1">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-sm" id="submit-ticket">Submit ticket</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#submit-ticket').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#report-sec-code').val();

            $('#submit-ticket').prop('disabled', true);

            if (!$('#sconcernid').val()) {
                Swal.fire({
                    icon: 'error',
                    html: `<span class="text-sm text-black">Concern is required.</span>`,
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                }).then(() => {
                    $('#submit-ticket').prop('disabled', false);
                });
            } else if (!$('#Remarks').val()) {
                Swal.fire({
                    icon: 'error',
                    html: `<span class="text-sm text-black">Remarks is required.</span>`,
                    customClass: {
                        popup: 'my-swal-popup',
                    }
                }).then(() => {
                    $('#submit-ticket').prop('disabled', false);
                });
            } else {
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

                        if (sec_code_array.includes(user_sec_onpage)) {
                            $('#submit-ticket').prop('disabled', true);

                            var index = sec_code_array.indexOf(user_sec_onpage);
                            var matched_user_id = user_id_array[index];

                            Swal.fire({
                                title: 'Success!',
                                text: 'Report sent!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                $('#container-test').fadeIn("slow");
                                $('#container-test').css('display', 'block');

                                var form_data = new FormData($('#ticketSubmition')[0]);
                                form_data.append('matched_user_id', matched_user_id);
                                form_data.append('ID', $('#ID').val());
                                form_data.append('menu_id', $('#menu-id').val());

                                $.ajax({
                                    url: "{{ route('send_report_error') }}",
                                    type: "post",
                                    data: form_data,
                                    contentType: false,
                                    processData: false,
                                    cache: false,
                                    success: function(data) {
                                        window.location.reload();
                                    }
                                });
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                text: 'Invalid or mismatched security code.',
                                customClass: {
                                    popup: 'my-swal-popup',
                                }
                            }).then(() => {
                                $('#submit-ticket').prop('disabled', false);
                            });
                        }
                    }
                });
            }
        });
    });
</script>
