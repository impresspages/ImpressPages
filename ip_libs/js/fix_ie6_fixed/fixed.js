// fixed.js: fix fixed positioning and fixed backgrounds in IE/Win
// version 1.8, 08-Aug-2003
// written by Andrew Clover <and@doxdesk.com>, use freely

/*@cc_on
@if (@_win32 && @_jscript_version>4)

var fixed_positions= new Array();
var fixed_backgrounds= new Array();
var fixed_viewport;

// Initialisation. Called when the <body> tag arrives. Set up viewport so the
// rest of the script knows we're going, and add a measurer div, used to detect
// font size changes and measure image sizes for backgrounds later   

function fixed_init() {
  fixed_viewport= (document.compatMode=='CSS1Compat') ?
    document.documentElement : document.body;
  var el= document.createElement('div');
  el.setAttribute('id', 'fixed-measure');
  el.style.position= 'absolute';
  el.style.top= '0'; el.style.left= '0';
  el.style.overflow= 'hidden'; el.style.visibility= 'hidden';
  el.style.fontSize= 'xx-large'; el.style.height= '5em';
  el.style.setExpression('width', 'fixed_measureFont()');
  document.body.insertBefore(el, document.body.firstChild);
}

// Binding. Called every time an element is added to the document, check it
// for fixed features, if found add to our lists and set initial props   

function fixed_bind(el) {
  var needLayout= false;
  var tag= el.tagName.toLowerCase();
  var st= el.style;
  var cst= el.currentStyle;
  var anc;

  // find fixed-position elements
  if (cst.position=='fixed') {
    needLayout= true;
    fixed_positions[fixed_positions.length]= el;
    // store original positioning as we'll overwrite it
    st.position= 'absolute';
    st.fixedPLeft=   cst.left;
    st.fixedPTop=    cst.top;
    st.fixedPRight=  cst.right;
    st.fixedPBottom= cst.bottom;
    st.fixedPWidth=  fixed_parseLength(cst.width);
    st.fixedPHeight= fixed_parseLength(cst.height);
    // find element that will act as containing box, for convenience later
    st.fixedCB= null;
    for (anc= el; (anc= anc.parentElement).parentElement;) {
      if (anc.currentStyle.position!='static') {
        st.fixedCB= anc;
        break;
    } }
    // detect nested fixed positioning (only ancestor need move)
    st.fixedNest= false;
    for (anc= el; anc= anc.parentElement;) {
      if (anc.style.fixedNest!=null)
        st.fixedNest= true;
        break;
    }
  }

  // find fixed-background elements (not body/html which IE already gets right)
  if (cst.backgroundAttachment=='fixed' && tag!='body' && tag!='html') {
    needLayout= true;
    fixed_backgrounds[fixed_backgrounds.length]= el;
    // get background offset, converting from keyword if necessary
    st.fixedBLeft= fixed_parseLength(cst.backgroundPositionX);
    st.fixedBTop=  fixed_parseLength(cst.backgroundPositionY);
    // if it's a non-zero %age, need to know size of image for layout
    if (st.fixedBLeft[1]=='%' || st.fixedBTop[1]=='%') {
      st.fixedBWidth= 0; st.fixedBHeight= 0;
      fixed_measureBack(el);
    }
  }
  if (needLayout) fixed_layout();
}

// Layout. On every window or font size change, recalculate positioning   

// Request re-layout at next free moment
var fixed_delaying= false;
function fixed_delayout() {
  if (fixed_delaying) return;
  fixed_delaying= true;
  window.setTimeout(fixed_layout, 0);
}

var fixed_ARBITRARY= 200;

function fixed_layout() {
  fixed_delaying= false;
  if (!fixed_viewport) return;
  var i, el, st, j, pr, tmp, A= 'auto';
  var cb, cbLeft, cbTop, cbRight, cbBottom, oLeft, oTop, oRight, oBottom;
  var vpWidth=fixed_viewport.clientWidth, vpHeight=fixed_viewport.clientHeight;

  // calculate initial position for fixed-position elements [black magic]
  for (i= fixed_positions.length; i-->0;) {
    el= fixed_positions[i]; st= el.style;
    // find positioning of containing block
    cb= st.fixedCB; if (!cb) cb= fixed_viewport;
    cbLeft= fixed_pageLeft(cb); cbTop= fixed_pageTop(cb);
    if (cb!=fixed_viewport) { cbLeft+= cb.clientLeft; cbTop+= cb.clientTop; }
    cbRight= fixed_viewport.clientWidth-cbLeft-cb.clientWidth;
    cbBottom= fixed_viewport.clientHeight-cbTop-cb.clientHeight;
    // if size is in %, must recalculate relative to viewport
    if (st.fixedPWidth[1]=='%')
      st.width= Math.round(vpWidth*st.fixedPWidth[0]/100)+'px';
    if (st.fixedPHeight[1]=='%')
      st.height= Math.round(vpHeight*st.fixedPHeight[0]/100)+'px';
    // find out offset values at max size, to account for margins
    st.left= A; st.right= '0'; st.top= A; st.bottom= '0';
    oRight= el.offsetLeft+el.offsetWidth; oBottom= el.offsetTop+el.offsetHeight;
    st.left= '0'; st.right= A; st.top= '0'; st.bottom= A;
    oLeft= el.offsetLeft; oTop= el.offsetTop;
    // use this to convert all edges to pixels
    st.left= A; st.right= st.fixedPRight;
    st.top= A; st.bottom= st.fixedPBottom;
    oRight-= el.offsetLeft+el.offsetWidth;
    oBottom-= el.offsetTop+el.offsetHeight;
    st.left= st.fixedPLeft; st.top= st.fixedPTop;
    oLeft= el.offsetLeft-oLeft; oTop= el.offsetTop-oTop;
    // edge positioning fix
    if (st.fixedPWidth[1]==A && st.fixedPLeft!=A && st.fixedPRight!=A) {
      tmp= el.offsetLeft; st.left= A; st.width= fixed_ARBITRARY+'px';
      tmp= fixed_ARBITRARY+el.offsetLeft-tmp+cbLeft+cbRight;
      st.left= st.fixedPLeft; st.width= ((tmp<1)?1:tmp)+'px';
    }
    if (st.fixedPHeight[1]==A && st.fixedPTop!=A && st.fixedPBottom!=A) {
      tmp= el.offsetTop; st.top= A; st.height= fixed_ARBITRARY+'px';
      tmp= fixed_ARBITRARY+el.offsetTop-tmp+cbTop+cbBottom;
      st.top= st.fixedPTop; st.height= ((tmp<1)?1:tmp)+'px';
    }
    // move all non-auto edges relative to the viewport
    st.fixedCLeft= (st.fixedPLeft=='auto') ? oLeft : oLeft-cbLeft;
    st.fixedCTop= (st.fixedPTop=='auto') ? oTop : oTop-cbTop;
    st.fixedCRight= (st.fixedPRight=='auto') ? oRight : oRight-cbRight;
    st.fixedCBottom= (st.fixedPBottom=='auto') ? oBottom : oBottom-cbBottom;
    // remove left-positioning of right-positioned elements
    if (st.fixedPLeft=='auto' && st.fixedPRight!='auto') st.fixedCLeft= 'auto';
    if (st.fixedPTop=='auto' && st.fixedPBottom!='auto') st.fixedCTop= 'auto';
  }


  // calculate initial positioning of fixed backgrounds
  for (i= fixed_backgrounds.length; i-->0;) {
    el= fixed_backgrounds[i]; st= el.style;
    tmp= st.fixedBImage;
    if (tmp) {
      if (tmp.readyState!='uninitialized') {
        st.fixedBWidth= tmp.offsetWidth;
        st.fixedBHeight= tmp.offsetHeight;
        st.fixedBImage= window.undefined;
      }
    }
    st.fixedBX= fixed_length(el, st.fixedBLeft, vpWidth-st.fixedBWidth);
    st.fixedBY= fixed_length(el, st.fixedBTop, vpHeight-st.fixedBHeight);
  }

  // now call scroll() to set the positions from the values just calculated
  fixed_scroll();
}

// Scrolling. Offset fixed elements relative to viewport scrollness

var fixed_lastX, fixed_lastY;
var fixed_PATCHDELAY= 300;
var fixed_patching= false;

// callback function after a scroll, because incorrect scroll position is
// often reported first go!
function fixed_patch() {
  fixed_patching= false;
  var scrollX= fixed_viewport.scrollLeft, scrollY= fixed_viewport.scrollTop;
  if (scrollX!=fixed_lastX && scrollY!=fixed_lastY) fixed_scroll();
}

function fixed_scroll() {
  if (!fixed_viewport) return;
  var i, el, st, viewportX, viewportY;
  var scrollX= fixed_viewport.scrollLeft, scrollY= fixed_viewport.scrollTop;
  fixed_lastX= scrollX; fixed_lastY= scrollY;

  // move non-nested fixed-position elements
  for (i= fixed_positions.length; i-->0;) {
    st= fixed_positions[i].style;
    viewportX= (st.fixedNest) ? 0 : scrollX;
    viewportY= (st.fixedNest) ? 0 : scrollY;
    if (st.fixedCLeft!='auto') st.left= (st.fixedCLeft+viewportX)+'px';
    if (st.fixedCTop!='auto') st.top= (st.fixedCTop+viewportY)+'px';
    viewportX= (st.fixedCB==null || st.fixedCB==fixed_viewport) ? 0 : viewportX;
    viewportY= (st.fixedCB==null || st.fixedCB==fixed_viewport) ? 0 : viewportY;
    st.right= (st.fixedCRight-viewportX+1)+'px'; st.right= (st.fixedCRight-viewportX)+'px';
    st.bottom= (st.fixedCBottom-viewportY+1)+'px'; st.bottom= (st.fixedCBottom-viewportY)+'px';
  }

  // align fixed backgrounds to viewport
  for (i= fixed_backgrounds.length; i-->0;) {
    el= fixed_backgrounds[i]; st= el.style;
    viewportX= scrollX;
    viewportY= scrollY;
    while (el.offsetParent) {
      viewportX-= el.offsetLeft+el.clientLeft;
      viewportY-= el.offsetTop +el.clientTop;
      el= el.offsetParent;
    }
    st.backgroundPositionX= (st.fixedBX+viewportX)+'px';
    st.backgroundPositionY= (st.fixedBY+viewportY)+'px';
  }

  // call back again in a tic
  if (!fixed_patching) {
    fixed_patching= true;
    window.setTimeout(fixed_patch, fixed_PATCHDELAY);
  }
}

// Measurement. Load bg-image into an invisible element on the page, when
// loaded write the width/height to an element's style for layout use; detect
// when font size changes

function fixed_measureBack(el) {
  var measure= document.getElementById('fixed-measure');
  var img= document.createElement('img');
  img.setAttribute('src', fixed_parseURL(el.currentStyle.backgroundImage));
  measure.appendChild(img);
  el.style.fixedBImage= img;
  if (img.readyState=='uninitialized')
    img.attachEvent('onreadystatechange', fixed_measureBackImage_ready);
}

function fixed_measureBackImage_ready() {
  var img= event.srcElement;
  if (img && img.readyState!='uninitialized') {
    img.detachEvent('onreadystatechange', fixed_measureBackImage_ready);
    fixed_layout();
  }
}

var fixed_fontsize= 0;
function fixed_measureFont() {
  var fs= document.getElementById('fixed-measure').offsetHeight;
  if (fixed_fontsize!=fs && fixed_fontsize!=0)
    fixed_delayout();
  fixed_fontsize= fs;
  return '5em';
}

// Utility. General-purpose functions

// parse url() to get value inside

function fixed_parseURL(v) {
  v= v.substring(4, v.length-1);
  if (v.charAt(0)=='"' && v.charAt(v.length-1)=='"' ||
      v.charAt(0)=="'" && v.charAt(v.length-1)=="'")
    return v.substring(1, v.length-1);
  else return v;
}

// parse length or auto or background-position keyword into number and unit

var fixed_numberChars= '+-0123456789.';
var fixed_ZERO= new Array(0, 'px');
var fixed_50PC= new Array(50, '%');
var fixed_100PC= new Array(100, '%');
var fixed_AUTO= new Array(0, 'auto');

function fixed_parseLength(v) {
  var num, i;
  if (v=='left'  || v=='top')    return fixed_ZERO;
  if (v=='right' || v=='bottom') return fixed_100PC;
  if (v=='center') return fixed_50PC;
  if (v=='auto')   return fixed_AUTO;
  i= 0;
  while (i<v.length && fixed_numberChars.indexOf(v.charAt(i))!=-1)
    i++;
  num= parseFloat(v.substring(0, i));
  if (num==0) return fixed_ZERO;
  else return new Array(num, v.substring(i));
}

// convert parsed (number, unit) into a number of pixels

function fixed_length(el, l, full) {
  var tmp, x;
  if (l[1]=='px') return l[0];
  if (l[1]=='%')  return Math.round(full*l[0]/100);
  // other units - measure by setting position; this is rather inefficient
  // but then these units are used for background-position so seldom...
  tmp= el.currentStyle.left;
  el.style.left= '0';
  x= el.offsetLeft;
  el.style.left= l[0]+l[1];
  x= el.offsetLeft-x;
  el.style.left= tmp;
  return x;
}

// convert stupid IE offsetLeft/Top to page-relative values

function fixed_pageLeft(el) {
  var v= 0;
  while (el.offsetParent) {
    v+= el.offsetLeft;
    el= el.offsetParent;
  }
  return v;
}
function fixed_pageTop(el) {
  var v= 0;
  while (el.offsetParent) {
    v+= el.offsetTop;
    el= el.offsetParent;
  }
  return v;
}

// Scanning. Check document every so often until it has finished loading. Do
// nothing until <body> arrives, then call main init. Pass any new elements
// found on each scan to be bound   

var fixed_SCANDELAY= 500;

function fixed_scan() {
  if (!document.body) return;
  if (!fixed_viewport) fixed_init();
  var el;
  for (var i= 0; i<document.all.length; i++) {
    el= document.all[i];
    if (!el.fixed_bound) {
      el.fixed_bound= true;
      fixed_bind(el);
  } }
}

var fixed_scanner;
function fixed_stop() {
  window.clearInterval(fixed_scanner);
  fixed_scan();
}

fixed_scan();
fixed_scanner= window.setInterval(fixed_scan, fixed_SCANDELAY);
window.attachEvent('onload', fixed_stop);
window.attachEvent('onresize', fixed_delayout);
window.attachEvent('onscroll', fixed_scroll);

@end @*/
