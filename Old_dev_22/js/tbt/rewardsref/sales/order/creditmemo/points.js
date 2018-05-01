
var originalAffiliateEarnings = 0;

var rewardsResetPointsAdjustment = resetPointsAdjustment;
var resetPointsAdjustment = function() {
    rewardsResetPointsAdjustment();
    $('affiliate_points_earned').value = originalAffiliateEarnings;
};

var cancelButton = document.getElementById('rewards_order_cancel_submit_button');
if (cancelButton != undefined) {
    rewardsOnClickHandler = cancelButton.onclick;
    cancelButton.onclick = function() {
        rewardsOnClickHandler();
        rewardsOrderCancelUrl += '&' + affiliate_points_earned.name + '=' + affiliate_points_earned.value;
        setLocation(rewardsOrderCancelUrl);
    };
}

// wait for full winddow to load
Event.observe(window, 'load', function() {
    if ($('rewards_adjust_points_table') != undefined) {
        var pointsToAjust = Element.extend($('rewards_adjust_points_table').down('tbody'));
          affiliatePointsEarned = $('affiliate_points_earned_credit_memo_row');
            pointsToAjust.insert({ bottom: affiliatePointsEarned });

            affiliatePointsEarned = $('affiliate_points_earned_row');
            pointsToAjust.insert({ bottom: affiliatePointsEarned });

        if (cancelButton != undefined) {
            enablePointsFields();
        }
    }
});
