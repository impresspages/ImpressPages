

"use strict";

(function($) {

    var methods = {

        init : function(options) {

            return this.each(function() {

                var $this = $(this);

                var data = $this.data('ipRepositoryRecent');
                if (!data) {
                    $this.find('.ipaConfirm').bind('click', $.proxy(methods._confirm, this));
                    $this.find('.ipaCancel').bind('click', $.proxy(methods._cancel, this));

                    $this.data('ipRepositoryRecent', {});

                    var data = Object();
                    data.g = 'administrator';
                    data.m = 'repository';
                    data.a = 'getRecent';

                    $.ajax ({
                        type : 'POST',
                        url : ip.baseUrl,
                        data : data,
                        context : this,
                        //success : $.proxy(methods._storeFilesResponse, this),
                        success : methods._getRecentFilesResponse,
                        error : function(){}, //TODO report error
                        dataType : 'json'
                    });

                }
            });
        },

        _getRecentFilesResponse : function(response) {
            var $this = $(this);

            if (!response || !response.files) {
                return; //TODO report error
            }

            var files = response.files;
            var $browserContainer = $this.find('.ipmBrowserContainer');
            var $template = $this.find('.ipsFileTemplate');

            for(var i in files) {
                var $newItem = $('<li></li>');
                $newItem.append($template.clone().removeClass('ipgHide').attr('src', ip.baseUrl + files[i].file));
                $newItem.data('fileData', files[i]);
                $browserContainer.append($newItem);
            }


//            $browserContainer.bind("mousedown", function(e) {
//                e.metaKey = true;
//            }).selectable();
            $browserContainer.selectable();

        },

        _confirm : function (e) {

            var $this = $(this);

            var files = new Array();

            $this.find('li.ui-selected').each(function(){
                var $this = $(this);
                files.push($this.data('fileData'));
            });

            $this.trigger('ipModRepository.confirm', [files]);

        },

        _cancel : function(e) {
            e.preventDefault();
            $(this).trigger('ipModRepository.cancel');
        }

    };

    $.fn.ipRepositoryRecent = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipRepositoryRecent');
        }

    };

})(jQuery);



