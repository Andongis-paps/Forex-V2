<!-- Modal -->
    <div class="modal-header px-4">
        <div class="row">
            <span class="modal-title text-xl font-bold" id="exampleModalLabel">Update FC Form Series</span>
        </div>
    </div>

    <div class="modal-body px-4">
        <div class="row">
            <div class="col-lg-12">
                <form class="form m-0" action="{{ route('maintenance.bill_tags.update') }}" enctype="multipart/form-data" method="POST" id="update-bill-tag-form">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-12">
                            <label class="mb-2" for="description">
                                <strong>
                                    {{ trans('labels.w_bill_tags_mainte_tag_descr') }}:&nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>

                            <input type="text" name="tag_description" id="tag-description" class="form-control" value="{{ $bill_tag_details[0]->BillStatus }}">
                        </div>
                    </div>
                    <input type="hidden" id="BillStatID" value="{{ $bill_tag_details[0]->BillStatID }}">
                </form>
            </div>

            <div class="col-lg-12 px-3 my-2">
                <hr>
            </div>

            <div class="col-lg-12 px-3">
                <div class="row justify-content-center align-items-center">
                    <div class="col-8">
                        <input class="form-control" step="any" autocomplete="false" id="update-bill-tag-security-code" type="password">
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
        <button type="button" class="btn btn-primary bill-tag-update-button" id="bill-tag-update-button">Update</button>
    </div>

<script>
    $(document).ready(function() {
        $('#update-bill-tag-form').validate({
            rules: {
                tag_description: 'required',
            },
            messages: {
                tag_description: 'Tag description is required.',
            },
            // submitHandler: function(form) {
            //     form.submit();
            // }
        });

        $('#bill-tag-update-button').click(function() {
            var user_sec_onpage = $('#update-bill-tag-security-code').val();

            if (sec_code_array.includes(user_sec_onpage)) {
                $('#proceed-transaction').prop('disabled', true);

                var index = sec_code_array.indexOf(user_sec_onpage);
                var matched_user_id = user_id_array[index];

                var form_data = new FormData($('#update-bill-tag-form')[0]);
                form_data.append('matched_user_id', matched_user_id);
                form_data.append('BillStatID', $('#BillStatID').val());

                $.ajax({
                    url: "{{ route('maintenance.bill_tags.update') }}",
                    type: "post",
                    data: form_data,
                    contentType: false,
                    processData: false,
                    cache: false,
                    success: function(data) {
                        loader();


                        Swal.fire({
                            title: 'Success!',
                            text: 'Bill Tag Updated!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            loaderOut();

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
