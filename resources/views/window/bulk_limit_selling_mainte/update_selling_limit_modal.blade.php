<!-- Modal -->
    <div class="modal-header px-4">
        <div class="row">
            <span class="modal-title text-xl font-bold" id="exampleModalLabel">Update Selling Limit</span>
        </div>
    </div>

    <div class="modal-body px-4">
        <div class="row">
            <div class="col-lg-12">
                <form class="form m-0" action="{{ route('maintenance.bulk_limit.update') }}" enctype="multipart/form-data" method="POST" id="update-bulk-limit-form">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-12">
                            <label class="mb-2" for="description">
                                <strong>
                                    {{ trans('labels.w_series_mainte_company') }}: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>

                            <select class="form-select" name="company_id" id="company-id">
                                <option>Select a company</option>
                                @foreach ($result['company'] as $company)
                                    <option value="{{ $company->CompanyID }}" @if ($company->CompanyID == $selling_limit_details[0]->CompanyID) selected @endif>{{ $company->CompanyName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-12">
                            <label class="my-2" for="description">
                                <strong>
                                    Limit:&nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>

                            <input type="number" name="selling_limit" id="selling-limit" class="form-control" value="{{ $selling_limit_details[0]->Limit }}">
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
                            <div class="row">
                                <label class="switch switch-success switch-square">
                                    <input type="checkbox" class="switch-input" @if ($selling_limit_details[0]->Active == 1) checked @else  @endif>
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"></span>
                                        <span class="switch-off"></span>
                                    </span>
                                    <span class="switch-label cursor-pointer">
                                        @if ($selling_limit_details[0]->Active == 1)
                                            <strong>
                                                Limited
                                            </strong>
                                        @else
                                            <strong>
                                                No Limit
                                            </strong>
                                        @endif
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="SLID" value="{{ $selling_limit_details[0]->SLID }}">
                </form>
            </div>

            <div class="col-lg-12 px-3 mb-2">
                <hr>
            </div>

            <div class="col-lg-12 px-3">
                <div class="row justify-content-center align-items-center">
                    <div class="col-8">
                        <input class="form-control" step="any" autocomplete="false" id="update-selling-limit-security-code" type="password">
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
        <button type="button" class="btn btn-primary selling-limit-update-button" id="selling-limit-update-button">Update</button>
    </div>

<script>
    $(document).ready(function() {
        $('.switch-input').change(function() {
            if ($(this).is(':checked')) {
                $(this).attr('checked', 'checked');
                $(this).siblings('.switch-label').fadeOut(100, function() {
                    $(this).empty().html('<strong>Limited</strong>').fadeIn(100);
                });
            } else {
                $(this).removeAttr('checked');
                $(this).siblings('.switch-label').fadeOut(100, function() {
                    $(this).empty().html('<strong>No Limit</strong>').fadeIn(100);
                });
            }
        });

        $('input[name="selling_limit"]').on('input', function() {
            this.value = this.value.replace(/[^a-zA-Z0-9]/g, '');

            if (this.value.length > 11) {
                this.value = this.value.slice(0, 11);
            }
        });

        $('#update-bulk-limit-form').validate({
            rules: {
                selling_limit: 'required',
            },
            messages: {
                selling_limit: 'Field is required.',
            },
            // submitHandler: function(form) {
            //     form.submit();
            // }
        });

        $('#selling-limit-update-button').click(function() {
            var user_sec_onpage = $('#update-selling-limit-security-code').val();

            if (sec_code_array.includes(user_sec_onpage)) {
                $('#selling-limit-update-button').prop('disabled', true);

                var index = sec_code_array.indexOf(user_sec_onpage);
                var matched_user_id = user_id_array[index];

                var form_data = new FormData($('#update-bulk-limit-form')[0]);
                form_data.append('matched_user_id', matched_user_id);
                form_data.append('SLID', $('#SLID').val());
                form_data.append('status', $('.switch-input').is(':checked'));

                $.ajax({
                    url: "{{ route('maintenance.bulk_limit.update') }}",
                    type: "post",
                    data: form_data,
                    contentType: false,
                    processData: false,
                    cache: false,
                    success: function(data) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Selling Limit Updated!',
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
