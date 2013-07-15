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
                    $this.find('.ipaConfirm').bind('click', $.proxy(methods._confirm, this));
                    $this.find('.ipmBrowser').dblclick($.proxy(methods._confirm, this));
                    $this.find('.ipaCancel').bind('click', $.proxy(methods._cancel, this));

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

        _getAllFilesResponse : function(response) {
            var $this = $(this);

            if (!response || !response.fileGroups) {
                return; //TODO report error
            }

            var fileGroups = response.fileGroups;
            var $browserContainer = $this.find('.ipmBrowserContainer');
            var $template = $this.find('.ipsFileTemplate');
            var $listTemplate = $this.find('.ipsListTemplate');
            var $titleTemplate = $this.find('.ipsListTitleTemplate');


//            var fileGroups = new Array();
//            console.log(files);
//            for(var i in files) {
//                var modified = new Date(files[i].modified*1000);
//                console.log(modified.getFullYear() + modified.getHours());
//                //fileGroups[]
//            }


            for(var gi in fileGroups) {
                var $newList = $listTemplate.clone().detach().removeClass('ipsListTemplate');
                var $newTitle = $titleTemplate.clone().detach().removeClass('ipsListTitleTemplate');
                $newTitle.text(gi);
                for(var i in fileGroups[gi]) {
                    var files = fileGroups[gi];
                    var $newItem = $('<li></li>');
                    $newItem.append($template.clone().removeClass('ipgHide'));
                    $newItem.find('img').attr('src', ip.baseUrl + files[i].preview)
                    $newItem.find('.name').text(files[i].fileName);
                    $newItem.data('fileData', files[i]);
                    $newList.append($newItem);

                }
                $newList.selectable();
                $browserContainer.append($newTitle);
                $browserContainer.append($newList);

            }

//            $browserContainer.bind("mousedown", function(e) {
//                e.metaKey = true;
//            }).selectable();

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
