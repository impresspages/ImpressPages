(function($) {
    "use strict";
    var methods = {

        init : function(options) {

            return this.each(function() {

                var $this = $(this);

                var data = $this.data('ipWidget_ipFile_container');

                // If the plugin hasn't been initialized yet
                var files = null;
                if (options.files) {
                    files = options.files;
                } else {
                    files = [];
                }

                if (!data) {
                    $this.data('ipWidget_ipFile_container', {
                        files : files,
                        fileTemplate : options.fileTemplate
                    });

                    for (var i in files) {
                        $this.ipWidget_ipFile_container('addFile', files[i]['fileName'], files[i]['title'], 'present');
                    }
                    $this.bind('removeFile.ipWidget_ipFile', function(event, fileObject) {
                        var $fileObject = $(fileObject);
                        $fileObject.ipWidget_ipFile_container('removeFile', $fileObject);
                    });

                    $( ".ipWidget_ipFile_container" ).sortable({
                        handle: '.ipsFileMove',
                        cancel: false
                    });
                }
            });
        },

        addFile : function (fileName, title, status) {
            var $this = this;
            var $newFileRecord = $this.data('ipWidget_ipFile_container').fileTemplate.clone().removeClass('ipsFileTemplate').addClass('ipsFile');
            $newFileRecord.ipWidget_ipFile_file({'status' : status, 'fileName' : fileName, 'title' : title});

            $this.append($newFileRecord);

        },

        removeFile : function ($fileObject) {
            $fileObject.hide();
            $fileObject.ipWidget_ipFile_file('setStatus', 'deleted');

        },

        getFiles : function () {
            var $this = this;
            return $this.find('.ipsFile');
        },


        destroy : function () {
            return this.each(function() {
                var $this = $(this);
                $this.html('');
                $.removeData(this, 'ipWidget_ipFile_container');
            });
        }
    };

    $.fn.ipWidget_ipFile_container = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(jQuery);
