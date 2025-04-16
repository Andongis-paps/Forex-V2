@extends('template.layout')
@section('content')

   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <!-- Content -->
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-lg-4 mb-4">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                @if(session()->has('message') && session()->has('latest_ftdid'))
                                    <div class="alert alert-success alert-dismissible" role="alert" id="success-message-saving-success" data-successexistence="1" data-recenftdid="{{ session()->get('latest_ftdid') }}">
                                        {{ session()->get('message') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                @endif

                                <div class="col-12">
                                    <div class="card p-0" id="new-buying-transaction-header">
                                        <div class="card-body p-3">
                                            <span class="text-lg font-semibold p-2 text-white">
                                                {{ trans('labels.w_currency_edit_denom') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                            </div>

                            <form class="m-0" action="" method="post" id="denomination-form">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                                <div class="row">
                                                    <div class="col-7 ps-3">
                                                        <span class="text-lg font-semibold text-black">
                                                            {{ trans('labels.w_currency_curr_denom') }}
                                                        </span>
                                                    </div>
                                                    <div class="col-5 text-end">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 p-2 border border-gray-300 border-r border-l">
                                                <div class="row px-2 py-0 mt-1">
                                                    <div class="col-12 mb-3">
                                                        <div class="row align-items-center">
                                                            <div class="col-3 ps-3">
                                                                <strong>
                                                                    <span>{{ trans('labels.w_denom_mainte_curr') }}:</span>
                                                                </strong>
                                                            </div>
                                                            <div class="col-9">
                                                                <input class="form-control" type="text" value="{{ Str::title($result['currency']->Currency) }}" disabled>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <div class="row px-3">
                                                            <button class="btn btn-primary button-add-denom" id="button-add-denom" type="button">Add Denom &nbsp; <i class="bx bx-plus"></i></button>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="col-12">
                                                            <table class="table table-hover table-bordered" id="currency-denominatiom-table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-th-buying text-center text-xl font-extrabold text-black py-1 px-1">{{ trans('labels.w_currency_denom') }}</th>
                                                                        <th class="text-th-buying text-center text-xl font-extrabold text-black py-1 px-1">{{ trans('labels.w_denom_mainte_trans_type') }}</th>
                                                                        <th class="text-th-buying text-center text-xl font-extrabold text-black py-1 px-1">{{ trans('labels.action_data') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="curr-denom-table-new-denom-container">
                                                                    @foreach ($result['denominations'] as $denominations)
                                                                        <tr>
                                                                            <td class="text-center">
                                                                                <input class="form-control" name="denominations" type="text" value="{{ $denominations->BillAmount }}">
                                                                            </td>
                                                                            <td class="text-center text-sm text-black">
                                                                                {{ $denominations->TransType }}
                                                                            </td>
                                                                            <td class="text-center">
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr>
                                                                        <td colspan="3"></td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>

                                                            {{-- <div class="col-12 mt-3">
                                                                <div class="row px-3">
                                                                    <button class="btn btn-secondary button-add-denom" id="button-add-denom" type="button">Add Denomination &nbsp; <i class="bx bx-plus"></i></button>
                                                                </div>
                                                            </div> --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-span-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                                <div class="row">
                                                    <div class="col-lg-6 offset-6 text-end">
                                                        <a class="btn btn-secondary" type="button" href="{{ route('denominations') }}">{{ trans('labels.back_action') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Modal - Confirm using security code --}}
                                    <div class="modal fade" id="buyingTransactModal" tabindex="-1" aria-labelledby="buying-transact" aria-hidden="true">
                                        <div class="modal-dialog modal-sm">
                                            <div class="modal-content">
                                                <div class="modal-header px-4">
                                                    <h4 class="modal-title" id="buying-transact">{{ trans('labels.buying_save_action') }}</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row px-2">
                                                        <div class="col-12 mb-2 mt-2">
                                                            <span>
                                                                <strong>
                                                                    {{ trans('labels.buying_enter_sec_code') }}: &nbsp;<span class="required-class">*</span>
                                                                </strong>
                                                            </span>
                                                        </div>
                                                        <div class="col-12 mb-3">
                                                            <input class="form-control password" id="security_code" name="security_code">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('labels.cancel_action') }}</button>
                                                    <button type="button" class="btn btn-primary" id="save-buying-transaction">{{ trans('labels.proceed_action') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    {{-- <div class="modal fade" id="currency-manual-modal" tabindex="-1" aria-labelledby="currency-manual-modal" aria-hidden="true">
        <div class="modal-dialog custom-modal-lg modal-lg modal-dialog-scrollable">
            <div class="modal-content currency-details-modal">
                <div class="modal-header ps-4 pe-4">
                    <h4 class="modal-title" id="currency-manual-modal">{{ trans('labels.buying_currency_manual') }}</h4>
                    <i class='bx bxs-info-circle' id="bxs-info"></i>
                </div>
                <div class="modal-body px-4">
                    <div id="currency-container">
                        <div class="row row-currency text-center" id="row-currency">
                            <div class="swiper currency-swiper-main mb-0" style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff">
                                <div class="swiper-wrapper text-center" id="currency-swiper-main-wrapper">

                                </div>

                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
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
    </div> --}}

@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
