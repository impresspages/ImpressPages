function WindowSize()
 {
  var x = 0, y = 0;
  if( typeof( window.innerWidth ) == 'number' )
   {//Non-IE
    x = window.innerWidth;
    y = window.innerHeight;
   }
  else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) )
   {//IE 6+ in 'standards compliant mode'
    x = document.documentElement.clientWidth;
    y = document.documentElement.clientHeight;
   }
  else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) )
   {//IE 4 compatible
    x = document.body.clientWidth;
    y = document.body.clientHeight;
   }
  return {x:x, y:y};
 }

function resizinam()
{
	if(document.getElementById("treeView")){
	  document.getElementById("bodyView").style.width = WindowSize().x - document.getElementById("treeView").offsetWidth - 15 + "px";  
	  document.getElementById("treeView").style.height = WindowSize().y - 20 + "px";
	  document.getElementById("splitterBar").style.height = WindowSize().y - 20 + "px";
	  document.getElementById("content").style.height = WindowSize().y - 21 + "px";//WindowSize().y - 79 - document.getElementById("backtrace_path").offsetHeight + "px";
	  //document.getElementById("sheet1").style.width = WindowSize().x - document.getElementById("treeView").offsetWidth - 36 + "px";
	}else{
	  document.getElementById("bodyView").style.width = WindowSize().x - 15 + "px";  
	  document.getElementById("bodyView").style.marginLeft = "6px";  
	  document.getElementById("content").style.height = WindowSize().y - 21 + "px";
	}
}
 
function perVisaPloti()
{
	if(document.getElementById("treeView")){	
	  document.getElementById("treeView").style.width = 250 + "px";
	  document.getElementById("bodyView").style.width = WindowSize().x - document.getElementById("treeView").offsetWidth - 15 + "px";  
	  document.getElementById("treeView").style.height = WindowSize().y - 20 + "px";
	  document.getElementById("splitterBar").style.height = WindowSize().y - 20 + "px";
	  document.getElementById("content").style.height = WindowSize().y - 21 + "px";//WindowSize().y - 79 - document.getElementById("backtrace_path").offsetHeight + "px";
	 // document.getElementById("sheet1").style.width = WindowSize().x - document.getElementById("treeView").offsetWidth - 36 + "px";
	}else{
	  document.getElementById("bodyView").style.width = WindowSize().x  - 15 + "px";  
	  document.getElementById("bodyView").style.marginLeft = "6px";  
	  document.getElementById("content").style.height = WindowSize().y - 21 + "px";//WindowSize().y - 79 - document.getElementById("backtrace_path").offsetHeight + "px";
	}
}

var curwidth = 0;
var curX = 0;
var newX = 0;
var mouseButtonPos = "up";
var curwidth1 = 0;

/*
var def_select = document.onselectstart;
var def_down = document.onmousedown;
*/

//Function 'getPos(...) gets the original div width.

function getPos(e)
 {
  //For handling events in ie vs. w3c.
  curEvent = ((typeof event == "undefined")? e: event);
  //Sets mouse flag as down.
  mouseButtonPos = "down";
  //Gets position of click.
  curX = curEvent.clientX;
  //Get the width of the div.
  curwidth = document.getElementById("treeView").offsetWidth;
 
  curwidth1 = document.getElementById("bodyView").offsetWidth - document.getElementById("splitterBar").offsetWidth + 1;
/*
  if( mouseButtonPos == "down" )
   {
	   document.onselectstart = function () { return false; } // ie
	   document.onmousedown = function () { return false; } // mozilla
	  }
	 else
   {
    document.onselectstart = def_select;
    document.onmousedown = def_down;
   }
*/
/*document.blur();*/

 }
 
//Function setPos(...) changes the width of the div while the mouse button is pressed.
function setPos(e)
 {
  if( mouseButtonPos == "down" )
   {
    //For handling events in ie vs. w3c.
    curEvent = ((typeof event == "undefined")? e: event);
    //Get new mouse position.
    newX = curEvent.clientX;
    //Calculate movement in pixels.
    var pixelMovement = parseInt(newX - curX);
    //Determine new width.
    var newwidth = parseInt(curwidth + pixelMovement);
    //Enforce a minimum width.
    newwidth = ((newwidth < 10)? 10: newwidth);
    //Enforce a maximum width.
    newwidth = ((newwidth > (WindowSize().x - 500))? (WindowSize().x - 500): newwidth);
    if((newwidth > 10) && (newwidth < (WindowSize().x - 500)))
     {
      if(pixelMovement > 0)
       {
        //Set the new width.
        document.getElementById("treeView").style.width = newwidth + "px";
        //Set the new left of the splitter bar.
        document.getElementById("splitterBar").style.left = newwidth + "px";
        //document.getElementById("splitterBar").style.left = parseInt(document.getElementById("treeView").style.width) + 10;
     
        document.getElementById("bodyView").style.width = curwidth1 - pixelMovement + "px";
       }
      else
       {
        document.getElementById("bodyView").style.width = curwidth1 - pixelMovement + "px";
       
        //Set the new width.
        document.getElementById("treeView").style.width = newwidth + "px";
        //Set the new left of the splitter bar.
        document.getElementById("splitterBar").style.left = newwidth + "px";
        //document.getElementById("splitterBar").style.left = parseInt(document.getElementById("treeView").style.width) + 10;
       }
     }
   }
 }

//window.onresize = resizinam();
//document.onresize = resizinam();
//window.onload = perVisaPloti();
//div.onresize = function() {alert("size changed!")};

function addEvent(obj, evType, fn){ 
 if (obj.addEventListener){ 
   obj.addEventListener(evType, fn, false); 
   return true; 
 } else if (obj.attachEvent){ 
   var r = obj.attachEvent("on"+evType, fn); 
   return r; 
 } else { 
   return false; 
 } 
}

addEvent(window, 'resize', resizinam);
