<script type="text/javascript" src="{{ asset('plugins/QZTray/demo/js/qz-websocket.js ') }}"></script>

<script>
    // Forex - Buying Transaction Receipt Printing
    var b_trans_id = $('#serials-ftdid').val();
    var b_trans_date = $('#buying-receipt-transact-date').val();
    var b_trans_number = $('#buying-receipt-transact-number').val();
    var b_trans_receipt_no = $('#buying-receipt-receipt-number').val();
    var b_trans_curr_amount = $('#buying-receipt-currency-amount').val();
    var b_trans_rate_used = $('#buying-receipt-rate-used').val();
    var b_trans_rset = $('#buying-receipt-rset').val();
    var b_trans_total_amount = $('#buying-receipt-total-amount').val();
    var b_trans_or_number = $('#buying-receipt-or-number').val();
    var b_trans_currency = $('#buying-receipt-currency').val();
    var b_trans_customer = $('#buying-receipt-customer').val();
    var b_trans_customer_no = $('#transact-customer-id').val();
    var b_trans_transact_type = $('#buying-receipt-transact-type').val();
    var b_trans_currency_abbrev = $('#buying-receipt-currency-abbrev').val();
    var b_trans_transacted_by = $('#buying-receipt-transacted-by').val();
    var b_trans_print_count = $('#buying-print-count').val();
    var b_trans_rate_used = $('#buying-rate-input').val();

    var bill_summary_table = $('#bill-summary-table');
    var bill_amount = bill_summary_table.find('.bill-amount-input');
    var bill_count = bill_summary_table.find('.bill-count-input');
    var bill_total = bill_summary_table.find('.bill-total-input');
    var bill_rate = bill_summary_table.find('.bill-rate-input');

    var b_bill_amnts_array = [];
    var b_bill_count_array = [];
    var b_bill_total_array = [];
    var b_buying_rate_array = [];

    // Redirect - Auto print of the buying transaction receipt
    $(document).ready(function() {
        var forex_serials_base_url = $('#full-url-serials').val();
        var forex_serials_ftdid = $('#serials-ftdid').val();
        var forex_serials_full_url = forex_serials_base_url + '/' + forex_serials_ftdid;

        if (window.location.href == forex_serials_full_url) {
            if ($('#buying-print-count').val() <= 0) {
                $("#printing-receipt-buying").click();
            }
        }
    });

    $('#printing-receipt-buying').click(function() {
        $("#transact-date").text(b_trans_date);
        $("#transact-number").text(b_trans_number);
        // $("#transact-receipt-number").text(b_trans_receipt_no);
        $("#transact-receipt-number").text(b_trans_receipt_no != '' ? b_trans_receipt_no : '0');
        $("#transact-rset").text(b_trans_rset);
        $("#transact-or-number").text(b_trans_or_number);
        $("#transact-customer").text(b_trans_customer);
        $("#transact-currency").text(b_trans_currency);
        $("#transact-type").text(b_trans_transact_type);
        $("#transact-currency-amount").text(b_trans_curr_amount);
        $("#transact-amount").text(b_trans_total_amount);
        $("#transact-user-processed").text();

        $('#buying-transact-modal').modal("show");
        $('#print-buying-receipt-modal').modal("hide");

        bill_amount.each(function() {
            var bill_amnt_field = $(this).closest('tr').find('.form-control.bill-amount-input');
            var bill_amnt_val = bill_amnt_field.val();

            b_bill_amnts_array.push(bill_amnt_val);
        });

        bill_count.each(function() {
            var bill_count_field = $(this).closest('tr').find('.form-control.bill-count-input');
            var bill_amnt_val = bill_count_field.val();

            b_bill_count_array.push(bill_amnt_val);
        });

        bill_total.each(function() {
            var bill_total_field = $(this).closest('tr').find('.form-control.bill-total-input');
            var bill_amnt_val = bill_total_field.val();

            b_bill_total_array.push(bill_amnt_val);
        });

        bill_rate.each(function() {
            var bill_rate_field = $(this).closest('tr').find('.form-control.bill-rate-input');
            var bill_amnt_val = bill_rate_field.val();

            b_buying_rate_array.push(bill_amnt_val);
        });

    });

    $('#proceed-transaction').click(function() {
        var user_id_array = [];
        var sec_code_array = [];
        var _token = $('input[name="_token"]').val();
        var on_page_final_printing = $('#security-code').val();

        $.ajax({
            url: "{{ route('user_info') }}",
            type: "GET",
            data: {
                _token: _token,
            },
            success: function(get_user_info) {
                var user_info = get_user_info.security_codes;

                user_info.forEach(function(gar) {
                    sec_code_array.push(gar.SecurityCode);
                    user_id_array.push(gar.UserID);
                });

                if (sec_code_array.includes(on_page_final_printing)) {
                    $('#proceed-transaction').prop('disabled', true);

                    var index = sec_code_array.indexOf(on_page_final_printing);
                    var matched_user_id = user_id_array[index];
                    buyingTransactReceipt();

                    let timerInterval;

                    Swal.fire({
                        title: "Printing...",
                        timer: 3000,
                        didOpen: () => {
                            Swal.showLoading();
                                const timer = Swal.getPopup().querySelector("b");
                            timerInterval = setInterval(() => {
                                timer.textContent = `${Swal.getTimerLeft()}`;
                            }, 100);
                        },
                        willClose: () => {
                            clearInterval(timerInterval);
                        }
                    }).then((result) => {
                        $.ajax({
                            url: "{{ route('branch_transactions.buying_transaction.print_count_buying') }}",
                            type: "post",
                            data: {
                                _token: "{{ csrf_token() }}",
                                b_trans_id: b_trans_id
                            },
                            success: function(data) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Receipt printed!',
                                    text: 'Receipt successfully printed.',
                                    customClass: {
                                        popup: 'my-swal-popup',
                                    }
                                }).then(() => {
                                    setTimeout(function() {
                                        $('#final-print-receipt-modal').modal('hide');
                                        b_trans_print_count = data.print_b_count_latest;
                                        buyingTransactReceipt();

                                        var route = "{{ route('branch_transactions.buying_transaction') }}";

                                        window.location.href = route;
                                    }, 200);
                                });
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
                    });
                }
            }
        });
    });

    window["deployQZ"] = typeof(deployQZ) == "function" ? deployQZ : deployQZApplet;

    function deployQZApplet() {
        console.log('Starting deploy of qz applet');

        var attributes = {id: "qz", code:'qz.PrintApplet.class',
            archive:'../qz-print.jar', width:1, height:1};
        var parameters = {jnlp_href: '../qz-print_jnlp.jnlp',
            cache_option:'plugin', disable_logging:'false',
            initial_focus:'false', separate_jvm:'true'};

        if (deployJava.versionCheck("1.7+") == true) {

        }
        else if (deployJava.versionCheck("1.6.0_45+") == true) {

        }
        else if (deployJava.versionCheck("1.6+") == true) {
            delete parameters['jnlp_href'];
        }

        deployJava.runApplet(attributes, parameters, '1.6');
    }

    deployQZ();

    function getCertificate(callback) {
        callback("-----BEGIN CERTIFICATE-----\n" +
            "MIIFAzCCAuugAwIBAgICEAIwDQYJKoZIhvcNAQEFBQAwgZgxCzAJBgNVBAYTAlVT\n" +
            "MQswCQYDVQQIDAJOWTEbMBkGA1UECgwSUVogSW5kdXN0cmllcywgTExDMRswGQYD\n" +
            "VQQLDBJRWiBJbmR1c3RyaWVzLCBMTEMxGTAXBgNVBAMMEHF6aW5kdXN0cmllcy5j\n" +
            "b20xJzAlBgkqhkiG9w0BCQEWGHN1cHBvcnRAcXppbmR1c3RyaWVzLmNvbTAeFw0x\n" +
            "NTAzMTkwMjM4NDVaFw0yNTAzMTkwMjM4NDVaMHMxCzAJBgNVBAYTAkFBMRMwEQYD\n" +
            "VQQIDApTb21lIFN0YXRlMQ0wCwYDVQQKDAREZW1vMQ0wCwYDVQQLDAREZW1vMRIw\n" +
            "EAYDVQQDDAlsb2NhbGhvc3QxHTAbBgkqhkiG9w0BCQEWDnJvb3RAbG9jYWxob3N0\n" +
            "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtFzbBDRTDHHmlSVQLqjY\n" +
            "aoGax7ql3XgRGdhZlNEJPZDs5482ty34J4sI2ZK2yC8YkZ/x+WCSveUgDQIVJ8oK\n" +
            "D4jtAPxqHnfSr9RAbvB1GQoiYLxhfxEp/+zfB9dBKDTRZR2nJm/mMsavY2DnSzLp\n" +
            "t7PJOjt3BdtISRtGMRsWmRHRfy882msBxsYug22odnT1OdaJQ54bWJT5iJnceBV2\n" +
            "1oOqWSg5hU1MupZRxxHbzI61EpTLlxXJQ7YNSwwiDzjaxGrufxc4eZnzGQ1A8h1u\n" +
            "jTaG84S1MWvG7BfcPLW+sya+PkrQWMOCIgXrQnAsUgqQrgxQ8Ocq3G4X9UvBy5VR\n" +
            "CwIDAQABo3sweTAJBgNVHRMEAjAAMCwGCWCGSAGG+EIBDQQfFh1PcGVuU1NMIEdl\n" +
            "bmVyYXRlZCBDZXJ0aWZpY2F0ZTAdBgNVHQ4EFgQUpG420UhvfwAFMr+8vf3pJunQ\n" +
            "gH4wHwYDVR0jBBgwFoAUkKZQt4TUuepf8gWEE3hF6Kl1VFwwDQYJKoZIhvcNAQEF\n" +
            "BQADggIBAFXr6G1g7yYVHg6uGfh1nK2jhpKBAOA+OtZQLNHYlBgoAuRRNWdE9/v4\n" +
            "J/3Jeid2DAyihm2j92qsQJXkyxBgdTLG+ncILlRElXvG7IrOh3tq/TttdzLcMjaR\n" +
            "8w/AkVDLNL0z35shNXih2F9JlbNRGqbVhC7qZl+V1BITfx6mGc4ayke7C9Hm57X0\n" +
            "ak/NerAC/QXNs/bF17b+zsUt2ja5NVS8dDSC4JAkM1dD64Y26leYbPybB+FgOxFu\n" +
            "wou9gFxzwbdGLCGboi0lNLjEysHJBi90KjPUETbzMmoilHNJXw7egIo8yS5eq8RH\n" +
            "i2lS0GsQjYFMvplNVMATDXUPm9MKpCbZ7IlJ5eekhWqvErddcHbzCuUBkDZ7wX/j\n" +
            "unk/3DyXdTsSGuZk3/fLEsc4/YTujpAjVXiA1LCooQJ7SmNOpUa66TPz9O7Ufkng\n" +
            "+CoTSACmnlHdP7U9WLr5TYnmL9eoHwtb0hwENe1oFC5zClJoSX/7DRexSJfB7YBf\n" +
            "vn6JA2xy4C6PqximyCPisErNp85GUcZfo33Np1aywFv9H+a83rSUcV6kpE/jAZio\n" +
            "5qLpgIOisArj1HTM6goDWzKhLiR/AeG3IJvgbpr9Gr7uZmfFyQzUjvkJ9cybZRd+\n" +
            "G8azmpBBotmKsbtbAU/I/LVk8saeXznshOVVpDRYtVnjZeAneso7\n" +
            "-----END CERTIFICATE-----\n" +
            "--START INTERMEDIATE CERT--\n" +
            "-----BEGIN CERTIFICATE-----\n" +
            "MIIFEjCCA/qgAwIBAgICEAAwDQYJKoZIhvcNAQELBQAwgawxCzAJBgNVBAYTAlVT\n" +
            "MQswCQYDVQQIDAJOWTESMBAGA1UEBwwJQ2FuYXN0b3RhMRswGQYDVQQKDBJRWiBJ\n" +
            "bmR1c3RyaWVzLCBMTEMxGzAZBgNVBAsMElFaIEluZHVzdHJpZXMsIExMQzEZMBcG\n" +
            "A1UEAwwQcXppbmR1c3RyaWVzLmNvbTEnMCUGCSqGSIb3DQEJARYYc3VwcG9ydEBx\n" +
            "emluZHVzdHJpZXMuY29tMB4XDTE1MDMwMjAwNTAxOFoXDTM1MDMwMjAwNTAxOFow\n" +
            "gZgxCzAJBgNVBAYTAlVTMQswCQYDVQQIDAJOWTEbMBkGA1UECgwSUVogSW5kdXN0\n" +
            "cmllcywgTExDMRswGQYDVQQLDBJRWiBJbmR1c3RyaWVzLCBMTEMxGTAXBgNVBAMM\n" +
            "EHF6aW5kdXN0cmllcy5jb20xJzAlBgkqhkiG9w0BCQEWGHN1cHBvcnRAcXppbmR1\n" +
            "c3RyaWVzLmNvbTCCAiIwDQYJKoZIhvcNAQEBBQADggIPADCCAgoCggIBANTDgNLU\n" +
            "iohl/rQoZ2bTMHVEk1mA020LYhgfWjO0+GsLlbg5SvWVFWkv4ZgffuVRXLHrwz1H\n" +
            "YpMyo+Zh8ksJF9ssJWCwQGO5ciM6dmoryyB0VZHGY1blewdMuxieXP7Kr6XD3GRM\n" +
            "GAhEwTxjUzI3ksuRunX4IcnRXKYkg5pjs4nLEhXtIZWDLiXPUsyUAEq1U1qdL1AH\n" +
            "EtdK/L3zLATnhPB6ZiM+HzNG4aAPynSA38fpeeZ4R0tINMpFThwNgGUsxYKsP9kh\n" +
            "0gxGl8YHL6ZzC7BC8FXIB/0Wteng0+XLAVto56Pyxt7BdxtNVuVNNXgkCi9tMqVX\n" +
            "xOk3oIvODDt0UoQUZ/umUuoMuOLekYUpZVk4utCqXXlB4mVfS5/zWB6nVxFX8Io1\n" +
            "9FOiDLTwZVtBmzmeikzb6o1QLp9F2TAvlf8+DIGDOo0DpPQUtOUyLPCh5hBaDGFE\n" +
            "ZhE56qPCBiQIc4T2klWX/80C5NZnd/tJNxjyUyk7bjdDzhzT10CGRAsqxAnsjvMD\n" +
            "2KcMf3oXN4PNgyfpbfq2ipxJ1u777Gpbzyf0xoKwH9FYigmqfRH2N2pEdiYawKrX\n" +
            "6pyXzGM4cvQ5X1Yxf2x/+xdTLdVaLnZgwrdqwFYmDejGAldXlYDl3jbBHVM1v+uY\n" +
            "5ItGTjk+3vLrxmvGy5XFVG+8fF/xaVfo5TW5AgMBAAGjUDBOMB0GA1UdDgQWBBSQ\n" +
            "plC3hNS56l/yBYQTeEXoqXVUXDAfBgNVHSMEGDAWgBQDRcZNwPqOqQvagw9BpW0S\n" +
            "BkOpXjAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBCwUAA4IBAQAJIO8SiNr9jpLQ\n" +
            "eUsFUmbueoxyI5L+P5eV92ceVOJ2tAlBA13vzF1NWlpSlrMmQcVUE/K4D01qtr0k\n" +
            "gDs6LUHvj2XXLpyEogitbBgipkQpwCTJVfC9bWYBwEotC7Y8mVjjEV7uXAT71GKT\n" +
            "x8XlB9maf+BTZGgyoulA5pTYJ++7s/xX9gzSWCa+eXGcjguBtYYXaAjjAqFGRAvu\n" +
            "pz1yrDWcA6H94HeErJKUXBakS0Jm/V33JDuVXY+aZ8EQi2kV82aZbNdXll/R6iGw\n" +
            "2ur4rDErnHsiphBgZB71C5FD4cdfSONTsYxmPmyUb5T+KLUouxZ9B0Wh28ucc1Lp\n" +
            "rbO7BnjW\n" +
            "-----END CERTIFICATE-----\n");
    }

    function signRequest(toSign, callback) {
        callback();
    }

    function qzReady() {
        if (!qz) {
            window["qz"] = document.getElementById('qz');
        }
        var title = document.getElementById("title");

        if (qz) {
            try {
                title.innerHTML = title.innerHTML + " " + qz.getVersion();
                document.getElementById("qz-status").style.background = "#F0F0F0";
            } catch(err) {
                document.getElementById("qz-status").style.background = "#F5A9A9";
                alert(
                    "ERROR:  \nThe applet did not load correctly.  Communication to the " +
                    "applet has failed, likely caused by Java Security Settings.  \n\n" +
                    "CAUSE:  \nJava 7 update 25 and higher block LiveConnect calls " +
                    "once Oracle has marked that version as outdated, which " +
                    "is likely the cause.  \n\nSOLUTION:  \n  1. Update Java to the latest " +
                    "Java version \n          (or)\n  2. Lower the security " +
                    "settings from the Java Control Panel."
                );
            }
        }
    }

    function qzSocketError(event) {
        document.getElementById("qz-status").style.background = "#F5A9A9";
        console.log('Error:');
        console.log(event);

        alert("Connection had an error:\n"+ event.reason);
    }

    function qzSocketClose(event) {
        document.getElementById("qz-status").style.background = "#A0A0A0";
        console.log('Close:');
        console.log(event);

        alert("Connection was closed:\n"+ event.reason);
    }

    function qzNoConnection() {
        alert("Unable to connect to QZ, is it running?");

        var content = '';
        var oldWrite = document.write;
        document.write = function(text) {
            content += text;
        };

        deployQZApplet();

        var newElem = document.createElement('ins');
        newElem.innerHTML = content;

        document.write = oldWrite;
        document.body.appendChild(newElem);
    }

    function notReady() {
        if (!isLoaded()) {
            return true;
        }
        else if (!qz.getPrinter()) {
            qz.findPrinter();
            return true;
        }
        return false;
    }

    function isLoaded() {
        if (!qz) {
            alert('Error:\n\n\tPrint plugin is NOT loaded!');
            return false;
        } else {
            try {
                if (!qz.isActive()) {
                    alert('Error:\n\n\tPrint plugin is loaded but NOT active!');
                    return false;
                }
            } catch (err) {
                alert('Error:\n\n\tPrint plugin is NOT loaded properly!');
                return false;
            }
        }
        return true;
    }

    function qzDonePrinting() {
        if (qz.getException()) {
            alert('Error printing:\n\n\t' + qz.getException().getLocalizedMessage());
            qz.clearException();
            return;
        }
    }

    function getPath() {
        var path = window.location.href;
        return path.substring(0, path.lastIndexOf("/")) + "/";
    }

    function fixHTML(html) {
        return html.replace(/\s/g, "&nbsp;").replace(/'/g, "'").replace(/-/g,"&#8209;");
    }

    function chr(i) {
        return String.fromCharCode(i);
    }

    function buyingTransactReceipt() {
        if (notReady()) {
            return;
        }

        var buying_transact_receipt =
            '<html>' +
                '<table cellspacing="0" style="font-size:9pt; font-face:\'Arial\'; margin: 0; padding: 0;">' +
                    '<tr style="margin: 0; padding: 0;">' +
                        '<td style="margin-left: 6pt; padding: 0;  width: 285pt;">' +
                            '<table cellspacing="0" style="font-size:9pt; font-face:\'Arial\'; padding: 0; margin: 0;">' +
                                '<tbody>'+
                                    // '<tr>' +
                                    //     '<td style="font-size:9pt; text-align: right; padding: 0;"></td>' +
                                    //     // '<td style="font-size:8pt; text-align: right; width: 177pt; padding: 0;">'+ b_trans_rset +'<td>' +
                                    //     '<td style="font-size:8pt; text-align: right; width: 177pt; padding: 0;"><td>' +
                                    // '</tr>' +
                                    // '<tr style="margin-bottom: 5pt;">';
                                    //     // '<td style="font-size:9pt; text-align: left; padding: 0; style="margin-bottom: 1px;""><b>Branch Copy</b></td>';
                                    //     if (b_trans_rset == 'O') {
                                    //         buying_transact_receipt +=
                                    //             '<td style="padding: 0; font-size:8pt; text-align: right; width: 195pt; style="margin-bottom: 1px;"">&nbsp;'+ b_trans_rset +'</td>';
                                    //     } else if (b_trans_rset == 'B') {
                                    //         buying_transact_receipt +=
                                    //             '<td style="padding: 0; font-size:8pt; text-align: right; width: 195pt; style="margin-bottom: 1px;""></td>';
                                    //     }
                                    // buying_transact_receipt +=
                                    // '</tr>' +
                                    // '<tr style="margin-top: 2pt;">' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Transaction No.</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_number +'</td>' +
                                        // '<td style="padding: 0; font-size:8pt; text-align: right;">&nbsp;:&nbsp;'+ b_trans_number +'</td>' + 
                                    '</tr>';
                                    if (b_trans_rset == 'O') {
                                        buying_transact_receipt +=
                                            '<tr>' +
                                                '<td style="text-align: left; padding: 0;">Invoice No.</td>' +
                                                '<td style="padding: 0;">&nbsp;:&nbsp;'+ String(b_trans_receipt_no).padStart(11, '0') +'</td>' +
                                            '</tr>';
                                    }

                                buying_transact_receipt +=
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">CN</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_customer_no +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Transaction Date</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_date +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Customer</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_customer +'</td>' +
                                    '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: left; padding: 0;">Transacted By</td>' +
                                    //     '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_transacted_by +'</td>' +
                                    // '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Currency</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_currency +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Currency Amount</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_curr_amount +'</td>' +
                                    '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: left; padding: 0;">Rate</td>' +
                                    //     '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_rate_used.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>' +
                                    // '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="margin-left: 15pt; text-align: right; 7adding: 0;">Rate Used</td>' +
                                    //     '<td style="padding: 0;">&nbsp;'+ b_trans_rate_used +'</td>' +
                                    // '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Peso Amount</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_total_amount +'</td>' +
                                    '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: right; padding: 0;"></td>' +
                                    //     '<td style="padding: 0;">&nbsp;</td>' +
                                    // '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="padding: 0; text-align: center;" colspan="2">_________________________________________</td>' +
                                    // '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: center; padding: 0;" colspan="2">' +
                                    //         '<strong>' +
                                    //             '<span>Signature over printed name</span>' +
                                    //         '</strong>' +
                                    //     '</td>' +
                                    // '</tr>' +
                                '</tbody>' +
                            '</table>' +
                        '</td>' +
                        '<td style="margin-left: 20pt; padding: 0; width: 290pt">' +
                            '<table cellspacing="0" style="font-size:9pt; font-face:\'Arial\'; padding-top: 0; padding-bottom: 0; padding-right: 0;">' +
                                '<tbody>'+
                                    // '<tr>' +
                                    //     '<td style="font-size:9pt; text-align: right; padding: 0;"></td>' +
                                    //     // '<td style="font-size:8pt; text-align: right; width: 177pt; padding: 0;">'+ b_trans_rset +'<td>' +
                                    //     '<td style="font-size:8pt; text-align: right; width: 177pt; padding: 0;"><td>' +
                                    // '</tr>' +
                                    // '<tr style="margin-bottom: 5pt;">';
                                    //     // '<td style="font-size:9pt; text-align: left; padding: 0; width: 108pt; style="margin-bottom: 1px;"><b>Customer &rsquo s Copy</b></td>';
                                    //     if (b_trans_rset == 'O') {
                                    //         buying_transact_receipt +=
                                    //             '<td style="padding: 0; font-size:8pt; text-align: right; width: 177pt; style="margin-bottom: 1px;">&nbsp;'+ b_trans_rset +'</td>';
                                    //     } else if (b_trans_rset == 'B') {
                                    //         buying_transact_receipt +=
                                    //             '<td style="padding: 0; font-size:8pt; text-align: right; width: 177pt; style="margin-bottom: 1px;">&nbsp;</td>';
                                    //     }
                                    // buying_transact_receipt +=
                                    //     '</tr>' +
                                        // '<tr style="margin-top: 2pt;">' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Transaction No.</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_number +'</td>' +
                                        // '<td style="padding: 0; font-size:8pt; text-align: right;">&nbsp;:&nbsp;'+ b_trans_number +'</td>' + 
                                    '</tr>';
                                    if (b_trans_rset == 'O') {
                                        buying_transact_receipt +=
                                            '<tr style="margin-top: 2pt;">' +
                                                '<td style="text-align: left; padding: 0;">Invoice No.</td>' +
                                                '<td style="padding: 0;">&nbsp;:&nbsp;'+ String(b_trans_receipt_no).padStart(11, '0') +'</td>' +
                                            '</tr>';
                                    }

                                buying_transact_receipt +=
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">CN</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_customer_no +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Transaction Date</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_date +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Customer</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_customer +'</td>' +
                                    '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: left; padding: 0;">Transacted By</td>' +
                                    //     '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_transacted_by +'</td>' +
                                    // '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Currency</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_currency +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Currency Amount</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_curr_amount +'</td>' +
                                    '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: left; padding: 0;">Rate</td>' +
                                    //     '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_rate_used.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>' +
                                    // '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="margin-left: 15pt; text-align: left; 7adding: 0;">Rate Used</td>' +
                                    //     '<td style="padding: 0;">&nbsp;'+ b_trans_rate_used +'</td>' +
                                    // '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Peso Amount</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp;'+ b_trans_total_amount +'</td>' +
                                    '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: right; padding: 0;"></td>' +
                                    //     '<td style="padding: 0;">&nbsp;</td>' +
                                    // '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: right; padding: 0;"></td>' +
                                    //     '<td style="padding: 0;">&nbsp;</td>' +
                                    // '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="padding: 0; text-align: center;" colspan="2">_________________________________________</td>' +
                                    // '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: center; padding: 0;" colspan="2">' +
                                    //         '<strong>' +
                                    //             '<span>Signature over printed name</span>' +
                                    //         '</strong>' +
                                    //     '</td>' +
                                    // '</tr>' +
                                '</tbody>' +
                            '</table>' +
                        '</td>' +
                    '</tr>' +
                '</table>';
            var new_var =
                '<table>'+
                    '<tr>' +
                        '<td style="padding: 0; width: 285pt;">' +
                            '<table cellspacing="0" style="font-size:9pt; font-face:\'Arial\'; padding: 0;">' +
                                '<tbody>'+
                                    '<tr>'+
                                        '<td style="padding: 0; text-align: center; width: 80pt; font-size:9pt;"><b>Bill Amount</b></td>'+
                                        '<td style="padding: 0; text-align: center; width: 50pt; font-size:9pt;"><b>Quantity</b></td>'+
                                        '<td style="padding: 0; text-align: center; width: 50pt; font-size:9pt;"><b>Total</b></td>'+
                                        '<td style="padding: 0; text-align: center; width: 50pt; font-size:9pt;"><b>Rate</b></td>'+
                                        '<td style="padding: 0; text-align: center; width: 90pt; font-size:9pt;"><b>Total Amount</b></td>'+
                                    '</tr>';

                                    var merged_bills = b_bill_amnts_array.map(function(bills_val, bills_index) {
                                        return {
                                            bill_amount: bills_val,
                                            bill_count: b_bill_count_array[bills_index],
                                            bill_sub_total: b_bill_total_array[bills_index],
                                            bill_rate: b_buying_rate_array[bills_index]
                                        };
                                    });

                                    merged_bills.forEach(function(gar) {
                                        var exchange_amnt = parseFloat(gar.bill_sub_total) * parseFloat(gar.bill_rate);
                                        var sub_total = parseFloat(gar.bill_sub_total);

                                        new_var +=
                                            '<tr>'+
                                                '<td style="padding: 0; text-align: right;">' + gar.bill_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 0}) + '&nbsp; &nbsp;&nbsp;</td>'+
                                                '<td style="padding: 0; text-align: center;">' + gar.bill_count + '</td>'+
                                                '<td style="padding: 0; text-align: right;">' + sub_total.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) + '&nbsp; &nbsp;&nbsp;</td>'+
                                                '<td style="padding: 0; text-align: right;">' + gar.bill_rate.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) + '&nbsp; &nbsp;&nbsp;</td>'+
                                                '<td style="padding: 0; text-align: right;">' + exchange_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) + '&nbsp; &nbsp;&nbsp;</td>'+
                                            '</tr>';
                                    });

                                    merged_bills.length = 0;
                            new_var +=
                                '</tbody>' +
                            '</table>' +
                        '</td>' +
                        '<td style="padding: 0; width: 285pt; margin-left: 15pt;">' +
                            '<table cellspacing="0" style="font-size:9pt; font-face:\'Arial\'; padding: 0;">' +
                                '<tbody>'+
                                    '<tr>'+
                                        '<td style="padding: 0; text-align: center; width: 80pt; font-size:9pt;"><b>Bill Amount</b></td>'+
                                        '<td style="padding: 0; text-align: center; width: 50pt; font-size:9pt;"><b>Quantity</b></td>'+
                                        '<td style="padding: 0; text-align: center; width: 50pt; font-size:9pt;"><b>Total</b></td>'+
                                        '<td style="padding: 0; text-align: center; width: 50pt; font-size:9pt;"><b>Rate</b></td>'+
                                        '<td style="padding: 0; text-align: center; width: 90pt; font-size:9pt;"><b>Total Amount</b></td>'+
                                    '</tr>';

                                    var merged_bills = b_bill_amnts_array.map(function(bills_val, bills_index) {
                                        return {
                                            bill_amount: bills_val,
                                            bill_count: b_bill_count_array[bills_index],
                                            bill_sub_total: b_bill_total_array[bills_index],
                                            bill_rate: b_buying_rate_array[bills_index]
                                        };
                                    });

                                    merged_bills.forEach(function(gar) {
                                        var exchange_amnt = parseFloat(gar.bill_sub_total) * parseFloat(gar.bill_rate);
                                        var sub_total = parseFloat(gar.bill_sub_total);

                                        new_var +=
                                            '<tr>'+
                                                '<td style="padding: 0; text-align: right;">' + gar.bill_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 0}) + '&nbsp; &nbsp;&nbsp;</td>'+
                                                '<td style="padding: 0; text-align: center;">' + gar.bill_count + '</td>'+
                                                '<td style="padding: 0; text-align: right;">' + sub_total.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) + '&nbsp; &nbsp;&nbsp;</td>'+
                                                '<td style="padding: 0; text-align: right;">' + gar.bill_rate.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) + '&nbsp; &nbsp;&nbsp;</td>'+
                                                '<td style="padding: 0; text-align: right;">' + exchange_amnt.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) + '&nbsp; &nbsp;&nbsp;</td>'+
                                            '</tr>';
                                    });

                                    merged_bills.length = 0;
                            new_var +=
                                '</tbody>' +
                            '</table>' +
                        '</td>' +
                    '</tr>' +
                '</table>'+
                '<table>'+
                    '<tr>' +
                        '<td style="margin-left: 20pt; padding: 0; width: 275pt;">' +
                            '<table cellspacing="0" style="font-size:9pt; font-face:\'Arial\'; padding: 0;">' +
                                '<tbody>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: right; padding: 0;"></td>' +
                                    //     '<td style="padding: 0;">&nbsp;</td>' +
                                    // '</tr>' +
                                    '<tr>' +
                                        '<td style="padding: 0; text-align: left; font-size:8pt;" colspan="2">I hereby confirm that I have received and acknowledged the full amount exchanged in <u>PHILIPPINE PESO</u>.</td>' +
                                        // '<td style="padding: 0; text-align: left; font-size:8pt;" colspan="2">I hereby confirm that I have received and acknowledged the full amount exchanged in <u>'+ b_trans_currency +'</u>.</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: right; padding: 0;"></td>' +
                                        '<td style="padding: 0;">&nbsp;</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="padding: 0; text-align: center; border-bottom: 1px solid #000; font-size: 8pt;" colspan="2">'+ b_trans_customer +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: center; padding: 0; font-size:9pt;" colspan="2">' +
                                            // '<span>Signature over printed name</span>' +
                                            '<span>Signature</span>' +
                                        '</td>' +
                                        // '<td style="padding-left: 30pt; padding-bottom: 13pt; text-align: right; font-size:3.5pt;">'+ b_trans_print_count +'</td>' +
                                    '</tr>' +
                                '</tbody>' +
                            '</table>' +
                        '</td>' +
                        '<td style="margin-left: 35pt; padding: 0; width: 285pt;">' +
                            '<table cellspacing="0" style="font-size:9pt; font-face:\'Arial\'; padding: 0;">' +
                                '<tbody>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: right; padding: 0;"></td>' +
                                    //     '<td style="padding: 0;">&nbsp;</td>' +
                                    // '</tr>' +
                                    '<tr>' +
                                        '<td style="padding: 0; text-align: left; font-size:8pt;" colspan="2">I hereby confirm that I have received and acknowledged the full amount exchanged in <u>PHILIPPINE PESO</u>.</td>' +
                                        // '<td style="padding: 0; text-align: left; font-size:8pt;" colspan="2">I hereby confirm that I have received and acknowledged the full amount exchanged in <u>'+ b_trans_currency +'</u>.</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: right; padding: 0;"></td>' +
                                        '<td style="padding: 0;">&nbsp;</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        // '<td style="padding: 0; text-align: center; border-bottom: 1px solid #000; font-size: 8pt;" colspan="2">'+ b_trans_customer +'</td>' +
                                        '<td style="padding: 0; text-align: center; border-bottom: 1px solid #000; font-size: 8pt;" colspan="2">'+ b_trans_customer +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: center; padding: 0; font-size:9pt;" colspan="2">' +
                                            // '<span>Signature over printed name</span>' +
                                            '<span>Signature</span>' +
                                        '</td>' +
                                        // '<td style="padding-left: 30pt; padding-bottom: 13pt; text-align: right; font-size:3.5pt;">'+ b_trans_print_count +'</td>' +
                                    '</tr>' +
                                '</tbody>' +
                            '</table>' +
                        '</td>' +
                    '</tr>' +
                    '<tr>' +
                    '</tr>' +
                '</table>'+
            '</html>';

        qz.appendHTML(buying_transact_receipt);
        qz.appendHTML(new_var);

        qz.printHTML();
    }

    function tx() {
        alert ("test");
    }

    function findPrinter(name) {
        qz.findPrinter();
    }

    // function qzDoneFinding() {
    //     if (qz.getPrinter()) {
    //         printT();
    //     } else {
    //         alert("Walay printor.");
    //     }
    // }
</script>
