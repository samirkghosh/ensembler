// Some global instances, this will be filled later
var tinyMCE = null, tinyMCELang = null;

function TinyMCE_Popup() {
};

TinyMCE_Popup.prototype = {
	findWin : function(w) {
		var c;

		// Check parents
		c = w;
		while (c && (c = c.parent) != null) {
			if (typeof(c.tinyMCE) != "undefined")
				return c;
		}

		// Check openers
		c = w;
		while (c && (c = c.opener) != null) {
			if (typeof(c.tinyMCE) != "undefined")
				return c;
		}

		// Try top
		if (typeof(top.tinyMCE) != "undefined")
			return top;

		return null;
	},

	init : function() {
		var win = window.opener ? window.opener : window.dialogArguments, c;
		var inst;

		if (!win)
			win = this.findWin(window);

		if (!win) {
			alert("tinyMCE object reference not found from popup.");
			return;
		}

		window.opener = win;
		this.windowOpener = win;
		this.onLoadEval = "";

		// Setup parent references
		tinyMCE = win.tinyMCE;
		tinyMCELang = win.tinyMCELang;

		inst = tinyMCE.selectedInstance;
		this.isWindow = tinyMCE.getWindowArg('mce_inside_iframe', false) == false;
		this.storeSelection = (tinyMCE.isRealIE) && !this.isWindow && tinyMCE.getWindowArg('mce_store_selection', true);

		if (this.isWindow)
			window.focus();

		// Store selection
		if (this.storeSelection)
			inst.selectionBookmark = inst.selection.getBookmark(true);

		// Setup dir
		if (tinyMCELang['lang_dir'])
			document.dir = tinyMCELang['lang_dir'];

		// Setup title
		var re = new RegExp('{|\\\$|}', 'g');
		var title = document.title.replace(re, "");
		if (typeof tinyMCELang[title] != "undefined") {
			var divElm = document.createElement("div");
			divElm.innerHTML = tinyMCELang[title];
			document.title = divElm.innerHTML;

			if (tinyMCE.setWindowTitle != null)
				tinyMCE.setWindowTitle(window, divElm.innerHTML);
		}
//the code below modified by Logan
		// Output Popup CSS class
/*		document.write('<link href="' + tinyMCE.getParam("popups_css") + '" rel="stylesheet" type="text/css">');

		if (tinyMCE.getParam("popups_css_add")) {
			c = tinyMCE.getParam("popups_css_add");

			// Is relative
			if (c.indexOf('://') == -1 && c.charAt(0) != '/')
				c = tinyMCE.documentBasePath + "/" + c;

			document.write('<link href="' + c + '" rel="stylesheet" type="text/css">');
		}*/
//the code above modified by Logan
		tinyMCE.addEvent(window, "load", this.onLoad);
	},

	onLoad : function() {
		var dir, i, elms, body = document.body;
//the code below modified by Logan
/*		if (tinyMCE.getWindowArg('mce_replacevariables', true))
			body.innerHTML = tinyMCE.applyTemplate(body.innerHTML, tinyMCE.windowArgs);
*/
	  //the code above modified by Logan
		dir = tinyMCE.selectedInstance.settings['directionality'];
		if (dir == "rtl" && document.forms && document.forms.length > 0) {
			elms = document.forms[0].elements;
			for (i=0; i<elms.length; i++) {
				if ((elms[i].type == "text" || elms[i].type == "textarea") && elms[i].getAttribute("dir") != "ltr")
					elms[i].dir = dir;
			}
		}

		if (body.style.display == 'none')
			body.style.display = 'block';

		// Execute real onload (Opera fix)
		if (tinyMCEPopup.onLoadEval != "")
			eval(tinyMCEPopup.onLoadEval);
	},

	executeOnLoad : function(str) {
		if (tinyMCE.isOpera)
			this.onLoadEval = str;
		else
			eval(str);
	},

	resizeToInnerSize : function() {
		// Netscape 7.1 workaround
		if (this.isWindow && tinyMCE.isNS71) {
			window.resizeBy(0, 10);
			return;
		}

		if (this.isWindow) {
			var doc = document;
			var body = doc.body;
			var oldMargin, wrapper, iframe, nodes, dx, dy;

			if (body.style.display == 'none')
				body.style.display = 'block';

			// Remove margin
			oldMargin = body.style.margin;
			body.style.margin = '0';

			// Create wrapper
			wrapper = doc.createElement("div");
			wrapper.id = 'mcBodyWrapper';
			wrapper.style.display = 'none';
			wrapper.style.margin = '0';

			// Wrap body elements
			nodes = doc.body.childNodes;
			for (var i=nodes.length-1; i>=0; i--) {
				if (wrapper.hasChildNodes())
					wrapper.insertBefore(nodes[i].cloneNode(true), wrapper.firstChild);
				else
					wrapper.appendChild(nodes[i].cloneNode(true));

				nodes[i].parentNode.removeChild(nodes[i]);
			}

			// Add wrapper
			doc.body.appendChild(wrapper);

			// Create iframe
			iframe = document.createElement("iframe");
			iframe.id = "mcWinIframe";
			iframe.src = document.location.href.toLowerCase().indexOf('https') == -1 ? "about:blank" : tinyMCE.settings['default_document'];
			iframe.width = "100%";
			iframe.height = "100%";
			iframe.style.margin = '0';

			// Add iframe
			doc.body.appendChild(iframe);

			// Measure iframe
			iframe = document.getElementById('mcWinIframe');
			dx = tinyMCE.getWindowArg('mce_width') - iframe.clientWidth;
			dy = tinyMCE.getWindowArg('mce_height') - iframe.clientHeight;

			// Resize window
			// tinyMCE.debug(tinyMCE.getWindowArg('mce_width') + "," + tinyMCE.getWindowArg('mce_height') + " - " + dx + "," + dy);
			window.resizeBy(dx, dy);

			// Hide iframe and show wrapper
			body.style.margin = oldMargin;
			iframe.style.display = 'none';
			wrapper.style.display = 'block';
		}
	},

	resizeToContent : function() {
		var isMSIE = (navigator.appName == "Microsoft Internet Explorer");
		var isOpera = (navigator.userAgent.indexOf("Opera") != -1);

		if (isOpera)
			return;

		if (isMSIE) {
			try { window.resizeTo(10, 10); } catch (e) {}

			var elm = document.body;
			var width = elm.offsetWidth;
			var height = elm.offsetHeight;
			var dx = (elm.scrollWidth - width) + 4;
			var dy = elm.scrollHeight - height;

			try { window.resizeBy(dx, dy); } catch (e) {}
		} else {
			window.scrollBy(1000, 1000);
			if (window.scrollX > 0 || window.scrollY > 0) {
				window.resizeBy(window.innerWidth * 2, window.innerHeight * 2);
				window.sizeToContent();
				window.scrollTo(0, 0);
				var x = parseInt(screen.width / 2.0) - (window.outerWidth / 2.0);
				var y = parseInt(screen.height / 2.0) - (window.outerHeight / 2.0);
				window.moveTo(x, y);
			}
		}
	},

	getWindowArg : function(name, default_value) {
		return tinyMCE.getWindowArg(name, default_value);
	},

	restoreSelection : function() {
		if (this.storeSelection) {
			var inst = tinyMCE.selectedInstance;

			inst.getWin().focus();

			if (inst.selectionBookmark)
				inst.selection.moveToBookmark(inst.selectionBookmark);
		}
	},

	execCommand : function(command, user_interface, value) {
		var inst = tinyMCE.selectedInstance;

		this.restoreSelection();
		inst.execCommand(command, user_interface, value);

		// Store selection
		if (this.storeSelection)
			inst.selectionBookmark = inst.selection.getBookmark(true);
	},

	close : function() {
		tinyMCE.closeWindow(window);
	},

	pickColor : function(e, element_id) {
		tinyMCE.selectedInstance.execCommand('mceColorPicker', true, {
			element_id : element_id,
			document : document,
			window : window,
			store_selection : false
		});
	},

	openBrowser : function(element_id, type, option) {
		var cb = tinyMCE.getParam(option, tinyMCE.getParam("file_browser_callback"));
		var url = document.getElementById(element_id).value;

		tinyMCE.setWindowArg("window", window);
		tinyMCE.setWindowArg("document", document);

		// Call to external callback
		if (eval('typeof(tinyMCEPopup.windowOpener.' + cb + ')') == "undefined")
			alert("Callback function: " + cb + " could not be found.");
		else
			eval("tinyMCEPopup.windowOpener." + cb + "(element_id, url, type, window);");
	},

	importClass : function(c) {
		window[c] = function() {};

		for (var n in window.opener[c].prototype)
			window[c].prototype[n] = window.opener[c].prototype[n];

		window[c].constructor = window.opener[c].constructor;
	}

	};

// Setup global instance
var tinyMCEPopup = new TinyMCE_Popup();

tinyMCEPopup.init();




//function below added by logan (cailongqun [at] yahoo [dot] com [dot] cn) from www.phpletter.com
function selectFile(msgNoFileSelected)
{
	var selectedFileRowNum = $('#selectedFileRowNum').val();
  if(selectedFileRowNum != '' && $('#row' + selectedFileRowNum))
  {
	  var win = tinyMCE.getWindowArg("window");
	  // insert information now
	  var url = $('#fileUrl'+selectedFileRowNum).val();
	 if(typeof(TinyMCE_convertURL) != "undefined")
	 {
	 	url = TinyMCE_convertURL(url, null, true);
	 }else
	 {
	 	url = tinyMCE.convertURL(url, null, true);
	 	
	 }
	  //change by abhishek 
	  //alert(url);
	  strr=url.split('uploaded');
	  //alert(strr[1]);
	  nurl='../uploaded'+strr[1];
	 // alert(nurl);
	  win.document.getElementById(tinyMCE.getWindowArg("input")).value = nurl;
	  
	  //for image browsers
	  if(typeof(win.showPreviewImage) != "undefined") 
	  {
	  	//priview image
	  	win.showPreviewImage(nurl, false );
	  } 	  

	  // close popup window
	  tinyMCEPopup.close();	  	
  }else
  {
  	alert(msgNoFileSelected);
  }
  

}



function cancelSelectFile()
{
  // close popup window
  tinyMCEPopup.close();		
}