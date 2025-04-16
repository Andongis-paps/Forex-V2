// $(function() {
//     var $hamburger = $('.hamburger'),
//         $nav = $('#site-nav'),
//         $masthead = $('#masthead');
//
//     $hamburger.click(function() {
//         $(this).toggleClass('is-active');
//         $nav.toggleClass('is-active');
//         $masthead.toggleClass('is-active');
//         return false;
//     })
// });

jQuery(".footer-main h6").click(function(){
    jQuery(this).parent(".footer-nav").toggleClass("open");
});

$(document).ready(function() {
    $('.shoppingCartItems').click(function() {
        $('.dropdownShoppingCartItems').toggle();
        $('.dropdownWishListCartItems').hide();
    });

    $('.close-dropdownShoppingCartItems').click(function() {
        $('.dropdownShoppingCartItems').hide();
    });
});

//SHOPPING CART DROPDOWN
function getCart(htmlCharacters) {
    dom = document.createElement('template');
    dom.innerHTML = htmlCharacters;
    content = dom.content;
    var gaId = "{{ env('GA_ID') }}";
    var gaItem = {
        "id": $(content).find('#ga-item-id').text(),
        "name": $(content).find('#ga-item-name').text(),
        "list_name": $(content).find('#ga-item-list_name').text(),
        "brand": "SpeedRegalo",
        "category": $(content).find('#ga-item-category').text(),
        "variant": $(content).find('#ga-item-variant').text(),
        "list_position": $(content).find('#ga-item-list_position').text(),
        "quantity": $(content).find('#ga-item-quantity').text(),
        "price": $(content).find('#ga-item-price').text()
    }
    gtag('event', 'add_to_cart', {
        "items": [
            gaItem
        ]
    });
    gtag('config', gaId)
    items_no = $(content).find('span.items')[0].innerText.replace(/\D/g,'');
    items_checked = 0;
    shopping_car_items = ``;
    dom_shopping_car_items = $(content).find('ul.shopping-cart-items li');
    sub_total_price = 0;
    dom_shopping_car_items.each(function(index, elem){
        if($(elem).find('.item-thumb a').length > 0)
        {
            customer_basket_id = $($(elem).find('.item-thumb a')).attr('onclick').replace(/\D/g,'');
            product_img_url = $(elem).find('.item-thumb img')[1].src;
            product_name = $(elem).find('.item-name')[0].innerText;
            product_qty = $(elem).find('.item-quantity')[0].innerText.replace(/\D/g,'');
            product_price = $(elem).find('.item-price')[0].innerText.replace(/[^0-9$.,]/g,'');
            product_is_checked = $(elem).find('.cart_is_checked')[0].value;

            if(product_is_checked == 1)
            {
                sub_total_price += parseFloat(product_price);
                items_checked += 1;
            }

            shopping_car_items += `<li data-content="${customer_basket_id}" class="header-car-items-list header-cart-item-list-id_${customer_basket_id}">
                        <div class="row">
                            <div class="col-2 checkbox-div">
                                <input type="checkbox" class="cart_confirmation" value="${customer_basket_id}" id="cartConfirmation_${customer_basket_id}" onchange="cartConfirmation(${customer_basket_id})" ${product_is_checked == 1 ? 'checked' : ''}>
                            </div>
                            <div class="col-3 img-div">
                                <img class="img-fluid" src="${product_img_url}">
                            </div>
                            <div class="col-5 product-detail-div">
                                <h2 class="text-uppercase">${product_name}</h2>
                                <p class="product-cart-price" id="product_cart_price_${customer_basket_id}"><?=$web_setting[19]->value?>${product_price}</p>
                                <div class="qty-div">
                                    <span class="mdi mdi-minus" onclick="qtyMinusCart(${customer_basket_id})"></span>
                                    <span id="product_cart_qty_${customer_basket_id}">${product_qty}</span>
                                    <span class="mdi mdi-plus" onclick="qtyAddCart(${customer_basket_id})"></span>
                                </div>
                            </div>
                            <div class="col-1 delete-cart-btn">
                                <i class="mdi mdi-close" onclick="delete_cart_product(${customer_basket_id})"></i>
                            </div>
                        </div>
                </li>`;
        }
    });

    summaries = $(content).find('.tt-summary p');
    if(summaries.length > 0)
    {
        subtotal = "<?=$web_setting[19]->value?>"+(Math.round(sub_total_price * 100) / 100).toFixed(2);
        $('.dropdown-cart-items').show();
        $('.dropdown-cart-totals').show();
        $('.dropdown-cart-links').show();
        $('.shoppingCarItems_number').html(items_no);
        $('.dropdownShoppingCartItems_status').html('<h1 class="text-uppercase">Your Gift Cart</h1>')
        $('.close-dropdownShoppingCartItems').css({'margin-top':'-23px'});
    }
    else
    {
        subtotal = 0;
        $('.dropdown-cart-items').hide();
        $('.dropdown-cart-totals').hide();
        $('.dropdown-cart-links').hide();
        $('.shoppingCarItems_number').html('');
        $('.dropdownShoppingCartItems_status').html('<p class="no-item-carts text-capitalize">You have no items in your shopping cart.</p>')
        $('.close-dropdownShoppingCartItems').css({'margin-top':'-10px'});
    }

    $('.header-car-items').html(shopping_car_items);
    $('.total-cart-item').html(items_no);

    if(items_no > 2)
    {
        $('.dropdownShoppingCartItems .header-car-items').css({
            'overflow-y':'scroll',
            'overflow-x':'hidden',
            'height':'290px'
        });
    }

    if(items_checked == items_no)
    {
        $('.check-all-cart-btn').html('Deselect All');
    }
    else
    {
        $('.check-all-cart-btn').html('Select All');
    }

    var cartTotal = 0;
    $('.product-cart-price').each(function(index){
        var price = parseFloat($(this).text().substring(1));
        cartTotal += parseFloat(price.toFixed(2));
    });

    $('.subtotal-price').html(cartTotal);
}

$(document).ready(function(){
    $("#newsletterModal").modal('show');
});
