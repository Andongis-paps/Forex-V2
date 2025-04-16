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

                    {{-- Control Details - Wallet --}}
                    <div class="col-lg-9">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-1 text-black">
                                                    <i class='bx bx-trending-up' ></i>&nbsp;Dollar In
                                                    {{-- <i class='bx bx-wallet'></i>&nbsp;{{ trans('labels.buffer_wallet') }} --}}
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">

                                            </div>
                                        </div>
                                    </div>

                                    {{-- Buffer in card --}}
                                    <div class="col-12">
                                        <table class="table table-bordered table-hover" id="transfers-result-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.buffer_wallet_date_time') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.buffer_wallet_branch') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.buffer_wallet_buffer_no') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap w-25">Type</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap w-25">Remarks</th>
                                                    {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.buffer_wallet_tranf_forex_no') }}</th> --}}
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Dollar In</th>
                                                    {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Balance</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody id="transfers-result-table-tbody">
                                                @forelse ($result['buffer_in'] as $buffer_in)
                                                    <tr>
                                                        <td class="text-center text-sm p-1 whitespace-nowrap">
                                                            {{ $buffer_in->BCDate }}
                                                        </td>
                                                        <td class="text-center text-sm p-1 whitespace-nowrap">
                                                            {{ $buffer_in->BranchCode }}
                                                        </td>
                                                        <td class="text-center text-sm p-1 whitespace-nowrap">
                                                            {{ $buffer_in->BCNO }}
                                                        </td>
                                                        <td class="text-center text-sm p-1 whitespace-nowrap">
                                                            @if ($buffer_in->BCType == 1)
                                                                {{ $buffer_in->DollarInType }}
                                                            @endif
                                                        </td>
                                                        <td class="text-center text-sm p-1" data-bs-toggle="popover" data-bs-content="{!! $buffer_in->Remarks == null ? 'No remarks.' : $buffer_in->Remarks !!}" data-bs-placement="bottom" data-bs-custom-class="popover-dark" tabindex="0">
                                                            @if ($buffer_in->Remarks == null)
                                                                -
                                                            @else
                                                                {{ \Illuminate\Support\Str::limit($buffer_in->Remarks, 20, '...') }}
                                                            @endif
                                                        </td>
                                                        <td class="text-right text-xs py-1 px-2 whitespace-nowrap">
                                                            @if ($buffer_in->DollarIn != '0.00')
                                                                <span class="text-[#00A65A] font-bold text-xs">
                                                                    + {{ number_format($buffer_in->DollarIn, 2, '.', ',') }}
                                                                </span>
                                                            @elseif ($buffer_in->DollarIn == '0.00')
                                                                {{ number_format(0, 2, '.', ',') }}
                                                            @endif
                                                        </td>
                                                        {{-- <td class="text-right text-xs py-1 px-2 whitespace-nowrap">
                                                            <span class="text-black font-bold text-xs">
                                                                {{ number_format($buffer_in->Balance, 2, '.', ',') }}
                                                            </span>
                                                        </td> --}}
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="text-center text-sm py-3" colspan="12" id="empty-receive-transf-table">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO AVAILABLE ENTRIES</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5"></td>
                                                    <td class="text-right text-sm py-1 pe-2 whitespace-nowrap">
                                                        <span class="badge success-badge-custom">
                                                            + {{ number_format($result['dollar_in']->total_dollar_in, 2, '.', ',') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="col-12 px-3 border border-gray-300 rounded-bl rounded-br py-2">
                                        {{ $result['buffer_in']->links() }}
                                    </div>
                                </div>
                            </div>

                            {{-- Buffer out card --}}
                            <div class="col-12 mt-2">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-1 text-black">
                                                    <i class='bx bx-trending-down' ></i>&nbsp;Dollar Out
                                                    {{-- <i class='bx bx-wallet'></i>&nbsp;{{ trans('labels.buffer_wallet') }} --}}
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <table class="table table-bordered table-hover" id="transfers-result-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.buffer_wallet_date_time') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.buffer_wallet_branch') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.buffer_wallet_buffer_no') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap w-25">Type</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap w-25">Remarks</th>
                                                    {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.buffer_wallet_tranf_forex_no') }}</th> --}}
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Dollar Out</th>
                                                    {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Balance</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody id="transfers-result-table-tbody">
                                                @forelse ($result['buffer_out'] as $buffer_out)
                                                    <tr>
                                                        <td class="text-center text-sm p-1 whitespace-nowrap">
                                                            {{ $buffer_out->BCDate }}
                                                        </td>
                                                        <td class="text-center text-sm p-1 whitespace-nowrap">
                                                            {{ $buffer_out->BranchCode }}
                                                        </td>
                                                        <td class="text-center text-sm p-1 whitespace-nowrap">
                                                            {{ $buffer_out->BCNO }}
                                                        </td>
                                                        <td class="text-center text-sm p-1 whitespace-nowrap">
                                                            @if ($buffer_out->BCType == 2)
                                                                {{ $buffer_out->DollarOutType }}
                                                            @endif
                                                        </td>
                                                        <td class="text-center text-sm p-1" data-bs-toggle="popover" data-bs-content="{!! $buffer_out->Remarks == null ? 'No remarks.' : $buffer_out->Remarks !!}" data-bs-placement="bottom" data-bs-custom-class="popover-dark" tabindex="0">
                                                            @if ($buffer_out->Remarks == null)
                                                                -
                                                            @else
                                                                {{ \Illuminate\Support\Str::limit($buffer_out->Remarks, 20, '...') }}
                                                            @endif
                                                        </td>
                                                        <td class="text-right text-xs py-1 px-2 whitespace-nowrap">
                                                            @if ($buffer_out->DollarOut != '0.00')
                                                                <span class="text-[#DC3545] font-bold text-xs">
                                                                    - {{ number_format($buffer_out->DollarOut, 2, '.', ',') }}
                                                                </span>
                                                            @elseif ($buffer_out->DollarOut == '0.00')
                                                                {{ number_format(0, 2, '.', ',') }}
                                                            @endif
                                                        </td>
                                                        {{-- <td class="text-right text-xs py-1 px-2 whitespace-nowrap">
                                                            <span class="text-black font-bold text-xs">
                                                                {{ number_format($buffer_out->Balance, 2, '.', ',') }}
                                                            </span>
                                                        </td> --}}
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="text-center text-sm py-3" colspan="12" id="empty-receive-transf-table">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO AVAILABLE ENTRIES</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5"></td>
                                                    <td class="text-right text-sm py-1 pe-2 whitespace-nowrap">
                                                        <span class="badge danger-badge-custom">
                                                            - {{ number_format($result['dollar_out']->total_dollar_out, 2, '.', ',') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="col-12 px-3 border border-gray-300 rounded-bl rounded-br py-2">
                                        {{ $result['buffer_out']->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Wallet --}}
                    <div class="col-lg-3">
                        <div class="row justify-content-center">
                            {{-- <div class="col-12">
                                <a class="primary-links" href="{{ route('admin_transactions.buffer.buffer_financing') }}">
                                    <div class="card dahsboard-cards h-100">
                                        <div class="card-body py-4">
                                            <div class="row align-items-center text-left mb-2">
                                                <div class="col-1">
                                                    <i class='bx bxs-dollar-circle text-[#0D6EFD]'></i>
                                                </div>
                                                &nbsp;
                                                <div class="col-9">
                                                    <span class="text-md">Total Buffer Balance</span>
                                                </div>
                                            </div>
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
                            </div> --}}
                            <div class="col-12">
                                <a class="primary-links" href="{{ route('admin_transactions.buffer.buffer_wallet') }}">
                                    <div class="card dahsboard-cards h-100">
                                        <div class="card-body py-4">
                                            @php
                                                $w_serials_amnt = $result['buffer_in_out']->Balance - $avail_breakdown[0]->total_w_o_serial;
                                            @endphp

                                            {{-- <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                                <div class="avatar flex-shrink-0">
                                                    <i class='bx bxs-dollar-circle bx-lg'></i>
                                                </div>
                                            </div> --}}
                                            <div class="row align-items-center text-left mb-2">
                                                <div class="col-1">
                                                    <i class='bx bx-coin-stack text-[#0D6EFD]'></i>
                                                </div>
                                                &nbsp;
                                                <div class="col-9">
                                                    <span class="text-md">Available Buffer Balance</span>
                                                </div>
                                            </div>
                                            {{-- <h4 class="card-title mb-3"></h4> --}}
                                            <div class="row">
                                                <div class="col-12 text-left">
                                                    <span class="card-title text-2xl font-bold">&#36; </span>&nbsp;<span class="card-title text-2xl mb-3 font-bold">{{ number_format($w_serials_amnt, 2, '.', ',') }}</span>
                                                    {{-- <span class="card-title text-2xl font-bold">&#36; </span>&nbsp;<span class="card-title text-2xl mb-3 font-bold">{{ number_format($result['buffer_in_out']->Balance, 2, '.', ',') }}</span> --}}
                                                </div>
                                            </div>

                                            <div class="row mt-2">
                                                <div class="col-12 text-left">
                                                    <span class="text-sm text-black">W/ Serials Amount:</span>
                                                    <span class="font-bold text-sm text-[#00A65A]">
                                                        &#36; {{ number_format($w_serials_amnt, 2, '.', ',') }}
                                                    </span>
                                                </div>
                                            </div>
                                            {{-- <div class="row mt-1">
                                                <div class="col-12 text-left">
                                                    <span class="text-sm text-black">W/O Serials Amount:</span>
                                                    <span class="font-bold text-sm text-[#DC3545]">
                                                        &#36; {{ number_format($avail_breakdown[0]->total_w_o_serial, 2, '.', ',') }}
                                                    </span>
                                                </div>
                                            </div> --}}
                                        </div>
                                    </div>
                                </a>
                            </div>

                            {{-- <div class="col-12 mt-2">
                                <a class="dashboard-links" href="{{ route('admin_transactions.buffer.buffer_financing') }}">
                                    <div class="card dahsboard-cards h-100">
                                        <div class="card-body py-4">
                                            <div class="row align-items-center text-left mb-2">
                                                <div class="col-1">
                                                    <i class='bx bxs-dollar-circle text-[#00A65A]'></i>
                                                </div>
                                                &nbsp;
                                                <div class="col-9">
                                                    <span class="text-md">Total Dollar In</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 text-left">
                                                    <span class="card-title text-2xl font-bold">&#36; </span>&nbsp;<span class="card-title text-2xl mb-3 font-bold">{{ number_format($result['dollar_in']->total_dollar_in, 2, '.', ',') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div> --}}

                            <div class="col-12 mt-2">
                                <a class="danger-links" href="{{ route('admin_transactions.buffer.buffer_financing') }}">
                                    <div class="card dahsboard-cards h-100">
                                        <div class="card-body py-4">
                                            {{-- <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                                <div class="avatar flex-shrink-0">
                                                    <i class='bx bxs-dollar-circle bx-lg'></i>
                                                </div>
                                            </div> --}}
                                            <div class="row align-items-center text-left mb-2">
                                                <div class="col-1">
                                                    <i class='bx bxs-dollar-circle text-[#DC3545]'></i>
                                                    {{-- <i class='bx bxs-right-top-arrow-circle text-[#DC3545]'></i> --}}
                                                </div>
                                                &nbsp;
                                                <div class="col-9">
                                                    <span class="text-md">Total Dollar Out</span>
                                                </div>
                                            </div>
                                            {{-- <h4 class="card-title mb-3"></h4> --}}
                                            <div class="row">
                                                <div class="col-12 text-left">
                                                    <span class="card-title text-2xl font-bold">&#36; </span>&nbsp;<span class="card-title text-2xl mb-3 font-bold">{{ number_format($result['dollar_out_card']->total_dollar_out, 2, '.', ',') }}</span>
                                                </div>
                                            </div>
                                            {{-- <div class="row">
                                                <div class="col-12 text-left">
                                                    <medium class="text-black fw-medium"><span>Transaction count:</span>&nbsp;&nbsp;<span class="font-semibold text-green-600" id="buying-transaction-count">{{ $buying_sales->transct_count }}</span></medium>
                                                </div>
                                            </div> --}}
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-12 mt-2">
                                <a class="dashboard-links" href="{{ route('admin_transactions.buffer.buffer_transfers') }}">
                                    <div class="card dahsboard-cards h-100">
                                        <div class="card-body py-4">
                                            {{-- <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                                <div class="avatar flex-shrink-0">
                                                    <i class='bx bxs-dollar-circle bx-lg'></i>
                                                </div>
                                            </div> --}}
                                            <div class="row align-items-center text-left mb-2">
                                                <div class="col-1">
                                                    <i class='bx bxs-left-down-arrow-circle text-[#00A65A]'></i>
                                                </div>
                                                &nbsp;
                                                <div class="col-9">
                                                    <span class="text-md">Incoming Buffer Amount:</span>
                                                </div>
                                            </div>
                                            {{-- <h4 class="card-title mb-3"></h4> --}}
                                            <div class="row">
                                                <div class="col-12 text-left">
                                                    <span class="card-title text-2xl font-bold">&#36; </span>&nbsp;<span class="card-title text-2xl mb-3 font-bold">{{ number_format($result['incoming_buffer_amnt']->Balance, 2, '.', ',') }}</span>
                                                </div>
                                            </div>
                                            {{-- <div class="row">
                                                <div class="col-12 text-left">
                                                    <medium class="text-black fw-medium"><span>Transaction count:</span>&nbsp;&nbsp;<span class="font-semibold text-green-600" id="buying-transaction-count">{{ $buying_sales->transct_count }}</span></medium>
                                                </div>
                                            </div> --}}
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

@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
