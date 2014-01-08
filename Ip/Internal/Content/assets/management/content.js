/**
 * @package ImpressPages
 *
 *
 */

var ipContent;

(function($) {

    ipContent = new function() {

        this.addWidgetToSide = function (widgetName, targetWidgetInstanceId, leftOrRight) {

            console.log(widgetName);
            console.log(targetWidgetInstanceId);
            console.log(leftOrRight);
        };
    };

})(ip.jQuery);
