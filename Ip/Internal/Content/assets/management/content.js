/**
 * @package ImpressPages
 *
 *
 */


var ipContent = new function () {
    "use strict";
    this.deleteWidget = function (widgetId, callback) {
        var $widget = $('#ipWidget-' + widgetId);
        var $subwidgets = $widget.find('.ipWidget');

        var $this = $(this);

        var data = Object();
        data.aa = 'Content.deleteWidget';
        data.securityToken = ip.securityToken;
        data.widgetId = new Array();

        $.each($subwidgets, function (key, widget) {
            var $widget = $(widget);
            data.widgetId.push($widget.data('widgetid'));
        });

        data.widgetId.push(widgetId);


        $.ajax({
            type: 'POST',
            url: ip.baseUrl,
            data: data,
            success: function (response) {
                var $block = $widget.closest('.ipBlock');
                $widget.remove();
                if ($block.children('.ipWidget').length == 0) {
                    $block.addClass('ipbEmpty');
                }


                var $columnsWidget = $block.closest('.ipWidget-Columns');
                if ($columnsWidget.length) {
                    deleteEmptyColumns($columnsWidget.data('widgetid'), function () {
                        $(document).trigger('ipWidgetDeleted', {widgetId: widgetId});
                        if (callback) {
                            callback();
                        }
                    });
                } else {
                    $(document).trigger('ipWidgetDeleted', {widgetId: widgetId});
                    if (callback) {
                        callback();
                    }
                }
            },
            dataType: 'json'
        });
    };

    var deleteEmptyColumns = function (columnswidgetid, callback) {
        var $columnsWidget = $('#ipWidget-' + columnswidgetid);
        var $columns = $columnsWidget.find('> .ipsColsContainer > .ipsCol'); // todo: refactor to remove container selector

        var $emptyColumns = [];
        var $notEmptyColumns = [];
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
                $.each($notEmptyColumn.find(' > .ipBlock > .ipWidget'), function (key, widget) {
                    var $widget = $(widget);
                    moveWidgetHelper($widget.data('widgetid'), columnsWidgetPosition + key, columnsWidgetBlockName, null);
                });
            }

            //remove the whole columns widget
            ipContent.deleteWidget(columnswidgetid, function () {
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
                ipContent.deleteColumn($columnsWidget.data('widgetid'), emptyColumnNames, function () {
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
    };


    this.createWidgetInsideWidget = function (widgetName, targetwidgetid, position, callback) {
        this.splitWidget(targetwidgetid, position, function (firstwidgetid, secondwidgetid) {
            var $firstWidget = $('#ipWidget-' + firstwidgetid);
            var blockName = $firstWidget.closest('.ipBlock').data('ipBlock').name;
            var firstWidgetPosition = $firstWidget.index();
            ipContent.createWidget(blockName, widgetName, firstWidgetPosition + 1, function (widgetId) {
                if (callback) {
                    callback(widgetId);
                }
            });
        });
    };


    this.moveWidgetInsideWidget = function (sourcewidgetid, targetwidgetid, position, callback) {
        this.splitWidget(targetwidgetid, position, function (firstwidgetid, secondwidgetid) {
            var $firstWidget = $('#ipWidget-' + firstwidgetid);
            var blockName = $firstWidget.closest('.ipBlock').data('ipBlock').name;
            var firstWidgetPosition = $firstWidget.index();
            ipContent.moveWidget(sourcewidgetid, firstWidgetPosition + 1, blockName, function (widgetId) {
                if (callback) {
                    callback(widgetId);
                }
            });
        });
    };


    this.splitWidget = function (widgetid, position, callback) {
        var context = this;
        var $widget = $('#ipWidget-' + widgetid);
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
        var splitData = widgetController.splitData($widget.data('widgetdata'), position);
        context.createWidget(blockName, widgetName, widgetPosition, function (firstwidgetid) {
            var $firstWidget = $('#ipWidget-' + firstwidgetid);
            $firstWidget.ipWidget('save', splitData[0], true);

            context.createWidget(blockName, widgetName, widgetPosition + 1, function (secondwidgetid) {
                var $secondWidget = $('#ipWidget-' + secondwidgetid);
                $secondWidget.ipWidget('save', splitData[1], true);
                context.deleteWidget(widgetid, function () {
                    if (callback) {
                        callback(firstwidgetid, secondwidgetid);
                    }
                });

            });
        });


    };

    this.createWidgetToColumn = function (widgetName, targetwidgetid, position, callback) {
        addColumn(targetwidgetid, position, function (newWidgetBlockName) {
            ipContent.createWidget(newWidgetBlockName, widgetName, 0, function (widgetId) {
                var $block = $('#ipBlock-' + newWidgetBlockName);
                $block.find('.ipbExampleContent').remove();
                if (callback) {
                    callback(widgetId);
                }
            });
        });
    };


    this.moveWidgetToColumn = function (sourcewidgetid, targetwidgetid, position, callback) {
        addColumn(targetwidgetid, position, function (newWidgetBlockName) {
            ipContent.moveWidget(sourcewidgetid, 0, newWidgetBlockName, function (widgetId) {
                if (callback) {
                    callback(widgetId);
                }

            });
        });
    };


    this.moveWidgetToSide = function (sourcewidgetid, targetwidgetid, leftOrRight, callback) {
        if (sourcewidgetid == targetwidgetid) {
            return;
        }
        createSpace(targetwidgetid, leftOrRight, function (newWidgetBlockName) {
            ipContent.moveWidget(sourcewidgetid, 0, newWidgetBlockName, function (widgetId) {
                if (callback) {
                    callback(widgetId);
                }

            });
        });
    };

    this.createWidgetToSide = function (widgetName, targetwidgetid, leftOrRight, callback) {

        createSpace(targetwidgetid, leftOrRight, function (newWidgetBlockName) {
            ipContent.createWidget(newWidgetBlockName, widgetName, 0, function (widgetId) {
                var $block = $('#ipBlock-' + newWidgetBlockName);
                $block.find('.ipbExampleContent').remove();
                if (callback) {
                    callback(widgetId);
                }
            });
        });
    };


    this.deleteColumn = function (columnswidgetid, columnNames, callback) {

        var widgetData = {
            method: 'deleteColumn',
            columnName: columnNames
        };
        ipContent.updateWidget(columnswidgetid, widgetData, 1, function (widgetId) {
            if (callback) {
                callback(widgetId);
            }
        });

    };


    var addColumn = function (columnwidgetid, newColPos, callback) {
        var updateData = {
            method: 'addColumn',
            position: newColPos
        };
        ipContent.updateWidget(columnwidgetid, updateData, true, function (newwidgetId) {
            var $colsWidget = $('#ipWidget-' + newwidgetId);
            var $newCol = $colsWidget.find('.ipsCol').eq(newColPos);
            var $newBlock = $newCol.find('.ipBlock');
            var newBlockName = $newBlock.data('ipBlock').name;
            if (callback) {
                callback(newBlockName);
            }
        });
    };

    var createSpace = function (targetwidgetid, leftOrRight, callback) {
        var newWidgetBlockName;
        var $targetWidget = $('#ipWidget-' + targetwidgetid);
        var $targetBlock = $targetWidget.closest('.ipBlock');
        var targetBlockName = $targetBlock.data('ipBlock').name;
        var targetPosition = $targetWidget.index();

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
            };
            ipContent.updateWidget(targetwidgetid, updateData, true, function (newwidgetId) {
                var $colsWidget = $('#ipWidget-' + newwidgetId);
                var $newCol = $colsWidget.find('.ipsCol').eq(newColPos);
                var $newBlock = $newCol.find('.ipBlock');
                var newBlockName = $newBlock.data('ipBlock').name;
                if (callback) {
                    callback(newBlockName);
                }
            });

        } else {
            //create columns widget above target widget
            ipContent.createWidget(targetBlockName, 'Columns', targetPosition, function (widgetId) {
                var columnwidgetid = widgetId;
                var $columnWidget = $('#ipWidget-' + columnwidgetid);
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
                ipContent.moveWidget(targetwidgetid, 0, existingWidgetBlockName, function (newwidgetId) {
                    var staticBlock = $('.ipBlock-existingWidgetBlockName').data('revisionId') == 0;
                    $columnWidget.ipWidget('save', {}, 1, function ($widget) {
                        $widget.closest('.ipBlock').find('.ipbExampleContent').remove();
                        if (callback) {
                            callback(newWidgetBlockName);
                        }
                    });
                });
            });
        }


    };


    this.updateWidget = function (widgetId, widgetData, regeneratePreview, callback) {
        var data = Object();
        var $widget = $('#ipWidget-' + widgetId);
        data.aa = 'Content.updateWidget';
        data.securityToken = ip.securityToken;
        data.widgetId = widgetId;
        data.widgetData = widgetData;
        if (regeneratePreview) {
            data.generatePreview = 1
        }

        $.ajax({
            type: 'POST',
            url: ip.baseUrl,
            data: data,
            success: function (response) {
                if (regeneratePreview) {
                    var newWidget = response.html;
                    var $newWidget = $(newWidget);
                    $newWidget.insertAfter($widget);
                    $newWidget.trigger('ipWidgetReinit');

                    // init any new blocks the widget may have created
                    $(document).ipContentManagement('initBlocks', $newWidget.find('.ipBlock'));
                    $widget.remove();
                }
                if (callback) {
                    callback(response.widgetId);
                }

                var data = {};
                if ($newWidget) {
                    data = {
                        'widgetId': $newWidget.data('widgetid'),
                        'widget': $newWidget
                    }
                }

                $(document).trigger('ipWidgetSaved', data);
            },
            error: function (response) {
                if (callback) {
                    callback(null);
                }
            },
            dataType: 'json'
        });
    };


    this.createWidget = function (block, widgetName, position, callback) {
        var data = {};
        data.aa = 'Content.createWidget';
        data.securityToken = ip.securityToken;
        data.widgetName = widgetName;
        data.position = position;
        data.block = block;

        var $block = $('#ipBlock-' + block);
        data.revisionId = $block.data('revisionid');
        data.languageId = $block.data('languageid');


        $.ajax({
            type: 'POST',
            url: ip.baseUrl,
            data: data,
            success: function (response) {
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

                    $(document).ipContentManagement('initBlocks', $newWidget.find('.ipBlock'));
                    $block.trigger('ipWidgetReinit');
                    $block.trigger('ipWidgetAdded', {
                        'widgetId': response.widgetId,
                        'widget': $newWidget
                    });

                    var widgetController = $newWidget.ipWidget('widgetController');
                    if (widgetController && typeof(widgetController['onAdd']) === 'function') {
                        widgetController.onAdd($newWidget);
                    }
                }

                $(document).trigger('ipWidgetAdded', {
                    'widgetId': $newWidget.data('widgetid'),
                    'widget': $newWidget
                });

                if (callback) {
                    callback($newWidget.data('widgetid'));
                }
            },
            dataType: 'json'
        });

    };


    this.moveWidget = function (widgetId, position, block, callback) {
        var $originalBlock = $('#ipWidget-' + widgetId).closest('.ipBlock');
        moveWidgetHelper(widgetId, position, block, function (newwidgetId) {
            var $columns = $originalBlock.closest('.ipWidget-Columns');
            if ($columns.length) {
                deleteEmptyColumns($columns.data('widgetid'), function () {
                    $(document).trigger('ipWidgetMoved', {widgetId: widgetId});
                    if (callback) {
                        callback(widgetId);
                    }
                    return;
                });
            }

            $(document).trigger('ipWidgetMoved', {widgetId: widgetId});

            if (callback) {
                callback(widgetId);
            }
        });
    };


    var moveWidgetHelper = function (widgetId, position, block, callback) {
        var data = Object();
        data.aa = 'Content.moveWidget';
        data.securityToken = ip.securityToken;
        data.widgetId = widgetId;
        data.position = position;
        data.blockName = block;
        var $block = $('#ipBlock-' + block);
        data.revisionId = $block.data('revisionid');
        data.languageId = $block.data('languageid');


        var $widget = $('#ipWidget-' + widgetId);
        var $originalBlock = $widget.closest('.ipBlock');

        $widget.detach();


        $.ajax({
            type: 'POST',
            url: ip.baseUrl,
            data: data,
            success: function (response) {
                if (response.status == 'error') {
                    alert(response.errorMessage);
                }
                var $block = $('#ipBlock-' + response.block);

                var $newWidget = $(response.widgetHtml);
                if (position == 0) {
                    $block.prepend($newWidget);
                } else {
                    $newWidget.insertAfter($block.find(' > .ipWidget').eq(position - 1));
                }
                $block.trigger('ipWidgetReinit');
                $block.find(' > .ipbExampleContent').remove();
                $block.removeClass('ipbEmpty');

                $(document).ipContentManagement('initBlocks', $newWidget.find('.ipBlock'));
                //check if we need to remove column from original place
                if ($originalBlock.children('.ipWidget').length == 0) {
                    $originalBlock.addClass('ipbEmpty');
                }


                if (callback) {
                    callback(response.newwidgetId);
                }
            },
            dataType: 'json'
        });
    }


};


