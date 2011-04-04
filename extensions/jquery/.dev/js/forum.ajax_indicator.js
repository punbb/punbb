if (typeof FORUM === "undefined" || !FORUM) {
	var FORUM = {};
}

FORUM.ajax_progress_indicator = function () {
	var $indicator = null,
		instances = 0;

	// CREATE INDICATOR DOM
	function create() {
		$indicator = $('<div id="fancy_ajax_progress_indicator"><div class="content"><span class="close"/></div></div>').appendTo('body');
	}

	// SHOW INDICATOR
	function show() {
		if (!$indicator) {
			return;
		}

		instances = instances + 1;

		// ALREADY RUNNING?
		if (instances > 1) {
			return;
		}

		$indicator.show();
	}

	// HIDE INDICATOR
	function hide() {
		$(document).stopTime('fancy_ajax_ind');

		if (instances < 1) {
			instances = 0;
			return;
		}

		instances = instances - 1;

		// ALREADY STOPPING?
		if (instances > 0) {
			return;
		}

		$indicator.hide();
	}

	return {
		init: function () {
			create();

			// BIND to jQuery AJAX GLOBAL
			$(document).bind('ajaxSend', function () {
				$(document).stopTime('fancy_ajax_ind').oneTime(150, 'fancy_ajax_ind', show);
			}).bind('ajaxComplete', function () {
				hide();
			});

			// CREATE EVENTS for SHOW
			$(document).bind('progress_show.fancy_jquery', show);

			// CREATE EVENTS for HIDE
			$(document).bind('progress_hide.fancy_jquery', hide);
		},

		// RETURN STATUS
		is_progress: function () {
			return (instances > 0);
		}
	};
}();

// RUN
jQuery(document).ready(function () {
	FORUM.ajax_progress_indicator.init();
});
