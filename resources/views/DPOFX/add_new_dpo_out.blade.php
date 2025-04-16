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
                                                    <i class='bx bxs-badge-dollar'></i>&nbsp;DPOFX OUT
                                                </span>
                                            </div>

                                            <div class="col-6 text-end">
                                                <a class="btn btn-primary text-white btn-sm" href="{{ route('admin_transactions.dpofx.dpo_add_out') }}">
                                                    {{ trans('labels.dpo_add_transact_sell') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-sm font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_#') }}</th>
                                                <th class="text-center text-sm font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_date') }}</th>
                                                <th class="text-center text-sm font-extrabold text-black p-1 whitespace-nowrap">Customer</th>
                                                <th class="text-center text-sm font-extrabold text-black p-1 whitespace-nowrap">Transacted By</th>
                                                <th class="text-center text-sm font-extrabold text-black p-1 whitespace-nowrap">Dollar Amount</th>
                                                <th class="text-center text-sm font-extrabold text-black p-1 whitespace-nowrap">Selling Rate</th>
                                                <th class="text-center text-sm font-extrabold text-black p-1 whitespace-nowrap">Principal</th>
                                                <th class="text-center text-sm font-extrabold text-black p-1 whitespace-nowrap">Exchange Amount</th>
                                                <th class="text-center text-sm font-extrabold text-black p-1 whitespace-nowrap">Gain/Loss</th>
                                                <th class="text-center text-sm font-extrabold text-black p-1 whitespace-nowrap">Remarks</th>
                                                @can('access-permission', $menu_id)
                                                    <th class="text-center text-sm font-extrabold text-black p-1 whitespace-nowrap"></th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div id="transaction-details">
                                                @php
                                                    $total_dollar_amnt = 0;
                                                    $total_peso_amnt = 0;
                                                @endphp

                                                @forelse ($result['dpo_out'] as $dpo_out)
                                                    <tr>
                                                        <td class="text-center text-sm p-1">
                                                            {{ $dpo_out->DPOSellingNo }}
                                                        </td>
                                                        <td class="text-center text-sm p-1">
                                                            {{ \Carbon\Carbon::parse($dpo_out->EntryDate)->format('Y-m-d') }}
                                                        </td>
                                                        <td class="text-center text-sm p-1">
                                                            {{ $dpo_out->FullName }}
                                                        </td>
                                                        <td class="text-center text-sm p-1">
                                                            {{ $dpo_out->Name }}
                                                        </td>
                                                        <td class="text-right text-sm py-1 px-3">
                                                            {{ number_format($dpo_out->DollarAmount, 2, '.', ',') }}
                                                        </td>
                                                        <td class="text-right text-sm py-1 px-3">
                                                            {{ number_format($dpo_out->SellingRate, 2, '.', ',') }}
                                                        </td>
                                                        <td class="text-right text-sm py-1 px-3">
                                                            {{ number_format($dpo_out->Principal, 2, '.', ',') }}
                                                        </td>
                                                        <td class="text-right text-sm py-1 px-3">
                                                            {{ number_format($dpo_out->ExchangeAmount, 2, '.', ',') }}
                                                        </td>
                                                        <td class="text-right text-sm py-1 px-2">
                                                            <span class="badge @if ($dpo_out->GainLoss >= 0) success-badge-custom @else danger-badge-custom @endif">
                                                                @if ($dpo_out->GainLoss >= 0){{ trans('labels.gain_symbol') }}@else{{ trans('labels.loss_symbol') }}@endif{{ number_format(str_replace('-', '', $dpo_out->GainLoss), 2, '.', ',') }}  @if ($dpo_out->GainLoss >= 0) <i class='bx bxs-up-arrow pb-1' style="font-size: .5rem;"></i> @else <i class='bx bxs-down-arrow pb-1' style="font-size: .5rem;"></i> @endif
                                                            </span>
                                                        </td>
                                                        <td class="text-center text-sm p-1 " data-bs-toggle="popover" data-bs-content="{!! $dpo_out->Remarks == null ? 'No remarks.' : $dpo_out->Remarks !!}" data-bs-placement="bottom" data-bs-custom-class="popover-dark" tabindex="0">
                                                            @if ($dpo_out->Remarks == null)
                                                                -
                                                            @else
                                                                {{ \Illuminate\Support\Str::limit($dpo_out->Remarks, 14, '...') }}
                                                            @endif
                                                        </td>
                                                        @can('access-permission', $menu_id)
                                                            <td class="text-center text-sm p-1">
                                                                <a class="btn btn-primary button-edit dpo-out-details text-white pe-2" type="button" href="{{ route('admin_transactions.dpofx.dpo_out_details', ['id' => $dpo_out->DPODOID]) }}">
                                                                    <i class='bx bx-detail'></i>
                                                                </a>
                                                            </td>
                                                        @endcan
                                                    </tr>

                                                    {{-- @php
                                                        $total_dollar_amnt += $dpo_out->DollarAmount;
                                                        $total_peso_amnt += $dpo_out->Amount;
                                                    @endphp --}}
                                                @empty
                                                    <tr>
                                                        <td class="text-center p-1 text-sm py-3" colspan="13">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO DPOFX OUT AVAILABLE</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </div>
                                        </tbody>
                                        <tfoot>
                                            {{-- <tr>
                                                <td class="text-center p-1 py-1 whitespace-nowrap" colspan="1">
                                                    <span class="transaction-count" id="transaction-count">
                                                        {{ trans('labels.transact_count') }}: <span class="table-footer-texts font-semibold" id="trans-count">{{ count($result['dpo_out']) }}</span>
                                                    </span>
                                                </td>
                                                <td colspan="3"></td>
                                                <td class="text-right px-3 py-1 whitespace-nowrap" colspan="1">
                                                    <strong><span class="text-sm">&#36;&nbsp;{{ number_format($total_dollar_amnt, 2, '.', ',') }}</span></strong>
                                                </td>
                                                <td class="text-right px-3 py-1 whitespace-nowrap" colspan="1">
                                                    <strong><span class="text-sm">PHP&nbsp;{{ number_format($total_peso_amnt, 2, '.', ',') }}</span></strong>
                                                </td>
                                            </tr> --}}
                                        </tfoot>
                                    </table>

                                    <div class="col-12 py-1 px-3 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                {{ $result['dpo_out']->links() }}
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

@endsection

@section('buying_scripts')
    @include('script.dpo_transact_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
