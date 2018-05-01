/**
 * created : 22 septembre 2009
 * 
 * @category SQLI
 * @author alay
 * @copyright SQLI - 2009 - http://www.sqli.com
 */

Product.Video = Class.create();
Product.Video.prototype = {
    config: {},
    containerId: null,
    initialize: function(containerId) {
        this.containerId = containerId;
    },
    setConfig: function(config) {
        this.config = config;
    },
    getConfig: function () {
        return this.config;
    },
    create: function () {
        var win = window.open(this.getConfig().url, 'new_video', 'width=900,height=300,resizable=1,scrollbars=0');
        win.focus();
    },
    update: function (id) {
        var win = window.open(this.getConfig().url + 'video_item_id/' + id, 'update_video', 'width=900,height=300,resizable=1,scrollbars=0');
        win.focus();
    },
};