<!-- Modal -->
<form class="form m-0" action="{{ URL::to('/addNewExpense') }}" enctype="multipart/form-data" method="POST" id="add-new-expense-form">
    <div class="modal-header ps-4">
        <h4 class="modal-title" id="exampleModalLabel">{{ trans('labels.w_expense_add') }}</h4>
        {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
    </div>

    @csrf

    <div class="modal-body px-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="row align-items-center">
                    <div class="col-4">
                        <label for="description">
                            <strong>
                                {{ trans('labels.w_expense_name') }}: &nbsp;<span class="required-class">*</span>
                            </strong>
                        </label>
                    </div>
                    <div class="col-8">
                        <input type="text" name="expense_name" class="form-control" step="any" required autocomplete="false">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="expense-add-button">Add</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('#add-new-expense-form').validate({
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
</script>

{{-- @include('script.scripts') --}}
