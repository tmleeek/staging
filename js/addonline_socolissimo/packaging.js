Event.observe(window, 'load', function() {
	
	var ws = $$('select[name="container_weight_units"]');
	if(ws.length != 1) {
		return;
	}
	
	var w = ws[0];
	
//	alert('goon');
//	$(w).setValue("KILOGRAM");
//	alert('done');
	
});
