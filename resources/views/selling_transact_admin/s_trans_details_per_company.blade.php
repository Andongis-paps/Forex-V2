<html>
    <div style="border: 2px solid #000; padding-top: 3px; padding-bottom: 10px; padding-left: 4px; padding-right: 4px; margin-left: 15px; margin-top: 15px;">
        <table class="selling-trans-details table table-bordered" style="display: none; padding: 0; width: 300pt;" cellspacing="0">
            <tbody>
                <tr class="t-0" style="margin: 0pt; padding: 0pt;">
                    <td class="p-0" style="font-size: 6pt; text-align: right; font-weight: 100;" colspan="4"><tt>{{ $test[0]->Rset }}</tt></td>
                </tr>
                <tr class="t-0">
                    <td class="p-0" style="font-size: 18pt; text-align: center; font-weight: 700;" colspan="4"><b>{{ Str::title($test[0]->CompanyName) }}</b></td>
                </tr>
                <tr class="pt-0">
                    <td class="p-0" style="font-size: 9pt; text-align: center;" colspan="4"><tt>Foreign &nbsp;Currency &nbsp; Conversion &nbsp; Form</tt></td>
                </tr>
                <tr class="pt-0" style="text-align: right;">
                    <td class="p-0" style="text-align: right; font-weight: 200; font-weight: 400; color: red;" colspan="4"><span style="font-size: 10px;">{{ trans('labels.selling_admin_series_no') }}</span><span style="font-size: 12px;">{{ str_pad($test[0]->FormSeries, 6, '0', STR_PAD_LEFT) }}</span></td>
                </tr>
                <tr class="pt-0" style="text-align: left;">
                    <td class="p-0" style="font-size: 9pt; text-align: left;" colspan="1"><tt>{{ trans('labels.selling_admin_date') }}:</tt></td>
                    <td class="p-0" style="font-size: 9pt; text-align: left;" colspan="3"><tt>{{ Str::title($test[0]->DateSold) }}</tt></td>
                </tr>
                <tr class="pt-0" style="text-align: left;">
                    <td class="p-0" style="font-size: 9pt; text-align: left;" colspan="1"><tt>{{ trans('labels.selling_admin_sold_to') }}:</tt></td>
                    <td class="p-0" style="font-size: 9pt; text-align: left;" colspan="3"><tt>{{ Str::title($test[0]->FullName) }}</tt></td>
                </tr>
                <tr class="pt-0" style="text-align: left;">
                    <td class="p-0" style="font-size: 9pt; text-align: left;" colspan="1"><tt>{{ trans('labels.selling_admin_company') }}:</tt></td>
                    @php
                        $string = Str::title($test[0]->Nameofemployer);
                        $formattedString = preg_replace('/(?<!^)(?=[A-Z])/', '&nbsp;', $string);
                    @endphp
                    <td class="p-0" style="font-size: 9pt; text-align: left;" colspan="3"><tt>{!! $formattedString !!}</tt></td>
                </tr>
                <tr class="pt-0" style="text-align: left;">
                    <td class="p-0" style="font-size: 9pt; text-align: left;" colspan="1"><tt>{{ trans('labels.selling_admin_address') }}:</tt></td>
                    <td class="p-0" style="font-size: 9pt; text-align: left;" colspan="3"><tt>{{ Str::title($test[0]->Address2) }}</tt></td>
                </tr>
            </tbody>
        </table>
        <table class="selling-trans-details table table-bordered" style="display: none; padding: 3px; width: 300pt; margin-top: 1px;" border=1 cellspacing="0">
            <tbody>
                <tr>
                    <td class="p-0" style="font-size:9pt; text-align: center; padding: 1px;"><b>Currency</b></td>
                    <td class="p-0" style="font-size:9pt; text-align: center; padding: 1px;"><b>Amount</b></td>
                    <td class="p-0" style="font-size:9pt; text-align: center; padding: 1px;"><b>Rate</b></td>
                    <td class="p-0" style="font-size:9pt; text-align: center; padding: 1px;"><b>Total</b></td>
                </tr>
            </tbody>
            <tbody id="selling-trans-details-body">
                @php
                    $total_sum_peso = 0;
                @endphp

                @forelse ($test as $selling_trans_details)
                    @foreach ($selling_trans_details->Currency as $rolly_parameter)
                        @php
                            $total_amnt_pesos = $rolly_parameter->total_curr_amount * $rolly_parameter->CMRUsed;
                            $total_sum_peso += $total_amnt_pesos;
                        @endphp

                        <tr>
                            <td style="font-size: 8pt; text-align: center; padding: 0px;"><tt>{{ $rolly_parameter->Currency }}</tt></td>
                            <td style="font-size: 8pt; text-align: center; padding: 0px;"><tt>{{ number_format($rolly_parameter->total_curr_amount, 2,'.',',') }}</tt></td>
                            <td style="font-size: 8pt; text-align: center; padding: 0px;"><tt>{{ number_format($rolly_parameter->CMRUsed, 2,'.',',') }}</tt></td>
                            <td style="font-size: 8pt; text-align: right; padding-right: 5px;"><tt>{{ number_format($total_amnt_pesos, 2,'.',',') }}</tt></td>
                        </tr>
                    @endforeach
                @empty
                    Edi wow
                @endforelse
                    <tr>
                        <td class="p-0" style="font-size: 8pt; text-align: right; padding-right: 5px;" colspan="3"><tt><b>Total</b></td>
                        <td class="p-0" style="font-size: 8pt; text-align: right; padding-right: 5px;" colspan="1"><tt><b>{{ number_format($total_sum_peso, 2,'.',',') }}</b></tt></td>
                    </tr>
            </tbody>
        </table>
        <table class="selling-trans-details table table-bordered" style="display: none; padding: 0; width: 300pt; margin-top: 4px;" cellspacing="0">
            <tbody>
                <tr class="pt-0" style="text-align: left;">
                    <td class="p-0" style="font-size: 8pt;" colspan="1"><tt>{{ trans('labels.selling_admin_prep_by') }}:</tt></td>
                    <td class="p-0" style="font-size: 8pt; text-decoration: overline; text-align: center;" colspan="2"><tt>{{ Str::title($test[0]->Name) }}</tt></td>
                    <td class="p-0" style="font-size: 8pt;" colspan="1"><tt>{{ trans('labels.selling_admin_date') }}:</tt></td>
                    <td class="p-0" style="font-size: 8pt; text-align: center;" colspan="1"><tt>{{ $test[0]->DateSold }}</tt></td>
                </tr>
                <tr class="pt-0" style="text-align: left;">
                    <td class="p-0" style="font-size: 8pt;" colspan="1"></td>
                    <td style="font-size: 8pt; text-align: center; border-top: 1px solid #000; padding: 0;" colspan="2"><tt><span style="text-decoration-line: overline; ">{{ trans('labels.selling_admin_signature') }}</span></tt></td>
                    <td class="p-0" style="font-size: 8pt;" colspan="1"></td>
                    <td class="p-0" style="font-size: 8pt; border-top: 1px solid #000;" colspan="1"></td>
                </tr>
            </tbody>
            <tbody>
                <tr class="pt-0" style="text-align: left;">
                    <td class="p-0" style="font-size: 8pt;" colspan="1"><tt>{{ trans('labels.selling_admin_received_by') }}:</tt></td>
                    <td class="p-0" style="font-size: 8pt; text-decoration: overline; text-align: center;" colspan="2"></td>
                    <td class="p-0" style="font-size: 8pt;" colspan="1"><tt>{{ trans('labels.selling_admin_date') }}:</tt></td>
                    <td class="p-0" style="font-size: 8pt;" colspan="1"></td>
                </tr>
                <tr class="pt-0" style="text-align: left;">
                    <td class="p-0" style="font-size: 8pt;" colspan="1"></td>
                    <td style="font-size: 8pt; text-align: center; border-top: 1px solid #000; padding: 0;" colspan="2"><tt><span style="text-decoration-line: overline; ">{{ trans('labels.selling_admin_signature') }}</span></tt></td>
                    <td class="p-0" style="font-size: 8pt;" colspan="1"></td>
                    <td class="p-0" style="font-size: 8pt; border-top: 1px solid #000;" colspan="1"></td>
                </tr>
            </tbody>
        </table>
    </div>
</html>
