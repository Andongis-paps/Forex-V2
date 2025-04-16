@extends('template.layout')
@section('content')

    <div class="layout-page">
        <!-- Navbar -->
        <div class="content-wrapper">
            <!-- Content -->
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <div class="row">
                            <div class="col-12">
                            </div>

                            <div class="col-12">
                                <div class="card p-0" id="new-buying-transaction-header">
                                    <div class="card-body row align-items-center p-3">
                                        <div class="col-6">
                                            <span class="text-lg font-semibold p-2 text-white">
                                                {{ trans('labels.bill_tagging_index') }}
                                            </span>
                                        </div>

                                        <div class="col-6 text-end">
                                            <a class="btn btn-primary text-white" href="{{ route('admin_transactions.bill_tagging') }}">
                                                {{ trans('labels.bill_tagging_add_label') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0'></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                        </div>
                                    </div>

                                    <table class="table table-hovered table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-sm font-extrabold text-black py-1 px-1 whitespace-nowrap">Transaction No.</th>
                                                <th class="text-center text-sm font-extrabold text-black py-1 px-1 whitespace-nowrap"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- @forelse ($result['bills_sold_to_mnl'] as $bills_sold_to_mnl)
                                                <tr>
                                                    <td class="text-sm text-center p-2 text-black">
                                                        {{ $bills_sold_to_mnl->STMNo }}
                                                    </td>
                                                    <td class="text-sm text-center p-2 text-black">
                                                        <a class="btn btn-primary button-edit button-edit-trans-details" id="button-trans-details" href="{{ route('selling_trans_details_admin', ['STMDID' => $bills_sold_to_mnl->STMDID]) }}">
                                                            <i class='menu-icon tf-icons bx bx-edit-alt text-white'></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td class="text-center text-td-buying text-sm py-3" colspan="13">
                                                        <span class="buying-no-transactions text-lg">
                                                            <strong>NO AVAILABLE TRANSACTIONS FOR TODAY</strong>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforelse --}}
                                        </tbody>
                                    </table>

                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                {{-- {{ $result['bills_sold_to_mnl']->links() }} --}}
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

@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
