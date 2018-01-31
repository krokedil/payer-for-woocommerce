jQuery( function( $ ) {
    var wc_payer_instant_checkout = {
        addInstantCheckoutButton: function() {
            if ( 'cart' === page_type ) {
                var checkout_button = $('.wc-proceed-to-checkout'),
                    instant_checkout_button = '<button type="button" class="payer_instant_checkout button" id="payer_instant_checkout">Instant Checkout with MasterPass</button>';
                checkout_button.after( instant_checkout_button );
            } else if ( 'product' === page_type ) {
                var checkout_button = $('form.cart'),
                    instant_checkout_button = '<button type="button" class="payer_instant_checkout button" id="payer_instant_checkout">Instant Checkout with MasterPass</button>';
                checkout_button.after( instant_checkout_button );
            }
        },

        maybeHideButton: function() {
            if ( $('.product-type-variable')[0] && $('.variation_id').val() === '0' ) {
                // Product is a variable product and variable is not set, hide button.
                $('#payer_instant_checkout').hide();
            } else if( $('.variation_id').val() === '' ) {
                $('#payer_instant_checkout').hide();
            } else {
                $('#payer_instant_checkout').show();
            }
        },
        
        getProductIdFromProductPage: function() {
            var product_id      = '',
                variation_id    = '';
            // Get product ids if variable product else get normal product id.
            if( $(".single_add_to_cart_button").val() === '' ){
                product_id 		= $("[name=product_id]").val();
                variation_id 	= $(".variation_id").val();
                console.log( product_id );
            } else {
                product_id 		= $(".single_add_to_cart_button").val();
            }                        
            var ids = {
                'product_id' : product_id,
                'variation_id': variation_id,
            }
            return ids;
        },

        makePurchaseProductPage: function() {
            var quantity = $('[name=quantity]').val();
            ids = wc_payer_instant_checkout.getProductIdFromProductPage();
            var product_id = '';
            // Set the correct product id
            if( '' !== ids.variation_id ) {
                product_id = ids.product_id;
                variation_id = ids.variation_id;
            } else {
                product_id = ids.product_id;
                variation_id = '';
            }

            // Set AJAX data
            var data = {
                'action': 'instant_product_purchase',
                'product_id': product_id,
                'variation_id': variation_id,
                'quantity': quantity,
            }
            // Make AJAX call
            jQuery.post(payer_instant_checkout_params.instant_product_purchase, data, function (data) {
                if ( true === data.success ) {
                    window.location.replace( data.data );
                }
            });
        },

        makePurchaseCartPage: function() {
                // Set AJAX data
                    var data = {
                        'action': 'instant_cart_purchase',
                    }
             // Make AJAX call
             jQuery.post(payer_instant_checkout_params.instant_cart_purchase, data, function (data) {
                if ( true === data.success ) {
                    window.location.replace( data.data );
                }
            });
        },
        makePurchaseShopPage: function() {
            // Set AJAX data
                var data = {
                'action': 'instant_cart_purchase',
            }
            // Make AJAX call
            jQuery.post(payer_instant_checkout_params.instant_cart_purchase, data, function (data) {
                if ( true === data.success ) {
                    window.location.replace( data.data );
                }
            });
        },
    }
    var page_type = payer_instant_checkout_params.page_type;
    //wc_payer_instant_checkout.addInstantCheckoutButton();
    wc_payer_instant_checkout.maybeHideButton();
    $(document).on('change', "input[name='variation_id']", function(){
        wc_payer_instant_checkout.maybeHideButton();
    });
    $('body').on('click', '#payer_instant_checkout', function() {
        console.log( 'click' );
        if( 'product' === page_type ) {
            wc_payer_instant_checkout.makePurchaseProductPage();
        } else if ( 'cart' === page_type ) {
            wc_payer_instant_checkout.makePurchaseCartPage();            
        } else {
            wc_payer_instant_checkout.makePurchaseShopPage();
        }
    });
});