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
    xmlHttp.setRequestHeader("Content-length", parameters.length);
    xmlHttp.setRequestHeader("Connection", "close");
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


  formPostAnswer : function(uniqueName){

    if(window.frames[uniqueName].new_fields){
      var new_fields = window.frames[uniqueName].new_fields;
      for(var i=0; i<new_fields.length; i++){
        eval(uniqueName + '_replace_input(\'' + new_fields[i][0] + '\', \'' + new_fields[i][1] + '\');');
      }
    }

    var first = true;


    var i = 1;

    while (document.getElementById(uniqueName + '_field_' + 'field_' + i )) {
      eval(uniqueName + '_reset(\'field_' + i + '\');');
      i++;
    }

    if(window.frames[uniqueName].global_error){
      eval(uniqueName + '_set_global_error(\'' + window.frames[uniqueName].global_error + '\', ' + first + ');');
      first = false;
    }

    if(window.frames[uniqueName].errors){
      var errors = window.frames[uniqueName].errors;
      for(var i=0; i<errors.length; i++){
        eval(uniqueName + '_set_error(\'' + errors[i][0] + '\', \'' + errors[i][1] + '\', '+ first + ');');
        first = false;
      }
    }

    if(window.frames[uniqueName].script){
      eval(window.frames[uniqueName].script);
    }
  },

  formBeforePost : function(form, uniqueName) {
    if (!window.frames[uniqueName]) {
      var newDiv = document.createElement("DIV");
      newDiv.innerHTML = '<iframe onload="LibDefault.formPostAnswer(\'' + uniqueName + '\')" name="' + uniqueName + '" width="0" height="0" frameborder="0">Your browser does not support iframes.</iframe>';
      document.body.appendChild(newDiv);
      form.setAttribute('target', uniqueName);
    }
    form.submit();
  }

}
