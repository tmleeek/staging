function translateSearch(url){
    new Ajax.Request(url, {
        method: 'get',
        parameters: $('phpro_search_form').serialize(),
        onSuccess: function(transport) {
            json = transport.responseText.evalJSON();
            $('result').update('<div class="hor-scroll">'+json.records+'</div>');
        }
    });
}

function translateSearchReset(){
    $('result').update('<div class="hor-scroll"></div>');
}

Event.observe(window, 'load', function() {
    //Event.observe('signinForm', 'submit', checkForm);
    $('phpro_search_form').observe('submit', function(event){
        $('form_search_submit').fire('click');
        Event.stop(event);
    });
});

Event.observe(window, 'load', function() {
    $('q').observe('keypress', function(event){
        if(event.keyCode == Event.KEY_RETURN) {
            translateSearch($('phpro_search_form').action);
            Event.stop(event);
        }
    });
});

function editUrlExpand(row) {
    location.href = row.select(':last-child a');
}

function sortResult(column, url) {
    new Ajax.Request(url, {
        method: 'get',
        parameters: 'column=' + column,
        onSuccess: function(transport) {
            
            json = transport.responseText.evalJSON();
            $('result').update('<div class="hor-scroll">'+json.records+'</div>');
        }
    });
}