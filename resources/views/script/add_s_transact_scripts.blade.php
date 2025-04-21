{{-- Selling Transaction Table --}}
<script>
    $(document).ready(function() {
        if ($('#selling-print-count').val() == 0) {
            $('#printing-receipt-selling').click();
        }
    });

    $(document).ready(function() {
        $('input[name="radio-search-type"]').change(function() {
            $('#invoice-searching, #date-range-searching').toggleClass('d-none');
        });
    });
    
    $(document).ready(function() {
        // Selling Transaction Table
        var selling_total_curr_amount = 0;

        $('.selling-transact-details-list-table').each(function() {
            var currency_amount_selling = parseFloat($(this).find('.total-amountpaid-selling').val());

            selling_total_curr_amount += currency_amount_selling;
        });

        $('#selling-trans-amount').text(selling_total_curr_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
    });

    var global_selling_trans_id;

    $(document).ready(function(){
        $('.button-delete-selling-trans-details').on('click', function(){
            const trans_id = $(this).attr('data-sellingtransdetailsid');
            global_selling_trans_id = trans_id;
        });

        function deleteSellingTransact(trans_id) {
            $('#proceed-transaction').click(function() {
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
                                text: 'Selling transaction deleted!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                setTimeout(function() {
                                    $.ajax({
                                        type: 'GET',
                                        url: "{{ route('branch_transactions.selling_transaction.delete') }}",
                                        data: {
                                            _token: "{{ csrf_token() }}",
                                            trans_id: trans_id,
                                        },
                                        success: function(response) {
                                            window.location.reload();
                                            console.log('Transaction with ID ' + trans_id + ' deleted successfully!');
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
                            });
                        }
                    }
                });
            });
        }

        $('.button-delete-selling-trans-details').on('click', function() {
            const trans_id = $(this).attr('data-sellingtransdetailsid');
            deleteSellingTransact(trans_id);

            $('#security-code-modal').modal("show");
        });
    });

    $(document).ready(function() {        
        $('#sc-details-button').click(function() {
            $.ajax({
                url: "{{ route('branch_transactions.selling_transaction.sc_details') }}?{!! http_build_query(request()->query()) !!}",
                type: "post",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(data) {
                    empty();
                    loader();
                    var deets = data.details;

                    if (deets.length > 0) {
                        clearLoader();

                        deets.forEach(function(gar) {
                            table(gar.Currency, gar.CurrAmount, gar.FullName, gar.ORNo, gar.RIBAmount, gar.whole_rate, gar.decimal_rate);
                        });
                    } else {
                        clearLoader();

                        var banner = $(`<tr><td class="text-black text-center" colspan="10">No data available.</td></tr>`);
                        $('#sc-details-table tbody').append(banner);
                    }

                }
            })              
        });

        function table(Currency, CurrAmount, FullName, ORNo, RIBAmount, whole_rate, decimal_rate) {
            var table = $('#sc-details-table');
            var row = $('<tr>');

            var rate_used = whole_rate + parseFloat(decimal_rate);

            var decimal_places = rate_used.toString().includes('.') ? rate_used.toString().split('.')[1].length : 0;

            var formatted_rate = decimal_places <= 2 ? (Math.floor(rate_used * 100) / 100).toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) : (Math.floor(rate_used * 1000) / 1000).toLocaleString("en" , {minimumFractionDigits: 4 , maximumFractionDigits: 4});

            row.append(`<td class="text-sm text-center text-black whitespace-nowrap p-1">${ORNo}</td>`);
            row.append(`<td class="text-sm text-center text-black whitespace-nowrap p-1">${FullName}</td>`);
            row.append(`<td class="text-sm text-center text-black whitespace-nowrap p-1">${Currency}</td>`);
            row.append(`<td class="text-sm text-right text-black whitespace-nowrap py-1 pe-3">${parseFloat(CurrAmount).toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</td>`);
            row.append(`<td class="text-sm text-right text-black whitespace-nowrap py-1 pe-3">${formatted_rate}</td>`);
            row.append(`<td class="text-sm text-right text-black whitespace-nowrap py-1 pe-3">${parseFloat(RIBAmount).toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</td>`);

            table.find('tbody').append(row);
        }

        function empty() {
            $('#sc-details-table tbody').empty();
        }

        function loader() {
            $('#container-test').fadeIn("fast");
            $('#container-test').css('display', 'block');
        }

        function clearLoader() {
            $('#container-test').fadeOut("fast");
        }
    });

    $(document).ready(function() {
        $('.report-error-modal-btn').click(function() {
            $('#ID').val($(this).attr('data-id'));
            $('#menu-id').val($(this).attr('data-menuid'));

            $.ajax({
                url: "{{ route('report_error') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    menu_id: $(this).attr('data-menuid'),
                },
                success: function (data) {
                    clear();

                    var ticket_concern = data.css_concerns;

                    ticket_concern.forEach(function(gar) {
                        appendConcerns(gar.SConcernID, gar.SConcern);
                    });
                }
            });
        });

        function appendConcerns(SConcernID, SConcern) {
            var options = $(`<option value="${SConcernID}">${SConcern}</option>`);

            $('#sconcernid').append(options);
        }

        function clear() {
            $('#sconcernid').empty();
            $('#sconcernid').append(`<option value="">Select Concern</option>`);
        }
    });

    $(document).ready(function() {
        let rate_used_field = $('#sold-currency-rate-used');

        $('#update-s-transact-details').click(function() {
            $('#container-test').fadeIn("fast");
            $('#container-test').css('display', 'block');

            setTimeout(function() {
                $('#container-test').fadeOut("fast");

                $('#or-number-container-deet').toggleClass('d-none');
                $('#or-no-details-cont').toggleClass('d-none');

                $('#rset-container').toggleClass('d-none');
                $('#rset-details-cont').toggleClass('d-none');

                $('#customer-container').toggleClass('d-none');
                $('#customer-details-cont').toggleClass('d-none');

                $('#update-transction-btn').toggleClass('d-none');

                if (rate_used_field.attr('readonly')) {
                    rate_used_field.removeAttr('readonly');
                } else {
                    rate_used_field.attr('readonly', 'readonly');
                }
            }, 500);
        });

        rate_used_field.keyup(function() {
            var new_total_amnt = $(this).val() * $('#t-sold-currency-curr-amnt').val();

            console.log(new_total_amnt);

            $('input[name="true-sold-currency-total-amnt"]').val(new_total_amnt);
            $('#sold-currency-total-amnt').val(new_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
        });
    });

    $(document).ready(function() {
        var trans_id = '';

        $('#update-transction-btn').on('click', function(){
            trans_id = $('#serials-scid').val();
        });

        $('#update-transaction').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#update-s-trans-security-code').val();

            $('#update-transaction').prop('disabled', true);

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
                        $('#update-transaction').prop('disabled', true);

                        var index = sec_code_array.indexOf(user_sec_onpage);
                        var matched_user_id = user_id_array[index];

                        Swal.fire({
                            title: 'Success!',
                            text: 'Details updated!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            setTimeout(function() {
                                $('#container-test').fadeIn("fast");
                                $('#container-test').css('display', 'block');

                                var form_data = new FormData($('#update-selling-trans-details')[0]);
                                form_data.append('matched_user_id', matched_user_id);
                                form_data.append('trans_id', trans_id);

                                $.ajax({
                                    url: "{{ route('branch_transactions.selling_transaction.update') }}",
                                    type: "post",
                                    data: form_data,
                                    contentType: false,
                                    processData: false,
                                    cache: false,
                                    success: function(data) {
                                        // var route = "{{ route('branch_transactions.buying_transaction.details', ['id' => ':id']) }}";
                                        // var url = route.replace(':id', data.latest_ftdid);

                                        // window.location.href = url;
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
                        }).then(()=> {
                            $('#update-transaction').prop('disabled', false);
                        });
                    }
                }
            });
        });

        $('input[name="radio-rset"]').change(function() {
            console.log($('#sold-currency-or-number').val());

            if ($(this).val() == 'O') {
                $('#or-number-selling').prop('readonly', false).val($('#sold-currency-or-number').val());
            } else if ($(this).val() == 'B') {
                $('#or-number-selling').prop('readonly', true).val('');
            }
        });
    });
</script>

