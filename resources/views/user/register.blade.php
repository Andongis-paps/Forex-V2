@extends('template.layout')
@section('content')

<div class="container">
    <div class="row justify-content-center mt-lg-5">
        <div class="col-lg-4 mt-lg-5">
            @if( count($errors) > 0)
                @foreach($errors->all() as $error)
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <span> {{ $error }} </span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endforeach
            @endif

            @if(Session::has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <span> {{ session('error') }} </span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <span> {{ session('success') }} </span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h4 class="text-center">
                        <span class="login-header">
                            <b>Register</b>
                        </span>
                    </h4>
                </div>
                <div class="card-body">
                    <form class="form" method="post" action="{{ URL::to('/storeAccount') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <label for="name" class="mb-2">Name</label>
                                <input type="text" class="form-control" name="name">
                            </div>

                            <div class="col-lg-12">
                                <label for="username" class="mb-2">Userame</label>
                                <input type="text" class="form-control" name="username">
                            </div>

                            <div class="col-lg-12">
                                <label for="password" class="mb-2">Password</label>
                                <input type="password" class="form-control" name="password">
                            </div>

                            <div class="col-lg-12">
                                <label for="confirm-password" class="mb-2">Confirm Password</label>
                                <input type="password" class="form-control" name="password_confirmation">
                            </div>

                            <div class="col-lg-12 my-3 text-center">
                                <button class="btn btn-outline-primary" type="submit">Sign Up</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
