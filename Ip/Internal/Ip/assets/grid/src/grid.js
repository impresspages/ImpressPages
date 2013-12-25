/**
 * @package ImpressPages
 *
 *
 */


(function ($) {
    "use strict";

    var methods = {
        init: function (options) {

            return this.each(function () {
                var $this = $(this);
                var data = $this.data('ipGridInit');
                // If the plugin hasn't been initialized yet
                if (!data) {
                    $this.data('ipGridInit', Object());

                    $.proxy(init, $this)();
                }
            });
        },

        refresh: function (options) {

            return this.each(function () {
                var $this = $(this);
                $.proxy(init, $this)();

            });
        }
    };

    var init = function () {
        var $this = this;
        var data = $this.data('gateway');
        data.jsonrpc = '2.0';
        data.method = 'init';
        data.hash = window.location.hash;
        data.params = {};

        $.ajax({
            type: 'GET',
            url: ip.baseUrl,
            data: data,
            context: $this,
            success: initResponse,
            error: function (response) {
                if (ip.debugMode || ip.developmentMode) {
                    alert(response);
                }
            },
            dataType: 'json'
        });
    }


    var initResponse = function (response) {
        var $this = this;
        $.proxy(doCommands, $this)(response.result);
    };

    var doCommands = function (commands) {
        var $this = this;
        $.each(commands, function (key, value) {
            switch (value.command) {
                case 'setHtml':
                    $this.html(value.html);
                    $.proxy(bindEvents, $this)();
                    $this.trigger('init.grid');
                    break;
                case 'setHash':
                    window.location.hash = value.hash;
                    break;
            }
        });
    };

    var bindEvents = function () {
        var $grid = this;

        $grid.find('.ipsAction[data-method]').off().on('click', function() {
            var $this = $(this);
            var data = $grid.data('gateway');
            data.jsonrpc = '2.0';
            data.method = $this.data('method');

            var params = $this.data('params');
            if (params !== null) {
                data.params = params;
            }

            data.hash = window.location.hash;

            $.ajax({
                type: 'GET',
                url: ip.baseUrl,
                data: data,
                context: $grid,
                success: initResponse,
                error: function (response) {
                    if (ip.debugMode || ip.developmentMode) {
                        alert(response);
                    }
                },
                dataType: 'json'
            });
        });

        $grid.find('.ipsDelete').off().on('click', function() {
            var $this = $(this);
            var $row = $this.closest('.ipsRow');
            var id = $row.data('id');
            var $modal = $grid.find('.ipsDeleteModal');
            $modal.modal();
            $modal.find('.ipsConfirm').off().on('click', function() {
                $.proxy(deleteRecord, $grid)(id);
            });
        });
    };

    var deleteRecord = function(id) {
        var $grid = this;
        var data = $grid.data('gateway');
        data.method = 'delete';
        data.params = {};
        data.params.id = id;
        data.securityToken = ip.securityToken;
        $.ajax({
            type: 'POST',
            url: ip.baseUrl,
            data: data,
            context: $grid,
            success: deleteResponse,
            error: function (response) {
                if (ip.debugMode || ip.developmentMode) {
                    alert(response);
                }
            },
            dataType: 'json'
        });
    }

    var deleteResponse = function (response) {
        var $this = this;
        if (!response.error) {
            $.proxy(doCommands, $this)(response.result);
        } else {
            if (ip.debugMode || ip.developmentMode) {
                alert(response.errorMessage);
            }
        }

    }

    $.fn.ipGrid = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipWidget');
        }

    };

})(jQuery);