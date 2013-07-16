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

                    $(window).bind("resize.ipRepositoryAll", $.proxy(methods._resize, this));
                    $popup.bind('ipModuleRepository.close', $.proxy(methods._teardown, this));
                    $.proxy(methods._resize, this)();
                }
            });
        },

        addRecentFiles : function (files) {
            var $this = $(this);
            $this.find('.ipaRecentTitle').removeClass('ipgHide');
            $this.find('.ipaRecentList').removeClass('ipgHide');

            var $template = $this.find('.ipsFileTemplate');
            var $newList = $this.find('.ipaRecentList');

            for(var i in files) {
                var $newItem = $('<li></li>');
                $newItem.append($template.clone().removeClass('ipgHide').removeClass('ipsFileTemplate'));
                $newItem.find('img').attr('src', ip.baseUrl + files[i].preview);
                $newItem.find('.name').text(files[i].fileName);
                $newItem.data('fileData', files[i]);
                $newList.append($newItem);
            }

        },

        _getAllFilesResponse : function(response) {
            var $this = $(this);
            var repositoryContainer = this;

            if (!response || !response.fileGroups) {
                return; //TODO report error
            }

            var fileGroups = response.fileGroups;
            var $browserContainer = $this.find('.ipmBrowserContainer');
            var $template = $this.find('.ipsFileTemplate');
            var $listTemplate = $this.find('.ipsListTemplate');
            var $titleTemplate = $this.find('.ipsListTitleTemplate');


            for(var gi in fileGroups) {
                var $newList = $listTemplate.clone().detach().removeClass('ipsListTemplate');
                var $newTitle = $titleTemplate.clone().detach().removeClass('ipsListTitleTemplate');
                $newTitle.text(gi);
                for(var i in fileGroups[gi]) {
                    var files = fileGroups[gi];
                    var $newItem = $('<li></li>');
                    $newItem.append($template.clone().removeClass('ipgHide').removeClass('ipsFileTemplate'));
                    $newItem.find('img').attr('src', ip.baseUrl + files[i].preview);
                    $newItem.find('.name').text(files[i].fileName);
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
            $block.height((parseInt($(window).height()) - (110 + padding)) + 'px');
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
