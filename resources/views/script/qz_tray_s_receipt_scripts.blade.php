<script type="text/javascript" src="{{ asset('plugins/QZTray/demo/js/qz-websocket.js ') }}"></script>

<script>
    // Forex - Selling Transaction Receipt Printing
    var receipt_watermark = $('#receipt-water-mark-pdf').val();
    var selling_receipt_printing = $('#selling-receipt-sec-code').attr('data-sellingreceiptseccode');

    // For Selling transaction receipt
    var s_trans_id = $('#serials-scid').val();
    var s_trans_date = $('#sold-currency-date-sold').val();
    var s_trans_number = $('#sold-currency-selling-number').val();
    var s_trans_receipt_no = $('#sold-currency-receipt-number').val();
    var s_trans_curr_amount = $('#sold-currency-curr-amnt').val();
    var s_trans_rate_used = $('#sold-currency-rate-used').val();
    var s_trans_rset = $('#sold-currency-rset').val();
    var s_trans_total_amount = $('#sold-currency-total-amnt').val();
    var s_trans_or_number = $('#sold-currency-or-number').val();
    var s_trans_currency = $('#sold-currency-currency').val();
    var s_trans_customer = $('#sold-currency-customer').val();
    var s_trans_customer_no = $('#transact-customer-id').val();
    var s_trans_by = $('#sold-currency-transacted-by').val();

    var serials_table = $('#sold-serials-table');
    var serials = serials_table.find('.serials-sold');
    var currency = serials_table.find('.serials-sold-currency');
    var rset = serials_table.find('.serials-sold-rset-input');
    var bill_amnt = serials_table.find('.serials-sold-bill-amnt');
    var s_trans_print_count = $('#selling-print-count').val();

    var sold_serials_array = [];
    var sold_serials_bill_amnt_array = [];
    var s_serials_curr_array = [];
    var s_serials_rset_array = [];
    var s_serials_bill_amnt_array = [];
    var s_serials_scid_array = [];
    var s_serials_date_array = [];
    var s_serials_time_array = [];

    $(document).ready(function() {
        var forex_sold_serials_base_url = $('#full-url-serials-sold').val();
        var forex_scid = $('#forex-scid').val();
        var forex_sold_serials_full_url = forex_sold_serials_base_url + '/' + forex_scid;

        if (window.location.href == forex_sold_serials_full_url) {
            if ($('#selling-print-count').val() <= 0) {
                $("#printing-receipt-selling").click();
            }
        }
    });

    $('#printing-receipt-selling').click(function() {
        serials.each(function() {
            var sold_serials = $(this).closest('tr').find('.form-control#serials-sold-input');
            var sold_serials_bill_amnt = $(this).closest('tr').find('.form-control#serials-sold-bill-amnt-input');
            var sold_serials_value = sold_serials.val();
            var sold_serials_bill_amnt_val = sold_serials_bill_amnt.val();

            sold_serials_array.push(sold_serials_value);
            sold_serials_bill_amnt_array.push(sold_serials_bill_amnt_val);
        });

        currency.each(function() {
            var sold_serials_curr_input = $(this).closest('tr').find('.form-control#serials-sold-currency-input');
            var sold_serials_currency = sold_serials_curr_input.val();

            s_serials_curr_array.push(sold_serials_currency);
        });

        rset.each(function() {
            var sold_serials_rset_input = $(this).closest('tr').find('.form-control#serials-sold-rset-input');
            var sold_serials_rset = sold_serials_rset_input.val();

            var sold_serials_scid_input = $(this).closest('tr').find('.form-control#serials-sold-scid-input');
            var sold_serials_scid = sold_serials_scid_input.val();

            var sold_serials_date_input = $(this).closest('tr').find('.form-control#serials-sold-date-input');
            var sold_serials_date = sold_serials_date_input.val();

            var sold_serials_time_input = $(this).closest('tr').find('.form-control#serials-sold-time-input');
            var sold_serials_time = sold_serials_time_input.val();

            s_serials_rset_array.push(sold_serials_rset);
            s_serials_scid_array.push(sold_serials_scid);
            s_serials_date_array.push(sold_serials_date);
            s_serials_time_array.push(sold_serials_time);

        });

        bill_amnt.each(function() {
            var sold_serials_bill_input = $(this).closest('tr').find('.form-control#serials-sold-bill-amnt-input');
            var sold_serials_bill = sold_serials_bill_input.val();

            s_serials_bill_amnt_array.push(sold_serials_bill);
        });

        var length_arrays = sold_serials_array.length;

        $("#selling-trans-date").text(s_trans_date);
        $("#selling-number").text(s_trans_number);
        $("#selling-receipt-number").text(s_trans_receipt_no);
        $("#selling-rset").text(s_trans_rset);
        $("#selling-or-number").text(s_trans_or_number != '' ? s_trans_or_number : '0');
        $("#selling-customer").text(s_trans_customer);
        $("#selling-currency").text(s_trans_currency);
        $("#sell-transact-currency-amount").text(s_trans_curr_amount);
        $("#sell-transact-amount").text(s_trans_total_amount);
        $("#selling-rate-used").text(s_trans_rate_used.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));

        $('#selling-transact-modal').modal("show");
        $('#print-selling-receipt-modal').modal("hide");
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

                    sellingTransactReceipt();

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
                            url: "{{ route('branch_transactions.selling_transaction.print') }}",
                            type: "post",
                            data: {
                                _token: "{{ csrf_token() }}",
                                s_trans_id: s_trans_id
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
                                        s_trans_print_count = data.print_s_count_latest;
                                        sellingTransactReceipt();

                                        window.location.reload();
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

    function sellingTransactReceipt() {
        if (notReady()) {
            return;
        }

        var selling_trans_html =
            '<html>' +
                '<table cellspacing="0" style="font-size:10pt; font-face:\'Arial\'; padding: 0;">' +
                    '<tr>' +
                        '<td style="margin-left: 10pt; padding: 0; width: 285pt;">' +
                            '<table cellspacing="0" style="font-size:10pt; font-face:\'Arial\'; padding: 0;">' +
                                '<tbody>' +
                                    // '<tr>' +
                                    //     '<td style="font-size:10pt; text-align: left; padding: 0;"><b>Branch Copy</b></td>';
                                    //     if (s_trans_rset == 'O') {
                                    //         selling_trans_html +=
                                    //             '<td style="padding: 0; font-size:8pt; text-align: right; width: 185pt;">&nbsp;'+ s_trans_rset +'</td>';
                                    //     } else if (s_trans_rset == 'B') {
                                    //         selling_trans_html +=
                                    //             '<td style="padding: 0; font-size:8pt; text-align: right; width: 185pt;"></td>';
                                    //     }
                                    //     selling_trans_html +=
                                    //         '</tr>'+
                                            '<tr>' +
                                                    '<td style="text-align: left; padding: 0;">Transaction No.</td>' +
                                                    '<td style="padding: 0;">&nbsp;:&nbsp; '+ s_trans_number +'</td>' +
                                            '</tr>';

                                    //     if (s_trans_rset == 'O') {
                                    //         selling_trans_html +=
                                    //             '<tr>' +
                                    //                 '<td style="text-align: left; padding: 0;">Invoice No. </td>' +
                                    //                 '<td style="padding: 0;">&nbsp;:&nbsp; '+ String(s_trans_or_number).padStart(11, '0') +'</td>' +
                                    //             '</tr>';
                                    //     }

                                    selling_trans_html +=
                                    // '<tr>' +
                                    //     '<td style="font-size:10pt; text-align: left; padding: 0;"><b>Branch Copy</b></td>' +
                                    //     '<td style="padding: 0; font-size:8pt; text-align: left; width: 195pt;">&nbsp;'+ s_trans_rset +'</td>' +
                                    // '</tr>'+
                                    // '<tr>' +
                                    //     '<td style="text-align: left; padding: 0; width: 100pt;">Receipt No. :</td>' +
                                    //     '<td style="padding: 0;">&nbsp; '+ s_trans_receipt_no +'</td>' +
                                    // '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: left; padding: 0;">OR Number :</td>' +
                                    //     '<td style="padding: 0;">&nbsp; '+ s_trans_or_number +'</td>' +
                                    // '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">CN</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp; '+ s_trans_customer_no +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Transact Date</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp; '+ s_trans_date +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Customer</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp; '+ s_trans_customer +'</td>' +
                                    '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: left; padding: 0;">Transacted By</td>' +
                                    //     '<td style="padding: 0;">&nbsp;:&nbsp; '+ s_trans_by +'</td>' +
                                    // '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Currency</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp; '+ s_trans_currency +'</td>' +
                                    '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: left; padding: 0;">Total Amount</td>' +
                                    //     '<td style="padding: 0;">&nbsp; '+ s_trans_currency +'</td>' +
                                    // '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Currency Amount</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp; '+ s_trans_curr_amount +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Rate</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp; '+ s_trans_rate_used.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Peso Amount</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp; '+ s_trans_total_amount +'</td>' +
                                    '</tr>' +
                                '</tbody>' +
                            '</table>' +
                        '</td>' +
                        '<td style="margin-left: 22pt; padding: 0; width: 285pt;">' +
                            '<table cellspacing="0" style="font-size:10pt; font-face:\'Arial\'; padding: 0;">' +
                                '<tbody>'+
                                    // '<tr>' +
                                    //     '<td style="font-size:10pt; text-align: left; padding: 0; width: 108pt;"><b>Customer &rsquo s Copy</b></td>';
                                    //     if (s_trans_rset == 'O') {
                                    //         selling_trans_html +=
                                    //             '<td style="padding: 0; font-size:8pt; text-align: right; width: 185pt">&nbsp;'+ s_trans_rset +'</td>';
                                    //     } else if (s_trans_rset == 'B') {
                                    //         selling_trans_html +=
                                    //             '<td style="padding: 0; font-size:8pt; text-align: right; width: 185pt">&nbsp;</td>';
                                    //     }
                                    // selling_trans_html +=
                                    //     '</tr>';

                                    // if (s_trans_rset == 'O') {
                                    //     selling_trans_html +=
                                            '<tr>' +
                                                '<td style="text-align: left; padding: 0;">Invoice No.</td>' +
                                                '<td style="padding: 0;">&nbsp;:&nbsp; '+ String(s_trans_or_number).padStart(11, '0') +'</td>' +
                                            '</tr>';
                                    // }

                                    selling_trans_html +=
                                    // '<tr>' +
                                    //     '<td style="font-size:10pt; text-align: left; padding: 0;"><b>Customer &rsquo s Copy</b></td>' +
                                    //     '<td style="padding: 0; font-size:8pt; text-align: right; width: 195pt;">&nbsp;</td>' +
                                    // '</tr>'+
                                    // '<tr>' +
                                    //     '<td style="text-align: right; padding: 0;">OR Number</td>' +
                                    //     '<td style="padding: 0;">&nbsp; '+ s_trans_or_number +'</td>' +
                                    // '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">CN</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp; '+ s_trans_customer_no +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Transact Date</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp; '+ s_trans_date +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Customer</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp; '+ s_trans_customer +'</td>' +
                                    '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: left; padding: 0;">Transacted By</td>' +
                                    //     '<td style="padding: 0;">&nbsp;:&nbsp; '+ s_trans_by +'</td>' +
                                    // '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Currency</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp; '+ s_trans_currency +'</td>' +
                                    '</tr>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: left; padding: 0;">Total Amount</td>' +
                                    //     '<td style="padding: 0;">&nbsp; '+ s_trans_currency +'</td>' +
                                    // '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Currency Amount</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp; '+ s_trans_curr_amount +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Rate</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp; '+ s_trans_rate_used.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}) +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: left; padding: 0;">Peso Amount</td>' +
                                        '<td style="padding: 0;">&nbsp;:&nbsp; '+ s_trans_total_amount +'</td>' +
                                    '</tr>' +
                                '</tbody>' +
                            '</table>' +
                        '</td>' +
                    '</tr>' +
                    '<tr>'+
                    '</tr>' +
                '</table>';
            var new_var =
                '<table>'+
                    '<tr>'+
                        '<td style="padding: 0; width: width: 285pt;">'+
                            '<table cellspacing="0" style="font-size:10pt; font-face:\'Arial\'; padding: 0;">' +
                                '<tbody>'+
                                    // '<tr >'+
                                    //     '<td style="padding: 0;  font-size:10pt; text-align: right; width: 100pt;"><b>Bill Amount &nbsp; &nbsp;</b></td>'+
                                    //     '<td style="padding: 0; font-size:10pt; width: 100pt;">&nbsp; &nbsp;<b>Serial</b></td>'+
                                    // '</tr>';

                                    // var merged_serial_bill_amnt = sold_serials_bill_amnt_array.map(function(serials_val, serials_indx) {
                                    //     return {
                                    //         serials: serials_val,
                                    //         bill_amount: sold_serials_array[serials_indx],
                                    //     };
                                    // });

                                    // merged_serial_bill_amnt.forEach(function(gar) {
                                    //     new_var +=
                                    //         '<tr>'+
                                    //             '<td style="padding: 0;  font-size:10pt; text-align: right">' + gar.serials + '&nbsp; &nbsp; &nbsp; </td>'+
                                    //             '<td style="padding: 0; font-size:10pt; ">&nbsp; &nbsp;' + gar.bill_amount + '</td>'
                                    //         '</tr>';
                                    // });

                                    // new_var +=
                                    // '</tr>'+
                                '</tbody>'+
                            '</table>'+
                        '</td>'+
                        '<td style="padding: 0; width: width: 285pt;">'+
                            '<table cellspacing="0" style="font-size:10pt; font-face:\'Arial\'; padding: 0;">' +
                                '<tbody>'+
                                    // '<tr >'+
                                    //     '<td style="padding: 0; font-size:10pt; text-align: right; width: 100pt;"><b>Bill Amount &nbsp; &nbsp;</b></td>'+
                                    //     '<td style="padding: 0; font-size:10pt; width: 100pt;">&nbsp; &nbsp;<b>Serial</b></td>'+
                                    // '</tr>';

                                    // var merged_serial_bill_amnt = sold_serials_bill_amnt_array.map(function(serials_val, serials_indx) {
                                    //     return {
                                    //         serials: serials_val,
                                    //         bill_amount: sold_serials_array[serials_indx],
                                    //     };
                                    // });

                                    // merged_serial_bill_amnt.forEach(function(gar) {
                                    //     new_var +=
                                    //         '<tr >'+
                                    //             '<td style="padding: 0; font-size:10pt; text-align: right">' + gar.serials + '&nbsp; &nbsp; &nbsp; </td>'+
                                    //             '<td style="padding: 0; font-size:10pt;">&nbsp; &nbsp;' + gar.bill_amount + '</td>'
                                    //         '</tr>';
                                    // });

                                    // new_var +=
                                    // '</tr>'+
                                '</tbody>'+
                            '</table>'+
                        '</td>'+
                    '</tr>'+
                '</table>' +
                '<table>'+
                    '<tr>' +
                        '<td style="margin-left: 20pt; padding: 0; width: 285pt;">' +
                            '<table cellspacing="0" style="font-size:10pt; font-face:\'Arial\'; padding: 0;">' +
                                '<tbody>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: right; padding: 0;"></td>' +
                                    //     '<td style="padding: 0;">&nbsp;</td>' +
                                    // '</tr>' +
                                    '<tr>' +
                                        '<td style="padding: 0; text-align: left; font-size:8pt;" colspan="2">I hereby confirm that I have received and acknowledged the full amount exchanged in <u>'+ s_trans_currency +'</u>.</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: right; padding: 0;"></td>' +
                                        '<td style="padding: 0;">&nbsp;</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="padding: 0; text-align: center; border-bottom: 1px solid #000; font-size: 8pt;" colspan="2">'+ s_trans_customer +'</td>' +
                                        // '<td style="padding: 0; text-align: center; border-bottom: 1px solid #000; font-size: 8pt;" colspan="2">'+ s_trans_customer +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: center; padding: 0; font-size:10pt;" colspan="2">' +
                                            // '<span>Signature over printed name</span>' +
                                            '<span>Signature</span>' +
                                        '</td>' +
                                        // '<td style="padding-left: 30pt; padding-bottom: 13pt; text-align: right; font-size:3.5pt;">'+ s_trans_print_count +'</td>' +
                                    '</tr>' +
                                '</tbody>' +
                            '</table>' +
                        '</td>' +
                        '<td style="margin-left: 35pt; padding: 0; width: 285pt;">' +
                            '<table cellspacing="0" style="font-size:10pt; font-face:\'Arial\'; padding: 0;">' +
                                '<tbody>' +
                                    // '<tr>' +
                                    //     '<td style="text-align: right; padding: 0;"></td>' +
                                    //     '<td style="padding: 0;">&nbsp;</td>' +
                                    // '</tr>' +
                                    '<tr>' +
                                        '<td style="padding: 0; text-align: left; font-size:8pt;" colspan="2">I hereby confirm that I have received and acknowledged the full amount exchanged in <u>'+ s_trans_currency +'</u>.</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: right; padding: 0;"></td>' +
                                        '<td style="padding: 0;">&nbsp;</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="padding: 0; text-align: center; border-bottom: 1px solid #000; font-size: 8pt;" colspan="2">'+ s_trans_customer +'</td>' +
                                        // '<td style="padding: 0; text-align: center; border-bottom: 1px solid #000; font-size: 8pt;" colspan="2">'+ s_trans_customer +'</td>' +
                                    '</tr>' +
                                    '<tr>' +
                                        '<td style="text-align: center; padding: 0; font-size:10pt;" colspan="2">' +
                                            // '<span>Signature over printed name</span>' +
                                            '<span>Signature</span>' +
                                        '</td>' +
                                        // '<td style="padding-left: 30pt; padding-bottom: 13pt; text-align: right; font-size:3.5pt;">'+ s_trans_print_count +'</td>' +
                                    '</tr>' +
                                '</tbody>' +
                            '</table>' +
                        '</td>' +
                    '</tr>' +
                '</table>' +
            '</html>';

        // Append the dynamically generated HTML to qz
        qz.appendHTML(selling_trans_html);
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
