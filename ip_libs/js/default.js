LibDefault = {

  cancelBubbling : function (e){
    if (window.event) {
      window.event.cancelBubble = true;
    }
    else {
      e.stopPropagation();
    }
  },

  //LibDefault.addEvent(window, 'load', load);
  addEvent : function(obj, evType, fn){
    if (obj.addEventListener){
      obj.addEventListener(evType, fn, false);
      return true;
    } else if (obj.attachEvent){
      var r = obj.attachEvent("on"+evType, fn);
      return r;
    } else {
      return false;
    }
  },

  removeEvent : function ( obj, evType, fn ) {
    if (obj.removeEventListener){
      obj.removeEventListener(evType, fn, false);
      return true;
    } else if (obj.attachEvent){
      var r = obj.detachEvent("on"+evType, fn);
      return r;
    } else {
      return false;
    }
  },

  // example
  // LibDefault.ajaxMessage('http://www.yoursite.com', 'action=' + encodeURIComponent(do_action) + '&var2=val2&....')
  ajaxMessage : function(url, parameters, responseFunction){
    var xmlHttp;
    try	{// Firefox, Opera 8.0+, Safari
      xmlHttp=new XMLHttpRequest();
    }catch (e){// Internet Explorer
      try{
        xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
      }catch (e){
        try{
          xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        catch (e){
          alert("Your browser does not support AJAX!");
          return false;
        }
      }
    }
    xmlHttp.onreadystatechange=function()
    {
      if(xmlHttp.readyState==4){
        var response = xmlHttp.responseText;
        if (responseFunction) {
          responseFunction (response);
        } else {
          eval(response);
        }
      }
    }

    xmlHttp.open("POST",url, true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
    //xmlHttp.setRequestHeader("Content-length", parameters.length);
    //xmlHttp.setRequestHeader("Connection", "close");
    xmlHttp.send(parameters);
  },

  switchDisplay : function(id, type){
    var el = document.getElementById(id);
    if(el.style.display != type){
      el.style.display = type;
    }else{
      el.style.display = 'none';
    }
  },

  switchHTML : function(id, html1, html2){
    var el = document.getElementById(id);
    if(el.innerHTML != html1){
      el.innerHTML = html1;
    }else{
      el.innerHTML = html2;
    }
  },

  getPositionTop : function(element){
    var offset = 0;
    while(element) {
      offset += element["offsetTop"];
      element = element.offsetParent;
    }
    return offset;
  },

  /* Is a given element visible or not? */
  isElementVisible : function(eltId) {
    var elt = document.getElementById(eltId);
    if (!elt) {
        // Element not found.
        return false;
    }
    // Get the top and bottom position of the given element.
    var posTop = LibDefault.getPositionTop(elt);
    var posBottom = posTop + elt.offsetHeight;
    // Get the top and bottom position of the *visible* part of the window.
    var visibleTop = document.documentElement.scrollTop;
    var visibleBottom = visibleTop + document.documentElement.offsetHeight;
    return ((posBottom >= visibleTop) && (posTop <= visibleBottom));
  },

  formPostAnswer : function(uniqueName){

    if(window.frames[uniqueName].new_fields){
      var new_fields = window.frames[uniqueName].new_fields;
      for(var i=0; i<new_fields.length; i++){
        if (eval("typeof " + uniqueName + '_reset' + " == 'function'")) {
          eval(uniqueName + '_replace_input(\'' + new_fields[i][0] + '\', \'' + new_fields[i][1] + '\');');
        } else {
          LibDefault.formReplaceInput(uniqueName, new_fields[i][0], new_fields[i][1]);
        }
      }
    }

    var first = true;

    if(window.frames[uniqueName].fields){
      var fields = window.frames[uniqueName].fields;
      for(var i=0; i<fields.length; i++){
        if (eval("typeof " + uniqueName + '_reset' + " == 'function'")) {
          eval(uniqueName + '_reset(\'field_' + fields[i] + '\');');
        } else {
          LibDefault.formReset(uniqueName, fields[i]);
        }
      }
    }

    if(window.frames[uniqueName].global_error){
      if (eval("typeof " + uniqueName + '_set_global_error' + " == 'function'")) {
        eval(uniqueName + '_set_global_error(\'' + window.frames[uniqueName].global_error + '\', ' + first + ');');
      } else {
        LibDefault.formSetGlobalError(uniqueName, window.frames[uniqueName].global_error, first)
      }
      first = false;
    }

    if(window.frames[uniqueName].errors){
      var errors = window.frames[uniqueName].errors;
      for(var i=0; i<errors.length; i++){
        if (eval("typeof " + uniqueName + '_set_error' + " == 'function'")) {
          eval(uniqueName + '_set_error(\'' + errors[i][0] + '\', \'' + errors[i][1] + '\', '+ first + ');');
        } else {
          LibDefault.formSetError(uniqueName, errors[i][0], errors[i][1], first)
        }
        first = false;
      }
    }

    if(window.frames[uniqueName].script){
      eval(window.frames[uniqueName].script);
    }
  },

  formBeforePost : function(form, uniqueName) {
    if(!document.getElementById(uniqueName + '_iframe')) {
      var newDiv = document.createElement("div");
      newDiv.innerHTML = '<iframe id="' + uniqueName + '_iframe" onload="LibDefault.formPostAnswer(\'' + uniqueName + '\')" name="' + uniqueName + '" width="0" height="0" frameborder="0">Your browser does not support iframes.</iframe>';
      document.body.appendChild(newDiv);
    }
    form.setAttribute('target', uniqueName);
    form.submit();
  },

  formReset : function(uniqueName, field_name){
    if(document.getElementById(uniqueName + '_field_' + field_name)) {
      document.getElementById(uniqueName + '_field_' + field_name).className = "libPhpFormField";
    }
    if(document.getElementById(uniqueName + '_field_' + field_name + '_error')) {
      document.getElementById(uniqueName + '_field_' + field_name + '_error').innerHTML = '';
    document.getElementById(uniqueName + '_field_' + field_name + '_error').style.display = 'none';
    }
    if(document.getElementById(uniqueName + '_global_error')) {
      document.getElementById(uniqueName + '_global_error').style.display = 'none';
    }
  },

  formSetError : function(uniqueName, field_name, error, first){
    document.getElementById(uniqueName + '_field_' + field_name).className = "libPhpFormFieldError";
    if(error != ''){
      document.getElementById(uniqueName + '_field_' + field_name + '_error').innerHTML = error;
      document.getElementById(uniqueName + '_field_' + field_name + '_error').style.display = 'block';
    }

    if(first && ! LibDefault.isElementVisible(uniqueName + '_field_' + field_name)){
      document.location = '#' + uniqueName + '_field_' + field_name + '_error_anchor';
    }
  },

  formSetGlobalError : function(uniqueName, error, first){
    document.getElementById(uniqueName + '_global_error').innerHTML = error;
    document.getElementById(uniqueName + '_global_error').style.display = 'block';
    if(first && ! LibDefault.isElementVisible(uniqueName + '_global_error')){
      document.location = '#' + uniqueName + '_global_error_anchor';
    }
  },

  formReplaceInput : function(uniqueName, field_name, new_html){
    if(document.getElementById(uniqueName + '_field_' + field_name + '_input')) {
      document.getElementById(uniqueName + '_field_' + field_name + '_input').innerHTML = new_html;
    }
  }

}
