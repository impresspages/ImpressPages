/*Process = {



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

		xmlHttp.open("POST", url, true);
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
		xmlHttp.setRequestHeader("Content-length", parameters.length);
		xmlHttp.setRequestHeader("Connection", "close");
		xmlHttp.send(parameters);
	},
	
	getResponse : function(response){
	  if (response == '') {
	    processStep++;
	    this.ajaxMessage(processURL, 'processStep=' + processStep);
	  } else {
	    var content = document.getElementById('instructions');
	    content.innerHTML = response;
	  }
	  document.getElementById();
	}
	
	
}*/