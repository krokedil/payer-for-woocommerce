jQuery( function( $ ) {
    var wc_payer_checkout = {
        moveInputFields: function() {
            var pno_field   = $('#billing_pno_field'),
                post_code   = $('#billing_postcode_field'),
                form        = $('form[name=checkout]');
            
            form.prepend(pno_field);  
            form.prepend(post_code);          
        },

        addGetAddressButton: function() {
            var pno_field   = $('#billing_pno_field'),
                button      = '<button type="button" class="payer_get_address_button button" id="payer_get_address">Get Address</button>';

            pno_field.after(button);
        },

        getAddress: function() {
            var personal_number = $('#billing_pno').val(),
                zip_code = $('#billing_postcode').val();
            // Set AJAX data
            var data = {
                'action': 'get_address',
                'personal_number': personal_number,
                'zip_code' : zip_code,
            }
            // Make AJAX call
            jQuery.post(payer_checkout_params.get_address, data, function (data) {
                if (true === data.success) {
                    var address_data = data.data;
                    wc_payer_checkout.populateAddressFields( address_data );
                }
            });
        },
        populateAddressFields: function( address_data ) {
            // Set fields
            var first_name      = $('#billing_first_name'),
                last_name       = $('#billing_last_name'),
                organisation    = $('#billing_company'),
                city            = $('#billing_city'),
                //zip_code        = $('#billing_postcode'),
                address_1       = $('#billing_address_1'),
                address_2       = $('#billing_address_2');

            // Populate fields - Needs to be masked.
            first_name.val( wc_payer_checkout.maskFormField( address_data.first_name ) ).prop( 'readonly', true );
            last_name.val( wc_payer_checkout.maskFormField( address_data.last_name ) ).prop( 'readonly', true );
            organisation.val( wc_payer_checkout.maskFormField( address_data.organisation ) ).prop( 'readonly', true );
            city.val( wc_payer_checkout.maskFormField( address_data.city ) ).prop( 'readonly', true );
            //zip_code.val( wc_payer_checkout.maskFormField( address_data.zip_code ) ).prop( 'readonly', true );
            address_1.val( wc_payer_checkout.maskFormField( address_data.address_1 ) ).prop( 'readonly', true );
            address_2.val( wc_payer_checkout.maskFormField( address_data.address_2 ) ).prop( 'readonly', true );
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
    }
    if ( payer_checkout_params.locale === 'SE' ) {
        wc_payer_checkout.moveInputFields();        
        wc_payer_checkout.addGetAddressButton();
    }
    $( "#payer_get_address" ).click(function() { 
            wc_payer_checkout.getAddress();
    });
});