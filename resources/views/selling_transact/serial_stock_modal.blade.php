<form class="m-0" action="">
    <!-- Modal -->
    <div class="modal-header px-4">
        <div class="row">
            <h4 class="modal-title" id="exampleModalLabel">{{ trans('labels.selling_trans_avlbl_bills_modal') }} <span id="currency-name"></span> </h4>
        </div>
        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
    </div>

    @csrf

    <div class="modal-body px-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="row align-items-center">
                    <table class="table table-bordered table-hover mb-0" id="serial-stock-table">
                        <thead>
                            <tr>
                                <th class="text-th-buying text-center text-sm font-extrabold text-black py-1 px-1">{{ trans('labels.action_data') }}</th>
                                <th class="text-th-buying text-center text-sm font-extrabold text-black py-1 px-1">{{ trans('labels.selling_trans_avlbl_bills_modal_serial') }}</th>
                                <th class="text-th-buying text-center text-sm font-extrabold text-black py-1 px-1">{{ trans('labels.selling_trans_avlbl_bills_modal_bill_amnt') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                {{-- <td class="text-center text-sm" colspan="3" id="serials-stock-currency-modal">
                                    <span class="buying-no-transactions text-lg font-semibold">
                                        NO SERIAL/S AVAILABLE
                                    </span>
                                </td> --}}
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <div class="btn btn-primary" id="select-serial-stock" type="submit">Select</div>
    </div>
</form>

{{-- @include('script.scripts') --}}
