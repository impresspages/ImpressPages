/**
 * @package ImpressPages
 *
 *
 */

var ipContent;

(function($) {

    ipContent = new function() {

        this.createWidgetToSide = function (widgetName, targetWidgetInstanceId, leftOrRight, callback) {
            var createdWidgetInstanceId;
            var $targetWidget =  $('#ipWidget-' + targetWidgetInstanceId);
            var $targetBlock = $targetWidget.closest('.ipBlock');
            var targetBlockName = $targetBlock.data('ipBlock').name;
            var revisionId = ip.revisionId;
            var targetPosition = $targetWidget.index();

            //create columns widget above target widget
            ipContent.createWidget(revisionId, targetBlockName, 'IpColumns', targetPosition, function (instanceId) {
                var columnWidgetInstanceId = instanceId;
                var $columnWidget = $('#ipWidget-' + columnWidgetInstanceId);
                if (leftOrRight == 'left') {
                    //put target widget to right
                    var $existingWidgetBlock = $columnWidget.find('.ipBlock').eq(1);
                    var $newWidgetBlock = $columnWidget.find('.ipBlock').eq(0);
                } else {
                    //put target widget to left
                    var $existingWidgetBlock = $columnWidget.find('.ipBlock').eq(0);
                    var $newWidgetBlock = $columnWidget.find('.ipBlock').eq(1);
                }
                var existingWidgetBlockName = $existingWidgetBlock.data('ipBlock').name
                var newWidgetBlockName = $newWidgetBlock.data('ipBlock').name

                //move target widget to right / left column
                ipContent.moveWidget(targetWidgetInstanceId, 0, existingWidgetBlockName, revisionId, function (newInstanceId) {
                    $('#ipWidget-' + newInstanceId).remove();
                    $columnWidget.ipWidget('save', {}, 1, function() {
                        ipContent.createWidget(revisionId, newWidgetBlockName, widgetName, 0, function (instanceId) {
                            console.log('create widget response');

                            createdWidgetInstanceId = instanceId;
                        });
                    });
                });
            });

            if (callback) {
                callback($newWidget.data('widgetinstanceid'));
            }

//            var $this = $(this);
//
//            var data = Object();
//            data.aa = 'Content.createWidgetToSide';
//            data.securityToken = ip.securityToken;
//            data.widgetName = widgetName;
//            data.targetWidgetInstanceId = targetWidgetInstanceId;
//            data.leftOrRight = leftOrRight;
//
//            $.ajax({
//                type: 'POST',
//                url: ip.baseUrl,
//                data: data,
//                context: $this,
//                success: createWidgetToSideResponse,
//                dataType: 'json'
//            });
        };

        var createWidgetToSideResponse = function (response) {

        };


        this.createWidget = function(revisionId, block, widgetName, position, callback) {
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
                success: function(response) {
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
                        $(document).ipContentManagement('initBlocks', $newWidget.find('.ipBlock'));

                        var widgetController = $newWidget.ipWidget('widgetController');
                        if (widgetController && typeof(widgetController['onAdd']) === 'function') {
                            widgetController.onAdd($newWidget);
                        }
                    }
                    if ($block.hasClass('ipbEmpty')) {
                        $block.removeClass('ipbEmpty');
                    }

                    if (callback) {
                        callback($newWidget.data('widgetinstanceid'));
                    }
                },
                dataType: 'json'
            });

        };


        this.moveWidget = function (instanceId, position, block, revisionId, callback) {
            var data = Object();
            data.aa = 'Content.moveWidget';
            data.securityToken = ip.securityToken;
            data.instanceId = instanceId;
            data.position = position;
            data.blockName = block;
            data.revisionId = revisionId;

            $.ajax( {
                type : 'POST',
                url : ip.baseUrl,
                data : data,
                success : function(response) {
                    if (response.status == 'error') {
                        alert(response.errorMessage);
                    }
                    var $block = $('#ipBlock-' + response.block);

                    $('#ipWidget-' + response.oldInstance).replaceWith(response.widgetHtml);
                    $block.trigger('reinitRequired.ipWidget');
                    if (callback) {
                        callback(response.newInstanceId);
                    }
                },
                dataType : 'json'
            });
        };



    };

})(ip.jQuery);
