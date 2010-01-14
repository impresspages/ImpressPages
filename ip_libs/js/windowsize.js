/*** Copy Right Information ***
  * Please do not remove following information.
  * Window Size v1.0
  * Author: John J Kim
  * Email: john@frontendframework.com
  * URL: www.FrontEndFramework.com
  * 
  * You are welcome to modify the codes as long as you include this copyright information.
 *****************************/
 
LibWindow = {	
	//Returns an integer representing the width of the browser window (without the scrollbar).
	getWindowWidth : function() {
	
		//IE
		if(!window.innerWidth){//strict mode
			if(!(document.documentElement.clientWidth == 0))
				w = document.documentElement.clientWidth;
			else  //quirks mode
				w = document.body.clientWidth;
		}	
		else  //w3c
			w = window.innerWidth;
		return w;	
		
		//return (document.layers||(document.getElementById&&!document.all)) ? window.outerWidth : (document.all ? document.body.clientWidth : 0);
	},
	
	//Returns an integer representing the height of the browser window (without the scrollbar).
	getWindowHeight : function() {	
	
		//IE
		if(!window.innerWidth){//strict mode
			if(!(document.documentElement.clientWidth == 0))
				h = document.documentElement.clientHeight;			
			else//quirks mode
				h = document.body.clientHeight;			
		}else{ //w3c
			h = window.innerHeight;
		}
		return h;	
	
		//return window.innerHeight ? window.innerHeight :(document.getBoxObjectFor ? Math.min(document.documentElement.clientHeight, document.body.clientHeight) : ((document.documentElement.clientHeight != 0) ? document.documentElement.clientHeight : (document.body ? document.body.clientHeight : 0)));
	},	
	
	//Returns an integer representing the scrollWidth of the window. 
	getScrollWidth : function() {	
		return document.all ? Math.max(Math.max(document.documentElement.offsetWidth, document.documentElement.scrollWidth), document.body.scrollWidth) : (document.body ? document.body.scrollWidth : ((document.documentElement.scrollWidth != 0) ? document.documentElement.scrollWidth : 0));
	},
	
	//Returns an integer representing the scrollHeight of the window. 
	getScrollHeight : function(){		
		return document.all ? Math.max(Math.max(document.documentElement.offsetHeight, document.documentElement.scrollHeight), Math.max(document.body.offsetHeight, document.body.scrollHeight)) : (document.body ? document.body.scrollHeight : ((document.documentElement.scrollHeight != 0) ? document.documentElement.scrollHeight : 0));
	},			
	
	//Returns an integer representing the scrollLeft of the window (the number of pixels the window has scrolled from the left).
	getScrollLeft : function() {
		return document.all ? (!document.documentElement.scrollLeft ? document.body.scrollLeft : document.documentElement.scrollLeft) : ((window.pageXOffset != 0) ? window.pageXOffset : 0);
	},
	
	//Returns an integer representing the scrollTop of the window (the number of pixels the window has scrolled from the top).
	getScrollTop : function() {
		return document.all ? (!document.documentElement.scrollTop ? document.body.scrollTop : document.documentElement.scrollTop) : ((window.pageYOffset != 0) ? window.pageYOffset : 0);
	}
}