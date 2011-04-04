if (typeof FORUM === "undefined" || !FORUM) {
	var FORUM = {};
}

FORUM.utils = function () {
	return {
		getCase: function (value, gen_pl, gen_sg, nom_sg) {
			if ((value % 100 >= 5) & (value % 100 <= 20)) {
				return gen_pl;
			}

			value = value % 10;
			if (((value >= 5) & (value <= 9)) | (value === 0)) {
				return gen_pl;
			}

			if ((value >= 2) & (value <= 4)) {
				return gen_sg;
			}

			if (value == 1) {
				return nom_sg;
			}
		},

		gct: function () {
			return new Date().getTime();
		},

		get_page_type: function () {
			if ($('#brd-viewtopic').length) {
				return 'viewtopic';
			} else if ($('#brd-index').length) {
				return 'index';
			} else if ($('#brd-viewforum').length) {
				return 'viewforum';
			}

			return null;
		},

		load_page: function (url) {
			if (url) {
				document.location = url;
			}
		},

		fancy_load_page: function (url) {
			if (!url) {
				return;
			}

			$(document).trigger('progress_show.fancy_jquery');

			_.defer(function () {
				FORUM.utils.load_page(url);
			});
		},

		loadScript: function (pause, name, script, useCache, callback) {
			$("body").oneTime(pause, name, function () {
				$.ajaxSetup({cache: useCache});
				$.getScript(script, function () {
					if (typeof callback == 'function') {
						callback();
					}
				});
				// revert to default
				$.ajaxSetup({cache: false});
			});
		}
	};
}();
