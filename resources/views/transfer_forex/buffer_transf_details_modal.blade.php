<!-- Modal -->
    <div class="modal-header ps-4 py-2">
        <span class="text-lg text-black font-bold">
            <strong>
                {{ trans('labels.transf_forex_buffer_deets') }}
            </strong>
        </span>
    </div>

    <div class="modal-body px-3 py-2">
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transfer_forex_date') }}</th> --}}
                            <tr>
                                <th class="text-center font-extrabold text-black p-1" colspan="1">
                                    <input class="form-check-input" type="checkbox" id="revert-bills">
                                </th>
                                <th class="text-left text-xs font-extrabold text-black p-1 ps-2 whitespace-nowrap" colspan="1">Revert Bills</th>
                                <th colspan="2"></th>
                            </tr>
                            <th class="text-center font-extrabold text-black p-1">
                                <input class="form-check-input" type="checkbox" id="select-all-buffer" disabled>
                            </th>
                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transfer_forex_currency') }}</th>
                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transfer_forex_serials') }}</th>
                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transfer_forex_bill_amnt') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_bill_amount = 0;
                        @endphp

                        @forelse ($result['buffer_transf_deets'] as $buffer_transf_deets)
                            @php
                                $total_bill_amount += $buffer_transf_deets->BillAmount;
                            @endphp

                            <tr>
                                <td class="text-center text-sm p-1">
                                    <input class="form-check-input select-one-buffer" type="checkbox" data-fsid="{{ $buffer_transf_deets->FSID }}" data-serials="{{ $buffer_transf_deets->Serials }}" data-billamount="{{ $buffer_transf_deets->BillAmount }}" disabled>
                                </td>
                                {{-- <td class="text-sm text-center p-1">
                                    {{ $buffer_transf_deets->TransferDate }}
                                </td> --}}
                                <td class="text-sm text-center p-1">
                                    {{ $buffer_transf_deets->Currency }}
                                </td>
                                <td class="text-sm text-center p-1">
                                    {{ $buffer_transf_deets->Serials }}
                                </td>
                                <td class="text-sm text-right py-1 px-3">
                                    <strong>
                                        {{ number_format($buffer_transf_deets->BillAmount, 2, '.', ',') }}
                                    </strong>
                                </td>
                            </tr>
                        @empty
                            TEST
                        @endforelse
                    </tbody>
                    <tfoot id="total-amount-footer">
                        <tr>
                            <td class="text-sm text-right p-1" colspan="3">
                                <span class="font-bold text-sm text-black">{{ trans('labels.transfer_deets_summary_amnt_curr') }}</span>:
                            </td>
                            <td class="text-sm text-right py-1 px-3" colspan="1">
                                <span class="text-sm text-black"><strong><span class="text-sm text-black font-bold">&nbsp;{{ number_format($total_bill_amount, 2, '.', ',') }}</span></strong>
                            </td>
                        </tr>
                    </tfoot>
                    <tfoot class="d-none" id="total-amnt-revert">
                        <tr>
                            <td class="text-sm text-right p-1" colspan="3">
                                <input type="hidden" id="buffer-type" value="{{ $buffer_transf->BufferType }}">
                                <input type="hidden" id="total-buffer-amount-text" value="{{ $total_bill_amount }}">
                                <span class="font-bold text-sm text-black">{{ trans('labels.transfer_deets_summary_amnt_curr') }}</span>:
                            </td>
                            <td class="text-sm text-right py-1 px-3" colspan="1">
                                <strong><span class="text-sm text-black font-bold" id="buffer-amount">&#36;&nbsp;{{ number_format($total_bill_amount, 2, '.', ',') }}</span></strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- <div class="row align-items-center mt-2 mb-0 d-none" id="total-amnt-revert">
            <div class="col-4 offset-5 text-end">
                <input type="hidden" id="buffer-type" value="{{ $buffer_transf->BufferType }}">
                <input type="hidden" id="total-buffer-amount-text" value="{{ $total_bill_amount }}">
                <span class="font-bold text-sm text-black">{{ trans('labels.transfer_deets_summary_amnt_curr') }}</span>:
            </div>
            <div class="col-3 text-end">
                <strong><span class="text-lg text-black font-bold" id="buffer-amount">&#36;&nbsp;{{ number_format($total_bill_amount, 2, '.', ',') }}</span></strong>
            </div>
        </div> --}}
        {{-- <input type="hidden" id="total-buffer-amount" value="{{ $total_bill_amount }}"> --}}
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary btn-warning-f btn-sm d-none" type="button" id="revert" disabled>Revert</button>
        <button type="button" class="btn btn-primary btn-sm button-buffer-details" id="ack-button-buffer-details" data-transferforexid="{{ $result['buffer_transf_deets'][0]->TransferForexID }}">{{ trans('labels.acknowledge') }}</button>
    </div>

<script>
    $(document).ready(function() {
        $('#ack-button-buffer-details').click(function() {
            $('#buffer-transfer-details').modal("hide");
            $('#ack-buff-security-code-modal').modal("show");
        });
    });

    $(document).ready(function() {
        const socketserver = "{{ config('app.socket_io_server') }}";
        const socket = io(socketserver);

        var selected_bill_count = [];
        var selected_bill_total_amount = 0;

        $('#revert-bills').click(function() {
            selected_bill_total_amount = 0;

            $('#total-buffer-amount').val(0);
            $('#buffer-amount').text("0.00");

            if ($(this).prop('checked') == true) {
                $('#select-all-buffer').prop('disabled', false);
                $('.select-one-buffer').prop('disabled', false);
                $('#total-amnt-revert, #revert').removeClass('d-none').hide().fadeIn(500);
                $('#ack-button-buffer-details, #total-amount-footer').addClass('d-none').fadeOut(500);
            } else {
                $('#select-all-buffer').attr('disabled', true).prop('checked', false);
                $('.select-one-buffer').attr('disabled', true).prop('checked', false);
                $('#total-amnt-revert, #revert').addClass('d-none').fadeOut(500);
                $('#ack-button-buffer-details, #total-amount-footer').removeClass('d-none').hide().fadeIn(500);
            }
        });

        $('.select-one-buffer:checked').each(function() {
            var bill_amount = parseFloat($(this).attr('data-billamount'));

            if (!isNaN(bill_amount)) {
                selected_bill_total_amount += bill_amount;
                selected_bill_count.push(bill_amount);
            }
        });

        $('#total-buffer-amount').val(selected_bill_total_amount.toFixed(2));
        $('#buffer-amount').text(selected_bill_total_amount.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        $('#select-all-buffer').click(function() {
            var check_status = $(this).prop('checked');

            if (check_status) {
                // $('#ack-button-buffer-details').prop('disabled', false);

                $('.select-one-buffer').each(function() {
                    if (!$(this).prop('checked')) {
                        $(this).prop('checked', true);

                        var bill_amount = parseFloat($(this).attr('data-billamount'));

                        if (!isNaN(bill_amount)) {
                            selected_bill_total_amount += bill_amount;
                            selected_bill_count.push(bill_amount);
                        }
                    }
                });
            } else {
                // $('#ack-button-buffer-details').prop('disabled', true);

                $('.select-one-buffer').each(function() {
                    if ($(this).prop('checked')) {
                        $(this).prop('checked', false);
                        var bill_amount = parseFloat($(this).attr('data-billamount'));
                        if (!isNaN(bill_amount)) {
                            selected_bill_total_amount -= bill_amount;
                            var delete_index = selected_bill_count.indexOf(bill_amount);
                            if (delete_index !== -1) {
                                selected_bill_count.splice(delete_index, 1);
                            }
                        }
                    }
                });
            }

            if (selected_bill_total_amount <= 0) {
                $('#buffer-amount').text("0.00");
                $('#total-buffer-amount').val("0.00");
                $('#revert').prop('disabled', true);
            } else {
                $('#revert').prop('disabled', false);
                $('#total-buffer-amount').val(selected_bill_total_amount.toFixed(2));
                $('#buffer-amount').text(selected_bill_total_amount.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            }
        });

        $('.select-one-buffer').click(function() {
            var bill_amount = parseFloat($(this).attr('data-billamount'));

            if ($(this).prop('checked')) {
                if (!isNaN(bill_amount)) {
                    selected_bill_total_amount += bill_amount;
                    selected_bill_count.push(bill_amount);
                }
            } else {
                if (!isNaN(bill_amount)) {
                    selected_bill_total_amount -= bill_amount;
                    var delete_index = selected_bill_count.indexOf(bill_amount);
                    if (delete_index !== -1) {
                        selected_bill_count.splice(delete_index, 1);
                    }
                }
            }

            var all_checked = $('.select-one-buffer').length === $('.select-one-buffer:checked').length;

            if (selected_bill_total_amount <= 0) {
                $('#revert').prop('disabled', true);
                // $('#ack-button-buffer-details').prop('disabled', true);

                $('#buffer-amount').text("0.00");
                $('#total-buffer-amount').val("0.00");
            } else {
                $('#revert').prop('disabled', false);
                // $('#ack-button-buffer-details').prop('disabled', false);

                $('#total-buffer-amount').val(selected_bill_total_amount.toFixed(2));
                $('#buffer-amount').text(selected_bill_total_amount.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            }

            if (all_checked) {
                $('#select-all-buffer').prop('checked', true);
            } else {
                $('#select-all-buffer').prop('checked', false);
            }
        });

        $('#revert').click(function() {
            $('#buffer-transfer-details').modal("hide");
            $('#rev-buff-security-code-modal').modal("show");
        });

        $('#halt-revert').click(function() {
            $('#buffer-transfer-details').modal("show");
            $('#rev-buff-security-code-modal').modal("hide");
        });
    });
</script>

