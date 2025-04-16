@extends('template.layout')
@section('content')

   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-8">
                        <div class="row">
                            <div class="col-12">
                                <hr>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    {{ trans('labels.buff_financing_add_buff') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 border border-gray-300">
                                        <div class="row mt-3 px-3 align-items-center">
                                            <div class="col-4">
                                                <strong>
                                                    <span class="font-bold">
                                                        {{ trans('labels.buff_financing_entry_date') }}: &nbsp;<span class="required-class">*</span>
                                                    </span>
                                                </strong>
                                            </div>
                                            <div class="col-8">
                                                <input class="form-control" name="buffer-finance-entry-date" type="text">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row">
                                            <div class="col-lg-6">

                                            </div>
                                            <div class="col-lg-6 text-end">
                                                <a class="btn btn-secondary btn-sm" type="button" href="{{ route('admin_transactions.buffer.buffer_financing') }}">{{ trans('labels.back_action') }}</a>
                                                <button class="btn btn-primary btn-sm" type="button" id="transaction-confirm-button" disabled>{{ trans('labels.confirm_action') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
