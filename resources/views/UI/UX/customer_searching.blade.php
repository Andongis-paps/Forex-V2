{{-- Modal - Get customer name --}}
<div class="modal fade" id="customerDeetsModal" tabindex="-1" aria-labelledby="customerDeetsModal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header ps-3 py-2">
                <span class="text-lg">
                    <i class='bx bx-check-square bx-sm pb-1'></i>&nbsp;<strong>{{ trans('labels.customer_details') }}</strong>
                </span>
            </div>

            <div class="modal-body pb-2">
                <div class="row px-2 align-items-center justify-content-center">
                    <div class="col-12">
                        <div class="row align-items-center justify-content-center p-0">
                            <div class="col-8 text-center">
                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                    <input type="radio" class="btn-check" name="customer-search-filter" id="customer-search-filter-1" value="1" checked>
                                    <label class="btn btn-outline-primary btn-sm" for="customer-search-filter-1">
                                        <strong>{{ trans('labels.customer_search_name') }}</strong>
                                    </label>

                                    <input type="radio" class="btn-check" name="customer-search-filter" id="customer-search-filter-2" value="2">
                                    <label class="btn btn-outline-primary btn-sm" for="customer-search-filter-2">
                                        <strong>{{ trans('labels.customer_search_number') }}</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-3 mt-3" id="c-searching-f-name">
                        <label class="mb-1" for="customer-search-f-name">
                            <strong><span class="text-sm">{{ trans('labels.customer_search_f_name') }}</span>: </strong>
                        </label>

                        <input class="form-control" type="text" id="customer-search-f-name" name="customer-search-f-name" placeholder="First Name">
                    </div>

                    <div class="col-3 mt-3" id="c-searching-m-name">
                        <label class="mb-1" for="customer-search-m-name">
                            <strong><span class="text-sm">{{ trans('labels.customer_search_m_name') }}</span>: </strong>
                        </label>

                        <input class="form-control" type="text" id="customer-search-m-name" name="customer-search-m-name" placeholder="Middle Name">
                    </div>

                    <div class="col-3 mt-3" id="c-searching-l-name">
                        <label class="mb-1" for="customer-search-l-name">
                            <strong><span class="text-sm">{{ trans('labels.customer_search_l_name') }}</span>: </strong>
                        </label>

                        <input class="form-control" type="text" id="customer-search-l-name" name="customer-search-l-name" placeholder="Last Name">
                    </div>

                    <div class="col-3 mt-3" id="c-searching-birth-date">
                        <label class="mb-1" for="customer-birth-date">
                            <strong><span class="text-sm">{{ trans('labels.customer_search_b_date') }}</span>: <span class="required-class">*</span></strong>
                        </label>

                        <input class="form-control" type="text" id="customer-birth-date" name="customer-birth-date mt-1" placeholder="YYYY-MM-DD">
                    </div>

                    <div class="col-5 mt-3 d-none text-center" id="c-searching-customer-number">
                        <label class="mb-2" for="customer-search-number">
                            <strong><span class="text-sm">{{ trans('labels.customer_search_number') }}</span>: <span class="required-class">*</span></strong>
                        </label>

                        <input class="form-control" type="number" id="customer-search-number" name="customer-search-number" placeholder="Customer Number">
                    </div>

                    <div class="col-12 mt-2" id="c-searching-label">
                        <span class="text-muted"><small><i class="bx bx-info-circle me-2"></i>Note: Search formats (Two of a Name + Birthday).</small></span>
                    </div>

                    <div class="col-12 mt-3 text-center">
                        <div class="row justify-content-center px-3">
                            <div class="col-3">
                                <div class="row">
                                    <button class="btn btn-primary btn-sm" id="customer-search-button" type="button">
                                        {{ trans('labels.rate_reports_search') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div id="sanctions-container"></div>

                        <div class="swiper test-swiper" id="test-swiper">
                            <div class="swiper-wrapper pb-2" id="customer_images">

                            </div>

                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                    </div>

                    <div class="col-1">
                        <div class="row">
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-12 text-end" id="customer-modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" id="customer-search-close-modal" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <script>
    $(document).ready(function() {
        var swiper = new Swiper(".mySwiper", {
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            pagination: {
                el: ".swiper-pagination",
                type: "fraction",
            },
            centeredSlides: true,
            keyboard: true,
            grabCursor: true,
            zoom: true,
        });
    });
</script> --}}
