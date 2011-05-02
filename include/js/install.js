// FORUM
if (typeof FORUM === 'undefined' || !FORUM) {
	var FORUM = {};
}

// INSTALL
FORUM.install = function () {
	var docEl = document.documentElement;

	function get(el) {
		return document.getElementById(el);
	}

	return {
		init: function () {
			FORUM.install.selectDB_attach_event();
		},


		// Hide/unhide optional db_username/password fileds depends on selected DB type
		selectDB_update: function () {
			var selected_db = get('req_db_type')[get('req_db_type').selectedIndex].value;

			if (selected_db === 'sqlite' || selected_db === 'sqlite3') {
				FORUM.punbb.addClass(get('db_username_block'), 'hidden');
				FORUM.punbb.addClass(get('db_password_block'), 'hidden');
			} else {
				FORUM.punbb.removeClass(get('db_username_block'), 'hidden');
				FORUM.punbb.removeClass(get('db_password_block'), 'hidden');
			}
			return false;
		},


		// Attach change event for DB-type select box
		selectDB_attach_event: function () {
			var db_sel = get('req_db_type');

			if (db_sel) {
				db_sel.onchange = function () {
					return FORUM.install.selectDB_update();
				};

				// Run first for update on page load
				FORUM.install.selectDB_update();
			}
		},
	};
}();


// One onload handler
FORUM.punbb.addLoadEvent(FORUM.install.init);

