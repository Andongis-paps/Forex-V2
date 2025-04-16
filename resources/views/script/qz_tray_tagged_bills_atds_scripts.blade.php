<script type="text/javascript" src="{{ asset('plugins/QZTray/demo/js/qz-websocket.js ') }}"></script>

<script>
    var var_test = '';

    $('#tagged-bill-date').change(function() {
        if ($(this).val() != '') {
            $('#print-atd-security-code').removeAttr('disabled', 'disabled');
        } else {
            $('#print-atd-security-code').attr('disabled', 'disabled');
        }
    });

    $('#print-atd-security-code').keyup(function() {
        if ($(this).val() != '') {
            $('#print-atd-confirm').removeAttr('disabled', 'disabled');
        } else {
            $('#print-atd-confirm').attr('disabled', 'disabled');
        }
    });

    $('#print-atd-confirm').click(function() {
        var date_range = $('#tagged-bill-date').val();
        var dates = date_range.split(" TO ");

        var user_id_array = [];
        var sec_code_array = [];
        var _token = $('input[name="_token"]').val();
        var on_page_final_printing = $('#print-atd-security-code').val();

        $.ajax({
            url: "{{ route('user_info') }}",
            type: "GET",
            data: {
                _token: _token,
            },
            // beforeSend: function() {
            //     adminSellingTransDetails();
            // },
            success: function(get_user_info) {
                var user_info = get_user_info.security_codes;

                user_info.forEach(function(gar) {
                    sec_code_array.push(gar.SecurityCode);
                    user_id_array.push(gar.UserID);
                });

                if (sec_code_array.includes(on_page_final_printing)) {
                    $('#proceed-transaction').prop('disabled', true);

                    $.ajax({
                        url: "{{ route('admin_transactions.bill_tagging.print') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            date_from: dates[0],
                            date_to: dates[1],
                        },
                        success: function(gar) {
                            var_test = gar.html;
                            $('#tagged-bills-atds').html(var_test);
                        },
                    });

                    var index = sec_code_array.indexOf(on_page_final_printing);
                    var matched_user_id = user_id_array[index];

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
                                taggedBillWithATDs();
                            }, 200);
                        },
                        willClose: () => {
                            clearInterval(timerInterval);
                        }
                    }).then((result) => {
                        taggedBillWithATDs();

                        Swal.fire({
                            icon: 'success',
                            title: 'Receipt printed!',
                            text: 'Transfer forex successfully printed.',
                            customClass: {
                                popup: 'my-swal-popup',
                            }
                        }).then(() => {
                            setTimeout(function() {
                                $('#final-print-receipt-modal').modal('hide');
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

    function taggedBillWithATDs() {
        if (notReady()) {
            return;
        }

        // Append the dynamically generated HTML to qz
        qz.appendHTML(var_test);
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
