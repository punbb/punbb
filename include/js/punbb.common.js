/* FORUM
 * version 0.3
 */

/*global PUNBB: true */

if (typeof PUNBB === "undefined" || !PUNBB) {
	var PUNBB = {};
}

PUNBB.common = function () {
	var docEl = document.documentElement;

	function get(el) {
		return document.getElementById(el);
	}

	return {
		init: function() {
			PUNBB.common.addClass(docEl, "js");

			PUNBB.common.attachWindowOpen();
			PUNBB.common.autoFocus();
			PUNBB.common.attachCtrlEnterForm();

			if (!PUNBB.common.input_support_attr("required")) {
				PUNBB.common.attachValidateForm();
			}
			// Hide Flash Messages
			var msgEl = get("brd-messages");
			if (msgEl) {
				setTimeout(function () {
					msgEl.style.visibility = "hidden";
				}, 3000);
			}
		},

		// attach FN to WINDOW.ONLOAD handler
		addLoadEvent: function (fn)
		{
			var x = window.onload;
			window.onload = (x && typeof x == "function") ? function () { x(); fn(); } : fn;
		},

		// return TRUE if node N has class X, else FALSE
		hasClass: function (n, x)
		{
			return (new RegExp("\\b" + x + "\\b")).test(n.className);
		},

		// add X class to N node, return TRUE if added, FALSE if already exists
		addClass: function (n, x)
		{
			if (PUNBB.common.hasClass(n, x)) return false;
			else n.className += " " + x;
			return true;
		},

		// remove X class from N node, return TRUE if removed, FALSE if not present
		removeClass: function (n, x)
		{
			if (!PUNBB.common.hasClass(n, x)) return false;
			x = new RegExp("\\s*\\b" + x + "\\b", "g");
			n.className = n.className.replace(x, "");
			return true;
		},

		// blink node N twice
		blink: function (n, i)
		{
			if (typeof i == "undefined") i = 2;
			var x = n.style.visibility;
			if (i && x!="hidden")
			{
				n.style.visibility = "hidden";
				setTimeout(function () { n.style.visibility=x; }, 200);
				setTimeout(function () { PUNBB.common.blink(n,i-1); }, 400);
			}
		},

		// return true if node N scrolled into view, else false (y axis only)
		onScreen: function (n)
		{
			function pageYOffset() // return number of pixels page has scrolled
			{
				var y = -1;
				if (window.pageYOffset)
					y = window.pageYOffset; // all except IE
				else if (docEl && docEl.scrollTop)
					y = docEl.scrollTop; // IE 6 Strict
				else if (document.body)
					y = document.body.scrollTop; // all other IE ver

				return y;
			}

			function innerHeight() // return inner height of browser window
			{
				var y = -1;
				if (window.innerHeight)
					y = window.innerHeight; // all except IE
				else if (docEl && docEl.clientHeight)
					y = docEl.clientHeight; // IE 6 Strict Mode
				else if (document.body)
					y = document.body.clientHeight; // all other IE ver

				return y;
			}

			function nodeYOffset(n) // return y coordinate of node N
			{
				var y = n.offsetTop;
				n = n.offsetParent;
				return n ? y += nodeYOffset(n) : y;
			}

			var screenTop = pageYOffset();
			var screenBottom = screenTop + innerHeight();
			var nodeTop = nodeYOffset(n);
			var nodeBottom = nodeTop + n.clientHeight;

			return nodeTop >= screenTop && nodeBottom < screenBottom;
		},

		// apply FN to every ARR item, return array of results
		map: function (fn, arr)
		{
			for (var i=0,len=arr.length; i<len; i++)
			{
				arr[i] = fn(arr[i]);
			}
			return arr;
		},

		// return first index where FN(ARR[i]) is true or -1 if none
		find: function (fn, arr)
		{
			for (var i=0,len=arr.length; i<len; i++)
			{
				if (fn(arr[i])) return i;
			}
			return -1;
		},

		// return array of elements for which FN(ARR[i]) is true
		arrayOfMatched: function (fn, arr)
		{
			var matched = [];
			for (var i=0,len=arr.length; i<len; i++)
			{
				if (fn(arr[i])) matched.push(arr[i]);
			}
			return matched;
		},

		// flattens multi-dimentional arrays into simple arrays
		flatten: function (arr)
		{
			var flt = [];
			for (var i=0,len=arr.length; i<len; i++)
			{
				if (typeof arr[i] == "object" && arr.length)
				{
					flt.concat(PUNBB.common.flatten(arr[i]));
				}
				else
					flt.push(arr[i]);
			}

			return flt;
		},

		// check FORMs required (REQ_) fields
		validateForm: function (form)
		{
			var elements = form.elements;
			var fn = function (x) { return x.name && x.name.indexOf("req_") === 0; };
			var nodes = PUNBB.common.arrayOfMatched(fn, elements);
			fn = function (x) { return (/^\s*$/).test(x.value); };
			var empty = PUNBB.common.find(fn, nodes);
			if (empty > -1)
			if (PUNBB.common.find(fn, nodes) > -1)
			{
				var n = get("req-msg");
				PUNBB.common.removeClass(n, "req-warn");
				var newlyAdded = PUNBB.common.addClass(n, "req-error");
				if (!PUNBB.common.onScreen(n))
				{
					n.scrollIntoView(); // method not in W3C DOM, but fully cross-browser?
					setTimeout(function () { PUNBB.common.blink(n); }, 500);
				}
				else if (!newlyAdded) PUNBB.common.blink(n);
				if (PUNBB.common.onScreen(nodes[empty])) nodes[empty].focus();
				return false;
			}
			return true;
		},


		// create a proper redirect URL (if were using SEF friendly URLs) and go there
		doQuickjumpRedirect: function (url, forum_names)
		{
			var selected_forum_id = get("qjump-select")[get("qjump-select").selectedIndex].value;
			url = url.replace("$1", selected_forum_id);
			url = url.replace("$2", forum_names[selected_forum_id]);
			document.location = url;
			return false;
		},


		//
		attachQuickjumpRedirect: function (qj_url, sf_url_array) {
			var qj_sel = get("qjump-select"),
				qj_submit = get("qjump-submit");

			if (qj_sel) {
				qj_sel.onchange = function () {
					return PUNBB.common.doQuickjumpRedirect(qj_url, sf_url_array);
				};
			}

			if (qj_submit) {
				qj_submit.onclick = function () {
					return PUNBB.common.doQuickjumpRedirect(qj_url, sf_url_array);
				};

				// Hide
				PUNBB.common.addClass(qj_submit, "visual-hidden");
			}
		},


		initToggleCheckboxes: function ()
		{
			var fn_click = function (frm) {
				return PUNBB.common.toggleCheckboxes(frm);
			};

			var inputlist = document.getElementsByTagName("span");
			for (var i = 0, cl = inputlist.length; i < cl; i++)
			{
				var el = inputlist[i];
				if (PUNBB.common.hasClass(el, "select-all") && el.getAttribute("data-check-form"))
				{
					var frm = get(el.getAttribute("data-check-form"));
					if (frm)
						el.onclick = fn_click(frm);
				}
			}
		},

		// toggle all checkboxes in the given form
		toggleCheckboxes: function (curForm)
		{
			if (!curForm)
				return false;

			var inputlist = curForm.getElementsByTagName("input");
			for (var i = 0, cl = inputlist.length; i < cl; i++)
			{
				var el = inputlist[i];
				if (el.getAttribute("data-no-select-all"))
					continue;

				if (el.getAttribute("type") == "checkbox" && el.disabled === false)
					el.checked = !el.checked;
			}

			return false;
		},


		// attach form submit by ctrl + enter
		attachCtrlEnterForm: function () {
			var fn_keypress = function (frm) {
				return function (e) {
					if (((e.keyCode == 13) || (e.keyCode == 10)) && (e.ctrlKey === true)) {
						return frm.submit();
					}
				};
			};

			var fn_textarea = function (x) {
				return x.tagName.toUpperCase() == 'TEXTAREA';
			};

			var forms = document.forms;
			for (var i = 0, len = forms.length; i<len; i++)
			{
				var f = forms[i];
				if (!PUNBB.common.hasClass(f, 'frm-ctrl-submit')) {
					continue;
				}

				var elements = f.elements,
					nodes = PUNBB.common.arrayOfMatched(fn_textarea, elements);

				for (var j = 0, j_len = nodes.length; j<j_len; j++) {
					nodes[j].onkeypress = fn_keypress(f);
				}
			}
		},


		// attach form validation function to submit-type inputs
		attachValidateForm: function ()
		{
			var fn_req = function (x) {
				return x.name && x.name.indexOf("req_") === 0;
			};

			var fn_button = function (x) {
				return x.type && (x.type == "submit" && x.name != "cancel");
			};

			var fn_validator = function (frm) {
				return function () {
					return PUNBB.common.validateForm(frm);
				};
			};

			var forms = document.forms;
			for (var i = 0, len = forms.length; i<len; i++)
			{
				var elements = forms[i].elements;
				if (PUNBB.common.find(fn_req, elements) > -1)
				{
					var nodes = PUNBB.common.arrayOfMatched(fn_button, elements),
						formRef = forms[i];

					//TODO: look at passing array of node refs instead of forum ref
					//fn = function() { return Forum.checkReq(required.slice(0)) };
					nodes = PUNBB.common.map(function (x) {
						x.onclick = fn_validator(formRef);
					}, nodes);
				}
			}
		},

		//
		attachWindowOpen: function ()
		{
			if (!document.getElementsByTagName)
				return;

			var fn_open = function () {
				return function () {
					window.open(this.href);
					return false;
				};
			};

			var nodes = document.getElementsByTagName("a");
			for (var i=0; i<nodes.length; i++)
			{
				if (PUNBB.common.hasClass(nodes[i], "exthelp"))
					nodes[i].onclick = fn_open();
			}
		},

		//
		autoFocus: function ()
		{
			var fn_input = function (x) {
				return x.tagName.toUpperCase() == "TEXTAREA" || (x.tagName.toUpperCase() == "INPUT" && (x.type == "text") || (x.type == "password") || (x.type == "email") || (x.type == "url") || (x.type == "number"));
			};

			var nodes = get("afocus");
			if (!nodes || window.location.hash.replace(/#/g,"")) return;
			nodes = nodes.all ? nodes.all : nodes.getElementsByTagName("*");
			// TODO: make sure line above gets nodes in display-order across browsers
			var n = PUNBB.common.find(fn_input, nodes);
			if (n > -1) nodes[n].focus();
		},

		input_support_attr: function (attr) {
			var inputElem = document.createElement("input");

			if (!attr) {
				return false;
			}

			return !!(attr in inputElem);
		}
	};
}();


// One onload handler
PUNBB.common.addLoadEvent(PUNBB.common.init);


/* A handful of functions in this script have been released into the Public
   Domain by Shawn Brown or other authors. Although I, Shawn Brown, do not
   believe that it is legally necessary to note which parts of a Copyrighted
   work are based on Public Domain content, a list of the Public Domain
   code (functions and methods) contained in this file is included below:

   * addLoadEvent: Released into the Public Domain by Shawn Brown and
        based on Simon Willisons Public Domain function of the same name.
   * hasClass, addClass & removeClass: Released into the Public Domain
        by Shawn Brown.
   * onScreen: Released into the Public Domain by Shawn Brown and based,
        in-part, on Peter Paul-Kochs Public Domain node-position functions.
   * map, find, arrayOfMatched & flatten: These basic functional methods
        have been released into the Public Domain by Shawn Brown.

   It is entirely possible that, in the future, someone may contribute code
   that is in the Public Domain but not note it as such. This should not be
   a problem, but one should keep in mind that the list provided here is known
   to be complete and accurate only up until 24-JUNE-2007.
*/
