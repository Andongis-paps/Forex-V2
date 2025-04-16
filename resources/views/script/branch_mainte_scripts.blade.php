{{-- Window based - Branch Mainte Scripts --}}
<script>
    var user_id_array = [];
    var sec_code_array = [];

    $(document).ready(function() {
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
            }
        });
    });

    // AJAX request for add new branch - Window Based - Branch
    $(document).ready(function(){
        $('#button-add-branch').click(function(){
            $('#branch-maint-add-modal').modal('show');
        });
    });

    // AJAX request for edit branch details - Window Based - Branch
    $(document).ready(function(){
        $('.button-edit-branch').click(function(){
            var BranchID = $(this).attr('data-branchmaintid');

            $.ajax({
                url: "{{ route('maintenance.branch_maintenance.edit') }}",
                method: "POST",
                data: {
                    BranchID: BranchID,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('.update-branch-details').html(data);
                }
            });
        });
    });

    // AJAX for branch searching - Window Based - Branch
    $(document).ready(function() {
        $('#search-branch').keyup(function(){
            var search_word = $(this).val();

            if(search_word != '') {
                var _token = $('input[name="_token"]').val();

                $.ajax({
                    url: "{{ URL::to('/searchFetch') }}",
                    method: "POST",
                    data: {
                        search_word: search_word,
                        _token:_token
                    },
                    success: function(data) {
                        var branch_details = data;
                        var array_index = branch_details[0];
                        var b_details_bcode = array_index.BranchCode;

                        $("tr").each(function() {
                            var branch_codes = $(this).attr('data-branchcode');
                            var branch_address = $(this).attr('data-branchaddress');

                            if (branch_codes === b_details_bcode) {
                                $(this).addClass("search-highlight");
                                $(this)[0].scrollIntoView({ behavior: "smooth", block: "center" });
                            } else {
                                $(this).removeClass("search-highlight");
                            }
                        });
                    }
                });
            }
        });

        $('#search-branch').keyup(function() {
            var search_word = $(this).val();

            if(search_word == '') {
                $("tr").each(function() {
                    $(this).removeClass("search-highlight").scrollTop();
                });
            }
        });
    });

    $(document).ready(function() {
        $('#branch-add-button').click(function() {
            var branch_code = $('input[name="branch_code"]').val();
            var branch_name = $('input[name="branch_name"]').val();
            var branch_address = $('input[name="branch_address"]').val();
            var branch_telno = $('input[name="branch_telno"]').val();
            var user_sec_onpage = $('#add-branch-security-code').val();

            $('#branch-add-button').prop('disabled', true);

            if (branch_code == '' || branch_name == '' ) {
                Swal.fire({
                    text: 'Branch code and Branch name fields are required.',
                    icon: 'warning',
                    showConfirmButton: true
                });
            } else {
                if (sec_code_array.includes(user_sec_onpage)) {
                    $('#branch-add-button').prop('disabled', true);

                    var index = sec_code_array.indexOf(user_sec_onpage);
                    var matched_user_id = user_id_array[index];

                    Swal.fire({
                        title: 'Success!',
                        text: 'Branch successfully added!',
                        icon: 'success',
                        timer: 900,
                        showConfirmButton: false
                    }).then(() => {
                        $('#container-test').fadeIn("slow");
                        $('#container-test').css('display', 'block');

                        var form_data = new FormData($('#add-new-branch-form')[0]);
                        form_data.append('matched_user_id', matched_user_id);

                        setTimeout(() => {
                            $.ajax({
                                url: "{{ route('maintenance.branch_maintenance.add') }}",
                                type: "post",
                                data: form_data,
                                contentType: false,
                                processData: false,
                                cache: false,
                                success: function(data) {
                                    window.location.reload();
                                }
                            });
                        }, 200);
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        text: 'Invalid or mismatched security code.',
                        customClass: {
                            popup: 'my-swal-popup',
                        }
                    }).then(() => {
                        $('#branch-add-button').prop('disabled', false);
                    });
                }
            }
        });
    });
</script>
