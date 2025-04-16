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
                                                <span class="text-lg font-bold p-2 text-black">
                                                    <i class='bx bx-cog'></i>&nbsp;Configuration
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 border border-gray-300">
                                        <form action="{{ route('maintenance.configuration.update') }}" method="POST" id="update-sessions">
                                            @csrf
                                            <div class="col-12 p-2 border-b border-gray-300 rounded-tl rounded-tr  ">
                                                <div class="row align-items-center justify-content-center px-2">
                                                    <div class="col-12">
                                                        <span class="text-lg font-semibold p-2">
                                                          <i class='bx bxs-cog me-1 bx-sm'></i>
                                                          Authentication Settings
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row my-2 mx-0">
                                                <div class="col col-12">
                                                    <div class="row mb-2">
                                                        <label class="col-4 d-flex align-items-start font-semibold pt-2 justify-content-end " for="lifetime"> Session Lifetime&nbsp;:</label>
                                                        <div class="col-4">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <span class="input-group-text bg-blue-custom-300"><i class="bx bx-time me-1"></i></span>
                                                                    <input type="text"  class="form-control timepicker" id="lifetime" name="lifetime" placeholder="Select Time" value="{{ $sessions['hours'].':'.$sessions['seconds'] }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row my-2 mx-0">
                                                <div class="col col-12">
                                                    <div class="row mb-2">
                                                        <label class="col-4 d-flex align-items-start font-semibold pt-1 justify-content-end " for="expire_on_close">  Signout Upon Browser Closure&nbsp;:</label>
                                                        <div class="col-8">
                                                            <div class="btn-group btn-group-sm custom-template-radio-btn" role="group" >
                                                                <input type="radio" class="btn-check" name="expire_on_close" id="expire_on_close_on" value="1" {!! $sessions['expire_on_close'] == 1? 'checked' : '' !!}>
                                                                <label class="btn btn-outline-primary" for="expire_on_close_on">On</label>
                                                                <input type="radio" class="btn-check" name="expire_on_close" id="expire_on_close_off" value="0" {!! $sessions['expire_on_close'] == 0? 'checked' : '' !!}>
                                                                <label class="btn btn-outline-primary" for="expire_on_close_off">Off</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row my-2 mx-0">
                                                <div class="col col-12">
                                                    <div class="row mb-2">
                                                        <label class="col-4 d-flex align-items-start font-semibold pt-2 justify-content-end " for="attemps"> No. of Login Attempts&nbsp;:</label>
                                                        <div class="col-4">
                                                            <input type="text" class="form-control numberField validate" id="attemps" name="attemps" placeholder="Time" value="{{ $sessions['attemps'] }}"  >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row my-2 mx-0">
                                                <div class="col col-12">
                                                    <div class="row mb-2">
                                                        <label class="col-4 d-flex align-items-start font-semibold pt-2 justify-content-end " for="waiting_time"> Waiting Time&nbsp;:</label>
                                                        <div class="col-4">
                                                            <input type="text" class="form-control numberField validate" id="waiting_time" name="waiting_time" placeholder="Time" value="{{ $sessions['waiting_time'] }}"  >
                                                        </div>
                                                        <div class="col-4">
                                                            <small class="col-4 d-flex align-items-start  pt-2 justify-content-start " > (Minutes) </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row my-2 mx-0">

                                            </div>
                                        </form>
                                    </div>

                                    <div class="col-12 p-2 border border-gray-300 rounded-bl rounded-br">
                                        <div class="col-12 text-end">
                                            @can('edit-permission', $menu_id)
                                                <button type="button" class="btn btn-primary" data-item-form="updateSessions" id="updateSessions" data-bs-toggle="modal" data-bs-target="#security-code-modal"><i class="bx bx-save me-1"></i>Save</button>
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

    @include('UI.UX.security_code')

<script>
    $(document).ready(function() {
        $('#proceed-transaction').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#security-code').val();

            $('#proceed-transaction').prop('disabled', true);

            $.ajax({
                url: "{{ route('user_info') }}",
                type: "GET",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(get_user_info) {
                    var user_info = get_user_info.security_codes;

                    user_info.forEach(function(gar) {
                        sec_code_array.push(gar.SecurityCode);
                        user_id_array.push(gar.UserID);
                    });

                    if (sec_code_array.includes(user_sec_onpage)) {
                        $('#proceed-transaction').prop('disabled', true);

                        var index = sec_code_array.indexOf(user_sec_onpage);
                        var matched_user_id = user_id_array[index];

                        Swal.fire({
                            title: 'Success!',
                            text: 'Session Updated!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            var form_data = new FormData($('#update-sessions')[0]);
                            form_data.append('matched_user_id', matched_user_id);

                            $.ajax({
                                url: "{{ route('maintenance.configuration.update') }}",
                                type: "post",
                                data: form_data,
                                contentType: false,
                                processData: false,
                                cache: false,
                                success: function(data) {
                                    var url = "{{ route('maintenance.configuration') }}"

                                    window.location.href = url;
                                }
                            });
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            text: 'Invalid or mismatched security code.',
                            customClass: {
                                popup: 'my-swal-popup',
                            }
                        }).then(() => {
                            $('#proceed-transaction').prop('disabled', false);
                        });
                    }
                }
            });
        });
    });
</script>
@endsection

<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<script defer src="{{asset('js/app.js')}}"></script>

