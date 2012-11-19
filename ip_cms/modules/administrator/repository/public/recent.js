

"use strict";

(function($) {

    var methods = {

        init : function(options) {

            return this.each(function() {

                var $this = $(this);

                var data = $this.data('ipRepositoryRecent');
                if (!data) {
                    var $elFinder = $this.find('.ipmElFinder');
                    var elf = $elFinder.elfinder({
                        url : ip.baseUrl + ip.moduleDir + 'administrator/repository/elfinder/php/connector.php',  // connector URL (REQUIRED)
                        commandsOptions : {
                            getfile : {
                                multiple : true,
                                oncomplete : 'destroy'
                            }
                        },
                        commands : [
                            'upload', 'search', 'sort'
                        ],
                        resizable: false,
                        ui : ['toolbar'],
                        contextmenu : false,
                        height: 330,
                        getFileCallback: function(){}
                    }).elfinder('instance');


//                    $elFinder.bind('upload', __ipModuleRepositoryFileBrowserDestroy);


                }
            });
        }



    };

    $.fn.ipRepositoryRecent = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipRepositoryRecent');
        }

    };

})(jQuery);



