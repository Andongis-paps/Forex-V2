@extends('template.layout')
@section('content')

    <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')
            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="row justify-content-center">
                    <div class="col-lg-12mb-4">
                        <div class="row">
                            <div class="col-12">
                                <hr>
                            </div>

                            @php
                                use Carbon\Carbon;

                                $raw_date = Carbon::now('Asia/Manila');
                            @endphp

                            <div class="col-12">
                                <div class="card">
                                    <div class="col-12 p-2 border border-gray-300 rounded-tl rounded-tr">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <div class="row align-items-center">
                                                    <div class="col-6">
                                                        <span class="text-lg font-bold p-2 text-black">
                                                            <i class='bx bx-bell'></i>&nbsp;Notifications
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 text-end">
                                                {{-- <strong>{{ $raw_date->format('F j, Y') }}</strong>&nbsp;({{ $raw_date->format('l') }}) as of&nbsp;<text class="font-bold text-[#0D6EFD]" id="clock"></text> --}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        @forelse ($result['notifs'] as $notifs)
                                            <a class="notif-button" data-fxdid="{{ $notifs->FXDID }}" href="{{ url('/') }}/{{ $notifs->URLName }}">
                                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0 me-3">
                                                            <div class="avatar flex-shrink-0">
                                                                <span class="avatar-initial rounded bg-label-secondary">
                                                                    @if ($notifs->Acknowledged == 0)
                                                                        <i class='bx bx-envelope'></i>
                                                                    @else
                                                                        <i class='bx bx-envelope-open'></i>
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                            <div class="me-2">
                                                                <h6 class="mb-1"><strong>{{ $notifs->AppMenuName }}</strong></h6>
                                                                <small class="text-muted">{{ $notifs->Notification }}</small><br> 
                                                                <small class="text-black @if ($notifs->Acknowledged == 0) font-bold @endif">{{ \Carbon\Carbon::parse($notifs->Date)->format('F j, Y') }}</small>
                                                            </div>
                                                            <div class="user-progress d-flex align-items-center gap-2">
                                                                <small class="text-black @if ($notifs->Acknowledged == 0) font-bold @endif">{{ \Carbon\Carbon::parse($notifs->Date)->diffForHumans() }}</small>
                                                                @if ($notifs->Acknowledged == 0)
                                                                    <i class='bx bxs-circle dot-badge !text-[#DC3545]'></i>
                                                                @endif
                                                            </div>
                                                        </div>
    
                                                        {{-- <div class="w-100">
                                                            <h6 class="mb-1"><strong>{{ $notifs->AppMenuName }}</strong></h6>
                                                            <small class="text-muted">{{ $notifs->Notification }}</small><br> 
                                                        </div>
                                                        <div class="pt-2">
                                                            <small class="text-muted">{{ $notifs->Date }}</small>
                                                            @if ($notifs->Acknowledged == 0)
                                                                <i class='bx bxs-circle dot-badge !text-[#DC3545]'></i>
                                                            @endif
                                                        </div> --}}
                                                    </div>
                                                </li>
                                            </a>
                                        @empty
                                            <table class="table table-hover table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td class="text-lg text-black text-center" colspan="10">
                                                            <strong>
                                                                NOT AVAILABLE
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        @endforelse
                                    </div>
                                    <div class="col-12 py-1 px-3 border border-gray-300 rounded-bl rounded-br">
                                        <div class="row align-items-center">
                                            <div class="col-12">
                                                {{ $result['notifs']->links() }}
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

{{-- @section('dasboard_scripts')
    @include('script.dashb_scripts')
@endsection --}}

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
