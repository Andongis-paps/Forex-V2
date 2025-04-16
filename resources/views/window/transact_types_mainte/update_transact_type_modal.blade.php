<!-- Modal -->
    <div class="modal-header px-4">
        <div class="row">
            <span class="modal-title text-xl font-bold" id="exampleModalLabel">Update Transaction Type</span>
        </div>
    </div>

    <div class="modal-body px-4">
        <div class="row">
            <div class="col-lg-12">
                <form class="form m-0" action="{{ route('maintenance.transaction_types.update') }}" enctype="multipart/form-data" method="POST" id="update-trans-type-form">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-12">
                            <label class="mb-2" for="description">
                                <strong>
                                    {{ trans('labels.w_bill_tags_mainte_tag_descr') }}:&nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>

                            <input type="text" name="transact_type" id="transact-type" class="form-control" value="{{ $transact_type_details[0]->TransType }}">
                        </div>
                    </div>

                    <div class="row align-items-center mt-2">
                        <div class="col-12">
                            <label class="mb-2" for="description">
                                <strong>
                                    Status:
                                </strong>
                            </label>
                        </div>

                        <div class="col-12 text-left">
                            <label class="switch switch-success switch-square">
                                <input type="checkbox" class="switch-input" @if ($transact_type_details[0]->Active == 1) checked @else  @endif>
                                <span class="switch-toggle-slider">
                                    <span class="switch-on"></span>
                                    <span class="switch-off"></span>
                                </span>
                                <span class="switch-label cursor-pointer">
                                    @if ($transact_type_details[0]->Active == 1)
                                        <strong>
                                            Active
                                        </strong>
                                    @else
                                        <strong>
                                            Inactive
                                        </strong>
                                    @endif
                                </span>
                            </label>
                        </div>
                    </div>
                    <input type="hidden" id="TTID" value="{{ $transact_type_details[0]->TTID }}">
                </form>
            </div>

            <div class="col-lg-12 px-3 mb-2">
                <hr>
            </div>

            <div class="col-lg-12 px-3">
                <div class="row justify-content-center align-items-center">
                    <div class="col-8">
                        <input class="form-control" step="any" autocomplete="false" id="update-trans-type-security-code" type="password">
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
        <button type="button" class="btn btn-primary trans-type-update-button" id="trans-type-update-button">Update</button>
    </div>

<script>
    $(document).ready(function() {
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

        $('#update-trans-type-form').validate({
            rules: {
                transact_type: 'required',
            },
            messages: {
                transact_type: 'Transaction type is required.',
            },
            // submitHandler: function(form) {
            //     form.submit();
            // }
        });

        $('#trans-type-update-button').click(function() {
            var user_sec_onpage = $('#update-trans-type-security-code').val();


            if (sec_code_array.includes(user_sec_onpage)) {
                $('#trans-type-update-button').prop('disabled', true);

                var index = sec_code_array.indexOf(user_sec_onpage);
                var matched_user_id = user_id_array[index];

                var form_data = new FormData($('#update-trans-type-form')[0]);
                form_data.append('matched_user_id', matched_user_id);
                form_data.append('TTID', $('#TTID').val());
                form_data.append('status', $('.switch-input').is(':checked'));

                $.ajax({
                    url: "{{ route('maintenance.transaction_types.update') }}",
                    type: "post",
                    data: form_data,
                    contentType: false,
                    processData: false,
                    cache: false,
                    success: function(data) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Transaction Type Updated!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            loader();

                            setTimeout(() => {
                                loader();
                                window.location.reload();
                            }, 1000);
                        });
                    }
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

        function loader() {
            $('#container-test').fadeIn("slow");
            $('#container-test').css('display', 'block');
        }

        function loaderOut() {
            $('#container-test').fadeOut("slow");
        }
    });
</script>
