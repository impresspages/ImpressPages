LibAdvanced ={

	getY: function( oElement ){
		var iReturnValue = 0;
		while( oElement != null ) {
			iReturnValue += oElement.offsetTop;
			oElement = oElement.offsetParent;
		}
		return iReturnValue;
	},

	getX : function( oElement ){
		var iReturnValue = 0;
		while( oElement != null ) {
			iReturnValue += oElement.offsetLeft;
			oElement = oElement.offsetParent;
		}
		return iReturnValue;
	},
			
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
	},
			
		  

	window_resize_recalc : function(){
		//get window width height
			var myWidth = 0, myHeight = 0;		
			if( typeof( window.innerWidth ) == 'number' ) {
				//Non-IE
				myWidth = window.innerWidth;
				myHeight = window.innerHeight;
			} else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
				//IE 6+ in 'standards compliant mode'
				myWidth = document.documentElement.clientWidth;
				myHeight = document.documentElement.clientHeight;
			}
		//eof get window width height	
	}


}
