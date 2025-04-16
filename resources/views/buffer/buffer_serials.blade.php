@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-lg-12 mb-4">
                        <div class="col-12">
                            <hr>
                        </div>

                        <div class="card mb-3">
                            <div class="col-12 border border-gray-300 rounded-tr rounded-tl p-2">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <span class="text-lg font-bold p-2 text-black">
                                            <i class='bx bx-time-five'></i>&nbsp;{{ trans('labels.add_pending_serials_title') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <form class="m-0" method="post" id="pending-serials-form">
                                    @csrf

                                    @php
                                        $count = count($result['pending_serials']);
                                    @endphp

                                    <div @if ($count <= 15) id="a-pending-few" @else id="a-pending-lot" @endif>
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_currency') }}</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_bill_amount') }}</th>
                                                    {{-- <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_trans_type') }}</th> --}}
                                                    <th class="text-center text-xs font-extrabold text-black p-1">{{ trans('labels.serials_serials') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($result['pending_serials'] as $pending_serials)
                                                    <tr class="transact-details-list-table" id="transact-details-list-table">
                                                        <td class="text-center text-xs font-semibold p-1">
                                                            {{ $pending_serials->Currency }}
                                                        </td>
                                                        <td class="text-right text-xs py-1 px-3">
                                                            {{ number_format($pending_serials->BillAmount , 2 , '.' , ',') }}
                                                        </td>
                                                        {{-- <td class="text-center text-xs p-1">
                                                            {{ $pending_serials->TransType }}
                                                        </td> --}}
                                                        <td class="text-center text-xs p-1">
                                                            <input class="form-control serials-input" name="serials[]" id="" type="text" value="{{ old('serials') ?? $pending_serials->Serials }}" data-fsid="{{ $pending_serials->AFSID }}" autocomplete="off">
                                                        </td>
                                                        <input type="hidden" name="forex-ftdid" value="{{ $pending_serials->BFID }}">
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
                                        </table>
                                    </div>
                                </div>

                                <div class="col-12 border border-gray-300 rounded-br rounded-bl p-2">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            {{-- <input type="hidden" id="pending-serial-url" data-transactpendingserials="{{ route('admin_transactions.admin_b_transaction.serials', ['id' => $pending_serials->BFID]) }}"> --}}
                                            <input type="hidden" id="forex-serial-url" data-forexserials="{{ route('admin_transactions.buffer.break_d_finance', ['BFID' => $pending_serials->BFID]) }}">
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            @can('access-permission', $menu_id)
                                                <a class="btn btn-secondary btn-sm text-white" type="button" href="{{ route('admin_transactions.buffer.break_d_finance', ['BFID' => $pending_serials->BFID]) }}">{{ trans('labels.back_action') }}</a>
                                            @endcan
                                            @can('add-permission', $menu_id)
                                                <button class="btn btn-primary btn-sm" type="button" id="submit-peding-serials">{{ trans('labels.confirm_action') }}</button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @include('UI.UX.security_code')
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('buying_scripts')
    @include('script.admin_pending_serials_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>

