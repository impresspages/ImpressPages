(function($) {
    "use strict";

    var settings = {};

    var methods = {

        init : function(options) {

            // defaults are set in ipRepository.js
            // otherwise here we should extend defaults with custom options
            settings = options;

            return this.each(function() {

                var $this = $(this);

                var data = $this.data('ipRepositoryAll');
                if (!data) {
                    var $popup = $('.ipModuleRepositoryPopup');

                    $this.data('ipRepositoryAll', {});

                    var data = Object();
                    data.aa = 'Repository.getAll';
                    data.securityToken = ip.securityToken;
                    data.filter = settings.filter;

                    $.ajax ({
                        type : 'POST',
                        url : ip.baseUrl,
                        data : data,
                        context : this,
                        //success : $.proxy(methods._storeFilesResponse, this),
                        success : methods._getAllFilesResponse,
                        error : function(){}, //TODO report error
                        dataType : 'json'
                    });

                    $('#ipModuleRepositoryBuyButton').on('click', function(e){
                        e.preventDefault();
                        $popup.find('a[href*=ipModuleRepositoryTabBuy]').click();
                    });
                    $popup.find('.ipmBrowserSearch .ipmTerm').on('keyup', function(e){
                        $popup.trigger('ipModuleRepository.search');
                    });
                    $popup.find('.ipmBrowserSearch .ipmForm').on('submit', function(e){
                        e.preventDefault();
                        $popup.trigger('ipModuleRepository.search');
                    });
                    $popup.find('.ipmBrowserSearch .ipmForm button').on('click', function(e){
                        var $this = $(this);
                        var $searchField = $this.closest('.ipmBrowserSearch').find('.ipmTerm');
                        if ($searchField.val() != '') {
                            $searchField.val('');
                        }
                    });

                    $(window).bind("resize.ipRepositoryAll", $.proxy(methods._resize, this));
                    $popup.bind('ipModuleRepository.close', $.proxy(methods._teardown, this));
                    $popup.bind('ipModuleRepository.search', $.proxy(methods._filterFilesByTerm, this));
                    $.proxy(methods._resize, this)();
                }
            });
        },

        _filterFilesByTerm : function(e) {
            var $this = $(this);
            var $lists = $this.find('.ipmBrowser .ipmList');
            var $files = $lists.find('li');
            var term = $this.find('.ipmBrowserSearch .ipmTerm').val().toLowerCase();

            if (term.length > 0) {
                // if term exists - loop all files
                $files.each(function(){
                    var $file = $(this);
                    var fileName = $file.data('fileData').fileName.toLowerCase();
                    // check in files' data whether filename include term
                    if (fileName.search(term) != -1) {
                        // show file if term match (in case it was hidden earlier)
                        $file.removeClass('ipgHide');
                    } else {
                        // hide file is term doesn't match
                        $file.addClass('ipgHide');
                    }
                });
                $this.find('.ipmBrowserSearch .fa-search').removeClass('fa-search').addClass('fa-times');
            } else {
                // show all files if term doesn't exist
                $files.removeClass('ipgHide');
                $this.find('.ipmBrowserSearch .fa-times').removeClass('fa-times').addClass('fa-search');
            }

            // loop all lists
            $lists.each(function(){
                var $list = $(this);
                var totalFiles = $list.find('li').length;
                var hiddenFiles = $list.find('li.ipgHide').length;
                var numberOfVisibleChildren = totalFiles - hiddenFiles;

                if (numberOfVisibleChildren > 0) {
                    // if list has at least one visible child display list and title
                    $list.removeClass('ipgHide');
                    $list.prev('.ipmListTitle').removeClass('ipgHide');
                } else {
                    // if all children in the list is hidden, hide it and its title
                    $list.addClass('ipgHide');
                    $list.prev('.ipmListTitle').addClass('ipgHide');
                }
            });
        },

        addRecentFiles : function (files) {
            var $this = $(this);
            $this.find('.ipmRecentTitle').removeClass('ipgHide');
            $this.find('.ipmRecentList').removeClass('ipgHide');

            var $template = $this.find('.ipmFileTemplate');
            var $newList = $this.find('.ipmRecentList');
            $newList.addClass('ipmPreview-'+settings.preview);

            for(var i in files) {
                var $newItem = $template.clone().removeClass('ipmFileTemplate');
                methods._addFileData($newItem,files[i]);

                $newItem.toggleClass('ui-selected');
                $newList.append($newItem);
            }
            $.proxy(methods._countSelected, this)();


        },

        _addFileData : function($file, data) {
            // icon
            var iconClass = 'fa fa-file-o';
            switch (data.ext) {
                case 'gif':
                case 'jpeg':
                case 'jpg':
                case 'png':
                    iconClass = 'fa fa-picture-o';
                    break;
                case 'pdf':
                    iconClass = 'fa fa-print';
                    break;
                case 'txt':
                    iconClass = 'fa fa-file-text-o';
                    break;
                case 'exe':
                    iconClass = 'fa fa-windows';
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
                    iconClass = 'fa fa-archive';
                    break;
                case 'aac':
                case 'cda':
                case 'm4a':
                case 'mp3':
                case 'mp4':
                case 'ogg':
                case 'wav':
                case 'wma':
                    iconClass = 'fa fa-music';
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
                    iconClass = 'fa fa-film';
                    break;
            }
            $file.find('i').addClass(iconClass);
            // thumbnail
            $file.find('img')
                .attr('src', data.previewUrl)
                .attr('alt', data.fileName)
                .attr('title', data.fileName);
            // filename
            $file.find('span').text(data.fileName);
            // file data
            $file.attr('data-file', data.file); // unique attribute to recognize required element
            $file.data('fileData', data);
        },

        _getAllFilesResponse : function(response) {
            var $this = $(this);
            var repositoryContainer = this;

            if (!response || !response.fileGroups) {
                return; //TODO report error
            }

            var fileGroups = response.fileGroups;
            var $browserContainer = $this.find('.ipmBrowserContainer');
            var $template = $this.find('.ipmFileTemplate');
            var $listTemplate = $this.find('.ipmListTemplate');
            var $titleTemplate = $this.find('.ipmListTitleTemplate');

            for(var gi in fileGroups) {
                var $newList = $listTemplate.clone().detach().removeClass('ipmListTemplate');
                $newList.addClass('ipmPreview-'+settings.preview);
                var $newTitle = $titleTemplate.clone().detach().removeClass('ipmListTitleTemplate');
                $newTitle.text(gi);
                for(var i in fileGroups[gi]) {
                    var files = fileGroups[gi];
                    var $newItem = $template.clone().removeClass('ipmFileTemplate');
                    methods._addFileData($newItem,files[i]);
                    $newList.append($newItem);
                }
                $browserContainer.append($newTitle);
                $browserContainer.append($newList);

            }

            $this.find('.ipmRepositoryActions .ipaSelectionConfirm').click($.proxy(methods._confirm, this));
            $this.find('.ipmRepositoryActions .ipaSelectionCancel').click($.proxy(methods._stopSelect, this));
            $this.find('.ipmRepositoryActions .ipaSelectionDelete').click($.proxy(methods._delete, this));

            $browserContainer.delegate('li', 'click', function(e){
                $(this).toggleClass('ui-selected');
                $.proxy(methods._countSelected, repositoryContainer)();
            });

        },

        _countSelected : function(e) {
            var $this = $(this);
            var count = $this.find('li.ui-selected').length;
            if (count) {
                $.proxy(methods._startSelect, this)();
            } else {
                $.proxy(methods._stopSelect, this)();
            }
            $this.find('.ipmRepositoryActions .ipmSelectionCount').text(count);
        },

        _startSelect : function(e) {
            var $this = $(this);
            $this.find('.ipmRepositoryActions').removeClass('ipgHide');
            $this.find('.ipmBrowserContainer').addClass('ui-selecting');
        },

        _stopSelect : function(e) {
            if (e) { e.preventDefault(); }
            var $this = $(this);
            $this.find('.ipmRepositoryActions').addClass('ipgHide');
            $this.find('.ipmBrowserContainer li').removeClass('ui-selected');
            $this.find('.ipmBrowserContainer').removeClass('ui-selecting');
        },

        _confirm : function (e) {
            e.preventDefault();
            var $this = $(this);

            var files = new Array();
            $this.find('li.ui-selected').each(function(){
                var $this = $(this);
                files.push($this.data('fileData'));
            });

            $this.trigger('ipModuleRepository.confirm', [files]);
        },

        _cancel : function(e) {
            e.preventDefault();
            $(this).trigger('ipModuleRepository.cancel');
        },

        _delete : function(e) {
            e.preventDefault();

            if (confirm(ipRepositoryTranslate_confirm_delete)) {
                var $this = $(this);

                var files = new Array();
                $this.find('li.ui-selected').each(function(){
                    var $this = $(this);
                    files.push($this.data('fileData'));
                });

                var data = Object();
                data.aa = 'Repository.deleteFiles';
                data.files = files;
                data.securityToken = ip.securityToken;

                $.ajax ({
                    type : 'POST',
                    url : ip.baseUrl,
                    data : data,
                    context : this,
                    //success : $.proxy(methods._storeFilesResponse, this),
                    success : methods._getDeleteFilesResponse,
                    error : function(){}, //TODO report error
                    dataType : 'json'
                });
            }
        },

        _getDeleteFilesResponse : function(response) {
            var $this = $(this);
            var repositoryContainer = this;

            if (!response || !response.success) {
                return; //TODO report error
            }

            // notify that not all files were deleted
            if (parseInt(response.notRemovedCount) > 0) {
                alert(ipRepositoryTranslate_delete_warning);
            }

            // remove deleted files
            var deletedFiles = response.deletedFiles;
            var $browser = $this.find('.ipmBrowser');
            for(var i in deletedFiles) {

                var  animateOptions = {};

                switch (settings.preview) {
                    case 'thumbnails':
                        animateOptions = {width: 0, paddingLeft: 0, paddingRight: 0, marginLeft: 0, marginRight: 0};
                        break;
                    default:
                        animateOptions = {height: 0, paddingTop: 0, paddingBottom: 0, marginTop: 0, marginBottom: 0};
                        break;
                }


                $browser.find("li[data-file='"+deletedFiles[i]+"']")
                    .css('overflow', 'hidden')
                    .css('border-bottom', 'none')
                    .animate(animateOptions, 'slow')
                    .hide(0, function() {
                        $(this).remove();
                        // recalculating selected files
                        $.proxy(methods._countSelected, repositoryContainer)();
                    })
                ;
            }
        },

        // set back our element
        _teardown : function() {
            $(window).unbind('resize.ipRepositoryAll');
        },

        _resize : function(e) {
            var $this = $(this);
            var $block = $this.find('.ipmBrowser');
            var padding = parseInt($block.css('padding-top')) + parseInt($block.css('padding-bottom'));
            $block.height((parseInt($(window).height()) - (37 + padding)) + 'px');
        }

    };

    $.fn.ipRepositoryAll = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipRepositoryAll');
        }

    };

})(ip.jQuery);
