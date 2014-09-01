/**
 * @package ImpressPages
 *
 */


var IpWidget_Columns = function () {
    "use strict";
    this.$widgetObject = null;

    this.init = function ($widgetObject, data) {
        $(document).trigger('ipWidgetColumnsInit');
    };

    this.onAdd = function () {
        $(document).trigger('ipWidgetColumnsOnAdd');
    };

};

var ipColumnsInitWidthHandles = function () {
    "use strict";
    var $handler = $('<div class="ipAdminWidgetColsResizeHandler ipsAdminWidgetColsWidthHandler"></div>');

    function addResizeHandlers($widget) {


        var $cols = $widget.find('> .ipsColsContainer > .ipsCol');
        $.each($cols, function (index, col) {
            if (index >= $cols.length - 1) {
                //skip last col
                return;
            }
            var $newHandler = $handler.clone();
            var $col = $(col);
            var $nextCol = $col.next();
            var $nextColStart = $nextCol.offset().left;
            $newHandler.css('position', 'absolute');
            $newHandler.css('left', $nextColStart - ($newHandler.width() / 2) + 'px');
            $newHandler.css('top', $col.offset().top + 'px');
            $newHandler.css('height', $widget.height() + 'px');

            $newHandler.draggable({
                axis: "x",
                drag: function (event, ui) {
                    var totalPercent = parseFloat($col[0].style.width) + parseFloat($nextCol[0].style.width);
                    var firstColStart = $col.offset().left;
                    var nextColEnd = $nextCol.offset().left + parseFloat($nextCol.css("padding-left")) + $nextCol.width();
                    var totalWidth = nextColEnd - firstColStart;
                    var markerPosition = $newHandler.offset().left - firstColStart + $newHandler.width() / 2;
                    var firstPercent = markerPosition * 100 / totalWidth;


                    if (firstPercent < 5) {
                        firstPercent = 5;
                    }
                    if (firstPercent > 95) {
                        firstPercent = 95;
                    }

                    firstPercent = totalPercent * firstPercent / 100; //sum of first and next widget width is not 100% when there are more than two columns

                    $col.css('width', firstPercent + '%');
                    $nextCol.css('width', (totalPercent - firstPercent) + '%');

                },
                stop: function (event, ui) {
                    var colWidths = new Array();
                    $.each($widget.find('.ipsCol'), function (index, col) {
                        colWidths.push(parseFloat(col.style.width));
                    });

                    var data = {
                        method: 'adjustWidth',
                        widths: colWidths
                    };
                    $widget.ipWidget('save', data, 0);

                    $(document).trigger('ipWidgetResized');
                }
            });

            $('body').append($newHandler);
        });
    }

    $('.ipsAdminWidgetColsWidthHandler').remove();

    $('.ipWidget-Columns').each(function (index, widget) {
        addResizeHandlers($(widget));
    });


    $(document).trigger('ipWidgetColumnsDragHandlersInit');
};

$(document).on('ipContentManagementInit', ipColumnsInitWidthHandles);
$(document).on('ipWidgetAdded', ipColumnsInitWidthHandles);
$(document).on('ipWidgetDeleted', ipColumnsInitWidthHandles);
$(document).on('ipWidgetMoved', ipColumnsInitWidthHandles);
$(document).on('ipWidgetSaved', ipColumnsInitWidthHandles);
