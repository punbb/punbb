/* Modernizr custom build of 1.7: input */
window.Modernizr=function(a,b,c){function G(){e.input=function(a){for(var b=0,c=a.length;b<c;b++)t[a[b]]=!!(a[b]in l);return t}("autocomplete autofocus list placeholder max min multiple pattern required step".split(" "))}function F(a,b){var c=a.charAt(0).toUpperCase()+a.substr(1),d=(a+" "+p.join(c+" ")+c).split(" ");return!!E(d,b)}function E(a,b){for(var d in a)if(k[a[d]]!==c&&(!b||b(a[d],j)))return!0}function D(a,b){return(""+a).indexOf(b)!==-1}function C(a,b){return typeof a===b}function B(a,b){return A(o.join(a+";")+(b||""))}function A(a){k.cssText=a}var d="1.7",e={},f=!0,g=b.documentElement,h=b.head||b.getElementsByTagName("head")[0],i="modernizr",j=b.createElement(i),k=j.style,l=b.createElement("input"),m=":)",n=Object.prototype.toString,o=" -webkit- -moz- -o- -ms- -khtml- ".split(" "),p="Webkit Moz O ms Khtml".split(" "),q={svg:"http://www.w3.org/2000/svg"},r={},s={},t={},u=[],v,w=function(a){var c=b.createElement("style"),d=b.createElement("div"),e;c.textContent=a+"{#modernizr{height:3px}}",h.appendChild(c),d.id="modernizr",g.appendChild(d),e=d.offsetHeight===3,c.parentNode.removeChild(c),d.parentNode.removeChild(d);return!!e},x=function(){function d(d,e){e=e||b.createElement(a[d]||"div");var f=(d="on"+d)in e;f||(e.setAttribute||(e=b.createElement("div")),e.setAttribute&&e.removeAttribute&&(e.setAttribute(d,""),f=C(e[d],"function"),C(e[d],c)||(e[d]=c),e.removeAttribute(d))),e=null;return f}var a={select:"input",change:"input",submit:"form",reset:"form",error:"img",load:"img",abort:"img"};return d}(),y=({}).hasOwnProperty,z;C(y,c)||C(y.call,c)?z=function(a,b){return b in a&&C(a.constructor.prototype[b],c)}:z=function(a,b){return y.call(a,b)};for(var H in r)z(r,H)&&(v=H.toLowerCase(),e[v]=r[H](),u.push((e[v]?"":"no-")+v));e.input||G(),e.crosswindowmessaging=e.postmessage,e.historymanagement=e.history,e.addTest=function(a,b){a=a.toLowerCase();if(!e[a]){b=!!b(),g.className+=" "+(b?"":"no-")+a,e[a]=b;return e}},A(""),j=l=null,e._enableHTML5=f,e._version=d,g.className=g.className.replace(/\bno-js\b/,"")+" js "+u.join(" ");return e}(this,this.document)
/*yepnope1.0.1|WTFPL*/
(function(a,b,c){function H(){var a=z;a.loader={load:G,i:0};return a}function G(a,b,c){var e=b=="c"?r:q;i=0,b=b||"j",u(a)?F(e,a,b,this.i++,d,c):(h.splice(this.i++,0,a),h.length==1&&E());return this}function F(a,c,d,g,j,l){function q(){!o&&A(n.readyState)&&(p.r=o=1,!i&&B(),n.onload=n.onreadystatechange=null,e(function(){m.removeChild(n)},0))}var n=b.createElement(a),o=0,p={t:d,s:c,e:l};n.src=n.data=c,!k&&(n.style.display="none"),n.width=n.height="0",a!="object"&&(n.type=d),n.onload=n.onreadystatechange=q,a=="img"?n.onerror=q:a=="script"&&(n.onerror=function(){p.e=p.r=1,E()}),h.splice(g,0,p),m.insertBefore(n,k?null:f),e(function(){o||(m.removeChild(n),p.r=p.e=o=1,B())},z.errorTimeout)}function E(){var a=h.shift();i=1,a?a.t?e(function(){a.t=="c"?D(a):C(a)},0):(a(),B()):i=0}function D(a){var c=b.createElement("link"),d;c.href=a.s,c.rel="stylesheet",c.type="text/css",!a.e&&(o||j)?function g(a){e(function(){if(!d)try{a.sheet.cssRules.length?(d=1,B()):g(a)}catch(b){b.code==1e3||b.message=="security"||b.message=="denied"?(d=1,e(function(){B()},0)):g(a)}},0)}(c):(c.onload=function(){d||(d=1,e(function(){B()},0))},a.e&&c.onload()),e(function(){d||(d=1,B())},z.errorTimeout),!a.e&&f.parentNode.insertBefore(c,f)}function C(a){var c=b.createElement("script"),d;c.src=a.s,c.onreadystatechange=c.onload=function(){!d&&A(c.readyState)&&(d=1,B(),c.onload=c.onreadystatechange=null)},e(function(){d||(d=1,B())},z.errorTimeout),a.e?c.onload():f.parentNode.insertBefore(c,f)}function B(){var a=1,b=-1;while(h.length- ++b)if(h[b].s&&!(a=h[b].r))break;a&&E()}function A(a){return!a||a=="loaded"||a=="complete"}var d=b.documentElement,e=a.setTimeout,f=b.getElementsByTagName("script")[0],g=({}).toString,h=[],i=0,j="MozAppearance"in d.style,k=j&&!!b.createRange().compareNode,l=j&&!k,m=k?d:f.parentNode,n=a.opera&&g.call(a.opera)=="[object Opera]",o="webkitAppearance"in d.style,p=o&&"async"in b.createElement("script"),q=j?"object":n||p?"img":"script",r=o?"img":q,s=Array.isArray||function(a){return g.call(a)=="[object Array]"},t=function(a){return typeof a=="object"},u=function(a){return typeof a=="string"},v=function(a){return g.call(a)=="[object Function]"},w=[],x={},y,z;z=function(a){function h(a,b){function i(a){if(u(a))g(a,f,b,0,c);else if(t(a))for(h in a)a.hasOwnProperty(h)&&g(a[h],f,b,h,c)}var c=!!a.test,d=c?a.yep:a.nope,e=a.load||a.both,f=a.callback,h;i(d),i(e),a.complete&&b.load(a.complete)}function g(a,b,d,e,g){var h=f(a),i=h.autoCallback;if(!h.bypass){b&&(b=v(b)?b:b[a]||b[e]||b[a.split("/").pop().split("?")[0]]);if(h.instead)return h.instead(a,b,d,e,g);d.load(h.url,h.forceCSS||!h.forceJS&&/css$/.test(h.url)?"c":c,h.noexec),(v(b)||v(i))&&d.load(function(){H(),b&&b(h.origUrl,g,e),i&&i(h.origUrl,g,e)})}}function f(a){var b=a.split("!"),c=w.length,d=b.pop(),e=b.length,f={url:d,origUrl:d,prefixes:b},g,h;for(h=0;h<e;h++)g=x[b[h]],g&&(f=g(f));for(h=0;h<c;h++)f=w[h](f);return f}var b,d,e=this.yepnope.loader;if(u(a))g(a,0,e,0);else if(s(a))for(b=0;b<a.length;b++)d=a[b],u(d)?g(d,0,e,0):s(d)?z(d):t(d)&&h(d,e);else t(a)&&h(a,e)},z.addPrefix=function(a,b){x[a]=b},z.addFilter=function(a){w.push(a)},z.errorTimeout=1e4,b.readyState==null&&b.addEventListener&&(b.readyState="loading",b.addEventListener("DOMContentLoaded",y=function(){b.removeEventListener("DOMContentLoaded",y,0),b.readyState="complete"},0)),a.yepnope=H()})(this,this.document)

/* FORUM */
if (typeof FORUM === "undefined" || !FORUM) {
	var FORUM = {};
}

FORUM.punbb = function () {
	return {

		// attach FN to WINDOW.ONLOAD handler
		addLoadEvent: function(fn)
		{
			var x = window.onload;
			window.onload = (x && typeof x=='function') ? function(){x();fn()} : fn;
		},

		// return TRUE if node N has class X, else FALSE
		hasClass: function(n, x)
		{
			return (new RegExp('\\b' + x + '\\b')).test(n.className)
		},

		// add X class to N node, return TRUE if added, FALSE if already exists
		addClass: function(n, x)
		{
			if (FORUM.punbb.hasClass(n, x)) return false;
			else n.className += ' ' + x;
			return true;
		},

		// remove X class from N node, return TRUE if removed, FALSE if not present
		removeClass: function(n, x)
		{
			if (!FORUM.punbb.hasClass(n, x)) return false;
			x = new RegExp('\\s*\\b' + x + '\\b', 'g');
			n.className = n.className.replace(x, '');
			return true;
		},

		// blink node N twice
		blink: function(n, i)
		{
			if (typeof i == 'undefined') i = 2;
			var x = n.style.visibility;
			if (i && x!='hidden')
			{
				n.style.visibility = 'hidden';
				setTimeout(function(){n.style.visibility=x}, 200);
				setTimeout(function(){FORUM.punbb.blink(n,i-1)}, 400);
			}
		},

		// return true if node N scrolled into view, else false (y axis only)
		onScreen: function(n)
		{
			function pageYOffset() // return number of pixels page has scrolled
			{
				var y = -1;
				if (self.pageYOffset) y = self.pageYOffset; // all except IE
				else if (document.documentElement && document.documentElement.scrollTop)
					y = document.documentElement.scrollTop; // IE 6 Strict
				else if (document.body) y = document.body.scrollTop; // all other IE ver
				return y;
			}
			function innerHeight() // return inner height of browser window
			{
				var y = -1;
				if (self.innerHeight) y = self.innerHeight; // all except IE
				else if (document.documentElement && document.documentElement.clientHeight)
					y = document.documentElement.clientHeight; // IE 6 Strict Mode
				else if (document.body) y = document.body.clientHeight; // all other IE ver
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
		map: function(fn, arr)
		{
			for (var i=0,len=arr.length; i<len; i++)
			{
				arr[i] = fn(arr[i])
			}
			return arr;
		},

		// return first index where FN(ARR[i]) is true or -1 if none
		find: function(fn, arr)
		{
			for (var i=0,len=arr.length; i<len; i++)
			{
				if (fn(arr[i])) return i;
			}
			return -1;
		},

		// return array of elements for which FN(ARR[i]) is true
		arrayOfMatched: function(fn, arr)
		{
			matched = [];
			for (var i=0,len=arr.length; i<len; i++)
			{
				if (fn(arr[i])) matched.push(arr[i])
			}
			return matched;
		},

		// flattens multi-dimentional arrays into simple arrays
		flatten: function(arr)
		{
			flt = [];
			for (var i=0,len=arr.length; i<len; i++)
			{
				if (typeof arr[i] == 'object' && arr.length)
				{
					flt.concat(FORUM.punbb.flatten(arr[i]))
					alert('length1!!'+ arr.length);
					//x.hasChildNodes()
				}
				else flt.push(arr[i])
			}
			return flt
		},

		// check FORM's required (REQ_) fields
		validateForm: function(form)
		{
			var elements = form.elements;
			var fn = function(x) { return x.name && x.name.indexOf('req_')==0 };
			var nodes = FORUM.punbb.arrayOfMatched(fn, elements);
			fn = function(x) { return /^\s*$/.test(x.value) };
			var empty = FORUM.punbb.find(fn, nodes);
			if (empty > -1)
			if (FORUM.punbb.find(fn, nodes) > -1)
			{
				var n = document.getElementById('req-msg');
				FORUM.punbb.removeClass(n, 'req-warn');
				var newlyAdded = FORUM.punbb.addClass(n, 'req-error');
				if (!FORUM.punbb.onScreen(n))
				{
					n.scrollIntoView(); // method not in W3C DOM, but fully cross-browser?
					setTimeout(function(){FORUM.punbb.blink(n)}, 500);
				}
				else if (!newlyAdded) FORUM.punbb.blink(n);
				if (FORUM.punbb.onScreen(nodes[empty])) nodes[empty].focus();
				return false;
			}
			return true;
		},


		// create a proper redirect URL (if we're using SEF friendly URLs) and go there
		doQuickjumpRedirect: function(url, forum_names)
		{
			var selected_forum_id = document.getElementById('qjump-select')[document.getElementById('qjump-select').selectedIndex].value;
			url = url.replace('$1', selected_forum_id);
			url = url.replace('$2', forum_names[selected_forum_id]);
			document.location = url;
			return false;
		},

		//
		attachQuickjumpRedirect: function() {
			var qj_sel = document.getElementById('qjump-select'),
				qj_submit = document.getElementById('qjump-submit');

			if (qj_sel) {
				qj_sel.onchange = function () {
					return FORUM.punbb.doQuickjumpRedirect(forum_quickjump_url, sef_friendly_url_array);
				};
			}

			if (qj_submit) {
				qj_submit.onclick = function () {
					return FORUM.punbb.doQuickjumpRedirect(forum_quickjump_url, sef_friendly_url_array);
				};
			}
		},

		// toggle all checkboxes in the given form
		toggleCheckboxes: function(curForm)
		{
			var inputlist = curForm.getElementsByTagName("input");
			for (i = 0; i < inputlist.length; i++)
			{
				if (inputlist[i].getAttribute("type") == 'checkbox' && inputlist[i].disabled == false)
					inputlist[i].checked = !inputlist[i].checked;
			}

			return false;
		},

		// attach form validation function to submit-type inputs
		attachValidateForm: function()
		{
			var forms = document.forms;
			for (var i=0,len=forms.length; i<len; i++)
			{
				var elements = forms[i].elements;
				var fn = function(x) { return x.name && x.name.indexOf('req_')==0 };
				if (FORUM.punbb.find(fn, elements) > -1)
				{
					fn = function(x) { return x.type && (x.type=='submit' && x.name!='cancel') };
					var nodes = FORUM.punbb.arrayOfMatched(fn, elements)
					var formRef = forms[i];
					fn = function() { return FORUM.punbb.validateForm(formRef) };
					//TODO: look at passing array of node refs instead of forum ref
					//fn = function() { return Forum.checkReq(required.slice(0)) };
					nodes = FORUM.punbb.map(function(x){x.onclick=fn}, nodes);
				}
			}
		},

		//
		attachWindowOpen: function()
		{
			if (!document.getElementsByTagName) return;
			var nodes = document.getElementsByTagName('a');
			for (var i=0; i<nodes.length; i++)
			{
				if (FORUM.punbb.hasClass(nodes[i], 'exthelp'))
					nodes[i].onclick = function() { window.open(this.href); return false; };
			}
		},

		//
		autoFocus: function()
		{
			var nodes = document.getElementById('afocus');
			if (!nodes || window.location.hash.replace(/#/g,'')) return;
			nodes = nodes.all ? nodes.all : nodes.getElementsByTagName('*');
			// TODO: make sure line above gets nodes in display-order across browsers
			var fn = function(x) { return x.tagName.toUpperCase()=='TEXTAREA' || (x.tagName.toUpperCase()=='INPUT' && (x.type=='text') || (x.type=='password') || (x.type=='email') || (x.type=='url') || (x.type=='number')) };
			var n = FORUM.punbb.find(fn, nodes);
			if (n > -1) nodes[n].focus();
		}
	};
}();

if (!Modernizr.input.required) {
	FORUM.punbb.addLoadEvent(FORUM.punbb.attachValidateForm);
}

FORUM.punbb.addLoadEvent(FORUM.punbb.attachWindowOpen);
FORUM.punbb.addLoadEvent(FORUM.punbb.autoFocus);
FORUM.punbb.addLoadEvent(FORUM.punbb.attachQuickjumpRedirect);

/* A handful of functions in this script have been released into the Public
   Domain by Shawn Brown or other authors. Although I, Shawn Brown, do not
   believe that it is legally necessary to note which parts of a Copyrighted
   work are based on Public Domain content, a list of the Public Domain
   code (functions and methods) contained in this file is included below:

   * addLoadEvent: Released into the Public Domain by Shawn Brown and
        based on Simon Willison's Public Domain function of the same name.
   * hasClass, addClass & removeClass: Released into the Public Domain
        by Shawn Brown.
   * onScreen: Released into the Public Domain by Shawn Brown and based,
        in-part, on Peter Paul-Koch's Public Domain node-position functions.
   * map, find, arrayOfMatched & flatten: These basic functional methods
        have been released into the Public Domain by Shawn Brown.

   It is entirely possible that, in the future, someone may contribute code
   that is in the Public Domain but not note it as such. This should not be
   a problem, but one should keep in mind that the list provided here is known
   to be complete and accurate only up until 24-JUNE-2007.
*/
