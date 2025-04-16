<div class="modal fade" id="currency-manual-modal" tabindex="-1" aria-labelledby="currency-manual-modal" aria-hidden="true">
    <div class="modal-dialog custom- modal-lg modal-dialog-scrollable">
        <div class="modal-content currency-details-modal">
            <div class="modal-header py-2 px-4">
                <i class='bx bxs-grid'></i><span class="text-lg font-bold">{{ trans('labels.buying_currency_manual') }}</span><i class='bx bxs-info-circle' id="bxs-info"></i>
            </div>
            <div class="modal-body px-4">
                <div id="currency-container">
                    <div class="row row-currency text-center" id="row-currency">
                        <div class="swiper currency-swiper-main mb-0" style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff">
                            <div class="swiper-wrapper text-center" id="currency-swiper-main-wrapper">

                            </div>

                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                            {{-- <div class="swiper-pagination"></div> --}}
                        </div>

                        <div class="swiper currency-swiper-thumb" thumbsSlider="">
                            <div class="swiper-wrapper text-center" id="currency-swiper-thumb-wrapper">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
