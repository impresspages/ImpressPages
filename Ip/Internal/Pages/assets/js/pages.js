var ipPages = null;

(function ($) {
    "use strict";


    var app = angular.module('Pages', []).directive('zonesPostRepeatDirective', function () {
        return function (scope, element, attrs) {
            if (scope.$last) {
                pagesZones.init();
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

        $scope.$on('PathChanged', function (event, path) {
            var menuName = getHashParams().menu;
            var languageCode = getHashParams().language;
            var pageId = getHashParams().page;

            if (!$scope.initialized) {
                if (languageCode == null) {
                    languageCode = languageList[0].code;
                }
                if (menuName == null) {
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


            if (menuName && menuName != $scope.activeMenu.alias) {
                $.each(menuList, function (key, value) {
                    if (value.alias == menuName) {
                        $scope.activateMenu(value);
                    }
                });
            }

            if (pageId && pageId != $scope.selectedPageId) {
                $scope.activatePage(pageId, $scope.activeMenu.alias);
            }

        });


        $scope.setMenuHash = function (menu) {
            updateHash(null, menu.alias, false);
        }

        $scope.setLanguageHash = function (language) {
            updateHash(language.code, null, false);
        }


        $scope.activateLanguage = function (language) {
            $scope.activeLanguage = language;
            initTree();
        }

        $scope.activateMenu = function (menu) {
            $scope.activeMenu = menu;
            $scope.selectedPageId = menu.id;
            initTree();
        }

        $scope.activatePage = function (pageId) {
            $scope.selectedPageId = pageId;
            var $properties = $('.ipsProperties');
            $properties.ipPageProperties({
                pageId: pageId
            });
            $properties.off('update.ipPages').on('update.ipPages', function () {
                getJsTree().set_text(getJsTree().get_selected(), $properties.find('input[name=navigationTitle]').val());
            });
            $properties.off('delete.ipPages').on('delete.ipPages', function () {
                deletePage($scope.selectedPageId, function () {
                    getJsTree().remove(getJsTree().get_selected());
                });
            });
            $properties.off('edit.ipPages').on('edit.ipPages', function () {
                editPage($scope.selectedPageId);
            });

        }

        $scope.addPageModal = function () {
            var $modal = $('.ipsAddModal');
            $modal.find('input[name=title]').val('');
            $modal.modal();


            $modal.find('.ipsAdd').off('click').on('click', function () {
                $modal.find('form').submit()
            });
            $modal.find('form').off('submit').on('submit', function (e) {
                e.preventDefault();
                var title = $modal.find('input[name=title]').val();
                var visible = $modal.find('input[name=visible]').is(':checked') ? 1 : 0;
                addPage(title, visible);
                $modal.modal('hide');
            });
        }


        $scope.updateZoneModal = function (zone) {
            var $modal = $('.ipsUpdateZoneModal');
            $modal.modal();

            var data = {
                aa: 'Pages.updateZoneForm',
                zoneName: zone.name
            }

            $.ajax({
                type: 'GET',
                url: ip.baseUrl,
                data: data,
                context: this,
                success: function (response) {
                    $modal.find('.ipsBody').html(response.html);
                    $modal.find('.ipsDelete').off('click').on('click', function () {
                        $modal.find('.ipsDeleteConfirmation').removeClass('ipgHide');
                        $modal.find('.ipsBody').addClass('ipgHide');
                        $modal.find('.ipsDelete').addClass('ipgHide');
                        $modal.find('.ipsModalActions').addClass('ipgHide');
                        $modal.find('.ipsDeleteProceed').off('click').on('click', function () {
                            deleteZone(zone.name);
                        });
                    });
                    $modal.find('.ipsDeleteCancel').off('click').on('click', function () {
                        $modal.find('.ipsDeleteConfirmation').addClass('ipgHide');
                        $modal.find('.ipsBody').removeClass('ipgHide');
                        $modal.find('.ipsDelete').removeClass('ipgHide');
                        $modal.find('.ipsModalActions').removeClass('ipgHide');
                        $modal.find('.ipsDeleteProceed').off('click');
                    });

                    $modal.find('.ipsSave').off('click').on('click', function () {
                        $modal.find('form').submit()
                    });
                    $modal.find('form').off('submit').on('submit', function (e) {
                        e.preventDefault();
                        var title = $modal.find('input[name=title]').val();
                        var url = $modal.find('input[name=url]').val();
                        var name = $modal.find('input[name=name]').val();
                        var layout = $modal.find('select[name=layout]').val();
                        var metaTitle = $modal.find('input[name=metaTitle]').val();
                        var metaKeywords = $modal.find('input[name=metaKeywords]').val();
                        var metaDescription = $modal.find('textarea[name=metaDescription]').val();
                        var languageId = $scope.activeLanguage.id;
                        updateZone(zone.name, languageId, title, url, name, layout, metaTitle, metaKeywords, metaDescription);
                        $modal.modal('hide');
                    });

                },
                error: function (response) {
                    if (ip.developmentEnvironment || ip.debugMode) {
                        alert('Server response: ' + response.responseText);
                    }
                },
                dataType: 'json'
            });


        }

        $scope.addMenuModal = function () {
            var $modal = $('.ipsAddMenuModal');
            $modal.find('input[name=title]').val('');
            $modal.modal();


            $modal.find('.ipsAdd').off('click').on('click', function () {
                $modal.find('form').submit()
            });
            $modal.find('form').off('submit').on('submit', function (e) {
                e.preventDefault();
                var title = $modal.find('input[name=title]').val();
                addMenu(title);
                $modal.modal('hide');
            });
        }

        $scope.cutPage = function () {
            $scope.copyPageId = false;
            $scope.cutPageId = $scope.selectedPageId
        }

        $scope.copyPage = function () {
            $scope.cutPageId = false;
            $scope.copyPageId = $scope.selectedPageId;
        }


        $scope.menuTitle = function (menu) {
            if (menu.navigationTitle) {
                return menu.navigationTitle;
            }
            if (menu.pageTitle) {
                return menu.pageTitle;
            }

            return 'Untitled';
        }


        $scope.pastePage = function () {
            var tree = getJsTree();
            var position = tree._get_children(-1).length; //last position
            var node = tree.get_selected();
            if (node.length) {
                var position = node.index() + 1;
            }
            if ($scope.cutPageId) {
                movePage($scope.cutPageId, $scope.selectedPageId, position, true);
            } else {
                copyPage($scope.copyPageId, $scope.selectedPageId, position, function () {
                    refresh();
                });
            }

        }

        var initTree = function () {
            $scope.selectedPageId = null;
            getTreeDiv().ipPageTree({languageId: $scope.activeLanguage.id, menuName: $scope.activeMenu.alias});
            getTreeDiv().off('select_node.jstree').on('select_node.jstree', function (e) {
                var node = getJsTree().get_selected();
                updateHash(null, null, node.attr('pageId'));
                $scope.$apply();
            });

            getTreeDiv().off('move_node.jstree').on('move_node.jstree', function (e, moveData) {
                moveData.rslt.o.each(function (i) {
                    var pageId = $(this).attr("pageId");
                    var destinationParentId = moveData.rslt.np.attr("pageId");
                    if (!destinationParentId) { //replace undefined with null;
                        destinationParentId = $scope.activeMenu.id;
                    }
                    var destinationPosition = moveData.rslt.cp + i;
                    movePage(pageId, destinationParentId, destinationPosition);
                });
            });


        }


        var getTreeDiv = function () {
            return $('#pages_' + $scope.activeLanguage.id + '_' + $scope.activeMenu.alias).find('.ipsTree');
        }

        var getJsTree = function () {
            return $.jstree._reference('#pages_' + $scope.activeLanguage.id + '_' + $scope.activeMenu.alias + ' .ipsTree');
        }

        var refresh = function () {
            $('.ipsTree').ipPageTree('destroy');
            $scope.activateMenu($scope.activeMenu);
            $scope.$apply();
        }


        var addPage = function (title, visible) {
            var data = {
                aa: 'Pages.addPage',
                securityToken: ip.securityToken,
                title: title,
                visible: visible,
                parentId: $scope.activeMenu.id
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

        }

        var addMenu = function (title) {
            var data = {
                aa: 'Pages.createMenu',
                securityToken: ip.securityToken,
                languageCode: $scope.activeLanguage.code,
                title: title
            };

            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: this,
                success: function (response) {
                    window.location = ip.baseUrl + '?aa=Pages.index';
                },
                error: function (response) {
                    if (ip.developmentEnvironment || ip.debugMode) {
                        alert('Server response: ' + response.responseText);
                    }
                },
                dataType: 'json'
            });

        }


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
        }

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
                success: successCallback,
                error: function (response) {
                    if (ip.developmentEnvironment || ip.debugMode) {
                        alert('Server response: ' + response.responseText);
                    }
                },
                dataType: 'json'
            });
        }


        var copyPage = function (pageId, destinationLanguageId, destinationZoneName, destinationParentId, destinationPosition, callback) {
            var data = {
                aa: 'Pages.copyPage',
                pageId: pageId,
                destinationParentId: destinationParentId,
                destinationPosition: destinationPosition,
                languageId: destinationLanguageId,
                zoneName: destinationZoneName,
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
        }

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
                        refresh();
                    }
                },
                error: function (response) {
                    if (ip.developmentEnvironment || ip.debugMode) {
                        alert('Server response: ' + response.responseText);
                    }
                },
                dataType: 'json'
            });
        }

        var updateHash = function (languageCode, menuName, pageId) {
            if (languageCode === null) {
                languageCode = $scope.activeLanguage.code;
            }
            if (menuName === null) {
                menuName = $scope.activeMenu.alias;
            }
            if (pageId === null) {
                pageId = $scope.selectedPageId;
            }
            var path = 'hash&language=' + languageCode + '&menu=' + menuName;
            if (pageId) {
                path = path + '&page=' + pageId;
            }
            $location.path(path);
        }

        var updateZone = function (zoneName, languageId, title, url, name, layout, metaTitle, metaKeywords, metaDescription) {
            var data = {
                aa: 'Pages.updateZone',
                zoneName: zoneName,
                languageId: languageId,
                title: title,
                url: url,
                name: name,
                layout: layout,
                metaTitle: metaTitle,
                metaKeywords: metaKeywords,
                metaDescription: metaDescription,
                securityToken: ip.securityToken
            };

            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: this,
                success: function (response) {
                    window.location = ip.baseUrl + '?aa=Pages.index';
                },
                error: function (response) {
                    if (ip.developmentEnvironment || ip.debugMode) {
                        alert('Server response: ' + response.responseText);
                    }
                },
                dataType: 'json'
            });
        }

        var deleteZone = function (zoneName) {
            var data = {
                aa: 'Pages.deleteZone',
                zoneName: zoneName,
                securityToken: ip.securityToken
            };

            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: this,
                success: function (response) {
                    window.location = ip.baseUrl + '?aa=Pages.index';
                },
                error: function (response) {
                    if (ip.developmentEnvironment || ip.debugMode) {
                        alert('Server response: ' + response.responseText);
                    }
                },
                dataType: 'json'
            });
        }


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
        }


    }


})(ip.jQuery);


