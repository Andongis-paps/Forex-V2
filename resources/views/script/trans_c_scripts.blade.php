<script>
    $(document).ready(function() {
        const socketserver = "{{ config('app.socket_io_server') }}";
        const socket = io(socketserver);

        var branch_id = '';
        var branch_code = '';
        var trans_cap_amnt = '';

        $('.trans-cap-details').click(function(){
            branch_id = parseInt($(this).attr('data-branchid'));
            branch_code = $(this).attr('data-branchcode');

            $.ajax({
                url: "{{ route('admin_transactions.details') }}",
                method: "POST",
                data: {
                    branch_id: branch_id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('.trans-c-details').html(data);
                }
            });

        });

        $('#halt-transaction').click(function() {
            $('#trans-cap-details-modal').modal("show");
            $('#security-code-modal').modal("hide");
        });

        function TransferCapital($mgs, $branchid) {
            socket.emit('transferCapital', {msg: $mgs, branchid: $branchid});
        }

        $('#proceed-transaction').click(function() {
            var trans_caps = [];
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#security-code').val();
            var amount_to_transf = isNaN(parseFloat($('#total-trans-cap-amount').val())) ? 0 : parseFloat($('#total-trans-cap-amount').val());

            $('.select-trans-cap').each(function() {
                if ($(this).prop('checked')) {
                    if (!trans_caps.includes($(this).attr('data-tcid'))) {
                        trans_caps.push($(this).attr('data-tcid'));
                    }
                }
            });

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

                    var index = sec_code_array.indexOf(user_sec_onpage);
                    var matched_user_id = user_id_array[index];

                    if (sec_code_array.includes(user_sec_onpage)) {
                        $('#proceed-transaction').prop('disabled', true);

                        Swal.fire({
                            title: 'Success',
                            text: 'Capital transferrred!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $.ajax({
                                url: "{{ route('admin_transactions.transfer') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    TCIDs: trans_caps.join(", ")
                                },
                                success: function(data) {
                                    $('#container-test').fadeIn("slow");
                                    $('#container-test').css('display', 'block');

                                    TransferCapital(`Admin has transferred <b>PHP ${amount_to_transf.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</b> on your branch <b>(${branch_code})</b> for capital. You can now check your wallet to see your updated balance.`, branch_id);

                                    setTimeout(function() {
                                        // var url = "{{ route('admin_transactions.buffer.buffer') }}";

                                        window.location.reload();

                                        $('#container-test').fadeIn("slow");
                                        $('#container-test').css('display', 'block');
                                    }, 500);
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
