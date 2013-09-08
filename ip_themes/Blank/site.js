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
    };

    var removeColClass = function (tag) {
        var i = 0;
        for (i; i < cols; i = i + 1) {
            $(tag).removeClass('col_' + (i + 1));
        }
    };

    var adjustRowEqual = function (row) {
        var originalWidth = row[0].width * optimalColWidth;
        var idealItemsPerRow = Math.floor(curColWidth() * cols / originalWidth);
        if (idealItemsPerRow < 1) {
            idealItemsPerRow = 1;
        }
        while (row.length % idealItemsPerRow !== 0 && cols % idealItemsPerRow === 0) {
            //while item count doesn't divide by idealItemsPerRow in equal parts
            //and
            //while grid cols count doesn't divide in idealItemsPerRow count
            //at the end of the day it should go down to 1
            idealItemsPerRow -= 1;
        }
        var newItemWidth = cols / idealItemsPerRow;

        var curWidth = row[0].width * curColWidth();
        var curDeflection = deflection(originalWidth, curWidth);
        var newDeflection = deflection(originalWidth, newItemWidth * curColWidth());
        if (newDeflection < curDeflection) {
            $.each(row, function (index, item) {
                removeColClass(item.tag);
                $(item.tag).addClass('col_' + newItemWidth);
            });
        } else {
            restoreDefault(row);
        }
    };

    var tab = function(depth) {
        var answer = '';
        for (var i = 0; i < depth; i ++) {
            answer = answer + ' ';
        }
        return answer;
    }

    /**
     * recursion way to find best split
     * 0. check if we need to split
     * 1. split row in the middle. Once more to left and once more to right.
     * 2. recurring call of itself to split each of the parts we got.
     * 4. return the best way out of those three and no split at all
     *
     * @param row
     */
    var findBestSplit = function (row, depth) {
        //console.log(tab(depth) + 'START BEST SPLIT' + row);

        if (depth > 6) {
            console.log(tab(depth) + 'likely infinite recursion');
            return [row];
        }
        if (row.length === 1) {
            return [row];
        }
        var ratio = optimalColWidth / curColWidth(),
            bestSplitRows = [row],
            bestSplitDeflection = rowDeflection(row, cols), //no split is our starting point.
            splits = [],
            partialSplits,
            newSplit,
            curSplitDeflection;

        //check if we need to split
        if (rowWidth(row) * ratio <= cols) {
            return [row];
        }

        //get likely the best splits
        partialSplits = splitInTheMiddle(row);

        $.each(partialSplits, function (index, partialSplit) {
            console.log(tab(depth) + 'partial split' + row + ' = ' + partialSplit[0] + ' | ' + partialSplit[1]);
            newSplit = [];
            newSplit.concat(['test']);
            newSplit = newSplit.concat(findBestSplit(partialSplit[0], depth + 1), findBestSplit(partialSplit[1], depth + 1));
            splits.push(newSplit);
        });
        $.each(splits, function (index, split) {
            curSplitDeflection = rowsDeflection(split);
            if (curSplitDeflection < bestSplitDeflection) {
                curSplitDeflection = bestSplitDeflection;
                bestSplitRows = split;
            }
        });
        return bestSplitRows;

    };

    /**
     *
     * return two or three splits
     *
     **/
    var splitInTheMiddle = function (row) {
        var splits = [];
        var i = 0;
        var leftPartWidth = 0;
        if (row.length < 2) {
            return [];
        }
        if (row.length === 2) {
            return [[row.slice(0, 1), row.slice(1, 2)]];
        }


        //iterate until leftPartWidth is greater than middle
        while (leftPartWidth <= rowWidth(row) / 2) {
            leftPartWidth += row[i].width;
            i += 1;
        }


        //our first split is right after the middle is reached
        splits.push([row.slice(0, i), row.slice(i)]);
        //next split is one step back
        if (i > 1) {
            splits.push([row.slice(0, i - 1), row.slice(i - 1)]);
        }
        //if your one step back is actually in the middle, then make on cut one more step back
        if (leftPartWidth - row[i].width === rowWidth(row) / 2 && i > 2) {
            splits.push([row.slice(0, i - 2), row.slice(i - 1)]);
        }
        return splits;
    };


    var fitToCols = function (row, cols) {
        var newWidths = [],
            curWidth = rowWidth(row),
            diff = 0,
            sacrificeIndex,
            sacrificeSignificance,
            tmpSignificance;

        //set new widths the same as old ones
        $.each(row, function (index, item) {
            newWidths.push(item.width);
        });


        //while new width not equal the required one
        while (cols !== curWidth) {
            if (cols > curWidth) {
                diff = 1;
            } else {
                diff = -1;
            }
            //find which column can handle resize with least effort
            sacrificeIndex = 0;
            sacrificeSignificance = 10000; //huge. Any other should be lower
            $.each(row, function (index, item) {
                tmpSignificance = deflection(row[index].width, newWidths[index] + diff);
                if (tmpSignificance < sacrificeSignificance && newWidths[index] + diff > 0) {
                    sacrificeSignificance = tmpSignificance;
                    sacrificeIndex = index;
                }
            });

            newWidths[sacrificeIndex] += diff;
            curWidth += diff;
        }
        //set new box widths
        $.each(row, function (index, item) {
            removeColClass(item.tag);
            $(item.tag).addClass('col_' + newWidths[index]);
        });

    }

    var adjustRowInequal = function (row) {
        var split = findBestSplit(row, 1);

        $.each(split, function (index, oneRow) {
            fitToCols(oneRow, cols);
        });
    }

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
                adjustRowInequal(row);
            }
        }
    };

    /**
     * find deflection of several rows passed as array
     * @param split
     */
    var rowsDeflection = function (rows) {
        var deflectionSum = 0;
        $.each(rows, function (index, row) {
            deflectionSum += rowDeflection(row, cols);
        });
        return deflectionSum / rows.length;
    }

    /**
     * calculate deflection sum if the will fit to given cols
     * @param row
     */
    var rowDeflection = function (row, cols) {
        var ratio = optimalColWidth / curColWidth();
        return deflection(cols, rowWidth(row) * ratio);
    }

    var deflection = function (originalWidth, curWidth) {
        if (curWidth > originalWidth) {
            return curWidth / originalWidth;
        } else {
            return originalWidth / curWidth;
        }
    }


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

