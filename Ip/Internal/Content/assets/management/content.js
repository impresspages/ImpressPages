/**
 * @package ImpressPages
 *
 *
 */

var ipContent;

(function($) {
    "use strict";
    ipContent = new function() {

        this.deleteWidget = function (instanceId, callback) {
            var $widget = $('#ipWidget-' + instanceId);
            var $subwidgets = $widget.find('.ipWidget');

            var $this = $(this);

            var data = Object();
            data.aa = 'Content.deleteWidget';
            data.securityToken = ip.securityToken;
            data.instanceId = Array();

            $.each($subwidgets, function (key, widget) {
                var $widget = $(widget);
                data.instanceId.push($widget.data('widgetinstanceid'));
            });

            data.instanceId.push(instanceId);


            $.ajax( {
                type : 'POST',
                url : ip.baseUrl,
                data : data,
                success : function(response) {
                    var $block = $widget.closest('.ipBlock');
                    $widget.remove();
                    if ($block.children('.ipWidget').length == 0) {
                        $this.addClass('ipbEmpty');
                    }


                    var $columnsWidget = $block.closest('.ipWidget-Columns');
                    if ($columnsWidget.length) {
                        deleteEmptyColumns($columnsWidget.data('widgetinstanceid'), function() {
                            if (callback) {
                                callback();
                            }
                        });
                    }
                },
                dataType : 'json'
            });
        }

        var deleteEmptyColumns = function (columnsWidgetInstanceId, callback) {
            var $columnsWidget = $('#ipWidget-' + columnsWidgetInstanceId);
            var $columns = $columnsWidget.find('> .ipsCol');

            var $emptyColumns = new Array();
            var $notEmptyColumns = new Array();
            var columnsWidgetBlockName = $columnsWidget.closest('.ipBlock').data('ipBlock').name;
            var columnsWidgetPosition = $columnsWidget.index();

            $.each($columns, function (key, column) {
                var $column = $(column);
                if ($column.find('.ipWidget').length == 0) {
                    $emptyColumns.push($column);
                } else {
                    $notEmptyColumns.push($column);
                }
            });

            if ($emptyColumns.length + 1 >= $columns.length) {
                //move widgets from not empty column above columns widget
                if ($notEmptyColumns.length == 1) {
                    var $notEmptyColumn = $notEmptyColumns[0];
                    $.each($notEmptyColumn.find('.ipWidget'), function (key, widget) {
                        var $widget = $(widget);
                        ipContent.moveWidget($widget.data('widgetinstanceid'), columnsWidgetPosition + key, columnsWidgetBlockName, ip.revisionId);
                    });
                }

                //remove the whole columns widget
                ipContent.deleteWidget(columnsWidgetInstanceId, function() {
                    if (callback) {
                        callback();
                    }
                });
            } else {
                //remove just empty columns
                var emptyColumnNames = new Array();
                $.each($emptyColumns, function (key, $emptyColumn) {
                    emptyColumnNames.push($emptyColumn.find('> .ipBlock').data('ipBlock').name);
                });

                if (emptyColumnNames.length > 0) {
                    //remove the columns
                    ipContent.deleteColumn($columnsWidget.data('widgetinstanceid'), emptyColumnNames, function() {
                        if (callback) {
                            callback();
                        }
                    });
                } else {
                    if (callback) {
                        callback();
                    }
                }
            }
        }


        this.createWidgetToWidget = function(widgetName, targetWidgetInstanceId, position, callback) {
            var revisionId = ip.revisionId;
            //var $targetWidget = $('#ipWidget-' + targetWidgetInstanceId);
            this.splitWidget(targetWidgetInstanceId, position, function(firstWidgetInstanceId, secondWidgetInstanceId) {
//                    ipContent.createWidget(revisionId, blockName, widgetName, 0, function (instanceId) {
//                        var $block = $('#ipBlock-' + newWidgetBlockName);
//                        $block.find('.ipbExampleContent').remove();
//                        if (callback) {
//                            callback(instanceId);
//                        }
//                    });

            });

        }


        this.splitWidget = function (widgetInstanceId, position, callback) {
            var $widget = $('#ipWidget-' + widgetInstanceId);
            var blockName = $widget.closest('.ipBlock').data('ipBlock').name;
            var widgetPosition = $widget.index();
            var widgetName = $widget.data('widgetname');
            var widgetController = $widget.data('widgetController');
            if (!widgetController.splitData) {
                if (ip.debugMode) {
                    alert('Widget ' + widgetName + ' javascript controller IpWidget_' + widgetName + ' has no method splitData');
                } else {
                    //do nothing
                }
                return;
            }
            var splitData = widgetController.spiltData($widget.data('widgetdata'), position);
            this.deleteWidget(widgetInstanceId, function() {
                this.createWidget(ip.revisionId, widgetName, widgetPosition, function (firstWidgetInstanceId) {
                    var $firstWidget = $('#ipWidget-' + firstWidgetInstanceId);
                    $firstWidget.save()
                    this.createWidget(ip.revisionId, widgetName, widgetPosition + 1, function (secondWidgetInstanceId) {

                    });
                });
            });


        }

        this.createWidgetToColumn = function(widgetName, targetWidgetInstanceId, position, callback) {
            var revisionId = ip.revisionId;
            addColumn(targetWidgetInstanceId, position, function (newWidgetBlockName) {
                ipContent.createWidget(revisionId, newWidgetBlockName, widgetName, 0, function (instanceId) {
                    var $block = $('#ipBlock-' + newWidgetBlockName);
                    $block.find('.ipbExampleContent').remove();
                    if (callback) {
                        callback(instanceId);
                    }
                });
            });
        }


        this.moveWidgetToColumn = function(sourceWidgetInstanceId, targetWidgetInstanceId, position, callback) {
            var revisionId = ip.revisionId;
            addColumn(targetWidgetInstanceId, position, function (newWidgetBlockName) {
                ipContent.moveWidget(sourceWidgetInstanceId, 0, newWidgetBlockName, revisionId, function (instanceId) {
                    if (callback) {
                        callback(instanceId);
                    }

                });
            });
        }


        this.moveWidgetToSide = function (sourceWidgetInstanceId, targetWidgetInstanceId, leftOrRight, callback) {
            var revisionId = ip.revisionId;

            createSpace(targetWidgetInstanceId, leftOrRight, function(newWidgetBlockName) {
                ipContent.moveWidget(sourceWidgetInstanceId, 0, newWidgetBlockName, revisionId, function (instanceId) {
                    if (callback) {
                        callback(instanceId);
                    }

                });
            });
        };

        this.createWidgetToSide = function (widgetName, targetWidgetInstanceId, leftOrRight, callback) {
            var revisionId = ip.revisionId;

            createSpace(targetWidgetInstanceId, leftOrRight, function(newWidgetBlockName) {
                ipContent.createWidget(revisionId, newWidgetBlockName, widgetName, 0, function (instanceId) {
                    var $block = $('#ipBlock-' + newWidgetBlockName);
                    $block.find('.ipbExampleContent').remove();
                    if (callback) {
                        callback(instanceId);
                    }
                });
            });
        };


        this.deleteColumn = function (columnsWidgetInstanceId, columnNames, callback) {

            var widgetData = {
                method: 'deleteColumn',
                columnName: columnNames
            }
            ipContent.updateWidget(columnsWidgetInstanceId, widgetData, 1, function (instanceId) {
                if (callback) {
                    callback(instanceId);
                }
            });

        }


        var addColumn = function (columnWidgetInstanceId, newColPos, callback) {
            var updateData = {
                method: 'addColumn',
                position: newColPos
            }
            ipContent.updateWidget(columnWidgetInstanceId, updateData, true, function (newInstanceId) {
                var $colsWidget = $('#ipWidget-' + newInstanceId);
                var $newCol = $colsWidget.find('.ipsCol').eq(newColPos);
                var $newBlock = $newCol.find('.ipBlock');
                var newBlockName = $newBlock.data('ipBlock').name;
                if (callback) {
                    callback(newBlockName);
                }
            });
        }

        var createSpace = function (targetWidgetInstanceId, leftOrRight, callback) {
            var newWidgetBlockName;
            var $targetWidget =  $('#ipWidget-' + targetWidgetInstanceId);
            var $targetBlock = $targetWidget.closest('.ipBlock');
            var targetBlockName = $targetBlock.data('ipBlock').name;
            var targetPosition = $targetWidget.index();
            var revisionId = ip.revisionId;

            if ($targetWidget.hasClass('ipWidget-Columns')) {
                //create additional column on existing columns widget
                var colCount = $targetWidget.find('.ipsCol').length;
                var newColPos;

                if (leftOrRight == 'left') {
                    newColPos = 0;
                } else {
                    newColPos = colCount;
                }

                var updateData = {
                    method: 'addColumn',
                    position: newColPos
                }
                ipContent.updateWidget(targetWidgetInstanceId, updateData, true, function (newInstanceId) {
                    var $colsWidget = $('#ipWidget-' + newInstanceId);
                    var $newCol = $colsWidget.find('.ipsCol').eq(newColPos);
                    var $newBlock = $newCol.find('.ipBlock');
                    var newBlockName = $newBlock.data('ipBlock').name;
                    if (callback) {
                        callback(newBlockName);
                    }
                });

            } else {
                //create columns widget above target widget
                ipContent.createWidget(revisionId, targetBlockName, 'Columns', targetPosition, function (instanceId) {
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
                    var existingWidgetBlockName = $existingWidgetBlock.data('ipBlock').name;
                    newWidgetBlockName = $newWidgetBlock.data('ipBlock').name;
                    //move target widget to right / left column
                    ipContent.moveWidget(targetWidgetInstanceId, 0, existingWidgetBlockName, revisionId, function (newInstanceId) {
                        $('#ipWidget-' + newInstanceId).remove();
                        $columnWidget.ipWidget('save', {}, 1, function($widget) {
                            $widget.closest('.ipBlock').find('.ipbExampleContent').remove();
                            if (callback) {
                                callback(newWidgetBlockName);
                            }
                        });
                    });
                });
            }



        }


        this.updateWidget = function (instanceId, widgetData, regeneratePreview, callback) {
            var data = Object();
            var $widget = $('#ipWidget-' + instanceId);
            data.aa = 'Content.updateWidget';
            data.securityToken = ip.securityToken;
            data.instanceId = instanceId;
            data.widgetData = widgetData;
            if (regeneratePreview) {
                data.generatePreview = 1
            }

            $.ajax( {
                type : 'POST',
                url : ip.baseUrl,
                data : data,
                success : function(response) {
                    if (regeneratePreview) {
                        var newWidget = response.html;
                        var $newWidget = $(newWidget);
                        $newWidget.insertAfter($widget);
                        $newWidget.trigger('reinitRequired.ipWidget');

                        // init any new blocks the widget may have created
                        $(document).ipContentManagement('initBlocks', $newWidget.find('.ipBlock'));
                        $widget.remove();
                    }
                    if (callback) {
                        callback(response.instanceId);
                    }
                },
                error: function(response) {
                    console.log('save error');
                    console.log(response);
                    if (callback) {
                        callback(null);
                    }
                },
                dataType : 'json'
            });
        }


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

                    if ($block.hasClass('ipbEmpty')) {
                        $block.removeClass('ipbEmpty');
                    }


                    $block.find(' > .ipbExampleContent').remove();


                    if (response.status == 'success') {

                        var $newWidget = $(response.widgetHtml);
                        if (response.position == 0) {
                            $block.prepend($newWidget);
                        } else {
                            var $secondChild = $block.children('.ipWidget:nth-child(' + response.position + ')');
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

            var $originalBlock = $('#ipWidget-' + instanceId).closest('.ipBlock');

            $.ajax( {
                type : 'POST',
                url : ip.baseUrl,
                data : data,
                success : function(response) {
                    if (response.status == 'error') {
                        alert(response.errorMessage);
                    }
                    var $block = $('#ipBlock-' + response.block);

                    var $widget = $('#ipWidget-' + response.oldInstance);
                    var $newWidget = $(response.widgetHtml);
                    $widget.replaceWith($newWidget);
                    $newWidget.detach();
                    if (position == 0) {
                        $block.prepend($newWidget);
                    } else {
                        $newWidget.insertAfter($block.find(' > .ipWidget').eq(position - 1));
                    }
                    $block.trigger('reinitRequired.ipWidget');
                    $block.find(' > .ipbExampleContent').remove();
                    $block.removeClass('ipbEmpty');


                    //check if we need to remove column from original place
                    if ($originalBlock.children('.ipWidget').length == 0) {
                        $originalBlock.addClass('ipbEmpty');
                    }
                    var $columnsWidget = $originalBlock.closest('.ipWidget-Columns');
                    if ($columnsWidget.length) {
                        deleteEmptyColumns($columnsWidget.data('widgetinstanceid'), function() {
                            if (callback) {
                                callback(response.newInstanceId);
                            }
                            return;
                        });
                    }

                    if (callback) {
                        callback(response.newInstanceId);
                    }
                },
                dataType : 'json'
            });
        };



    };

})(ip.jQuery);
