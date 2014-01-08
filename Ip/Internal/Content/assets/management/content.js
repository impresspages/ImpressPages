/**
 * @package ImpressPages
 *
 *
 */

var ipContent;

(function($) {

    ipContent = new function() {

        this.addWidgetToSide = function (widgetName, targetWidgetInstanceId, leftOrRight) {
            var $this = $(this);

            var data = Object();
            data.aa = 'Content.addWidgetToSide';
            data.securityToken = ip.securityToken;
            data.widgetName = widgetName;
            data.targetWidgetInstanceId = targetWidgetInstanceId;
            data.leftOrRight = leftOrRight;

            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: $this,
                success: addWidgetToSideResponse,
                dataType: 'json'
            });
        };

        var addWidgetToSideResponse = function (response) {
            
        };

    };

})(ip.jQuery);
