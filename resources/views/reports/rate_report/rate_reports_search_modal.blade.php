<div class="modal-header px-4">
    <div class="row">
        <h4 class="modal-title" id="exampleModalLabel">{{ trans('labels.selling_trans_avlbl_bills_modal') }} <span id="currency-name"></span> </h4>
    </div>
    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
</div>

<div class="modal-body px-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="row align-items-center">
                @csrf
                <div class="d-flex align-items-center">
                    <div class="row">
                        <div class="row align-items-center">
                            <div class="col-4">
                                <span>
                                    <strong>
                                        {{ trans('labels.rate_reports_search_date_from') }}: &nbsp;<span class="required-class">*</span>
                                    </strong>
                                </span>
                            </div>
                            <div class="col-8">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search-rate-date-from" name="search-rate-date-from" placeholder="Select date from (yyyy-mm-dd)">
                                    <span class="input-group-text bg-slate-950"><i class='bx bx-calendar'></i></span>
                                </div>
                            </div>

                            <div class="col-4">
                                <span>
                                    <strong>
                                        {{ trans('labels.rate_reports_search_date_to') }}: &nbsp;<span class="required-class">*</span>
                                    </strong>
                                </span>
                            </div>
                            <div class="col-8">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search-rate-date-to" name="search-rate-date-to" placeholder="Select date to (yyyy-mm-dd)">
                                    <span class="input-group-text bg-slate-950"><i class='bx bx-calendar'></i></span>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row align-items-center">
                            <div class="col-4">
                                <span>
                                    <strong>
                                        {{ trans('labels.rate_reports_search_date_to') }}: &nbsp;<span class="required-class">*</span>
                                    </strong>
                                </span>
                            </div>
                            <div class="col-8">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search-rate-date-to" name="search-rate-date-to" placeholder="Select date to (yyyy-mm-dd)">
                                    <span class="input-group-text bg-slate-950"><i class='bx bx-calendar'></i></span>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    <button class="btn btn-primary" id="button-search-rate-report" type="button">Apply Search</button>
</div>
