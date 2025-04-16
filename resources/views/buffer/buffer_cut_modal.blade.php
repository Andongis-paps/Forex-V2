<div class="modal fade" id="buffer-cut-details" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content add-denom">
            <div class="modal-header ps-4 py-2">
                <span class="text-lg">
                    <strong>
                        Stock Details
                    </strong>
                </span>
            </div>

            <div class="modal-body px-4 py-2">
                {{-- <div class="col-12 text-center">
                    <strong>
                        <span class="text-lg">Cash Count</span>
                    </strong>
                </div> --}}

                <div class="col-12 text-center">
                    <table class="table table-hovered table-bordered mb-0" id="bill-cash-count">
                        <thead>
                            <tr>
                                <th class="text-center text-xs font-extrabold text-white p-1 !bg-[#00a65a]" colspan="4"><span>Cash Count</span></th>
                            </tr>
                            <tr>
                                <th class="text-center text-xs font-extrabold text-black p-0">{{ trans('labels.transfer_forex_currency') }}</th>
                                <th class="text-center text-xs font-extrabold text-black p-0">Pieces</th>
                                <th class="text-center text-xs font-extrabold text-black p-0 w-50">Amount</th>
                                {{-- <th class="text-center text-xs font-extrabold text-black p-1"></th> --}}
                            </tr>
                        </thead>
                        <tbody id="bill-cash-count-body">

                        </tbody>
                    </table>
                </div>

                <div class="col-12">
                    <hr class="my-2">
                </div>

                <div class="col-12">
                    <div class="row">
                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                            <input type="radio" class="btn-check radio-buffer-type" name="buffer-type" id="buffer-selling" value="1" checked>
                            <label class="btn btn-outline-primary" for="buffer-selling">
                                <strong>For Selling</strong>
                            </label>

                            <input type="radio" class="btn-check radio-buffer-type" name="buffer-type" id="buffer-additonal" value="2">
                            <label class="btn btn-outline-primary" for="buffer-additonal">
                                <strong>Additional Buffer</strong>
                            </label>

                            <input type="hidden" id="current-buffer-balance" value="{{ $result['buffer_in_out']->Balance }}">
                        </div>
                    </div>
                </div>

                {{-- <div class="col-12 mt-2">
                    <div class="row align-items-center">
                        <div class="col-1 text-start pe-0">
                            <label class="switch switch-success switch-square">
                                <input type="checkbox" class="switch-input">
                                <span class="switch-toggle-slider">
                                    <span class="switch-on"></span>
                                    <span class="switch-off"></span>
                                </span>
                                <span class="switch-label cursor-pointer">

                                </span>
                            </label>
                        </div>
                        <div class="col-5 text-start ps-0">
                            <span class="text-sm text-black font-bold">Buy Out</span>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-md text-black font-semibold">
                                Available Buffer Balance:
                            </span>
                            &nbsp;
                            <span class="text-md font-extrabold text-[#00A65A]">
                                {{ number_format($result['buffer_in_out']->Balance, 2, '.', ',') }}
                            </span>
                        </div>
                    </div>
                </div> --}}

                <div class="col-12">
                    <hr class="my-2">
                </div>

                {{-- <div class="col-12">
                    <hr class="my-2">
                </div> --}}

                <div class="col-12 mt-2">
                    <form class="m-0" method="post" id="declare-buffer-form">
                        @csrf
                        <div class="col-12">
                            <div class="row align-items-center">
                                <div class="col-4">
                                    <span class="text-black text-sm font-semibold">
                                        <strong>Buffer Amount:</strong>
                                    </span>
                                </div>
                                <div class="col-8">
                                    <input class="form-control text-right" type="number" id="selected-bill-total-amount" placeholder="0.00">
                                    <input type="hidden" id="currency-id" value="">
                                    {{-- <span class="text-sm font-extrabold">&#36;</span>&nbsp;<span class="text-sm font-extrabold" id="selected-bill-total-amount"> 0.00</span> --}}
                                </div>
                            </div>
                            <div class="row align-items-center mt-2 d-none" id="selling-rate-container">
                                <div class="col-4">
                                    <span class="text-black text-sm font-semibold">
                                        <strong>Selling Rate:</strong>
                                    </span>
                                </div>
                                <div class="col-8">
                                    <input class="form-control text-right" type="number" id="selling-rate" placeholder="0.00">
                                </div>
                            </div>

                            <div class="d-none" id="fields-container">
                                <div class="col-12">
                                    <hr class="my-2">
                                </div>
                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button type="button" class="accordion-button px-0 py-1" data-bs-toggle="collapse" data-bs-target="#accordionOne" aria-expanded="true" aria-controls="accordionOne" role="tabpanel">
                                                <span class="text-sm"><strong>Breakdown By Rate</strong></span>
                                            </button>
                                        </h2>

                                        <div id="accordionOne" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                            <div class="accordion-body px-0">
                                                <table class="table table-hovered table-bordered mb-0 mt-1" id="by-rate-breakdown">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center text-xs font-extrabold text-black p-1">Amount</th>
                                                            <th class="text-center text-xs font-extrabold text-black p-1">Count</th>
                                                            <th class="text-center text-xs font-extrabold text-black p-1">Selling Rate</th>
                                                            <th class="text-center text-xs font-extrabold text-black p-1">Exchange Amount</th>
                                                            <th class="text-center text-xs font-extrabold text-black p-1">Buying Rate</th>
                                                            <th class="text-center text-xs font-extrabold text-black p-1">Principal</th>
                                                            <th class="text-center text-xs font-extrabold text-black p-1">Gain\Loss</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <td class="text-center p-1 text-sm py-2" colspan="13">
                                                            <span class="text-sm">
                                                                <strong>NOT AVAILABLE</strong>
                                                            </span>
                                                        </td>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="6"></td>
                                                            <td class="text-right text-sm py-1 px-2" id="gain-loss-container"></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center mt-2">
                                    <div class="col-4">
                                        <span class="text-black text-sm font-semibold">
                                            Exchange Amount:
                                        </span>
                                    </div>
                                    <div class="col-8">
                                        <input class="form-control pe-4 text-right font-semibold" type="text" id="exch-amount" readonly placeholder="0.00">
                                        <input type="hidden" id="true-exch-amount">
                                    </div>
                                </div>
                                <div class="row align-items-center mt-2">
                                    <div class="col-4">
                                        <span class="text-black text-sm font-semibold">
                                            Total Principal:
                                        </span>
                                    </div>
                                    <div class="col-8">
                                        <input class="form-control pe-4 text-right font-semibold" type="text" id="principal" readonly placeholder="0.00" value="">
                                        <input type="hidden" id="true-principal" value="">
                                    </div>
                                </div>
                                <div class="row align-items-center mt-2">
                                    <div class="col-4">
                                        <span class="text-black text-sm font-semibold">
                                            Income:
                                        </span>
                                    </div>
                                    <div class="col-8">
                                        <input class="form-control pe-4 text-right font-bold" type="text" id="income" readonly placeholder="0.00">
                                        <input type="hidden" id="true-income">
                                    </div>
                                </div>
                                <div class="row justify-content-center align-items-center mt-3 mb-2">
                                    <div class="col-4 text-center">
                                        <div class="row">
                                            <button class="btn btn-primary button-edit pe-2" type="button" id="compute-buffer">Calculate</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    {{-- <tfoot>
                        <tr>
                            <td colspan="4"></td>
                            <td class="text-center text-black text-td-buying text-sm p-1">
                                <span class="text-sm">Bill Count:</span>
                                <strong>
                                    <span class="text-sm font-extrabold" id="selected-bill-count">0</span>
                                </strong>
                            </td>
                            <td class="text-right text-td-buying text-sm py-1 px-3">
                                <span class="text-sm">Total Amount:</span>
                                <strong>
                                    <span class="text-sm font-extrabold">&#36;</span>&nbsp;<span class="text-sm font-extrabold" id="selected-bill-total-amount"> 0.00</span>
                                </strong>
                            </td>
                        </tr>
                    </tfoot> --}}
                    {{-- <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br py-2">
                        <div class="row">
                            <div class="col-12 text-end">
                                @can('access-permission', $menu_id)
                                    <a class="btn btn-secondary btn-sm" type="button" href="{{ route('admin_transactions.buffer.buffer') }}">{{ trans('labels.back_action') }}</a>
                                @endcan

                                @can('add-permission', $menu_id)
                                    <button class="btn btn-primary btn-sm @if (count($result['branch_stock_details']) < 0) disabled @endif" type="button" id="declare-buffer-confirm-button">{{ trans('labels.declare_as_buffer') }}</button>
                                @endcan
                            </div>
                        </div>
                    </div> --}}
                </div>

                {{-- <div class="col-lg-12 mb-2 border border-solid border-gray-300" id="transfer-summary-container">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <table class="table table-hover mb-0 " id="bills-for-buffer-table">
                                <thead>
                                    <tr>
                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_forex_currency') }}</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_forex_serials') }}</th>
                                        <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.transfer_forex_bill_amnt') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="bill-for-transfer-table-body">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> --}}
            </div>

            <div class="modal-footer">
                <div class="row align-items-center">
                    <div class="col-12 text-end pe-0">
                        <button class="btn btn-secondary btn-sm" type="button" id="cancel-cut" data-bs-dismiss="modal">Cancel</button>
   
                        <button class="btn btn-primary btn-sm" type="button" id="proceed-transfer">Proceed</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.switch-input').change(function() {
            if ($(this).is(':checked')) {
                $(this).attr('checked', 'checked');
                var available_buffer = parseFloat($('#available-buffer').val());

                $('#selected-bill-total-amount').val(available_buffer.toFixed(2));
                // $('#selected-bill-total-amount').val("{{ $result['buffer_in_out']->Balance }}");
            } else {
                $(this).removeAttr('checked');
                $('#selected-bill-total-amount').val('').attr('placeholder', '0.00');
            }
        });
    });
</script>
