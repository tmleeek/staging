RewardsSocialWidgetHover = Class.create();
RewardsSocialWidgetHover.prototype = {
    initialize: function() {
        this.widgets = [];
        return this;
    },
    addWidget: function(widgetName)
    {
        this.widgets.push(widgetName);
        return this;
    }
};