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
                                                    <i class='bx bxs-badge-dollar' ></i>&nbsp;{{ trans('labels.buying_trans_title') }}
                                                </span>
                                            </div>

                                            <div class="col-6 text-end">
                                                @can('add-permission', $menu_id)
                                                    <a class="btn btn-primary btn-sm text-white" href="{{ route('admin_transactions.admin_b_transaction.add') }}">
                                                        {{ trans('labels.forex_return_buying') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_date') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_#') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_invoice_#') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_customer_name') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_curr') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_type') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_curr_amnt') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_amnt') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Remarks</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap @if (session('time_toggle_status') == 1) d-none @else @endif">{{ trans('labels.transact_rset') }}</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Status</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.forex_serials_title') }}</th> --}}
                                                @can('access-permission', $menu_id)
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap"></th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <div id="transaction-details">
                                                @if (count($result['transact_details']) > 0)
                                                    @if (session('time_toggle_status') == 1)
                                                        @foreach ($result['transact_details'] as $transact_details)
                                                            @php
                                                                $report_status = $transact_details->Voided == 0;
                                                            @endphp

                                                            <tr class="transact-details-list-table @if($transact_details->Voided == 1) !bg-red-100 @endif" id="transact-details-list-table">
                                                                <td class="text-center text-sm p-1">
                                                                    {{ $transact_details->TransactionDate }}
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    {{ $transact_details->TransactionNo }}
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    @if ($transact_details->Rset == 'O')
                                                                        {{ $transact_details->ORNo }}
                                                                    @else
                                                                        0
                                                                    @endif
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    {{ $transact_details->FullName }}
                                                                </td>
                                                                <td class="text-center text-sm p-1 whitespace-nowrap">
                                                                    {{ $transact_details->Currency }}
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    {{ $transact_details->TransType }}
                                                                </td>
                                                                <td class="text-right text-sm p-1">
                                                                    {{ number_format($transact_details->CurrencyAmount, 2, '.', ',') }}
                                                                </td>
                                                                <td class="text-right text-sm py-1 px-3">
                                                                    {{ number_format($transact_details->Amount, 2, '.', ',') }}
                                                                    @if ($transact_details->Voided == 0)
                                                                        <input type="hidden" class="currency-amount" id="currency-amount" value="{{ $transact_details->Amount }}">
                                                                    @else
                                                                        <input type="hidden" class="currency-amount" id="currency-amount" value="0">
                                                                    @endif
                                                                </td>
                                                                <td class="text-center text-sm p-1" data-bs-toggle="popover" data-bs-content="{!! $transact_details->Remarks == null ? 'No remarks.' : $transact_details->Remarks !!}" data-bs-placement="bottom" data-bs-custom-class="popover-dark" tabindex="0">
                                                                    @if ($transact_details->Remarks == null)
                                                                        -
                                                                    @else
                                                                        {{ \Illuminate\Support\Str::limit($transact_details->Remarks, 14, '...') }}
                                                                    @endif
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    @if ($transact_details->Voided == 1)
                                                                        <span class="badge bg-label-danger text-sm font-bold">
                                                                            Void
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-label-success font-bold">
                                                                            <i class='bx bx-check-double'></i>
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center p-1 whitespace-nowrap">
                                                                    @can('access-permission', $menu_id)
                                                                        @if ($transact_details->Voided == 0)
                                                                            <a class="btn btn-primary button-edit button-edit-trans-details text-white pe-2 ms-1" href="{{ route('admin_transactions.admin_b_transaction.details', ['id' => $transact_details->AFTDID]) }}">
                                                                                <i class='bx bx-detail'></i>
                                                                            </a>

                                                                            @if (is_array($transact_details->serials) && count(array_filter($transact_details->serials, 'strlen')) !== count($transact_details->serials))
                                                                                <span class="badge translate-middle pending-badge pending-badge-danger"><i class='bx bxs-info-circle bx-flashing badge-icon'></i></span>
                                                                            @else
                                                                                <span class="badge translate-middle pending-badge pending-badge-success"><i class='bx bx-check-double badge-icon'></i></span>
                                                                            @endif
                                                                        @endif
                                                                    @endcan
                                                                    @can('delete-permission', $menu_id)
                                                                        @if ($transact_details->Voided == 0)

                                                                            {{-- <a class="btn btn-primary button-edit button-edit-trans-details" id="button-trans-details" href="{{ route('editbuyingtransact', ['id' => $transact_details->AFTDID]) }}">
                                                                                <i class='menu-icon tf-icons bx bx-edit-alt text-white'></i>
                                                                            </a> --}}
                                                                            <a class="btn btn-primary button-delete button-delete-trans-details pe-2 -ms-3" data-transdetailsid="{{ $transact_details->AFTDID }}">
                                                                                <i class='bx bx-trash text-white'></i>
                                                                            </a>
                                                                        @endif
                                                                    @endcan
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        @foreach ($result['transact_details'] as $transact_details)
                                                            <tr class="transact-details-list-table @if($transact_details->Voided == 1) !bg-red-100 @endif" id="transact-details-list-table">
                                                                <td class="text-center text-sm p-1">
                                                                    {{ $transact_details->TransactionDate }}
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    {{ $transact_details->TransactionNo }}
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    @if ($transact_details->Rset == 'O')
                                                                        {{ $transact_details->ORNo }}
                                                                    @else
                                                                        0
                                                                    @endif
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    {{ $transact_details->FullName }}
                                                                </td>
                                                                <td class="text-center text-sm p-1 whitespace-nowrap">
                                                                    {{ $transact_details->Currency }}
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    {{ $transact_details->TransType }}
                                                                </td>
                                                                <td class="text-right text-sm p-1">
                                                                    {{ number_format($transact_details->CurrencyAmount, 2, '.', ',') }}
                                                                </td>
                                                                <td class="text-right text-sm py-1 px-3">
                                                                    {{ number_format($transact_details->Amount, 2, '.', ',') }}
                                                                    @if ($transact_details->Voided == 0)
                                                                        <input type="hidden" class="currency-amount" id="currency-amount" value="{{ $transact_details->Amount }}">
                                                                    @else
                                                                        <input type="hidden" class="currency-amount" id="currency-amount" value="0">
                                                                    @endif
                                                                </td>
                                                                <td class="text-center text-sm p-1 " data-bs-toggle="popover" data-bs-content="{!! $transact_details->Remarks == null ? 'No remarks.' : $transact_details->Remarks !!}" data-bs-placement="bottom" data-bs-custom-class="popover-dark" tabindex="0">
                                                                    @if ($transact_details->Remarks == null)
                                                                        -
                                                                    @else
                                                                        {{ \Illuminate\Support\Str::limit($transact_details->Remarks, 14, '...') }}
                                                                    @endif
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    {{ $transact_details->Rset }}
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    @if ($transact_details->Voided == 1)
                                                                        <span class="badge bg-label-danger text-sm font-bold">
                                                                            Void
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-label-success font-bold">
                                                                            <i class='bx bx-check-double'></i>
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center p-1 whitespace-nowrap">
                                                                    @can('access-permission', $menu_id)
                                                                        @if ($transact_details->Voided == 0)
                                                                            <a class="btn btn-primary button-edit button-edit-trans-details text-white pe-2 ms-1" href="{{ route('admin_transactions.admin_b_transaction.details', ['id' => $transact_details->AFTDID]) }}">
                                                                                <i class='bx bx-detail'></i>
                                                                            </a>

                                                                            @if (is_array($transact_details->serials) && count(array_filter($transact_details->serials, 'strlen')) !== count($transact_details->serials))
                                                                                <span class="badge translate-middle pending-badge pending-badge-danger"><i class='bx bxs-info-circle bx-flashing badge-icon'></i></span>
                                                                            @else
                                                                                <span class="badge translate-middle pending-badge pending-badge-success"><i class='bx bx-check-double badge-icon'></i></span>
                                                                            @endif
                                                                        @endif
                                                                    @endcan
                                                                    @can('delete-permission', $menu_id)
                                                                        @if ($transact_details->Voided == 0)

                                                                            {{-- <a class="btn btn-primary button-edit button-edit-trans-details" id="button-trans-details" href="{{ route('editbuyingtransact', ['id' => $transact_details->AFTDID]) }}">
                                                                                <i class='menu-icon tf-icons bx bx-edit-alt text-white'></i>
                                                                            </a> --}}
                                                                            <a class="btn btn-primary button-delete button-delete-trans-details pe-2 -ms-3" data-transdetailsid="{{ $transact_details->AFTDID }}">
                                                                                <i class='bx bx-trash text-white'></i>
                                                                            </a>
                                                                        @endif
                                                                    @endcan
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                @else
                                                    <tr>
                                                        <td class="text-center p-1 text-sm py-3" colspan="13">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO AVAILABLE TRANSACTIONS FOR TODAY</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </div>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td class="text-center p-1 py-1 whitespace-nowrap" colspan="1">
                                                    <span class="transaction-count" id="transaction-count">
                                                        {{ trans('labels.transact_count') }}: <span class="table-footer-texts font-bold" id="trans-count">{{ count($result['transact_details']) }}</span>
                                                    </span>
                                                </td>
                                                <td class="text-center p-1" colspan="6">
                                                </td>
                                                <td class="text-right py-1 px-3 whitespace-nowrap" colspan="1">
                                                    <span class="transaction-total-amount" id="transaction-total-amount">
                                                        {{ trans('labels.transact_total_amount') }}: <strong><span class="text-sm">PHP</span></strong>&nbsp; <span class="table-footer-texts font-bold" id="trans-amount"></span>
                                                    </span>
                                                </td>
                                                <td class="text-center p-1" colspan="3">
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <div class="col-12 py-1 px-3 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                {{ $result['transact_details']->links() }}
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

@section('admin_buying_scripts')
    @include('script.admin_add_b_transact_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
