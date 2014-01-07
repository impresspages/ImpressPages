/**
* @package ImpressPages
*
*/


var ipContentManagement;


(function($){
    "use strict";


    ipContentManagement = new function() {

        var widgetOnDroppable = false;


        $(document).ready(function() {
            var $ipObject = $(document);

            $ipObject.bind('initFinished.ipContentManagement', ipAdminPanelInit);
            $ipObject.bind('initFinished.ipContentManagement', ipAdminWidgetsScroll);
            $(window).bind('resizeEnd',                        ipAdminWidgetsScroll);
            $ipObject.bind('initFinished.ipContentManagement', ipAdminWidgetsSearch);

            $ipObject.ipContentManagement();

            // case insensitive search
            ip.jQuery.expr[':'].icontains = function(a, i, m) {
                return ip.jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
            };

            if (isMobile) {
                $('body').addClass('ipMobile');
            }


            $('.ipAdminPanel .ipActionWidgetButton').on('dragstart', ipStartWidgetDrag);
            $('.ipAdminPanel .ipActionWidgetButton').on('dragstop', ipStopWidgetDrag);

        });

        $(window).resize(function() {
            if(this.resizeTO) { clearTimeout(this.resizeTO); }
            this.resizeTO = setTimeout(function() {
                $(this).trigger('resizeEnd');
            }, 100);
        });

        /**
         *
         * Function used to paginate Widgets on Administration Panel
         *
         * @param none
         * @returns nothing
         *
         *
         */
        var ipAdminWidgetsScroll = function() {
            var $scrollable = $('.ipAdminWidgetsContainer'); // binding object
            $scrollable.scrollable({
                items: 'li', // items are <li> elements; on scroll styles will be added to <ul>
                touch: false
            });
            var scrollableAPI = $scrollable.data('scrollable'); // getting instance API
            var itemWidth = scrollableAPI.getItems().eq(0).outerWidth(true);
            var containerWidth = scrollableAPI.getRoot().width() + 24; // adding left side compensation
            var scrollBy = Math.floor(containerWidth / itemWidth); // define number of items to scroll
            if(scrollBy < 1) { scrollBy = 1; } // setting the minimum
            $('.ipAdminWidgets .ipaRight, .ipAdminWidgets .ipaLeft').unbind('click'); // unbind if reinitiating dynamically
            scrollableAPI.begin(); // move to scroller to default position (beginning)
            $('.ipAdminWidgets .ipaRight').click(function(event){
                event.preventDefault();
                scrollableAPI.move(scrollBy);
            });
            $('.ipAdminWidgets .ipaLeft').click(function(event){
                event.preventDefault();
                scrollableAPI.move(-scrollBy);
            });
        }

        /**
         *
         * Function used to search Widgets on Administration Panel
         *
         * @param none
         * @returns nothing
         *
         *
         */
        var ipAdminWidgetsSearch = function() {
            var $input = $('.ipAdminWidgetsSearch .ipaInput');
            var $button = $('.ipAdminWidgetsSearch .ipaButton');
            var $widgets = $('.ipAdminWidgetsContainer li');

            $input.focus(function(){
                if( this.value == this.defaultValue ){
                    this.value = '';
                };
            }).blur(function(){
                    if( this.value == '' ){
                        this.value = this.defaultValue;
                    };
                }).keyup(function(){
                    var value = this.value;
                    $widgets.css('display',''); // restate visibility
                    if (value && value != this.defaultValue ) {
                        $widgets.not(':icontains(' + value + ')').css('display','none');
                        $button.addClass('ipaClear');
                    } else {
                        $button.removeClass('ipaClear');
                    }
                    ipAdminWidgetsScroll(); // reinitiate scrollable
                });

            $button.click(function(event){
                event.preventDefault();
                var $this = $(this);
                if ($this.hasClass('ipaClear')) {
                    $input.val('').blur().keyup(); // blur returns default value; keyup displays all hidden widgets
                    $this.removeClass('ipaClear'); // makes button look default
                }
            });
        }

        /**
         *
         * Function used to create a space on a page for Administration Panel
         *
         * @param none
         * @returns nothing
         *
         *
         */
        var ipAdminPanelInit = function() {
            var $container = $('.ipAdminPanelContainer'); // the most top element physically creates a space
            var $panel = $('.ipAdminPanel'); // Administration Panel that stays always visible
            $container.height($panel.height()); // setting the height to container
            $panel.css('top',$('.ipsAdminToolbarContainer').outerHeight()); // move down to leave space for top toolbar
        }

        var ipStartWidgetDrag = function(event, ui) {
            $('.ipWidget').each(function(key, value) {
                //left placeholder
                var $droppable = $('<div class="ipsWidgetDropPlaceholder" style="width: 10px; background-color: #000;"></div>');
                $('body').append($droppable);
                $droppable.css('position', 'absolute');
                $droppable.css('left', $(value).offset().left - $droppable.width() + 'px');
                $droppable.css('top', $(value).offset().top + 10 + 'px');
                $droppable.css('height', $(value).height() - 20 + 'px');
                $droppable.data('instanceId', $(value).data('widgetinstanceid'));
                $droppable.data('leftOrRight', 'left');

                //right placeholder
                var $droppable = $('<div class="ipsWidgetDropPlaceholder" style="width: 10px; background-color: #000;"></div>');
                $('body').append($droppable);
                $droppable.css('position', 'absolute');
                $droppable.css('left', $(value).offset().left + $(value).width() + 'px');
                $droppable.css('top', $(value).offset().top + 10 + 'px');
                $droppable.css('height', $(value).height() - 20 + 'px');
                $droppable.data('instanceId', $(value).data('widgetinstanceid'));
                $droppable.data('leftOrRight', 'right');
            });

            $('.ipsWidgetDropPlaceholder').droppable({
                accept: ".ipActionWidgetButton, .ipWidget",
                activeClass: "ui-state-hover",
                hoverClass: "ui-state-active",
                over: function(event,ui) {
                    widgetOnDroppable = $(this);
                },
                out: function(event, ui) {
                    widgetOnDroppable = false;
                },
                drop: function( event, ui ) {
                    //this method on jQuery-ui is buggy and fires fake drop events. So we better handle stop event on draggable. This is just for widget side drops.
                }
            });

        }

        var ipStopWidgetDrag = function(event, ui) {

            if (widgetOnDroppable) {
                var targetWidgetInstanceId = widgetOnDroppable.data('instanceId');
                var leftOrRight = widgetOnDroppable.data('leftOrRight');
                var widgetName = $(this).data('ipAdminWidgetButton').name;
                ipAddWidgetToSide(widgetName, targetWidgetInstanceId, leftOrRight);
            }
            $('.ipsWidgetDropPlaceholder').remove();

        }

        var ipMoveWidgetToSide = function (widgetInstanceId, targetWidgetInstanceId, leftOrRight) {
            // todo
        }

        var ipAddWidgetToSide = function(widgetName, targetWidgetInstanceId, leftOrRight) {

            console.log(widgetName);
            console.log(targetWidgetInstanceId);
            console.log(leftOrRight);
        }
    }

})(ip.jQuery);
