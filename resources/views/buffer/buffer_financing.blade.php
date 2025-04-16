@extends('template.layout')
@section('content')

   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <hr>
                    </div>

                    <div class="col-lg-12">
                        <div class="row justify-content-center">
                            <div class="col-9">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-list-plus'></i>&nbsp;{{ trans('labels.buff_financing') }}
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                @can('add-permission', $menu_id)
                                                    {{-- <a class="btn btn-primary text-white btn-sm" href="{{ route('admin_transactions.buffer.add_financing') }}"> --}}
                                                    <button class="btn btn-primary text-white btn-sm" type="button" id="add-buffer" data-bs-toggle="modal" data-bs-target="#add-buffer-modal">
                                                        Add Buffer <i class='bx bx-plus'></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <table class="table table-bordered table-hover" id="transfers-result-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.buff_financing_entry_date') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.buff_financing_buff_no') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">Currency</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.buff_financing_remarks') }}</th>
                                                    {{-- <th class="text-center text-xs font-extrabold text-black p-1">Added By</th> --}}
                                                    <th class="text-center text-xs font-extrabold text-black p-1">Dollar In</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">Principal</th>
                                                    {{-- <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.buff_financing_status') }}</th> --}}
                                                    <th class="text-center text-xs font-extrabold text-black p-1"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="transfers-result-table-tbody">
                                                @if (count($result['buffer_financing']) > 0)
                                                    @foreach ($result['buffer_financing'] as $buffer_financing)
                                                        <tr>
                                                            <td class="text-center text-xs p-1">
                                                                {{ $buffer_financing->BFDate }}
                                                            </td>
                                                            <td class="text-center text-xs p-1">
                                                                {{ $buffer_financing->BFNo }}
                                                            </td>
                                                            <td class="text-center text-xs p-1">
                                                                @if ($buffer_financing->Currency == null)
                                                                    <strong>-</strong>
                                                                @else
                                                                    {{ $buffer_financing->Currency }}
                                                                @endif
                                                            </td>
                                                            <td class="text-center text-xs p-1" data-bs-toggle="popover" data-bs-content="{!! $buffer_financing->Remarks == null ? 'No remarks.' : $buffer_financing->Remarks !!}" data-bs-placement="bottom" data-bs-custom-class="popover-dark" tabindex="0">
                                                                @if ($buffer_financing->Remarks == null)
                                                                    -
                                                                @else
                                                                    {{ \Illuminate\Support\Str::limit($buffer_financing->Remarks, 35, '...') }}
                                                                @endif
                                                            </td>
                                                            {{-- <td class="text-center text-xs p-1">
                                                                {{ $buffer_financing->Name }}
                                                            </td> --}}
                                                            <td class="text-right text-xs py-1 pe-3">
                                                                @if ($buffer_financing->DollarAmount != '0.00')
                                                                    <span class="text-[#00A65A] font-bold text-xs">
                                                                        + {{ number_format($buffer_financing->DollarAmount, 2, '.', ',') }}
                                                                    </span>
                                                                @elseif ($buffer_financing->DollarAmount == '0.00')
                                                                    {{ number_format(0, 2, '.', ',') }}
                                                                @endif
                                                            </td>
                                                            <td class="text-right text-xs py-1 pe-3">
                                                                {{ number_format($buffer_financing->Principal, 2, '.', ',') }}
                                                            </td>
                                                            {{-- <td class="text-center text-xs p-1">
                                                                @if ($buffer_financing->Received == 0)
                                                                    <span class="badge rounded-pill bg-warning warning-badge-custom font-bold">
                                                                        <strong>
                                                                            {{ trans('labels.status_pending') }}
                                                                        </strong>
                                                                    </span>
                                                                @elseif ($buffer_financing->Received == 1)
                                                                    <span class="badge rounded-pill success-badge-custom font-bold">
                                                                        <strong>
                                                                            {{ trans('labels.status_received') }}
                                                                        </strong>
                                                                    </span>
                                                                @endif
                                                            </td> --}}
                                                            <td class="text-center text-xs p-1">
                                                                <a class="btn btn-primary button-edit pe-2 text-white receive-buffer-transfer" type="button" href="{{ route('admin_transactions.buffer.break_d_finance', ['BFID' => $buffer_financing->BFID]) }}">
                                                                    <i class='bx bx-detail'></i>
                                                                </a>
                                                                {{-- @If ($buffer_financing->Received == 0)
                                                                    <a class="btn btn-primary button-edit pe-2 text-white receive-buffer-transfer" type="button" data-bs-toggle="modal" data-bs-target="#receive-buffer" data-bufferid="{{ $buffer_financing->BFID }}">
                                                                        <i class='bx bxs-archive-in'></i>
                                                                    </a>
                                                                @elseif ($buffer_financing->Received == 1)
                                                                    <a class="btn btn-primary button-edit pe-2 text-white receive-buffer-transfer" type="button" href="#">
                                                                        <i class='bx bx-detail'></i>
                                                                    </a>
                                                                @endif --}}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center text-xs py-3" colspan="12" id="empty-receive-transf-table">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO AVAILABLE DPOFX TRANSFERS</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-12 px-3 border border-gray-300 rounded-bl rounded-br py-2">
                                        {{ $result['buffer_financing']->links() }}
                                    </div>
                                </div>
                            </div>

                            {{-- Wallet --}}
                            <div class="col-lg-3">
                                <div class="row justify-content-center">
                                    <div class="col-12">
                                        <a class="primary-links" href="{{ route('admin_transactions.buffer.buffer_financing') }}">
                                            <div class="card dahsboard-cards h-100">
                                                <div class="card-body py-4">
                                                    {{-- <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                                        <div class="avatar flex-shrink-0">
                                                            <i class='bx bxs-dollar-circle bx-lg'></i>
                                                        </div>
                                                    </div> --}}
                                                    <div class="row align-items-center text-left mb-2">
                                                        <div class="col-1">
                                                            <i class='bx bxs-dollar-circle text-[#0D6EFD]'></i>
                                                        </div>
                                                        &nbsp;
                                                        <div class="col-9">
                                                            <span class="text-md">Total Buffer Amount</span>
                                                        </div>
                                                    </div>
                                                    {{-- <h4 class="card-title mb-3"></h4> --}}
                                                    <div class="row mb-2">
                                                        <div class="col-12 text-left">
                                                            <span class="card-title text-2xl font-bold">&#36; </span>&nbsp;<span class="card-title text-2xl mb-3 font-bold">{{ number_format($result['totality']->buffer_totality, 2, '.', ',') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 text-left">
                                                            <span>As of {{ now()->format('F d, Y') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <div class="row justify-content-center mt-2">
                                    <div class="col-12">
                                        <a class="primary-links" href="{{ route('admin_transactions.buffer.buffer_financing') }}">
                                            <div class="card dahsboard-cards h-100">
                                                <div class="card-body py-4">
                                                    {{-- <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                                        <div class="avatar flex-shrink-0">
                                                            <i class='bx bxs-dollar-circle bx-lg'></i>
                                                        </div>
                                                    </div> --}}
                                                    <div class="row align-items-center text-left mb-2">
                                                        <div class="col-1">
                                                            <i class='bx bxs-dollar-circle text-[#0D6EFD]'></i>
                                                        </div>
                                                        &nbsp;
                                                        <div class="col-9">
                                                            <span class="text-md">Total Principal</span>
                                                        </div>
                                                    </div>
                                                    {{-- <h4 class="card-title mb-3"></h4> --}}
                                                    <div class="row mb-2">
                                                        <div class="col-12 text-left">
                                                            <span class="card-title text-2xl font-bold">PHP </span>&nbsp;<span class="card-title text-2xl mb-3 font-bold">{{ number_format($result['total_principal']->total_principal, 2, '.', ',') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12 text-left">
                                                            <span>As of {{ now()->format('F d, Y') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('UI.UX.security_code')
    @include('buffer.add_buffer_modal')

@endsection

@section('buffer_transfer_scripts')
    @include('script.add_buffer_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
