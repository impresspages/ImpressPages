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
                var uniqueId = Math.floor((Math.random()*10000000)+1);
                // If the plugin hasn't been initialized yet
                if (!data) {
                    $this.data('ipGridInit', Object());

                    $.proxy(init, $this)(uniqueId);
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

    var init = function (uniqueId) {
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


        $(window).off('hashchange.grid' + uniqueId).on('hashchange.grid' + uniqueId, function () {
            console.log('change');
            $.proxy(init, $this)();
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
                    ipModuleForm.init();
                    break;
                case 'setHash':
                    window.location.hash = value.hash;
                    break;
                case 'showMessage':
                    alert(value.message);
                    break;
            }
        });
    };

    var bindEvents = function () {
        var $grid = this;

        $grid.find('.ipsAction[data-method]').off().on('click', function(e) {
            e.preventDefault();
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
            $modal.find('.ipsConfirm').focus();
            $modal.find('.ipsConfirm').off().on('click', function() {
                $.proxy(deleteRecord, $grid)(id);
                $modal.modal('hide');
            });
        });

        $grid.find('.ipsUpdate').off().on('click', function() {
            var $this = $(this);
            var $row = $this.closest('.ipsRow');
            var id = $row.data('id');
            var $modal = $grid.find('.ipsUpdateModal');
            $modal.modal();
            $.proxy(loadUpdateForm, $grid)($modal, id);

        });



        $grid.find('.ipsCreate').off().on('click', function() {
            var $this = $(this);
            var $modal = $grid.find('.ipsCreateModal');
            $modal.modal();
            $modal.find('.form-group').not('.type-blank').first().find('input').focus();
            $modal.find('.ipsBody form').validator(validatorConfig);
            $modal.find('.ipsBody form').on('submit', $.proxy(createFormSubmit, $grid));
            $modal.find('.ipsConfirm').off().on('click', function() {
                $modal.find('.ipsBody form').submit();
            });
        });

        $grid.find('.ipsSearch').off().on('click', function() {
            var $this = $(this);
            var $modal = $grid.find('.ipsSearchModal');
            $modal.modal();
            $modal.find('.form-group').not('.type-blank').first().find('input').focus();
            $modal.find('.ipsBody form').validator(validatorConfig);
            $modal.find('.ipsBody form').on('submit', $.proxy(searchFormSubmit, $grid));
            $modal.find('.ipsSearch').off().on('click', function() {
                $modal.find('.ipsBody form').submit();
            });
        });


        if ($grid.find('.ipsDrag').length) {
            $grid.find("table tbody").sortable({
                handle: '.ipsDrag',
                cancel: false,
                helper: dragFix,
                axis: "y",
                start: $.proxy(startDrag, $grid),
                stop: $.proxy(dragStop, $grid)
            });
        }

    };

    var startDrag = function(event, ui) {
            ui.item.data('originIndex', ui.item.index());
    }

    var dragStop = function(event, ui) {
        var originIndex = ui.item.data('originIndex');
        var curRow = ui.item;
        var currentIndex = curRow.index();

        if (originIndex == currentIndex) {
            return;
        }

        var targetId = null;
        var beforeOrAfter = 'after';
        if (currentIndex == 0) {
            beforeOrAfter = 'before';
            targetId = curRow.next().data('id');
        } else {
            targetId = curRow.prev().data('id');
        }

        var $grid = this;
        var id = ui.item.data('id');
        var data = $grid.data('gateway');
        data.method = 'move';
        data.params = {};
        data.params.id = id;
        data.params.targetId = targetId;
        data.params.beforeOrAfter = beforeOrAfter;
        data.securityToken = ip.securityToken;
        data.hash = window.location.hash;
        $.ajax({
            type: 'POST',
            url: ip.baseUrl,
            data: data,
            context: $grid,
            dataType: 'json',
            success: function (response) {
                $.proxy(doCommands, $grid)(response.result);
            },
            error: function (response) {
                if (ip.debugMode || ip.developmentMode) {
                    alert(response);
                }
            }
        });
    }

    var dragFix = function(e, tr) {
        var $originals = tr.children();
        var $helper = tr.clone();
        $helper.children().each(function(index)
        {
            $(this).width($originals.eq(index).width())
        });
        return $helper;
    };

    var loadUpdateForm = function($modal, id){
        var $grid = this;
        var data = $grid.data('gateway');
        data.method = 'updateForm';
        data.params = {};
        data.params.id = id;
        data.securityToken = ip.securityToken;
        $.ajax({
            type: 'POST',
            url: ip.baseUrl,
            data: data,
            context: $grid,
            dataType: 'json',
            success: function (response) {
                $modal.find('.ipsBody').html(response.result);
                $modal.find('.ipsBody form').validator(validatorConfig);
                $modal.find('.ipsBody form').on('submit', $.proxy(updateFormSubmit, $grid));
                $modal.find('.form-group').not('.type-blank').first().find('input').focus();
                $modal.find('.ipsConfirm').off().on('click', function() {
                    $modal.find('.ipsBody form').submit();
                    $modal.modal('hide');
                });
                ipModuleForm.init();

            },
            error: function (response) {
                if (ip.debugMode || ip.developmentMode) {
                    alert(response);
                }
            }
        });

    }

    var updateFormSubmit = function(e) {
        var $grid = this;
        var form = $grid.find('.ipsUpdateModal .ipsBody form');
        var data = $grid.data('gateway');


        // client-side validation OK.
        if (!e.isDefaultPrevented()) {
            $.ajax({
                url: ip.baseUrl,
                dataType: 'json',
                type : 'POST',
                data: form.serialize() + '&aa=' + data.aa + '&hash=' + encodeURIComponent(window.location.hash),
                success: function (response){
                    if (!response.error) {
                        //form has been successfully submitted.
                        $.proxy(doCommands, $grid)(response.result.commands);
                    } else {
                        //PHP controller says there are some errors
                        if (response.errors) {
                            form.data("validator").invalidate(response.errors);
                        }
                    }
                },
                error: function (response) {
                    if (ip.debugMode || ip.developmentMode) {
                        alert(response);
                    }
                }
            });
        }
        e.preventDefault();
    }

    var createFormSubmit = function(e) {
        var $grid = this;
        var form = $grid.find('.ipsCreateModal .ipsBody form');
        var data = $grid.data('gateway');
        var $modal = $grid.find('.ipsCreateModal');

        $modal.modal('hide');

        // client-side validation OK.
        if (!e.isDefaultPrevented()) {
            $.ajax({
                url: ip.baseUrl,
                dataType: 'json',
                type : 'POST',
                data: form.serialize() + '&aa=' + data.aa,
                success: function (response){
                    if (!response.error) {
                        //form has been successfully submitted.
                        $.proxy(doCommands, $grid)(response.result.commands);
                    } else {
                        //PHP controller says there are some errors
                        if (response.errors) {
                            form.data("validator").invalidate(response.errors);
                        }
                    }
                },
                error: function (response) {
                    if (ip.debugMode || ip.developmentMode) {
                        alert(response);
                    }
                }
            });
        }
        e.preventDefault();
    }

    var searchFormSubmit = function(e) {
        var $grid = this;
        var form = $grid.find('.ipsSearchModal .ipsBody form');
        var data = $grid.data('gateway');
        var $modal = $grid.find('.ipsSearchModal');
        data.hash = null;

        $modal.modal('hide');

        // client-side validation OK.
        if (!e.isDefaultPrevented()) {
            $.ajax({
                url: ip.baseUrl,
                dataType: 'json',
                type : 'GET',
                data: form.serialize() + '&aa=' + data.aa,
                success: function (response){
                    if (!response.error) {
                        //form has been successfully submitted.
                        $.proxy(doCommands, $grid)(response.result.commands);
                    } else {
                        //PHP controller says there are some errors
                        if (response.errors) {
                            form.data("validator").invalidate(response.errors);
                        }
                    }
                },
                error: function (response) {
                    if (ip.debugMode || ip.developmentMode) {
                        alert(response);
                    }
                }
            });
        }
        e.preventDefault();
    }


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

})(ip.jQuery);
