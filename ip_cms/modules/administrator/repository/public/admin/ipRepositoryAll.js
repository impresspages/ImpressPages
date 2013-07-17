/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 *
 */

"use strict";

(function($) {

    var methods = {

        init : function(options) {

            return this.each(function() {

                var $this = $(this);

                var data = $this.data('ipRepositoryAll');
                if (!data) {
                    var $popup = $('.ipModuleRepositoryPopup');

                    $this.data('ipRepositoryAll', {});

                    var data = Object();
                    data.g = 'administrator';
                    data.m = 'repository';
                    data.a = 'getAll';

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

                    $(window).bind("resize.ipRepositoryAll", $.proxy(methods._resize, this));
                    $popup.bind('ipModuleRepository.close', $.proxy(methods._teardown, this));
                    $.proxy(methods._resize, this)();
                }
            });
        },

        addRecentFiles : function (files) {
            var $this = $(this);
            $this.find('.ipmRecentTitle').removeClass('ipgHide');
            $this.find('.ipmRecentList').removeClass('ipgHide');

            var $template = $this.find('.ipmFileTemplate');
            var $newList = $this.find('.ipmRecentList');

            for(var i in files) {
                var $newItem = $template.clone().removeClass('ipmFileTemplate');
                $newItem.find('img').attr('src', ip.baseUrl + files[i].preview);
                $newItem.find('.name').text(files[i].fileName);
                $newItem.data('fileData', files[i]);

                $newItem.toggleClass('ui-selected');
                $newList.append($newItem);
            }
            $.proxy(methods._startSelect, this)();


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
                var $newTitle = $titleTemplate.clone().detach().removeClass('ipmListTitleTemplate');
                $newTitle.text(gi);
                for(var i in fileGroups[gi]) {
                    var files = fileGroups[gi];
                    var $newItem = $template.clone().removeClass('ipmFileTemplate');
                    $newItem.find('img')
                        .attr('src', ip.baseUrl + files[i].preview)
                        .attr('alt', files[i].fileName)
                        .attr('title', files[i].fileName);
                    $newItem.data('fileData', files[i]);
                    $newList.append($newItem);

                }
                $browserContainer.append($newTitle);
                $browserContainer.append($newList);

            }

            $this.find('.ipmRepositoryActions .ipaSelectionConfirm').click($.proxy(methods._confirm, this))
            $this.find('.ipmRepositoryActions .ipaSelectionCancel').click($.proxy(methods._stopSelect, this))

            $browserContainer.delegate('li', 'click', function(e){
                $(this).toggleClass('ui-selected');
                $.proxy(methods._startSelect, repositoryContainer)();
            });

        },

        _startSelect : function(e) {
            var $this = $(this);
            $this.find('.ipmRepositoryActions').removeClass('ipgHide');

        },

        _stopSelect : function(e) {
            var $this = $(this);
            $this.find('.ipmRepositoryActions').addClass('ipgHide');
            $this.find('.ipmBrowserContainer li').removeClass('ui-selected');
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

        // set back our element
        _teardown: function() {
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

})(jQuery);
