/**
 * @package ImpressPages
 *
 *
 */

(function($) {
    "use strict";

    var methods = {
        init : function(options) {
            return this.each(function() {
                var $this = $(this);

                var data = $this.data('ipPageTree');

                // If the plugin hasn't been initialized yet
                if ( ! data ) {
                    $this.data('ipPageTree', {
                        menuName: options.menuName,
                        languageId: options.languageId
                    });
                    $.proxy(refresh, $this)(options.menuName, options.languageId);
                } else {
                    $this.jstree('deselect_all');
                }

            });
        },

        refresh : function() {
            return this.each(function() {
                console.log('refresh');
                var $this = $(this);
                var data = $this.data('ipPageTree');
                $this.ipPageTree('refresh');

                $this.ipPageTree({
                    menuName: data.menuName,
                    languageId: data.languageId
                });
            });
        },

        destroy : function() {
            return this.each(function() {
                var $this = $(this);
                $this.data('ipPageTree', false);
                $this.jstree('destroy');
                $this.html('');
            });
        }



    };

    var refresh = function (menuName, languageId) {
        var $this = this;
        var data = {
            menuName : menuName,
            languageId : languageId,
            aa : 'Pages.getPages'
        };

        $.ajax({
            type : 'GET',
            url : ip.baseUrl,
            data : data,
            context : $this,
            success : refreshResponse,
            dataType : 'json'
        });
    };

    var refreshResponse = function (response) {
        var $this = this;

        $.proxy(initializeTreeManagement, $this)(response.tree);


    }



    /**
     * Initialize tree management
     *
     * @param id
     *            id of div where management should be initialized
     */
    function initializeTreeManagement(data) {
        var $this = this;
        var plugins = [ 'themes', 'json_data', 'types'];
        plugins.push('ui');
        plugins.push('crrm');
        //plugins.push('contextmenu');

//        $this.jstree({ 'core' : {
//            'data' : [
//                'Simple root node',
//                {
//                    'text' : 'Root node 2',
//                    'state' : {
//                        'opened' : true,
//                        'selected' : true
//                    },
//                    'children' : [
//                        { 'text' : 'Child 1' },
//                        'Child 2'
//                    ]
//                }
//            ]
//        } });

        $this.jstree({
            'core' : {
                'data' : data,
                "check_callback" : function (operation, node, node_parent, node_position, more)  {


                    if (node_parent.id && node_parent.id != '#') {
                        var $parentNode = $('#' + node_parent.id);
                        ipPagesDropPageId = $parentNode.attr('pageid');
                    } else {
                        ipPagesDropPageId = null;
                    }
                    ipPagesDropPagePosition = node_position;
                }
            },
            'plugins' : [
                'dnd', 'wholerow'
            ]
        });


//        $this.jstree({
//
//            'plugins': plugins,
//            'json_data': {data: data},
//            'types': {
//                // -2 do not need depth and children count checking
//                'max_depth': -2,
//                'max_children': -2,
//                'types': {
//                    // The default type
//                    'page': {
//                        'valid_children': [ 'page' ],
//                        'icon': {
//                            'image': ipFileUrl('Ip/Internal/Pages/assets/img/file.png')
//                        }
//                    }
//                }
//            },
//            "themes" : {
//                "theme" : "impresspages",
//                "dots" : false,
//                "icons" : false
//            }
//        });



    }



    $.fn.ipPageTree = function(method) {

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(jQuery);


