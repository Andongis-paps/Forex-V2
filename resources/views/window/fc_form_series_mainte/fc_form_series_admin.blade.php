@extends('template.layout')
@section('content')

   <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row">
                    <div class="col-lg-12 mb-4">
                        <div class="row">
                            <div class="col-12">
                                <hr>
                            </div>

                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row justify-content-center">
                                            <div class="col-6">
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-cog'></i>&nbsp;{{ trans('labels.w_series_mainte') }}
                                                </span>
                                            </div>

                                            <div class="col-6 text-end">
                                                @can('add-permission', $menu_id)
                                                    <a class="btn btn-primary text-white btn-sm" data-bs-toggle="modal" data-bs-target="#r-series-maint-add-modal">
                                                        {{ trans('labels.w_series_mainte_add') }} <i class='menu-icon tf-icons bx bx-plus text-white ms-1 me-0 mb-1'></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-hovered table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Company</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Latest FCF Series</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Receipt Set</th>
                                                {{-- <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Added By</th> --}}
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Entry Date</th>
                                                <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap">Entry Time</th>
                                                @can('access-permission', $menu_id)
                                                    <th class="text-center text-xs font-extrabold text-black p-1 whitespace-nowrap"></th>
                                                @endcan
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($result['fc_form_series'] as $fc_form_series)
                                                <tr>
                                                    <td class="text-sm text-center p-2 text-black">
                                                        {{ $fc_form_series->CompanyName }}
                                                    </td>
                                                    <td class="text-sm text-center p-2 text-black">
                                                        {{ $fc_form_series->FormSeries }}
                                                    </td>
                                                    <td class="text-sm text-center p-2 text-black">
                                                        {{ $fc_form_series->RSet }}
                                                    </td>
                                                    {{-- <td class="text-sm text-center p-2 text-black">
                                                        {{ $fc_form_series->Name }}
                                                    </td> --}}
                                                    <td class="text-sm text-center p-2 text-black">
                                                        {{ $fc_form_series->EntryDate }}
                                                    </td>
                                                    <td class="text-sm text-center p-2 text-black">
                                                        {{ $fc_form_series->EntryTime }}
                                                    </td>
                                                    <td class="text-center text-sm p-1">
                                                        @can('edit-permission', $menu_id)
                                                            <a class="btn btn-primary button-edit button-edit-fc-form-series" id="button-edit-fc-form-series" data-fcfsid="{{ $fc_form_series->FCFSID }}" data-bs-toggle="modal" data-bs-target="#fc-form-series-maint-edit-modal">
                                                                <i class='menu-icon tf-icons bx bx-edit-alt text-white'></i>
                                                            </a>
                                                        @endcan
                                                        @can('delete-permission', $menu_id)
                                                            <a class="btn btn-primary button-delete button-delete-fc-form-series" data-fcfsid="{{ $fc_form_series->FCFSID }}">
                                                                <i class='menu-icon tf-icons bx bx-trash text-white'></i>
                                                            </a>
                                                        @endcan
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
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                {{ $result['fc_form_series']->links() }}
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

    {{-- Add new branch via AJAX --}}
    <div class="modal fade" id="r-series-maint-add-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content add-branch-mainte">
                @include('window.fc_form_series_mainte.add_fc_form_series_mainte_modal')
            </div>
        </div>
    </div>

    {{-- Update foreign conversion form details via AJAX --}}
    <div class="modal fade" id="fc-form-series-maint-edit-modal" tabindex="-1" aria-hidden="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content update-fc-form-series-details">

            </div>
        </div>
    </div>

    @section('r_series_mainte_scripts')
        @include('script.receipt_series_scripts')
    @endsection

@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
