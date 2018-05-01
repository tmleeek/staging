CommonAmazonListingGridHandler = Class.create(CommonListingGridHandler, {

    // ---------------------------------------

    getComponent: function()
    {
        return 'amazon';
    },

    // ---------------------------------------

    getMaxProductsInPart: function()
    {
        return 10;
    },

    // ---------------------------------------

    prepareActions: function($super)
    {
        $super();
        this.movingHandler = new ListingMovingHandler(this);
        this.actionHandler = new CommonAmazonListingActionHandler(this);
        this.productSearchHandler = new CommonAmazonListingProductSearchHandler(this);
        this.templateDescriptionHandler = new CommonAmazonListingTemplateDescriptionHandler(this);
        this.templateShippingHandler = new CommonAmazonListingTemplateShippingHandler(this);
        this.templateProductTaxCodeHandler = new CommonAmazonListingTemplateProductTaxCodeHandler(this);
        this.variationProductManageHandler = new CommonAmazonListingVariationProductManageHandler(this);
        this.fulfillmentHandler = new CommonAmazonFulfillmentHandler(this);
        this.repricingHandler = new CommonAmazonRepricingHandler(this);

        this.actions = Object.extend(this.actions, {

            movingAction: this.movingHandler.run.bind(this.movingHandler),
            deleteAndRemoveAction: this.actionHandler.deleteAndRemoveAction.bind(this.actionHandler),

            assignTemplateDescriptionIdAction: (function(id) {
                id = id || this.getSelectedProductsString();
                this.templateDescriptionHandler.validateProductsForTemplateDescriptionAssign(id)
            }).bind(this),
            unassignTemplateDescriptionIdAction: (function(id) {
                id = id || this.getSelectedProductsString();
                this.templateDescriptionHandler.unassignFromTemplateDescription(id)
            }).bind(this),

            assignTemplateShippingTemplateIdAction: (function(id) {
                id = id || this.getSelectedProductsString();
                this.templateShippingHandler.openPopUp(id, M2ePro.php.constant('Ess_M2ePro_Model_Amazon_Account::SHIPPING_MODE_TEMPLATE'))
            }).bind(this),
            unassignTemplateShippingTemplateIdAction: (function(id) {
                id = id || this.getSelectedProductsString();
                this.templateShippingHandler.unassign(id, M2ePro.php.constant('Ess_M2ePro_Model_Amazon_Account::SHIPPING_MODE_TEMPLATE'))
            }).bind(this),

            assignTemplateShippingOverrideIdAction: (function(id) {
                id = id || this.getSelectedProductsString();
                this.templateShippingHandler.openPopUp(id, M2ePro.php.constant('Ess_M2ePro_Model_Amazon_Account::SHIPPING_MODE_OVERRIDE'))
            }).bind(this),
            unassignTemplateShippingOverrideIdAction: (function(id) {
                id = id || this.getSelectedProductsString();
                this.templateShippingHandler.unassign(id, M2ePro.php.constant('Ess_M2ePro_Model_Amazon_Account::SHIPPING_MODE_OVERRIDE'))
            }).bind(this),

            assignTemplateProductTaxCodeIdAction: (function(id) {
                id = id || this.getSelectedProductsString();
                this.templateProductTaxCodeHandler.openPopUp(id)
            }).bind(this),
            unassignTemplateProductTaxCodeIdAction: (function(id) {
                id = id || this.getSelectedProductsString();
                this.templateProductTaxCodeHandler.unassign(id)
            }).bind(this),

            switchToAfnAction: (function(id) {
                id = id || this.getSelectedProductsString();
                this.fulfillmentHandler.switchToAFN(id);
            }).bind(this),
            switchToMfnAction: (function(id) {
                id = id || this.getSelectedProductsString();
                this.fulfillmentHandler.switchToMFN(id);
            }).bind(this),

            addToRepricingAction: (function(id) {
                id = id || this.getSelectedProductsString();
                this.repricingHandler.addToRepricing(id);
            }).bind(this),
            showDetailsAction: (function(id) {
                id = id || this.getSelectedProductsString();
                this.repricingHandler.showDetails(id);
            }).bind(this),
            editRepricingAction: (function(id) {
                id = id || this.getSelectedProductsString();
                this.repricingHandler.editRepricing(id);
            }).bind(this),
            removeFromRepricingAction: (function(id) {
                id = id || this.getSelectedProductsString();
                this.repricingHandler.removeFromRepricing(id);
            }).bind(this),

            assignGeneralIdAction: (function() { this.productSearchHandler.searchGeneralIdAuto(this.getSelectedProductsString())}).bind(this),
            newGeneralIdAction: (function() { this.productSearchHandler.addNewGeneralId(this.getSelectedProductsString())}).bind(this),
            unassignGeneralIdAction: (function() { this.productSearchHandler.unmapFromGeneralId(this.getSelectedProductsString())}).bind(this)

        });

    },

    // ---------------------------------------

    unassignTemplateDescriptionIdActionConfrim: function (id)
    {
        if (!this.confirm()) {
            return;
        }

        this.templateDescriptionHandler.unassignFromTemplateDescription(id)
    },

    // ---------------------------------------

    unassignTemplateShippingTemplateIdActionConfrim: function (id)
    {
        if (!this.confirm()) {
            return;
        }

        this.templateShippingHandler.unassign(id, M2ePro.php.constant('Ess_M2ePro_Model_Amazon_Account::SHIPPING_MODE_TEMPLATE'))
    },

    unassignTemplateShippingOverrideIdActionConfrim: function (id)
    {
        if (!this.confirm()) {
            return;
        }

        this.templateShippingHandler.unassign(id, M2ePro.php.constant('Ess_M2ePro_Model_Amazon_Account::SHIPPING_MODE_OVERRIDE'))
    },

    unassignTemplateProductTaxCodeIdActionConfrim: function (id)
    {
        if (!this.confirm()) {
            return;
        }

        this.templateProductTaxCodeHandler.unassign(id)
    }

    // ---------------------------------------
});