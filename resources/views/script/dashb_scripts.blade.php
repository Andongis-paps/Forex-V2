<script>
    $(document).ready(function () {
        function updateClock() {
            var now = new Date();
            var hours = now.getHours();
            var minutes = now.getMinutes().toString().padStart(2, '0');
            var meridiem = hours >= 12 ? 'PM' : 'AM';

            hours = hours % 12 || 12;

            var timeString = hours + ":" + minutes + " " + meridiem;
            $("#clock").text(timeString);
        }

        updateClock();

        var delay = 60000 - (new Date().getSeconds() * 1000 + new Date().getMilliseconds());

        setTimeout(function () {
            updateClock();
            setInterval(updateClock, 60000);
        }, delay);

        if ($('.old-stock-currency').val()) {
            let gar = '';
            let array = [];
            let height = '';
            let currency = '';
            let plural_form = '';

            $('.old-stock-currency').each(function() {
                array.push($(this).val());
            });

            array.forEach(function(test) {
                currency +=   `<tr><td class="p-2 text-center text-xs border-t-gray-300">`+ test + `</td></tr>`;
            })

            gar = array.length > 1? 'are' : 'is';
            ff = array.length > 1? 'from the' : 'from the following';
            plural_form = array.length > 1? 'currency' : 'currencies';
            height = array.length > 9? 'height: 300px!important;' : 'height: auto; ';

            Swal.fire({
                html:
                `<div class="mb-2">
                    <span class="text-sm text-black">Overdue stocks `+ gar +` seen `+ ff +` `+ plural_form +` listed below</span>:                                                   
                </div>
                <div class="row px-2">
                    <div class="col-12 border border-gray-300 p-0" style="${height} overflow: hidden; overflow-y: scroll;">
                        <table class="table table-hover mb-0">
                            <thead style="position: sticky; top: 0; background: #fff; z-index: 3;">
                                <tr>
                                    <th class="border-gray-300 py-1 text-black font-bold text-center">Branch</th>
                                </tr>
                            </thead>
                            <tbody>
                                `+ currency +`
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="my-2">
                    <span><small><i class="bx bx-info-circle me-1"></i>Note: Stocks that are <strong>3 days old or older</strong> are considered <strong>overdue stocks</strong>.</small></span>
                </div>`,
                icon: 'warning',
                showConfirmButton: true,
                confirmButtonText: 'Proceed',
                showClass: {
                    popup: 'swal2-zoom-in'
                },
            });
        }

        $('#stocks-button').click(function() {
            $.ajax({
                url: "{{ route('branch_transactions.dashbooard.stocks') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(data) {
                    clear();
                    loader();
                    var regular = data.stocks_set_o;

                    if (regular.length > 0) {
                        clearLoader();
                        $('#regular').show();

                        regular.forEach(function(gar) {
                            populateRegular(gar.Currency, gar.BillAmount, gar.bill_amount_count, gar.Rset, gar.serials, gar.sub_total, gar.max_days);
                        });
                    } else {
                        $('#regular').hide();
                    }
                }
            });
        });

        function populateRegular(Currency, BillAmount, bill_amount_count, Rset, serials, sub_total, max_days) {
            var days = max_days <= 1 ? `Day` : `Days`;

            var days_badge = max_days <= 3 ? `<span class="badge primary-badge-custom">${max_days <= 0 ? `New` : `${max_days} ${days}`} </span>` : `<span class="badge warning-badge-custom">${max_days} ${days}</span>`;

            var table = $('#reg-stocks-o-table');
            var row = $('<tr>');
            var currency = $(`<td class="text-center text-black text-sm p-1">${Currency}</td>`);
            var denomination = $(`<td class="text-center text-black text-sm p-1">${BillAmount.toFixed(2)}</td>`);
            var count = $(`<td class="text-center text-black text-sm p-1">${bill_amount_count}</td>`);
            var sub_total = $(`<td class="text-end text-black text-sm py-1 pe-2"><strong>${sub_total.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</strong></td>`);
            var days_old = $(`<td class="text-center text-black text-sm py-1"><strong>${days_badge}</strong></td>`);
            
            row.append(currency);
            row.append(denomination);
            row.append(count);
            row.append(sub_total);
            row.append(days_old);

            table.append(row);
        }

        $('#buffer-button').click(function() {
            $.ajax({
                url: "{{ route('branch_transactions.dashbooard.buffer') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(data) {
                    clear();
                    loader();
                    var buffer = data.buffer_o;

                    if (buffer.length > 0) {
                        clearLoader();
                        $('#buffer').show();

                        buffer.forEach(function(gar) {
                            populateBuffer(gar.Currency, gar.BillAmount, gar.bill_amount_count, gar.Rset, gar.serials, gar.sub_total);
                        });
                    } else {
                        $('#buffer').hide();
                    }
                }
            });
        });

        function populateBuffer(Currency, BillAmount, bill_amount_count, Rset, serials, sub_total) {
            var table = $('#buffer-stocks-table');
            var row = $('<tr>');
            var currency = $(`<td class="text-center text-black text-sm p-1">${Currency}</td>`);
            var denomination = $(`<td class="text-center text-black text-sm p-1">${BillAmount.toFixed(2)}</td>`);
            var count = $(`<td class="text-center text-black text-sm p-1">${bill_amount_count}</td>`);
            var sub_total = $(`<td class="text-end text-black text-sm py-1 pe-2"><strong>${sub_total.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})}</strong></td>`);
            
            row.append(currency);
            row.append(denomination);
            row.append(count);
            row.append(sub_total);

            table.append(row);
        }

        function clear() {
            $('#reg-stocks-o-table tbody').empty();
            $('#buffer-stocks-table tbody').empty();
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
        $.ajax({
            url: "{{ route('branch_transactions.dashbooard.b_sales_breakd_down') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
            },
            success: function(data) {
                var buying_sales_breakdown = data.buying_sales_breakdown;

                var amount = [];
                var trans_type_arr = [];
                var transction_count = [];

                if (buying_sales_breakdown.length < 1) {
                    $('#has-transaction').hide();
                    $('#has-no-transaction').show();
                } else {
                    $('#has-transaction').show();
                    $('#has-no-transaction').hide();
                }

                buying_sales_breakdown.forEach(function(gar) {
                    amount.push(gar.Amount);
                    trans_type_arr.push(gar.TransType);
                    transction_count.push(gar.transct_count);
                });

                var merged_data = transction_count.map(function(val, index) {
                    return {
                        b_transct_count: val,
                        b_amount: amount[index],
                    };
                });

                var options = {
                    series: [{
                        name: 'Transaction Count',
                        data: transction_count,
                    }],
                    // }, {
                    //     name: 'Total Amount',
                    //     data: amount,
                    // }],
                    chart: {
                        height: 190,
                        type: 'bar',
                        events: {
                            click: function(chart, w, e) {
                            // console.log(chart, w, e)
                            }
                        },
                        toolbar: {
                            show: false
                        },
                        style: {
                            colors: ['#fff'],
                        },
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '30%',
                            distributed: true,
                            // borderRadius: 10,
                        }
                    },
                    colors: ['#00A65A', '#2d85ff', '#FFAB00', '#ed404e'],
                    dataLabels: {
                        enabled: true,
                        style: {
                            colors: ['#fff'],
                            fontSize: '16px',
                            fontWeight: 'bold',
                            fontFamily: 'Nunito-Regular, sans-serif',
                        },
                    },
                    legend: {
                        show: false
                    },
                    xaxis: {
                        categories: trans_type_arr,
                        labels: {
                            style: {
                                colors: 'gray',
                                fontSize: '13px',
                                fontWeight: 'medium',
                                fontFamily: 'Nunito-Regular, sans-serif',
                            }
                        }
                    },
                    yaxis: {
                        show: false
                    },
                };

                var chart = new ApexCharts(document.querySelector("#chart"), options);
                chart.render();
            }
        });
    });
</script>
