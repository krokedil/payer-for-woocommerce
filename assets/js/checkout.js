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
                button      = '<div class="payer_get_address_button button" id="payer_get_address">Get Address</div>';

            pno_field.after(button);
        },

        getAddress: function() {
            console.log('click');
        },
    }
    wc_payer_checkout.moveInputFields();
    wc_payer_checkout.addGetAddressButton();
    $( "#payer_get_address" ).click(function() { 
            wc_payer_checkout.getAddress();
    });
});