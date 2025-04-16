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

    $(document).ready(function(){
        $('.button-edit-rate').click(function(){
            var CRID = $(this).attr('data-expensemaintid');

            $.ajax({
                url: "{{ route('maintenance.rate_maintenance.edit') }}",
                method: "POST",
                data: {
                    CRID: CRID,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('.update-rate-details').html(data);
                }
            });
        });
    });

    $(document).ready(function() {
        $('#rate-add-button').click(function() {
            var curr_rate = $('#currency-rate').val();
            var curr_name = $('#currency-name').val();
            var country_origin = $('#currency-country-origin').val();
            var user_sec_onpage = $('#add-rate-security-code').val();

            $('#proceed-transaction').prop('disabled', true);

            if (curr_name == '' || country_origin == '' || curr_rate == '') {
                Swal.fire({
                    text: 'All fields are required.',
                    icon: 'warning',
                    showConfirmButton: true
                });
            } else {
                if (sec_code_array.includes(user_sec_onpage)) {
                    $('#proceed-transaction').prop('disabled', true);

                    var index = sec_code_array.indexOf(user_sec_onpage);
                    var matched_user_id = user_id_array[index];

                    Swal.fire({
                        title: 'Success!',
                        text: 'Currency successfully added!',
                        icon: 'success',
                        timer: 900,
                        showConfirmButton: false
                    }).then(() => {
                        $('#container-test').fadeIn("slow");
                        $('#container-test').css('display', 'block');

                        var form_data = new FormData($('#add-new-rate-form')[0]);
                        form_data.append('matched_user_id', matched_user_id);

                        setTimeout(() => {
                            $.ajax({
                                url: "{{ route('maintenance.rate_maintenance.save') }}",
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
                        $('#proceed-transaction').prop('disabled', false);
                    });
                }
            }
        });
    });

    $(document).ready(function () {
        $('#currency-name').change(function() {
            $('#currency-country-origin').empty();
            $('#currency-country-origin-true').empty();
            var country_id = $(this).find('option:selected').data('countryid');

            $.ajax({
                url: "{{ route('maintenance.rate_maintenance.select') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    country_id: country_id
                },
                success: function(data) {
                    var countryName = data[0].Country;

                    $('#currency-country-origin').append('<option value="' + country_id + '" selected>' + countryName + '</option>');
                    $('#currency-country-origin-true').val(country_id);
                }
            });
        });
    });

    $(document).ready(function() {
        $('.rate-history').click(function() {
            $.ajax({
                url: "{{ route('maintenance.rate_maintenance.history') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    currency_id: $(this).attr('data-currencyid')
                },
                success: function(data) {
                    clear();
                    var rate_history = data.rate_history;

                    $('#container-test').fadeIn("fast");
                    $('#container-test').css('display', 'block');

                    setTimeout(function() {
                        $('#container-test').fadeOut("fast");

                        if (rate_history.length > 20) {
                            $('#rate-history-container').css('height', '750px');
                        }

                        rate_history.forEach(function(gar) {
                            history(gar.Rate, gar.EntryDate, gar.EntryTime, gar.Name);
                        });
                    }, 1000);
                }
            });
        });

        function history(Rate, EntryDate, EntryTime, Name) {
            var table = $('#rate-history-table');
            var row = $('<tr>');
            var rate = $('<td class="text-sm font-bold text-black whitespace-nowrap py-1 pe-3 text-right">'+ Rate +'</td>')
            var entry_date = $('<td class="text-sm text-black whitespace-nowrap p-1 text-center">'+ EntryDate +'</td>')
            var entry_time = $('<td class="text-sm text-black whitespace-nowrap p-1 text-center">'+ EntryTime +'</td>')
            var updated_by = $('<td class="text-sm text-black whitespace-nowrap p-1 text-center">'+ Name +'</td>')

            row.append(updated_by);
            row.append(entry_date);
            row.append(entry_time);
            row.append(rate);

            table.find('tbody').append(row);
        }

        function clear() {
            $('#rate-history-table tbody').empty();
        }
    });
</script>
