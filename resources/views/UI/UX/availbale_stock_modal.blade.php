<div class="modal fade" id="available-stocks-modal" tabindex="-1" aria-labelledby="buying-transact" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-12">
                    <span class="text-lg font-bold p-2">
                        {{ trans('labels.selling_availble_bills') }}
                    </span>
                </div>
            </div>

            <div class="modal-body p-2">
                @if (count($result['stocks_set_o']) > 0)
                    {{-- @if (session('time_toggle_status') == 0)
                        <div class="col-12 mb-2">
                            <span class="text-lg font-bold p-2">
                                Receipt Set O
                            </span>
                        </div>
                    @endif --}}

                    <div class="col-12 available-stock-list-container @if (count($result['available_serials']) < 5) stocks-lowest-height @elseif (count($result['available_serials']) >= 10) stocks-min-height @elseif (count($result['available_serials']) >= 20) stocks-max-height @endif border">
                        <table class="table table-hover m-0">
                            <thead class="sticky-header">
                                <tr>
                                    <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.selling_currency') }}</th>
                                    <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.selling_denom') }}</th>
                                    <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.selling_pieces') }}</th>
                                    <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.selling_sub_total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($result['stocks_set_o'] as $stocks_set_o)
                                    <tr class="denom-details-list-tabl" id="denom-details-list-table">
                                        <td class="text-center text-sm py-1">
                                            {{ $stocks_set_o->Currency }}
                                        </td>
                                        <td class="text-center text-sm py-1">
                                            {{ $stocks_set_o->BillAmount }}
                                        </td>
                                        <td class="text-center text-sm py-1">
                                            {{ $stocks_set_o->bill_amount_count }}
                                        </td>
                                        <td class="text-right text-sm font-bold py-1 pe-2">
                                            @php
                                                $bill_amnt =  $stocks_set_o->BillAmount;
                                                $bill_amnt_cnt =  $stocks_set_o->bill_amount_count;

                                                $subtotal = $bill_amnt * $bill_amnt_cnt;
                                            @endphp

                                            {{ number_format($subtotal, 2 , '.' , ',') }}
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
                @endif
                @if (session('time_toggle_status') == 0)
                    @if (count($result['stocks_set_b']) > 0)
                        <div class="col-12 my-2">
                            <span class="text-lg font-bold p-2">
                                Receipt Set B
                            </span>
                        </div>

                        <div class="col-12 available-stock-list-container @if (count($result['available_serials']) < 5) stocks-lowest-height @elseif (count($result['available_serials']) >= 10) stocks-min-height @elseif (count($result['available_serials']) >= 20) stocks-max-height @endif border rounded">
                            <table class="table table-hover m-0">
                                <thead class="sticky-header">
                                    <tr>
                                        <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.selling_currency') }}</th>
                                        <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.selling_bill_amount') }}</th>
                                        <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.selling_pieces') }}</th>
                                        <th class="text-center text-sm font-extrabold text-black p-1">{{ trans('labels.selling_sub_total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($result['stocks_set_b'] as $stocks_set_b)
                                        <tr class="denom-details-list-tabl" id="denom-details-list-table">
                                            <td class="text-center text-sm py-1">
                                                {{ $stocks_set_b->Currency }}
                                            </td>
                                            <td class="text-center text-sm py-1">
                                                {{ $stocks_set_b->BillAmount }}
                                            </td>
                                            <td class="text-center text-sm py-1">
                                                {{ $stocks_set_b->bill_amount_count }}
                                            </td>
                                            <td class="text-right text-sm font-bold py-1">
                                                @php
                                                    $bill_amnt =  $stocks_set_b->BillAmount;
                                                    $bill_amnt_cnt =  $stocks_set_b->bill_amount_count;

                                                    $subtotal = $bill_amnt * $bill_amnt_cnt;
                                                @endphp

                                                {{ number_format($subtotal, 2 , '.' , ',') }}
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
                    @endif
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ trans('labels.close_action') }}</button>
            </div>
        </div>
    </div>
</div>
