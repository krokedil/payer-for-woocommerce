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
            var personal_number = $('#billing_pno').val();

            var data = {
                'action': 'get_address',
                'personal_number': personal_number,
            }
            jQuery.post(payer_checkout_params.get_adress, data, function (data) {
                if (true === data.success) {
                    console.log(data);
                }
            });
        },
    }
    wc_payer_checkout.moveInputFields();
    if ( payer_checkout_params.locale === 'SE' ) {
        wc_payer_checkout.addGetAddressButton();
    }
    $( "#payer_get_address" ).click(function() { 
            wc_payer_checkout.getAddress();
    });
});