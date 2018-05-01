//*********************************************************************************************************
//method to store current tab in a hidden field
function beforeSaveProduct()
{
	//Stock current tab in hidden form item
	var currentTabId = advancedstock_product_tabsJsTabs.activeTab.name;
	document.getElementById('current_tab').value = currentTabId;

	//submit form
	editForm.submit();
}

//*********************************************************************************************************************************************
//function to enable or disable field depending of checkbox state
function toggleFieldFromCheckbox(checkboxId, fieldId)
{
	var checked = document.getElementById(checkboxId).checked;
	var field = document.getElementById(fieldId);	
	if (checked)
		field.disabled = true;
	else
		field.disabled = false;
}

//*********************************************************************************************************************************************
//function to enable or disable field depending of combo state
function toggleFieldFromCombo(comboId, fieldId)
{
	var checked = document.getElementById(comboId).value;
	var field = document.getElementById(fieldId);	
	if (checked == 1)
		field.disabled = true;
	else
		field.disabled = false;
	
}

//*********************************************************************************************************
//
function refreshPrices(from)
{

	//calcul
	var price_ttc = 0;
	var price = 0;
	var margin_percent = 0;
	var cost = 0;
	var taxCoef = 1 + (taxRate / 100);

	cost = document.getElementById('cost').value;
	switch (from)
	{
		case 'margin':
			margin_percent = parseFloat(document.getElementById('margin_percent').value);
			if (cost == 0)
			{
				alert('Unable to compute values as buy price is not defined !');
				return;
			}
			price = cost / (1 - margin_percent / 100);
			price_ttc = Math.round(price * taxCoef * 100) / 100;
			break;
		case 'price':
			price = document.getElementById('price').value;
			price_ttc = Math.round(price * taxCoef * 100) / 100;
			if (cost > 0)
				margin_percent = (price - cost) / price * 100;
			break;
		case 'price_ttc':
			price_ttc = document.getElementById('price_ttc').value;
			price = price_ttc / taxCoef;
			if (cost > 0)
				margin_percent = (price - cost) / price * 100;
			break;
	}
	
	//affiche
	document.getElementById('margin_percent').value = margin_percent.toFixed(3);
	document.getElementById('price').value = price;
	document.getElementById('price_ttc').value = price_ttc;
	
}

//****************************************************************************************************************
//print barcode labels
function printLabels()
{
    var url = printLabelUrl;
    var qty = document.getElementById('label_count').value;
    if(qty>0){
        url += 'qty/' + qty;
        document.location.href = url;
    }else{
        alert('Please set a quantity to print > 0');
    }

}

//****************************************************************************************************************
//auto calculate prefered stock level
function autoCalculatePreferedStockLevel(productId)
{
	alert('ok');
	//var url = autoCalculatePreferedStockLevelUrl + 'product_id/' + productId;
	//alert(url);
}