/**
 * @package ImpressPages
 *
 *
 */

(function($) {

    var methods = {
    init : function(options) {

        return this.each(function() {

            var $this = $(this);

            var data = $this.data('ipBlock');

            // If the plugin hasn't been initialized yet
            if (!data) {
                $this.delegate('.ipActionWidgetMove', 'click', function(e){e.preventDefault();});
                $this.sortable( {
                    connectWith : '.ipBlock',
                    revert : true,
                    dropOnEmpty : true,
                    forcePlaceholderSize : false,
                    placeholder: 'ipAdminWidgetPlaceholder',
                    handle : '.ipAdminWidgetControls .ipActionWidgetMove',
                    start : function (event, ui) {
                        ui.item.addClass('ipAdminWidgetDrag');
                        ui.item.width(ui.item.find('.ipAdminWidgetMoveIcon').outerWidth());
                        ui.item.height(ui.item.find('.ipAdminWidgetMoveIcon').outerHeight());
                        $('.ipAdminWidgetControls').css('display', 'none');
                    },
                    
                    stop : function (event, ui) {
                        ui.item.removeClass('ipAdminWidgetDrag');
                        ui.item.width('auto');
                        ui.item.height('auto');
                        $('.ipAdminWidgetControls').css('visibility', 'visible');
                        $('.ipAdminWidgetControls').css('display', '');
                    },
                    
                    // this event is fired twice by both blocks, when element is moved from one block to another.
                    update : function(event, ui) {
                        if (!$(ui.item).data('ipWidget')) {
                            // some other object is dragged in. Do nothing.
                            return;
                        }
    
                        // item is dragged out of the block. This action will be handled by the reciever using "receive"
                        if ($(ui.item).parent().data('ipBlock').name != $this.data('ipBlock').name) {
                            return;
                        }
    
                        var instanceId = $(ui.item).data('ipWidget').instanceId;
                        var position = $(ui.item).index();
    
                        var data = Object();
                        data.g = 'standard';
                        data.m = 'content_management';
                        data.a = 'moveWidget';
                        data.instanceId = instanceId;
                        data.position = position;
                        data.blockName = $this.data('ipBlock').name;
                        data.revisionId = $this.data('ipBlock').revisionId;
                        if ($(ui.item).ipWidget('managementState')) {
                            data.managementState = 1;
                        } else {
                            data.managementState = 0;
                        }

                        var urlParts = window.location.href.split('#');
                        var postUrl = urlParts[0];
                        $.ajax( {
                            type : 'POST',
                            url : postUrl,
                            data : data,
                            context : $this,
                            success : methods._moveWidgetResponse,
                            dataType : 'json'
                        });
    
                    },
    
                    receive : function(event, ui) {
                        $element = $(ui.item);
                        // if received element is AdminWidgetButton (insert new widget)
                        if ($element && $element.is('.ipActionWidgetButton')) {
                            $duplicatedDragItem = $('.ipBlock .ipActionWidgetButton');
                            $position = $duplicatedDragItem.index();
                            var newWidgetName = $element.data('ipAdminWidgetButton').name;
                            $duplicatedDragItem.remove();
                            $block = $this;
                            $block.ipBlock('_createWidget', newWidgetName, $position);
                        }
    
                    }
                });
                
                $this.data('ipBlock', {
                    name : $this.attr('id').substr(8),
                    revisionId : $this.data('revisionid'),
                    widgetControlsHtml : options.widgetControlsHtml,
                    contenManagementObject : options.contentManagementObject
                });

                var widgetOptions = new Object;
                widgetOptions.widgetControlls = $this.data('ipBlock').widgetControlsHtml;
                $this.children('.ipWidget').ipWidget(widgetOptions);

                $this.delegate('.ipWidget .ipActionWidgetDelete', 'click', function(event) {
                    event.preventDefault();
                    $(this).trigger('deleteClick.ipBlock');
                });

                $this.delegate('.ipWidget', 'deleteClick.ipBlock', function(event) {
                    var instanceId = $(this).data('ipWidget').instanceId;
                    $(this).trigger('deleteWidget.ipBlock', {
                        'instanceId': instanceId
                    });
                });

                $this.bind('deleteWidget.ipBlock', function(event, data) {
                    $(this).ipBlock('deleteWidget', data.instanceId);
                });

                $this.bind('reinitRequired.ipWidget', function(event) {
                    $(this).ipBlock('reinit');
                });

            }
        });
    },

    reinit : function() {
        return this.each(function() {
            var $this = $(this);
            var widgetOptions = new Object;
            widgetOptions.widgetControlls = $this.data('ipBlock').widgetControlsHtml;
            $(this).children('.ipWidget').ipWidget(widgetOptions);

        });
    },

    _moveWidgetResponse : function(response) {
        var $this = $(this);
        if (response.status == 'success') {
            $('#ipWidget-' + response.oldInstance).replaceWith(response.widgetHtml);
            $this.trigger('reinitRequired.ipWidget');
        }
        // todo show error on error response
    },

    pageSaveStart : function() {
        return this.each(function() {
            var $this = $(this);
            $(this).children('.ipWidget').ipWidget('fetchManaged').ipWidget('save');
        });
    },

    destroy : function() {
        // TODO
    },

    _showError : function(errorMessage) {
        alert(errorMessage);

    },

    deleteWidget : function(instanceId) {
        return this.each(function() {

            var $this = $(this);

            var data = Object();
            data.g = 'standard';
            data.m = 'content_management';
            data.a = 'deleteWidget';
            data.instanceId = instanceId;

            var urlParts = window.location.href.split('#');
            var postUrl = urlParts[0];

            $.ajax( {
            type : 'POST',
            url : postUrl,
            data : data,
            context : $this,
            success : methods._deleteWidgetResponse,
            dataType : 'json'
            });
        });
    },

    _deleteWidgetResponse : function(response) {
        var $this = $(this);
        $this.find('#ipWidget-' + response.widgetId).remove();
        if ($this.children('.ipWidget').length == 0) {
            $this.addClass('ipbEmpty');
        }
    },

    _createWidget : function(widgetName, position) {

        return this.each(function() {

            var $this = $(this);

            var data = Object();
            data.g = 'standard';
            data.m = 'content_management';
            data.a = 'createWidget';
            data.widgetName = widgetName;
            data.position = position;
            data.blockName = $this.data('ipBlock').name;
            data.zoneName = $this.data('ipBlock').zoneName;
            data.pageId = $this.data('ipBlock').pageId;
            data.revisionId = $this.data('ipBlock').revisionId;

            var urlParts = window.location.href.split('#');
            var postUrl = urlParts[0];
            $.ajax( {
            type : 'POST',
            url : postUrl,
            data : data,
            context : $this,
            success : methods._createWidgetResponse,
            dataType : 'json'
            });

        });

    },

    _createWidgetResponse : function(response) {
        var $this = $(this);
        if (response.status == 'error') {
            $.fn.ipBlock('_showError', response.errorMessage);
        }

        if (response.status == 'success') {
            if (response.position == 0) {
                $(this).prepend(response.widgetManagementHtml);
            } else {
                $secondChild = $(this).children('.ipWidget:nth-child(' + response.position + ')');
                $(response.widgetManagementHtml).insertAfter($secondChild);
            }
            $this.trigger('reinitRequired.ipWidget');
            $this.trigger('stateManagement.ipWidget',{
                'instanceId': response.instanceId
            });
            // $this.ipBlock('reinit');

        }
        if ($this.hasClass('ipbEmpty')) {
            $this.removeClass('ipbEmpty');
        }
    }

    };
    


    $.fn.ipBlock = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(jQuery);