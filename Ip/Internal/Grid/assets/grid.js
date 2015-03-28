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
                var uniqueId = Math.floor((Math.random() * 10000000) + 1);
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
        var data = urlParams($this.data('gateway'));
        data.jsonrpc = '2.0';
        data.method = 'init';
        data.gridHash = window.location.hash;
        data.params = {};

        $.ajax({
            type: 'GET',
            url: $this.data('gateway').split('?')[0],
            data: data,
            context: $this,
            success: initResponse,
            error: function (response) {
                $this.html(response.responseText);
            },
            dataType: 'json'
        });


        $(window).off('hashchange.grid' + uniqueId).on('hashchange.grid' + uniqueId, function () {
            $.proxy(init, $this)();
        });
    };


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
                    $this.trigger('htmlChanged.ipGrid');
                    $.proxy(bindEvents, $this)();
                    $this.find('.ipsPages .disabled a').on('click', function(e) {
                        e.preventDefault();
                        //By default last link is just a #. Clicking on it resets pages section to the root. Prevent that from happening.
                    });

                    $this.trigger('init.ipGrid');
                    ipInitForms();
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

        $grid.find('.ipsAction[data-method]').off().on('click', function (e) {
            e.preventDefault();
            var $this = $(this);
            var data = urlParams($grid.data('gateway'));
            data.jsonrpc = '2.0';
            data.method = $this.data('method');

            var params = $this.data('params');
            if (params !== null) {
                data.params = params;
            }

            data.gridHash = window.location.hash;

            $.ajax({
                type: 'GET',
                url: $grid.data('gateway').split('?')[0],
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

        $grid.find('.ipsPageSize .ipsPageSizeSetting').on('click', function (e) {
            e.preventDefault();
            var $this = $(this);

            var data = urlParams($grid.data('gateway'));
            data.jsonrpc = '2.0';
            data.method = 'setPageSize';

            data.params = {};
            data.params.pageSize = $this.data('rows');

            data.gridHash = window.location.hash;

            $.ajax({
                type: 'GET',
                url: $grid.data('gateway').split('?')[0],
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

        $grid.find('.ipsGridLanguageSetting').on('click', function (e) {
            e.preventDefault();
            var $this = $(this);

            var data = urlParams($grid.data('gateway'));
            data.jsonrpc = '2.0';
            data.method = 'setLanguage';

            data.params = {};
            data.params.language = $this.data('value');

            data.gridHash = window.location.hash;

            $.ajax({
                type: 'GET',
                url: $grid.data('gateway').split('?')[0],
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


        $grid.find('.ipsDelete').off().on('click', function () {
            var $this = $(this);
            var $row = $this.closest('.ipsRow');
            var id = $row.data('id');
            var $modal = $grid.find('.ipsDeleteModal');
            $modal.modal();
            $modal.find('.ipsConfirm').focus();
            $modal.find('.ipsConfirm').off().on('click', function () {
                $this.trigger('beforeRecordDeleted.ipGrid', id);
                $.proxy(deleteRecord, $grid)(id);
                $modal.modal('hide');
                $this.trigger('afterRecordDeleted.ipGrid', id);
            });
        });

        $grid.find('.ipsUpdate').off().on('click', function () {
            var $this = $(this);
            var $row = $this.closest('.ipsRow');
            var id = $row.data('id');
            var $modal = $grid.find('.ipsUpdateModal');
            $modal.modal();
            $.proxy(loadUpdateForm, $grid)($modal, id);
        });


        $grid.find('.ipsCreate').off().on('click', function () {
            var $modal = $grid.find('.ipsCreateModal');
            var $form = $modal.find('.ipsBody form');
            var data = $grid.data('gateway');
            $modal.modal();
            $modal.find('.form-group').not('.type-blank').first().find('input').focus();
            if (!$form.find('input[name=aa]').length) {
                //$form.append($('<input type="hidden" name="aa" />').val(data.aa));
                $form.attr('action', data);
            }
            $form.find('input[name=gridHash]').remove();
            $form.append($('<input type="hidden" name="gridHash" />').val(window.location.hash));
            $form.on('ipSubmitResponse', function (e, response) {
                if (!response.error) {
                    $modal.modal('hide');
                    //form has been successfully submitted.
                    $.proxy(doCommands, $grid)(response.result.commands);
                }
            });
            $modal.find('.ipsConfirm').off().on('click', function () {
                $modal.find('.ipsBody form').submit();
            });

            $modal.find('.ipsBody form').off('ipOnFail.gridTabs').on('ipOnFail.gridTabs', function(e, errors) {
                var $form = $(this);
                var $errorField = $form.find('.form-group.has-error');
                var $errorPane = $errorField.closest('.tab-pane');
                if ($errorPane.length) {
                    var id = $errorPane.attr('id')
                    $modal.find('.nav-tabs li a[href=#' + id + ']').tab('show');
                    $modal.animate({
                        scrollTop: $errorField.offset().top
                    }, 300);
                }
            });

            $grid.trigger('createModalOpen.ipGrid', $modal);

        });

        $grid.find('.ipsSearch').off().on('click', function () {
            var $modal = $grid.find('.ipsSearchModal');
            var $form = $modal.find('.ipsBody form');
            var data = $grid.data('gateway');
            $modal.modal();
            $modal.find('.form-group').not('.type-blank').first().find('input').focus();
            if (!$form.find('input[name=aa]').length) {
                //$form.append($('<input type="hidden" name="aa" />').val(data.aa));
                $form.attr('action', data);

            }
            $form.find('input[name=gridHash]').remove();
            $form.append($('<input type="hidden" name="gridHash" />').val(window.location.hash));

            $form.on('ipSubmitResponse', function (e, response) {
                if (!response.error) {
                    $modal.modal('hide');
                    //form has been successfully submitted.
                    $.proxy(doCommands, $grid)(response.result.commands);
                }
            });

            $modal.find('.ipsSearch').off().on('click', function () {
                $modal.find('.ipsBody form').submit();
            });

            $modal.find(".nav-tabs").on("click", "a", function(e) {
                e.preventDefault();
                $(this).tab('show');
            });

            $modal.find('.ipsBody form').off('ipOnFail.gridTabs').on('ipOnFail.gridTabs', function(e, errors) {
                var $form = $(this);
                var $errorField = $form.find('.form-group.has-error');
                var $errorPane = $errorField.closest('.tab-pane');
                if ($errorPane.length) {
                    var id = $errorPane.attr('id')
                    $modal.find('.nav-tabs li a[href=#' + id + ']').tab('show');
                    $modal.animate({
                        scrollTop: $errorField.offset().top
                    }, 300);
                }
            });

            $grid.trigger('searchModalOpen.ipGrid', $modal);
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

        $grid.find('.ipsMoveModal .ipsConfirm').off('click.grid').on('click.grid', function (e) {
            $('.ipsMoveModal form').submit();
        });
        $grid.find('.ipsSetPosition').off('click.grid').on('click.grid', function(e) {
            e.preventDefault();
            $('.ipsMoveModal').modal();
            $('.ipsMoveModal input[name=position]').focus();
            $('.ipsMoveModal').find('input[name=id]').val($(this).closest('.ipsRow').data('id'));
        });

        $('.ipsMoveModal form').off('submit.grid').on('submit.grid', $.proxy(moveToPosition, $grid));

    };

    var moveToPosition = function (event, ui) {
        event.preventDefault();
        var $form = $(event.currentTarget);
        var position = $form.find('input[name=position]').val();
        if (position == '') {
            alert('Please enter an integer number');
            return;
        }

        var id = $form.find('input[name=id]').val();



        var $grid = this;
        var data = {};
        data.method = 'movePosition';
        data.params = {};
        data.params.id = id;
        data.params.position = position;
        data.securityToken = ip.securityToken;
        data.gridHash = window.location.hash;
        $.ajax({
            type: 'POST',
            url: $grid.data('gateway'),
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
        $('.ipsMoveModal').modal('hide');

    };

    var startDrag = function (event, ui) {
        ui.item.data('originIndex', ui.item.index());
    };

    var dragStop = function (event, ui) {
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
        var data = {};
        data.method = 'move';
        data.params = {};
        data.params.id = id;
        data.params.targetId = targetId;
        data.params.beforeOrAfter = beforeOrAfter;
        data.securityToken = ip.securityToken;
        data.gridHash = window.location.hash;
        $.ajax({
            type: 'POST',
            url: $grid.data('gateway'),
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
    };

    var dragFix = function (e, tr) {
        var $originals = tr.children();
        var $helper = tr.clone();
        $helper.children().each(function (index) {
            $(this).width($originals.eq(index).width())
        });
        return $helper;
    };

    var loadUpdateForm = function ($modal, id) {
        var $grid = this;
        var data = urlParams($grid.data('gateway'));
        data.method = 'updateForm';
        data.params = {};
        data.params.id = id;
        data.gridHash = window.location.hash;
        data.securityToken = ip.securityToken;
        $.ajax({
            type: 'POST',
            url: $grid.data('gateway').split('?')[0],
            data: data,
            context: $grid,
            dataType: 'json',
            success: function (response) {
                $modal.find('.ipsBody').html(response.result);
                var $form = $modal.find('.ipsBody form');
                var data = $grid.data('gateway');
                if (!$form.find('input[name=aa]').length) {
                    //$form.append($('<input type="hidden" name="aa" />').val(data.aa));
                    $form.attr('action', data);

                }
                $form.find('input[name=gridHash]').remove();
                $form.append($('<input type="hidden" name="gridHash" />').val(window.location.hash));

                $form.on('ipSubmitResponse', function (e, response) {
                    if (!response.error) {
                        $modal.modal('hide');
                        //form has been successfully submitted.
                        $.proxy(doCommands, $grid)(response.result.commands);
                    }
                });
                $modal.find('.form-group').not('.type-blank').first().find('input').focus();
                $modal.find('.ipsConfirm').off().on('click', function () {
                    $modal.find('.ipsBody form').submit();
                });
                ipInitForms();


                $modal.find(".nav-tabs").on("click", "a", function(e) {
                    e.preventDefault();
                    $(this).tab('show');
                });

                $modal.find('.ipsBody form').off('ipOnFail.gridTabs').on('ipOnFail.gridTabs', function(e, errors) {
                    var $form = $(this);
                    var $errorField = $form.find('.form-group.has-error');
                    var $errorPane = $errorField.closest('.tab-pane');
                    if ($errorPane.length) {
                        var id = $errorPane.attr('id')
                        $modal.find('.nav-tabs li a[href=#' + id + ']').tab('show');
                        $modal.animate({
                            scrollTop: $errorField.offset().top
                        }, 300);
                    }
                });

                //new bootstrap can't handle backdrop height properly. So we fix it.
                var $backdrop = $modal.children('.modal-backdrop');
                var $dialog = $modal.children('.modal-dialog');
                if ($backdrop.outerHeight(true) < $dialog.outerHeight(true)) {
                    $backdrop.css('height', 0).css('height', $dialog.outerHeight(true));
                }


                $grid.trigger('updateModalOpen.ipGrid', $modal);

            },
            error: function (response) {
                if (ip.debugMode || ip.developmentMode) {
                    alert(response);
                }
            }
        });

    };


    var deleteRecord = function (id) {
        var $grid = this;
        var data = {};
        data.method = 'delete';
        data.params = {};
        data.params.id = id;
        data.gridHash = window.location.hash;
        data.securityToken = ip.securityToken;
        $.ajax({
            type: 'POST',
            url: $grid.data('gateway'),
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
    };

    var deleteResponse = function (response) {
        var $this = this;
        if (!response.error) {
            $.proxy(doCommands, $this)(response.result);
        } else {
            if (ip.debugMode || ip.developmentMode) {
                alert(response.errorMessage);
            }
        }

    };

    var urlParams = function (url) {
        url = '?' + url;
        var parts = url.split('?'),
            query = parts[parts.length - 1],
            urlParams = {},
            match,
            pl     = /\+/g,  // Regex for replacing addition symbol with a space
            search = /([^&=]+)=?([^&]*)/g,
            decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); };

        while (match = search.exec(query)) {
            urlParams[decode(match[1])] = decode(match[2]);
        }
        return urlParams;
    };

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
