<html>
    <div style="border: 1px solid #000; padding-top: 1px; padding-bottom: 7px; padding-left: 4px; padding-right: 7px; margin-left: 3px; margin-top: 3px;">
        @php
            use Carbon\Carbon;

            $total_sum_peso = 0;
            $raw_date = Carbon::now('Asia/Manila');
        @endphp
        <table class="selling-trans-details table table-bordered" style="display: none; padding: 2px; width: 200pt;" cellspacing="0">
            <tbody>
                <tr class="t-0">
                    <td class="p-0" style="font-size: 11pt; text-align: center; font-weight: 700;" colspan="5"><b>Foreign Currency Summary</b></td>
                </tr>
            </tbody>
        </table>
        <table class="selling-trans-details table table-bordered" style="display: none; padding: 2px; width: 204pt;" border=1 cellspacing="0">
            <tbody>
                <tr>
                    <td class="p-0" style="font-size:8pt; text-align: center; padding: 1px;"><b>Currency</b></td>
                    <td class="p-0" style="font-size:8pt; text-align: center; padding: 1px; width: 26px;"><b>Pieces</b></td>
                    <td class="p-0" style="font-size:8pt; text-align: center; padding: 1px;"><b>Amount</b></td>
                </tr>
            </tbody>
            <tbody id="queued-bills-t-body">
                @forelse ($test as $selling_trans_details)
                    <tr>
                        <td class="p-0" style="font-size: 8pt; text-align: center; padding: 0;"><tt>{{ $selling_trans_details->CurrAbbv }}</tt></td>
                        <td class="p-0" style="font-size: 8pt; text-align: center; padding: 0;"><tt>{{ $selling_trans_details->total_bill_count }}</tt></td>
                        <td class="p-0" style="font-size: 8pt; text-align: right; padding-right: 5px;"><tt>{{ number_format($selling_trans_details->total_bill_amount, 2,'.',',') }}</tt></td>
                    </tr>
                    @empty
                        Edi wow
                @endforelse
            </tbody>
        </table>
        <table class="selling-trans-details table table-bordered" style="display: none; padding: 0; width: 200pt; padding-left: 4px; margin-top: 3px;" cellspacing="0">
            <tbody>
                <tr class="pt-0" style="text-align: left;">
                    <td class="p-0" style="font-size: 8pt; text-decoration: overline; text-align: center;" colspan="3"><tt></tt></td>
                    <td class="p-0" style="font-size: 8pt;"></td>
                    <td class="p-0" style="font-size: 8pt; text-align: center;" colspan="1"><tt>{{ $raw_date->toDateString() }}</tt></td>
                </tr>
                <tr class="pt-0" style="text-align: left;">
                    <td style="font-size: 8pt; text-align: center; border-top: 1px solid #000; padding: 0;" colspan="3"><tt><span style="text-decoration-line: overline; ">{{ trans('labels.selling_admin_prep_by') }}</span></tt></td>
                    <td class="p-0" style="font-size: 8pt;"></td>
                    <td style="font-size: 8pt; text-align: center; border-top: 1px solid #000; padding: 0;" colspan="1"><tt><span style="text-decoration-line: overline; ">{{ trans('labels.selling_admin_date') }}</span></tt></td>
                </tr>
            </tbody>
            <tbody style=" margin-top: 1px;">
                <tr class="pt-0" style="text-align: left;">
                    <td class="p-0" style="font-size: 8pt; text-decoration: overline; text-align: center;" colspan="3"><tt></tt></td>
                    <td class="p-0" style="font-size: 8pt;"></td>
                    <td class="p-0" style="font-size: 8pt; text-align: center;" colspan="1"></td>
                </tr>
                <tr class="pt-0" style="text-align: left;">
                    <td style="font-size: 8pt; text-align: center; border-top: 1px solid #000; padding: 0;" colspan="3"><tt><span style="text-decoration-line: overline; ">{{ trans('labels.selling_admin_received_by') }}</span></tt></td>
                    <td class="p-0" style="font-size: 8pt;"></td>
                    <td style="font-size: 8pt; text-align: center; border-top: 1px solid #000; padding: 0;" colspan="1"><tt><span style="text-decoration-line: overline; ">{{ trans('labels.selling_admin_date') }}</span></tt></td>
                </tr>
            </tbody>
        </table>
    </div>
</html>
