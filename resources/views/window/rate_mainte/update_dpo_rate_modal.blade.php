<!-- Modal -->
<form class="form m-0" action="{{ route('maintenance.rate_maintenance.save') }}" enctype="multipart/form-data" method="POST" id="update-dpo-rate-form">
    @csrf

    <div class="modal-header ps-3">
        <span class="modal-title text-lg font-bold" id="exampleModalLabel">{{ trans('labels.dpofx_update_rate') }}</span>
    </div>
    
    <div class="modal-body px-4 py-2">
        <div class="row">
            <div class="col-12">
                <div class="row align-items-center">
                    <label class="font-bold mb-1 p-0" for="dpofx-rate">
                        <strong>
                            {{ trans('labels.dpofx_rate') }}:
                        </strong>
                    </label>

                    <input class="form-control" type="number" name="dpofx-rate" id="dpofx-rate" placeholder="0.0000" steps="any">
                </div>
            </div>

            <div class="col-12">
                <div class="row">
                    <hr class="my-2">
                </div>
            </div>

            <div class="col-12">
                <div class="row align-items-center">
                    <div class="col-12 border border-gray-300 p-0" id="branch-dpofx-rate-container"  style="height: 550px!important; overflow: hidden; overflow-y: scroll;">
                        <table class="table table-hover mb-0">
                            <thead class="sticky-header">
                                <tr>
                                    <th class="text-black text-xs text-center p-1">
                                        <input class="form-check-input mb-1" type="checkbox" id="dpofx-rate-select-all" name="dpofx-rate-select-all" disabled>
                                    </th>
                                    <th class="text-black text-xs text-center p-1">{{ trans('labels.dpofx_branch') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($result['branches'] as $branches)
                                    <tr>
                                        <td class="text-sm text-black text-center p-1">
                                            <input class="form-check-input dpofx-rate-select-one" type="checkbox" value="{{ $branches->BranchCode }}" data-branchid="{{ $branches->BranchID }}" disabled>
                                        </td>
                                        <td class="text-sm text-black text-center p-1">
                                            {{ $branches->BranchCode }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="selected-branches" name="selected-branches">

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        @can('edit-permission', $menu_id)
            <button type="button" class="btn btn-primary btn-sm" id="update-dpo-rate-button" disabled>Update Rate</button>
        @endcan
    </div>
</form>
