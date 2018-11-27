jQuery( function( $ ) {
    var wc_payer_checkout = {
        moveInputFields: function() {
            var pno_field           = $('#billing_pno_field'),
                post_code           = $('#billing_postcode_field'),
                customer_details    = $('div.woocommerce-billing-fields div'),
                button              = $('#payer_get_address');

            pno_field.addClass('form-row-first');
            post_code.addClass('form-row-last');
            post_code.removeClass('form-row-wide');
            post_code.before('<div id="payer_postcode_placeholder"></div>');
            customer_details.prepend(post_code);     
            customer_details.prepend(pno_field);
            post_code.after(button);      
        },

        hidePNOfield: function() {
            var pno_field   = $('#billing_pno_field');
            $(pno_field).hide();
        },

        hideAddressButton: function() {
            var button      = $('#payer_get_address');
            $(button).hide();
        },

        showPNOfield: function() {
            var pno_field   = $('#billing_pno_field');
            $(pno_field).show();
        },

        showAddressButton: function() {
            var button      = $('#payer_get_address');
            $(button).show();
        },

        resetPostCodeField: function() {
            var placeholder_div = $('#payer_postcode_placeholder'),
                post_code       = $('#billing_postcode_field');
                
            placeholder_div.before( post_code );
            placeholder_div.remove();
            post_code.removeClass('form-row-last');
            post_code.addClass('form-row-wide');

        },

        addGetAddressButton: function() {
            var post_code   = $('#billing_postcode_field'),
                button      = '<button type="button" class="payer_get_address_button button" id="payer_get_address">' + payer_checkout_params.get_address_text + '</button>';

            post_code.after(button);
        },

        getAddress: function() {
            var personal_number = $('#billing_pno').val(),
                zip_code = $('#billing_postcode').val()
                button = $('#payer_get_address'),
                response_message_field = $('#payer-get-address-response');

            response_message_field.remove();
            // Add spinner
            button.prop('disabled', true)
            button.addClass('payer_spinner');
            $.ajax({
                type: 'POST',
                url: payer_checkout_params.get_address,
                data: {
                    'action': 'get_address',
                    'personal_number': personal_number,
                    'zip_code' : zip_code,
                },
                dataType: 'json',
                success: function(data) {
                    var address_data = data.data.address_information,
                        message = data.data.message;
                    if( data.success === false ) { 
                        button.after('<div id="payer-get-address-response" class="woocommerce-error">' + message + '</div>');
                    } else {
                        button.after('<div id="payer-get-address-response" class="woocommerce-message">' + message + '</div>');
                        wc_payer_checkout.populateAddressFields( address_data );
                    }
                },
                error: function(data) {
                },
                complete: function(data) {
                    button.prop('disabled', false)
                    // Remove spinner
                    button.removeClass('payer_spinner');
                    $('body').trigger('update_checkout');
                }
            });    
        },
        populateAddressFields: function( address_data ) {
            // Set fields
            var first_name      = $('#billing_first_name'),
                last_name       = $('#billing_last_name'),
                organisation    = $('#billing_company'),
                city            = $('#billing_city'),
                post_code       = $('#billing_postcode'),
                address_1       = $('#billing_address_1'),
                address_2       = $('#billing_address_2');
            // Populate fields
            first_name.val( ( '' === address_data.organisation ? wc_payer_checkout.maskFormField( address_data.first_name ) : address_data.first_name ) );
            last_name.val( ( '' === address_data.organisation ? wc_payer_checkout.maskFormField( address_data.last_name ) : address_data.last_name ) );
            organisation.val( ( '' === address_data.organisation ? wc_payer_checkout.maskFormField( address_data.organisation ) : address_data.organisation ) );
            city.val( ( '' === address_data.organisation ? wc_payer_checkout.maskFormField( address_data.city ) : address_data.city ) );
            post_code.val( address_data.zip_code );
            address_1.val( ( '' === address_data.organisation ? wc_payer_checkout.maskFormField( address_data.address_1 ) : address_data.address_1 ) );
            address_2.val( ( '' === address_data.organisation ? wc_payer_checkout.maskFormField( address_data.address_2 ) : address_data.address_2 ) );
        },
        maskFormField: function( field ) {
            if ( field !== '' ) {
                var field_split = field.split( ' ' );
                var field_masked = new Array();
    
                $.each(field_split, function ( i, val ) {
                    if ( isNaN( val ) ) {
                        field_masked.push( val.charAt( 0 ) + Array( val.length ).join( '*' ) );
                    } else {
                        field_masked.push( '**' + val.substr( val.length - 3 ) );
                    }
                });
    
                return field_masked.join( ' ' );
            }
        },

        addBodyClass: function() {
            $('body').addClass('payer-active');
        },

        removeBodyClass: function() {
            $('body').removeClass('payer-active');
        },

        checkIfPnoIsFilled: function() {
            var payment_method = $('input[name="payment_method"]:checked').val();

            if( 'payer_direct_invoice_gateway' === payment_method ) {
                if( $('#billing_pno').val() === '' ) {
                    $('#place_order').prop('disabled', true);
                } else {
                    $('#place_order').prop('disabled', false);
                }
            } else {
                $('#place_order').prop('disabled', false);
            }
        }
    }

    $('body').on('click', '#payer_get_address', function() {
            wc_payer_checkout.getAddress();
    });
    $( document ).ready(function() {        
        if ( payer_checkout_params.locale === 'SE' && payer_checkout_params.enable_get_address === 'yes' ) {
            wc_payer_checkout.addGetAddressButton();
            wc_payer_checkout.moveInputFields();  
            wc_payer_checkout.showPNOfield();
            wc_payer_checkout.showAddressButton();
            wc_payer_checkout.addBodyClass();
            wc_payer_checkout.checkIfPnoIsFilled();
        }
    });
    $(document.body).on("change", "input[name='payment_method']", function (event) {
        if( payer_checkout_params.masterpass_campaign === '1' ) {
            $('body').trigger('update_checkout');
        }
        wc_payer_checkout.checkIfPnoIsFilled();
    });
    $('form.checkout').on('keyup', '#billing_pno', function() {
        wc_payer_checkout.checkIfPnoIsFilled();
    });
    $('body').on('updated_checkout', function() { 
        wc_payer_checkout.checkIfPnoIsFilled();
    });
});