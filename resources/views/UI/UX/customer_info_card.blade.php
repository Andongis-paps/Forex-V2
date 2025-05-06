{{-- <div class="card">
    <div class="col-12 p-1 border border-gray-300 rounded-tl rounded-tr">
        <div class="row align-items-center px-2 py-1">
            <div class="col-12">
                <span class="text-lg font-bold text-black p-2">
                    <i class='bx bx-list-ul' ></i>&nbsp;
                    Customer Information
                </span>
            </div>
        </div>
    </div>

    <div class="col-12 p-1 border border-gray-300">
        <div class="row py-1 px-3">
            <div class="col-4 px-2 text-center">
                <div class="ms-0 rounded customer-image-thumbnail">
                    <img class="responsive-image img_show" id="customer-photo" src="{{ asset('images//default-customer-img.jpg') }}" alt="Item Image" >
                </div>
            </div>
            <div class="col-8 ps-4">
                <ul class="list-unstyled my-1">
                    <li class="d-flex align-items-center mb-3 customer_no text-sm"><i class="bx bx-id-card"></i><span class="  mx-2"><strong>Customer No:</strong></span> <span> </span></li>
                    <li class="d-flex align-items-center mb-3 customer_name text-sm"><i class="bx bx-user"></i><span class="  mx-2"><strong>Customer Name:</strong></span> <span> </span></li>
                    <li class="d-flex align-items-center mb-3 customer_birthday text-sm"><i class="bx bx-calendar"></i><span class="  mx-2"><strong>Birthday:</strong></span> <span> </span></li>
                    <li class="d-flex align-items-center mb-3 customer_celno text-sm"><i class="bx bx-phone"></i><span class="  mx-2"><strong>Cell No:</strong></span> <span> </span></li>
                    <li class="d-flex align-items-center mb-3 customer_email text-sm"><i class="bx bx-envelope"></i><span class="  mx-2"><strong>Email:</strong></span> <span> </span></li>
                </ul>
            </div>
        </div>
    </div>
</div> --}}

<div class="card border  border-gray-300  mb-4">
    <div class="col-12 p-1 border border-gray-300 rounded-tl rounded-tr">
        <div class="row align-items-center px-2 py-1">
            <div class="col-12">
                <span class="text-lg font-bold text-black p-2">
                    <i class="bx bx-user-circle me-2 bx-sm"></i>&nbsp;
                    Customer Information
                </span>
            </div>
        </div>
    </div>

    <div class="col-12 p-2 border-b border-gray-300 rounded-tl rounded-tr">
          <div class="row">
                <div class="col-3">
                    <div class="ms-0 rounded customer-image-thumbnail">
                        <img src="{{ App\Helpers\CustomerManagement::getCustomerPhoto(!empty($customer->CustomerID) ? $customer->CustomerID : '') }}" alt="Item Image" class="responsive-image img_show" id="customer-photo">
                    </div>
                </div>
                <div class="col-9">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td class="text-sm align-middle py-1">
                                    <i class="bx bx-id-card me-1"></i>
                                    <strong>CUSTOMER NO:</strong>
                                </td>
                                <td class="text-sm align-middle py-1 customer_no">
                                    {{ !empty($customer->CustomerID) ? $customer->CustomerID : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-sm align-middle py-1">
                                    <i class="bx bx-user me-1"></i>
                                    <strong> CUSTOMER NAME:</strong> 
                                </td>
                                <td class="text-sm align-middle py-1 customer_name">
                                    {{ !empty($customer->FullName) ? $customer->FullName : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-sm align-middle py-1">
                                    <i class="bx bx-calendar me-1"></i>
                                    <strong>BIRTHDAY:</strong>
                                </td>
                                <td class="text-sm align-middle py-1 customer_birthday">
                                    {{ !empty($customer->Birthday) ? Carbon\Carbon::parse($customer->Birthday)->format('F j, Y') : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-sm align-middle py-1">
                                    <i class="bx bx-phone me-1"></i>
                                    <strong> CONTACT NO:</strong>
                                </td>
                                <td class="text-sm align-middle py-1 customer_celno">
                                    {{ !empty($customer->WithCP) && $customer->WithCP ? $customer->CelNo : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-sm align-middle py-1">
                                    <i class="bx bx-envelope me-1"></i>
                                    <strong>EMAIL:</strong>
                                </td>
                                <td class="text-sm align-middle py-1 customer_email">
                                    {{ !empty($customer->Email) ? $customer->Email : '-' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
          </div>
    </div>
</div>



