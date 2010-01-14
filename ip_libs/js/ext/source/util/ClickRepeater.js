/*
 * Ext JS Library 1.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 @class Ext.util.ClickRepeater
 @extends Ext.util.Observable

 A wrapper class which can be applied to any element. Fires a "click" event while the
 mouse is pressed. The interval between firings may be specified in the config but
 defaults to 10 milliseconds.

 Optionally, a CSS class may be applied to the element during the time it is pressed.

 @cfg {String/HTMLElement/Element} el The element to act as a button.
 @cfg {Number} delay The initial delay before the repeating event begins firing.
 Similar to an autorepeat key delay.
 @cfg {Number} interval The interval between firings of the "click" event. Default 10 ms.
 @cfg {String} pressClass A CSS class name to be applied to the element while pressed.
 @cfg {Boolean} accelerate True if autorepeating should start slowly and accelerate.
           "interval" and "delay" are ignored. "immediate" is honored.
 @cfg {Boolean} preventDefault True to prevent the default click event
 @cfg {Boolean} stopDefault True to stop the default click event

 @history
    2007-02-02 jvs Original code contributed by Nige "Animal" White
    2007-02-02 jvs Renamed to ClickRepeater
    2007-02-03 jvs Modifications for FF Mac and Safari 

 @constructor
 @param {String/HTMLElement/Element} el The element to listen on
 @param {Object} config
 */
Ext.util.ClickRepeater = function(el, config)
{
    this.el = Ext.get(el);
    this.el.unselectable();

    Ext.apply(this, config);

    this.addEvents({
    /**
     * @event mousedown
     * Fires when the mouse button is depressed.
     * @param {Ext.util.ClickRepeater} this
     */
        "mousedown" : true,
    /**
     * @event click
     * Fires on a specified interval during the time the element is pressed.
     * @param {Ext.util.ClickRepeater} this
     */
        "click" : true,
    /**
     * @event mouseup
     * Fires when the mouse key is released.
     * @param {Ext.util.ClickRepeater} this
     */
        "mouseup" : true
    });

    this.el.on("mousedown", this.handleMouseDown, this);
    if(this.preventDefault || this.stopDefault){
        this.el.on("click", function(e){
            if(this.preventDefault){
                e.preventDefault();
            }
            if(this.stopDefault){
                e.stopEvent();
            }
        }, this);
    }

    // allow inline handler
    if(this.handler){
        this.on("click", this.handler,  this.scope || this);
    }

    Ext.util.ClickRepeater.superclass.constructor.call(this);
};

Ext.extend(Ext.util.ClickRepeater, Ext.util.Observable, {
    interval : 20,
    delay: 250,
    preventDefault : true,
    stopDefault : false,
    timer : 0,

    // private
    handleMouseDown : function(){
        clearTimeout(this.timer);
        this.el.blur();
        if(this.pressClass){
            this.el.addClass(this.pressClass);
        }
        this.mousedownTime = new Date();

        Ext.get(document).on("mouseup", this.handleMouseUp, this);
        this.el.on("mouseout", this.handleMouseOut, this);

        this.fireEvent("mousedown", this);
        this.fireEvent("click", this);
        
        this.timer = this.click.defer(this.delay || this.interval, this);
    },

    // private
    click : function(){
        this.fireEvent("click", this);
        this.timer = this.click.defer(this.getInterval(), this);
    },

    // private
    getInterval: function(){
        if(!this.accelerate){
            return this.interval;
        }
        var pressTime = this.mousedownTime.getElapsed();
        if(pressTime < 500){
            return 400;
        }else if(pressTime < 1700){
            return 320;
        }else if(pressTime < 2600){
            return 250;
        }else if(pressTime < 3500){
            return 180;
        }else if(pressTime < 4400){
            return 140;
        }else if(pressTime < 5300){
            return 80;
        }else if(pressTime < 6200){
            return 50;
        }else{
            return 10;
        }
    },

    // private
    handleMouseOut : function(){
        clearTimeout(this.timer);
        if(this.pressClass){
            this.el.removeClass(this.pressClass);
        }
        this.el.on("mouseover", this.handleMouseReturn, this);
    },

    // private
    handleMouseReturn : function(){
        this.el.un("mouseover", this.handleMouseReturn);
        if(this.pressClass){
            this.el.addClass(this.pressClass);
        }
        this.click();
    },

    // private
    handleMouseUp : function(){
        clearTimeout(this.timer);
        this.el.un("mouseover", this.handleMouseReturn);
        this.el.un("mouseout", this.handleMouseOut);
        Ext.get(document).un("mouseup", this.handleMouseUp);
        this.el.removeClass(this.pressClass);
        this.fireEvent("mouseup", this);
    }
});