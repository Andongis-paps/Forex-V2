@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-12">


                    </div>

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
                                                    <i class="bx bx-trending-up"></i>&nbsp;DPO In
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                <a class="btn btn-primary text-white btn-sm" href="{{ route('admin_transactions.dpofx.dpo_in') }}">
                                                    {{ trans('labels.dpo_add_transact') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                </a>
                                                {{-- <a class="btn btn-primary text-white btn-sm" href="{{ route('admin_transactions.dpofx.dpo_out') }}">
                                                    {{ trans('labels.dpo_add_transact_sell') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                </a> --}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <table class="table table-bordered table-hover" id="transfers-result-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">DPO Ctrl. No.</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Company</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Type</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">DPO In</th>
                                                </tr>
                                            </thead>

                                            <tbody id="transfers-result-table-tbody">
                                                @forelse ($result['dpo_in'] as $dpo_in)
                                                    <tr>
                                                        <td class="text-center text-sm p-1 whitespace-nowrap">
                                                            {{ $dpo_in->DPOCNo }}
                                                        </td>
                                                        <td class="text-center text-td-buying text-sm p-1 whitespace-nowrap">
                                                            @if ($dpo_in->CompanyName != null)
                                                                {{ $dpo_in->CompanyName }}
                                                            @else
                                                                <strong><span>N/A</span></strong>
                                                            @endif
                                                        </td>
                                                        <td class="text-center text-sm p-1 whitespace-nowrap">
                                                            {{ $dpo_in->DPOType }}
                                                        </td>
                                                        <td class="text-right text-sm py-1 pe-2 whitespace-nowrap">
                                                            @if ($dpo_in->DollarIn != '0.00')
                                                                <span class="text-[#00A65A] font-bold text-xs">
                                                                    + {{ number_format($dpo_in->DollarIn, 2, '.', ',') }}
                                                                </span>
                                                            @elseif ($dpo_in->DollarIn == '0.00')
                                                                {{ number_format(0, 2, '.', ',') }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="text-center text-td-buying text-sm py-2" colspan="12" id="empty-receive-transf-table">
                                                            <span class="buying-no-transactions">
                                                                <strong>NO AVAILABLE ENTRIES</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3"></td>
                                                    <td class="text-right text-sm py-1 pe-2 whitespace-nowrap">
                                                        <span class="badge success-badge-custom">
                                                            @if (count($result['dollar_in']) == 0)
                                                                + {{ number_format(0, 2, '.', ',') }}
                                                            @else
                                                                + {{ number_format(array_sum($result['dollar_in']), 2, '.', ',') }}
                                                            @endif
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br py-2">
                                        {{ $result['dpo_in']->links() }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-2">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-1 text-black">
                                                    <i class="bx bx-trending-down"></i>&nbsp;DPO Out
                                                </span>
                                            </div>
                                            <div class="col-6 text-end">
                                                <a class="btn btn-primary text-white btn-sm" href="{{ route('admin_transactions.dpofx.dpo_out') }}">
                                                    {{ trans('labels.dpo_add_transact_sell') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <table class="table table-bordered table-hover" id="transfers-result-table">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">DPO Ctrl. No.</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Company</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Type</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">DPO Out</th>
                                                </tr>
                                            </thead>

                                            <tbody id="transfers-result-table-tbody">
                                                @forelse ($result['dpo_out'] as $dpo_out)
                                                    <tr>
                                                        <td class="text-center text-sm p-1 whitespace-nowrap">
                                                            {{ $dpo_out->DPOCNo }}
                                                        </td>
                                                        <td class="text-center text-td-buying text-sm p-1 whitespace-nowrap">
                                                            @if ($dpo_out->CompanyName != null)
                                                                {{ $dpo_out->CompanyName }}
                                                            @else
                                                                <strong><span>N/A</span></strong>
                                                            @endif
                                                        </td>
                                                        <td class="text-center text-sm p-1 whitespace-nowrap">
                                                            {{ $dpo_out->DPOType }}
                                                        </td>
                                                        <td class="text-right text-sm py-1 pe-2 whitespace-nowrap">
                                                            @if ($dpo_out->DollarOut != '0.00')
                                                                <span class="text-[#DC3545] font-bold text-xs">
                                                                    - {{ number_format($dpo_out->DollarOut, 2, '.', ',') }}
                                                                </span>
                                                            @elseif ($dpo_out->DollarOut == '0.00')
                                                                {{ number_format(0, 2, '.', ',') }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="text-center text-td-buying text-sm py-2" colspan="12" id="empty-receive-transf-table">
                                                            <span class="buying-no-transactions">
                                                                <strong>NO AVAILABLE ENTRIES</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3"></td>
                                                    <td class="text-right text-sm py-1 pe-2 whitespace-nowrap">
                                                        <span class="badge danger-badge-custom">
                                                            @if (count($result['dollar_out']) == 0)
                                                                - {{ number_format(0, 2, '.', ',') }}
                                                            @else
                                                                - {{ number_format(array_sum($result['dollar_out']), 2, '.', ',') }}
                                                            @endif
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br py-2">
                                        {{ $result['dpo_out']->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Wallet --}}
                    <div class="col-lg-3">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                {{-- <a class="primary-links" href="{{ route('admin_transactions.buffer.buffer_wallet') }}"> --}}
                                    <div class="card dahsboard-cards h-100">
                                        <div class="card-body py-4">
                                            <div class="row align-items-center text-left mb-2">
                                                <div class="col-1">
                                                    <i class='bx bx-coin-stack text-[#0D6EFD]'></i>
                                                </div>
                                                &nbsp;
                                                <div class="col-9">
                                                    <span class="!text-[#424242] text-md">Available DPOFX Balance</span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 text-left">
                                                    <span class="!text-[#424242] card-title text-2xl font-bold">&#36; </span>&nbsp;<span class="!text-[#424242] card-title text-2xl mb-3 font-bold">{{ number_format($result['current_balance']->Balance, 2, '.', ',') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                {{-- </a> --}}
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
