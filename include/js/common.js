/* LAB.js v1.2.0 */
(function(p){var q="string",w="head",L="body",M="script",u="readyState",j="preloaddone",x="loadtrigger",N="srcuri",E="preload",Z="complete",y="done",z="which",O="preserve",F="onreadystatechange",ba="onload",P="hasOwnProperty",bb="script/cache",Q="[object ",bw=Q+"Function]",bx=Q+"Array]",e=null,h=true,i=false,k=p.document,bc=p.location,bd=p.ActiveXObject,A=p.setTimeout,be=p.clearTimeout,R=function(a){return k.getElementsByTagName(a)},S=Object.prototype.toString,G=function(){},r={},T={},bf=/^[^?#]*\//.exec(bc.href)[0],bg=/^\w+\:\/\/\/?[^\/]+/.exec(bf)[0],by=R(M),bh=p.opera&&S.call(p.opera)==Q+"Opera]",bi=("MozAppearance"in k.documentElement.style),bj=(k.createElement(M).async===true),v={cache:!(bi||bh),order:bi||bh||bj,xhr:h,dupe:h,base:"",which:w};v[O]=i;v[E]=h;r[w]=k.head||R(w);r[L]=R(L);function B(a){return S.call(a)===bw}function U(a,b){var c=/^\w+\:\/\//,d;if(typeof a!=q)a="";if(typeof b!=q)b="";d=((/^\/\//.test(a))?bc.protocol:"")+a;d=(c.test(d)?"":b)+d;return((c.test(d)?"":(d.charAt(0)==="/"?bg:bf))+d)}function bz(a){return(U(a).indexOf(bg)===0)}function bA(a){var b,c=-1;while(b=by[++c]){if(typeof b.src==q&&a===U(b.src)&&b.type!==bb)return h}return i}function H(t,l){t=!(!t);if(l==e)l=v;var bk=i,C=t&&l[E],bl=C&&l.cache,I=C&&l.order,bm=C&&l.xhr,bB=l[O],bC=l.which,bD=l.base,bn=G,J=i,D,s=h,m={},K=[],V=e;C=bl||bm||I;function bo(a,b){if((a[u]&&a[u]!==Z&&a[u]!=="loaded")||b[y]){return i}a[ba]=a[F]=e;return h}function W(a,b,c){c=!(!c);if(!c&&!(bo(a,b)))return;b[y]=h;for(var d in m){if(m[P](d)&&!(m[d][y]))return}bk=h;bn()}function bp(a){if(B(a[x])){a[x]();a[x]=e}}function bE(a,b){if(!bo(a,b))return;b[j]=h;A(function(){r[b[z]].removeChild(a);bp(b)},0)}function bF(a,b){if(a[u]===4){a[F]=G;b[j]=h;A(function(){bp(b)},0)}}function X(b,c,d,g,f,n){var o=b[z];A(function(){if("item"in r[o]){if(!r[o][0]){A(arguments.callee,25);return}r[o]=r[o][0]}var a=k.createElement(M);if(typeof d==q)a.type=d;if(typeof g==q)a.charset=g;if(B(f)){a[ba]=a[F]=function(){f(a,b)};a.src=c;if(bj){a.async=i}}r[o].insertBefore(a,(o===w?r[o].firstChild:e));if(typeof n==q){a.text=n;W(a,b,h)}},0)}function bq(a,b,c,d){T[a[N]]=h;X(a,b,c,d,W)}function br(a,b,c,d){var g=arguments;if(s&&a[j]==e){a[j]=i;X(a,b,bb,d,bE)}else if(!s&&a[j]!=e&&!a[j]){a[x]=function(){br.apply(e,g)}}else if(!s){bq.apply(e,g)}}function bs(a,b,c,d){var g=arguments,f;if(s&&a[j]==e){a[j]=i;f=a.xhr=(bd?new bd("Microsoft.XMLHTTP"):new p.XMLHttpRequest());f[F]=function(){bF(f,a)};f.open("GET",b);f.send("")}else if(!s&&a[j]!=e&&!a[j]){a[x]=function(){bs.apply(e,g)}}else if(!s){T[a[N]]=h;X(a,b,c,d,e,a.xhr.responseText);a.xhr=e}}function bt(a){if(typeof a=="undefined"||!a)return;if(a.allowDup==e)a.allowDup=l.dupe;var b=a.src,c=a.type,d=a.charset,g=a.allowDup,f=U(b,bD),n,o=bz(f);if(typeof d!=q)d=e;g=!(!g);if(!g&&((T[f]!=e)||(s&&m[f])||bA(f))){if(m[f]!=e&&m[f][j]&&!m[f][y]&&o){W(e,m[f],h)}return}if(m[f]==e)m[f]={};n=m[f];if(n[z]==e)n[z]=bC;n[y]=i;n[N]=f;J=h;if(!I&&bm&&o)bs(n,f,c,d);else if(!I&&bl)br(n,f,c,d);else bq(n,f,c,d)}function Y(a){if(t&&!I)K.push(a);if(!t||C)a()}function bu(a){var b=[],c;for(c=-1;++c<a.length;){if(S.call(a[c])===bx)b=b.concat(bu(a[c]));else b[b.length]=a[c]}return b}D={script:function(){be(V);var a=bu(arguments),b=D,c;if(bB){for(c=-1;++c<a.length;){if(B(a[c]))a[c]=a[c]();if(c===0){Y(function(){bt((typeof a[0]==q)?{src:a[0]}:a[0])})}else b=b.script(a[c]);b=b.wait()}}else{for(c=-1;++c<a.length;){if(B(a[c]))a[c]=a[c]()}Y(function(){for(c=-1;++c<a.length;){bt((typeof a[c]==q)?{src:a[c]}:a[c])}})}V=A(function(){s=i},5);return b},wait:function(a){be(V);s=i;if(!B(a))a=G;var b=H(t||J,l),c=b.trigger,d=function(){try{a()}catch(err){}c()};delete b.trigger;var g=function(){if(J&&!bk)bn=d;else d()};if(t&&!J)K.push(g);else Y(g);return b}};if(t){D.trigger=function(){var a,b=-1;while(a=K[++b])a();K=[]}}else D.trigger=G;return D}function bv(a){var b,c={},d={"UseCachePreload":"cache","UseLocalXHR":"xhr","UsePreloading":E,"AlwaysPreserveOrder":O,"AllowDuplicates":"dupe"},g={"AppendTo":z,"BasePath":"base"};for(b in d)g[b]=d[b];c.order=!(!v.order);for(b in g){if(g[P](b)&&v[g[b]]!=e)c[g[b]]=(a[b]!=e)?a[b]:v[g[b]]}for(b in d){if(d[P](b))c[d[b]]=!(!c[d[b]])}if(!c[E])c.cache=c.order=c.xhr=i;c.which=(c.which===w||c.which===L)?c.which:w;return c}p.$LAB={setGlobalDefaults:function(a){v=bv(a)},setOptions:function(a){return H(i,bv(a))},script:function(){return H().script.apply(e,arguments)},wait:function(){return H().wait.apply(e,arguments)}};(function(a,b,c){if(k[u]==e&&k[a]){k[u]="loading";k[a](b,c=function(){k.removeEventListener(b,c,i);k[u]=Z},i)}})("addEventListener","DOMContentLoaded")})(window);

/* FORUM */
if (typeof FORUM === "undefined" || !FORUM) {
	var FORUM = {};
}

FORUM.punbb = function () {
	return {
		init: function() {
    		FORUM.punbb.addClass(document.documentElement, "js");
		},

		// attach FN to WINDOW.ONLOAD handler
		addLoadEvent: function(fn)
		{
			var x = window.onload;
			window.onload = (x && typeof x=="function") ? function(){x();fn()} : fn;
		},

		// return TRUE if node N has class X, else FALSE
		hasClass: function(n, x)
		{
			return (new RegExp("\\b" + x + "\\b")).test(n.className)
		},

		// add X class to N node, return TRUE if added, FALSE if already exists
		addClass: function(n, x)
		{
			if (FORUM.punbb.hasClass(n, x)) return false;
			else n.className += " " + x;
			return true;
		},

		// remove X class from N node, return TRUE if removed, FALSE if not present
		removeClass: function(n, x)
		{
			if (!FORUM.punbb.hasClass(n, x)) return false;
			x = new RegExp("\\s*\\b" + x + "\\b", "g");
			n.className = n.className.replace(x, "");
			return true;
		},

		// blink node N twice
		blink: function(n, i)
		{
			if (typeof i == "undefined") i = 2;
			var x = n.style.visibility;
			if (i && x!="hidden")
			{
				n.style.visibility = "hidden";
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
				if (typeof arr[i] == "object" && arr.length)
				{
					flt.concat(FORUM.punbb.flatten(arr[i]))
				}
				else flt.push(arr[i])
			}
			return flt
		},

		// check FORMs required (REQ_) fields
		validateForm: function(form)
		{
			var elements = form.elements;
			var fn = function(x) { return x.name && x.name.indexOf("req_")==0 };
			var nodes = FORUM.punbb.arrayOfMatched(fn, elements);
			fn = function(x) { return /^\s*$/.test(x.value) };
			var empty = FORUM.punbb.find(fn, nodes);
			if (empty > -1)
			if (FORUM.punbb.find(fn, nodes) > -1)
			{
				var n = document.getElementById("req-msg");
				FORUM.punbb.removeClass(n, "req-warn");
				var newlyAdded = FORUM.punbb.addClass(n, "req-error");
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


		// create a proper redirect URL (if were using SEF friendly URLs) and go there
		doQuickjumpRedirect: function(url, forum_names)
		{
			var selected_forum_id = document.getElementById("qjump-select")[document.getElementById("qjump-select").selectedIndex].value;
			url = url.replace("$1", selected_forum_id);
			url = url.replace("$2", forum_names[selected_forum_id]);
			document.location = url;
			return false;
		},

		//
		attachQuickjumpRedirect: function() {
			var qj_sel = document.getElementById("qjump-select"),
				qj_submit = document.getElementById("qjump-submit");

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
				if (inputlist[i].getAttribute("type") == "checkbox" && inputlist[i].disabled == false)
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
				var fn = function(x) { return x.name && x.name.indexOf("req_")==0 };
				if (FORUM.punbb.find(fn, elements) > -1)
				{
					fn = function(x) { return x.type && (x.type=="submit" && x.name!="cancel") };
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
			var nodes = document.getElementsByTagName("a");
			for (var i=0; i<nodes.length; i++)
			{
				if (FORUM.punbb.hasClass(nodes[i], "exthelp"))
					nodes[i].onclick = function() { window.open(this.href); return false; };
			}
		},

		//
		autoFocus: function()
		{
			var nodes = document.getElementById("afocus");
			if (!nodes || window.location.hash.replace(/#/g,"")) return;
			nodes = nodes.all ? nodes.all : nodes.getElementsByTagName("*");
			// TODO: make sure line above gets nodes in display-order across browsers
			var fn = function(x) { return x.tagName.toUpperCase()=="TEXTAREA" || (x.tagName.toUpperCase()=="INPUT" && (x.type=="text") || (x.type=="password") || (x.type=="email") || (x.type=="url") || (x.type=="number")) };
			var n = FORUM.punbb.find(fn, nodes);
			if (n > -1) nodes[n].focus();
		},

		input_support_attr: function(attr) {
			var inputElem = document.createElement("input");

			if (!attr) {
				return false;
			}

			return !!(attr in inputElem);
		}

	};
}();

if (!FORUM.punbb.input_support_attr("required")) {
	FORUM.punbb.addLoadEvent(FORUM.punbb.attachValidateForm);
}

FORUM.punbb.addLoadEvent(FORUM.punbb.init);
FORUM.punbb.addLoadEvent(FORUM.punbb.attachWindowOpen);
FORUM.punbb.addLoadEvent(FORUM.punbb.autoFocus);
FORUM.punbb.addLoadEvent(FORUM.punbb.attachQuickjumpRedirect);


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
