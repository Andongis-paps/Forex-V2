{{-- Start of script/s for the project (forex web app v2) --}}

{{-- Add Bill with Serials --}}
<script>
    $(document).ready(function() {
        $('#serial-bill-amount').on('change', function() {
            $(this).find('option').removeAttr('selected');
            var get_bill_amount = $(this).find('option:selected').attr('selected', 'selected').val();
            var get_serial_ftdid = $(this).find('option:selected').attr('selected', 'selected').attr('data-serialftdid');
            var get_serial_fsid = $(this).find('option:selected').attr('selected', 'selected').attr('data-serialfsid');

            $('#serial-total-amount').val(get_bill_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2})).text(get_bill_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            $('#true-serial-total-amount').val(get_bill_amount.toLocaleString("en" , {minimumFractionDigits: 2 , maximumFractionDigits: 2}));
            $('#serial-ftdid').val(get_serial_ftdid);
            $('#serial-fsid').val(get_serial_fsid);
        });
    });
</script>

{{-- End of Selling Transcation module script --}}

{{-- Selling Transaction Module scripts - Admin --}}

{{-- Redirect - After successful buying transaction/edit buying transaction --}}
<script>
    $(document).ready(function() {
        function refreshPage(delay) {
            setTimeout(function() {
                var forex_serials_full_url = $('#full-url-addnewselling-admin').val();

                window.location.href = forex_serials_full_url;
            }, delay);
        }

        var consildation_success = $('#selling-admin-success-message').attr('data-successexistence');

        if (consildation_success == 1) {
            Swal.fire({
                title: 'Success!',
                text: "Redirecting...",
                icon: 'success',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                timer: 1000,
            }).then(() => {
                $('#container-test').fadeIn("slow");
                $('#container-test').css('display', 'block');
                refreshPage(600);
            });
        } else {
            return;
        }
    });
</script>

<script>
    $(document).on('input', 'input[type="text"], input[type="search"], textarea', function() {
        var $this = $(this);
        var cursorPosition = this.selectionStart;

        $this.val($this.val().toUpperCase());

        this.setSelectionRange(cursorPosition, cursorPosition);
    });
</script>

<script>
    //--------------------------- toggle rset function. ------------------------------//
    $(document).ready(function() {
        $('#toggle-rset').on('click', function() {
            $('input[name="rsetradio"]').each(function() {
                if ($(this).is(':checked')) {
                    // Uncheck the currently checked radio and check the other one
                    $(this).prop('checked', false);
                    $('input[name="rsetradio"]').not(this).prop('checked', true).trigger('change');
                    return false; // Exit the loop
                }
            });
        });

        $('input[name="rsetradio"]').on('change',  function() {
            updateRsetToggle(this.value);
        });

        function updateRsetToggle(val) {
            $.ajax({
                url: "{{ URL::to('/updateTimeToggleSession') }}",
                type: "POST",
                beforeSend: function() {
                    $('#container-test').fadeIn("fast");
                },
                data: {
                    _token: '{{ csrf_token() }}',
                    time_toggle_session: val
                },
                success: function(res) {
                    // setTimeout(function() {
                        window.location.reload();
                    // }, 700);
                    // console.log('Rset Changed!');
                }
            });
        }
    });
    //--------------------------- End toggle rset function. --------------------------//
</script>

{{-- End of Selling Transaction Module scripts - Admin --}}

{{-- ======================================================================================================================================================================================== --}}

{{-- Window based - Expense Mainte Scripts --}}

{{-- AJAX request for add new expense - Window Based - Expense --}}
<script>
    $(document).ready(function(){
        $('#button-add-expense').click(function(){
            $('#expense-maint-add-modal').modal('show');
        });
    });
</script>

{{-- AJAX for expense searching - Window Based - Expense --}}
<script>
    $(document).ready(function() {
        $('#search-expense').keyup(function(){
            var search_word = $(this).val();

            if(search_word != '') {
                var _token = $('input[name="_token"]').val();

                $.ajax({
                    url: "{{ URL::to('/searchExpense') }}",
                    method: "POST",
                    data: {
                        search_word: search_word,
                        _token:_token
                    },
                    success: function(data) {
                        var expense_details = data;
                        var array_index = expense_details[0];
                        var e_details_exp_name = array_index.ExpenseName;

                        $("tr").each(function() {
                            var expense_desc = $(this).attr('data-expensendesc');

                            if (expense_desc === e_details_exp_name) {
                                $(this).addClass("search-highlight");
                                $(this)[0].scrollIntoView({ behavior: "smooth", block: "center" });
                            } else {
                                $(this).removeClass("search-highlight");
                            }
                        });
                    }
                });
            }
        });

        $('#search-currency').keyup(function() {
            var search_word = $(this).val();

            if(search_word == '') {
                $("tr").each(function() {
                    $(this).removeClass("search-highlight").scrollTop();
                });
            }
        });
    });
</script>

{{-- AJAX request for edit currency details - Window Based - Currency --}}
<script>
    $(document).ready(function(){
        $('.button-edit-expense').click(function(){
            var ExpenseID = $(this).attr('data-expensemaintid');

            $.ajax({
                url: "{{ URL::to('/editExpense') }}",
                method: "POST",
                data: {
                    ExpenseID: ExpenseID,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    $('.update-expense-details').html(data);
                    // $('#branch-maint-edit-modal').modal('show');
                }
            });
        });
    });
</script>

{{-- End of Window based - Expense Mainte Scripts --}}

{{-- ======================================================================================================================================================================================== --}}

{{-- For forgot password email sending --}}
{{-- <script>
    $(document).ready(function(){
        $('#send-email-test-btn').click(function(){
            $.ajax({
                url: "{{ route('send_email') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert(response.message);
                },
                error: function(xhr, status, error) {
                    alert('Error sending email');
                }
            });
        });
    });
</script> --}}

{{-- ======================================================================================================================================================================================== --}}

{{-- Customer selection --}}
{{-- <script>
    $(document).ready(function() {
        var swiper = new Swiper("#test-swiper", {
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            pagination: {
                el: ".swiper-pagination",
                type: "fraction",
            },
            centeredSlides: true,
            keyboard: true,
            grabCursor: true,
            zoom: true,
        });

        function clear() {
            // $('#cms-redirect-button').hide();
            $('#customer_images').empty().fadeOut("fast");
            $('#sanctions-container').empty().fadeOut("fast");
            // $('.customer-modal-footer #cms-redirect-button').remove();
        }

        $('#customerDeetsModal').on('show.bs.modal', function() {
            clear();
        });

        var current_date = new Date();
        var year = current_date.getFullYear();
        var month = String(current_date.getMonth() + 1).padStart(2, '0');
        var day = String(current_date.getDate()).padStart(2, '0');

        var formatted_date = year + '-' + month + '-' + day;

        // UI/UX - Buying Transaction date input field
        $(document).ready(function(){
            $('#customer-birth-date').datepicker({
                yearRange: (new Date().getFullYear() - 100) + ':' + new Date().getFullYear(),
                maxDate: 0,
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                changeYear: true,
            });
        });

        $('input[name="customer-search-filter"]').change(function() {
            var filter_type = $(this).val();

            $('#go-to-cms-button').remove();
            $('#customer-search-l-name').val('');
            $('#customer-search-f-name').val('');
            $('#customer-search-m-name').val('');
            $('#customer-search-number').val('');
            $('#search-customer-table').find('tbody').empty().fadeOut("fast");

            if (filter_type == 1) {
                $('#c-searching-label').fadeIn(100);
                $('#c-searching-l-name').fadeIn(100);
                $('#c-searching-f-name').fadeIn(100);
                $('#c-searching-m-name').fadeIn(100);
                $('#c-searching-m-name').fadeIn(100);
                $('#c-searching-birth-date').fadeIn(100);
                $('#c-searching-customer-number').fadeOut(100);
            } else if (filter_type == 2) {
                $('#c-searching-label').fadeOut(100);
                $('#c-searching-l-name').fadeOut(100);
                $('#c-searching-f-name').fadeOut(100);
                $('#c-searching-m-name').fadeOut(100);
                $('#c-searching-birth-date').fadeOut(100);
                $('#c-searching-customer-number').removeClass('d-none').fadeIn(100);
            }

            var search_banner =
                `<tr>
                    <td class="text-center text-sm" colspan="10">
                        <strong>
                            <span class="buying-no-transactions text-lg font-bold">
                                SEARCH FOR CUSTOMER
                            </span>
                        </strong>
                    </td>
                </tr>`;

            $('#search-customer-table').find('tbody').append(search_banner).fadeIn("fast");
            $('#customer-search-field').val('');

            clear();
        });

        $('#customer-search-close-modal').click(function() {
            $('#customer-search-l-name').val('');
            $('#customer-search-f-name').val('');
            $('#customer-search-m-name').val('');
            $('#customer-search-number').val('');
            // $('#cms-redirect-button').fadeOut("fast");

            var search_banner =
                `<tr>
                    <td class="text-center text-sm" colspan="10">
                        <strong>
                            <span class="buying-no-transactions text-lg font-bold">
                                SEARCH FOR CUSTOMER
                            </span>
                        </strong>
                    </td>
                </tr>`;

            $('#search-customer-table').find('tbody').empty().append(search_banner).fadeIn("fast");
        });

        // search customer
        $('#customer-search-button').click(function() {
            $('#go-to-cms-button').remove();
            var birth_date = $('#customer-birth-date').val();
            var customer_l_name = $('#customer-search-l-name').val();
            var customer_f_name = $('#customer-search-f-name').val();
            var customer_m_name = $('#customer-search-m-name').val();
            var customer_number = $('#customer-search-number').val();
            var search_filter = $('input[name="customer-search-filter"]:checked').val();
            var required = '';

            // customer name
            if(search_filter == 1) {
                var nameFieldsFilledCount = 0;

                if (customer_f_name) {
                    nameFieldsFilledCount++;
                }
                if (customer_m_name) {
                    nameFieldsFilledCount++;
                }
                if (customer_l_name) {
                    nameFieldsFilledCount++;
                }

                // Ensure at least two name fields are filled
                var hasName = nameFieldsFilledCount >= 2;

                if (birth_date && hasName) {
                    searchCustomer(search_filter, customer_f_name, customer_m_name, customer_l_name, customer_number, birth_date);
                } else {
                    if (!birth_date) {
                        required += '<li><small>Birthdate field is required.</small></li>';
                    }
                    if (!hasName) {
                        required += '<li><small>At least two of the name fields is required.</small></li>';
                    }
                }
            }

            // customer no
            if(search_filter == 2) {
                if ($('#customer-search-number').val()) {
                    searchCustomer(search_filter, customer_f_name, customer_m_name, customer_l_name, customer_number, birth_date);
                }
                else {
                    required +=  '<li><small>Customer No. field is required.</small></li>';
                }
            }

            if(required) {
                Swal.fire({
                    icon: 'warning',
                    html: '<ul class="ps-0">'+required+'</ul>',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });

        function searchCustomer(search_filter, customer_f_name, customer_m_name, customer_l_name, customer_number, birth_date) {
            $('#container-test').fadeIn("slow");
            $('#container-test').css('display', 'block');

            $.ajax({
                url: "{{ route('search_customer') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    search_filter: search_filter,
                    customer_l_name: customer_l_name,
                    customer_f_name: customer_f_name,
                    customer_m_name: customer_m_name,
                    customer_number: customer_number,
                    birth_date: birth_date,
                },
                success: function(data) {
                    var employer = '';
                    var search_result_customer = data.customers;
                    var fucking_path = "{{ config('app.cms_photo_location_ip') }}";
                    var redirect_path = "{{ config('app.cms_url') }}";
                    var search_results_data = '';

                    $('#container-test').fadeOut("fast");
                    $('#search-customer-table').find('tbody').empty().fadeOut("fast");

                    var needs_updation_entries = 0;

                    if (search_result_customer.length > 0) {
                        search_result_customer.forEach(function(gar) {
                            employer = gar.Nameofemployer;

                            search_results_data +=
                                `<div class="swiper-slide">
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <div class=" rounded search-customer-image-thumbnail">
                                                <img src="${fucking_path}/${gar.ScanID ? `${gar.ScanID}` : `${gar.Photo}`}" loading="lazy" alt="customer Image"  class="responsive-image" />
                                                <div class="swiper-lazy-preloader swiper-lazy-preloader-white"></div>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="table-container">
                                                <table class="table table-bordered table-hover" id="search-customer-table">
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-xs whitespace-nowrap p-1 text-center"> <strong>Customer No </strong></td>
                                                            <td class="text-left text-xs p-1">
                                                                ${gar.CustomerNo}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-xs whitespace-nowrap p-1 text-center w-25"> <strong>Customer Name </strong></td>
                                                            <td class="text-left text-xs p-1">
                                                                ${gar.FullName}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-xs whitespace-nowrap p-1 text-center"> <strong>Birthdate </strong></td>
                                                            <td class="text-left text-xs p-1">
                                                                ${gar.Birthday}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-xs whitespace-nowrap p-1 text-center"> <strong>Risk </strong></td>
                                                            <td class="text-left text-xs p-1">
                                                                ${gar.RiskLevel === 'LOW' ? `<span class="badge success-badge-custom updated-accounts">Low</span>` : gar.RiskLevel === 'MEDIUM' ? `<span class="badge warning-badge-custom updated-accounts">Medium</span>` : gar.RiskLevel === 'HIGH' ? `<span class="badge danger-badge-custom updated-accounts">High</span>` : `Default`}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-xs whitespace-nowrap p-1 text-center"> <strong>Status </strong></td>
                                                            <td class="text-left text-xs p-1">
                                                                ${gar.IDExpiration >= formatted_date || gar.IDExpiration == null ?
                                                                    `<span class="badge success-badge-custom pt-2 updated-accounts">Updated</strong></span>`
                                                                    : gar.count >= 1 ?
                                                                        `<span class="badge danger-badge-custom pt-2 accounts-for-updation">ID Expired</span>`
                                                                        : `<span class="badge danger-badge-custom pt-2 accounts-for-updation">ID Expired</span>`
                                                                }
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-xs whitespace-nowrap p-1 text-center"><strong>Action</strong></td>
                                                            <td class="text-left text-xs p-1">
                                                                ${gar.IDExpiration >= formatted_date || gar.IDExpiration == null ?
                                                                    `<button class="btn btn-primary btn-edit-details get-customer-details btn-sm px-2 py-1" id="get-customer-details" type="button" data-customerid="${gar.CustomerID}" data-customername="${gar.FullName}" data-customerno="${gar.CustomerNo}"
                                                                    data-birthday="${gar.Birthday}"  data-celno="${gar.CelNo}"   data-email="${gar.Email}"  data-withcp="${gar.WithCP}"   data-photo="${gar.ScanID}"  data-photo2="${gar.Photo}"><i class='bx bx-user-check'></i>&nbsp;&nbsp;Select</button>`
                                                                    : gar.count >= 1 ?
                                                                        `<button class="btn btn-primary button-info text-xs btn-sm px-2 cms-redirect-button" type="button"><i class='bx bx-info-circle bx-tada me-1'></i>Update Info</button>`
                                                                            : gar.RiskLevel == 'HIGH' ?
                                                                                `<button class="btn btn-primary button-info text-xs btn-sm px-2 cms-redirect-button" type="button"><i class='bx bx-info-circle bx-tada me-1'></i>Update Info</button>`
                                                                                : `<button class="btn btn-primary button-info text-xs btn-sm px-2 cms-redirect-button" type="button"><i class='bx bx-info-circle bx-tada me-1'></i>Update Info</button>`
                                                                }
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;

                            // $('#search-customer-table').find('tbody').append(search_results_data).fadeIn("fast");
                        });

                        $('#customer_images').empty().append(search_results_data).fadeIn("fast");

                        $('.cms-redirect-button').click(function() {
                            window.open(`${redirect_path}`);
                        });

                        $('.accounts-for-updation').each(function() {
                            needs_updation_entries = $(this).length;
                        });

                        if (needs_updation_entries == 0) {
                            $('.cms-redirect-button').fadeOut("fast");
                        } else {
                            $('.cms-redirect-button').fadeIn("fast");
                        }

                        $('#cms-redirect-button').remove();
                    } else {
                        var test =
                            `<div class="swiper-slide">
                                <div class="table-container mt-1">
                                    <table class="table table-bordered table-hover" id="search-customer-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-xs whitespace-nowrap p-1"> <strong>Customer No </strong></th>
                                                <th class="text-center text-xs whitespace-nowrap p-1 w-25"> <strong>Customer Name </strong></th>
                                                <th class="text-center text-xs whitespace-nowrap p-1"> <strong>Birthdate </strong></th>
                                                <th class="text-center text-xs whitespace-nowrap p-1"> <strong>Risk </strong></th>
                                                <th class="text-center text-xs whitespace-nowrap p-1"> <strong>Status </strong></th>
                                                <th class="text-center text-xs whitespace-nowrap p-1"> <strong>Action </strong></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center text-xs p-1" colspan="100">
                                                    <strong>
                                                        <span class="buying-no-transactions text-lg font-bold">
                                                            NO CUSTOMER FOUND
                                                        </span>
                                                    </strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>`;

                        var test =
                            `<tr>
                                <td class="text-center text-sm" colspan="10">
                                    <strong>
                                        <span class="buying-no-transactions text-lg font-bold">
                                            NO CUSTOMER FOUND
                                        </span>
                                    </strong>
                                </td>1955-11-12
                            </tr>`;

                        var go_to_cms_button = `<a class='btn btn-primary btn-sm text-xs cms-redirect-button-footer' href ='${redirect_path}' id='go-to-cms-button' type='button'  target='_blank'> Go to CMS </a>`;

                        $('#customer_images').empty().append(test).fadeIn("fast");
                        $('#customer-modal-footer').append(go_to_cms_button);
                        clear();
                    }

                    if (data.sanctions.length > 0) {
                        data.sanctions.forEach(function(res) {
                            $sanctions = `<div class="table-container mt-1">
                                            <table class="table table-bordered table-hover" id="search-customer-table">
                                                <thead>
                                                    <tr>
                                                        <th colspan="100" class="text-center text-xs p-1 !bg-[#00a65a]">
                                                            <span class="text-white">Sanction Results</span>
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center text-xs whitespace-nowrap p-1 w-25"> <strong>First Name </strong></th>
                                                        <th class="text-center text-xs whitespace-nowrap p-1 w-25"> <strong>Middle Name </strong></th>
                                                        <th class="text-center text-xs whitespace-nowrap p-1 w-25"> <strong>Last Name </strong></th>
                                                        <th class="text-center text-xs whitespace-nowrap p-1"> <strong>Birthdate </strong></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="bg-red-100">
                                                        <td class="text-center text-xs p-1">
                                                            ${res.FName}
                                                        </td>
                                                        <td class="text-center text-xs p-1">
                                                            ${res.MName}
                                                        </td>
                                                        <td class="text-center text-xs p-1">
                                                            ${res.LName}
                                                        </td>
                                                        <td class="text-center text-xs p-1">
                                                            ${res.Birthday?res.Birthday: '-'}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>`;

                            $('#sanctions-container').empty().append($sanctions)
                                .fadeIn(
                                    "fast");
                        });
                    } else {
                        $no_results = `<div class="table-container mt-3">
                                                <table class="table table-bordered table-hover" d="search-customer-table">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="100" class="text-center text-xs p-1 !bg-[#00a65a]">
                                                                <span class="text-white">Sanction Results</span>
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-center text-xs whitespace-nowrap p-1 w-25"> <strong>First Name </strong></th>
                                                            <th class="text-center text-xs whitespace-nowrap p-1 w-25"> <strong>Middle Name </strong></th>
                                                            <th class="text-center text-xs whitespace-nowrap p-1 w-25"> <strong>Last Name </strong></th>
                                                            <th class="text-center text-xs whitespace-nowrap p-1"> <strong>Birthdate </strong></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-center text-xs p-1" colspan="100">
                                                                <p class="text-center text-sm m-0" ><span >No records found</span></p>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>`;


                        $('#sanctions-container').empty().append($no_results).fadeIn("fast");
                    }

                    $('.get-customer-details').click(function() {
                        const customer_no = $(this).attr('data-customerno');
                        const customer_id = $(this).attr('data-customerid');
                        const customer_name = $(this).attr('data-customername');

                        const customer_fullname = $(this).attr('data-customername');
                        const customer_photo = $(this).attr('data-photo') != 'null' ? $(this).attr('data-photo') : $(this).attr('data-photo2');
                        const customer_birthday = $(this).attr('data-birthday') != 'null' ? $(this).attr('data-birthday') : 'N/A';
                        const customer_celno = $(this).attr('data-celno') != 'null' ? $(this).attr('data-celno') : 'N/A';
                        const customer_email = $(this).attr('data-email') != 'null' ? $(this).attr('data-email') : 'N/A';
                        // const fucking_path = `${fucking_path}`;

                        const image = `${fucking_path}${customer_photo}`;

                        // <span class="text-black" style="font-size: 16px;">Could you please confirm that this is the correct customer?</span>
                        // <div class="my-1" id="count-down-container">
                        //     <span class="text-black" style="font-size: 16px;" id="count-down-label">Clickable in:</span>&nbsp;<span class="text-red-500" style="font-size: 17px;"></span>
                        // </div>

                        Swal.fire({
                            html: `
                            <div class="row justify-content-center px-2 py-1">
                                <div class="col-12 text-center border border-2 border-gray-300 rounded-lg p-2">
                                    <div class="row justify-content-center">
                                        <img src="${image}" alt="Customer Photo" style="width: 470px;">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2 mt-1" id="count-down-container">
                                <span class="text-black" style="font-size: 17px;" id="count-down-label">Is this the correct customer?&nbsp;<strong class="text-red-500"  id="count-down">3</strong></span>
                            </div>`,
                            confirmButtonColor: '#3085d6',
                            showCancelButton: true,
                            cancelButtonColor: '#8592A3',
                            confirmButtonText: 'Proceed',
                            showClass: {
                                popup: 'swal2-zoom-in'
                            },
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
                                selectCustomer(customer_no, customer_id, customer_name, customer_fullname, customer_photo, customer_birthday, customer_celno, customer_email, fucking_path);
                            }
                        });
                    });

                    function selectCustomer(customer_no, customer_id, customer_name, customer_fullname, customer_photo, customer_birthday, customer_celno, customer_email, fucking_path) {
                        Swal.fire({
                            title: 'Customer Selected!',
                            icon: 'success',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            cancelButtonColor: '#8592A3',
                            timer: 750,
                            showClass: {
                                popup: 'swal2-zoom-in'
                            },
                        }).then(() => {
                            setTimeout(function() {
                                $('#customerDeetsModal').modal('hide');
                            });

                            // if (("{{ session('time_toggle_status') }}") == 0) {
                            //     $('#or-number-container').fadeOut("fast");
                            // }

                            if (window.location.href !== $('#base-url').val() + '/sellToManila') {
                                $('#r-set-b').prop('checked', true);
                            } else {
                            }
                                $('#r-set-o').prop('checked', true);

                            $('#radio-button-Bills').prop('checked', true).change();

                            $('#get-bills').removeAttr('disabled');
                            $('#or-number-buying').removeAttr('disabled');
                            $('input[name="radio-rset"]').removeAttr('disabled');
                            $('#or-number-selling').removeAttr('disabled');
                            // $('#currencies-select-selling').removeAttr('disabled');
                            $('input[name="radio-transact-type"]').removeAttr('disabled');
                            $('#radio-button-BILLS').prop('checked', true).trigger('change');
                            $('input[name="dpo-out-usd-amount"]').removeAttr('disabled');
                            $('#remarks').removeAttr('disabled', 'disabled');
                            $('#petnet-selling-rate').removeAttr('disabled', 'disabled');
                        });

                        $('#customer-id-selected').val(customer_id);
                        $('#customer-no-selected').val(customer_no);
                        $('#customer-name-selected').val(customer_name);

                        if (employer) {
                            $('#customer-employer').val(employer);
                        } else {
                            $('#customer-employer').val('N/A');
                        }

                        $('#customername').val(customer_fullname);
                        $('#customerno').val(customer_id);

                        $('.customer_no span').last().text(customer_id);
                        $('.customer_name span').last().text(customer_fullname);
                        $('.customer_birthday span').last().text(customer_birthday);
                        $('.customer_celno span').last().text(customer_celno);
                        $('.customer_email span').last().text(customer_email);
                        $('.radio-button-rset').removeAttr('disabled');
                        $('.customer-image-thumbnail').find('#customer-photo').attr('src', fucking_path + customer_photo);

                        $('.img_show').on('click',  function() {
                            var imgSrc = $(this).attr('src');
                            $('.myimg').attr('src', imgSrc);
                            $('#modal-image').modal('show');
                        });
                    }
                }
            });
        }

        $('#customer-search-number').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value.length > 11) {
                this.value = this.value.slice(0, 11);
            }
        });
    });
</script> --}}

<script>
    $(document).ready(function() {
        // Initialized RSet
        updateRsetToggle();

        // Handle rset updates
        function updateRsetToggle() {
            $.ajax({
                url: "{{ route('rset') }}",
                type: "POST",
                beforeSend: function() {
                    $("#loader").show();
                },
                data: {
                    "_token": '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        if (response.rset === 'O') {
                            $('#rsetAvatar').addClass('avatar-online');
                        } else {
                            $('#rsetAvatar').removeClass('avatar-online');
                        }
                    }
                }
            });
        }
    });
</script>

{{-- ======================================================================================================================================================================================== --}}

{{-- Log In Scripts --}}

{{-- Input field validation for login --}}
<script>
    (function () {
        'use strict'

        var forms = document.querySelectorAll('.needs-validation')

        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated');
                }, false)
            })
        })(
    );
</script>

{{-- Log in design/animation --}}
<script>
    $(document).ready(function(){
        $('.forex-card-login').hide();

        $('.login-col').fadeIn( 3000, function() {
            $('.forex-card-login').fadeIn( 600 );
        });
    });
</script>

{{-- End of Log In Scripts --}}

{{-- ======================================================================================================================================================================================== --}}

{{-- Forex V2 UI/UX Scripts --}}

{{-- UI/UX - Swiper JS script for Buying Transaction's Currency Manual --}}
<script>
    $(document).ready(function() {
        var swiper = new Swiper(".currency-swiper-thumb", {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
        });

        var currency_swiper_main = new Swiper(".currency-swiper-main", {
            spaceBetween: 10,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },

            thumbs: {
                swiper: swiper,
            },
        });
    });
</script>

{{-- Sneat template - custom scripts --}}

{{-- Menu toggle && Active classes --}}
<script>
    $(document).ready(function(){
        $('.menu-toggle').click(function() {
            $(this).addClass('active');
            $(this).siblings('.menu-sub').slideToggle('fast');
        });

        var current_url = window.location.href;
        var dynamic_url = $('.current-dynamic-url').val() + '/authenticate';

        // $('.menu-item').removeClass('active');

        $('.menu-item').each(function(){
            var menu_url = $(this).find('.menu-link').attr('href');

            if(current_url.includes(menu_url)) {
                $(this).addClass('active');
            }
        });

        $('.menu-sub .menu-item').removeClass('active');

        $('.menu-sub .menu-item').each(function() {
            var menu_url = $(this).find('.menu-link').attr('href');

            if(current_url.includes(menu_url)) {
                $(this).addClass('active');
                $(this).parents('.menu-item').addClass('active').toggleClass('open');
                $(this).parents('.menu-sub').css('display' , 'block');
            }
        });

        $('.menu-item').click(function() {
            $(this).toggleClass('open');
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('[data-bs-toggle="popover"]').popover({
            trigger: 'hover',
            delay: { "show": 100, "hide": 100 }
        });

        $(document).on('mouseleave', '[data-bs-toggle="popover"]', function() {
            var popover = $(this).data('bs.popover');
            if (popover) {
                popover.hide();
            }
        });
    });

    $(document).ready(function() {
        $('input').attr('autocomplete', 'off');
    });
</script>

{{-- Time on and off toggle --}}
<script>
    $(document).ready(function(){
        var on_off_session_val = $('#on-off-status-session').val();
        var _token = $('input[name="_token"]').val();

        $('[name="graduate"]').change(function() {
            if($('[name="graduate"]:checked').is(":checked")){
                $('#time-on-and-off-status').attr('data-timeonandoffstatus' , 1);
                updateTimeToggSession(1);
            } else {
                $('#time-on-and-off-status').attr('data-timeonandoffstatus' , 0);
                updateTimeToggSession(0);
            }
        });

        function updateTimeToggSession(value) {
            $.ajax({
                url: "{{ URL::to('/updateTimeToggleSession') }}",
                type: "POST",
                data: {
                    _token: _token,
                    time_toggle_session: value
                }, success: function(data) {
                    var toggle_stat_to_int = parseInt(data.get_time_toggle_stat);
                    $('#container-test').fadeIn("slow");
                    $('#container-test').css('display', 'block');

                    setTimeout(function() {
                        location.reload(true);
                    }, 2500);

                    if (toggle_stat_to_int == 1) {
                        $('[name="graduate"]:checked').attr('checked', 'checked');
                    } else if (toggle_stat_to_int == 0) {
                        $('[name="graduate"]:checked').removeAttr('checked', 'checked');
                    }
                }
            });
        }
    });
</script>

{{-- Toggle - time on and off --}}
<script>
    $(document).ready(function() {
        $("#time-on-off-card").hide();

        $("#user-icon-forex").click(function() {
            if ($("#time-on-off-card").is(":hidden")) {
                // $('#or-number-container').show();
                $("#time-on-off-card").fadeIn("fast");
            } else {
                // $('#or-number-container').hide();
                $("#time-on-off-card").fadeOut("fast");
            }
        });
    });
</script>

{{-- Security code popover --}}
<script>
    $(document).ready(function() {
        $("#security-code-card").hide();

        $("#print-test").click(function() {
            if ($("#security-code-card").is(":hidden")) {
                $("#security-code-card").fadeIn("fast");
            } else {
                $("#security-code-card").fadeOut("fast");
            }
        });
    });
</script>

{{-- Script active classes in nav bar --}}
<script>
    $(document).ready(function() {
        var project_base_url = $('#base-url').val();

        var current_url = window.location.href;
        var pending_serial_url = $('#pending-serials-url').attr('data-transactpendingserials');
        var sold_serial_url = $('#sold-serials-url').attr('data-soldserials');
        var edit_buying_url = $('#url-edit-buying-trans').attr('data-editbuyingurl');
        var forex_list_url = $('#forex-url').attr('data-forexurl');

        if (current_url == edit_buying_url) {
            $('.transact-menu').addClass('active open');
            $('.buying-transact-menu').addClass('active');
        } else if (current_url == forex_list_url) {
            $('.transact-menu').addClass('active open');
            $('.buying-transact-menu').addClass('active');
        } else if (current_url == pending_serial_url) {
            $('.transact-menu').addClass('active open');
            $('.buying-transact-menu').addClass('active');
        } else if (project_base_url + '/buyingTransact' == current_url) {
            $('.transact-menu').addClass('active open');
            $('.buying-transact-menu').addClass('active');
        } else if (project_base_url + '/sellingTransact' == current_url) {
            $('.transact-menu').addClass('active open');
            $('.selling-transact-menu').addClass('active');
        } else if (project_base_url + '/transferForex' == current_url) {
            $('.transfer-forex').addClass('active');
        } else if (current_url == sold_serial_url) {
            $('.transact-menu').addClass('active open');
            $('.selling-transact-menu').addClass('active');
        }
    });
</script>

{{-- Logout script --}}
<script>
    $(document).ready(function() {
        $('#navbar-logout-button').click(function(){
            var logout_route = $(this).attr('data-logoutroute');

            let timerInterval;

            Swal.fire({
                title: "Loggin' out...",
                timer: 2000,
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
                $('#container-test').fadeIn("slow");
                $('#container-test').css('display', 'block');

                setTimeout(function() {
                    window.location.href = logout_route;
                }, 1500);
            });
        });
    });
</script>

{{-- Popover buttons --}}
<script>
    $(document).ready(function() {
        $('.btn-popover').popover({
            trigger: "hover",
            container: "body",
            placement: "bottom",
            customClass: "popover-dark",
            content: function() {
                switch (true) {
                    case $(this).hasClass('btn-delete'):
                        return "Void";
                        break;
                    case $(this).hasClass('btn-details'):
                        return "Details";
                        break;
                    case $(this).hasClass('btn-acknowledge'):
                        return "Acknowledge";
                        break;
                    case $(this).hasClass('btn-css-report'):
                        return "CSS Report";
                        break;
                    case $(this).hasClass('btn-revert-tracking'):
                        return "Revert Tracking No.";
                        break;
                    default:
                        return "Popover";
                        break;
                }
            }
        });
    });
</script>

{{-- End of Forex V2 UI/UX Scripts --}}

{{-- ======================================================================================================================================================================================== --}}

{{-- Excess scripts --}}

{{-- Utilities - ajax for toggle orderby of data of branch buffer --}}
<script>
    $(document).ready(function(){
        setTimeout(function() {
            $('.bg-toast-custom-gold').fadeOut(4000);
        }, 5000);

        $('#toggle_select_submit').on('change',function(){
            $('#form-toggle-orderby').submit();
        });

        $('input[type="text"]').on('input', function() {
            $(this).val($(this).val().toUpperCase());
        });
    });

    $(document).ready(function() {
        // $('body').css('zoom', '85%');

        // Custom menu toggle
        // Retrieve the initial state of the menu bar from localStorage
        var isCollapsed = localStorage.getItem('menuCollapsed');

        // If the menu state is collapsed, apply the corresponding classes
        if (isCollapsed === 'true') {
            $('.layout-page').addClass('collapse-mainpage');
            $('.text-truncate').addClass('hide-text');
            $('.menu-toggle').addClass('small');
            $('.menu-link').addClass('small-menu-link');
            $('#layout-menu').addClass('collapse-sidebar');

            $('#collapse-nav-chevron-left-icon').addClass('rotate-right');

                // Update the state of the menu bar in localStorage
            if ($('#layout-menu').hasClass('collapse-sidebar')) {
                localStorage.setItem('menuCollapsed', 'true');
                $('.menu-toggle').siblings('.menu-sub').slideUp('fast');
            } else {
                localStorage.setItem('menuCollapsed', 'false');
                var menu_item_with_active = $('.menu-item.active');
                var menu_link_a_tag = menu_item_with_active.find('.menu-link.menu-toggle');
                var menu_sub = menu_link_a_tag.siblings('.menu-sub');
                menu_sub.slideToggle('fast');
            }
        }

        // Toggle function for the menu bar
        $('#test-click').click(function() {
            // Toggle classes for menu elements
            $('.layout-page').toggleClass('collapse-mainpage');
            $('.text-truncate').toggleClass('hide-text');
            $('.menu-toggle').toggleClass('small');
            $('.menu-link').toggleClass('small-menu-link');
            $('#layout-menu').toggleClass('collapse-sidebar');
            $('#collapse-nav-chevron-left-icon').toggleClass('rotate-right');

            // Update the state of the menu bar in localStorage
            if ($('#layout-menu').hasClass('collapse-sidebar')) {
                localStorage.setItem('menuCollapsed', 'true');
                $('.menu-toggle').siblings('.menu-sub').slideUp('fast');
            } else {
                localStorage.setItem('menuCollapsed', 'false');
                var menu_item_with_active = $('.menu-item.active');
                var menu_link_a_tag = menu_item_with_active.find('.menu-link.menu-toggle');
                var menu_sub = menu_link_a_tag.siblings('.menu-sub');
                menu_sub.slideToggle('fast');
            }
        });
    });
</script>

{{-- Dashboard Swiper --}}
<script>
    var swiper = new Swiper("#rate-swiper", {
        spaceBetween: 30,
        centeredSlides: true,
        loop: true,
        autoplay: {
            delay: 10000,
            disableOnInteraction: false,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
</script>

<script>
    var swiper = new Swiper("#tagged-b-swiper", {
        spaceBetween: 30,
        centeredSlides: true,
        loop: true,
        autoplay: {
            delay: 10000,
            disableOnInteraction: false,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
</script>

{{-- End of Excess scripts --}}
