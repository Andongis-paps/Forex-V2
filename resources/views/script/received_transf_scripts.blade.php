<script>
    $(document).ready(function() {
        var RTID = '';
        var TFXID = '';

        $('.unreceive-transfer').click(function() {
            RTID = $(this).attr('data-rtid');
            TFXID = $(this).attr('data-tfxid');
        });

        $('#proceed-transaction').click(function() {
            $('#proceed-transaction').prop('disabled', true);

            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#security-code').val();

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
                            text: 'Transfer unreceived!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            $('#container-test').fadeIn("slow");
                            $('#container-test').css('display', 'block');

                            $.ajax({
                                url: "{{ route('admin_transactions.receive_transfer_forex.unreceive') }}",
                                type: "post",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    RTID: RTID,
                                    TFXID: TFXID,
                                },
                                success: function(data) {
                                    window.location.reload();
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

        $('.incoming-transfer-details').click(function() {
            $.ajax({
                url: "{{ route('admin_transactions.receive_transfer_forex.incoming') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    TFXID: $(this).attr('data-tfxid')
                },
                success: function(data) {
                    clear();
                    var transfer_per_currency = data.transfer_per_currency;
                    var transfer_per_serial = data.transfer_per_serial;
                    var data_count = transfer_per_serial.length;

                    transfer_per_currency.forEach(function(gar) {
                        transferPerCurrency(gar.Currency, gar.bill_amount_count, gar.total_bill_amount);
                    });

                    transfer_per_serial.forEach(function(gar) {
                        transferPerSerial(gar.TransactionDate, gar.ORNo, gar.FSID, gar.Currency, gar.BillAmount, gar.Serials, data_count);
                    });
                }
            })
        });

        function transferPerCurrency(Currency, bill_amount_count, total_bill_amount) {
            var table = $('#bill-cash-count');
            var row = $('<tr>');
            var currency = $('<td class="text-center text-sm text-black p-1">'+ Currency +'</td>');
            var bill_count = $('<td class="text-center text-sm text-black p-1">'+ bill_amount_count +'</td>');
            var curr_amnt = $('<td class="text-right text-sm text-black py-1 px-3"><strong>'+ total_bill_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</strong></td>');

            row.append(currency);
            row.append(bill_count);
            row.append(curr_amnt);

            table.find('tbody').append(row);
        }

        function transferPerSerial(TransactionDate, ORNo, FSID, Currency, BillAmount, Serials, data_count) {
            var table = $('#bill-for-transfer-table');
            var row = $('<tr>');
            var transact_date = $('<td class="text-center text-sm text-black p-1">'+ TransactionDate +'</td>');
            var invoice = $('<td class="text-center text-sm text-black p-1">'+ ORNo +'</td>');
            var currency = $('<td class="text-center text-sm text-black p-1">'+ Currency +'</td>');
            var serials = $('<td class="text-center text-sm text-black py-1 px-3">'+ Serials +'</td>');
            var bill_amnt = $('<td class="text-right text-sm text-black py-1 px-3"><strong>'+ BillAmount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</strong></td>');

            row.append(transact_date);
            row.append(invoice);
            row.append(currency);
            row.append(serials);
            row.append(bill_amnt);

            if (data_count >= 10) {
                $('#transfer-summary-container').css({
                    height: data_count * 16
                });
            } else if (data_count < 10) {
                $('#transfer-summary-container').css({
                    height: 'auto'
                });
            }

            table.find('tbody').append(row);
        }

        function clear() {
            $('#bill-cash-count tbody').empty();
            $('#bill-for-transfer-table tbody').empty();
        }
    });
</script>
