function category_new() {
    if ($('page_alternate_category').value != '-1') {
        $('page_alternate_category_new').up().up().hide();
    } else {
        $('page_alternate_category_new').up().up().show();
    }
}
