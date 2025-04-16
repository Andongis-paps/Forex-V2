<!-- Modal -->
<form class="form m-0" action="{{ URL::to('/addPendingSerials') }}" enctype="multipart/form-data" method="POST" id="add-pending-serials-form">
    <div class="modal-header ps-4">
        <h4 class="modal-title" id="exampleModalLabel">{{ trans('labels.add_pending_serials') }}</h4>
        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
    </div>

    @csrf

    <div class="modal-body px-4">
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-hovered table-bordered mb-0" id="pending-serials-table">
                    <thead>
                        <tr>
                            <th class="text-th-buying text-center text-sm font-extrabold text-black py-1 px-1">{{ trans('labels.add_pending_serials_receipt_no') }}</th>
                            <th class="text-th-buying text-center text-sm font-extrabold text-black py-1 px-1">{{ trans('labels.add_pending_serials_currency') }}</th>
                            <th class="text-th-buying text-center text-sm font-extrabold text-black py-1 px-1">{{ trans('labels.add_pending_serials_bill_amount') }}</th>
                            <th class="text-th-buying text-center text-sm font-extrabold text-black py-1 px-1">{{ trans('labels.add_pending_serials_serial') }}</th>
                            <th class="text-th-buying text-center text-sm font-extrabold text-black py-1 px-1">{{ trans('labels.add_pending_serials_entry_date') }}</th>
                        </tr>
                    </thead>
                    <tbody id="pending-serials-table-body">
                        <tr>
                            <td class="text-sm text-center"></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="add-pending-serial-button">Add Serials</button>
    </div>
</form>

{{-- <script>
    $(document).ready(function() {
        $('#update-expense-form').validate({
            rules: {
                expense_name: {
                    required: true,
                    pattern: /^[a-zA-z]\s*/,
                },
            },
            messages: {
                expense_name: {
                    required: 'Enter a currency name.',
                    pattern: 'Invalid currency name format.'
                },
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script> --}}

{{-- @include('script.scripts') --}}
