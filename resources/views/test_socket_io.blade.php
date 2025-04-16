
@extends('template.layout')

@section('content')
    <div class="layout-page">
        <div class="content-wrapper">
            @include('template.menu')

            <div class="container-fluid flex-grow-1 container-p-y m-0 pt-0">
                <div class="col-5">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <input class="form-control" type="text" id="input-notif" placeholder="Type a message">
                        </div>
                        <div class="col-3">
                            <button class="btn-primary btn-sm" id="send-button">Send</button>
                        </div>
                    </div>
                </div>
                <div id="chatbox"></div>
            </div>
        </div>
    </div>
@endsection
