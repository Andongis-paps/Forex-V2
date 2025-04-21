<!-- Modal -->
<div class="modal-header py-2 ps-3">
    <span class="text-lg font-bold">
        <i class='bx bx-detail me-2'></i> Breakdown
    </span>
</div>

<div class="modal-body p-2">
    <div class="col-12">
        {{-- <div class="col-12"> --}}
        <div class="col-12 border border-solid border-gray-300 rounded-md" id="branch-breakdown-container">
            <table class="table table-bordered table-hover mb-0" id="branch-breakdown">
                <thead>
                    <tr>
                        <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1 w-25">Branch</th>
                        <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1 w-25">Count</th>
                        <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1 w-25">Total Amount</th>
                        <th class="text-center text-xs font-extrabold text-black whitespace-nowrap p-1 w-25">Pending Serials</th>
                        <th class="text-center text-xs font-extrabold text-white whitespace-nowrap p-1 w-25 !bg-red-500">Pending Count</th>
                        <th class="text-center text-xs font-extrabold text-white whitespace-nowrap p-1 w-25 !bg-red-500">Pending Amount</th>
                    </tr>
                </thead>
                <tbody id="branch-breakdown-body">
                    <tr>
                        <td class="text-black text-sm text-center p-2" colspan="3">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
</div>
