$(document).ready(function () {
    "use strict";
    $('.ipWidget-IpImageGallery li a, .ipWidget-IpImage a').colorbox({
        rel: 'ipwImage',
        maxWidth: '90%',
        maxHeight: '90%'
    });
    $('.topmenu')
        .touchMenuHover() // mimics iOS behavior allowing to get hover state without clicking
        .find('.toggle').on('click', function (e) { // opens menu in mobile view
            e.preventDefault();
            $(this).next('ul').css('display', 'block');
        });
});







$(document).ready(function () {
    "use strict";
    var optimalColWidth = 80;
    var cols = 12;
    var $previous = null;
    var rows = [];

    var getTagCols = function (tag) {
        var classList =$(tag).attr('class').split(/\s+/);
        var answer = null;
        $.each(classList, function (index, item){
            var parts = item.split('_');
            if (parts[0] === 'col' && parts[1]) {
                answer = parseInt(parts[1]);
            }
        });
        return answer;
    };

    var rowWidth = function (row) {
        var width = 0;
        $.each(row, function (index, item){
            width += item.width;
        });
        return width;

    };

    var restoreDefault = function (row) {
        $.each(row, function (index, item){
            removeColClass (item.tag);
            $(item.tag).addClass('col_' + item.width);
        });
    };

    var rowItemsEqual = function (row) {
        var firstWidth = row[0].width;
        var equal = true;
        $.each(row, function (index, item) {
            if (item.width != firstWidth) {
                equal = false;
            }
        });
        return equal;
    }

    var removeColClass = function (tag) {
        var i = 0;
        for (i; i < cols; i = i + 1) {
            $(tag).removeClass('col_' + (i + 1));
        }
    }

    var adjustRowEqual = function (row) {
        var originalWidth = row[0].width * optimalColWidth;
        var curWidth = row[0].width * curColWidth();
        var curDeflection = deflection(originalWidth, curWidth);
        var newDeflection = deflection(originalWidth, cols * curColWidth());
        if (newDeflection < curDeflection) {
            $.each(row, function (index, item) {
                removeColClass(item.tag);
                $(item.tag).addClass('col_' + cols);
            });
        } else {
            restoreDefault(row);
        }
    };

    var curColWidth = function () {
        return $(document).width() / cols;
    };

    var adjustRow = function (row) {
        if (curColWidth() >= optimalColWidth) {
            restoreDefault(row);
        } else {
            if (rowItemsEqual(row)) {
                adjustRowEqual(row);
            } else {
                //
            }
        }
    };


    var deflection = function (originalWidth, curWidth) {
        if (curWidth > originalWidth) {
            return curWidth / originalWidth;
        } else {
            return originalWidth / curWidth;
        }
    }
//    var calcDeflection = function (elements, widths) {
//        $.each(elements, function (index, item) {
//            var width = item.width;
//            if (width[index]) {
//
//            }
//        });
//    }

    var adjust = function (rows) {
        $.each(rows, function(index, value) {
            adjustRow(value);
        });
    };


    var row = [];
    $.each($('[class*="col_"]'), function (index, value) {
        var tagWidth = getTagCols(value);
        if (rowWidth(row) + tagWidth > cols) {
            //if cols count will exceed our grid, create new row
            rows.push(row);
            row = [];
        }

        row.push({
            width: tagWidth,
            tag: value
        });


        if (rowWidth(row) >= cols) {
            //if we reached grid col count, create new row
            rows.push(row);
            row = [];
        }
    });

console.log(rows);
    $(window).resize(function () {
        adjust(rows);
    });
});







/*
 * Izilla touchMenuHover jQuery plugin v1.6
 * Allows ULs (or any element of your choice) that open on li:hover to open on tap/click on mobile platforms such as iOS, Android, WP7, WP8, BlackBerry, Bada, WebOS, 3DS & WiiU
 *
 * Copyright (c) 2013 Izilla Partners Pty Ltd
 *
 * http://izilla.com.au
 *
 * Licensed under the MIT license
 */
;(function(a){a.fn.touchMenuHover=function(j){var f=a.extend({childTag:"ul",closeElement:"",forceiOS:false,openClass:"tmh-open"},j);var d=a(this).find("a"),i="3ds|android|bada|bb10|hpwos|iemobile|kindle fire|opera mini|opera mobi|opera tablet|rim|silk|wiiu",c="|ipad|ipod|iphone",b,g="aria-haspopup",e="html",h;if(f.childTag.toString().toLowerCase()!=="ul"||f.forceiOS){i+=c}b=new RegExp(i,"gi");if(d.length>0&&b.test(navigator.userAgent)){d.each(function(){var m=a(this),l=m.parent("li"),k=l.siblings().find("a");if(m.next(f.childTag).length>0){l.attr(g,true)}m.click(function(o){var n=a(this);o.stopPropagation();k.removeClass(f.openClass);if(!n.hasClass(f.openClass)&&n.next(f.childTag).length>0){o.preventDefault();n.addClass(f.openClass)}})});if(f.closeElement.length>1){e+=","+f.closeElement}h=a(e);if("ontouchstart" in window){h.css("cursor","pointer")}h.click(function(){d.removeClass(f.openClass)})}return this}})(jQuery);

