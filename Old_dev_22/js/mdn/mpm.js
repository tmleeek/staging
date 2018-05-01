function toggleItemVisibility(checkbox, target)
{
    target = document.getElementById(target);
    if (target)
        target.style.display = (checkbox.checked ? 'none' : '');
}

function toggleDiv(divId)
{
    var elt = document.getElementById(divId);
    elt.style.display = (elt.style.display == 'none' ? '' : 'none');
}

function updateMatchingUrls(productId)
{
    var url = MPM_URL_MATCHING_URLS;
    var urls = document.getElementById('matching_urls').value;
    url = url.replace('#product_id#', productId.replace('#', '[sharp]').replace('/', '[slash]').replace('+', '[plus]'));
    url = url.replace('#urls#', urls.replace(/#/g, '[sharp]').replace(/\//g, '[slash]').replace(/\+/g, '[plus]').replace(/\n/g, ',').replace('?', '[interrogation]').replace('&', '[esperluette]'));

    new Ajax.Request(url,
        {
            method:'get',
            onSuccess: function onSuccess(transport)
            {
                document.getElementById('mpm-success').style.display = 'block';
                setTimeout(function() {
                    document.getElementById('mpm-success').style.display = 'none';
                }, 3000);
                updateProductData(productId, 'custom_nc_default', 'behavior', parent.document.getElementById('behavior_'+productId).value, true);
            },
            onFailure: function onAddressFailure()
            {
                alert('An error occured');
            }
        }
    );

}

function updateProductData(productId, channel, field, value, forceAction)
{
    forceAction = typeof forceAction !== 'undefined' ? forceAction : false;
    var url = MPM_URL_UPDATE_PRODUCT;
    url = url.replace('#product_id#', productId.replace('#', '[sharp]').replace('/', '[slash]').replace('+', '[plus]'));
    url = url.replace('#channel#', channel);
    url = url.replace('#field#', field);
    url = url.replace('#value#', value);

    if ( !forceAction && (field == 'behavior') && (value == 'harakiri') && !confirm('Do you really want to apply AGGRESSIVE behaviour ?'))
    {
        return false;
    }

    var behaviourCheckBox = document.getElementById('mpm['+ channel +'][behavior][' + value +']');
    if(behaviourCheckBox !== null){
        behaviourCheckBox.checked = true;
    }


    var request = new Ajax.Request(url,
        {
            method:'get',
            onSuccess: function onSuccess(transport)
            {
                elementValues = eval('(' + transport.responseText + ')');

                refreshRowCells(elementValues)

            },
            onFailure: function onAddressFailure()
            {
                alert('An error occured');
            }
        }
    );
}

function refreshRowCells(elementValues)
{
    var cellPrefix = 'cell_' + elementValues['product_id'] + '_' + elementValues['channel'] + '_';

    if(document.getElementById(cellPrefix + 'final_price')) {
        finalPrice = document.getElementById(cellPrefix + 'final_price');
        finalMargin = document.getElementById(cellPrefix + 'final_margin');
        targetPosition = document.getElementById(cellPrefix + 'target_position');
        status = document.getElementById(cellPrefix + 'status');
        matchingstatus = document.getElementById(cellPrefix + 'matching_status');
    } else {
        finalPrice = parent.document.getElementById(cellPrefix + 'final_price');
        finalMargin = parent.document.getElementById(cellPrefix + 'final_margin');
        targetPosition = parent.document.getElementById(cellPrefix + 'target_position');
        status = parent.document.getElementById(cellPrefix + 'status');
        matchingstatus = parent.document.getElementById(cellPrefix + 'matching_status');
    }

    var regexCurrency = /(.)+ [0-9.]+/;
    var matches;
    var currency = "";
    if ((matches = regexCurrency.exec(finalPrice.innerHTML)) !== null) {
        if (matches.index === regexCurrency.lastIndex) {
            regexCurrency.lastIndex++;
        }
        currency = matches[1];
    }
    
    finalPrice.innerHTML = currency + " " + elementValues['final_price'];
    finalMargin.innerHTML = elementValues['margin'];
    targetPosition.innerHTML = elementValues['target_position'];
    status.innerHTML = elementValues['status_img'];
    status.innerHTML = elementValues['status_img'];
    matchingstatus.innerHTML = elementValues['matching_status'];
    document.dispatchEvent(repriceDoneEvent);
}

function openMyPopup(url, title, callback) {


    if ($('browser_window') && typeof(Windows) != 'undefined') {
            Windows.focus('browser_window');
            return;
        }

    if (!callback)
    {
        callback = function (param, el) {

        };
    }

    //stupid hack to support slash in sku
    //url = url.replace('[slash]', '%2F');
    url = url.replace('[sharp]', '%23');

    var dialogWindow = Dialog.info(null, {
        closable:true,
        resizable:true,
        draggable:true,
        className:'magento',
        windowClassName:'popup-window',
        title: title,
        top:50,
        width:1100,
        height:600,
        zIndex:1000,
        recenterAuto:false,
        hideEffect:Element.hide,
        showEffect:Element.show,
        id:'browser_window',
        url:url,
        onClose:callback
    });
}


var lastHighlightedDiv = false;

function highLightChannel(div)
{
    if (lastHighlightedDiv)
    {
        lastHighlightedDiv.style.border="0px solid red";
    }

    div.style.border="2px solid red";
    lastHighlightedDiv = div;
}


function decorateCurrentBehaviour(channel, productId)
{
    var items = document.getElementsByName('mpm[' + channel + '][' + productId + '][behavior]')
    var currentBehaviour = null;
    for(i=0;i<items.length;i++) {
        if (items[i].checked)
            currentBehaviour = items[i].value;
    }

    document.getElementById('row_' + channel + '_' + productId + '_behaviour_normal').removeClassName('current_behaviour');
    document.getElementById('row_' + channel + '_' + productId + '_behaviour_aggressive').removeClassName('current_behaviour');
    document.getElementById('row_' + channel + '_' + productId + '_behaviour_harakiri').removeClassName('current_behaviour');
    document.getElementById('row_' + channel + '_' + productId + '_behaviour_' + currentBehaviour).addClassName('current_behaviour');
}

function deletePerimeterRow(rowId)
{
    var table = document.getElementById('perimeterConditionsTable');
    var rowCount = table.rows.length;
    for(var i=0; i<rowCount; i++)
    {
        var row = table.rows[i];
        if (row.id == 'perimeterConditionsRow' + rowId) {
            table.deleteRow(i);
            return;
        }
    }
}

function deleteConditionRow(rowId)
{
    var table = document.getElementById('conditionConditionsTable');
    var rowCount = table.rows.length;
    for(var i=0; i<rowCount; i++)
    {
        var row = table.rows[i];
        if (row.id == 'conditionConditionsRow' + rowId) {
            table.deleteRow(i);
            return;
        }
    }
}

function addGrid()
{
    var gridName = document.getElementById('grid_name').value;
    if(gridName.length < 5){
        alert(translate("Grid name must have at least %s caracters").replace('%s', 5));
        return false;
    }
    var tmpId = new Date().getTime();
    var section =
    '<div class="section-config">' +
        '<div class="entry-edit-head collapseable">' +
            '<a id="grid_'+tmpId+'-head" href="#" onclick="Fieldset.toggleCollapse(\'grid_'+tmpId+'\', \'/magento/index.php/admin/system_config/state/\'); return false;">'+gridName+'</a>' +
        '</div>' +
        '<input id="grid_'+tmpId+'-state" name="config_state[grid_'+tmpId+']" type="hidden" value="0" />' +
        '<fieldset class="config collapseable" id="grid_'+tmpId+'">' +
            '<div class="grid">' +
                '<table id="'+tmpId+'" cellspacing="0" width="100%" role="shippingGrid" class="data" >' +
                    '<thead><tr class="headings">' +
                        '<th bgcolor="#ddd" align="center"><b>'+translate('Weight')+'</b></th>' +
                        '<th bgcolor="#ddd" align="center"><b>'+translate('Cost')+ ' (' + clientCurrency + ')</b></th>' +
                        '<th bgcolor="#ddd" align="center"><b>'+translate('Action')+ '</b></th>' +
                    '</tr></thead><tbody></tbody>' +
                '</table>' +
                '<br/><span class="form-button right" onclick="addGridRow(\''+gridName+'\', \''+tmpId+'\')">'+translate('Add row')+'</span>' +
            '</div>' +
        '</fieldset>' +
    '</div>' +
    '<script type="text/javascript">Fieldset.applyCollapse(\'grid_'+tmpId+'\');</script>';

    document.getElementById('manage_shipping').innerHTML += section;
}

function addGridRow(gridName, gridTable)
{
    var tmpId = new Date().getTime();
    var nbRow = document.getElementById(gridTable).querySelectorAll('tbody tr').length + 1 ;
    var rowClass = "pointer";
    if(nbRow%2){
        rowClass += " even"
    }
    var row =
    '<tr id="grid_row_'+tmpId+'" class="'+ rowClass+'">' +
        '<input type="hidden" style="width:90%" name="groups[shipping][fields][grid_'+gridTable+'][name_'+tmpId+'][value]" value="'+gridName+'">' +
        '<td align="center">' +
            '<input type="text" style="width:90%" name="groups[shipping][fields][grid_'+gridTable+'][weight_'+tmpId+'][value]" value="">' +
        '</td>' +
        '<td align="center">' +
            '<input type="text" style="width:90%" name="groups[shipping][fields][grid_'+gridTable+'][price_'+tmpId+'][value]" value="">' +
        '</td>' +
        '<td align="center">' +
            '<a  onclick="$(\'grid_row_'+tmpId+'\').remove();">' + translate('Delete') + '</a>' +
        '</td>' +
    '</tr>';

    document.getElementById(gridTable).querySelector('tbody').insertAdjacentHTML('beforeend', row);
}

function submitShippingForm(gridTable){
    var grids = document.querySelectorAll('[role="shippingGrid"]');
    for (var i = 0; i < grids.length; i++) {
        if( !couldSubmitForm(grids[i].id))
            return false;

    }
    document.getElementById('form1').submit();
}

function couldSubmitForm(gridTable){
    rows = document.getElementById(gridTable).querySelectorAll('tbody tr');
    var couldSubmit = true;
    var hasEmptyField = false;
    for (var i = 1; i < rows.length; i++) {
        rowsInputs = rows[i].querySelectorAll('input');
        var isEmpty = true;

        for (var j = 1; j < rowsInputs.length; j++) {
            if(rowsInputs[j].value != ""){
                isEmpty = false;
                if(checkShippingGridValue(rowsInputs[j].name, rowsInputs[j].value) === false){
                    couldSubmit = false;
                    rowsInputs[j].style.border="1px solid red";
                }
            }else{

                hasEmptyField = true;
                rowsInputs[j].style.border="1px solid red";
            }
        }
        if(isEmpty){
            $(rows[i]).remove();
        }else{
            if(hasEmptyField){
                couldSubmit = false;
            }
        }
    }
    return couldSubmit;
}

function checkShippingGridValue(type, val){
    var re = /\[([a-zA-Z]+)_[0-9a-zA-Z]+\]\[value\]/;
    var m;

    if ((m = re.exec(type)) !== null) {
        if (m.index === re.lastIndex) {
            re.lastIndex++;
        }
        type = m[1];
    }
    var check;
    switch(type){
        case "number" :c
        case "weight"  :
        case "price"  :
            check = !isNaN(val);
            break;
        default :
            check = true;

    }
    return check;
}

function translate(text) {
    try {
        if(Translator){
            return Translator.translate(text);
        }
    }
    catch(e){}
    return text;
}

function productGridAction(select)
{
    var action = select.value;

    switch(action){

        case "edit" :
            var url = select.querySelector('[value="'+ select.value +'"]' ).getAttribute('data-url');
            window.location.href = url;
            break;
        case "reprice" :
            var behaviourSelect = select.parentNode.parentNode.querySelector('.behaviour');
            var methodToCall = behaviourSelect.getAttribute('onchange').replace('this.value', '"'+ behaviourSelect.value + '",true');
            eval(methodToCall);
            break;
        default :
            console.log('Action not found');
    }
    select.selectedIndex = 0;
}



var massActionChangeBehaviourmethodsToCall = [];


window.addEventListener("load", function(){
    if(typeof MpmProducts_massactionJsObject !== "undefined"){
        MpmProducts_massactionJsObject.apply = function(){

            var items = MpmProducts_massactionJsObject.checkedString;
            if(items.length === 0){
                alert(translate('No rows selected'))
                return;
            }
            var massActionForm = MpmProducts_massactionJsObject.form;
            var massAction = massActionForm.querySelector('#MpmProducts_massaction-select').value;

            if(massAction === 'change_behaviour' ){
                var massActionValue = massActionForm.querySelector('[name="behaviours"]').value;
                items = items.split(",");

                for (var i=0; i<items.length; i++) {
                    var item = items[i];
                    var behaviourSelect = document.querySelector('#MpmProducts .grid input[name="product"][value="'+item+'"]')
                        .parentNode.parentNode.querySelector('.behaviour');
                    behaviourSelect.selectedIndex= massActionForm.querySelector('[name="behaviours"]').selectedIndex;
                    behaviourSelect.parentNode.parentNode.style.opacity = 0.3;
                    var action = behaviourSelect.getAttribute("onchange");
                    massActionChangeBehaviourmethodsToCall.push( action.replace('this.value', '"'+ massActionValue + '",true'));

                }
                raiseMassActionChangeBehaviour();

            }

        };

    }


});


var repriceDoneEvent = new Event('hasRepriceProduct');
var nbEventRepricePending = 1;

function raiseMassActionChangeBehaviour()
{
    nbEventRepricePending--;
    for(var i=nbEventRepricePending; i<20;i++){
        var action = massActionChangeBehaviourmethodsToCall.pop();
        if( "undefined" !== action){
            nbEventRepricePending++;
            eval(action);
        }else{
            nbEventRepricePending = 1;
            break;
        }
    }

}

document.addEventListener('hasRepriceProduct', function (e) {
    raiseMassActionChangeBehaviour();
}, false);


/**
 * Abstract class for rule condition management
 *
 * @type {{
 *          type: string,
 *          list: string,
 *          container: string,
 *          url: string,
 *          show: RuleCondition.show,
 *          add: RuleCondition.add,
 *          hide: RuleCondition.hide,
 *          remove: RuleCondition.remove
 *      }}
 */
RuleCondition = {

    /**
     * Type of the condition (Condition or Perimeter)
     * @string
     */
    type: '',
    /**
     * Id of the select which contain available conditions
     * @string
     */
    list: '',
    /**
     * Id of the div in which new condition is added
     * @string
     */
    container: '',
    /**
     * url to call in order to get the condition html widget
     * @string
     */
    url: '',

    /**
     * Show select in order to add new condition
     *
     * @param elt
     */
    show: function(elt){

        jQuery('#'+this.list).show();
        elt.setAttribute('class', 'scalable delete');
        elt.innerHTML = "<span>Cance</span>";
        elt.setAttribute('onclick', this.type+'.hide(this)');

    },
    /**
     * Add new condition field in form. Ajax call in order to get html widget
     *
     * @param type
     */
    add: function(type){
        if(type != '0' && !document.getElementById(type)){

            var url = this.url+'?field='+type;
            var container = this.container;
            var request = new Ajax.Request(
                url,
                {
                    method: 'get',
                    onSuccess: function(transport){

                        jQuery('#'+container).append(transport.responseText);

                    },
                    onFailure: function(transport){
                        alert(transport.responseText);
                    }
                }
            )

        }
    },
    /**
     * Hide conditions select
     *
     * @param elt
     */
    hide: function(elt){

        jQuery('#'+this.list).hide();
        elt.setAttribute('class', 'scalable add');
        elt.innerHTML = "<span>Add new condition</span>";
        elt.setAttribute('onclick', this.type+'.show(this)');

    },
    /**
     * Remove a condition
     *
     * @param id
     */
    remove: function (id){

        document.getElementById(id).remove();

    }
};

/**
 * Class for offer condition management
 *
 * @type {RuleCondition}
 */
ConditionClass = Class.create(RuleCondition, {
    initialize: function(url) {
        this.type = 'Condition';
        this.list = 'condition-list';
        this.container = 'rule-conditions';
        this.url = url;
    }
});

/**
 * Class for product condition management
 *
 * @type {RuleCondition}
 */
PerimeterClass = Class.create(RuleCondition, {
    initialize: function(url){
        this.type = 'Perimeter';
        this.list = 'perimeter-list';
        this.container = 'product-conditions';
        this.url = url;
    }
});
