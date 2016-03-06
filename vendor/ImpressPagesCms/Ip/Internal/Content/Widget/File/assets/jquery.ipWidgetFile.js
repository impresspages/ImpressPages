(function($) {
    "use strict";
    var methods = {
        init : function(options) {

            return this.each(function() {

                var $this = $(this);
                var data = $this.data('ipWidget_ipFile_file');

                // If the plugin hasn't been initialized yet
                if (!data) {
                    var data = {
                        title : '',
                        fileName : '',
                        status : 'new'
                    };

                    if (options.title) {
                        data.title = options.title;
                    }
                    if (options.fileName) {
                        data.fileName = options.fileName;
                    }
                    if (options.status) {
                        data.status = options.status;
                    }

                    $this.data('ipWidget_ipFile_file', {
                        title : data.title,
                        fileName : data.fileName,
                        status : data.status
                    });
                    $this.find('.ipsFileTitle').val(data.title);
                }

                $this.find('.ipsFileLink').attr('href', ipFileUrl('file/repository/' + data.fileName));
                $this.find('.ipsFileRemove').bind('click', function(event){
                    event.preventDefault();
                    $this = $(this);
                    $this.trigger('removeClick.ipWidget_ipFile');
                });
                $this.bind('removeClick.ipWidget_ipFile', function(event) {
                    $this.trigger('removeFile.ipWidget_ipFile', this);
                });
                return $this;
            });
        },

        getTitle : function() {
            var $this = this;
            return $this.find('.ipsFileTitle').val();
        },

        getFileName : function() {
            var $this = this;
            var tmpData = $this.data('ipWidget_ipFile_file');
            return tmpData.fileName;
        },

        getStatus : function() {
            var $this = this;
            var tmpData = $this.data('ipWidget_ipFile_file');
            return tmpData.status;
        },

        setStatus : function(newStatus) {
            var $this = $(this);
            var tmpData = $this.data('ipWidget_ipFile_file');
            tmpData.status = newStatus;
            $this.data('ipWidget_ipFile_file', tmpData);

        }
    };

    $.fn.ipWidget_ipFile_file = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }
    };

})(jQuery);
