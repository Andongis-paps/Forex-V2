{{-- Modal - Get customer name --}}
<div class="modal fade" id="customerDeetsModal" tabindex="-1" aria-labelledby="customerDeetsModal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header ps-3 py-2">
                <span class="text-lg">
                    <i class='bx bx-check-square bx-sm pb-1'></i>&nbsp;<strong>{{ trans('labels.customer_details') }}</strong>
                </span>
            </div>

            <div class="modal-body pb-2">
                <div class="row px-2 align-items-center justify-content-center">
                    <form method="POST" id="searchCustomerForm" class="mb-0">
                        @csrf
                        <div class="col-12">
                            <div class="row align-items-center justify-content-center p-0">
                                <div class="col-8 text-center">
                                    <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check" name="filter" id="customer-search-filter-1" value="1" checked>
                                        <label class="btn btn-outline-primary btn-sm" for="customer-search-filter-1">
                                            <strong>{{ trans('labels.customer_search_name') }}</strong>
                                        </label>

                                        <input type="radio" class="btn-check" name="filter" id="customer-search-filter-2" value="2">
                                        <label class="btn btn-outline-primary btn-sm" for="customer-search-filter-2">
                                            <strong>{{ trans('labels.customer_search_number') }}</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="c-searching-name">
                            <div class="col-3 mt-3" id="c-searching-f-name">
                                <label class="mb-1" for="customer-search-f-name">
                                    <strong><span class="text-sm">{{ trans('labels.customer_search_f_name') }}</span>: </strong>
                                </label>
    
                                <input class="form-control" type="text" id="f-name" name="f-name" placeholder="First Name">
                            </div>
    
                            <div class="col-3 mt-3" id="c-searching-m-name">
                                <label class="mb-1" for="m-name">
                                    <strong><span class="text-sm">{{ trans('labels.customer_search_m_name') }}</span>: </strong>
                                </label>
    
                                <input class="form-control" type="text" id="m-name" name="m-name" placeholder="Middle Name">
                            </div>
    
                            <div class="col-3 mt-3" id="c-searching-l-name">
                                <label class="mb-1" for="l-name">
                                    <strong><span class="text-sm">{{ trans('labels.customer_search_l_name') }}</span>: </strong>
                                </label>
    
                                <input class="form-control" type="text" id="l-name" name="l-name" placeholder="Last Name">
                            </div>
    
                            <div class="col-3 mt-3" id="c-searching-birth-date">
                                <label class="mb-1" for="birth-date">
                                    <strong><span class="text-sm">{{ trans('labels.customer_search_b_date') }}</span>: <span class="required-class">*</span></strong>
                                </label>
    
                                <input class="form-control" type="text" id="birth-date" name="birth-date" placeholder="YYYY-MM-DD">
                            </div>

                            <small class="text-muted pt-1">
                                <i class="bx bx-info-circle me-2"></i>Note: Search formats (Two of a Name + Birthday).
                            </small>
                        </div>

                        <div class="row d-none" id="c-searching-customer-number">
                            <div class="col-12 mt-3">
                                <label class="mb-1" for="c-number">
                                    <strong><span class="text-sm">{{ trans('labels.customer_search_number') }}</span>: <span class="required-class">*</span></strong>
                                </label>

                                <input class="form-control mb-1" type="number" id="c-number" name="c-number" placeholder="Customer Number">

                                <small class="text-muted">
                                    <i class="bx bx-info-circle me-2"></i>Note: Search by Customer No.
                            </small>
                            </div>
                        </div>

                        <div class="col-12 mt-3 text-center">
                            <div class="row justify-content-center px-3">
                                <div class="col-3">
                                    <div class="row">
                                        <button class="btn btn-primary btn-sm" id="customer-search-button">
                                            {{ trans('labels.rate_reports_search') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div id="sanctions-container"></div>

                            <div class="swiper test-swiper" id="test-swiper">
                                <div class="swiper-wrapper pb-2" id="customer_images">

                                </div>

                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>

                            {{-- <div class="swiper-pagination"></div> --}}
                        </div>

                        {{-- <div class="col-1">
                            <div class="row">
                                <div class="swiper-pagination"></div>
                            </div>
                        </div> --}}
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-12 text-end" id="customer-modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" id="customer-search-close-modal" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var swiper = new Swiper(".mySwiper", {
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

        $('#customerDeetsModal').on('show.bs.modal', function() {
            resetModal();
        });

        // search customer date picker
        $("#customer-birth-date").flatpickr({
            maxDate: new Date(new Date().setFullYear(new Date().getFullYear() - 18)),
            dateFormat: "Y-m-d",
            allowInput: true,
            todayHighlight: true,
            minDate: 0
        });

        // search customer filter change script
        $(document).on('change', 'input[name="filter"]', function() {
            var filter_type = $(this).val();
            var no_record =`<tr class="no-record"><td colspan="100"  class="text-center text-sm" ><span >No records found</span></td></tr>`;

            if (parseInt(filter_type) == 1) {
                    $('#c-searching-name').fadeIn(100);
                    $('#c-searching-customer-number').fadeOut(100);
            }
            if (parseInt(filter_type) == 2) {
                    $('#c-searching-name').fadeOut(100);
                    $('#c-searching-customer-number').removeClass('d-none').fadeIn(100);
            }

            resetModal();
        });

        $('#searchCustomerForm').on('submit', function(e) {
            e.preventDefault();
            searchCustomer(this);
        });

        function searchCustomer(form) {
            var form_data = new FormData(form);

            $.ajax({
                type: "POST",
                url: "{{ route('search_customer') }}",
                contentType: false,
                processData: false,
                data: form_data,
                beforeSend: function() {
                    $('#container-test').fadeIn("fast");
                    $('#container-test').css('display', 'block');
                },
                complete: function() {
                    $('#container-test').fadeOut("fast");
                },
                success: function(result) {
                    var fucking_path = "{{ config('app.cms_url') }}";
                    var rows = '';

                    if(result.errors) {
                        $('#container-test').fadeOut("fast");

                        Swal.fire({
                            title: result.title,
                            html: result.html,
                            text: result.message,
                            icon: 'warning',
                            showConfirmButton: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            backdrop: true,
                            focusConfirm: false,
                            showClass: {
                                popup: 'swal2-zoom-in'
                            },
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.success) {  
                            // window.location.reload(); // You can optionally enable this if needed
                            }
                        });
                    }

                    resetModal();

                    if (result.customers.length > 0) {
                        result.customers.forEach(function(res) {

                            rows += `<div class="swiper-slide"> 
                                        <div class="row">
                                            <div class="col-6">
                                                <div class=" rounded search-customer-image-thumbnail">
                                                    <img src="${res.Photo}" loading="lazy" alt="customer Image" class="responsive-image" />
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="table-container">
                                                <table class="table table-bordered table-hover" id="search-customer-table">
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-xs whitespace-nowrap">
                                                                    <strong>Customer No</strong>
                                                                </td>
                                                                <td class="text-xs">
                                                                    ${res.CustomerNo}
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td class="text-xs whitespace-nowrap w-25">
                                                                    <strong>Customer Name</strong>
                                                                </td>
                                                                <td class="text-xs">
                                                                    ${res.FullName}
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td class="text-xs whitespace-nowrap">
                                                                    <strong>Birthdate</strong>
                                                                </td>
                                                                <td class="text-xs">
                                                                    ${res.Birthday ? new Intl.DateTimeFormat('en-US', { year: 'numeric', month: 'long', day: '2-digit' }).format(new Date(res.Birthday)) : '-'}
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td class="text-xs whitespace-nowrap">
                                                                    <strong>Status</strong>
                                                                </td>
                                                                <td class="text-xs text-danger">
                                                                    ${res.Reason}
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td class="text-xs whitespace-nowrap">
                                                                    <strong>Action</strong>
                                                                </td>
                                                                <td class="text-xs">
                                                                ${res.Status? 
                                                                    `<button class="btn btn-success btn-sm get-customer-details py-1 px-3" id="get-customer-details" type="button" data-customerid="${res.CustomerID}"  data-photo="${res.Photo}"  ><i class='bx bx-user-check me-1'></i>Select</button>`
                                                                    :
                                                                    `<a class="btn btn-primary btn-warning btn-sm text-sm cms-redirect-button pe-2" href="${fucking_path}" type="button" target="_blank"><i class='bx bx-info-circle bx-flashing me-2'></i>Update Info</a>`
                                                                }
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;

                            $('#customer_images').empty().append(rows).fadeIn("fast");
                        });
                    } else {
                        $no_results = `<div class="swiper-slide">
                                            <div class="table-container mt-1">
                                                <table class="table table-bordered table-hover" id="search-customer-table">
                                                <thead>
                                                        <tr>
                                                            <th class="text-center text-xs whitespace-nowrap"><strong>Customer No</strong></th>
                                                            <th class="text-center text-xs whitespace-nowrap w-25"><strong>Customer Name</strong></th>
                                                            <th class="text-center text-xs whitespace-nowrap"><strong>Birthdate</strong></th>
                                                            <th class="text-center text-xs whitespace-nowrap"><strong>Risk</strong></th>
                                                            <th class="text-center text-xs whitespace-nowrap"><strong>Status</strong></th>
                                                            <th class="text-center text-xs whitespace-nowrap"><strong>Action</strong></th>
                                                        </tr
                                                </thead>
                                                <tbody>
                                                        <tr>
                                                            <td class="text-center text-xs" colspan="100">
                                                                <p class="text-center text-sm" ><span >No records found</span></p>
                                                            </td>
                                                        </tr>
                                                </tbody>
                                                </table>
                                            </div>
                                        </div>`;

                        $cms_button = `<a class='btn btn-primary text-xs' href ='${fucking_path}/cms/login' id='cms-redirect-button' type='button'  target='_blank'> Go to CMS </a>`;

                        $('#customer_images').empty().append($no_results).fadeIn("fast");

                        resetModal();

                        $('.customer-modal-footer').append($cms_button);
                    }


                    if (result.sanctions.length > 0) {
                        result.sanctions.forEach(function(res) {
                            $sanctions = `<div class="table-container mt-1">
                                            <table class="table table-bordered table-hover" id="search-customer-table">
                                                <thead>
                                                <tr>
                                                        <th colspan="100" class="text-center text-xs p-0">
                                                            <div class=" bg-[#FFD50A] p-2">
                                                                <strong>Sanction Results</strong>
                                                            </div>
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
                                                            ${res.Birthday}
                                                        </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>`;

                            $('#sanctions-container').empty().append($sanctions).fadeIn("fast");

                        });
                    } else {

                        $no_results = `<div class="table-container mt-2">
                                                <table class="table table-bordered table-hover" id="search-customer-table">
                                                <thead>
                                                        <tr>
                                                            <th colspan="100" class="text-center text-xs p-0">
                                                                <div class=" bg-[#FFD50A] p-2">
                                                                    <strong>Sanction Results</strong>
                                                                </div>
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
                        var customerid = $(this).attr('data-customerid');
                        var photo = $(this).attr('data-photo');

                        Swal.fire({
                            html: `
                                    <div class="row justify-content-center px-2 py-1">
                                        <div class="col-12 text-center border border-2 border-gray-300 rounded-lg p-2">
                                            <div class="row justify-content-center">
                                                <img src="${photo}" alt="Customer Photo" style="width: 470px;">
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
                                    const count_down_container =Swal.getHtmlContainer().querySelector('#count-down-container');

                                    const count_down_tick =
                                        setInterval(
                                            () => {
                                                countdown -= 1;
                                                count_down_display.textContent = countdown;

                                                if (countdown === 0) {
                                                        confirm_button.disabled = false;
                                                        // count_down_label.style.display = 'none';
                                                        count_down_display.style.display = 'none';
                                                        // count_down_container.style.display = 'none';

                                                        clearInterval(
                                                            count_down_tick
                                                        );
                                                }
                                            }, 1000);
                                    }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                    var route = "{{ route(Route::currentRouteName(), array_merge(request()->route()->parameters(), ['customerid' => '__CUSTOMER_ID__'])) }}";
                                    var url = route.replace('__CUSTOMER_ID__', customerid);

                                    Swal.fire({
                                        title: '',
                                        icon: 'success',
                                        html: '<strong>Customer Selected!</strong>',
                                        showConfirmButton: false,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                        timer: 750,
                                        showClass: {
                                            popup: 'swal2-zoom-in'
                                        },
                                    }).then(() => {
                                        setTimeout(function() {
                                            window.location.href = url;
                                            $('#modal').modal('hide');
                                        });
                                    });

                            }
                        });
                    });
                },
            });
        }

        function resetModal() {
            $('#customer_images').empty().fadeOut("fast");
            $('#sanctions-container').empty().fadeOut("fast");
            $('.customer-modal-footer #cms-redirect-button-footer').remove();
        }
    });
</script>