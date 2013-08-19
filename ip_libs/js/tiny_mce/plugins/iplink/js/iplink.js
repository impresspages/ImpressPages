/* Functions for the iplink plugin popup */

tinyMCEPopup.requireLangPack();

var templates = {
	"window.open" : "window.open('${url}','${target}','${options}')"
};

function preinit() {
	var url;

	if (url = tinyMCEPopup.getParam("external_link_list_url"))
		document.write('<script language="javascript" type="text/javascript" src="' + tinyMCEPopup.editor.documentBaseURI.toAbsolute(url) + '"></script>');
}

function changeClass() {
	var f = document.forms[0];

	f.classes.value = getSelectValue(f, 'classlist');
}

function init() {
	tinyMCEPopup.resizeToInnerSize();

	var formObj = document.forms[0];
	var inst = tinyMCEPopup.editor;
	var elm = inst.selection.getNode();
	var action = "insert";
	var html;

	document.getElementById('hrefbrowsercontainer').innerHTML = getBrowserHTML('hrefbrowser','href','file','iplink');
	document.getElementById('targetlistcontainer').innerHTML = getTargetListHTML('targetlist','target');

	elm = inst.dom.getParent(elm, "A");
	if (elm != null && elm.nodeName == "A")
		action = "update";

	formObj.insert.value = tinyMCEPopup.getLang(action, 'Insert', true); 

	if (action == "update") {
		var href = inst.dom.getAttrib(elm, 'href');
		var onclick = inst.dom.getAttrib(elm, 'onclick');

		// Setup form data
		setFormValue('href', href);
		setFormValue('title', inst.dom.getAttrib(elm, 'title'));
		setFormValue('target', inst.dom.getAttrib(elm, 'target'));
		setFormValue('classes', inst.dom.getAttrib(elm, 'class'));

		addClassesToList('classlist', 'iplink_styles');

		selectByValue(formObj, 'classlist', inst.dom.getAttrib(elm, 'class'), true);
		selectByValue(formObj, 'targetlist', inst.dom.getAttrib(elm, 'target'), true);
	} else {
		addClassesToList('classlist', 'iplink_styles');
	}

  openIPlinks();
}

function checkPrefix(n) {
	if (n.value && Validator.isEmail(n) && !/^\s*mailto:/i.test(n.value) && confirm(tinyMCEPopup.getLang('iplink_dlg.is_email')))
		n.value = 'mailto:' + n.value;

	if (/^\s*www\./i.test(n.value) && confirm(tinyMCEPopup.getLang('iplink_dlg.is_external')))
		n.value = 'http://' + n.value;
}

function setFormValue(name, value) {
	document.forms[0].elements[name].value = value;
}

function parseFunction(onclick) {
	var formObj = document.forms[0];
	var onClickData = parseLink(onclick);

	// TODO: Add stuff here
}

function getOption(opts, name) {
	return typeof(opts[name]) == "undefined" ? "" : opts[name];
}

function parseLink(link) {
	link = link.replace(new RegExp('&#39;', 'g'), "'");

	var fnName = link.replace(new RegExp("\\s*([A-Za-z0-9\.]*)\\s*\\(.*", "gi"), "$1");

	// Is function name a template function
	var template = templates[fnName];
	if (template) {
		// Build regexp
		var variableNames = template.match(new RegExp("'?\\$\\{[A-Za-z0-9\.]*\\}'?", "gi"));
		var regExp = "\\s*[A-Za-z0-9\.]*\\s*\\(";
		var replaceStr = "";
		for (var i=0; i<variableNames.length; i++) {
			// Is string value
			if (variableNames[i].indexOf("'${") != -1)
				regExp += "'(.*)'";
			else // Number value
				regExp += "([0-9]*)";

			replaceStr += "$" + (i+1);

			// Cleanup variable name
			variableNames[i] = variableNames[i].replace(new RegExp("[^A-Za-z0-9]", "gi"), "");

			if (i != variableNames.length-1) {
				regExp += "\\s*,\\s*";
				replaceStr += "<delim>";
			} else
				regExp += ".*";
		}

		regExp += "\\);?";

		// Build variable array
		var variables = [];
		variables["_function"] = fnName;
		var variableValues = link.replace(new RegExp(regExp, "gi"), replaceStr).split('<delim>');
		for (var i=0; i<variableNames.length; i++)
			variables[variableNames[i]] = variableValues[i];

		return variables;
	}

	return null;
}

function parseOptions(opts) {
	if (opts == null || opts == "")
		return [];

	// Cleanup the options
	opts = opts.toLowerCase();
	opts = opts.replace(/;/g, ",");
	opts = opts.replace(/[^0-9a-z=,]/g, "");

	var optionChunks = opts.split(',');
	var options = [];

	for (var i=0; i<optionChunks.length; i++) {
		var parts = optionChunks[i].split('=');

		if (parts.length == 2)
			options[parts[0]] = parts[1];
	}

	return options;
}

function setAttrib(elm, attrib, value) {
	var formObj = document.forms[0];
	var valueElm = formObj.elements[attrib.toLowerCase()];
	var dom = tinyMCEPopup.editor.dom;

	if (typeof(value) == "undefined" || value == null) {
		value = "";

		if (valueElm)
			value = valueElm.value;
	}

	// Clean up the style
	if (attrib == 'style')
		value = dom.serializeStyle(dom.parseStyle(value));

	dom.setAttrib(elm, attrib, value);
}

function insertAction() {
	var inst = tinyMCEPopup.editor;
	var elm, elementArray, i;

	elm = inst.selection.getNode();
	checkPrefix(document.forms[0].href);

	elm = inst.dom.getParent(elm, "A");

	// Remove element if there is no href
	if (!document.forms[0].href.value) {
		tinyMCEPopup.execCommand("mceBeginUndoLevel");
		i = inst.selection.getBookmark();
		inst.dom.remove(elm, 1);
		inst.selection.moveToBookmark(i);
		tinyMCEPopup.execCommand("mceEndUndoLevel");
		tinyMCEPopup.close();
		return;
	}

	tinyMCEPopup.execCommand("mceBeginUndoLevel");

	// Create new anchor elements
	if (elm == null) {
		inst.getDoc().execCommand("unlink", false, null);
		tinyMCEPopup.execCommand("CreateLink", false, "#mce_temp_url#", {skip_undo : 1});

		elementArray = tinymce.grep(inst.dom.select("a"), function(n) {return inst.dom.getAttrib(n, 'href') == '#mce_temp_url#';});
		for (i=0; i<elementArray.length; i++)
			setAllAttribs(elm = elementArray[i]);
	} else
		setAllAttribs(elm);

	// Don't move caret if selection was image
	if (elm.childNodes.length != 1 || elm.firstChild.nodeName != 'IMG') {
		inst.focus();
		inst.selection.select(elm);
		inst.selection.collapse(0);
		tinyMCEPopup.storeSelection();
	}

	tinyMCEPopup.execCommand("mceEndUndoLevel");
	tinyMCEPopup.close();
}

function setAllAttribs(elm) {
	var formObj = document.forms[0];
	var href = formObj.href.value;
	var target = getSelectValue(formObj, 'targetlist');

	setAttrib(elm, 'href', href);
	setAttrib(elm, 'title');
	setAttrib(elm, 'target', target == '_self' ? '' : target);
	setAttrib(elm, 'class', getSelectValue(formObj, 'classlist'));

	// Refresh in old MSIE
	if (tinyMCE.isMSIE5)
		elm.outerHTML = elm.outerHTML;
}

function getSelectValue(form_obj, field_name) {
	var elm = form_obj.elements[field_name];

	if (!elm || elm.options == null || elm.selectedIndex == -1)
		return "";

	return elm.options[elm.selectedIndex].value;
}

function getTargetListHTML(elm_id, target_form_element) {
	var targets = tinyMCEPopup.getParam('theme_advanced_link_targets', '').split(';');
	var html = '';

	html += '<select id="' + elm_id + '" name="' + elm_id + '" onf2ocus="tinyMCE.addSelectAccessibility(event, this, window);" onchange="this.form.' + target_form_element + '.value=';
	html += 'this.options[this.selectedIndex].value;">';
	html += '<option value="_self">' + tinyMCEPopup.getLang('iplink_dlg.target_same') + '</option>';
	html += '<option value="_blank">' + tinyMCEPopup.getLang('iplink_dlg.target_blank') + ' (_blank)</option>';
	html += '<option value="_parent">' + tinyMCEPopup.getLang('iplink_dlg.target_parent') + ' (_parent)</option>';
	html += '<option value="_top">' + tinyMCEPopup.getLang('iplink_dlg.target_top') + ' (_top)</option>';

	for (var i=0; i<targets.length; i++) {
		var key, value;

		if (targets[i] == "")
			continue;

		key = targets[i].split('=')[0];
		value = targets[i].split('=')[1];

		html += '<option value="' + key + '">' + value + ' (' + key + ')</option>';
	}

	html += '</select>';

	return html;
}

function openIPlinks() {
  //+ po default'u sukti div'e gif'a, kad content is loading
  //+ jeigu atidaromas popup'as - kvieciam ajax'a, kad paduotu sitemap'a
  //+ kai gaunam atsakyma - tikrinam ar turinys geras
  //+ jeigu turinys geras - paduodam html'a i div'a, modifikuojam a tag'us ir iskvieciam medi sudaranti js'a
  //+ jeigu turinys blogas - paduodam klaidos pranesima
  var href = document.getElementById('href').value;
//  LibDefault.ajaxMessage(parent.global_config_modules_url+'standard/content_management/sitemap_list.php', 'action=sitemap_list&current_href=' + encodeURIComponent(href), updateIPlinks)

  //LibDefault.ajaxMessage(parent.window.location, 'g=standard&m=content_management&ba=getSitemapInList&securityToken=' + ip.securityToken + '&current_href=' + encodeURIComponent(href), updateIPlinks)

    $.ajax({
            type: 'GET',
            url : parent.window.location,
            data : {
                g : 'standard',
                m : 'content_management',
                ba : 'getSitemapInList',
                current_href : href
            },
            success : function(response) {console.log(response); updateIPlinks(response);}
    });




}

function updateIPlinks(content) {
  if (content.indexOf("ipSitemap")!=-1) {
    // ok
    var html = '';
    html += '<div>';
    html += content;
    html += '</div>';
    document.getElementById('ipbrowsercontainer').innerHTML = html;
    sitemapFunctions("ipSitemap");
    sitemapStyler("ipSitemap");
  } else {
    // rezultatas nedave listo
    document.getElementById('ipbrowsercontainer').innerHTML = 'Something bad happened.';
    alert(content);
  }
}

function updateHref(obj) {
  document.getElementById('href').value = obj.href;
  return false;
}

function sitemapFunctions(objID) {
  var sitemap = document.getElementById(objID);
  if(sitemap){

    var anchors = sitemap.getElementsByTagName("a");
    for(var i=0;i<anchors.length;i++){
      anchors[i].onclick = function(){
        document.getElementById('href').value = this.href;
        return false;
      }
    }

  }
}

function sitemapStyler(objID) {
  // Author: Alen Grakalic

  var sitemap = document.getElementById(objID);
  if(sitemap){

    this.listItem = function(li){
      if(li.getElementsByTagName("ul").length > 0){
        var ul = li.getElementsByTagName("ul")[0];
        ul.style.display = "none";
        var span = document.createElement("span");
        span.className = "collapsed";
        span.onclick = function(){
          ul.style.display = (ul.style.display == "none") ? "block" : "none";
          this.className = (ul.style.display == "none") ? "collapsed" : "expanded";
        }
        li.appendChild(span);
      }
    }

    var items = sitemap.getElementsByTagName("li");
    for(var i=0;i<items.length;i++){
      listItem(items[i]);
    }

  }
}



// While loading
preinit();
tinyMCEPopup.onInit.add(init);
