/**
 * Helps
 *
 * To modify the text of the node see the function getTextNodeData
 * To modify the nodes order see the function getNodeDataArray
 * To modify the style of the nodes seed the function debugRender
 */

function init()
{

    // Create the data node from the debug
    var nodeDataArray = getNodeDataArray(pricer.debug);

    // Call the debug renderer with the node data
    debugRenderer('debug', nodeDataArray);

    /**
     * Create the html debug
     *
     * @param elementId
     * @param nodeDataArray
     */
    function debugRenderer(elementId, nodeDataArray)
    {
        var costContainer = document.createElement('div');
        costContainer.setAttribute('class', 'pricer_container pricer_container_node_cost');

        var behaviorContainer = document.createElement('div');
        behaviorContainer.setAttribute('class', 'pricer_container pricer_container_node_behavior');

        var endContainer = document.createElement('div');
        endContainer.setAttribute('class', 'pricer_container pricer_container_node_end');

        for (var i = 0; i < nodeDataArray.length; i++) {
            nodeData = nodeDataArray[i];

            var nodeElement = document.createElement('div');
            nodeElement.setAttribute('class', 'pricer_node pricer_' + nodeData.category);

            var titleElement = document.createElement('h4');
            titleElement.innerHTML = nodeData.title;

            var contentElement = document.createElement('div');
            contentElement.innerHTML = nodeData.text;

            nodeElement.appendChild(titleElement);
            nodeElement.appendChild(contentElement);

            switch(nodeData.category) {
                case 'cost':
                    costContainer.appendChild(nodeElement);
                    break;
                case 'final_cost':
                    costContainer.appendChild(getClearElement());
                    costContainer.appendChild(nodeElement);
                    break;
                case 'target':
                    behaviorContainer.appendChild(getClearElement());
                    behaviorContainer.appendChild(nodeElement);
                    break;
                case 'behavior':
                    behaviorContainer.appendChild(nodeElement);
                    break;
                case 'final_price':
                    endContainer.appendChild(nodeElement);
                    break;
            }
        }

        costContainer.appendChild(getClearElement());
        behaviorContainer.appendChild(getClearElement());

        var debugElement = document.getElementById(elementId);
        debugElement.appendChild(costContainer);
        debugElement.appendChild(getSeparateElement());
        debugElement.appendChild(behaviorContainer);
        debugElement.appendChild(getSeparateElement());
        debugElement.appendChild(endContainer);
    }

    function getClearElement()
    {
        var clearElement = document.createElement('div');
        clearElement.setAttribute('class', 'clear');

        return clearElement;
    }

    /**
     * Create an arrow element (png)
     *
     * @returns {Element}
     */
    function getSeparateElement()
    {
        var separateElement = document.createElement('div');
        separateElement.setAttribute('class', 'pricer_next');

        return separateElement;
    }

    /**
     * Extract the node from the debug
     *
     * @param debugJson
     * @returns {Array}
     */
    function getNodeDataArray(debugJson)
    {
        var debug = JSON.parse(debugJson);
        var nodeDataArray = [];
        var currency = debug.currency.currency;

        // Cost results
        var finalCost = 0;
        if (debug.cost) {
            finalCost += parseFloat(debug.cost.result);
            nodeDataArray.push(getNodeData(debug.cost, currency));
        }

        if (debug.cost_shipping) {
            finalCost += parseFloat(debug.cost_shipping.result);
            nodeDataArray.push(getNodeData(debug.cost_shipping, currency));
        }

        if (debug.additional_cost) {
            for (var i = 0; i < debug.additional_cost.length; i++) {
                var node = debug.additional_cost[i];
                finalCost += parseFloat(node.result);
                nodeDataArray.push(getNodeData(node, currency));
            }
        }
        if (debug.commission) {
            finalCost += parseFloat(debug.commission.amount);
            nodeDataArray.push(getNodeData(debug.commission, currency));
        }

        if (debug.tax_rate) {
            finalCost += parseFloat(debug.tax_rate.amount);
            nodeDataArray.push(getNodeData(debug.tax_rate, currency));
        }

        nodeDataArray.push({
            category: 'final_cost',
            title: 'FINAL COST',
            text: 'Cost' + ' ' + Math.round(finalCost * 100) / 100
        });

        // Behavior results
        if (debug.behavior) {
            nodeDataArray.push(getNodeData(debug.behavior, currency));
        }
        if(debug.price_without_competitor && debug.price_without_competitor.result == false) {
            if (debug.margin) nodeDataArray.push(getNodeData(debug.margin, currency));
            if (debug.adjustment) nodeDataArray.push(getNodeData(debug.adjustment, currency));
        }
        if (debug.price_without_competitor && debug.price_without_competitor.result != false) {
            nodeDataArray.push(getNodeData(debug.price_without_competitor, currency));
        }

        if(debug.bbw && debug.bbw.price !== false) {
            nodeDataArray.push({
                category: 'target',
                title: 'TARGET',
                text: 'Name' + ': ' + debug.bbw.name + '<br />' +
                'Price' + ': ' + debug.bbw.price
            });
        } else {
            nodeDataArray.push({
                category: 'target',
                title: 'TARGET',
                text: 'No competitor'
            });
        }

        if (debug.min_price) {
            nodeDataArray.push(getNodeData(debug.min_price, currency));
        }
        if (debug.max_price) {
            nodeDataArray.push(getNodeData(debug.max_price, currency));
        }

        if (debug.adjust_margin) {
            debug.adjust_margin['type'] = 'ADJUST_MARGIN';
            nodeDataArray.push(getNodeData(debug.adjust_margin, currency));
        }

        if (debug.error) {
            nodeDataArray.push({
                category: 'error',
                title: 'Error',
                text: debug.error.message
            });
        } else {
            nodeDataArray.push({
                category: 'final_price',
                title: 'FINAL PRICE (' + debug.end_pricing.datetime + ')',
                text: 'Price' + ': ' + debug.end_pricing.price + ' ' + currency + '<br />' +
                'Price without shipping' + ': ' + (debug.end_pricing.price - debug.end_pricing.shipping_price) + ' ' + currency + '<br />' +
                'Final price to save' + ': ' + debug.end_pricing.final_price + ' ' + currency + '<br />' +
                'Marge' + ': ' + debug.end_pricing.final_margin + '%<br />' +
                'Net margin' + ': ' + debug.end_pricing.margin_amount + ' ' + currency + '<br/>' +
                'Status' + ': ' + debug.end_pricing.status + '<br/>' +
                'Rank' + ': ' + debug.end_pricing.rank
            });
        }

        return nodeDataArray;
    }

    function getNodeData(node, currency)
    {
        return {
            category: getCategoryNode(node.type),
            title: getTitleNode(node),
            text: getTextNode(node, currency)
        }
    }

    function getCategoryNode(type)
    {
        switch(type) {
            case 'COST':
            case 'COST_SHIPPING':
            case 'ADDITIONAL_COST':
            case 'COMMISSION':
            case 'TAX_RATE':
                return 'cost';
            case 'BEHAVIOR':
            case 'MIN_PRICE':
            case 'MAX_PRICE':
            case 'PRICE_WITHOUT_COMPETITOR':
            case 'MARGIN':
            case 'ADJUSTMENT':
                return 'behavior';
            case 'ERROR':
                return 'error';
            case 'END_PRICING':
                return 'end';
        }
    }

    function getTitleNode(node)
    {

        var ruleUrl = pricer.rule_url.replace(/(\[rule_id\])/, node.id);

        return '<span class="small">[' + node.type + ']</span> ' +
            (node.id
                    ? ' <a target="_blank" href="' + ruleUrl + '" >' + node.name + '</a>'
                    : ''
            );
    }

    /**
     * Create the text of the nodeData
     *
     * @param values
     * @param currency
     * @returns {string}
     */
    function getTextNode(values, currency)
    {
        var text = '';
        switch (values.type) {
            case 'COST':
                text = 'Cost' + ': ' + values.result + ' ' + currency + '<br />' +
                    'Formula' + ': <span class="small">' + values.formula + '</span>'
                break;
            case 'COST_SHIPPING':
                text = 'Cost' + ': ' + values.result + ' ' + currency + '<br />' +
                    'Weight' + ': ' + values.weight;
                break;
            case 'ADDITIONAL_COST':
                text = 'Cost' + ': ' + values.result+ ' ' + currency + '<br />' +
                    'Formula' + ': <span class="small">' + values.formula + '</span>'
                break;
            case 'COMMISSION':
                text = 'Percent' + ': ' + values.result + '%<br />' +
                    'Cost' + ': ' + values.amount + ' ' + currency;
                break;
            case 'TAX_RATE':
                text = 'Percent' + ': ' + values.result + "%<br />" +
                    'Cost' + ': ' + values.amount + ' ' + currency + '<br />' +
                    'Formula' + ': <span class="small">' + values.formula + '</span>'
                break;
            case 'BEHAVIOR':
                text = 'Behavior' + ': ' + values.result;
                break;
            case 'MARGIN':
                text = 'Percent' + ': ' + values.result + '%';
                break;
            case 'ADJUSTMENT':
                text = 'Price' + ': ' + values.result + ' ' + currency + "<br />" +
                    'Method' + ':' + values.method + '<br />' +
                    'Formula' + ': <span class="small">' + values.formula + '</span>'
                break;
            case 'PRICE_WITHOUT_COMPETITOR':
                if(values.result.margin) {
                    text = 'Percent' + ': ' + values.result.margin + '%';
                } else {
                    text = 'Price' + ': ' + values.result.value + currency + '<br />' +
                        'Formula' + ': <span class="small">' + values.formula + '</span>'
                }
                break;
            case 'MIN_PRICE':
                text = 'Price' + ': ' + values.result + ' ' + currency + '<br />' +
                    'Formula' + ': <span class="small">' + values.formula + '</span>'
                break;
            case 'MAX_PRICE':
                text = 'Price' + ': ' + values.result + (values.mode == 'value' ? currency : '%') + '<br />' +
                    'Formula' + ': <span class="small">' + values.formula + '</span>'
                break;
            case 'ADJUST_MARGIN':
                text = 'Before adjustment' + ': ' + values.before + '% (' + values.price_before + ' ' + currency + ')' + "<br />" +
                    'After adjustment' + ': ' + values.after + '% (' + values.price_after + ' ' + currency + ')'
                break;
        }

        return text;
    }
}
