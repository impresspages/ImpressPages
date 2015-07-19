var ipPages = null;
var ipPagesDropPageId;
var ipPagesDropPagePosition;
var ipPagesStartPagePosition;
var ipPagesStartPageParentId;
var ipPageDragId;

(function ($) {
    "use strict";

    var app = angular.module('Pages', []).directive('menulistPostRepeatDirective', function () {
        return function (scope, element, attrs) {
            if (scope.$last) {
                pageMenuList.init();
            }
        };
    });

    app.run(function ($rootScope) {
        $rootScope.$on('$locationChangeSuccess', function (e, newUrl, oldUrl) {
            $rootScope.$broadcast('PathChanged', newUrl);
        });
    });

    ipPages = function ($scope, $location) {
        //init
        $scope.activeLanguage = {id: null, code: null};
        $scope.activeMenu = {alias: ''};
        $scope.copyPageId = false;
        $scope.cutPageId = false;
        $scope.selectedPageId = null;
        $scope.languageList = languageList;
        $scope.menuList = menuList;
        $scope.initialized = false;
        $scope.allowActions = !getQuery('disableActions');
        $scope.ipPagesLanguagesPermission = ipPagesLanguagesPermission;

        var hashIsBeingApplied = false;

        $scope.$on('PathChanged', function (event, path) {
            var menuName = getHashParams().menu;
            var languageCode = getHashParams().language;
            var pageId = getHashParams().page;

            if (!$scope.initialized) {
                if (languageCode == null) {
                    languageCode = languageList[0].code;
                }
                if (menuName == null && menuList[0]) {
                    menuName = menuList[0].alias;
                }
            }

            if (languageCode && languageCode != $scope.activeLanguage.code) {
                $.each(languageList, function (key, value) {
                    if (value.code == languageCode) {
                        $scope.activateLanguage(value);
                    }
                });
            }

            if (!$scope.activeMenu || menuName && menuName != $scope.activeMenu.alias || $scope.activeLanguage.code != $scope.activeMenu.languageCode) {
                var newActiveMenu = null;
                $.each(menuList, function (key, value) {
                    if (value.alias == menuName && value.languageCode == $scope.activeLanguage.code) {
                        newActiveMenu = value;
                    }
                });
                if (newActiveMenu == null) {
                    newActiveMenu = getFirstMenuOfLanguage($scope.activeLanguage);
                }
                $scope.activateMenu(newActiveMenu);
            }

            if (pageId && pageId != $scope.selectedPageId) {
                $scope.activatePage(pageId, $scope.activeMenu.alias);
            }
        });


        $scope.setMenuHash = function (menu) {
            hashIsBeingApplied = true;
            updateHash(null, menu.alias, false);
            hashIsBeingApplied = false;
        };

        $scope.setLanguageHash = function (language) {
            updateHash(language.code, null, false);
        };

        $scope.activateLanguage = function (language) {
            $scope.activeLanguage = language;
            showPages();
        };

        $scope.activateMenu = function (menu) {
            $scope.activeMenu = menu;
            if (menu) {
                $scope.selectedPageId = menu.id;
            } else {
                $scope.selectedPageId = null;
            }

            showPages();
        };

        $scope.activatePage = function (pageId) {
            $scope.selectedPageId = pageId;
            var $properties = $('.ipsProperties');
            $properties.ipPageProperties({
                pageId: pageId
            });

            // updating title dynamically
            $properties.off('update.ipPages').on('update.ipPages', function () {
                var title = $properties.find('input[name=title]').val();
                if (!title) {
                    title = $properties.find('input[name=metaTitle]').val();
                }
                if ($scope.activeMenu.type == 'list') { // list view
                    getTreeDiv().find('.ipsRow.active .ipsDrag').text(title);
                } else { // tree view
                    getTreeDiv().jstree('rename_node', getTreeDiv().jstree('get_selected'), escapeHtml(title));
                }
            });

            // removing element from list/tree
            $properties.off('delete.ipPages').on('delete.ipPages', function () {
                if (confirm(ipTranslationAreYouSure)) {
                    var nextId = null;

                    //detect which page has to be selected after this one is deleted
                    if ($scope.activeMenu.type === 'list') { // list view
                        var cur = $('.ipsTreeDiv tr[data-id=' + $scope.selectedPageId + ']');
                        var next = $('.ipsTreeDiv tr').eq(cur.index() + 1).first();
                        if (next.length) {
                            nextId = next.data('id');
                        } else {
                            var prev = $('.ipsTreeDiv tr').eq(cur.index() - 1).first();
                            if (prev.length) {
                                nextId = prev.data('id');
                            }
                        }
                    } else {
                        var $cur = $('.ipsTreeDiv li[pageid=' + $scope.selectedPageId + ']');
                        var $next = $cur.next();
                        if ($next.length) {
                            nextId = $next.attr('pageid');
                        } else {
                            var $prev = $cur.prev();
                            if ($prev.length) {
                                nextId = $prev.attr('pageid');
                            }
                        }
                    }

                    //actually delete the page
                    deletePage($scope.selectedPageId, function () {
                        $scope.selectedPageId = null;
                        if ($scope.activeMenu.type === 'list') { // list view
                            getPagesContainer().ipGrid('refresh');
                            if (nextId) {
                                $scope.activatePage(nextId, $scope.activeMenu.alias);
                            }
                        } else {
                            getTreeDiv().jstree('delete_node', getTreeDiv().jstree('get_selected'));
                            if (nextId) {
                                getTreeDiv().find('#page_' + nextId + ' a').click();
                            }
                        }
                        $scope.$apply();
                    });
                }
            });
            $properties.off('edit.ipPages').on('edit.ipPages', function () {
                editPage($scope.selectedPageId);
            });

            // making page visually active
            if ($scope.activeMenu.type == 'list') { // list view
                getTreeDiv().find('.ipsRow').removeClass('active');
                getTreeDiv().find('[data-id="' + $scope.selectedPageId + '"]').addClass('active');
            } else { // tree view
                var $nodeLink = $('#page_' + $scope.selectedPageId + ' a');
                if (!$nodeLink.hasClass('jstree-clicked')) {
                    hashIsBeingApplied = true;
                    getTreeDiv().on('ready.jstree', function () {
                        getTreeDiv().jstree("deselect_all");
                        getTreeDiv().jstree("select_node", '#page_' + $scope.selectedPageId);
                    });
                    hashIsBeingApplied = false;
                }
            }
        };

        $scope.addPageModal = function () {
            var $modal = $('.ipsAddModal');
            $modal.find('input[name=title]').val('');
            $modal.modal();

            $modal.on('shown.bs.modal', function () {
                $modal.find('input[name=title]').focus();
            });

            $modal.find('.ipsAdd').off('click').on('click', function () {
                $modal.find('form').submit()
            });

            var $positionSelect = $modal.find('form select[name=position]');
            if ($scope.selectedPageId) {
                $positionSelect.find('option[value=above]').show();
                $positionSelect.find('option[value=child]').show();
                $positionSelect.find('option[value=below]').show();
                $positionSelect.val($scope.activeMenu.defaultPositionWhenSelected);
                if ($scope.activeMenu.type == 'list') {
                    $positionSelect.find('option[value=child]').hide();
                }
            } else {
                $positionSelect.val($scope.activeMenu.defaultPosition);
                $positionSelect.find('option[value=above]').hide();
                $positionSelect.find('option[value=child]').hide();
                $positionSelect.find('option[value=below]').hide();
            }

            $modal.find('form').off('submit').on('submit', function (e) {

                e.preventDefault();
                var title = $modal.find('input[name=title]').val();
                var isVisible = $modal.find('input[name=isVisible]').is(':checked') ? 1 : 0;

                var parentId = $scope.activeMenu.id;
                var position = 0;
                switch($modal.find('select[name=position]').val()) {
                    default:
                    case 'top':
                        //Default settings are just fine
                        break;
                    case 'above':
                        if ($scope.selectedPageId && $scope.activeMenu.type != 'list') {
                            var $selectedPage = $('#page_' + $scope.selectedPageId);
                            var $parent = $selectedPage.parent().closest('li');
                            if ($parent.length) {
                                parentId = $parent.attr('pageid');
                            }
                            position = $selectedPage.index();
                        } else {
                            position = $('.ipsTreeDiv .active').index();
                            var curVariables = getHashParams();
                            if (curVariables.gpage) {
                                position = position + (curVariables.gpage - 1) * listStylePageSize;
                            }
                        }
                        break;
                    case 'child':
                        if ($scope.selectedPageId && $scope.activeMenu.type != 'list') {
                            parentId = $scope.selectedPageId;
                        }
                        break;
                    case 'below':
                        if ($scope.selectedPageId && $scope.activeMenu.type != 'list') {
                            var $selectedPage = $('#page_' + $scope.selectedPageId);
                            var $parent = $selectedPage.parent().closest('li');
                            if ($parent.length) {
                                parentId = $parent.attr('pageid');
                            }
                            position = $('#page_' + $scope.selectedPageId).index() + 1;
                        } else {
                            position = $('.ipsTreeDiv .active').index() + 1;
                            var curVariables = getHashParams();
                            if (curVariables.gpage) {
                                position = position + (curVariables.gpage - 1) * listStylePageSize;
                            }
                        }

                        break;
                    case 'bottom':
                        if ($scope.activeMenu.type != 'list') {
                            position = getTreeDiv().find('ul').first().children().length;
                        } else {
                            position = $('.ipsTreeDiv tr').length;
                            var curVariables = getHashParams();
                            if (curVariables.gpage) {
                                position = position + (curVariables.gpage - 1) * listStylePageSize;
                            }
                        }
                        break;
                }

                setDefaultPositionForNextTime($scope.activeMenu.alias, $modal.find('select[name=position]').val(), $scope.selectedPageId ? 1 : 0);
                addPage(title, isVisible, parentId, position);
                $modal.modal('hide');
            });
        };

        $scope.updateMenuModal = function (menu) {
            var $modal = $('.ipsUpdateMenuModal');
            $modal.modal();

            var data = {
                aa: 'Pages.updateMenuForm',
                id: menu.id
            };

            $.ajax({
                type: 'GET',
                url: ip.baseUrl,
                data: data,
                context: this,
                success: function (response) {
                    $modal.find('.ipsBody').html(response.html);

                    // initial state
                    $modal.find('.ipsDeleteConfirmation').addClass('hidden');
                    $modal.find('.ipsBody').removeClass('hidden');
                    $modal.find('.ipsModalActions').removeClass('hidden');

                    ipInitForms();

                    $modal.find('.ipsDelete').off('click').on('click', function () {
                        $modal.find('.ipsDeleteConfirmation').removeClass('hidden');
                        $modal.find('.ipsBody').addClass('hidden');
                        $modal.find('.ipsModalActions').addClass('hidden');
                        $modal.find('.ipsDeleteProceed').off('click').on('click', function () {
                            deletePage(menu.id, function () {
                                window.location = ip.baseUrl + '?aa=Pages.index#/hash=&language=' + $scope.activeLanguage.code + '&menu=' + $scope.activeMenu.alias;
                                location.reload();
                            });

                        });
                    });
                    $modal.find('.ipsDeleteCancel').off('click').on('click', function () {
                        $modal.find('.ipsDeleteConfirmation').addClass('hidden');
                        $modal.find('.ipsBody').removeClass('hidden');
                        $modal.find('.ipsModalActions').removeClass('hidden');
                        $modal.find('.ipsDeleteProceed').off('click');
                    });

                    $modal.find('.ipsSave').off('click').on('click', function () {
                        $modal.find('form').submit()
                    });
                    $modal.find('form').off('ipSubmitResponse').on('ipSubmitResponse', function (e, response) {
                        if (response.status == 'ok') {
                            window.location = ip.baseUrl + '?aa=Pages.index#/hash=&language=' + $scope.activeLanguage.code + '&menu=' + $scope.activeMenu.alias;
                            location.reload();
                            $modal.modal('hide');
                        }
                    });

                },
                error: function (response) {
                    if (ip.developmentEnvironment || ip.debugMode) {
                        alert('Server response: ' + response.responseText);
                    }
                },
                dataType: 'json'
            });
        };

        $scope.addMenuModal = function () {
            var $modal = $('.ipsAddMenuModal');
            $modal.find('input[name=title]').val('');
            $modal.modal();

            $modal.on('shown.bs.modal', function () {
                $modal.find('input[name=title]').focus();
            });

            $modal.find('.ipsAdd').off('click').on('click', function () {
                $modal.find('form').submit()
            });
            $modal.find('form').off('submit').on('submit', function (e) {
                e.preventDefault();
                var title = $modal.find('input[name=title]').val();
                var type = $modal.find('select[name=type]').val();
                addMenu(title, type);
                $modal.modal('hide');
            });
        };

        $scope.cutPage = function () {
            $scope.copyPageId = false;
            $scope.cutPageId = $scope.selectedPageId
        };

        $scope.copyPage = function () {
            $scope.cutPageId = false;
            $scope.copyPageId = $scope.selectedPageId;
        };

        $scope.menuTitle = function (menu) {
            if (menu.title) {
                return menu.title;
            }

            return 'Untitled';
        };

        $scope.pastePage = function () {
            var position = 0;
            var parentId = $scope.activeMenu.id;
            if ($scope.activeMenu.type != 'list') {
                if ($scope.selectedPageId) {
                    //add below selected
                    var $selectedPage = $('#page_' + $scope.selectedPageId);
                    position = $selectedPage.index() + 1;
                    var $parent = $selectedPage.parent().closest('li');
                    if ($parent.length) {
                        parentId = $parent.attr('pageid');
                    }

                } else {
                    //add to the bottom
                    position = getTreeDiv().find('ul').first().children().length;
                }
            } else {
                if ($scope.selectedPageId) {
                    //add below selected
                    position = $('.ipsTreeDiv .active').index() + 1;
                    var curVariables = getHashParams();
                    if (curVariables.gpage) {
                        position = position + (curVariables.gpage - 1) * listStylePageSize;
                    }
                } else {
                    //add to the bottom
                    position = $('.ipsTreeDiv tr').length;
                    var curVariables = getHashParams();
                    if (curVariables.gpage) {
                        position = position + (curVariables.gpage - 1) * listStylePageSize;
                    }
                }

            }


            if ($scope.cutPageId) {
                movePage($scope.cutPageId, parentId, position, true);
            } else {
                copyPage($scope.copyPageId, parentId, position, function () {
                    refresh();
                });
            }
        };


        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        var showPages = function () {
            $scope.selectedPageId = null;
            if (!$scope.activeMenu) {
                $('.ipsPages').addClass('hidden');
                return;
            }

            $('.ipsPages').removeClass('hidden');

            if ($scope.activeMenu.type == 'list') { // list view
                var gridContainer = getPagesContainer();
                if (!gridContainer.data('gateway')) {
                    gridContainer.data('gateway', ip.baseUrl + '?aa=Pages.pagesGridGateway&parentId=' + $scope.activeMenu.id);
                    gridContainer.ipGrid();
                    gridContainer.on('click', '.ipsRow', function (e) {
                        var $row = $(e.currentTarget);
                        updateHash(null, null, $row.data('id'));
                        $scope.$apply();
                    });

                    // setting active
                    gridContainer.on('htmlChanged.ipGrid', function (e) {
                        getTreeDiv().find('[data-id="' + $scope.selectedPageId + '"]').addClass('active');
                    });
                }
            } else {
                if (getPagesContainer().data('ipPageTree')) {
                    return; //already initialized
                }
                getTreeDiv().off('loaded.jstree').on('loaded.jstree', function (e) {
                    $('#page_' + $scope.selectedPageId + ' a').first().click();
                });
                getPagesContainer().ipPageTree({languageId: $scope.activeLanguage.id, menuName: $scope.activeMenu.alias});
                getTreeDiv().off('changed.jstree').on('changed.jstree', function (e, data) {

                    if (hashIsBeingApplied) {
                        return;
                    }
                    var id = data.selected;
                    var node = $('#' + id);
                    updateHash(null, null, node.attr('pageId'));
                    $scope.$apply();
                });
                $(document).off('dnd_start.vakata.impresspages').on('dnd_start.vakata.impresspages', function (e, data) {
                    var $draggedElement = $(data.element).closest('li');
                    ipPagesStartPagePosition = $draggedElement.index();
                    var $parentElement = $draggedElement.parent().closest('li.jstree-node');
                    ipPagesStartPageParentId = $parentElement.attr('pageid');
                    if (typeof(ipPagesStartPageParentId) == 'undefined') {
                        ipPagesStartPageParentId = $scope.activeMenu.id;
                    }

                });

                $(document).off('dnd_stop.vakata.impresspages').on('dnd_stop.vakata.impresspages', function (e, data) {
                    if (ipPageDragId == null) {
                        return;
                    }
                    //var $node = $('#' + data.data.nodes[0]);
                    var pageId = ipPageDragId;
                    var destinationParentId = $scope.activeMenu.id;
                    if (ipPagesDropPageId) {
                        destinationParentId = ipPagesDropPageId;
                    }


                    var destinationPosition = ipPagesDropPagePosition;
                    if (destinationPosition > ipPagesStartPagePosition && ipPagesStartPageParentId == destinationParentId) {
                        destinationPosition++;
                    }
                    movePage(pageId, destinationParentId, destinationPosition);
                });
                $(document).off('dnd_move.vakata.impresspages').on('dnd_move.vakata.impresspages', function (e, data) {
                    ipPageDragId = null;
                    ipPagesDropPageId = null;
                    ipPagesDropPagePosition = null;
                });

            }
        };

        var getPagesContainer = function () {
            return $('#pages_' + $scope.activeMenu.languageCode + '_' + $scope.activeMenu.alias).find('.ipsPages');
        };

        var getTreeDiv = function () {
            return getPagesContainer().find('.ipsTreeDiv');
        };

        var refresh = function () {
            if ($scope.activeMenu.type == 'list') { // list view
                getPagesContainer().ipGrid('refresh');
            } else {
                var selectedPageId = $scope.selectedPageId;
                getPagesContainer().ipPageTree('destroy');
                $scope.activateMenu($scope.activeMenu);
                if (selectedPageId) {
                    $scope.activatePage(selectedPageId, $scope.activeMenu.alias);
                }
                $scope.$apply();

            }
        };


        var setDefaultPositionForNextTime = function (alias, position, isPageSelected) {
            if (isPageSelected) {
                $scope.activeMenu.defaultPositionWhenSelected = position;
            } else {
                $scope.activeMenu.defaultPosition = position;
            }

            var data = {
                aa: 'Pages.setDefaultPagePosition',
                securityToken: ip.securityToken,
                alias: alias,
                isPageSelected: isPageSelected,
                position: position
            };

            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                dataType: 'json'
            });
        };

        var addPage = function (title, isvisible, parentId, position) {

            var data = {
                aa: 'Pages.addPage',
                securityToken: ip.securityToken,
                title: title,
                isVisible: isvisible,
                parentId: parentId,
                position: position
            };

            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: this,
                success: function (response) {
                    refresh();
                },
                error: function (response) {
                    if (ip.developmentEnvironment || ip.debugMode) {
                        alert('Server response: ' + response.responseText);
                    }
                },
                dataType: 'json'
            });
        };

        var addMenu = function (title, type) {
            var data = {
                aa: 'Pages.createMenu',
                securityToken: ip.securityToken,
                languageCode: $scope.activeLanguage.code,
                title: title,
                type: type
            };

            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: this,
                success: function (response) {
                    window.location = ip.baseUrl + '?aa=Pages.index#/hash=&language=' + $scope.activeLanguage.code;
                    location.reload();
                },
                error: function (response) {
                    if (ip.developmentEnvironment || ip.debugMode) {
                        alert('Server response: ' + response.responseText);
                    }
                },
                dataType: 'json'
            });
        };

        var editPage = function (pageId, successCallback) {
            var data = {
                aa: 'Pages.getPageUrl',
                pageId: pageId
            };

            $.ajax({
                type: 'GET',
                url: ip.baseUrl,
                data: data,
                context: this,
                success: function (response) {
                    window.location = response.pageUrl;
                },
                error: function (response) {
                    if (ip.developmentEnvironment || ip.debugMode) {
                        alert('Server response: ' + response.responseText);
                    }
                },
                dataType: 'json'
            });
        };

        var deletePage = function (pageId, successCallback) {
            var data = {
                aa: 'Pages.deletePage',
                pageId: pageId,
                securityToken: ip.securityToken
            };

            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: this,
                success: function () {
                    updateHash(null, null, 0);
                    $scope.$apply();

                    if (successCallback) {
                        successCallback();
                    }
                },
                error: function (response) {
                    if (ip.developmentEnvironment || ip.debugMode) {
                        alert('Server response: ' + response.responseText);
                    }
                },
                dataType: 'json'
            });
        };

        var copyPage = function (pageId, destinationParentId, destinationPosition, callback) {
            var data = {
                aa: 'Pages.copyPage',
                pageId: pageId,
                destinationParentId: destinationParentId,
                destinationPosition: destinationPosition,
                securityToken: ip.securityToken
            };

            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: this,
                success: callback,
                error: function (response) {
                    if (ip.developmentEnvironment || ip.debugMode) {
                        alert('Server response: ' + response.responseText);
                    }
                },
                dataType: 'json'
            });
        };

        var movePage = function (pageId, destinationParentId, destinationPosition, doRefresh) {
            var data = {
                aa: 'Pages.movePage',
                pageId: pageId,
                destinationPosition: destinationPosition,
                destinationParentId: destinationParentId,
                securityToken: ip.securityToken
            };

            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: this,
                success: function (response) {
                    if (doRefresh) {
                        window.location = ip.baseUrl + '?aa=Pages.index#/hash=&language=' + $scope.activeLanguage.code + '&menu=' + $scope.activeMenu.alias;
                        location.reload();
                    }
                },
                error: function (response) {
                    if (ip.developmentEnvironment || ip.debugMode) {
                        alert('Server response: ' + response.responseText);
                    }
                },
                dataType: 'json'
            });
        };

        /**
         * null values = current
         * false = unset
         * @param languageCode
         * @param menuName
         * @param pageId
         */
        var updateHash = function (languageCode, menuName, pageId) {
            var curVariables = getHashParams();
            curVariables['/hash'] = '';

            if (languageCode === null && $scope.activeLanguage) {
                languageCode = $scope.activeLanguage.code;
            }
            if (menuName === null && $scope.activeMenu) {
                menuName = $scope.activeMenu.alias;
            }
            if (pageId === null && $scope.selectedPageId) {
                pageId = $scope.selectedPageId;
            }

            curVariables.language = languageCode ? languageCode : null;
            curVariables.menu = menuName ? menuName : null;
            curVariables.page = pageId ? pageId : null;

            var path = '';
            $.each(curVariables, function (key, value) {
                if (value != null) {
                    if (path != '') {
                        path = path + '&';
                    }
                    path = path + key + '=' + value;
                }
            });
            $location.path(path);
        };


        var getHashParams = function () {

            var hashParams = {};
            var e,
                a = /\+/g,  // Regex for replacing addition symbol with a space
                r = /([^&;=]+)=?([^&;]*)/g,
                d = function (s) {
                    return decodeURIComponent(s.replace(a, " "));
                },
                q = window.location.hash.substring(1);

            while (e = r.exec(q))
                hashParams[d(e[1])] = d(e[2]);

            return hashParams;
        };

        function getQuery(name) {
            name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
            var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
                results = regex.exec(location.search);
            return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
        }

        var getFirstMenuOfLanguage = function (language) {
            var firstMenu = null;
            $.each(menuList, function (key, menu) {
                if (menu.languageCode == language.code) {
                    if (firstMenu == null) {
                        firstMenu = menu;
                    }
                }
            });
            return firstMenu;
        }
    }

})(jQuery);
