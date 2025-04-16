<div class="modal-header ps-4 py-2">
    <span class="text-lg">
        <strong>
            {{ trans('labels.buffer_stocks_summary') }}
        </strong>
    </span>
</div>

<div class="modal-body px-3 py-3">
    <div class="col-12 text-center">
        <table class="table table-hovered table-bordered mb-0" id="buff-details">
            <thead>
                {{-- <tr>
                    <th class="text-center font-extrabold text-black p-1" colspan="1">
                        <input class="form-check-input" type="checkbox" id="revert-bills">
                    </th>
                    <th class="text-left text-xs font-extrabold text-black p-1 ps-2 whitespace-nowrap" colspan="1">Revert Bills</th>
                    <th colspan="2"></th>
                </tr> --}}
                <tr>
                    {{-- <th class="text-center font-extrabold text-black p-1">
                        <input class="form-check-input" type="checkbox" id="select-all-buffer" disabled>
                    </th> --}}
                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transfer_forex_currency') }}</th>
                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transfer_forex_serials') }}</th>
                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transfer_forex_bill_amnt') }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_bill_amount = 0;
                @endphp

                @forelse ($result['buff_cut_details'] as $buff_cut_details)
                    <tr>
                        {{-- <td class="text-center text-td-buying text-xs p-1">
                            <input class="form-check-input select-one-buffer" type="checkbox" data-fsid="{{ $buff_cut_details->FSID }}" data-serials="{{ $buff_cut_details->Serials }}" data-billamount="{{ $buff_cut_details->BillAmount }}" disabled>
                        </td> --}}
                        <td class="text-sm text-center p-1">
                            {{ $buff_cut_details->Currency }}
                        </td>
                        <td class="text-sm text-center p-1">
                            {{ $buff_cut_details->Serials }}
                        </td>
                        <td class="text-sm text-right py-1 px-3">
                            {{ number_format($buff_cut_details->BillAmount, 2, '.', ',') }}
                        </td>

                        @php
                            $total_bill_amount += $buff_cut_details->BillAmount;
                        @endphp
                    </tr>
                @empty
                    TEST
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td class="text-sm text-center py-1" colspan="2"></td>
                    {{-- <td class="text-center py-1 whitespace-nowrap p-2" colspan="1">
                        <span class="text-sm">
                            {{ trans('labels.transfer_deets_bills_count') }}: <span class="text-sm font-semibold" id="trans-count">{{ count($result['buff_cut_details']) }}</span>
                        </span>
                    </td> --}}
                    <td class="text-right py-1 whitespace-nowrap px-3">
                        <input type="hidden" id="total-buffer-amount" value="{{ $total_bill_amount }}">
                        <span class="text-sm text-black">{{ trans('labels.transfer_deets_summary_amnt_curr') }}</span>: <strong><span class="text-sm text-black">&#36;&nbsp;</span><span class="text-sm text-black font-bold" id="buffer-amount">{{ number_format($total_bill_amount, 2, '.', ',') }}</span></strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="modal-footer">
    <div class="row align-items-center">
        <div class="col-6 text-end pe-0">
            <button class="btn btn-secondary btn-sm" type="button" id="cancel-cut" data-bs-dismiss="modal">Cancel</button>
        </div>
        {{-- <div class="col-6 text-end pe-2">
            <button class="btn btn-primary" type="button" id="proceed-transfer" disabled>Proceed</button>
        </div> --}}
    </div>
</div>

<script>
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
            } else {
                $('#select-all-buffer').attr('disabled', true).prop('checked', false);
                $('.select-one-buffer').attr('disabled', true).prop('checked', false);
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
                $('#ack-button-buffer-details').prop('disabled', false);

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
                $('#ack-button-buffer-details').prop('disabled', true);

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
                $('#proceed-transfer').prop('disabled', true);
            } else {
                $('#proceed-transfer').prop('disabled', false);
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
                $('#proceed-transfer').prop('disabled', true);
                $('#ack-button-buffer-details').prop('disabled', true);

                $('#buffer-amount').text("0.00");
                $('#total-buffer-amount').val("0.00");
            } else {
                $('#proceed-transfer').prop('disabled', false);
                $('#ack-button-buffer-details').prop('disabled', false);

                $('#total-buffer-amount').val(selected_bill_total_amount.toFixed(2));
                $('#buffer-amount').text(selected_bill_total_amount.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            }

            if (all_checked) {
                $('#select-all-buffer').prop('checked', true);
            } else {
                $('#select-all-buffer').prop('checked', false);
            }
        });

        $('#proceed-transfer').click(function() {
            $('#incoming-buff-modal').modal("hide");
            $('#security-code-modal').modal("show");
        });

        $('#halt-transaction').click(function() {
            $('#incoming-buff-modal').modal("show");
            $('#security-code-modal').modal("hide");
        });
    });
</script>


