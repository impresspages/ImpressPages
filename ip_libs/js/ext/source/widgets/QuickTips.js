/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.QuickTips
 * Provides attractive and customizable tooltips for any element.
 * @singleton
 */
Ext.QuickTips = function(){
    var el, tipBody, tipBodyText, tipTitle, tm, cfg, close, tagEls = {}, esc, removeCls = null, bdLeft, bdRight;
    var ce, bd, xy, dd;
    var visible = false, disabled = true, inited = false;
    var showProc = 1, hideProc = 1, dismissProc = 1, locks = [];
    
    var onOver = function(e){
        if(disabled){
            return;
        }
        var t = e.getTarget();
        if(!t || t.nodeType !== 1 || t == document || t == document.body){
            return;
        }
        if(ce && t == ce.el){
            clearTimeout(hideProc);
            return;
        }
        if(t && tagEls[t.id]){
            tagEls[t.id].el = t;
            showProc = show.defer(tm.showDelay, tm, [tagEls[t.id]]);
            return;
        }
        var ttp, et = Ext.fly(t);
        var ns = cfg.namespace;
        if(tm.interceptTitles && t.title){
            ttp = t.title;
            t.qtip = ttp;
            t.removeAttribute("title");
            e.preventDefault();
        }else{
            ttp = t.qtip || et.getAttributeNS(ns, cfg.attribute);
        }
        if(ttp){
            showProc = show.defer(tm.showDelay, tm, [{
                el: t, 
                text: ttp, 
                width: et.getAttributeNS(ns, cfg.width),
                autoHide: et.getAttributeNS(ns, cfg.hide) != "user",
                title: et.getAttributeNS(ns, cfg.title),
           	    cls: et.getAttributeNS(ns, cfg.cls)
            }]);
        }
    };
    
    var onOut = function(e){
        clearTimeout(showProc);
        var t = e.getTarget();
        if(t && ce && ce.el == t && (tm.autoHide && ce.autoHide !== false)){
            hideProc = setTimeout(hide, tm.hideDelay);
        }
    };
    
    var onMove = function(e){
        if(disabled){
            return;
        }
        xy = e.getXY();
        xy[1] += 18;
        if(tm.trackMouse && ce){
            el.setXY(xy);
        }
    };
    
    var onDown = function(e){
        clearTimeout(showProc);
        clearTimeout(hideProc);
        if(!e.within(el)){
            if(tm.hideOnClick){
                hide();
                tm.disable();
            }
        }
    };
    
    var onUp = function(e){
        tm.enable();
    };

    var getPad = function(){
        return bdLeft.getPadding('l')+bdRight.getPadding('r');
    };

    var show = function(o){
        if(disabled){
            return;
        }
        clearTimeout(dismissProc);
        ce = o;
        if(removeCls){ // in case manually hidden
            el.removeClass(removeCls);
            removeCls = null;
        }
        if(ce.cls){
            el.addClass(ce.cls);
            removeCls = ce.cls;
        }
        if(ce.title){
            tipTitle.update(ce.title);
            tipTitle.show();
        }else{
            tipTitle.update('');
            tipTitle.hide();
        }
        el.dom.style.width  = tm.maxWidth+'px';
        //tipBody.dom.style.width = '';
        tipBodyText.update(o.text);
        var p = getPad(), w = ce.width;
        if(!w){
            var td = tipBodyText.dom;
            var aw = Math.max(td.offsetWidth, td.clientWidth, td.scrollWidth);
            if(aw > tm.maxWidth){
                w = tm.maxWidth;
            }else if(aw < tm.minWidth){
                w = tm.minWidth;
            }else{
                w = aw;
            }
        }
        //tipBody.setWidth(w);
        el.setWidth(parseInt(w, 10) + p);
        if(ce.autoHide === false){
            close.setDisplayed(true);
            if(dd){
                dd.unlock();
            }
        }else{
            close.setDisplayed(false);
            if(dd){
                dd.lock();
            }
        }
        if(xy){
            el.avoidY = xy[1]-18;
            el.setXY(xy);
        }
        if(tm.animate){
            el.setOpacity(.1);
            el.setStyle("visibility", "visible");
            el.fadeIn({callback: afterShow});
        }else{
            afterShow();
        }
    };
    
    var afterShow = function(){
        if(ce){
            el.show();
            esc.enable();
            if(tm.autoDismiss && ce.autoHide !== false){
                dismissProc = setTimeout(hide, tm.autoDismissDelay);
            }
        }
    };
    
    var hide = function(noanim){
        clearTimeout(dismissProc);
        clearTimeout(hideProc);
        ce = null;
        if(el.isVisible()){
            esc.disable();
            if(noanim !== true && tm.animate){
                el.fadeOut({callback: afterHide});
            }else{
                afterHide();
            } 
        }
    };
    
    var afterHide = function(){
        el.hide();
        if(removeCls){
            el.removeClass(removeCls);
            removeCls = null;
        }
    };
    
    return {
        /**
        * @cfg {Number} minWidth
        * The minimum width of the quick tip (defaults to 40)
        */
       minWidth : 40,
        /**
        * @cfg {Number} maxWidth
        * The maximum width of the quick tip (defaults to 300)
        */
       maxWidth : 300,
        /**
        * @cfg {Boolean} interceptTitles
        * True to automatically use the element's DOM title value if available (defaults to false)
        */
       interceptTitles : false,
        /**
        * @cfg {Boolean} trackMouse
        * True to have the quick tip follow the mouse as it moves over the target element (defaults to false)
        */
       trackMouse : false,
        /**
        * @cfg {Boolean} hideOnClick
        * True to hide the quick tip if the user clicks anywhere in the document (defaults to true)
        */
       hideOnClick : true,
        /**
        * @cfg {Number} showDelay
        * Delay in milliseconds before the quick tip displays after the mouse enters the target element (defaults to 500)
        */
       showDelay : 500,
        /**
        * @cfg {Number} hideDelay
        * Delay in milliseconds before the quick tip hides when autoHide = true (defaults to 200)
        */
       hideDelay : 200,
        /**
        * @cfg {Boolean} autoHide
        * True to automatically hide the quick tip after the mouse exits the target element (defaults to true).
        * Used in conjunction with hideDelay.
        */
       autoHide : true,
        /**
        * @cfg {Boolean}
        * True to automatically hide the quick tip after a set period of time, regardless of the user's actions
        * (defaults to true).  Used in conjunction with autoDismissDelay.
        */
       autoDismiss : true,
        /**
        * @cfg {Number}
        * Delay in milliseconds before the quick tip hides when autoDismiss = true (defaults to 5000)
        */
       autoDismissDelay : 5000,
       /**
        * @cfg {Boolean} animate
        * True to turn on fade animation. Defaults to false (ClearType/scrollbar flicker issues in IE7).
        */
       animate : false,

       /**
        * @cfg {String} title
        * Title text to display (defaults to '').  This can be any valid HTML markup.
        */
       /**
        * @cfg {String} text
        * Body text to display (defaults to '').  This can be any valid HTML markup.
        */
       /**
        * @cfg {String} cls
        * A CSS class to apply to the base quick tip element (defaults to '').
        */
       /**
        * @cfg {Number} width
        * Width in pixels of the quick tip (defaults to auto).  Width will be ignored if it exceeds the bounds of
        * minWidth or maxWidth.
        */

    /**
     * Initialize and enable QuickTips for first use.  This should be called once before the first attempt to access
     * or display QuickTips in a page.
     */
       init : function(){
          tm = Ext.QuickTips;
          cfg = tm.tagConfig;
          if(!inited){
              if(!Ext.isReady){ // allow calling of init() before onReady
                  Ext.onReady(Ext.QuickTips.init, Ext.QuickTips);
                  return;
              }
              el = new Ext.Layer({cls:"x-tip", shadow:"drop", shim: true, constrain:true, shadowOffset:4});
              el.fxDefaults = {stopFx: true};
              // maximum custom styling
              el.update('<div class="x-tip-top-left"><div class="x-tip-top-right"><div class="x-tip-top"></div></div></div><div class="x-tip-bd-left"><div class="x-tip-bd-right"><div class="x-tip-bd"><div class="x-tip-close"></div><h3></h3><div class="x-tip-bd-inner"></div><div class="x-clear"></div></div></div></div><div class="x-tip-ft-left"><div class="x-tip-ft-right"><div class="x-tip-ft"></div></div></div>');
              tipTitle = el.child('h3');
              tipTitle.enableDisplayMode("block");
              tipBody = el.child('div.x-tip-bd');
              tipBodyText = el.child('div.x-tip-bd-inner');
              bdLeft = el.child('div.x-tip-bd-left');
              bdRight = el.child('div.x-tip-bd-right');
              close = el.child('div.x-tip-close');
              close.enableDisplayMode("block");
              close.on("click", hide);
              var d = Ext.get(document);
              d.on("mousedown", onDown);
              d.on("mouseup", onUp);
              d.on("mouseover", onOver);
              d.on("mouseout", onOut);
              d.on("mousemove", onMove);
              esc = d.addKeyListener(27, hide);
              esc.disable();
              if(Ext.dd.DD){
                  dd = el.initDD("default", null, {
                      onDrag : function(){
                          el.sync();  
                      }
                  });
                  dd.setHandleElId(tipTitle.id);
                  dd.lock();
              }
              inited = true;
          }
          this.enable(); 
       },

    /**
     * Configures a new quick tip instance and assigns it to a target element (should be passed as config.target).
     * @param {Object} config The config object
     */
       register : function(config){
           var cs = config instanceof Array ? config : arguments;
           for(var i = 0, len = cs.length; i < len; i++) {
               var c = cs[i];
               var target = c.target;
               if(target){
                   if(target instanceof Array){
                       for(var j = 0, jlen = target.length; j < jlen; j++){
                           tagEls[target[j]] = c;
                       }
                   }else{
                       tagEls[typeof target == 'string' ? target : Ext.id(target)] = c;
                   }
               }
           }
       },

    /**
     * Removes this quick tip from its element and destroys it.
     * @param {String/HTMLElement/Element} el The element from which the quick tip is to be removed.
     */
       unregister : function(el){
           delete tagEls[Ext.id(el)];
       },

    /**
     * Enable this quick tip.
     */
       enable : function(){
           if(inited && disabled){
               locks.pop();
               if(locks.length < 1){
                   disabled = false;
               }
           }
       },

    /**
     * Disable this quick tip.
     */
       disable : function(){
          disabled = true;
          clearTimeout(showProc);
          clearTimeout(hideProc);
          clearTimeout(dismissProc);
          if(ce){
              hide(true);
          }
          locks.push(1);
       },

    /**
     * Returns true if the quick tip is enabled, else false.
     */
       isEnabled : function(){
            return !disabled;
       },

        // private
       tagConfig : {
           namespace : "ext",
           attribute : "qtip",
           width : "width",
           target : "target",
           title : "qtitle",
           hide : "hide",
           cls : "qclass"
       }
   };
}();

// backwards compat
Ext.QuickTips.tips = Ext.QuickTips.register;