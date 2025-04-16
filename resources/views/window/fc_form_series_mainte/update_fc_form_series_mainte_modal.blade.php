<!-- Modal -->
    <div class="modal-header px-4">
        <div class="row">
            <span class="modal-title text-xl font-bold" id="exampleModalLabel">Update FC Form Series</span>
        </div>
    </div>

    <div class="modal-body px-4">
        <div class="row">
            <div class="col-lg-12">
                <form class="form m-0" action="{{ route('maintenance.form_series.update') }}" enctype="multipart/form-data" method="POST" id="update-r-series-form">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-4">
                            <label for="description">
                                <strong>
                                    {{ trans('labels.w_series_mainte_company') }}: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
                        </div>
                        <div class="col-8">
                            <select class="form-select" name="company_id" id="company-id">
                                <option>Select a company</option>
                                @foreach ($result['company'] as $company)
                                    <option value="{{ $company->CompanyID }}" @if ($company->CompanyID == $FCF_series_details[0]->CompanyID) selected @endif>{{ $company->CompanyName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-4">
                            <label for="description">
                                <strong>
                                    {{ trans('labels.w_series_mainte_r_set') }}: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
                        </div>
                        <div class="col-8">
                            <div class="row">
                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                    <input type="radio" class="btn-check" name="radio-rset" id="r-set-o" value="{{ trans('labels.buying_rset_o') }}" @if(session('time_toggle_status') == 1) @endif @if ($FCF_series_details[0]->RSet == 'O') checked @endif>
                                    <label class="btn btn-outline-primary" for="r-set-o">
                                        <strong>{{ trans('labels.buying_rset_o') }}</strong>
                                    </label>

                                    <input type="radio" class="btn-check" name="radio-rset" id="r-set-b" value="{{ trans('labels.buying_rset_b') }}" @if ($FCF_series_details[0]->RSet == 'B') checked @endif>
                                    <label class="btn btn-outline-primary" for="r-set-b">
                                        <strong>{{ trans('labels.buying_rset_b') }}</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center mt-3">
                        <div class="col-5">
                            <label for="description">
                                <strong>
                                    {{ trans('labels.w_series_mainte_series') }}:&nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>
                        </div>
                        <div class="col-7">
                            <input type="number" name="fc_form_series" id="fc-form-series" class="form-control" step="any" value="{{ old('fc_form_series') ?? $FCF_series_details[0]->FormSeries }}" required>
                        </div>
                    </div>
                    <input type="hidden" id="FCFSID" value="{{ $FCF_series_details[0]->FCFSID }}">
                </form>
            </div>

            <div class="col-lg-12 px-3 my-2">
                <hr>
            </div>

            <div class="col-lg-12 px-3">
                <div class="row justify-content-center align-items-center">
                    <div class="col-8">
                        <input class="form-control" step="any" autocomplete="false" id="update-r-series-security-code" type="password">
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
        <button type="button" class="btn btn-primary r-series-update-button" id="r-series-update-button">Update</button>
    </div>

<script>
    $(document).ready(function() {
        $('#add-r-series-form').validate({
            rules: {
                company_id: 'required',
                receipt_series: {
                    required: true,
                    // pattern: /^[A-Z0-9\s]*(?<![\[{}\]])[A-Z0-9\s]+(?![\]{}\]])$/
                },
            },
            messages: {
                company_id: 'Select a company.',
                receipt_series: {
                    required: "Enter a series for the company's receipt. ",
                },
            },
            // submitHandler: function(form) {
            //     form.submit();
            // }
        });

        $('#r-series-update-button').click(function() {
            var user_sec_onpage = $('#update-r-series-security-code').val();

            $('#proceed-transaction').prop('disabled', true);

            if (sec_code_array.includes(user_sec_onpage)) {
                $('#proceed-transaction').prop('disabled', true);

                var index = sec_code_array.indexOf(user_sec_onpage);
                var matched_user_id = user_id_array[index];

                var form_data = new FormData($('#update-r-series-form')[0]);
                form_data.append('matched_user_id', matched_user_id);
                form_data.append('FCFSID', $('#FCFSID').val());

                loader();

                $.ajax({
                    url: "{{ route('maintenance.form_series.update') }}",
                    type: "post",
                    data: form_data,
                    contentType: false,
                    processData: false,
                    cache: false,
                    success: function(data) {
                        loaderOut();

                        Swal.fire({
                            title: 'Success!',
                            text: 'Form Series Updated!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            loader();

                            setTimeout(() => {
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
                })then(() => {
                    $('#proceed-transaction').prop('disabled', false);
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

    $(document).on('load', function() {
        $('#fc-form-series').on('input', function() {
            var input_val = $(this).val().replace(/\D/g, '');

            if (input_val.length > 6) {
                input_val = input_val.slice(-6);
            }

            var formatted_series = input_val.padStart(6, '0');
            $(this).val(formatted_series);
        });
    });
</script>
