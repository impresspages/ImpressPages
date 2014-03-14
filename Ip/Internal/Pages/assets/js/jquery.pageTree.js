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
                    //console.log('deselect');
                    $this.jstree('deselect_all');
                }

            });
        },

        refresh : function() {
            return this.each(function() {
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

        $this.jstree({
            'core' : {
                "themes" : { "name" : 'ImpressPages', "stripes" : false, "icons" : false },
                'data' : data,
                "check_callback" : function (operation, node, node_parent, node_position, more)  {
                    var $node = $('#' + node.id);
                    ipPageDragId = $node.attr('pageid');

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


