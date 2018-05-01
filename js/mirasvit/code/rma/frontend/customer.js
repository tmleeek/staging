

jQuery.noConflict();
jQuery(document).ready(function($) {
    if (!$('#rma_item_template').length) {
        return;
    }
	var template = $('#rma_item_template')[0].outerHTML;
	var index = 1;
	$('#rma_item_template').hide();

    $('#rma_add_item').click(function(e) {
    	rmaAddItem();
    	return false;
    });

    var rmaAddItem = function(){
        var t = template;
        t = t.replace(new RegExp('__index__', 'g'), index);
		$('#rma_placeholder').after(t);

		$( "#rma_remove_item" + index).click(function(e) {
			rmaRemoveItem($(e.target).data("id"));
			return false;
		});
		index++;
		return false;
    }

    var rmaRemoveItem = function(index){
    	if (index == 1) {
    		return;
    	}
    	$( "#rma_item" + index).remove();
    }

    rmaAddItem();

});