<!-- Modal -->
<div class="modal-header px-4 py-2">
    <div class="col-12">
        <div class="row align-items-center">
            <div class="col-5">
                <span class="modal-title text-xl font-bold" id="exampleModalLabel">Applied Configuration</span>
            </div>
            <div class="col-2 text-end">
                <span class="modal-title font-semibold"><i class='bx bx-filter'></i>&nbsp;Filter:</span>
            </div>
            <div class="col-5" id="currency-filter">
                <div class="input-group input-group-sm">
                    <select class="form-control form-control-sm" id="currency-select">
                        <option value="default">Select a currency</option>
                    </select>

                    <button class="btn btn-secondary" type="button" id="clear-filter">Clear</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-body px-4 py-3">
    <div class="row">
        <div class="col-lg-12 border border-gray-300 rounded-md" id="config-container">
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>

<script>
    $(document).ready(function() {
        $('#clear-filter').click(function() {
            $('#container-test').fadeIn("fast");
            $('#container-test').css('display', 'block');

            setTimeout(function() {
                $('#container-test').fadeOut("fast");

                $('#currency-select option[value="default"]').prop('selected', true).trigger('change');
            }, 600);
        });
    });
</script>
