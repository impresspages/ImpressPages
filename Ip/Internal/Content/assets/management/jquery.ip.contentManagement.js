/**
 * @package ImpressPages
 *
 *
 */


(function($) {
    "use strict";
    var lastDroppable = false;

    var methods = {
        init : function(options) {

            return this.each(function() {

                var $this = $(this);

                var data = $this.data('ipContentManagement');

                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    $this.data('ipContentManagement', {
                    });

                    $(window).resize(function () {
                        if (this.resizeTO) {
                            clearTimeout(this.resizeTO);
                        }
                        this.resizeTO = setTimeout(function () {
                            $(this).trigger('resizeEnd');
                        }, 100);
                    });
                    $(window).bind('resizeEnd', ipAdminWidgetsScroll);


                    // case insensitive search
                    jQuery.expr[':'].icontains = function (a, i, m) {
                        return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
                    };

                    if (isMobile) {
                        $(document.body).addClass('ipMobile');
                    }


                    $(document.body).prepend(ipContentInit.saveProgressHtml);
                    $(document.body).prepend(ipContentInit.controlPanelHtml);

                    var options = new Object;
                    options.pageId = ip.pageId;
                    options.revisionId = ip.revisionId;
                    options.widgetControlsHtml = ipContentInit.widgetControlsHtml;
                    options.contentManagementObject = $this;
                    options.manageableRevision = ipContentInit.manageableRevision;

                    var data = $this.data('ipContentManagement');
                    data.initInfo = options;
                    $this.data('ipContentManagement', data);

                    $('.ipsAdminPanelWidgetButton').ipAdminWidgetButton();

                    // float fix, categories don't exist in mobile
                    var $widgetCategories = $('._widgetCategories');
                    if ($widgetCategories.length) {
                        $('.ipsWidgetList').css({'marginLeft':$widgetCategories.width()});
                    }

                    ipSpaceForWidgets();
                    ipAdminWidgetsSearch();

                    // Widget category switches
                    $('.ipsWidgetTag a').on('click', function (e) {e.preventDefault();});
                    var $widgetSwitches = $('.ipsWidgetTag');
                    $widgetSwitches.on('click', function(e) {
                        e.preventDefault();
                        var $this = $(this);

                        $widgetSwitches.removeClass('_active');
                        $this.addClass('_active');

                        $('.ipsAdminPanelWidgetsContainer .ipsWidgetItem').addClass('hidden');
                        var tagWidgetNames = ipContentInit.tags[$this.data('tag')];
                        $.each(tagWidgetNames, function (key, item) {
                            $('.ipsWidgetItem-' + item).removeClass('hidden');

                        });

                        ipAdminWidgetsScroll();
                    });

                    $widgetSwitches.first().click();

                    $('.ipsAdminPanelWidgetButton')
                        .on('dragstart', ipStartWidgetDrag)
                        .on('dragstop', ipStopWidgetDrag);

                    //$('.ipWidget').on('sortstart', ipStartWidgetDrag);
                    $('.ipBlock .ipWidget')
                        .on('dragstart.ipContentManagement', ipStartWidgetDrag)
                        .on('dragstop.ipContentManagement', ipStopWidgetDrag);
                    $('body').on('ipWidgetReinit', function () {
                        $('.ipBlock .ipWidget')
                            .off('dragstart.ipContentManagement').on('dragstart.ipContentManagement', ipStartWidgetDrag)
                            .off('dragstop.ipContentManagement').on('dragstop.ipContentManagement', ipStopWidgetDrag);
                    })

                    $('.ipsContentSave').on('click', function(e){$.proxy(methods.save, $this)(false)});
                    $('.ipsContentPublish').on('click', function(e){$.proxy(methods.save, $this)(true)});
                    $('.ipsContentPreview').on('click', function(e){e.preventDefault(); ipManagementMode.setManagementMode(0);});
                    $.proxy(methods.initBlocks, $this)($('.ipBlock'));

                    $this.trigger('ipContentManagementInit', options);

                }
            });
        },

        initBlocks : function(blocks) {
            var $this = this;
            var data = $this.data('ipContentManagement');
            var options = data.initInfo;
            if (options.manageableRevision) {
                blocks.ipBlock(options);
            }
        },

        save : function(publish) {
            return this.each(function() {
                var $this = $(this);
                var data = $this.data('ipContentManagement');

                var postData = Object();
                postData.aa = 'Content.savePage';
                postData.securityToken = ip.securityToken;
                postData.revisionId = ip.revisionId;
                if (publish) {
                    postData.publish = 1;
                } else {
                    postData.publish = 0;
                }

                $.ajax({
                    type : 'POST',
                    url : ip.baseUrl,
                    data : postData,
                    context : $this,
                    success : methods._savePageResponse,
                    dataType : 'json'
                });
            });
        },

        _savePageResponse: function(response) {
            var $this = $(this);
            var data = $this.data('ipContentManagement');
            if (response.status == 'success') {
                window.location.href = response.newRevisionUrl;
            } else {

            }
        }
    };


    /**
     *
     * Function used to paginate Widgets on Administration Panel
     *
     * @param none
     * @returns nothing
     *
     *
     */
    var ipAdminWidgetsScroll = function () {
        var $scrollable = $('.ipsAdminPanelWidgetsContainer'); // binding object
        $scrollable.removeData("scrollable");
        $scrollable.scrollable({
            item: 'li:not(.hidden)',
            items: 'li', // items are <li> elements; on scroll styles will be added to <ul>
            touch: false,
            keyboard: false
        });
        var scrollableAPI = $scrollable.data('scrollable'); // getting instance API
        var itemWidth = scrollableAPI.getItems().eq(0).outerWidth(true);
        var containerWidth = scrollableAPI.getRoot().width() + 24; // adding left side compensation
        var scrollBy = Math.floor(containerWidth / itemWidth); // define number of items to scroll
        if (scrollBy < 1) {
            scrollBy = 1;
        } // setting the minimum
        $scrollable.siblings('.ipsRight, .ipsLeft').off('click'); // unbind if reinitiating dynamically
        scrollableAPI.begin(); // move to scroller to default position (beginning)
        $scrollable.siblings('.ipsRight').on('click', function (event) {
            event.preventDefault();
            scrollableAPI.move(scrollBy);
        });
        $scrollable.siblings('.ipsLeft').on('click', function (event) {
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
    var ipAdminWidgetsSearch = function () {
        var $input = $('.ipsAdminPanelWidgetsSearch .ipsInput');
        var $button = $('.ipsAdminPanelWidgetsSearch .ipsButton');
        var $widgets = $('.ipsAdminPanelWidgetsContainer li');

        $input.focus(function () {
            if (this.value == this.defaultValue) {
                this.value = '';
            }
            ;
        }).blur(function () {
            if (this.value == '') {
                this.value = this.defaultValue;
            }
            ;
        }).keyup(function () {
            var value = this.value;
            $widgets.css('display', ''); // restate visibility
            if (value && value != this.defaultValue) {
                $widgets.not(':icontains(' + value + ')').css('display', 'none');
                $button.addClass('ipaClear');
            } else {
                $button.removeClass('ipaClear');
            }
            ipAdminWidgetsScroll(); // reinitiate scrollable
        });

        $button.click(function (event) {
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
    var ipSpaceForWidgets = function () {
        var $container = $('.ipsAdminPanelContainer'); // the most top element physically creates a space
        var $panel = $('.ipsAdminPanel'); // Administration Panel that stays always visible
        $container.height($panel.outerHeight()); // setting the height to container
        $panel.css('top', $('.ipsAdminNavbarContainer').outerHeight()); // move down to leave space for top navbar
    }

    var ipStartWidgetDrag = function (event, ui) {
        var draggingElement = event.target;
        if (!$(draggingElement).hasClass('ipWidget') && !$(draggingElement).hasClass('ipsAdminPanelWidgetButton')) {
            //we are dragging something inside widget, not the widget itself
            return;
        }

        //drop side
        var sidePlaceholders = new Array();

        $('*:not(.ipsCol) > .ipBlock > .ipWidget').not(".ipWidget .ipWidget .ipWidget .ipWidget").not($(draggingElement)).each(function (key, value) {
            //left placeholder
            sidePlaceholders.push({
                left: $(value).offset().left - 20,
                top: $(value).offset().top + 1,
                height: Math.max($(value).height() - 2, 10),
                width: 20,
                widgetId: $(value).data('widgetid'),
                leftOrRight: 'left'
            });

            //right placeholder
            sidePlaceholders.push({
                left: $(value).offset().left + $(value).width(),
                top: $(value).offset().top + 1,
                height: Math.max($(value).height() - 2, 10),
                width: 20,
                widgetId: $(value).data('widgetid'),
                leftOrRight: 'right'
            });
        });

        $.each(sidePlaceholders, function (key, value) {
            var $droppable = $('<div class="ipsWidgetDropPlaceholder ipAdminWidgetPlaceholderVertical"><div class="ipsWidgetDropMarker _marker"></div></div>');
            $('body').append($droppable);
            $droppable.css('position', 'absolute');
            $droppable.css('left', value.left + 'px');
            $droppable.css('top', value.top + 'px');
            $droppable.css('height', value.height + 'px');
            $droppable.css('width', value.width + 'px');
            $droppable.data('widgetId', value.widgetId);
            $droppable.data('leftOrRight', value.leftOrRight);
            $droppable.data('side', 1);
            $droppable.find('.ipsWidgetDropMarker').height(value.height);
            $droppable.find('.ipsWidgetDropMarker').css('marginLeft', Math.round(value.width / 2));
        });

        //------------------------------------------------------

        var colsPlaceholders = new Array();
        $.each($('.ipWidget-Columns'), function (widgetKey, columnsWidget) {
            $.each($(columnsWidget).find('.ipsCol'), function (colKey, col) {
                var $col = $(col);
                var $prevBlock = $col.prev().find('.ipBlock');
                var $block = $col.find('.ipBlock');
                if (colKey != 0 && $block.offset() && $prevBlock.offset()) { //skip first col. Offset checking is just in case. If everything goes right, prev block should always exist.
                    var space = $block.offset().left - ($prevBlock.offset().left + $prevBlock.width());
                    //alert(space);
                    colsPlaceholders.push({
                        left: $col.find('.ipBlock').offset().left - space,
                        top: $col.find('.ipBlock').offset().top + 1,
                        height: Math.max($(columnsWidget).height() - 2, 10),
                        width: space,
                        widgetId: $(columnsWidget).data('widgetid'),
                        position: colKey
                    });
                }
            });
        });

        $.each(colsPlaceholders, function (key, value) {
            var $droppable = $('<div class="ipsWidgetDropPlaceholder ipAdminWidgetPlaceholderVertical"><div class="ipsWidgetDropMarker _marker"></div></div>');
            $('body').append($droppable);
            $droppable.css('position', 'absolute');
            $droppable.css('left', value.left + 'px');
            $droppable.css('top', value.top + 'px');
            $droppable.css('height', value.height + 'px');
            $droppable.css('width', value.width + 'px');
            $droppable.data('widgetId', value.widgetId);
            $droppable.data('newCol', 1);
            $droppable.data('position', value.position);
            $droppable.find('.ipsWidgetDropMarker').height(value.height);
            $droppable.find('.ipsWidgetDropMarker').css('marginLeft', Math.round(value.width / 2));
        });

        //------------------------------------------------------

        //drop between the widgets horizontally
        var horizontalPlaceholders = new Array();
        $.each($('.ipBlock').not($(draggingElement).find('.ipBlock')), function (blockKey, block) {
            var $widgets = $(block).find('> .ipWidget');
            $.each($widgets, function (key, value) {
                var $widget = $(value);
                var newPlaceholder = {};
                if ($widget.index() == 0) { //first widget
                    var space = 15;
                    //first placeholder
                    newPlaceholder = {
                        left: $widget.offset().left,
                        top: $widget.offset().top - space,
                        width: $widget.width(),
                        blockName: $(block).data('ipBlock').name,
                        position: 0,
                        markerOffset: space/2
                    };

                    var widgetController = $widget.data('widgetController');
                    if (!widgetController.splitParts) {
                        widgetController.splitParts = function () {return new Array()};
                    }
                    if (widgetController.splitParts && widgetController.splitParts().length) {
                        //middle of the first paragraph
                        var $firstParagraph = widgetController.splitParts().first();
                        newPlaceholder.height = $firstParagraph.offset().top + Math.round($firstParagraph.height() / 2) - newPlaceholder.top;
                    } else {
                        newPlaceholder.height = $widget.offset().top + ($widget.height() / 2) - newPlaceholder.top;
                    }

                    if ($widget.find(".ipBlock").length) { //if this is a columns widget, make a 3/4 space for dropping. Leave 1/4 for column placeholders
                        newPlaceholder.height = space*3/4;
                        newPlaceholder.markerOffset = space*3/4 / 2;
                    }

                    if ($widget.parent().closest('.ipWidget').find('.ipBlock').length && !$widget.find('.ipBlock').length) {//if this is first widget inside a column. Take 1/4 of space for placeholder
                        var $aboveColumnsWidget = $widget.parent().closest('.ipWidget').prev();
                        if ($aboveColumnsWidget.length) {
                            space = $widget.offset().top - ($aboveColumnsWidget.offset().top + $aboveColumnsWidget.height());
                            newPlaceholder.top = $widget.offset().top - space / 2;
                            newPlaceholder.markerOffset = space / 2 / 2; //half of marker size
                        } else {
                            newPlaceholder.top = $widget.offset().top - space * 1 / 4;
                            newPlaceholder.markerOffset = space * 1 / 4 / 2;
                        }

                        var widgetController = $widget.data('widgetController');
                        if (!widgetController.splitParts) {
                            widgetController.splitParts = function () {return new Array()};
                        }
                        if (widgetController.splitParts && widgetController.splitParts().length) {
                            //middle of the first paragraph
                            var $firstParagraph = widgetController.splitParts().first();
                            newPlaceholder.height = $firstParagraph.offset().top + Math.round($firstParagraph.height() / 2) - newPlaceholder.top;
                        } else {
                            //middle of the widget
                            newPlaceholder.height = $widget.offset().top + ($widget.height() / 2) - newPlaceholder.top;
                        }
                    }
                    horizontalPlaceholders.push(newPlaceholder);
                } else {  //not first widget
                    var $prevWidget = $widget.prev();
                    var space = $widget.offset().top - ($prevWidget.offset().top + $prevWidget.height());
                    //all up to the last placeholders
                    newPlaceholder = {
                        left: $prevWidget.offset().left,
                        top: $prevWidget.offset().top + ($prevWidget.height() / 2),
                        width: $widget.width(),
                        blockName: $(block).data('ipBlock').name,
                        position: $widget.index()
                    };
                    if ($prevWidget.find(".ipBlock").length) { //if above is columns widget
                        newPlaceholder.top = $prevWidget.offset().top + $prevWidget.height() + space * 1 / 4; //the end of column widget
                    }

                    var prevWidgetController = $prevWidget.data('widgetController');
                    if (!prevWidgetController.splitParts) {
                        prevWidgetController.splitParts = function () {return new Array()};
                    }
                    if (prevWidgetController.splitParts() && prevWidgetController.splitParts().length) {
                        //start placeholder from the middle of last paragraph
                        var $lastParagraph = prevWidgetController.splitParts().last();
                        newPlaceholder.top = $lastParagraph.offset().top + Math.round($lastParagraph.height() / 2)
                    }

                    var widgetController = $widget.data('widgetController');
                    if (!widgetController.splitParts) {
                        widgetController.splitParts = function () {return new Array()};
                    }
                    if (widgetController.splitParts() && widgetController.splitParts().length) {
                        //placeholder touches center of first paragraph
                        var $firstParagraph = widgetController.splitParts().first();
                        newPlaceholder.height = $firstParagraph.offset().top - newPlaceholder.top + Math.round($firstParagraph.height() / 2);
                    } else {
                        //placeholder touches the center of the widget
                        newPlaceholder.height = $widget.offset().top + ($widget.height() / 2) - newPlaceholder.top;
                    }

                    if ($widget.find(".ipBlock").length) {
                        newPlaceholder.height = $widget.offset().top - newPlaceholder.top - (space / 2);
                        newPlaceholder.markerOffset = newPlaceholder.height - 1 ;
                    }

                    newPlaceholder.markerOffset = ($prevWidget.offset().top + $prevWidget.height() + $widget.offset().top) / 2 - newPlaceholder.top;

                    horizontalPlaceholders.push(newPlaceholder);
                }

                if ($widget.index() == $widgets.length - 1) {
                    var space = 10;
                    var lastPlaceholder = {
                        left: $widget.offset().left,
                        top: newPlaceholder.top + newPlaceholder.height + 1,
                        height: $widget.height() / 2 + space,
                        width: $widget.width(),
                        markerOffset: $widget.height() / 2 + space / 2,
                        blockName: $(block).data('ipBlock').name,
                        position: $widget.index() + 1
                    };

                    var widgetController = $widget.data('widgetController');
                    if (!widgetController.splitParts) {
                        widgetController.splitParts = function () {return new Array()};
                    }
                    if (widgetController.splitParts && widgetController.splitParts().length) {
                        //middle of the last paragraph
                        var $lastParagraph = widgetController.splitParts().last();
                        lastPlaceholder.top = $lastParagraph.offset().top + Math.round($lastParagraph.height() / 2);
                        lastPlaceholder.height = Math.round($lastParagraph.height() / 2);
                        lastPlaceholder.markerOffset = Math.round($lastParagraph.height() / 2) + space;
                    }

                    var $columnsWidget = $widget.parent().closest('.ipWidget');
                    if ($columnsWidget.find('.ipBlock').length && !$widget.find(".ipBlock").length) {
                        //we are last widget inside a column
                        var columnsEnd = $columnsWidget.offset().top + $columnsWidget.height();
                        if ($columnsWidget.next().length) {
                            space = $columnsWidget.next().offset().top - columnsEnd;
                        }
                        lastPlaceholder.height = columnsEnd -  lastPlaceholder.top + space * 1 / 4;
                        lastPlaceholder.markerOffset = lastPlaceholder.markerOffset - space * 1/4
                    }

                    if ($widget.find(".ipBlock").length) {
                        //if last widget has blocks inside (columns widget)
                        var columnsEnd = $widget.offset().top + $widget.height();
                        lastPlaceholder.height = space * 2;
                        lastPlaceholder.top = columnsEnd + space * 1 / 4;
                        lastPlaceholder.markerOffset = 5;
                    }

                    horizontalPlaceholders.push(lastPlaceholder);
                }
            });

            if ($(block).find('> .ipWidget').length == 0) { //empty block
                var $block = $(block);
                horizontalPlaceholders.push({
                    left: $block.offset().left,
                    top: $block.offset().top,
                    height: $block.height(),
                    width: $block.width(),
                    markerOffset: $block.height() / 2,
                    blockName: $block.data('ipBlock').name,
                    position: 0
                });
            }
        });

        $.each(horizontalPlaceholders, function (key, value) {
            var $droppable = $('<div class="ipsWidgetDropPlaceholder ipAdminWidgetPlaceholderHorizontal"><div class="ipsWidgetDropMarker _marker"></div></div>');
            $('body').append($droppable);
            $droppable.css('position', 'absolute');
            $droppable.css('left', value.left + 'px');
            $droppable.css('top', value.top + 'px');
            $droppable.css('width', value.width + 'px');
            $droppable.css('height', value.height + 'px');
            $droppable.find('.ipsWidgetDropMarker').css('marginTop', value.markerOffset);
            $droppable.data('position', value.position);
            $droppable.data('blockName', value.blockName);
        });

        //drop between paragraphs inside widget
        var paragraphPlaceholders = new Array();
        $.each($('.ipBlock .ipWidget').not('.ipbEmpty .ipWidget'), function (widgetKey, widget) {
            var $widget = $(widget);
            var widgetController = $widget.data('widgetController');
            if (!widgetController) {
                widgetController = {};
            }
            if (!widgetController.splitParts) {
                widgetController.splitParts = function () {return new Array()};
            }
            var $paragraphs = widgetController.splitParts();
            if($paragraphs.length <= 1) {
                return;
            }
            $.each($paragraphs, function (paragraphKey, paragraph) {
                var $paragraph = $(paragraph);

                if (paragraphKey == 0) {
                    return;
                }
                var $prevParagraph = $paragraphs.eq(paragraphKey - 1);

                var newPlaceholder = {
                    left: $widget.offset().left,
                    top: $prevParagraph.offset().top + Math.round($prevParagraph.height() / 2),
                    width: $widget.width(),
                    widgetId: $widget.data('widgetid'),
                    position: paragraphKey + 1
                };

                newPlaceholder.height = $paragraph.offset().top + Math.round($paragraph.height() / 2) - newPlaceholder.top;
                newPlaceholder.markerOffset = ($prevParagraph.offset().top + $prevParagraph.height() + $paragraph.offset().top) / 2 - newPlaceholder.top;

                paragraphPlaceholders.push(newPlaceholder);
            });
        });

        $.each(paragraphPlaceholders, function (key, value) {
            var $droppable = $('<div class="ipsWidgetDropPlaceholder ipAdminWidgetPlaceholderHorizontal"><div class="ipsWidgetDropMarker _marker"></div></div>');
            $('body').append($droppable);
            $droppable.css('position', 'absolute');
            $droppable.css('left', value.left + 'px');
            $droppable.css('top', value.top + 'px');
            $droppable.css('width', value.width + 'px');
            $droppable.css('height', value.height + 'px');
            $droppable.find('.ipsWidgetDropMarker').css('marginTop', value.markerOffset);
            $droppable.data('position', value.position);
            $droppable.data('widgetId', value.widgetId);
            $droppable.data('paragraph', 1);
        });

        $('.ipsWidgetDropPlaceholder').droppable({
            accept: ".ipsAdminPanelWidgetButton, .ipWidget",
            activeClass: "",
            hoverClass: "_hover",
            greedy: true,
            over: function (event, ui) {
                lastDroppable = $(this);
                $(this).data('hover', true);
            },
            out: function (event, ui) {
                $(this).data('hover', false);
            },
            drop: function (event, ui) {
                //this method on jQuery-ui is buggy and fires fake drop events. So we better handle stop event on draggable. This is just for widget side drops.
            }
        });
    }

    var ipStopWidgetDrag = function (event, ui) {
        if (lastDroppable && lastDroppable.data('hover') && $(event.target).data('ipAdminWidgetButton')) {
            //new widget has been dropped
            var targetwidgetid = lastDroppable.data('widgetId');
            var leftOrRight = lastDroppable.data('leftOrRight');
            var widgetName = $(this).data('ipAdminWidgetButton').name;
            var side = lastDroppable.data('side');
            var newCol = lastDroppable.data('newCol');
            var blockName = lastDroppable.data('blockName');
            var position = lastDroppable.data('position');
            var paragraph = lastDroppable.data('paragraph');
            if (side) {
                ipContent.createWidgetToSide(widgetName, targetwidgetid, leftOrRight);
            } else if (newCol) {
                ipContent.createWidgetToColumn(widgetName, targetwidgetid, position);
            } else if (paragraph) {
                ipContent.createWidgetInsideWidget(widgetName, targetwidgetid, position);
            } else {
                ipContent.createWidget(blockName, widgetName, position);
            }
        }
        if (lastDroppable && lastDroppable.data('hover') && $(event.target).hasClass('ipWidget')) {
            //existing widget has been moved
            var $widget = $(event.target);
            var widgetId = $widget.data('widgetid');
            var curPosition = $widget.index();
            var curBlock = $widget.closest('.ipBlock').data('ipBlock').name;
            var position = lastDroppable.data('position');
            var block = lastDroppable.data('blockName');
            var side = lastDroppable.data('side');
            var newCol = lastDroppable.data('newCol');
            var leftOrRight = lastDroppable.data('leftOrRight');
            var targetwidgetid = lastDroppable.data('widgetId');
            var sourcewidgetid = $widget.data('widgetid');
            var paragraph = lastDroppable.data('paragraph');

            if (block == curBlock && curPosition < position) {
                position--;
            }
            if (block != curBlock || curPosition != position) {
                if (side) {
                    ipContent.moveWidgetToSide(sourcewidgetid, targetwidgetid, leftOrRight);
                } else if (newCol) {
                    ipContent.moveWidgetToColumn(sourcewidgetid, targetwidgetid, position);
                } else if (paragraph) {
                    ipContent.moveWidgetInsideWidget(sourcewidgetid, targetwidgetid, position);
                } else {
                    ipContent.moveWidget(widgetId, position, block);
                }
            }
        }

        $('.ipsWidgetDropPlaceholder').remove();
    }

    $.fn.ipContentManagement = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }
    };

})(jQuery);
