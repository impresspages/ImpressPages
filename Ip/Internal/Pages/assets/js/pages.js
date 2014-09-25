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
                    getTreeDiv().find('.ipsRow.active .ipsDrag').text(escapeHtml(title));
                } else { // tree view
                    getTreeDiv().jstree('rename_node', getTreeDiv().jstree('get_selected'), escapeHtml(title));
                }
            });

            // removing element from list/tree
            $properties.off('delete.ipPages').on('delete.ipPages', function () {
                if (confirm(ipTranslationAreYouSure)) {
                    deletePage($scope.selectedPageId, function () {
                        $scope.selectedPageId = null;
                        if ($scope.activeMenu.type == 'list') { // list view
                            getPagesContainer().ipGrid('refresh');
                        } else {
                            getTreeDiv().jstree('delete_node', getTreeDiv().jstree('get_selected'));
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
            $modal.find('form').off('submit').on('submit', function (e) {
                e.preventDefault();
                var title = $modal.find('input[name=title]').val();
                var isVisible = $modal.find('input[name=isVisible]').is(':checked') ? 1 : 0;
                addPage(title, isVisible);
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
            var position = getTreeDiv().find('ul').first().children.length;
            if ($scope.cutPageId) {
                movePage($scope.cutPageId, $scope.activeMenu.id, position, true);
            } else {
                copyPage($scope.copyPageId, $scope.activeMenu.id, position, function () {
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
                    return; //alrady initialized
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
                getPagesContainer().ipPageTree('destroy');
                $scope.activateMenu($scope.activeMenu);
                $scope.$apply();
            }
        };


        var addPage = function (title, isvisible) {
            var parentId = $scope.activeMenu.id;

            if ($scope.selectedPageId && $scope.activeMenu.type != 'list') {
                parentId = $scope.selectedPageId;
            }

            var data = {
                aa: 'Pages.addPage',
                securityToken: ip.securityToken,
                title: title,
                isVisible: isvisible,
                parentId: parentId
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
