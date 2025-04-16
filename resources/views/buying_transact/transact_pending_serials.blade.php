@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-lg-12 mb-4">
                        <div class="col-12">
                            @if($errors->any())
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    @foreach($errors->all() as $error)
                                        <span class="error-message error-bottom"> {{ $error }} </span>
                                    @endforeach
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if(session()->has('message'))
                                <div class="alert alert-success alert-dismissible d-none" role="alert" id="success-message-saving-pending" data-successpending="1">
                                    {{ session()->get('message') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                        </div>

                        <div class="col-12">
                            <hr>
                        </div>

                        <div class="card mb-3">
                            <div class="col-12 p-2 border border-gray-300 rounded-tr rounded-tl">
                                <span class="text-lg font-bold p-2 text-black">
                                    <i class='bx bx-list-ul'></i>&nbsp;{{ trans('labels.add_pending_serials_title') }}
                                </span>
                            </div>

                            <div class="col-12">
                                <form class="m-0" method="post" id="pending-serials-form">
                                    @csrf

                                    @php
                                        $count = count($result['pending_serials']);
                                    @endphp

                                    <div @if ($count <= 15) id="a-pending-few" @else id="a-pending-lot" @endif>
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_currency') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_trans_type') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_bill_amount') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_serials') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (count($result['pending_serials']) > 0)
                                                    @foreach ($result['pending_serials'] as $pending_serials)
                                                        <div id="transaction-details">
                                                            <tr class="transact-details-list-table" id="transact-details-list-table">
                                                                <td class="text-center text-sm font-semibold p-1">
                                                                    {{ $pending_serials->Currency }}
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    {{ $pending_serials->TransType }}
                                                                </td>
                                                                <td class="text-right text-sm py-1 px-3">
                                                                    <strong>
                                                                        {{ number_format($pending_serials->BillAmount , 2 , '.' , ',') }}
                                                                    </strong>
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    <input class="form-control serials-input" name="serials[]" id="" type="text" value="{{ old('serials') ?? $pending_serials->Serials }}" data-fsid="{{ $pending_serials->FSID }}" autocomplete="off">
                                                                </td>
                                                                <input type="hidden" name="forex-ftdid" value="{{ $pending_serials->FTDID }}">
                                                            </tr>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-12 border border-gray-300 rounded-br rounded-bl p-2">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <input type="hidden" id="pending-serial-url" data-transactpendingserials="{{ route('branch_transactions.buying_transaction.pending_serials', ['id' => $pending_serials->FTDID]) }}">
                                            <input type="hidden" id="forex-serial-url" data-forexserials="{{ route('branch_transactions.buying_transaction.details', ['id' => $pending_serials->FTDID]) }}">
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <a class="btn btn-secondary btn-sm text-white" type="button" href="{{ route('branch_transactions.buying_transaction.details', ['id' => $pending_serials->FTDID]) }}">{{ trans('labels.back_action') }}</a>
                                            <button class="btn btn-primary btn-sm" type="button" id="submit-peding-serials">{{ trans('labels.confirm_action') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('UI.UX.security_code')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // $("#pending-serials-form").validate();

            // $('[name^="serials"]').each(function() {
            //     if ($(this).hasClass('serials-input')) {
            //         $(this).rules('add', {
            //             // required: true,
            //             minlength: 6,
            //             maxlength: 12,
            //             pattern: /^[a-zA-Z0-9]+$/,
            //             messages: {
            //                 // required: "Please enter a serial.",
            //                 minlength: "Serial must be at least 6 characters long.",
            //                 maxlength: "Serial can't exceed 12 characters.",
            //                 pattern: "Serial/s must be in a alphanumeric format. (No special characters should be present)",
            //                 duplicate: "Theres a duplicate serial."
            //             },
            //         });
            //     }
            // });

            // $.validator.addMethod("duplicate", function(value, element) {
            //     var input = $('[name^="serials"]');

            //     var input_fields_val = input.map(function() {
            //         return $(this).val();
            //     }).get();

            //     var duplicate_count = $.grep(input_fields_val, function(field_values) {
            //         return field_values == value;
            //     }).length;

            //     return duplicate_count <= 1;
            // }, "Value already exists");

            // // Handle form submission
            // $("#pending-serials-form").on('submit', function(e) {
            //     e.preventDefault();  // Prevent the default form submission

            //     // Debugging: Log form validation state
            //     console.log("Form valid state:", $(this).valid());

            //     if ($(this).valid()) {
            //         $('#add-pending-serials-modal').modal("hide");
            //     } else {
            //         Swal.fire({
            //             text: 'Invalid serial format.',
            //             icon: 'error',
            //             timer: 900,
            //             showConfirmButton: false
            //         });
            //     }
            // });

            // $("#submit-peding-serials").click(function() {
            //     console.log($("#pending-serials-form").valid());

            //     if ($("#pending-serials-form").valid() == false) {
            //         Swal.fire({
            //             text: 'Invalid serial format.',
            //             icon: 'error',
            //             timer: 900,
            //             showConfirmButton: false
            //         })

            //         // $(this).removeAttr('disabled', 'disabled');
			//     } else if ($("#pending-serials-form").valid() == true) {
            //         $('#add-pending-serials-modal').modal("hide");
            //     }
            // });
        });
    </script>
@endsection

@section('buying_scripts')
    @include('script.pending_serials_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>

