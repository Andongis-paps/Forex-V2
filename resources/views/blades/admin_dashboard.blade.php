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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('dasboard_scripts')
    @include('script.dashb_scripts')
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>
