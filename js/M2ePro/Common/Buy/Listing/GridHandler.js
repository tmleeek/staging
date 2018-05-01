CommonBuyListingGridHandler = Class.create(CommonListingGridHandler, {

    // ---------------------------------------

    getComponent: function()
    {
        return 'buy';
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
        this.productSearchHandler = new CommonBuyListingProductSearchHandler(this);

        this.actions = Object.extend(this.actions, {

            movingAction: this.movingHandler.run.bind(this.movingHandler),

            assignGeneralIdAction: (function() { this.productSearchHandler.searchGeneralIdAuto(this.getSelectedProductsString())}).bind(this),
            unassignGeneralIdAction: (function() { this.productSearchHandler.unmapFromGeneralId(this.getSelectedProductsString())}).bind(this)
        });
    }

    // ---------------------------------------
});