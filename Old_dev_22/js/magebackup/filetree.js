if (jQuery) (function($) {

	$.extend($.fn, {
		mbFileTree: function(o, h) {
			// Defaults
			if (!o) var o = {};
			if (o.root == undefined) o.root = '/';
			if (o.script == undefined) o.script = 'jqueryFileTree.php';
			if (o.folderEvent == undefined) o.folderEvent = 'click';
			if (o.expandSpeed == undefined) o.expandSpeed = 500;
			if (o.collapseSpeed == undefined) o.collapseSpeed = 500;
			if (o.expandEasing == undefined) o.expandEasing = null;
			if (o.collapseEasing == undefined) o.collapseEasing = null;
			if (o.multiFolder == undefined) o.multiFolder = true;
			if (o.loadMessage == undefined) o.loadMessage = 'Loading...';
			if (o.selectedFiles == undefined) o.selectedFiles = '#selectedFiles';
			if (o.checkboxEvent == undefined) o.checkboxEvent = 'click';
			if (o.formKey == undefined) o.formKey = '';

			if (o.afterLoad == undefined) {
				o.afterLoad	= null;
			}

			$(this).each(function() {

				function showTree(c, t) {
					$(c).addClass('wait');
					$('#loading-mask').show();
					$(".jqueryFileTree.start").remove();
					$.post(o.script, { dir: t, form_key: o.formKey }, function(data) {
						$(c).find('.start').html('');
						$(c).removeClass('wait').append(data);
						$('#loading-mask').hide();
						if (o.root == t) $(c).find('UL:hidden').show(); else $(c).find('UL:hidden').slideDown({ duration: o.expandSpeed, easing: o.expandEasing });
						bindTree(c);

						if (typeof o.afterLoad == 'function') {
							o.afterLoad.call(this, c);
						}
					});
				}

				function toggleCheck(checked, value, selectedFiles) {
					if (checked && $.inArray(value, selectedFiles) < 0) {
						selectedFiles.push(value);
					} else if (!checked) {
						while (selectedFiles.indexOf(value) != -1) {
							selectedFiles.splice(selectedFiles.indexOf(value), 1);
						}
					}

					return selectedFiles.join(',');
				}

				function bindTree(t) {
					// check files
					if ($(o.selectedFiles).length) {
						var selectedFiles	= $(o.selectedFiles).val().split(',');
						$(t).find('LI INPUT').each(function() {
							this.checked = $.inArray($(this).val(), selectedFiles) >= 0;
						});

						$(t).find('LI INPUT').bind(o.checkboxEvent, function() {
							varienElementMethods.setHasChanges(this);
							var selectedFiles	= $(o.selectedFiles).val().split(',');
							$(o.selectedFiles).val(toggleCheck(this.checked, $(this).val(), selectedFiles));
						});
					}

					$(t).find('LI A').bind(o.folderEvent, function() {
						if ($(this).parent().hasClass('directory')) {
							if ($(this).parent().hasClass('collapsed')) {
								// Expand
								if (!o.multiFolder) {
									$(this).parent().parent().find('UL').slideUp({ duration: o.collapseSpeed, easing: o.collapseEasing });
									$(this).parent().parent().find('LI.directory').removeClass('expanded').addClass('collapsed');
								}
								$(this).parent().find('UL').remove(); // cleanup
								showTree($(this).parent(), escape($(this).attr('rel').match(/.*\//)));
								$(this).parent().removeClass('collapsed').addClass('expanded');
							} else {
								// Collapse
								$(this).parent().find('UL').slideUp({ duration: o.collapseSpeed, easing: o.collapseEasing });
								$(this).parent().removeClass('expanded').addClass('collapsed');
							}
						} else {
							h($(this).attr('rel'));
						}
						return false;
					});
					// Prevent A from triggering the # on non-click events
					if (o.folderEvent.toLowerCase != 'click') $(t).find('LI A').bind('click', function() {
						return false;
					});
				}

				// Loading message
				$(this).html('<ul class="jqueryFileTree start"><li class="wait">' + o.loadMessage + '<li></ul>');
				// Get the initial file list
				showTree($(this), escape(o.root));
			});
		}
	});

})(jQuery);