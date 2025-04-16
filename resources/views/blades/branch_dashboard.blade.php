@extends('template.layout')
@section('content')
    @php
        use Carbon\Carbon;

        $raw_date = Carbon::now('Asia/Manila');
    @endphp

    <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-lg-12 mb-4">
                        <div class="row">
                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-6 pe-0">
                                <div class="container ps-3">
                                    <div class="row" id="transact-cards">
                                        <div class="col-6 ps-0">
                                            <a class="dashboard-links !text-[#6d757c]" href="{{ route('branch_transactions.buying_transaction') }}">
                                                <div class="card dahsboard-cards">
                                                    <div class="card-body py-3">
                                                        {{-- <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                                            <div class="avatar flex-shrink-0">
                                                                <i class='bx bxs-dollar-circle bx-lg'></i>
                                                            </div>
                                                        </div> --}}
                                                        @forelse ($result['buying_sales'] as $buying_sales)
                                                            <div class="row align-items-center text-left mb-2">
                                                                <div class="col-1 ps-2">
                                                                    <div class="avatar flex-shrink-0">
                                                                        <span class="avatar-initial rounded bg-label-green"><i class='bx bxs-dollar-circle'></i></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-9 ps-4">
                                                                    <span class="text-md font-semibold">Buying Sales</span>
                                                                </div>
                                                            </div>
                                                            {{-- <div class="row align-items-center text-left mb-2">
                                                                <div class="col-1">
                                                                    <i class='bx bxs-dollar-circle'></i>
                                                                </div>
                                                                &nbsp;
                                                                <div class="col-9">
                                                                    <span class="text-md">Buying Sales</span>
                                                                </div>
                                                            </div> --}}
                                                            {{-- <h4 class="card-title mb-3"></h4> --}}
                                                            <div class="row mb-2">
                                                                <div class="col-12 text-left">
                                                                    <span class="card-title text-2xl font-bold">PHP</span>&nbsp;<span class="card-title text-2xl mb-3 font-bold">{{ number_format($buying_sales->Amount, 2, '.', ',') }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12 text-left">
                                                                    <medium class="text-black fw-medium"><span>Transaction count:</span>&nbsp;&nbsp;<span class="font-semibold text-green-600" id="buying-transaction-count">{{ $buying_sales->transct_count }}</span></medium>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="row text-left mb-2">
                                                                <div class="col-12">
                                                                    <i class='bx bxs-dollar-circle'></i>&nbsp;&nbsp;<span class="mb-2 text-md">Buying Sales</span>
                                                                    {{-- <span class="mb-2 text-md">Buying Transcation Sales</span> --}}
                                                                </div>
                                                            </div>
                                                            {{-- <h4 class="card-title mb-3"></h4> --}}
                                                            <div class="row mb-2">
                                                                <div class="col-12 text-left">
                                                                    <span class="card-title text-2xl font-bold">PHP</span>&nbsp;<span class="card-title text-2xl mb-3 font-bold">0.00</span>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12 text-left">
                                                                    <medium class="!text-[#6d757c] fw-medium"><span>Transaction count:</span>&nbsp;&nbsp;<span class="font-semibold text-green-600" id="buying-transaction-count">0</span></medium>
                                                                </div>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="col-6 pe-0">
                                            <a class="dashboard-links !text-[#6d757c]" href="{{ route('branch_transactions.selling_transaction') }}">
                                                <div class="card dahsboard-cards">
                                                    <div class="card-body py-3">
                                                        {{-- <div class="card-title d-flex align-items-start justify-content-between b-4">
                                                            <div class="avatar flex-shrink-0">m
                                                                <i class='bx bxs-dollar-circle bx-lg'></i>
                                                            </div>
                                                        </div> --}}
                                                        @forelse ($result['selling_sales'] as $selling_sales)
                                                            <div class="row align-items-center text-left mb-2">
                                                                <div class="col-1 ps-2">
                                                                    <div class="avatar flex-shrink-0">
                                                                        <span class="avatar-initial rounded bg-label-green"><i class='bx bxs-dollar-circle'></i></span>
                                                                    </div>
                                                                </div>
                                                                <div class="col-9 ps-4">
                                                                    <span class="text-md font-semibold">Selling Sales</span>
                                                                </div>
                                                            </div>
                                                            {{-- <div class="row align-items-center text-left mb-2">
                                                                <div class="col-1">
                                                                    <i class='bx bxs-dollar-circle'></i>
                                                                </div>
                                                                &nbsp;
                                                                <div class="col-9">
                                                                    <span class="text-md">Selling Sales</span>
                                                                </div>
                                                            </div> --}}
                                                            {{-- <h4 class="card-title mb-3"></h4> --}}
                                                            <div class="row mb-2">
                                                                <div class="col-12 text-left">
                                                                    <span class="card-title text-2xl font-bold">PHP</span>&nbsp;&nbsp;<span class="card-title text-2xl mb-3 font-bold">{{ number_format($selling_sales->Amount, 2, '.', ',') }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12 text-left">
                                                                    <medium class="text-black fw-medium"><span>Transaction count:</span>&nbsp;&nbsp;<span class="font-semibold text-green-600" id="buying-transaction-count">{{ $selling_sales->transct_count }}</span></medium>
                                                                </div>
                                                            </div>
                                                        @empty
                                                             <div class="row text-left mb-2">
                                                                <div class="col-12">
                                                                    <i class='bx bxs-dollar-circle'></i>&nbsp;&nbsp;<span class="mb-2 text-md">Selling Sales</span>
                                                                    {{-- <span class="mb-2 text-md">Buying Transcation Sales</span> --}}
                                                                </div>
                                                            </div>
                                                            {{-- <h4 class="card-title mb-3"></h4> --}}
                                                            <div class="row mb-2">
                                                                <div class="col-12 text-left">
                                                                    <span class="card-title text-2xl font-bold">PHP</span>&nbsp;&nbsp;<span class="card-title text-2xl mb-3 font-bold">0.00</span>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12 text-left">
                                                                    <medium class="!text-[#6d757c] fw-medium"><span>Transaction count:</span>&nbsp;&nbsp;<span class="font-semibold text-green-600" id="buying-transaction-count">0</span></medium>
                                                                </div>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                 
                                    <div class="row mt-3" id="transact-today-cards">
                                        <div class="col-12 px-0">
                                            <div class="card dahsboard-cards h-100">
                                                <div class="card-header pb-1 pt-3 mb-2 align-items-center">
                                                    <div class="row align-items-center text-left">
                                                        {{-- <div class="col-1 pe-0">
                                                            <i class='bx bx-history'></i>
                                                        </div> --}}
                                                        <div class="col-5 ps-3 text-start">
                                                            <span class="mb-2 font-semibold">Today's Transaction</span>
                                                        </div>
                                                        <div class="col-7 text-end">
                                                            <strong>{{ $raw_date->format('l') }}</strong>&nbsp;({{ $raw_date->format('F j, Y') }})
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body pb-3">
                                                    <div class="col-12 py-0 @if (count($result['transactions']) > 3) shadow-[inset_0_-7px_7px_-6px_rgba(0,0,0,0.3)] @endif" @if (count($result['transactions']) > 3) id="transaction-today-container" @endif>
                                                        <ul class="p-0 m-0">
                                                            @forelse ($result['transactions'] as $transactions)
                                                                <li class="d-flex align-items-center mb-2">
                                                                    <div class="row align-items-center avatar me-4 text-xs">
                                                                        <div class="col-12">
                                                                            <div class="avatar flex-shrink-0">
                                                                                <span class="avatar-initial rounded bg-label-secondary"><i class='bx bx-cart-add'></i></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                                        <div class="me-2">
                                                                            <small class="text-muted d-block">{{ $transactions->CurrAbbv }}</small>
                                                                            <span class="text-sm mb-0">{{ $transactions->Currency }}</span>
                                                                        </div>
                                                                        <div class="user-progress text-xs d-flex align-items-center gap-2">
                                                                            <span class="text-xs mb-0">
                                                                                {{ $transactions->User }}
                                                                            </span>
                                                                            @if ($transactions->source_type == 1)
                                                                                <span class="text-xs font-semibold text-blue-500 mb-0">
                                                                                    (Buying)
                                                                                </span>
                                                                            @else
                                                                                <span class="text-xs font-semibold text-blue-500 mb-0">
                                                                                    (Selling)
                                                                                </span>
                                                                            @endif
    
                                                                            <span class="text-xs @if ($transactions->Voided == 1) text-red-500 @else text-green-700 @endif font-bold mb-0">
                                                                                {{ number_format($transactions->total_curr_amnt, 2, '.', ',') }} @if ($transactions->Voided == 1) - @else + @endif
                                                                            </span>

                                                                            @if ($transactions->Voided == 1)
                                                                                <span class="badge rounded-pill danger-badge-custom p-1">
                                                                                    <strong>Voided</strong>
                                                                                </span>
                                                                            @endif
                                                                            {{-- <span class="text-xs text-blue-500">
                                                                                <span class="badge primary-badge-custom"><text class="font-bold">{{ $available_stocks->Cnt }}</text>@if ($available_stocks->Cnt > 1) pcs.@else pc.@endif</span>
                                                                            </span> --}}
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @empty
                                                                <li class="d-flex align-items-center mb-2">
                                                                    {{-- <div class="row align-items-center avatar me-3 text-xs">
                                                                        <i class='bx bx-dollar-circle bx-sm'></i>
                                                                    </div> --}}
                                                                    <div class="d-flex w-100 flex-wrap py-1 align-items-center justify-content-between gap-2">
                                                                        <div class="me-2 ps-1">
                                                                            <h6 class="fw-normal mb-0">No transaction(s) yet.</h6>
                                                                        </div>
                                                                        <div class="user-progress d-flex align-items-center gap-2">
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endforelse
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3" id="serial-chart-card"> 
                                        <div class="col-6 ps-0">
                                            @php
                                                $count = count($result['pending_serials']);
                                            @endphp
                                            
                                            <a class="@if ($count < 1) dashboard-links @else dashboard-links-serials @endif" href="{{ route('branch_transactions.pending_serials') }}">
                                                <div class="card dahsboard-cards">
                                                    <div class="card-body py-3 @if ($count < 1) pb-4 @else  @endif">
                                                        <div class="row align-items-center text-left mb-2">
                                                            {{-- <div class="col-1">
                                                                <i class='bx bx-barcode bx-sm @if ($count > 0) bx-flashing text-red-500 @else @endif'></i>
                                                            </div>
                                                            &nbsp; --}}
                                                            <div class="col-9">
                                                                <span class="mb-2 text-md font-semibold @if ($count > 0) text-red-500 @else @endif">Pending Serials</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 pe-3" @if ($count > 3) id="pending-serials-container" @endif>
                                                            <ul class="p-0 m-0">
                                                                @forelse ($result['pending_serials'] as $pending_serials)
                                                                    <li class="d-flex align-items-center mb-2">
                                                                        <div class="col-1 me-3">
                                                                            <div class="avatar flex-shrink-0">
                                                                                <span class="avatar-initial rounded @if ($count > 0) bx-flashing text-red-500 bg-label-red @else bg-label-secondary @endif"><i class='bx bx-barcode bx-sm @if ($count > 0) bx-flashing text-red-500 @else @endif'></i></span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2 mt-2">
                                                                            <div class="me-2">
                                                                                <small class="text-muted d-block"><strong>{{ $pending_serials->CurrAbbv }}</strong></small>
                                                                                <h6 class="fw-normal text-xs mb-0">{{ $pending_serials->Currency }}</h6>
                                                                            </div>
                                                                            <div class="user-progress d-flex align-items-center gap-2">
                                                                                <span class="text-xs text-red-500">
                                                                                    <span class="badge danger-badge-custom"><text class="font-bold">{{ $pending_serials->serial_count }}</text>@if ($pending_serials->serial_count > 1) pcs. @else pc. @endif</span>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                @empty
                                                                    <li class="d-flex align-items-center">
                                                                        {{-- <div class="row align-items-center avatar me-3 text-xs">
                                                                            <i class='bx bx-check-double bx-sm text-green-700'></i>
                                                                        </div> --}}
                                                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                                            <div class="me-2">
                                                                                <h6 class="fw-normal text-black mb-0">No pending serials.</h6>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                @endforelse
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>

                                        <div class="col-6 pe-0">
                                            <div class="card dahsboard-cards">
                                                <div class="card-body pt-3 pb-0">
                                                    <div class="row align-items-center text-left">
                                                        {{-- <div class="col-1">
                                                            <i class='bx bx-list-ul'></i>
                                                        </div> --}}
                                                        &nbsp;
                                                        <div class="col-11">
                                                            <span class="text-md font-semibold text-black">Buying Sales Breakdown</span>
                                                        </div>
                                                    </div>
    
                                                    <div class="row align-items-center text-left">
                                                        <div class="col-12 p-0" id="has-transaction">
                                                            <div class="p-0" id="chart"></div>
                                                        </div>
    
                                                        <div class="col-12 mt-2 pb-4 ps-3" id="has-no-transaction">
                                                            <h6 class="fw-normal mb-0"><span class="text-black">No transaction(s) yet.</span></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3" id="fake-bills-card">
                                        <div class="col-lg-12 px-0">
                                            <div class="card dahsboard-cards mb-2">
                                                <div class="card-body py-2">
                                                    <div class="row align-items-center">
                                                        <div class="col-1 ps-2 pe-0">
                                                            <div class="avatar flex-shrink-0">
                                                                <span class="avatar-initial rounded bg-label-yellow"><i class='bx bx-error'></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-6 ps-0">
                                                            <span class="text-black text-lg font-bold">
                                                                Fake & Tagged Bills
                                                            </span>
                                                        </div>
                                     
                                                        {{-- <div class="col-6 text-end">
                                                            <strong>{{ $raw_date->format('F j, Y') }}</strong>&nbsp;({{ $raw_date->format('l') }})&nbsp;<text class="font-semibold text-[#0D6EFD]">as of <u>{{ $raw_date->format('h:i A') }}</u></text>
                                                        </div> --}}
                                                    </div>
                                                </div>
                                            </div>

                                            @if (count($result['tagged_bills']) > 0) 
                                                <div class="swiper tagged-b-swiper" id="tagged-b-swiper">
                                                    <div class="swiper-wrapper">
                                                        @foreach ($result['tagged_bills'] as $tagged_bills)
                                                            <div class="swiper-slide">
                                                                <div class="card dahsboard-cards">
                                                                    <div class="card-body p-2">
                                                                        <table class="table table-bordered table-hover">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th class="text-center text-xs font-bold text-black p-1 whitespace-nowrap">Front Bill Image</th>
                                                                                    <th class="text-center text-xs font-bold text-black p-1 whitespace-nowrap">Back Bill Image</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td class="text-center text-sm p-1 whitespace-nowrap">
                                                                                        <img class="curr-swiper-main-pictures" src="{{ asset('storage/' . $tagged_bills->FrontBillImage) }}" alt="">
                                                                                    </td>
                                                                                    <td class="text-center text-sm p-1 whitespace-nowrap">
                                                                                        <img class="curr-swiper-main-pictures" src="{{ asset('storage/' . $tagged_bills->BackBillImage) }}" alt="">
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                        @if ($tagged_bills->Remarks)
                                                                            <table class="table table-bordered table-hover mt-3">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th class="text-center text-xs font-bold text-black p-1 whitespace-nowrap">Remarks</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <td class="text-center text-sm p-1">
                                                                                            <span class="text-sm text-black">
                                                                                                {{ $tagged_bills->Remarks }}
                                                                                            </span>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        @endif
                                                                        <table class="table table-bordered table-hover mt-3">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th class="text-center text-xs font-bold text-black p-1 whitespace-nowrap">Tags</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td class="text-center text-sm p-1 whitespace-nowrap">
                                                                                        @foreach ($tagged_bills->BillTags as $saved_bill_tags)
                                                                                            <span class="badge success-badge-custom">
                                                                                                {{ $saved_bill_tags->BillStatus }}
                                                                                            </span>
                                                                                            {{-- <option class="select-options font-bold" @if($saved_bill_tags->BillStatID == $bill_tags->BillStatID) selected @endif>
                                                                                                @if($saved_bill_tags->BillStatID == $bill_tags->BillStatID) {{ $bill_tags->BillStatus }} @endif
                                                                                            </option> --}}
                                                                                        @endforeach
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    {{-- <div class="swiper-button-next"></div> --}}
                                                    {{-- <div class="swiper-button-prev"></div> --}}
                                                    <div class="swiper-pagination"></div>
                                                </div>
                                            @else
                                                <div class="card dahsboard-cards">
                                                    <div class="card-body py-2">
                                                        <div class="row">
                                                            <div class="col-12 text-center">
                                                                <span class="text-black">
                                                                    Nothing to see here.
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 ps-0">
                                <div class="container pe-3">
                                    <div class="row" id="rates-card">
                                        <div class="card dahsboard-cards mb-2">
                                            <div class="card-body py-2 px-1">
                                                <div class="row align-items-center">
                                                    <div class="col-6">
                                                        <div class="row align-items-center">
                                                            {{-- <div class="col-1 ps-2">
                                                                <div class="avatar flex-shrink-0">
                                                                    <span class="avatar-initial rounded bg-label-blue"><i class='bx bx-candles'></i></span>
                                                                </div>
                                                            </div> --}}
                                                            <div class="col-6">
                                                                <span class="text-black text-lg font-bold">
                                                                    Current Rates
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <strong>{{ $raw_date->format('F j, Y') }}</strong>&nbsp;({{ $raw_date->format('l') }}) as of&nbsp;<text class="font-bold text-[#0D6EFD]" id="clock"></text>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card dahsboard-cards p-1">
                                            <div class="card-bodyp p-1">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center text-xs font-bold text-black p-1 whitespace-nowrap w-25">Currency</th>
                                                            <th class="text-center text-xs font-bold text-black p-1 whitespace-nowrap w-25">Buying Rate</th>
                                                            <th class="text-center text-xs font-bold text-black p-1 whitespace-nowrap w-25">Selling Rate</th>
                                                            <th class="text-center text-xs font-bold text-black p-1 whitespace-nowrap w-25">Denominations</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($result['priority_rates'] as $priority_rates)
                                                            @php
                                                                $exploded_denom =  array_filter(explode(' - ', $priority_rates->Denomination));

                                                                // Buying rates
                                                                $b_formatted_rate = '';
                                                                $rate_used = $priority_rates->whole_b_rate + $priority_rates->b_decimal_rate;
                                                                $decimal_places = (strpos((string) $rate_used, '.') !== false) ? strlen(explode('.', $rate_used)[1]) : 0;

                                                                if ($decimal_places <= 2 && !in_array($priority_rates->CurrencyID, [12, 14, 31])) {
                                                                    $b_formatted_rate = number_format(floor($rate_used * 100) / 100, 2);
                                                                } else if ($decimal_places <= 4 && in_array($priority_rates->CurrencyID, [12, 14, 31])) {
                                                                    $b_formatted_rate = number_format(floor($rate_used * 100000) / 100000, 4, '.', ',');
                                                                }

                                                                 // Selling rates
                                                                $s_formatted_rate = '';
                                                                $rate_used = $priority_rates->whole_s_rate + $priority_rates->s_decimal_rate;
                                                                $decimal_places = (strpos((string) $rate_used, '.') !== false) ? strlen(explode('.', $rate_used)[1]) : 0;

                                                                if ($decimal_places <= 2 && !in_array($priority_rates->CurrencyID, [12, 14, 31])) {
                                                                    $s_formatted_rate = number_format(floor($rate_used * 100) / 100, 2);
                                                                } else if ($decimal_places <= 4 && in_array($priority_rates->CurrencyID, [12, 14, 31])) {
                                                                    $s_formatted_rate = number_format(floor($rate_used * 100000) / 100000, 4, '.', ',');
                                                                }
                                                            @endphp
                                                            
                                                            <tr>
                                                                <td class="text-center text-sm p-1 whitespace-nowrap">
                                                                    {{ $priority_rates->Currency }}
                                                                </td>
                                                                <td class="text-center text-sm p-1 whitespace-nowrap">
                                                                    {{ $b_formatted_rate }}
                                                                </td>
                                                                <td class="text-center text-sm p-1 whitespace-nowrap">
                                                                    {{ $s_formatted_rate }}
                                                                </td>
                                                                <td class="text-center text-sm p-1 whitespace-nowrap">
                                                                    @foreach ($exploded_denom as $denoms)
                                                                        <span class="badge success-lighter-badge-custom text-sm">
                                                                            {{ number_format($denoms, 0) }}
                                                                        </span>
                                                                    @endforeach
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        {{-- <span class="badge success-lighter-badge-custom text-sm"> --}}
                                                        {{-- <tr class="!bg-gray-400"> --}}
                                                        <tr class="!bg-[#19c075]">
                                                            <td class="text-center text-white text-sm p-1 whitespace-nowrap">
                                                                US DOLLAR <strong>(DPOFX)</strong>
                                                            </td>
                                                            <td class="text-center text-white text-sm p-1 whitespace-nowrap">
                                                                {{ $result['dpofx_rate'][0] }}
                                                            </td>
                                                            <td class="text-center text-white text-sm p-1 whitespace-nowrap">
                                                                -
                                                            </td>
                                                            <td class="text-center text-white text-sm p-1 whitespace-nowrap">
                                                                -
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="swiper rate-swiper mt-2" id="rate-swiper">
                                            <div class="swiper-wrapper">
                                                @php
                                                    $rate_chunks = $result['general_rates']->chunk(11);
                                                @endphp

                                                @forelse ($rate_chunks as $chunk)
                                                    <div class="swiper-slide">
                                                        <div class="card dahsboard-cards">
                                                            <div class="card-body p-2">
                                                                <table class="table table-bordered table-hover">
                                                                    <tbody>
                                                                        @foreach ($chunk as $general_rates)
                                                                            @php
                                                                                $exploded_denom =  array_filter(explode(' - ', $general_rates->Denomination));

                                                                                // Buying rates
                                                                                $b_formatted_rate = '';
                                                                                $rate_used = $general_rates->whole_b_rate + $general_rates->b_decimal_rate;
                                                                                $decimal_places = (strpos((string) $rate_used, '.') !== false) ? strlen(explode('.', $rate_used)[1]) : 0;

                                                                                if ($decimal_places <= 2 && !in_array($general_rates->CurrencyID, [12, 14, 31])) {
                                                                                    $b_formatted_rate = number_format(floor($rate_used * 100) / 100, 2);
                                                                                } else if ($decimal_places <= 4 && in_array($general_rates->CurrencyID, [12, 14, 31])) {
                                                                                    $b_formatted_rate = number_format(floor($rate_used * 100000) / 100000, 4, '.', ',');
                                                                                }

                                                                                // Selling rates
                                                                                $s_formatted_rate = '';
                                                                                $rate_used = $general_rates->whole_s_rate + $general_rates->s_decimal_rate;
                                                                                $decimal_places = (strpos((string) $rate_used, '.') !== false) ? strlen(explode('.', $rate_used)[1]) : 0;

                                                                                if ($decimal_places <= 2 && !in_array($general_rates->CurrencyID, [12, 14, 31])) {
                                                                                    $s_formatted_rate = number_format(floor($rate_used * 100) / 100, 2);
                                                                                } else if ($decimal_places <= 4 && in_array($general_rates->CurrencyID, [12, 14, 31])) {
                                                                                    $s_formatted_rate = number_format(floor($rate_used * 100000) / 100000, 4, '.', ',');
                                                                                }
                                                                            @endphp
                                                                            <tr>
                                                                                <td class="text-center text-sm p-1 whitespace-nowrap w-25">
                                                                                    {{ $general_rates->Currency }}
                                                                                </td>
                                                                                <td class="text-center text-sm p-1 whitespace-nowrap w-25">
                                                                                    {{ $b_formatted_rate }}
                                                                                </td>
                                                                                <td class="text-center text-sm p-1 whitespace-nowrap w-25">
                                                                                    {{ $s_formatted_rate }}
                                                                                </td>
                                                                                <td class="text-center text-sm p-1 whitespace-nowrap w-25">
                                                                                    @foreach ($exploded_denom as $denoms)
                                                                                        <span class="badge success-lighter-badge-custom text-sm">
                                                                                            {{ number_format($denoms, 0) }}
                                                                                        </span>
                                                                                    @endforeach
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty

                                                @endforelse
                                            </div>
                                            {{-- <div class="swiper-button-next"></div> --}}
                                            {{-- <div class="swiper-button-prev"></div> --}}
                                            <div class="swiper-pagination"></div>
                                        </div>
                                    </div>

                                    <div class="row" id="stocks-card">
                                        <div class="col-12 ps-0">
                                            <div class="card dahsboard-cards">
                                                <div class="card-header pb-1 pt-3 mb-2 align-items-center ">
                                                    <div class="row align-items-center text-left">
                                                        <div class="col-6 ps-3">
                                                            <span class="font-semibold">Available Stocks</span>
                                                        </div>
                                                        <div class="col-6 pe-3 text-end">
                                                            @if (count($result['buffer_stocks']) != 0 || count($result['available_stocks']) != 0)
                                                                <a class="text-xs hover:!text-[#0D6EFD] hover:font-semibold cursor-pointer" id="stocks-button" data-bs-toggle="modal" data-bs-target="#stocks-modal"><span>See All</span></a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="col-12 @if (count($result['available_stocks']) > 4) shadow-[inset_0_-7px_7px_-6px_rgba(0,0,0,0.3)] @endif" @if (count($result['available_stocks']) > 4) id="available-stocks-container" @endif>
                                                        <ul class="ps-2  @if (count($result['available_stocks']) > 4) pe-1 @endif m-0">
                                                            @if (count($result['buffer_stocks']) > 0 || count($result['available_stocks']) > 0)
                                                                @foreach ($result['available_stocks'] as $available_stocks)
                                                                    <li class="d-flex align-items-center mb-2">
                                                                        <div class="row align-items-center avatar me-3 text-xs">
                                                                            <div class="col-1 ps-2 pe-0">
                                                                                <div class="avatar flex-shrink-0">
                                                                                    <span class="avatar-initial rounded @if ($available_stocks->stock_days > 3) bg-label-yellow @else bg-label-secondary @endif"><i class='bx bx-money'></i></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                                            <div class="me-2">
                                                                                <small class="text-muted d-block"><strong>{{ $available_stocks->CurrAbbv }}</strong></small>
                                                                                <span class="text-xs mb-0">{{ $available_stocks->Currency }}</span>
                                                                            </div>
                                                                            <div class="user-progress d-flex align-items-center gap-2">
                                                                                <span class="text-xs font-bold mb-0">{{ number_format($available_stocks->TotalCurrencyAmount, 2, '.', ',') }}</span>
                                                                                {{-- <span class="text-xs text-blue-500">
                                                                                    <span class="badge primary-badge-custom"><text class="font-bold">{{ $available_stocks->Cnt }}</text>@if ($available_stocks->Cnt > 1) pcs.@else pc.@endif</span>
                                                                                </span> --}}

                                                                                {{-- @if ($available_stocks->min_days > 3 && $available_stocks->min_days == $available_stocks->max_days)
                                                                                    <span class="text-xs text-red-500">
                                                                                        <span class="badge danger-badge-custom">
                                                                                            <text class="font-bold">{{ $available_stocks->min_days }} Days</text>
                                                                                        </span>
                                                                                    </span>
                                                                                @elseif ($available_stocks->min_days > 3 )
                                                                                    <span class="text-xs text-red-500">
                                                                                        <span class="badge danger-badge-custom">
                                                                                            <text class="font-bold">{{ $available_stocks->max_days }} - {{ $available_stocks->min_days }} Days</text>
                                                                                        </span>
                                                                                    </span> --}}
                                                                                {{-- @elseif ($available_stocks->min_days < 3)
                                                                                    <span class="text-xs text-red-500">
                                                                                        <span class="badge info-badge-custom">
                                                                                            <text class="font-bold">{{ $available_stocks->max_days }} Day</text>
                                                                                        </span>
                                                                                    </span>
                                                                                @elseif ($available_stocks->min_days == $available_stocks->min_days)
                                                                                    <span class="text-xs text-red-500">
                                                                                        <span class="badge info-badge-custom">
                                                                                            <text class="font-bold">{{ $available_stocks->max_days }} Day</text>
                                                                                        </span>
                                                                                    </span> --}}
                                                                                {{-- @endif --}}
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                @endforeach

                                                                @if (count($result['buffer_stocks']) > 0)
                                                                    <li class="d-flex align-items-center mb-2">
                                                                        <div class="row align-items-center avatar me-3 text-xs">
                                                                            <div class="col-1 ps-2 pe-0">
                                                                                <div class="avatar flex-shrink-0">
                                                                                    <span class="avatar-initial rounded bg-label-secondary"><i class='bx bx-money'></i></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                                            @foreach ($result['buffer_stocks'] as $buffer_stocks)
                                                                                <div class="me-2">
                                                                                    <small class="text-muted d-block"><strong>{{ $buffer_stocks->CurrAbbv }}</strong></small>
                                                                                    <small class="text-muted d-block"><span class="badge success-badge-custom font-bold text-xs p-1">Buffer</span></small>
                                                                                    {{-- <span class="text-xs mb-0">{{ $buffer_stocks->Currency }}</span> --}}
                                                                                </div>
                                                                                <div class="user-progress d-flex align-items-center gap-2">
                                                                                    <span class="text-xs font-bold mb-0">{{ number_format($buffer_stocks->total_amount, 2, '.', ',') }}</span>
                                                                                    {{-- <span class="text-xs text-blue-500">
                                                                                        <span class="badge primary-badge-custom"><text class="font-bold">{{ $buffer_stocks->count }}</text>@if ($buffer_stocks->count > 1) pcs.@else pc.@endif</span>
                                                                                    </span> --}}
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </li>
                                                                @endif
                                                            @else
                                                                <li class="d-flex align-items-center">
                                                                    {{-- <div class="row align-items-center avatar me-3 text-xs">
                                                                        <i class='bx bx-dollar-circle bx-sm'></i>
                                                                    </div> --}}
                                                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                                        <div class="me-2">
                                                                            <h6 class="fw-normal mb-0">No available stock.</h6>
                                                                        </div>
                                                                        <div class="user-progress d-flex align-items-center gap-2">
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-6 pe-0">
                                            <div class="card dahsboard-cards">
                                                <div class="card-header pb-1 pt-3 mb-2 align-items-center ">
                                                    <div class="row align-items-center text-left">
                                                        <div class="col-12">
                                                            <span class="mb-2 font-semibold">Old Stocks</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="col-12" @if (count($result['old_stocks']) > 4) id="old-stocks-container" @endif>
                                                        <ul class="p-0 m-0">
                                                            @forelse ($result['old_stocks'] as $old_stocks)
                                                                <li class="d-flex align-items-center mb-2">
                                                                    <div class="row align-items-center avatar me-3 text-xs">
                                                                        <div class="row align-items-center avatar me-3 text-xs">
                                                                            <div class="col-1 ps-2 pe-0">
                                                                                <div class="avatar flex-shrink-0">
                                                                                    <span class="avatar-initial rounded bg-label-secondary"><i class='bx bx-money'></i></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                                        <div class="me-2">
                                                                            <small class="text-muted d-block"><strong>{{ $old_stocks->CurrAbbv }}</strong></small>
                                                                            <span class="text-xs mb-0">{{ $old_stocks->Currency }}</span>
                                                                        </div>
                                                                        <div class="user-progress d-flex align-items-center gap-2">
                                                                            <span class="text-xs font-bold mb-0">{{ number_format($old_stocks->TotalCurrencyAmount, 2, '.', ',') }}</span>
                                                                            <span class="text-xs text-red-500">
                                                                                <span class="badge danger-badge-custom">
                                                                                    @if ($old_stocks->min_days > 3 && $old_stocks->min_days == $old_stocks->max_days)
                                                                                        <text class="font-bold">{{ $old_stocks->min_days }}</text>
                                                                                    @elseif ($old_stocks->min_days > 3 )
                                                                                        <text class="font-bold">{{ $old_stocks->max_days }} - {{ $old_stocks->min_days }}</text>
                                                                                    @elseif ($old_stocks->min_days < 3)
                                                                                        <text class="font-bold">{{ $old_stocks->max_days }}</text>
                                                                                    @elseif ($old_stocks->min_days == $old_stocks->min_days)
                                                                                        <text class="font-bold">{{ $old_stocks->max_days }}</text>
                                                                                    @endif

                                                                                    @if ($old_stocks->Cnt > 1) days @else day @endif
                                                                                </span>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @empty
                                                                <li class="d-flex align-items-center">
                                                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                                        <div class="me-2">
                                                                            <h6 class="fw-normal mb-0">No old stock(s) available.</h6>
                                                                        </div>
                                                                        <div class="user-progress d-flex align-items-center gap-2">
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endforelse
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('blades.stocks_modal')
@endsection

@section('dasboard_scripts')
    @include('script.dashb_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
