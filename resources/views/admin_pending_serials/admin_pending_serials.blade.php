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
                                <div class="card mb-3">
                                    <div class="col-12 border border-gray-300 rounded-tr rounded-tl p-2">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-list-ul' ></i>&nbsp;{{ trans('labels.pending_serials') }}
                                                </span>
                                            </div>

                                            <div class="col-6 text-end">
                                                {{-- <a class="btn btn-primary text-white" href="{{ URL::to('/buyingTransact') }}">
                                                    {{ trans('labels.forex_return_buying') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                                </a> --}}
                                                @can('add-permission', $menu_id)
                                                    <button class="btn btn-primary btn-sm" type="button" id="submit-peding-serials" @if (count($result['pending_serials']) == 0) disabled @endif>
                                                        <i class='bx bxs-save ps-0 pe-1'></i>{{ trans('labels.save_action') }}
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <form class="m-0" action="{{ route('admin_transactions.admin_b_transaction.save_serials') }}" method="post" id="admin-pending-serials-form">
                                            @csrf

                                            @php
                                                $count = count($result['pending_serials']);
                                            @endphp

                                            <div @if ($count <= 15) id="a-pending-few" @else id="a-pending-lot" @endif>
                                                <table class="table table-bordered table-hover">
                                                    <thead class="sticky-header">
                                                        <tr>
                                                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Entry Date</th>
                                                            {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.transact_#') }}</th> --}}
                                                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.serials_currency') }}</th>
                                                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Type</th>
                                                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.serials_bill_amount') }}</th>
                                                            <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.serials_serials') }}</th>
                                                            {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.serials_trans_type') }}</th> --}}
                                                            {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">{{ trans('labels.action_data') }}</th> --}}
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($result['pending_serials'] as $pending_serials)
                                                            <tr class="transact-details-list-table" id="transact-details-list-table">
                                                                {{-- <td class="text-center text-sm p-1">
                                                                    {{ $pending_serials->TransactionDate }}
                                                                </td> --}}
                                                                {{-- <td class="text-center text-sm p-1">
                                                                    {{ $pending_serials->TransactionNo }}
                                                                </td> --}}
                                                                <td class="text-center text-sm p-1">
                                                                    {{ \Carbon\Carbon::parse($pending_serials->EntryDate)->toDateString() }}
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    {{ $pending_serials->Currency }}
                                                                </td>
                                                                <td class="text-center text-xs p-1">
                                                                    @if ($pending_serials->Buffer == 1)
                                                                        <span class="badge success-badge-custom">Buffer</span>
                                                                    @else
                                                                        <span class="badge primary-badge-custom">Regular</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-right text-sm py-1 px-3">
                                                                    {{ number_format($pending_serials->BillAmount , 2 , '.' , ',') }}
                                                                </td>
                                                                <td class="text-center text-sm p-1">
                                                                    {{-- <div class="input-group input-group-serials"> --}}
                                                                        <input class="form-control serials-input" name="serials[]" id="" type="text" value="{{ old('serials') ?? $pending_serials->Serials }}" data-fsid="{{ $pending_serials->ID }}" autocomplete="off">
                                                                        {{-- <div class="input-group-text p-1">
                                                                            <label class="checkbox-label checkbox-enable">
                                                                                <input class="form-check-input mt-0 enable-serial-field" id="enable-serial-field" type="checkbox" data-fxfsid="">
                                                                                <i class="bx bx-sm bx-toggle-left text-red ms-1"></i>
                                                                                <i class="bx bx-sm bxs-toggle-right text-green ms-1"></i>
                                                                            </label>
                                                                        </div> --}}
                                                                    </div>
                                                                </td>

                                                                {{-- <input type="hidden" name="forex-ftdid" value="{{ $pending_serials->AFTDID }}"> --}}
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td class="text-center text-td-buying text-sm py-3" colspan="13">
                                                                    <span class="buying-no-transactions text-lg">
                                                                        <strong>NO PENDING SERIALS</strong>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                    {{-- <tfoot>
                                                        <tr>
                                                            <td class="text-end" colspan="10">
                                                                {{ $result['pending_serials']->links() }}
                                                            </td>
                                                        </tr>
                                                    </tfoot> --}}
                                                </table>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="col-12 border border-gray-300 rounded-br rounded-bl p-2">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                {{-- <input type="hidden" id="pending-serial-url" data-transactpendingserials="{{ route('transact_pending_serials', ['id' => $pending_serials->FTDID]) }}"> --}}
                                                {{-- <input type="hidden" id="forex-serial-url" data-forexserials="{{ route('buyingtransaction', ['id' => $pending_serials->FTDID]) }}"> --}}
                                            </div>
                                            <div class="col-lg-6 text-end">
                                                {{-- <a class="btn btn-secondary text-white" type="button" href="{{ route('buyingtransaction', ['id' => $pending_serials->FTDID]) }}">{{ trans('labels.back_action') }}</a> --}}
                                            </div>
                                        </div>
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

@endsection

@section('buying_scripts')
    @include('script.a_pending_serials_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
