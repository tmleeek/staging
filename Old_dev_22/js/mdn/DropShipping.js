/**
 *
 */
function dropShip()
{
    if (confirm('Are you sure ?'))
            document.getElementById('form_dropshipping').submit();
}

/**
 *
 */
function toggleSupplierPriceDiv(select, code)
{
    var divId = 'div_pricerequest_' + code;
    if (select.value == 'confirm')
        document.getElementById(divId).style.display = '';
    else
        document.getElementById(divId).style.display = 'none';
}

/**
 * 
 */
function cancelDropShip(poId)
{
    if(!confirm('Are you sure you want to cancel this drop ship ?'))
        return false;
    
    //ajax call
    var url = cancelDropShipUrl + 'po_id/' + poId;
    var request = new Ajax.Request(
        url,
        {
            method: 'GET',
            onSuccess: function onSuccess(transport)
            {
                if (!checkJsonResult(transport))
                    return false;
                
                //remove row
                var row = document.getElementById('table_action_po_' + poId).parentElement.parentElement;
                var table = row.parentElement.parentElement;
                table.deleteRow(row.rowIndex);
                
                //update drop shippable grid
                dropshippableGridJsObject.doFilter();
                if (!(typeof dropShippPendingSupplierDeliveryJsObject === 'undefined'))
                    dropShippPendingSupplierDeliveryJsObject.doFilter();
                if (!(typeof DropshippedHistoryJsObject === 'undefined'))
                    DropshippedHistoryJsObject.doFilter();
                
                updateRecordCount();
            },
            onFailure: function onFailure(transport)
            {
                alert('An error occured');
            }
        }
        );	
    
}

/**
 *
 */
function confirmDropShipRequest(poId, popIds)
{
    //set url
    var url = confirmDropShipRequestUrl + 'po_id/' + poId;
    
    //ajax call
    var request = new Ajax.Request(url,
        {
            method:'post',
            onSuccess: function onSuccess(transport)
                        {
                            if (!checkJsonResult(transport))
                                return false;
                            
                            //remove row
                            var row = document.getElementById('table_action_po_' + poId).parentElement.parentElement;
                            var table = row.parentElement.parentElement;
                            table.deleteRow(row.rowIndex);
                            
                            //update grids
                            if (!(typeof dropShippPendingSupplierDeliveryJsObject === 'undefined'))
                                dropShippPendingSupplierDeliveryJsObject.doFilter();
                            if (!(typeof dropShippPendingSupplierResponseJsObject === 'undefined'))
                                dropShippPendingSupplierResponseJsObject.doFilter();
                            
                            updateRecordCount();
                        },
            onFailure: function onAddressFailure() 
                        {
                                alert('An error occured');
                        },
            parameters: Form.serialize(document.getElementById('table_edit_po_' + poId))
        }
    );

}

/**
 * 
 */
function dropShipCron(){
    if(confirm('Are you sure ?'))
            location.href = cronUrl;
}

/**
 * 
 */
function confirmDropShipShipping(poId)
{
    //set url
    var tracking = document.getElementById('trackings[' + poId + ']').value;
    if (tracking == '')
    {
        if (!confirm('You have not filled the tracking number, do you want to continue ?'))
            return false;
    }
    var url = confirmDropShipShippingUrl + 'po_id/' + poId + '/tracking/' + tracking;
    
    //ajax call
    var request = new Ajax.Request(url,
        {
            method:'get',
            onSuccess: function onSuccess(transport)
                        {
                            if (!checkJsonResult(transport))
                                return false;
                            
                            //remove row
                            var row = document.getElementById('table_action_po_' + poId).parentElement.parentElement;
                            var table = row.parentElement.parentElement;
                            table.deleteRow(row.rowIndex);
                            
                            if (!(typeof dropShippPendingSupplierDeliveryJsObject === 'undefined'))
                                dropShippPendingSupplierDeliveryJsObject.doFilter();
                            
                            //update history
                            if (!(typeof DropshippedHistoryJsObject === 'undefined'))
                                DropshippedHistoryJsObject.doFilter();
                            
                            updateRecordCount();
                        },
            onFailure: function onAddressFailure() 
                        {
                                alert('An error occured');
                        }
        }
    );
}

/**
 * 
 */
function applyDropShippableAction(orderId)
{
    //set url
    var url = applyDropShippableActionUrl;
    
    //ajax call
    var request = new Ajax.Request(url,
        {
            method:'post',
            onSuccess: function onSuccess(transport)
                        {
                            if (!checkJsonResult(transport))
                                return false;
                            
                            //remove row
                            var row = document.getElementById('dropshippable_content_' + orderId).parentElement.parentElement;
                            var table = row.parentElement.parentElement;
                            table.deleteRow(row.rowIndex);
                            
                            //update grids
                            dropshippableGridJsObject.doFilter();
                            if (!(typeof dropShippPendingPriceResponseJsObject === 'undefined'))
                                dropShippPendingPriceResponseJsObject.doFilter();
                            if (!(typeof dropShippPendingSupplierResponseJsObject === 'undefined'))
                                dropShippPendingSupplierResponseJsObject.doFilter();
                            if (!(typeof dropShippPendingSupplierDeliveryJsObject === 'undefined'))
                                dropShippPendingSupplierDeliveryJsObject.doFilter();
                            if (!(typeof DropshippedHistoryJsObject === 'undefined'))
                                DropshippedHistoryJsObject.doFilter();
                            
                            updateRecordCount();
                        },
            onFailure: function onAddressFailure() 
                        {
                                alert('An error occured');
                        },
            parameters: Form.serialize(document.getElementById('dropshippable_content_' + orderId))
        }
    );
    
}

/**
 * 
 */
function pendingPriceResponseAction(orderId)
{
    //set url
    var url = applyPendingPriceResponseActionUrl;
    
    //ajax call
    var request = new Ajax.Request(url,
        {
            method:'post',
            onSuccess: function onSuccess(transport)
                        {
                            if (!checkJsonResult(transport))
                                return false;
                            
                            //remove row
                            var row = document.getElementById('pendingpriceresponse_content_' + orderId).parentElement.parentElement;
                            var table = row.parentElement.parentElement;
                            table.deleteRow(row.rowIndex);
                            
                            //update grids
                            dropshippableGridJsObject.doFilter();
                            if (!(typeof dropShippPendingPriceResponseJsObject === 'undefined'))
                                dropShippPendingPriceResponseJsObject.doFilter();                            
                            if (!(typeof dropShippPendingSupplierResponseJsObject === 'undefined'))
                                dropShippPendingSupplierResponseJsObject.doFilter();
                            if (!(typeof dropShippPendingSupplierDeliveryJsObject === 'undefined'))
                                dropShippPendingSupplierDeliveryJsObject.doFilter();
                            
                            updateRecordCount();

                        },
            onFailure: function onAddressFailure() 
                        {
                                alert('An error occured');
                        },
            parameters: Form.serialize(document.getElementById('pendingpriceresponse_content_' + orderId))
        }
    );
    
}

/**
 * Check json result
 */
function checkJsonResult(transport)
{
    elementValues = eval('(' + transport.responseText + ')');
    if (elementValues['success'] == false)
    {
        alert(elementValues['msg']);
        return false;
    }
    else
        return true;
}

function updateRecordCount()
{
    //define tabs
    var tabs = new Array();
    
    updateRecordCountForTab('dropshipping_tab_drop_shippable', 'dropshippableGrid');
    updateRecordCountForTab('dropshipping_tab_pending_price_response', 'dropShippPendingPriceResponse');
    updateRecordCountForTab('dropshipping_tab_pending_supplier_response', 'dropShippPendingSupplierResponse');
    updateRecordCountForTab('dropshipping_tab_pending_supplier_delivery', 'dropShippPendingSupplierDelivery');

}

function updateRecordCountForTab(tabId, gridId)
{
    if (document.getElementById(gridId))
    {
        var total = document.getElementById(gridId + '-total-count').innerHTML;
        document.getElementById(tabId  + '_count').innerHTML = total;
        
    }
}

window.setInterval("updateRecordCount()", 1000);