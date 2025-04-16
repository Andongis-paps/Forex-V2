
<script>
    $(document).ready(function() {
        const socketserver = "{{ config('app.socket_io_server') }}";
        // const socketserver = 'http://192.168.88.25:3434';
        const socket = io(socketserver);
        const base_url = "{{ url('/') }}";

        socket.on('message', function(msg) {
            var route = "{{ route('notifications') }}";
            var url = route;

            $.ajax({
                type: "GET",
                url: url,
                success: function(res) {
                    // var see_all = '';
                    var notification = '';

                    if (res.success) {
                        var data = res.data;
                        var new_notif_badge = res.count > 0 ? `<span class="badge bg-label-blue !font-extrabold p-1">${res.count}&nbsp;NEW</span>` : "";
                        var bell_badge = res.count > 0 ? `<span class="badge rounded-pill badge-danger badge-notifications danger-badge-custom notification-badge bx-tada rounded-full"> </span>` : "";

                        data.forEach(e => {
                            notification +=
                            `<a class="notif-button" data-fxdid="${e.FXDID}" href="${base_url}/${e.URLName}">
                                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar flex-shrink-0">
                                                <span class="avatar-initial rounded bg-label-secondary"> ${e.Acknowledged == 0 ? `<i class='bx bx-envelope'></i>` : `<i class='bx bx-envelope-open'></i>`}</span>
                                            </div>
                                        </div>
                                        <div class="w-100">
                                            <h6 class="mb-1"><strong>${e.AppMenuName}</strong></h6>
                                            <small class="text-muted">${e.Notification}</small><br> 
                                            <small class="text-muted">${e.Date}</small>
                                        </div>
                                        <div class="pt-2">
                                            ${e.Acknowledged == 0 ? `<i class='bx bxs-circle dot-badge !text-[#DC3545]'></i>` : ``}
                                        </div>
                                    </div>
                                </li>
                            </a>`;
                        });

                        if (bell_badge) {
                            $('#notif-count').empty().append(new_notif_badge);
                            $('.notification-badge').empty();
                            $('#notification-count').append(bell_badge);
                            $('#bell-icon').addClass("bx-tada");
                        };

                        // see_all +=
                        // `<div class="col-12 text-center">
                        //     <div class="row px-3">
                        //         <a class="btn btn-primary btn-sm" type="button" href="{{ route('notif.show') }}">
                        //             See All
                        //         </a>
                        //     </div>
                        // </div>`;
                    } else {
                        notification +=
                        `<li class="list-group-item dropdown-notifications-item">
                            <div class="d-flex">
                                <div class="row align-items-center my-2 px-2">
                                    <div class="col-3 pe-0 text-center">
                                        <img class="bx-spin" src="{{ asset('images/tumbleweed.png') }}" alt="forex-web" width="22">
                                    </div>
                                    <div class="col-9 ps-0 text-start">
                                        <span class="font-semibold mb-0">Nothing to see here.</span>
                                    </div>
                                    <small class="text-muted"></small>
                                </div>
                            </div>
                        </li>`;
                    }

                    // $('#notification-footer').empty().append(see_all);
                    $('#notification-body').empty().append(notification);

                    ackNotif();
                },
            });

            function ackNotif() {
                $('.notif-button').click(function() {
                    $.ajax({
                        url: "{{ route('notif.acknowledge') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            FXDID: $(this).attr('data-fxdid')
                        },
                        success: function(res) {
                            console.log("test");
                        }
                    });
                });
            }
        });

        socket.on('branchPrompt', function(data) {
            var userBranchID = {{ Auth::user()->getBranch()->BranchID }};

            if(data.branchid == userBranchID) {
                Swal.fire({
                    title: '',
                    html: `<small>${data.msg}</small>`,
                    icon: 'info',
                    showConfirmButton: true,
                    allowOutsideClick: false,  // Prevents closing by clicking outside
                    allowEscapeKey: false,
                    html:
                    `<div class="mb-2 mt-1" id="count-down-container">
                        <small class="text-black" id="count-down-label">${data.msg} &nbsp;<strong class="text-red-500"  id="count-down">3</strong></small>
                    </div>`,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Reload',
                    didOpen: () => {
                        const confirm_button = Swal.getConfirmButton();
                        confirm_button.disabled = true;

                        let countdown = 3;

                        const count_down_display = Swal.getHtmlContainer().querySelector('#count-down');
                        const count_down_label = Swal.getHtmlContainer().querySelector('#count-down-label');
                        const count_down_container = Swal.getHtmlContainer().querySelector('#count-down-container');

                        const count_down_tick = setInterval(() => {
                            countdown -= 1;
                            count_down_display.textContent = countdown;

                            if (countdown === 0) {
                                confirm_button.disabled = false;
                                // count_down_label.style.display = 'none';
                                count_down_display.style.display = 'none';
                                // count_down_container.style.display = 'none';

                                clearInterval(count_down_tick);
                            }
                        }, 1000);
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            }
        });

        socket.on('revertBuffer', function(data) {
            let bills = '';
            let height = '';
            let serials = '';
            var userBranchID = {{ Auth::user()->getBranch()->BranchID }};

            if(data.branchid == userBranchID) {
                let rows = '';
                let total_reverted_amnt = 0;

                const serials_array = data.serials.split(',');
                const bills_array = data.bill_amnt.split(',');

                $.each(bills_array, function(index, value) {
                    total_reverted_amnt += parseFloat(value); // Add each value to sum
                });

                serials_array.forEach((bill_serial, index) => {
                    const bill_amount = parseFloat(bills_array[index]).toLocaleString("en", {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    rows += `
                        <tr>
                            <td class="p-2 text-center text-xs border-t-gray-300">${bill_serial}</td>
                            <td class="p-2 text-center text-xs border-t-gray-300">${bill_amount}</td>
                        </tr>`;
                });

                height = data.serials.split(',').length > 9? 'height: 300px!important;' : 'height: auto; ';

                Swal.fire({
                    title: '',
                    html: `<small>${data.msg}</small>`,
                    icon: 'info',
                    showConfirmButton: true,
                    allowOutsideClick: false,  // Prevents closing by clicking outside
                    allowEscapeKey: false,
                    html:
                       `<div class="col-12">
                            <span class="text-sm text-black">
                                ${data.msg} &nbsp;
                            </span?
                        </div>
                        <div class="col-12 mt-2 border border-gray-300 p-0" style="${height} overflow: hidden; overflow-y: scroll;">
                            <table class="table table-hover mb-0">
                                <thead style="position: sticky; top: 0; background: #fff; z-index: 3;">
                                    <tr>
                                        <th class="border-gray-300 py-1 text-black font-bold text-center" colspan="2">Reverted Bills</th>
                                    </tr>
                                    <tr>
                                        <th class="border-gray-300 py-1 text-black font-bold text-center">Serial</th>
                                        <th class="border-gray-300 py-1 text-black font-bold text-center">Bill Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rows}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="border-gray-300 py-1 text-black font-bold text-center"></td>
                                        <td class="border-gray-300 py-1 text-black font-bold text-center text-sm">${total_reverted_amnt.toLocaleString("en", {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="mt-2" id="count-down-container">
                            <small class="text-black text-sm" id="count-down-label">
                                You are required to reload the page.
                                <strong class="text-red-500 text-sm" id="count-down">3</strong>
                            </small>
                        </div>`,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Reload',
                    didOpen: () => {
                        const confirm_button = Swal.getConfirmButton();
                        confirm_button.disabled = true;

                        let countdown = 3;

                        const count_down_display = Swal.getHtmlContainer().querySelector('#count-down');
                        const count_down_label = Swal.getHtmlContainer().querySelector('#count-down-label');
                        const count_down_container = Swal.getHtmlContainer().querySelector('#count-down-container');

                        const count_down_tick = setInterval(() => {
                            countdown -= 1;
                            count_down_display.textContent = countdown;

                            if (countdown === 0) {
                                confirm_button.disabled = false;
                                // count_down_label.style.display = 'none';
                                count_down_display.style.display = 'none';
                                // count_down_container.style.display = 'none';

                                clearInterval(count_down_tick);
                            }
                        }, 1000);
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            }
        });

        socket.on('transferCapital', function(data) {
            var userBranchID = {{ Auth::user()->getBranch()->BranchID }};

            if(data.branchid == userBranchID) {
                Swal.fire({
                    title: '',
                    html: `<small>${data.msg}</small>`,
                    icon: 'info',
                    showConfirmButton: true,
                    allowOutsideClick: false,  // Prevents closing by clicking outside
                    allowEscapeKey: false,
                    html: `<div class="mb-2 mt-1" id="count-down-container">
                            <small class="text-black" id="count-down-label">${data.msg} &nbsp;<strong class="text-red-500" id="count-down">3</strong></small>
                        </div>`,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Go to Dashboard',
                    didOpen: () => {
                        const confirm_button = Swal.getConfirmButton();
                        confirm_button.disabled = true;

                        let countdown = 3;

                        const count_down_display = Swal.getHtmlContainer().querySelector('#count-down');
                        const count_down_label = Swal.getHtmlContainer().querySelector('#count-down-label');
                        const count_down_container = Swal.getHtmlContainer().querySelector('#count-down-container');

                        const count_down_tick = setInterval(() => {
                            countdown -= 1;
                            count_down_display.textContent = countdown;

                            if (countdown === 0) {
                                confirm_button.disabled = false;
                                // count_down_label.style.display = 'none';
                                count_down_display.style.display = 'none';
                                // count_down_container.style.display = 'none';

                                clearInterval(count_down_tick);
                            }
                        }, 1000);
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        var url = "{{ route('branch_transactions.dashboard') }}";

                        window.location.href = url;
                    }
                });
            }
        });

        socket.on('ATDEPS', function(data) {
            let bills = '';
            let height = '';
            let serials = '';
            var userBranchID = {{ Auth::user()->getBranch()->BranchID }};

            console.log(data);

            if(data.branchid == userBranchID) {
                let rows = '';
                const atd_amnt = parseFloat(data.atd_amount).toLocaleString("en", {minimumFractionDigits: 2,maximumFractionDigits: 2 });
                const currency = data.curr;
                const discrepancy_no = data.disc_no;

                rows += `
                    <tr>
                        <td class="p-2 text-center text-sm border-t-gray-300">${currency}</td>
                        <td class="p-2 text-center text-sm border-t-gray-300 font-bold">${atd_amnt}</td>
                        <td class="p-2 text-center text-sm border-t-gray-300 font-bold">${discrepancy_no}</td>
                    </tr>`;

                Swal.fire({
                    title: '',
                    html: `<small>${data.msg}</small>`,
                    icon: 'info',
                    showConfirmButton: true,
                    allowOutsideClick: false,  // Prevents closing by clicking outside
                    allowEscapeKey: false,
                    html:
                       `<div class="col-12">
                            <span class="text-sm text-black">
                                ${data.msg} &nbsp;
                            </span?
                        </div>
                        <div class="col-12 mt-2 border border-gray-300 p-0" style="height: auto; overflow: hidden; overflow-y: scroll;">
                            <table class="table table-hover mb-0">
                                <thead style="position: sticky; top: 0; background: #fff; z-index: 3;">
                                    <tr>
                                        <th class="border-gray-300 py-1 text-black font-bold text-center" colspan="3">Details</th>
                                    </tr>
                                    <tr>
                                        <th class="border-gray-300 py-1 text-black font-bold text-center">Currency</th>
                                        <th class="border-gray-300 py-1 text-black font-bold text-center">ATD Amount</th>
                                        <th class="border-gray-300 py-1 text-black font-bold text-center">Discrepancy No.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rows}
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-2" id="count-down-container">
                            <small class="text-black text-sm" id="count-down-label">
                                You are required to reload the page.
                                <strong class="text-red-500 text-sm" id="count-down">3</strong>
                            </small>
                        </div>`,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Reload',
                    didOpen: () => {
                        const confirm_button = Swal.getConfirmButton();
                        confirm_button.disabled = true;

                        let countdown = 3;

                        const count_down_display = Swal.getHtmlContainer().querySelector('#count-down');
                        const count_down_label = Swal.getHtmlContainer().querySelector('#count-down-label');
                        const count_down_container = Swal.getHtmlContainer().querySelector('#count-down-container');

                        const count_down_tick = setInterval(() => {
                            countdown -= 1;
                            count_down_display.textContent = countdown;

                            if (countdown === 0) {
                                confirm_button.disabled = false;
                                // count_down_label.style.display = 'none';
                                count_down_display.style.display = 'none';
                                // count_down_container.style.display = 'none';

                                clearInterval(count_down_tick);
                            }
                        }, 1000);
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            }
        });

        socket.on('stopBuyingChanges', function(data) {
            let bills = '';
            let height = '';
            let serials = '';
            var userBranchID = {{ Auth::user()->getBranch()->BranchID }};

            var branch_ids_array = data.branchids.split(', ');

            // if(data.branch_ids == userBranchID) {
            if (data.branchids.includes(userBranchID)) {
                let rows = '';
                const denom_array = data.denom.split(', ');

                denom_array.forEach((denomination, index) => {
                    rows += `
                        <tr>
                            <td class="p-2 text-center text-xs border-t-gray-300">${denomination}</td>
                        </tr>`;
                });

                height = denom_array.length > 9? 'height: 300px!important;' : 'height: auto; ';

                Swal.fire({
                    title: '',
                    html: `<small>${data.msg}</small>`,
                    icon: 'info',
                    showConfirmButton: true,
                    allowOutsideClick: false,  // Prevents closing by clicking outside
                    allowEscapeKey: false,
                    html:
                       `<div class="col-12">
                            <span class="text-sm text-black">
                                ${data.msg} &nbsp;
                            </span?
                        </div>

                        <div class="col-12 mt-2 border border-gray-300 p-0" style="${height} overflow: hidden; overflow-y: scroll;">
                            <table class="table table-hover mb-0">
                                <thead style="position: sticky; top: 0; background: #fff; z-index: 3;">
                                    <tr>
                                        <th class="border-gray-300 py-1 text-black font-bold text-center" colspan="2">Denominations for stop buying</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rows}
                                </tbody>
                            </table>
                        </div>

                        <div class="col-12 mt-3">
                            <table class="table table-bordered table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="border-gray-300 py-1 text-black font-bold text-center" colspan="2">Currency For Stop Buying</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <td class="p-2 text-center text-xs border-t-gray-300">${data.currency}</td>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-2" id="count-down-container">
                            <small class="text-black text-sm" id="count-down-label">
                                You are required to reload the page.
                                <strong class="text-red-500 text-sm" id="count-down">3</strong>
                            </small>
                        </div>`,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Reload',
                    didOpen: () => {
                        const confirm_button = Swal.getConfirmButton();
                        confirm_button.disabled = true;

                        let countdown = 3;

                        const count_down_display = Swal.getHtmlContainer().querySelector('#count-down');
                        const count_down_label = Swal.getHtmlContainer().querySelector('#count-down-label');
                        const count_down_container = Swal.getHtmlContainer().querySelector('#count-down-container');

                        const count_down_tick = setInterval(() => {
                            countdown -= 1;
                            count_down_display.textContent = countdown;

                            if (countdown === 0) {
                                confirm_button.disabled = false;
                                // count_down_label.style.display = 'none';
                                count_down_display.style.display = 'none';
                                // count_down_container.style.display=  'none';

                                clearInterval(count_down_tick);
                            }
                        }, 1000);
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            }
        });

        socket.on('updateRate', function(data) {
            let bills = '';
            let height = '';
            let serials = '';
            var userBranchID = {{ Auth::user()->getBranch()->BranchID }};

            if (userBranchID != 10) {
                $.ajax({
                    url: "{{ route('rate_updates') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        userBranchID: userBranchID,
                        currency_id: data.currency_id,
                        old_rate: data.old_rate,
                        new_rate: data.new_rate,
                    },
                    success: function(gar) {
                        var buying_rates = gar.buying_rates;
                        var selling_rates = gar.selling_rates;

                        var notif_element = 
                            `<div class="col-12">
                                <span class="text-sm text-black">
                                    ${data.msg} &nbsp;
                                </span?
                            </div>

                            <div class="col-12 mt-2">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th class="border-gray-300 py-1 text-white font-bold text-center !bg-[#00A65A]" colspan="2">Currency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="p-1 text-center text-sm border-t-gray-300" colspan="2"><strong>${data.currency}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>`;

                        notif_element +=`
                            <div class="col-12 mt-2">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th class="border-gray-300 py-1 text-black font-bold text-center" colspan="3">Buying Rates</th>
                                        </tr>
                                        <tr>
                                            <th class="border-gray-300 py-1 text-black font-bold text-center">Denomination</th>
                                            <th class="border-gray-300 py-1 text-black font-bold text-center">Previous Rate</th>
                                            <th class="border-gray-300 py-1 text-white font-bold text-center !bg-[#0D6EFD]">Updated Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                                    buying_rates.forEach(function(poy) {
                                        notif_element += `
                                            <tr>
                                                <td class="p-1 text-center text-sm border-t-gray-300">${poy.BillAmount}</td>
                                                <td class="p-1 text-center text-sm border-t-gray-300">${parseFloat(poy.previous_rate).toLocaleString("en", {minimumFractionDigits: 2,maximumFractionDigits: 2 })}</td>
                                                <td class="p-1 text-center text-sm border-t-gray-300"><strong>${parseFloat(poy.updated_rate).toLocaleString("en", {minimumFractionDigits: 2,maximumFractionDigits: 2 })}</strong></td>
                                            </tr>`;
                                    });

                            notif_element +=`
                                    </tbody>
                                </table>
                            </div>`;

                        notif_element +=`
                            <div class="col-12 mt-2">
                                <table class="table table-bordered table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th class="border-gray-300 py-1 text-black font-bold text-center" colspan="3">Selling Rates</th>
                                        </tr>
                                        <tr>
                                            <th class="border-gray-300 py-1 text-black font-bold text-center">Denomination</th>
                                            <th class="border-gray-300 py-1 text-black font-bold text-center">Previous Rate</th>
                                            <th class="border-gray-300 py-1 text-white font-bold text-center !bg-[#0D6EFD]">Updated Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>`;

                                    selling_rates.forEach(function(poy) {
                                        notif_element += `
                                            <tr>
                                                <td class="p-1 text-center text-sm border-t-gray-300">${poy.BillAmount}</td>
                                                <td class="p-1 text-center text-sm border-t-gray-300">${parseFloat(poy.previous_rate).toLocaleString("en", {minimumFractionDigits: 2,maximumFractionDigits: 2 })}</td>
                                                <td class="p-1 text-center text-sm border-t-gray-300"><strong>${parseFloat(poy.updated_rate).toLocaleString("en", {minimumFractionDigits: 2,maximumFractionDigits: 2 })}</strong></td>
                                            </tr>`;
                                    });

                            notif_element +=`
                                    </tbody>
                                </table>
                            </div>`;

                        notif_element +=`
                            <div class="mt-2" id="count-down-container">
                                <small class="text-black text-sm" id="count-down-label">
                                    Please reload the page. <strong class="text-red-500 text-sm" id="count-down">3</strong>
                                </small>
                            </div>`;

                        Swal.fire({
                            title: '',
                            html: `<small>${data.msg}</small>`,
                            icon: 'info',
                            showConfirmButton: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            html: notif_element,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Proceed',
                            didOpen: () => {
                                const confirm_button = Swal.getConfirmButton();
                                confirm_button.disabled = true;

                                let countdown = 3;

                                const count_down_display = Swal.getHtmlContainer().querySelector('#count-down');
                                const count_down_label = Swal.getHtmlContainer().querySelector('#count-down-label');
                                const count_down_container = Swal.getHtmlContainer().querySelector('#count-down-container');

                                const count_down_tick = setInterval(() => {
                                    countdown -= 1;
                                    count_down_display.textContent = countdown;

                                    if (countdown === 0) {
                                        confirm_button.disabled = false;
                                        // count_down_label.style.display = 'none';
                                        count_down_display.style.display = 'none';
                                        // count_down_container.style.display=  'none';

                                        clearInterval(count_down_tick);
                                    }
                                }, 1000);
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    }
                });
            }
        });

        function getAllNotifications() {
            socket.emit('message', 'sync');
        }

        function empty() {
            $('#notification-count .notification-badge').remove();
            $('#notification-body a, #notification-body li').remove();
        }

        getAllNotifications();
    });
</script>
