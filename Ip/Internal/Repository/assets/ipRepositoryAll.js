(function ($) {
    "use strict";

    var settings = {},
        dynamicThumbnailClass = 'js-dynamic-preview',
        selectedItemClass = 'ui-selected',
        $lastSelectedItem = null;

    var methods = {

        init: function (options) {

            // defaults are set in ipRepository.js
            // otherwise here we should extend defaults with custom options
            settings = options;

            return this.each(function () {

                var $this = $(this);

                var data = $this.data('ipRepositoryAll');
                if (!data) {
                    var $popup = $('.ipsModuleRepositoryPopup');

                    $this.data('ipRepositoryAll', options);

                    var data = {
                        aa: 'Repository.getAll',
                        securityToken: ip.securityToken,
                        filter: settings.filter,
                        filterExtensions: settings.filterExtensions,
                        secure: settings.secure,
                        path: settings.path
                    };

                    if ($popup.find('.ipsPermissionError').length === 0) {
                        $.ajax({
                            type: 'GET',
                            url: ip.baseUrl,
                            data: data,
                            context: this,
                            //success : $.proxy(methods._storeFilesResponse, this),
                            success: methods._getAllFilesResponse,
                            error: function () {
                            }, //TODO report error
                            dataType: 'json'
                        });
                    }

                    $('#ipsModuleRepositoryBuyButton').on('click', function (e) {
                        e.preventDefault();
                        $popup.find('a[href*=ipsModuleRepositoryTabBuy]').click();
                    });
                    $popup.find('.ipsBrowserSearch').on('submit', function (e) {
                        e.preventDefault();
                        $popup.trigger('ipModuleRepository.search');
                    });
                    $popup.find('.ipsBrowserSearch .ipsTerm').on('keyup change', function (e) {
                        $popup.trigger('ipModuleRepository.search');
                    });
                    $popup.find('.ipsBrowserSearch .ipsSubmit').on('click', function (e) {
                        var $this = $(this);
                        var $searchField = $this.closest('.ipsBrowserSearch').find('.ipsTerm');
                        if ($searchField.val() != '') {
                            $searchField.val('');
                        }
                    });

                    $(window).bind("resize.ipRepositoryAll", $.proxy(methods._resize, this));
                    $('.ipsBrowser').bind("scroll", $.proxy(methods._scroll, this));
                    $popup.bind('ipModuleRepository.close', $.proxy(methods._teardown, this));
                    $popup.bind('ipModuleRepository.search', $.proxy(methods._filterFilesByTerm, this));
                    $.proxy(methods._resize, this)();



                }
            });
        },


        _isVisible: function (element) {
            var $browser = $('.ipsBrowser'),
                $element = $(element),
                scrollTop = $browser.scrollTop(),
                elementY = $element.offset().top;
            return ((elementY < ($browser.height() + scrollTop)) && (elementY > (scrollTop - $element.height())));
        },

        _loadVisibleThumbnails: function() {
            var $browserContainer = $(this).find('.ipsBrowserContainer'),
                $items = $browserContainer.find('.' + dynamicThumbnailClass + ':visible');

            $items.each(function () {
                var $item = $(this);
                if (methods._isVisible(this)) {
                    $item
                        .removeClass(dynamicThumbnailClass)
                        .attr('src', $item.attr('data-preview'));
                }
            });
        },

        _filterFilesByTerm: function (e) {
            var $this = $(this);
            var $lists = $this.find('.ipsBrowser .ipsList');
            var $files = $lists.find('li');
            var term = $this.find('.ipsBrowserSearch .ipsTerm').val().toLowerCase();

            if (term.length > 0) {
                // if term exists - loop all files
                $files.each(function () {
                    var $file = $(this);
                    var fileName = $file.data('fileData').fileName.toLowerCase();
                    // check in files' data whether filename include term
                    if (fileName.search(term) != -1) {
                        // show file if term match (in case it was hidden earlier)
                        $file.removeClass('hidden');
                    } else {
                        // hide file is term doesn't match
                        $file.addClass('hidden');
                    }
                });
                $this.find('.ipsBrowserSearch .fa-search').removeClass('fa-search').addClass('fa-times');
            } else {
                // show all files if term doesn't exist
                $files.removeClass('hidden');
                $this.find('.ipsBrowserSearch .fa-times').removeClass('fa-times').addClass('fa-search');
            }

            // loop all lists
            $lists.each(function () {
                var $list = $(this);
                var totalFiles = $list.find('li').length;
                var hiddenFiles = $list.find('li.hidden').length;
                var numberOfVisibleChildren = totalFiles - hiddenFiles;

                if (numberOfVisibleChildren > 0) {
                    // if list has at least one visible child display list and title
                    $list.removeClass('hidden');
                    $list.prev('.ipsListTitle').removeClass('hidden');
                } else {
                    // if all children in the list is hidden, hide it and its title
                    $list.addClass('hidden');
                    $list.prev('.ipsListTitle').addClass('hidden');
                }
            });

            $.proxy(methods._loadVisibleThumbnails, this)();
        },

        addRecentFiles: function (files) {
            var $this = $(this);
            $this.find('.ipsRecentTitle').removeClass('hidden');
            $this.find('.ipsRecentList').removeClass('hidden');

            var $template = $this.find('.ipsFileTemplate');
            var $newList = $this.find('.ipsRecentList');
            $newList.addClass('_previewType-' + settings.preview);

            for (var i in files) {
                var $newItem = $template.clone().removeClass('ipsFileTemplate');
                methods._addFileData($newItem, files[i], true);

                $newItem.toggleClass(selectedItemClass);
                $newList.append($newItem);
                $lastSelectedItem = $newItem;
            }

            $.proxy(methods._countSelected, this)();
            $.proxy(methods._loadVisibleThumbnails, this)();

        },

        _addFileData: function ($file, data, instantPreview) {
            // icon
            var iconClass = 'fa fa-file-o';
            switch (data.ext) {
                case 'gif':
                case 'jpeg':
                case 'jpg':
                case 'png':
                case 'svg':
                    iconClass = 'fa fa-file-image-o';
                    break;
                case 'pdf':
                    iconClass = 'fa fa-file-pdf-o';
                    break;
                case 'txt':
                    iconClass = 'fa fa-file-text-o';
                    break;
                case 'doc':
                case 'docb':
                case 'docm':
                case 'docx':
                case 'dot':
                case 'dotm':
                case 'dotx':
                case 'odt':
                case 'rtf':
                    iconClass = 'fa fa-file-word-o';
                    break;
                case 'csv':
                case 'ods':
                case 'xla':
                case 'xlam':
                case 'xll':
                case 'xlm':
                case 'xls':
                case 'xlsb':
                case 'xlsm':
                case 'xlsx':
                case 'xlt':
                case 'xltm':
                case 'xltx':
                case 'xlw':
                    iconClass = 'fa fa-file-excel-o';
                    break;
                case 'odp':
                case 'pot':
                case 'potm':
                case 'ppam':
                case 'pps':
                case 'ppsm':
                case 'ppsx':
                case 'ppt':
                case 'pptm':
                case 'pptx':
                case 'sldm':
                case 'sldx':
                    iconClass = 'fa fa-file-powerpoint-o';
                    break;
                case 'asp':
                case 'aspx':
                case 'cgi':
                case 'css':
                case 'dll':
                case 'exe':
                case 'htm':
                case 'html':
                case 'jsp':
                case 'js':
                case 'less':
                case 'php':
                case 'pl':
                case 'py':
                case 'rb':
                case 'rss':
                case 'sass':
                case 'scss':
                case 'xml':
                    iconClass = 'fa fa-file-code-o';
                    break;
                case '7z':
                case 'apk':
                case 'arc':
                case 'arj':
                case 'cab':
                case 'gz':
                case 'iso':
                case 'rar':
                case 'tar':
                case 'tar.gz':
                case 'tgz':
                case 'zip':
                    iconClass = 'fa fa-file-archive-o';
                    break;
                case 'aac':
                case 'cda':
                case 'm4a':
                case 'mp3':
                case 'mp4':
                case 'ogg':
                case 'wav':
                case 'wma':
                    iconClass = 'fa fa-file-audio-o';
                    break;
                case 'aaf':
                case 'avi':
                case 'flv':
                case 'm4v':
                case 'mkv':
                case 'mpeg':
                case 'mpg':
                case 'mov':
                case 'wmv':
                    iconClass = 'fa fa-file-video-o';
                    break;
            }
            $file.find('i').addClass(iconClass);
            // thumbnail

            var $img = $file.find('img');

            $img
                .attr('alt', data.fileName)
                .attr('title', data.fileName);

            if (instantPreview) {
                $img.attr('src', data.previewUrl);
            } else {
                $img.addClass(dynamicThumbnailClass)
                    .attr('data-preview', data.previewUrl);
            }

            // filename
            $file.find('span').text(data.fileName);
            // file data
            $file.attr('data-file', data.fileName); // unique attribute to recognize required element
            $file.data('fileData', data);
        },

        _getAllFilesResponse: function (response) {
            var $this = $(this);
            var repositoryContainer = this;

            if (!response || !response.fileGroups) {
                return; //TODO report error
            }

            var fileGroups = response.fileGroups;
            var $browserContainer = $this.find('.ipsBrowserContainer');
            var $template = $this.find('.ipsFileTemplate');
            var $listTemplate = $this.find('.ipsListTemplate');
            var $titleTemplate = $this.find('.ipsListTitleTemplate');

            for (var gi in fileGroups) {
                var $newList = $listTemplate.clone().detach().removeClass('ipsListTemplate');
                $newList.addClass('_previewType-' + settings.preview);
                var $newTitle = $titleTemplate.clone().detach().removeClass('ipsListTitleTemplate');
                $newTitle.text(gi);
                for (var i in fileGroups[gi]) {
                    var files = fileGroups[gi];
                    var $newItem = $template.clone().removeClass('ipsFileTemplate');
                    methods._addFileData($newItem, files[i]);
                    $newList.append($newItem);
                }
                $browserContainer.append($newTitle);
                $browserContainer.append($newList);

            }

            $.proxy(methods._loadVisibleThumbnails, this)();

            $this.find('.ipsRepositoryActions .ipsSelectionConfirm').click($.proxy(methods._confirm, this));
            $this.find('.ipsRepositoryActions .ipsSelectionCancel').click($.proxy(methods._stopSelect, this));
            $this.find('.ipsRepositoryActions .ipsSelectionDelete').click($.proxy(methods._delete, this));

            $browserContainer.delegate('li', 'click', function (evt) {
                var $self = $(this);

                if (evt.metaKey || evt.ctrlKey) {
                    $self.toggleClass(selectedItemClass);
                } else if (evt.shiftKey) {

                    var $startElem = $lastSelectedItem || $self.siblings('li').eq(0);

                    $self.addClass(selectedItemClass);

                    if ($startElem.is($self))
                        return;

                    var isLastClickedBefore = $startElem[0].compareDocumentPosition($self[0]) & 4,
                        crossGroup = !$startElem.parent().is($self.parent());

                    if (isLastClickedBefore) {

                        $startElem.nextUntil($self).addClass(selectedItemClass);

                        // selecting elements across different groups
                        if (crossGroup) {

                            // go through all groups between the previously selected and target group
                            $startElem.parent().nextUntil($self.parent(), 'ul').each(function() {
                                $(this)
                                    .find('li:first')
                                    .addClass(selectedItemClass)
                                    .nextUntil($self)
                                    .addClass(selectedItemClass);
                            });

                            // finally elements in the current target group
                            $self.prevUntil('ul').addClass(selectedItemClass);
                        }

                    }
                    else {
                        $startElem.prevUntil($self).addClass(selectedItemClass);

                        // selecting elements across different groups
                        if (crossGroup) {

                            // go through all groups between the previously selected and target group
                            $startElem.parent().prevUntil($self.parent(), 'ul').each(function() {
                                $(this)
                                    .find('li:last')
                                    .addClass(selectedItemClass)
                                    .prevUntil($self)
                                    .addClass(selectedItemClass);
                            });

                            // finally elements in the current target group
                            $self.nextUntil('ul').addClass(selectedItemClass);
                        }
                    }

                } else {
                    $lastSelectedItem = $self;
                    $self.toggleClass(selectedItemClass);
                }

                $.proxy(methods._countSelected, repositoryContainer)();
            });

        },

        _countSelected: function (e) {
            var $this = $(this);
            var count = $this.find('li.ui-selected').length;
            if (count) {
                $.proxy(methods._startSelect, this)();
            } else {
                $.proxy(methods._stopSelect, this)();
            }
            $this.find('.ipsRepositoryActions .ipsSelectionCount').text(count);
        },

        _startSelect: function (e) {
            var $this = $(this);
            $this.find('.ipsRepositoryActions').removeClass('hidden');
            $this.find('.ipsBrowserContainer').addClass('ui-selecting');
        },

        _stopSelect: function (e) {
            if (e) {
                e.preventDefault();
            }
            var $this = $(this);
            $this.find('.ipsRepositoryActions').addClass('hidden');
            $this.find('.ipsBrowserContainer li').removeClass(selectedItemClass);
            $this.find('.ipsBrowserContainer').removeClass('ui-selecting');
        },

        _confirm: function (e) {
            e.preventDefault();
            var $this = $(this);

            var files = new Array();
            $this.find('li.ui-selected').each(function () {
                var $this = $(this);
                files.push($this.data('fileData'));
            });

            $this.trigger('ipModuleRepository.confirm', [files]);
        },

        _cancel: function (e) {
            e.preventDefault();
            $(this).trigger('ipModuleRepository.cancel');
        },

        _delete: function (e) {
            e.preventDefault();
            var context = this;

            if (confirm(ipRepositoryTranslate_confirm_delete)) {
                var $this = $(this);

                var files = new Array();
                $this.find('li.ui-selected').each(function () {
                    var $this = $(this);
                    files.push($this.data('fileData'));
                });

                $.proxy(methods._executeDelete, context)(files);

            }
        },

        _executeDelete: function(files, forced) {
            var context = this;
            var $this = $(this);
            var data = Object();
            data.aa = 'Repository.deleteFiles';
            data.files = files;
            data.securityToken = ip.securityToken;
            data.secure = $this.data('ipRepositoryAll').secure;
            data.forced = forced;

            $.ajax({
                type: 'POST',
                url: ip.baseUrl,
                data: data,
                context: this,
                //success : $.proxy(methods._storeFilesResponse, this),
                success: function (response) {
                    var $this = $(this);
                    var repositoryContainer = this;

                    if (!response || !response.success) {
                        return; //TODO report error
                    }


                    // remove deleted files
                    var deletedFiles = response.deletedFiles;
                    var $browser = $this.find('.ipsBrowser');
                    for (var i in deletedFiles) {

                        var animateOptions = {};

                        switch (settings.preview) {
                            case 'thumbnails':
                                animateOptions = {width: 0, paddingLeft: 0, paddingRight: 0, marginLeft: 0, marginRight: 0};
                                break;
                            default:
                                animateOptions = {height: 0, paddingTop: 0, paddingBottom: 0, marginTop: 0, marginBottom: 0};
                                break;
                        }


                        $browser.find("li[data-file='" + deletedFiles[i] + "']")
                            .css('overflow', 'hidden')
                            .css('border-bottom', 'none')
                            .animate(animateOptions, 'slow')
                            .hide(0, function () {
                                $(this).remove();
                                // recalculating selected files
                                $.proxy(methods._countSelected, repositoryContainer)();
                            });
                    }


                    // notify that not all files were deleted
                    if (parseInt(response.notRemovedCount) > 0) {
                        if (confirm(ipRepositoryTranslate_delete_warning)) {

                            // do not include already deleted files in the request, otherwise
                            // we'll end up in an endless loop, telling the user that some files
                            // could not be deleted (because they have been deleted already)
                            $.proxy(methods._executeDelete, context)(files.filter(function(x) {
                                return !~response.deletedFiles.indexOf(x.fileName);
                            }), true);
                        }
                    }
                },
                error: function () {
                }, //TODO report error
                dataType: 'json'
            });

        },


        // set back our element
        _teardown: function () {
            $(window).unbind('resize.ipRepositoryAll');
        },

        _resize: function (e) {
            var $popup = $('.ipsModuleRepositoryPopup');
            var $block = $popup.find('.ipsBrowser');
            var tabsHeight = parseInt($popup.find('.ipsTabs').outerHeight());
            $block.outerHeight((parseInt($(window).height()) - tabsHeight));
            $.proxy(methods._loadVisibleThumbnails, this)();
        },

        _scroll: function(e) {
            $.proxy(methods._loadVisibleThumbnails, this)();
        }

    };

    $.fn.ipRepositoryAll = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipRepositoryAll');
        }

    };

})(jQuery);
