<html>
    <div style="padding-top: 0; padding-bottom: 0; padding-left: 1px; padding-right: 0;">
        <table class="selling-trans-details table table-bordered" style="display: none; padding: 3px; width: 575pt; margin-top: 1px;" border=1 cellspacing="0">
            <tbody>
                <tr>
                    <td class="p-0" style="font-size: 10pt; text-align: center; padding: 2.5px;" colspan="9"><b>Returned Bills for ATD</b></td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td class="p-0" style="font-size:8pt; text-align: center; padding: 1px;" width="7%"><b>Branch</b></td>
                    <td class="p-0" style="font-size:8pt; text-align: center; padding: 1px;" width="8%"><b>Currency</b></td>
                    <td class="p-0" style="font-size:8pt; text-align: center; padding: 1px;" width="10%"><b>Amount</b></td>
                    {{-- <td class="p-0" style="font-size:8pt; text-align: center; padding: 1px;"><b>Selling Rate</b></td> --}}
                    <td class="p-0" style="font-size:8pt; text-align: center; padding: 1px;" width="15%"><b>Amount (Peso)</b></td>
                    <td class="p-0" style="font-size:8pt; text-align: center; padding: 1px;" width="8%"><b>Serial</b></td>
                    <td class="p-0" style="font-size:8pt; text-align: center; padding: 1px;" width="8%"><b>Tags</b></td>
                    {{-- <td class="p-0" style="font-size:8pt; text-align: center; padding: 1px;"><b>Transaction No.</b></td> --}}
                    <td class="p-0" style="font-size:8pt; text-align: center; padding: 1px;" width="8%"><b>Date Sold</b></td>
                    <td class="p-0" style="font-size:8pt; text-align: center; padding: 1px;" width="23%"><b>Employee</b></td>
                    <td class="p-0" style="font-size:9pt; text-align: center; padding: 1px;" width="15%"><b>ATD No.</b></td>
                </tr>
            </tbody>
            <tbody id="tagged-bills-atds">
                @forelse ($test as $tagged_bills)
                    @php
                        $amount_php = $tagged_bills->BillAmount * $tagged_bills->CMRUsed;
                        $trimmed_tags = rtrim($tagged_bills->BillTags[0]->BillStatus);
                        $trimmed_full_name = rtrim($tagged_bills->FullName);
                    @endphp
                    <tr height="30%">
                        <td style="font-size: 9pt; text-align: center; padding: 1px;">{{ $tagged_bills->BranchCode }}</td>
                        <td style="font-size: 9pt; text-align: center; padding: 1px;">{{ $tagged_bills->CurrAbbv }}</td>
                        <td style="font-size: 9pt; text-align: center; padding: 1px;">{{ number_format($tagged_bills->BillAmount, 2,'.',',') }}</td>
                        {{-- <td style="font-size: 9pt; text-align: center; padding: 1px;">{{ number_format($tagged_bills->CMRUsed, 2,'.',',') }}</td> --}}
                        <td style="font-size: 9pt; text-align: center; padding: 1px;">{{ number_format($amount_php, 2,'.',',') }}</td>
                        <td style="font-size: 9pt; text-align: center; padding: 1px;">{{ $tagged_bills->Serials }}</td>
                        <td style="font-size: 9pt; text-align: center; padding: 1px;">{{ $trimmed_tags }}</td>
                        {{-- <td style="font-size: 9pt; text-align: center; padding: 1px;">{{ $tagged_bills->TransactionNo }}</td> --}}
                        <td style="font-size: 9pt; text-align: center; padding: 1px;">{{ $tagged_bills->DateSold }}</td>
                        <td style="font-size: 9pt; text-align: center; padding: 1px;">{{ $trimmed_full_name }}</td>
                        <td hidden style="font-size: 9pt; text-align: center; padding: 1px;"></td>
                    </tr>
                @empty
                    Edi wow
                @endforelse
            </tbody>
        </table>
    </div>
</html>
