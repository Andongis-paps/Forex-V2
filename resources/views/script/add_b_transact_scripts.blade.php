{{-- Buying Transaction Table --}}
<script>
    $(document).ready(function() {
        if ($('#buying-print-count').val() == 0) {
            $('#printing-receipt-buying').click();
        }
    });

    $(document).ready(function() {
        $('input[name="radio-search-type"]').change(function() {
            $('#invoice-searching, #date-range-searching').toggleClass('d-none');
        });
    });
    
    $(document).ready(function() {
       var total_curr_amount = 0;

       $('.transact-details-list-table').each(function() {
           var currency_amount = parseFloat($(this).find('.currency-amount').val());

           total_curr_amount += currency_amount;
       });

       $('#trans-amount').text(total_curr_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
    });

    $(document).ready(function() {        
        $('#sc-details-button').click(function() {
            $.ajax({
                url: "{{ route('branch_transactions.buying_transaction.sc_details') }}?{!! http_build_query(request()->query()) !!}",
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
                            table(gar.CurrencyID, gar.Currency, gar.CurrencyAmount, gar.FullName, gar.ORNo, gar.RIBAmount, gar.whole_rate, gar.decimal_rate);
                        });
                    } else {
                        clearLoader();

                        var banner = $(`<tr><td class="text-black text-center" colspan="10">No data available.</td></tr>`);
                        $('#sc-details-table tbody').append(banner);
                    }

                }
            })              
        });

        function table(CurrencyID, Currency, CurrencyAmount, FullName, ORNo, RIBAmount, whole_rate, decimal_rate) {
            var table = $('#sc-details-table');
            var row = $('<tr>');

            var rate_used = whole_rate + parseFloat(decimal_rate);
            var decimal_places = rate_used.toString().includes('.') ? rate_used.toString().split('.')[1].length : 0;

            var formatted_rate = decimal_places <= 2 ? (Math.floor(rate_used * 100) / 100).toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) : (Math.floor(rate_used * 10000) / 10000).toLocaleString("en" , {minimumFractionDigits: 4 , maximumFractionDigits: 4});

            row.append(`<td class="text-sm text-center text-black whitespace-nowrap p-1">${ORNo}</td>`);
            row.append(`<td class="text-sm text-center text-black whitespace-nowrap p-1">${FullName}</td>`);
            row.append(`<td class="text-sm text-center text-black whitespace-nowrap p-1">${Currency}</td>`);
            row.append(`<td class="text-sm text-right text-black whitespace-nowrap py-1 pe-3">${parseFloat(CurrencyAmount).toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</td>`);
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
</script>

{{-- Delete buying transaction --}}
<script>
    var global_trans_id;

    $(document).ready(function(){
        $('.button-delete-trans-details').on('click', function(){
            const trans_id = $(this).attr('data-transdetailsid');
            global_trans_id = trans_id;
        });

        function deleteTransact(trans_id) {
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
                                text: 'Buying transaction deleted!',
                                icon: 'success',
                                timer: 900,
                                showConfirmButton: false
                            }).then(() => {
                                setTimeout(function() {
                                    $.ajax({
                                        type: 'GET',
                                        url: "{{ route('branch_transactions.buying_transaction.delete') }}",
                                        data: {
                                            _token: "{{ csrf_token() }}",
                                            trans_id: trans_id,
                                        },
                                        success: function(response) {
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
                                $('#proceed-transaction').prop('disabled', false);
                            });
                        }
                    }
                });
            });
        }

        $('.button-delete-trans-details').on('click', function() {
            const trans_id = $(this).attr('data-transdetailsid');
            deleteTransact(trans_id);

            $('#security-code-modal').modal("show");
        });

        $('#showall').change(function() {
            $('#normal-search').click();
            $('#container-test').fadeIn("slow");
            $('#container-test').css('display', 'block');
        });
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
        $('#update-b-transact-details').click(function() {
            $('#container-test').fadeIn("fast");
            $('#container-test').css('display', 'block');

            setTimeout(function() {
                $('#container-test').fadeOut("fast");

                $('#or-number-container-deet').toggleClass('d-none');
                $('#or-no-details-cont').toggleClass('d-none');

                $('#mtcn-container').toggleClass('d-none');
                $('#mtcn-cont').toggleClass('d-none');

                $('#rset-container').toggleClass('d-none');
                $('#rset-details-cont').toggleClass('d-none');

                $('#customer-container').toggleClass('d-none');
                $('#customer-details-cont').toggleClass('d-none');

                $('#update-transction-btn').toggleClass('d-none');
            }, 500);
        });

        $('#update-b-rate').click(function() {
            var test = $('#og-total-amount').val();
            $('#container-test').fadeIn("fast");
            $('#container-test').css('display', 'block');

            setTimeout(function() {
                var rate_array = [];
                $('#container-test').fadeOut("fast");

                $('.update-rate').toggleClass('d-none');
                $('.read-only-rate').toggleClass('d-none');
                $('#update-new-rates').toggleClass('d-none');

                $('.bill-rate-input').each(function(index) {
                    let rate = $(this).val();
                    $('.current-rates').eq(index).val(rate);
                });
                
                $('#buying-receipt-total-amount').val(test);
            }, 500);

            $('.current-rates').on('keyup', function() {
                rateValues();
            });

            function rateValues() {
                var new_total_amount = 0;
                var table = $('#bill-summary-table');
                var sub_totals = table.find('.bill-total-input');

                sub_totals.each(function() {
                    var rate_input = $(this).closest('tr').find('.form-control#current-rates');
                    var rate_val = rate_input.val();
                    var sub_total_val = parseFloat($(this).closest('tr').find('.form-control#bill-total-input').val().toString().replace(/,/g, ""));
                    var true_sub_total = sub_total_val * rate_val;

                    new_total_amount += parseFloat(true_sub_total);
                });

                var integer_part = Math.floor(new_total_amount);
                var decim_part = new_total_amount - integer_part;

                if (decim_part < 0.25) {
                    decim_part = 0;
                } else if (decim_part >= 0.25 && decim_part < 0.50) {
                    decim_part = 0.25;
                } else if (decim_part >= 0.50 && decim_part < 0.75) {
                    decim_part = 0.50;
                } else if (decim_part >= 0.75 && decim_part < .94) {
                    decim_part = 0.75;
                }

                var rounded_total_amnt = integer_part + decim_part;

                $('#buying-receipt-total-amount').val(rounded_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})).text(rounded_total_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            }
        });

        $('#update-rate-transaction').click(function() {
            var rates = [];
            var denom_ids = [];
            var total_amount = [];
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#update-b-rate-security-code').val();

            $('.denom-id').each(function() {
                denom_ids.push($(this).val());
            });
            
            $('.bill-total-input').each(function() {
                total_amount.push($(this).val());
            });

            $('.current-rates').each(function() {
                rates.push($(this).val());
            });

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
                        $('#update-rate-transaction').prop('disabled', true);

                        var index = sec_code_array.indexOf(user_sec_onpage);
                        var matched_user_id = user_id_array[index];

                        Swal.fire({
                            title: 'Success!',
                            text: 'Rate updated!',
                            icon: 'success',
                            timer: 900,
                            showConfirmButton: false
                        }).then(() => {
                            setTimeout(function() {
                                var form_data = new FormData($('#update-rates')[0]);
                                form_data.append('matched_user_id', matched_user_id);
                                form_data.append('FTDID', $('#serials-ftdid').val())
                                form_data.append('rates', rates.join(", "));
                                form_data.append('denom_ids', denom_ids.join(", "));
                                form_data.append('total_amount', total_amount.join(", "));
                                form_data.append('new_total_amnt', $('#buying-receipt-total-amount').val());
                              
                                $.ajax({
                                    url: "{{ route('branch_transactions.buying_transaction.update_rate') }}",
                                    type: "post",
                                    data: form_data,
                                    contentType: false,
                                    processData: false,
                                    cache: false,
                                    success: function(data) {
                                        var route = "{{ route('branch_transactions.buying_transaction.details', ['id' => ':id']) }}";
                                        var url = route.replace(':id', data.FTDID);

                                        window.location.href = url;
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
                            $('#update-rate-transaction').prop('disabled', false);
                        });
                    }
                }
            });
        });
    });

    $(document).ready(function() {
        var trans_id = '';

        $('#update-transction-btn').on('click', function(){
            trans_id = $('#serials-ftdid').val();
        });

        $('#update-transaction').click(function() {
            var user_id_array = [];
            var sec_code_array = [];
            var user_sec_onpage = $('#update-b-trans-security-code').val();

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

                                var form_data = new FormData($('#update-buying-trans-details')[0]);
                                form_data.append('matched_user_id', matched_user_id);
                                form_data.append('trans_id', trans_id);

                                $.ajax({
                                    url: "{{ route('branch_transactions.buying_transaction.update') }}",
                                    type: "post",
                                    data: form_data,
                                    contentType: false,
                                    processData: false,
                                    cache: false,
                                    success: function(data) {
                                        var route = "{{ route('branch_transactions.buying_transaction.details', ['id' => ':id']) }}";
                                        var url = route.replace(':id', data.FTDID);

                                        window.location.href = url;
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
            if ($(this).val() == 'O') {
                $('#or-number-buying').prop('readonly', false).val($('#buying-receipt-receipt-number').val());
            } else if ($(this).val() == 'B') {
                $('#or-number-buying').prop('readonly', true).val('');
            }
        });
    });
</script>





