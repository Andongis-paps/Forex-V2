<div class="modal fade" id="transfer-breakdown-modal" tabindex="-1" aria-labelledby="buying-transact" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-12">
                    <span class="text-lg font-semibold p-2">
                        {{ trans('labels.selling_availble_bills') }}
                    </span>
                </div>
            </div>

            <div class="modal-body">
                <div class="col-12 available-stock-list-container @if (count($result['serial_breakdown']) <= 5) stocks-lowest-height @elseif (count($result['serial_breakdown']) < 10) stocks-min-height @elseif (count($result['serial_breakdown']) > 10) stocks-max-height @endif border rounded">
                    <table class="table table-hover m-0" id="transf-all-break-d-table">
                        <thead class="sticky-header">
                            <tr>
                                <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.selling_currency') }}</th>
                                <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.selling_bill_amount') }}</th>
                                <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.selling_pieces') }}</th>
                                <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.selling_sub_total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($result['serial_breakdown'] as $serial_breakdown)
                                <tr class="denom-details-list-tabl" id="denom-details-list-table">
                                    <td class="text-center text-sm p-1">
                                        <span class="">
                                            {{ Str::title($serial_breakdown->Currency) }}
                                        </span>
                                        <input type="hidden" class="transfer-break-d-currency" value="{{ Str::title($serial_breakdown->Currency) }}">
                                    </td>
                                    <td class="text-right text-sm p-1">
                                        <span class=" font-medium">
                                            {{-- <strong> --}}
                                                {{ number_format($serial_breakdown->BillAmount, 2, '.', ',') }}
                                            {{-- </strong> --}}
                                        </span>
                                        <input type="hidden" class="transfer-break-d-bill-amnt" value="{{ $serial_breakdown->BillAmount }}">
                                    </td>
                                    <td class="text-center text-sm p-1">
                                        <span class=" font-medium">
                                            {{ $serial_breakdown->bill_amount_count }}
                                        </span>
                                        <input type="hidden" class="transfer-break-d-bill-count" value="{{ $serial_breakdown->bill_amount_count }}">
                                    </td>
                                    <td class="text-right text-sm p-1">
                                        <span class=" font-medium">
                                            <strong>
                                                {{ number_format($serial_breakdown->total_bill_amount, 2, '.' , ',') }}
                                            </strong>
                                        </span>
                                        <input type="hidden" class="transfer-break-d-total-amount" value="{{ number_format($serial_breakdown->total_bill_amount, 2, '.' , ',') }}">
                                    </td>
                                </tr>
                            @empty
                                <td class="text-center text-td-buying text-sm py-3" colspan="12">
                                    <span class="buying-no-transactions text-lg">
                                        <strong>NO AVAILABLE STOCK</strong>
                                    </span>
                                </td>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('labels.close_action') }}</button>
            </div>
        </div>
    </div>
</div>
