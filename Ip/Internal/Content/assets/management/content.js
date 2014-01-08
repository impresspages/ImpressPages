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


        this.createWidget = function(revisionId, block, widgetName, position) {
            var data = {};
            data.aa = 'Content.createWidget';
            data.securityToken = ip.securityToken;
            data.widgetName = widgetName;
            data.position = position;
            data.block = block;
            data.revisionId = revisionId;


            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                success: createWidgetResponse,
                dataType: 'json'
            });

        };

        var createWidgetResponse = function(response) {
            if (response.status == 'error') {
                alert(response.errorMessage);
            }
            var $block = $('#ipBlock-' + response.block);

            if (response.status == 'success') {
                var $newWidget = $(response.widgetHtml);
                if (response.position == 0) {
                    $block.prepend($newWidget);
                } else {
                    $secondChild = $block.children('.ipWidget:nth-child(' + response.position + ')');
                    $newWidget.insertAfter($secondChild);
                }
                $block.trigger('reinitRequired.ipWidget');
                $block.trigger('addWidget.ipWidget',{
                    'instanceId': response.instanceId,
                    'widget': $newWidget
                });
                var widgetController = $newWidget.ipWidget('widgetController');
                if (widgetController && typeof(widgetController['onAdd']) === 'function') {
                    widgetController.onAdd($newWidget);
                }
            }
            if ($block.hasClass('ipbEmpty')) {
                $block.removeClass('ipbEmpty');
            }
        };

    };

})(ip.jQuery);
