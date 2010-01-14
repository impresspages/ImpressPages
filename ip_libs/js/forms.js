LibForms = {


	selectValue : function(select_id, value){
	  var element = document.getElementById(select_id);
	  if(element){
	    var options = element.options;
	    for(var i=0; i<options.length; i++){
	      if(options[i].value == value){
	        element.selectedIndex = i;
	        return;
	      }
	    }
	  }
	}

	

}
