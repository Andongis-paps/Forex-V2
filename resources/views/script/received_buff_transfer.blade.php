{{-- Receive Buffer --}}
<script>
    $(document).ready(function() {
        const socketserver = "{{ config('app.socket_io_server') }}";
        const socket = io(socketserver);

        var serials = '';
        var branch_id = 10;
        var branch_code = '';
        var bill_amnt = '';
        var transfer_forex_id = '';
        var transfer_forex_no = '';

        $('.acknowledge-buffer-transf').click(function() {
            // branch_id = $(this).attr('data-branchid');
            // transfer_forex_id = $(this).attr('data-bufftfid');
            transfer_forex_no = $(this).attr('data-tfxno');
            transfer_forex_id = $(this).attr('data-transferforexid');
            branch_code = $(this).attr('data-branchcode');

            $.ajax({
                url: "{{ route('admin_transactions.buffer.incoming_buff_details') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    transfer_fx_id: $(this).attr('data-bufftfid'),
                },
                success: function(data) {
                    $('#incoming-buff-modal').modal("show");
                    $('.incoming-buff-details').html(data);
                }
            });
        });

        $('.incoming-buffer-details').click(function() {
            // branch_id = $(this).attr('data-branchid');
            // transfer_forex_id = $(this).attr('data-bufftfid');
            transfer_forex_no = $(this).attr('data-tfxno');
            transfer_forex_id = $(this).attr('data-transferforexid');
            branch_code = $(this).attr('data-branchcode');

            $.ajax({
                url: "{{ route('admin_transactions.buffer.incoming_buff_details') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    transfer_fx_id: $(this).attr('data-bufftfid'),
                },
                success: function(data) {
                    $('#incoming-buff-modal').modal("show");
                    $('.incoming-buff-details').html(data);
                }
            });
        });

        function RevertBuffer($mgs, $branchid, $serials, $bill_amnt, $branch_code) {
            socket.emit('revertBuffer', {msg: $mgs, branchid: $branchid, serials: $serials, bill_amnt: $bill_amnt, branch_code: $branch_code});
        }

        $('#proceed-revert').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var selected_ids_array = [];
            var selected_serials_array = [];
            var selected_bills_array = [];
            var not_selected_ids_array = [];
            var not_selected_serials_array = [];
            var not_s_selected_bills_array = [];
            var user_sec_onpage = $('#rev-security-code').val();
            $('#proceed-transaction').prop('disabled', false);

            $('.select-one-buffer').each(function() {
                if ($(this).prop('checked')) {
                    if (!selected_ids_array.includes($(this).attr('data-fsid'))) {
                        selected_ids_array.push($(this).attr('data-fsid'));
                    }

                    if (!selected_serials_array.includes($(this).attr('data-serials'))) {
                        selected_serials_array.push($(this).attr('data-serials'));
                    }

                    // if (!selected_bills_array.includes($(this).attr('data-billamount'))) {
                    selected_bills_array.push($(this).attr('data-billamount'));
                    // }
                } else {
                    if (!not_selected_ids_array.includes($(this).attr('data-fsid'))) {
                        not_selected_ids_array.push($(this).attr('data-fsid'));
                    }

                    if (!not_selected_serials_array.includes($(this).attr('data-serials'))) {
                        not_selected_serials_array.push($(this).attr('data-serials'));
                    }

                    not_s_selected_bills_array.push($(this).attr('data-billamount'));
                }
            });

            serials = selected_serials_array.join(",");
            bill_amnt = selected_bills_array.join(",");

            not_s_serials = not_selected_ids_array.join(",");
            not_s_bill_amnt = not_s_selected_bills_array.join(",");

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
                            text: 'Buffer Reverted!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            setTimeout(function() {
                                $.ajax({
                                    url: "{{ route('admin_transactions.buffer.revert_buffer') }}",
                                    type: "post",
                                    data: {
                                        _token: "{{ csrf_token() }}",
                                        FSIDS: selected_ids_array.join(","),
                                        not_FSIDS: not_selected_ids_array.join(","),
                                        currency_amnt: bill_amnt,
                                        not_s_currency_amnt: not_s_bill_amnt,
                                        matched_user_id: matched_user_id,
                                        buffer_type: $('#buffer-type').val(),
                                        transfer_forex_id: transfer_forex_id
                                    },
                                    success: function(response) {
                                        $('#container-test').fadeIn("slow");
                                        $('#container-test').css('display', 'block');

                                        RevertBuffer(`Branch <b>${branch_code}</b> has reverted a buffer entry. <br>(Transfer FX #: <b><u>${transfer_forex_no}</u></b>).`, branch_id, serials, bill_amnt);

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
                            $('#proceed-transaction').prop('disabled', false);
                        });
                    }
                }
            });
        });
    });
</script>
