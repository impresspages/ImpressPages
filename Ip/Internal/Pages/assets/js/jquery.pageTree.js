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
                var $this = $(this);
                var data = $this.data('ipPageTree');
                $this.ipPageTree('destroy');

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
        plugins.push('dnd');
        //plugins.push('contextmenu');

        $this.jstree({

            'plugins': plugins,
            'json_data': {data: data},
            'types': {
                // -2 do not need depth and children count checking
                'max_depth': -2,
                'max_children': -2,
                'types': {
                    // The default type
                    'page': {
                        'valid_children': [ 'page' ],
                        'icon': {
                            'image': ipFileUrl('Ip/Internal/Pages/assets/img/file.png')
                        }
                    }
                }
            },

//            'ui': {
//                'select_limit': 1,
//                'select_multiple_modifier': 'alt',
//                'selected_parent_close': 'select_parent',
//                'select_prev_on_delete': true
//            }
//            'cookies': {
//                'save_opened': 'PagesOpen',
//                'save_selected': 'PagesSelected'
//            }
            'dnd': {
                'open_timeout': 1
            }

// TODO reimplement
//            'contextmenu': {
//                'show_at_node': false,
//                'select_node': true,
//                'items': jsTreeCustomMenu
//            }


        });



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

})(ip.jQuery);


