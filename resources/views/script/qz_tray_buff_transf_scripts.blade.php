<script type="text/javascript" src="{{ asset('plugins/QZTray/demo/js/qz-websocket.js ') }}"></script>

<script>
    // Buffer Transfer Forex - Buffer Transfer Details Printing
    var buffer_transfer_forex_number = $('#buffer-transfer-transfer-number').val();
    var buffer_transfer_forex_date = $('#buffer-transfer-transfer-date').val();
    var buffer_transfer_forex_branch = $('#buffer-transfer-branch').val();
    // Buffer Transfer Summary table
    var buffer_transfer_summary_table = $('#bufffer-transfer-summary-table');
    var buffer_summ_currency = [];
    var buffer_summ_total_amnt = [];
    // Buffer Transfer Breakdown table
    var buffer_transfer_break_d_table = $('#buffer-transfer-breakd-down-table');
    var buffer_break_d_currency = [];
    var buffer_break_d_bill_amnt = [];
    var buffer_break_d_count = [];
    var buffer_break_d_total_amnt = [];

    $('#print-buffer-transfer').click(function() {
        buffer_transfer_summary_table.find('tbody tr').each(function() {
            var currency = $(this).find('.transfer-summ-currency').val();
            var amount = $(this).find('.transfer-summ-total-amount').val();

            buffer_summ_currency.push(currency);
            buffer_summ_total_amnt.push(amount);
        });

        buffer_transfer_break_d_table.find('tbody tr').each(function() {
            var currency = $(this).find('.transfer-break-d-currency').val();
            var bill_amnt = $(this).find('.transfer-break-d-bill-amnt').val();
            var bill_count = $(this).find('.transfer-break-d-bill-count').val();
            var total_amnt = $(this).find('.transfer-break-d-total-amount').val();

            buffer_break_d_currency.push(currency);
            buffer_break_d_bill_amnt.push(bill_amnt);
            buffer_break_d_count.push(bill_count);
            buffer_break_d_total_amnt.push(total_amnt);
        });

        $('#security-code-modal').modal("show");
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

                console.log(on_page_final_printing);

                if (sec_code_array.includes(on_page_final_printing)) {
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

                            setTimeout(function() {
                                bufferTransferForexDetails();
                            }, 200);
                        },
                        willClose: () => {
                            clearInterval(timerInterval);
                        }
                    }).then((result) => {
                        bufferTransferForexDetails();

                        Swal.fire({
                            icon: 'success',
                            title: 'Receipt printed!',
                            text: 'Transfer forex successfully printed.',
                            customClass: {
                                popup: 'my-swal-popup',
                            }
                        }).then(() => {
                            setTimeout(function() {
                                window.location.reload();
                            }, 200);
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

    function bufferTransferForexDetails() {
        if (notReady()) {
            return;
        }

        var buffer_transfer_forex_detail =
        '<html>' +
            '<table cellspacing="0" style="font-size:10pt; font-face:\'Arial\'; padding: 0;">' +
                '<tr>' +
                    '<td style="padding-left: 3pt; padding-right: 15pt; padding-top: 0; padding-bottom: 0; width: 285pt;">' +
                        '<table cellspacing="0" style="font-size:10pt; font-face:\'Arial\'; padding: 0;" border=1 width="100%">' +
                            '<tbody>' +
                                '<tr>' +
                                    '<td style="text-align: left; style="font-size:12pt; padding: 0;" colspan="4"><b>SINAG PAWNSHOP</b></td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="text-align: left; padding-bottom: 3pt; style="font-size:12pt;" colspan="4"><b>TRANSFER FOREX</b></td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="text-align: left; padding: 0; font-size:8pt;"><b>Transfer Forex No.</b></td>' +
                                    '<td style="padding: 0; font-size:8pt;">&nbsp; '+ buffer_transfer_forex_number +'</td>' +
                                    '<td style="text-align: left; padding: 0; font-size:8pt;"><b>Transfer Date</b></td>' +
                                    '<td style="padding: 0; font-size:8pt;">&nbsp; '+ buffer_transfer_forex_date +'</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="text-align: left; padding: 0; font-size:8pt;"><b>Remarks</b></td>' +
                                    '<td style="padding: 0; font-size:8pt;">&nbsp;BUFFER</td>' +
                                    '<td style="text-align: left; padding: 0; font-size:8pt;"><b>Branch</b></td>' +
                                    '<td style="padding: 0; font-size:8pt;">&nbsp; '+ buffer_transfer_forex_branch +'</td>' +
                                '</tr>' +
                            '</tbody>' +
                        '</table>' +
                    '</td>' +
                    '<td style="padding-left: 15pt; padding-top: 0; padding-bottom: 0; width: 285pt;">' +
                        '<table cellspacing="0" style="font-size:10pt; font-face:\'Arial\'; padding: 0;" border=1 width="100%">' +
                            '<tbody>' +
                                '<tr>' +
                                    '<td style="text-align: left; style="font-size:12pt; padding: 0;" colspan="4"><b>SINAG PAWNSHOP</b></td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="text-align: left; padding-bottom: 3pt; style="font-size:12pt;" colspan="4"><b>TRANSFER FOREX</b></td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="text-align: left; padding: 0; font-size:8pt;"><b>Transfer Forex No.</b></td>' +
                                    '<td style="padding: 0; font-size:8pt;">&nbsp; '+ buffer_transfer_forex_number +'</td>' +
                                    '<td style="text-align: left; padding: 0; font-size:8pt;"><b>Transfer Date</b></td>' +
                                    '<td style="padding: 0; font-size:8pt;">&nbsp; '+ buffer_transfer_forex_date +'</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="text-align: left; padding: 0; font-size:8pt;"><b>Remarks</b></td>' +
                                    '<td style="padding: 0; font-size:8pt;">&nbsp;BUFFER</td>' +
                                    '<td style="text-align: left; padding: 0; font-size:8pt;"><b>Branch</b></td>' +
                                    '<td style="padding: 0; font-size:8pt;">&nbsp; '+ buffer_transfer_forex_branch +'</td>' +
                                '</tr>' +
                            '</tbody>' +
                        '</table>' +
                    '</td>' +
                '</tr>' +
                '<tr style="margin-top: 5pt;">'+
                    '<td style="padding-left: 3pt; padding-right: 15pt; padding-top: 0; padding-bottom: 0; width: 285pt;">'+
                        '<table cellspacing="0" style="font-size:10pt; font-face:\'Arial\'; padding: 0;" border=1 width="100%">' +
                            '<tbody>'+
                                '<tr >'+
                                    '<td style="text-align: left; padding-bottom: 3pt; style="font-size:12pt;" colspan="6"><b>TRANSFER SUMMARY</b></td>' +
                                '</tr>'+
                                '<tr >'+
                                    '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;" colspan="3"><b>Currency</b></td>'+
                                    '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;" colspan="3"><b>Total Amount</b></td>'+
                                '</tr>';

                                var merged_b_transfer_summary = buffer_summ_currency.map(function(b_summ_currency_val, b_summ_currency_indx) {
                                    return {
                                        buffer_summ_currency: b_summ_currency_val,
                                        buffer_summ_total_amnt: buffer_summ_total_amnt[b_summ_currency_indx],
                                    };
                                });

                                merged_b_transfer_summary.forEach(function(gar) {
                                    buffer_transfer_forex_detail +=
                                        '<tr >'+
                                            '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;" colspan="3">' + gar.buffer_summ_currency + '&nbsp;&nbsp;</td>'+
                                            '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: right;" colspan="3">' + gar.buffer_summ_total_amnt + '&nbsp;&nbsp;</td>'+
                                        '</tr>';
                                });

                                buffer_transfer_forex_detail +=
                            '</tbody>'+
                        '</table>'+
                    '</td>'+
                    '<td style="padding-left: 15pt; padding-top: 0; padding-bottom: 0; width: 285pt;">'+
                        '<table cellspacing="0" style="font-size:10pt; font-face:\'Arial\'; padding: 0;" border=1 width="100%">' +
                            '<tbody>'+
                                '<tr >'+
                                    '<td style="text-align: left; padding-bottom: 3pt; style="font-size:12pt;" colspan="6"><b>TRANSFER SUMMARY</b></td>' +
                                '</tr>'+
                                '<tr >'+
                                    '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;" colspan="3"><b>Currency</b></td>'+
                                    '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;" colspan="3"><b>Total Amount</b></td>'+
                                '</tr>';

                                var merged_b_transfer_summary = buffer_summ_currency.map(function(b_summ_currency_val, b_summ_currency_indx) {
                                    return {
                                        buffer_summ_currency: b_summ_currency_val,
                                        buffer_summ_total_amnt: buffer_summ_total_amnt[b_summ_currency_indx],
                                    };
                                });

                                merged_b_transfer_summary.forEach(function(gar) {
                                    buffer_transfer_forex_detail +=
                                        '<tr >'+
                                            '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;" colspan="3">' + gar.buffer_summ_currency + '&nbsp;&nbsp;</td>'+
                                            '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: right;" colspan="3">' + gar.buffer_summ_total_amnt + '&nbsp;&nbsp;</td>'+
                                        '</tr>';
                                });

                                buffer_transfer_forex_detail +=
                            '</tbody>'+
                        '</table>'+
                    '</td>'+
                '</tr>'+
                '<tr style="margin-top: 5pt;">'+
                    '<td style="padding-left: 3pt; padding-right: 15pt; padding-top: 0; padding-bottom: 0; width: 285pt;">'+
                        '<table cellspacing="0" style="font-size:10pt; font-face:\'Arial\'; padding-top: 0;" border=1 width="100%">' +
                            '<tbody>'+
                                '<tr>'+
                                    '<td style="text-align: left; padding-bottom: 3pt; style="font-size:12pt;" colspan="5"><b>TRANSFER BREAKDOWN</b></td>' +
                                '</tr>'+
                                '<tr>'+
                                    '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;"><b>Currency</b></td>'+
                                    '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;"><b>Bill Amount</b></td>'+
                                    '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;"><b>Count</b></td>'+
                                    '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;" colspan="2"><b>Total Amount</b></td>'+
                                '</tr>';

                                var merged_b_transfer_breakdown = buffer_break_d_currency.map(function(b_break_d_currency_val, b_break_d_currency_indx) {
                                    return {
                                        buffer_break_d_currency: b_break_d_currency_val,
                                        buffer_break_d_bill_amnt: buffer_break_d_bill_amnt[b_break_d_currency_indx],
                                        buffer_break_d_count: buffer_break_d_count[b_break_d_currency_indx],
                                        buffer_break_d_total_amnt: buffer_break_d_total_amnt[b_break_d_currency_indx],
                                    };
                                });

                                merged_b_transfer_breakdown.forEach(function(test) {
                                    buffer_transfer_forex_detail +=
                                        '<tr>'+
                                            '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;">' + test.buffer_break_d_currency + '&nbsp;&nbsp;</td>'+
                                            '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;">' + test.buffer_break_d_bill_amnt + '&nbsp;&nbsp;</td>'+
                                            '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;">' + test.buffer_break_d_count + '&nbsp;&nbsp;</td>'+
                                            '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: right;" colspan="2">' + test.buffer_break_d_total_amnt + '&nbsp;&nbsp;</td>'+
                                        '</tr>';
                                });

                                buffer_transfer_forex_detail +=
                            '</tbody>'+
                        '</table>'+
                    '</td>'+
                    '<td style="padding-left: 15pt; padding-top: 0; padding-bottom: 0; width: 285pt;">'+
                        '<table cellspacing="0" style="font-size:10pt; font-face:\'Arial\'; padding-top: 0;" border=1 width="100%">' +
                            '<tbody>'+
                                '<tr >'+
                                    '<td style="text-align: left; padding-bottom: 3pt; style="font-size:12pt;" colspan="5"><b>TRANSFER BREAKDOWN</b></td>' +
                                '</tr>'+
                                '<tr>'+
                                    '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;"><b>Currency</b></td>'+
                                    '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;"><b>Bill Amount</b></td>'+
                                    '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;"><b>Count</b></td>'+
                                    '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;" colspan="2"><b>Total Amount</b></td>'+
                                '</tr>';

                                var merged_b_transfer_breakdown = buffer_break_d_currency.map(function(b_break_d_currency_val, b_break_d_currency_indx) {
                                    return {
                                        buffer_break_d_currency: b_break_d_currency_val,
                                        buffer_break_d_bill_amnt: buffer_break_d_bill_amnt[b_break_d_currency_indx],
                                        buffer_break_d_count: buffer_break_d_count[b_break_d_currency_indx],
                                        buffer_break_d_total_amnt: buffer_break_d_total_amnt[b_break_d_currency_indx],
                                    };
                                });

                                merged_b_transfer_breakdown.forEach(function(test) {
                                    buffer_transfer_forex_detail +=
                                        '<tr>'+
                                            '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;">' + test.buffer_break_d_currency + '&nbsp;&nbsp;</td>'+
                                            '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;">' + test.buffer_break_d_bill_amnt + '&nbsp;&nbsp;</td>'+
                                            '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: center;">' + test.buffer_break_d_count + '&nbsp;&nbsp;</td>'+
                                            '<td style="padding-bottom: 0; padding-top: 0;  padding-right: 0;  padding-left: 0; text-align: right;" colspan="2">' + test.buffer_break_d_total_amnt + '&nbsp;&nbsp;</td>'+
                                        '</tr>';
                                });

                                buffer_transfer_forex_detail +=
                            '</tbody>'+
                        '</table>'+
                    '</td>'+
                '</tr>'+
                '<tr>'+
                    '<td style="padding-left: 3pt; padding-right: 15pt; padding-top: 0; padding-bottom: 0; width: 285pt;">'+
                        '<table cellspacing="0" style="font-size:10pt; font-face:\'Arial\'; padding-top: 0;" width="100%">' +
                            '<tbody>'+
                                '<tr>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="padding: 0; text-align: center;" colspan="2">_________________________________________</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="text-align: center; padding: 0;" colspan="2">' +
                                        '<strong>' +
                                            '<span>Authorized Signature</span>' +
                                        '</strong>' +
                                    '</td>' +
                                '</tr>' +
                            '</tbody>'+
                        '</table>'+
                    '</td>'+
                    '<td style="padding-left: 15pt; padding-top: 0; padding-bottom: 0; width: 285pt;">'+
                        '<table cellspacing="0" style="font-size:10pt; font-face:\'Arial\'; padding-top: 0;" width="100%">' +
                            '<tbody>'+
                                '<tr>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="padding: 0; text-align: center;" colspan="2">_________________________________________</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="text-align: center; padding: 0;" colspan="2">' +
                                        '<strong>' +
                                            '<span>Authorized Signature</span>' +
                                        '</strong>' +
                                    '</td>' +
                                '</tr>' +
                            '</tbody>'+
                        '</table>'+
                    '</td>'+
                '</tr>'+
            '</table>' +
        '</html>';

        // Append the dynamically generated HTML to qz
        qz.appendHTML(buffer_transfer_forex_detail);

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
