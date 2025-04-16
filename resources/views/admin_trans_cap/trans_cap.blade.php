@extends('template.layout')
@section('content')
   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-12">

                        @if (session()->has('message'))
                            <div class="alert alert-success alert-dismissible" role="alert" data-successexistence="1">
                                {{ session()->get('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>

                    <div class="col-12">
                        <hr>
                    </div>

                    {{-- Control Details - Wallet --}}
                    <div class="col-lg-12">
                        <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-1 text-black">
                                                    <i class='bx bxs-right-top-arrow-circle'></i>&nbspTransfer Capital
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
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Branch</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Transfer Amount</th>
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="transfers-result-table-tbody">
                                                @forelse ($result['trans_cap'] as $trans_cap)
                                                    <tr>
                                                        <td class="text-center text-xs p-1 whitespace-nowrap">
                                                            {{ $trans_cap->BranchCode }}
                                                        </td>
                                                        <td class="text-right text-xs py-1 pe-3 font-bold whitespace-nowrap">
                                                            {{ number_format($trans_cap->TranscapAmount, 2, '.', ',') }}
                                                        </td>
                                                        <td class="text-center text-xs p-1 whitespace-nowrap">
                                                            <button class="btn btn-primary button-edit pe-2 trans-cap-details" data-branchid="{{ $trans_cap->BranchID }}" data-branchcode="{{ $trans_cap->BranchCode }}" data-bs-toggle="modal" data-bs-target="#trans-cap-details-modal">
                                                                <i class='bx bx-detail'></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td class="text-center text-xs py-3" colspan="12" id="empty-receive-transf-table">
                                                            <span class="buying-no-transactions text-lg">
                                                                <strong>NO AVAILABLE ENTRIES</strong>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-12 px-3 border border-gray-300 rounded-bl rounded-br py-2">
                                        {{ $result['trans_cap']->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="trans-cap-details-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content trans-c-details">

            </div>
        </div>
    </div>

    @include('UI.UX.security_code')

@endsection

@section('trans_c_scripts')
    @include('script.trans_c_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
