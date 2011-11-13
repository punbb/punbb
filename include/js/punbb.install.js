// PunBB install functions
// version 0.2

/*jslint browser: true, maxerr: 50, indent: 4 */
/*global PUNBB: true */

if (typeof PUNBB === 'undefined' || !PUNBB) {
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
			PUNBB.install.get_username_from_email_attach_event();
		},


		// Hide/unhide optional db_username/password fileds depends on selected DB type
		selectDB_update: function () {
			var selected_db = get('req_db_type')[get('req_db_type').selectedIndex].value;

			if (selected_db === 'sqlite' || selected_db === 'sqlite3') {
				PUNBB.common.addClass(get('db_host_block'), 'hidden');
				PUNBB.common.addClass(get('db_username_block'), 'hidden');
				PUNBB.common.addClass(get('db_password_block'), 'hidden');

				// #db_host is required and can not be empty
				if (get('db_host').value.length < 0) {
					get('db_host').value  = 'localhost';
				}
			} else {
				PUNBB.common.removeClass(get('db_host_block'), 'hidden');
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
		},

		get_username_from_email_attach_event: function () {
			var fn_keypress = function (t) {
				return function (e) {
					PUNBB.common.set_username_from_email(this, t);
				};
			};

			get('admin_email').onkeyup = fn_keypress(get('admin_username'));
		}
	};
}());


// One onload handler
PUNBB.common.addDOMReadyEvent(PUNBB.install.init);

