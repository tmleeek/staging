var DataCapture = {
    attachEvents: function ()
    {
        var self = this;

        var inputs = $$('[type=text]');
        inputs.each(function(input) {
            input.observe('change', function(item, event) {
                var e = Event.element(event);
                self.testValue(item.name, e.value);
            }.bind(this, input));
        });
    },

    testValue: function(name, value)
    {

        if (name == 'billing[firstname]') {
            this.ajax('firstname', value);
        } else if (name == 'billing[lastname]') {
            this.ajax('lastname', value);
        } else if (name == 'billing[email]') {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if (re.test(value)) {
                this.ajax('email', value);
            }
        }

    },

    ajax: function(type, value)
    {
        url = window.location.protocol + '//' + window.location.host + '/index.php/email/index/capture';

        new Ajax.Request(url, {
            method: 'post',
            parameters: {type: type, value: value}
        });
    }
};

Event.observe(document, 'dom:loaded', function(){
    DataCapture.attachEvents();
});