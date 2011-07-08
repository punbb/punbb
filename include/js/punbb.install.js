// PunBB install functions
// version 0.2

/*jslint browser: true, maxerr: 50, indent: 4 */
/*global PUNBB: true */

if (typeof PUNBB === undefined || !PUNBB) {
	var PUNBB = {};
}

// INSTALL
PUNBB.install = (function () {
	'use strict';

	function get(el) {
		return document.getElementById(el);
	}

	return {
		init: function () {
			PUNBB.install.selectDB_attach_event();
		},


		// Hide/unhide optional db_username/password fileds depends on selected DB type
		selectDB_update: function () {
			var selected_db = get('req_db_type')[get('req_db_type').selectedIndex].value;

			if (selected_db === 'sqlite' || selected_db === 'sqlite3') {
				PUNBB.common.addClass(get('db_username_block'), 'hidden');
				PUNBB.common.addClass(get('db_password_block'), 'hidden');
			} else {
				PUNBB.common.removeClass(get('db_username_block'), 'hidden');
				PUNBB.common.removeClass(get('db_password_block'), 'hidden');
			}
			return false;
		},


		// Attach change event for DB-type select box
		selectDB_attach_event: function () {
			var db_sel = get('req_db_type');

			if (db_sel) {
				db_sel.onchange = function () {
					return PUNBB.install.selectDB_update();
				};

				// Run first for update on page load
				PUNBB.install.selectDB_update();
			}
		}
	};
}());


// One onload handler
PUNBB.common.addLoadEvent(PUNBB.install.init);

