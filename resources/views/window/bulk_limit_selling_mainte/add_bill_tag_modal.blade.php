<!-- Modal -->
    <div class="modal-header px-4">
        <div class="row">
            <span class="modal-title text-xl font-bold" id="exampleModalLabel">Add New Selling Limit</span>
        </div>
    </div>

    <div class="modal-body px-4">
        <div class="row">
            <div class="col-lg-12">
                <form class="form m-0" action="{{ route('maintenance.bulk_limit.add') }}" enctype="multipart/form-data" method="POST" id="add-selling-limit-form">
                    @csrf

                    <div class="row align-items-center">
                        <div class="col-12">
                            <label class="mb-2" for="company_id">
                                <strong>
                                    {{ trans('labels.w_series_mainte_company') }}: &nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>

                            <select class="form-select" name="company_id" id="company-id">
                                <option>Select a company</option>
                                @foreach ($result['company'] as $company)
                                    <option value="{{ $company->CompanyID }}" >{{ $company->CompanyName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-12">
                            <label class="my-2" for="description">
                                <strong>
                                    {{ trans('labels.w_sell_limit_mainte_descr') }}:&nbsp;<span class="required-class">*</span>
                                </strong>
                            </label>

                            <input type="text" name="selling_limit" id="selling-limit" class="form-control">
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
                        <input class="form-control" step="any" autocomplete="false" id="add-selling-limit-security-code" type="password">
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
            <button type="button" class="btn btn-primary" id="selling-limit-add-button">Add</button>
        @endcan
    </div>

<script>
    $(document).ready(function() {
        $('#add-selling-limit-form').validate({
            rules: {
                selling_limit: 'required',
            },
            messages: {
                selling_limit: 'Field is required.',
            },
        });
    });
</script>
