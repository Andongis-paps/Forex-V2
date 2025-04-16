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

    $(document).ready(function() {
        let annual_amnt = 0;

        $('#series-set-o').on('input', function() {
            var input_val = $(this).val().replace(/\D/g, '');

            if (input_val.length > 6) {
                input_val = input_val.slice(-6);
            }

            var formatted_series = input_val.padStart(6, '0');
            $(this).val(formatted_series);
        });

        $('#series-set-o').keyup(function() {
            if ($(this).val() == 0 || $(this).val() == '') {
                $('#company').attr('disabled', true);
            } else {
                $('#company').attr('disabled', false);
            }
        })

        $('#company').change(function(){
            if ($(this).val() == 'default') {
                $('#annual-amount').attr('disabled', true);
            } else {
                $('#annual-amount').attr('disabled', false);
            }
        });

        $('#annual-amount').keyup(function() {
            let total_annual_amnt = 0;
            annual_amnt = $(this).val();

            if ($(this).val() <= 0) {
                $('.monthly-amount, .monthly-percentage').attr('disabled', true);
            } else {
                $('.monthly-amount, .monthly-percentage').attr('disabled', false);
            }

            $('.monthly-percentage').each(function() {
                var percentage = $(this).val();
                var amount_limit = (percentage / 100) * annual_amnt;

                total_annual_amnt += amount_limit;

                $(this).closest('tr').find('.monthly-amount').val(amount_limit.toFixed(2));
            });

            $('#total-annual-amnt').text('').text(total_annual_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
        });

        $('.percentage-td').find('input').on('keyup', function () {
            percentage($(this)); 
        });

        function percentage(curr_input) {
            let total_percentage = 0;
            let total_annual_amnt = 0;
            let table = $('#company-limit-table');
            let percentage_tds = table.find('.percentage-td');
            let amount_input = '';
            
            percentage_tds.each(function () {
                let percentage_input = $(this).find('.form-control');
                let per_val = parseFloat(percentage_input.val()) || 0;

                let row = $(this).closest('tr');
                amount_input = row.find('.monthly-amount');
                let amnt_val = parseFloat(amount_input.val()) || 0;
                var amnt_per_month = (per_val / 100) * annual_amnt;

                amount_input.val(amnt_per_month);
                total_annual_amnt += amnt_per_month;

                if (!curr_input.is(percentage_input)) {
                    total_percentage += per_val;
                }
            });

            let current_val = parseFloat(curr_input.val()) || 0;
            total_percentage += current_val;

            $('#total-percentage').empty().text(total_percentage.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            $('#total-annual-amnt').text('').text(total_annual_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));

            if (total_percentage > 100) {
                let row = curr_input.closest('tr');
                let amount_input = row.find('.monthly-amount');

                Swal.fire({
                    html: `<span class="text-sm">Percentage must not exceed the 100% limit.</span>`,
                    icon: 'warning',
                    showCancelButton: false,
                });

                amount_input.val('').attr('placeholder', '0.00');
                curr_input.val('').attr('placeholder', '0.00');
            }
        }

        $('#add-company-limit').click(function()  {
            var fields_array = [];

            $('.monthly-percentage').each(function() {
                if ($(this).val() > 0) {
                    fields_array.push($(this).val());
                }
            });

            if (fields_array.length < 12) {
                Swal.fire({
                    html: `<span class="text-sm">All fields are required.</span>`,
                    icon: 'error',
                    showCancelButton: false,
                });
            } else {
                $('#security-code-modal').modal("show");
            }
        });
    });

    $(document).ready(function() {
        var month = [];
        var amount = [];
        var percentage = [];

        $('#proceed-transaction').click(function() {
            $(this).prop('disabled', true);
            var user_sec_onpage = $('#security-code').val();

            $('.monthly-percentage').each(function() {
                percentage.push($(this).val());
            });

            $('.monthly-amount').each(function() {
                amount.push($(this).val());
            });

            $('.month').each(function() {
                month.push($(this).val());
            });

            if (sec_code_array.includes(user_sec_onpage)) {
                $('#proceed-transaction').prop('disabled', true);

                var index = sec_code_array.indexOf(user_sec_onpage);
                var matched_user_id = user_id_array[index];
                
                Swal.fire({
                    title: 'Success!',
                    text: 'Company limit configuration added!',
                    icon: 'success',
                    timer: 900,
                    showConfirmButton: false
                }).then(() => {
                    $('#container-test').fadeIn("slow");
                    $('#container-test').css('display', 'block');

                    var form_data = new FormData($('#add-company-limit-form')[0]);
                    form_data.append('month', month.join(", "));
                    form_data.append('amount', amount.join(", "));
                    form_data.append('percentage', percentage.join(", "));
                    form_data.append('matched_user_id', matched_user_id);

                    $.ajax({
                        url: "{{ route('maintenance.company_limit.save_company_limit') }}",
                        type: "post",
                        data: form_data,
                        contentType: false,
                        processData: false,
                        cache: false,
                        success: function(data) {
                            var route = "{{ route('maintenance.company_limit') }}";

                            window.location.href = route;
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
        });
    });
</script>