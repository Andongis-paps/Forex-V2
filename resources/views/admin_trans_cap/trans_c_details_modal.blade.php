<div class="modal-header ps-4 py-2">
    <span class="text-lg">
        <strong>
            Transfer Capital Details
        </strong>
    </span>
</div>

<div class="modal-body px-4 py-2">
    <div class="row align-items-center">
        <div class="col-12 p-1">
            <table class="table table-bordered table-hover mb-0" id="trans-cap-details">
                <thead>
                    <th class="text-black font-extrabold text-center text-xs whitespace-nowrap p-1">
                        <input class="form-check-input" type="checkbox" id="select-all-trans-cap">
                    </th>
                    <th class="text-black font-extrabold text-center text-xs whitespace-nowrap p-1">TC No.</th>
                    <th class="text-black font-extrabold text-center text-xs whitespace-nowrap p-1">Amount</th>
                    <th class="text-black font-extrabold text-center text-xs whitespace-nowrap p-1">Status</th>
                </thead>
                <tbody>
                    @forelse ($result['details'] as $details)
                        <tr>
                            <td class="text-black text-center text-xs whitespace-nowrap p-1">
                                @if ($details->Transferred == 0 && $details->Received == 0)
                                    <input class="form-check-input select-trans-cap" type="checkbox" data-tcid="{{ $details->TCID }}" data-transcapamnt="{{ $details->TranscapAmount }}">
                                @endif
                            </td>
                            <td class="text-black text-center text-xs whitespace-nowrap p-1">
                                {{ $details->TCNo }}
                            </td>
                            <td class="text-black text-right text-xs whitespace-nowrap py-1 pe-3">
                                <strong>
                                    {{ number_format($details->TranscapAmount, 2, '.', ',') }}
                                </strong>
                            </td>
                            <td class="text-black text-center text-xs whitespace-nowrap p-1">
                                @if ($details->Transferred == 0)
                                    <span class="badge warning-badge-custom">Pending</span>
                                @elseif ($details->Transferred == 1)
                                    <span class="badge primary-badge-custom">Transferred</span>
                                @elseif ($details->Received == 1)
                                    <span class="badge success-badge-custom">Received</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-black text-center text-xs whitespace-nowrap p-1"></td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td class="p-1" colspan="4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

    <div class="row align-items-center mt-1">
        <div class="col-3 offset-5 text-end">
            <input type="hidden" id="total-trans-cap-amount" value="">
            <span class="text-sm text-black">{{ trans('labels.transfer_deets_summary_amnt_curr') }}</span>:
        </div>
        <div class="col-4 text-end">
            <strong>
                <span class="text-lg text-black">PHP&nbsp;</span>
                <span class="text-lg text-black font-bold" id="trans-cap-amount">0.00</span>
            </strong>
        </div>
    </div>
</div>

<div class="modal-footer">
    <div class="row align-items-center">
        <div class="col-6 text-end pe-0">
            <button class="btn btn-secondary btn-sm" type="button" id="halt-add-buffer" data-bs-dismiss="modal">Cancel</button>
        </div>
        <div class="col-6 text-end pe-2">
            <button class="btn btn-primary btn-sm" type="button" id="proceed-trans-cap" disabled>Transfer</button>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        var trans_c_count = [];
        var total_trans_c_amnt = 0;

        $('.select-trans-cap:checked').each(function() {
            var trans_cap = parseFloat($(this).attr('data-transcapamnt'));

            if (!isNaN(trans_cap)) {
                total_trans_c_amnt += trans_cap;
                trans_c_count.push(trans_cap);
            }
        });

        $('#select-all-trans-cap').click(function() {
            var check_status = $(this).prop('checked');

            if (check_status) {
                $('#proceed-trans-cap').prop('disabled', false);

                $('.select-trans-cap').each(function() {
                    if (!$(this).prop('checked')) {
                        $(this).prop('checked', true);

                        var trans_cap = parseFloat($(this).attr('data-transcapamnt'));

                        if (!isNaN(trans_cap)) {
                            total_trans_c_amnt += trans_cap;
                            trans_c_count.push(trans_cap);
                        }
                    }
                });
            } else {
                $('#proceed-trans-cap').prop('disabled', true);

                $('.select-trans-cap').each(function() {
                    if ($(this).prop('checked')) {
                        $(this).prop('checked', false);

                        var trans_cap = parseFloat($(this).attr('data-transcapamnt'));

                        if (!isNaN(trans_cap)) {
                            total_trans_c_amnt -= trans_cap;
                            var delete_index = trans_c_count.indexOf(trans_cap);
                            if (delete_index !== -1) {
                                trans_c_count.splice(delete_index, 1);
                            }
                        }
                    }
                });
            }

            if (total_trans_c_amnt <= 0) {
                $('#trans-cap-amount').text("0.00");
                $('#total-trans-cap-amount').val("0.00");
                $('#proceed-trans-cap').prop('disabled', true);
            } else {
                $('#proceed-trans-cap').prop('disabled', false);
                $('#total-trans-cap-amount').val(total_trans_c_amnt.toFixed(2));
                $('#trans-cap-amount').text(total_trans_c_amnt.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            }
        });

        $('.select-trans-cap').click(function() {
            var trans_cap = parseFloat($(this).attr('data-transcapamnt'));

            if ($(this).prop('checked')) {
                if (!isNaN(trans_cap)) {
                    total_trans_c_amnt += trans_cap;
                    trans_c_count.push(trans_cap);
                }
            } else {
                if (!isNaN(trans_cap)) {
                    total_trans_c_amnt -= trans_cap;
                    var delete_index = trans_c_count.indexOf(trans_cap);
                    if (delete_index !== -1) {
                        trans_c_count.splice(delete_index, 1);
                    }
                }
            }

            var all_checked = $('.select-trans-cap').length === $('.select-trans-cap:checked').length;

            if (total_trans_c_amnt <= 0) {
                $('#proceed-trans-cap').prop('disabled', true);
                $('#ack-button-buffer-details').prop('disabled', true);

                $('#trans-cap-amount').text("0.00");
                $('#total-trans-cap-amount').val("0.00");
            } else {
                $('#proceed-trans-cap').prop('disabled', false);
                $('#ack-button-buffer-details').prop('disabled', false);

                $('#total-trans-cap-amount').val(total_trans_c_amnt.toFixed(2));
                $('#trans-cap-amount').text(total_trans_c_amnt.toLocaleString("en", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            }

            if (all_checked) {
                $('#select-all-trans-cap').prop('checked', true);
            } else {
                $('#select-all-trans-cap').prop('checked', false);
            }
        });

        $('#proceed-trans-cap').click(function() {
            $('#trans-cap-details-modal').modal("hide");
            $('#security-code-modal').modal("show");
        });
    });
</script>
