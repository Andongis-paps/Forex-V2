@extends('template.layout')
@section('content')

   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <span class="text-lg font-extrabold p-2 text-black">
                                                    <i class='bx bx-cog'></i>&nbsp;Edit Company Limit
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 border border-gray-300">
                                        <form method="post" action="{{ route('maintenance.company_limit.update_company_limit') }}" id="update-company-limit-form">
                                            @csrf
                                            <div class="row align-items-center px-3">
                                                <div class="col-4 px-2 pt-2">
                                                    <label class="text-sm font-bold" for="company">Series (Set O):</label>
    
                                                    <input class="form-control mt-1" name="series-set-o" id="series-set-o" type="number" placeholder="000000" value="{{ str_pad($result['series_o'][0], 6, '0', STR_PAD_LEFT) }}">
                                                </div>
                                                <div class="col-4 px-2 pt-2">
                                                    <label class="text-sm font-bold" for="company">Company:</label>
    
                                                    <select class="form-select mt-1" name="company" id="company">
                                                        @foreach ($result['company'] as $company)
                                                            <option value="{{ $company->CompanyID }}" @if ($result['current_comp'][0] == $company->CompanyID) selected @endif>{{ $company->CompanyName }}</option>
                                                        @endforeach
                                                    </select>

                                                    <input id="CLDID" value="{{ $CLDID }}" type="hidden">
                                                </div>
                                                <div class="col-4 px-2 pt-2">
                                                    <label class="text-sm font-bold" for="company">Annual Limit:</label>

                                                    <input class="form-control mt-1" name="annual-amount" id="annual-amount" type="number" value="{{ $result['annual_limit'][0] }}" placeholder="0.00">
                                                </div>
                                            </div>
    
                                            <div class="row">
                                                <div class="col-12">
                                                    <hr class="my-2">
                                                </div>
                                            </div>
    
                                            <div class="row px-2">
                                                <div class="col-12">
                                                    <table class="table table-hover table-bordered" id="company-limit-table">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center text-xs font-bold whitespace-nowrap text-black p-1">Month</th>
                                                                <th class="text-center text-xs font-bold whitespace-nowrap text-black p-1">Percentage</th>
                                                                <th class="text-center text-xs font-bold whitespace-nowrap text-black p-1">Amount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php
                                                                $total_percentage = 0;
                                                                $total_annual_limit = 0;
                                                            @endphp

                                                            @foreach ($result['company_limit'] as $company_limit)
                                                                @foreach ($result['month_config'] as $key => $month_config)

                                                                    @php
                                                                        $total_percentage += $company_limit->percentage[$key];
                                                                        $total_annual_limit += $company_limit->amount[$key];
                                                                    @endphp

                                                                    <tr>
                                                                        <td class="text-center text-xs whitespace-nowrap text-black p-1">
                                                                            {{ $month_config->Month }}
                                                                        </td>
                                                                        <td class="text-center text-sm whitespace-nowrap text-black p-1 percentage-td">
                                                                            <div class="input-group">
                                                                                <input class="form-control monthly-percentage text-right" id="{{ $month_config->Month }}-percentage-{{ $month_config->MID }}" name="{{ $month_config->Month }}-percentage-{{ $month_config->MID }}" value="{{ number_format($company_limit->percentage[$key], 2, '.', ',') }}" type="number" placeholder="0.00">
                                                                                <span class="font-bold input-group-text">%</span>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-center text-sm whitespace-nowrap text-black p-1 amount-td">
                                                                            <input class="CLIDs" value="{{ $company_limit->CLIDs[$key] }}" type="hidden">
                                                                            <input class="form-control monthly-amount text-right" id="{{ $month_config->Month }}-amount-{{ $month_config->MID }}" name="{{ $month_config->Month }}-amount-{{ $month_config->MID }}" value="{{ number_format($company_limit->amount[$key], 2, '.', ',') }}" placeholder="0.00" readonly>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="1"></td>
                                                                <td class="text-right whitespace-nowrap text-black py-1 pe-3">
                                                                    <span class="text-black font-bold" id="total-percentage">
                                                                        {{ number_format($total_percentage, 2, '.', ',') }}
                                                                    </span>
                                                                    &nbsp;
                                                                    <span class="text-black font-bold">
                                                                        %
                                                                    </span>
                                                                </td>
                                                                <td class="text-right whitespace-nowrap text-black py-1 pe-3">
                                                                    <span class="text-black font-bold">
                                                                        PHP
                                                                    </span>
                                                                    &nbsp;
                                                                    <span class="text-black font-bold" id="total-annual-amnt">
                                                                        {{ number_format($total_annual_limit, 2, '.', ',') }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12 text-end">
                                                @can('access-permission', $menu_id)
                                                    <a class="btn btn-secondary text-white btn-sm" href="{{ route('maintenance.company_limit') }}">
                                                        Back
                                                    </a>
                                                @endcan

                                                @can('edit-permission', $menu_id)
                                                    <button class="btn btn-primary btn-sm" id="update-company-limit">
                                                        Confirm
                                                    </button>
                                                @endcan
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

    @section('comp_limit_scripts')
        @include('script.edit_company_limit_scripts')
    @endsection
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
