<!-- Modal -->
    <div class="modal-header px-4">
        <div class="row">
            <span class="modal-title text-xl font-bold" id="exampleModalLabel">Add New FC Form Series</span>
        </div>
    </div>

    <div class="modal-body px-4">
        <div class="row">
            <div class="col-lg-12">
                <form class="form m-0" action="{{ route('maintenance.form_series.add') }}" enctype="multipart/form-data" method="POST" id="add-r-set-series-form">
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
                                    <option value="{{ $company->CompanyID }}" >{{ $company->CompanyName }}</option>
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
                                    <input type="radio" class="btn-check" name="radio-rset" id="r-set-o" value="{{ trans('labels.buying_rset_o') }}" @if(session('time_toggle_status') == 1) @endif disabled="true">
                                    <label class="btn btn-outline-primary" for="r-set-o">
                                        <strong>{{ trans('labels.buying_rset_o') }}</strong>
                                    </label>

                                    <input type="radio" class="btn-check" name="radio-rset" id="r-set-b" value="{{ trans('labels.buying_rset_b') }}" disabled="true">
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
                            <input type="number" name="fc_form_series" id="fc-form-series" class="form-control" step="any" value="000000" required disabled>
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
                        <input class="form-control" step="any" autocomplete="false" id="add-r-series-security-code" type="password">
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
            <button type="button" class="btn btn-primary" id="r-series-add-button">Add</button>
        @endcan
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
    });
</script>
