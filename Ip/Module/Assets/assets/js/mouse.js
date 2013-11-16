LibMouse ={


	getMouseY : function(e) { 
		if (document.all) { // grab the x-y pos.s if browser is IE
			tempY = event.clientY + document.documentElement.scrollTop;
		} else {  // grab the x-y pos.s if browser is NS
			tempY = e.pageY;
		}  
		// catch possible negative values in NS4
		if (tempY < 0){tempY = 0}  
		return tempY;
	},
		 	
	getMouseX : function(e) {
		if (document.all) { // grab the x-y pos.s if browser is IE
			tempX = event.clientX + document.body.scrollLeft;
		} else {  // grab the x-y pos.s if browser is NS
			tempX = e.pageX;
		}  
		// catch possible negative values in NS4
		if (tempX < 0){tempX = 0}
		return tempX;
	}
		


}
