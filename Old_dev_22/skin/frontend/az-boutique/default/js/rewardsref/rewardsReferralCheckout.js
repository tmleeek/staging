var stReferralGuestCheckout = $('login:guest')
    , stReferralRegisterOnCheckout = $('login:register');

// wait for full winddow to load
Event.observe(window, 'load', function() {
    if (stReferralGuestCheckout != undefined) {
        if (stReferralGuestCheckout.checked) {
            hideReferralField();
        }
        Event.observe(stReferralGuestCheckout, 'click', function(event) {
            hideReferralField();
        });
    }
    if (stReferralRegisterOnCheckout != undefined) {
        if (stReferralRegisterOnCheckout.checked) {
            showReferralField();
        }
        Event.observe(stReferralRegisterOnCheckout, 'click', function(event) {
            showReferralField();
        });
    }
    // compatibility for OSC
    var stReferalOSCRegisterOnCheckbox = $('id_create_account');
    if (stReferalOSCRegisterOnCheckbox != undefined) {
        hideReferralField();

        Event.observe(stReferalOSCRegisterOnCheckbox, 'click', function(event) {
            var element = event.element();
            if (element.checked) {
                showReferralField();
            } else {
                hideReferralField();
            }
        });
    }
});

/**
 * Hide referral field on checkout for guests.
 */
var hideReferralField = function() {
    if (!showReferralFieldForGuests) {
        Element.hide('rewards_referral_information');
    }
}

/**
 * Show referral field on checkout.
 */
var showReferralField = function() {
    Element.show('rewards_referral_information');
}