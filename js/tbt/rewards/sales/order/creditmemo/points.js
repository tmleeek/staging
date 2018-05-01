
var isErrorShowing = false;
var originalSpendings = 0;
var originalEarnings = 0;
var customerPointsBalance = 0;

var enablePointsFields = function() {
    var labels = $$('.points_lbl');
    var link = $$('.points_adjust_lnk');
    var spinners = $$('.points_adjust');
    
    var i;
    for (i = 0; i < labels.length; i++) {
        labels[i].style.display = 'none';
    }
    for (i = 0; i < link.length; i++) {
        link[i].style.display = 'none';
    }
    for (i = 0; i < spinners.length; i++) {
        spinners[i].style.display = '';
    }
};

var checkSpendingsAgainstBalance = function() {
    var adjustedPointsSpent = parseInt($('points_spent').value, 10);
    var additionalSpendings = adjustedPointsSpent - originalSpendings;
    
    if (!isErrorShowing && additionalSpendings > customerPointsBalance) {
        isErrorShowing = true;
        disableElements('submit-button');
        new Effect.Appear('points_adjust_error', {duration: 1});
    } else if (isErrorShowing && additionalSpendings <= customerPointsBalance) {
        isErrorShowing = false;
        enableElements('submit-button');
        new Effect.Fade('points_adjust_error', {duration: 1});
    }
};

var resetPointsAdjustment = function() {
	$('points_spent').value = originalSpendings;
	$('points_earned').value = originalEarnings;
};
