<form class="m-0" action="">
    <!-- Modal -->
    <div class="modal-header px-4">
        <div class="row">
            <h4 class="modal-title" id="exampleModalLabel">{{ trans('labels.selling_trans_avlbl_bills_modal') }} <span class="font-bold" id="currency-name"></span> </h4>
        </div>
        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
    </div>

    @csrf

    <div class="modal-body px-1 py-0">
        <div class="col-12 p-2">
            <div class="row align-items-center">
                <div class="col-5">
                    <label class="text-sm font-bold" for="">Bill Amount Filter:</label>
                    <select class="form-select mt-1" id="denomination-filter">
                    </select>
                </div>
                <div class="col-7 ps-0">
                    <label class="text-sm font-bold" for="">Serial Search:</label>

                    <input type="text" class="form-control search-serials-available mt-1" id="search-serials-available" name="search-serials-available" value="{{ app('request')->input('search') }}" placeholder="Search for a serial">
                    {{ csrf_field() }}
                </div>
            </div>
        </div>

        <div class="col-12 p-2">
            <div class="col-lg-12 border border-gray-300" id="serial-stock-container">
                <table class="table table-hover mb-0" id="serial-stock-table-appended">
                    <thead class="sticky-header">
                        <tr>
                            <th class="text-center text-black p-1">
                                <input class="form-check-input" type="checkbox" id="available-bills-select-all" name="available-bills-select-all">
                            </th>
                            <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_trans_avlbl_bills_modal_serial') }}</th>
                            <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.selling_trans_avlbl_bills_modal_bill_amnt') }}</th>
                        </tr>
                    </thead>
                    <tbody id="serial-stock-table-body">
                    </tbody>
                    <tfoot class="sticky-footer">
                        <td class="text-center text-sm p-1"></td>
                        <td class="text-right font-bold text-sm p-1">
                            Total Amount:
                        </td>
                        <td class="text-right text-sm py-1 px-3">
                            <strong>
                                <span id="available-bills-total-amount">

                                </span>
                                <input id="available-bills-total-amount-input" type="hidden" value="">
                            </strong>
                        </td>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary btn-sm" id="select-serial-stock-appended" type="button">Select</button>
    </div>
</form>
