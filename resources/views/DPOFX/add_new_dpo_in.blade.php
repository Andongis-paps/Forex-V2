@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <div class="row justify-content-center">


                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bxs-badge-dollar'></i>&nbsp;DPOFX IN
                                                </span>
                                            </div>

                                            <div class="col-6 text-end">
                                                @can('add-permission', $menu_id)
                                                    <a class="btn btn-primary text-white btn-sm" href="{{ route('admin_transactions.dpofx.dpo_add_in') }}">
                                                        {{ trans('labels.dpo_add_transact') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Entry Date</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_#') }}</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Company</th> --}}
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Processed By</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Dollar Amount</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Exchange Amount</th>
                                                @can('access-permission', $menu_id)
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Action</th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div id="transaction-details">
                                                @php
                                                    $total_dollar_amnt = 0;
                                                    $total_peso_amnt = 0;
                                                @endphp

                                                @forelse ($result['dpo_ins'] as $dpo_ins)
                                                    <tr>
                                                        <td class="text-center text-xs p-1">
                                                            {{ \Carbon\Carbon::parse($dpo_ins->EntryDate)->format('Y-m-d') }}
                                                        </td>
                                                        <td class="text-center text-xs p-1">
                                                            {{ $dpo_ins->DPDNo }}
                                                        </td>
                                                        {{-- <td class="text-center text-xs p-1">
                                                            {{ $dpo_ins->CompanyName }}
                                                        </td> --}}
                                                        <td class="text-center text-xs p-1">
                                                            {{ $dpo_ins->Name }}
                                                        </td>
                                                        <td class="text-right text-xs py-1 px-3">
                                                            {{ number_format($dpo_ins->DollarAmount, 2, '.', ',') }}
                                                        </td>
                                                        <td class="text-right text-xs py-1 px-3">
                                                            {{ number_format($dpo_ins->Amount, 2, '.', ',') }}
                                                        </td>
                                                        @can('access-permission', $menu_id)
                                                            <td class="text-center text-xs p-1">
                                                                <button class="btn btn-primary button-edit dpo-in-details text-white pe-2" type="button" data-dpdid="{{ $dpo_ins->DPDID }}" data-bs-toggle="modal" data-bs-target="#dpo-in-details-modal">
                                                                    <i class='bx bx-detail'></i>
                                                                </button>
                                                            </td>
                                                        @endcan
                                                    </tr>

                                                    @php
                                                        $total_dollar_amnt += $dpo_ins->DollarAmount;
                                                        $total_peso_amnt += $dpo_ins->Amount;
                                                    @endphp
                                                @empty
                                                    <tr>
                                                        <td class="text-center p-1 text-sm py-3" colspan="13">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO DPOFX IN AVAILABLE</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </div>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="text-center p-1 py-1 whitespace-nowrap" colspan="1">
                                                    <span class="text-sm" id="transaction-count">
                                                        {{ trans('labels.transact_count') }}: <span class="font-semibold" id="trans-count">{{ count($result['dpo_ins']) }}</span>
                                                    </span>
                                                </td>
                                                <td colspan="2"></td>
                                                <td class="text-right px-3 py-1 whitespace-nowrap" colspan="1">
                                                    <strong><span class="text-sm">&#36;&nbsp;{{ number_format($total_dollar_amnt, 2, '.', ',') }}</span></strong>
                                                </td>
                                                <td class="text-right px-3 py-1 whitespace-nowrap" colspan="1">
                                                    <strong><span class="text-sm">PHP&nbsp;{{ number_format($total_peso_amnt, 2, '.', ',') }}</span></strong>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <div class="col-12 py-1 px-3 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                {{ $result['dpo_ins']->links() }}
                                            </div>
                                        </div>
                                    </div>

                                    @include('UI.UX.security_code')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="dpo-in-details-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-xl ">
            <div class="modal-content add-denom">
                @include('DPOFX.dpo_in_details_modal')
            </div>
        </div>
    </div>

@endsection

@section('buying_scripts')
    @include('script.dpo_transact_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
