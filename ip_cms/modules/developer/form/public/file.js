/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

"use strict";


(function($) {

    var methods = {

        init : function(options) {

            return this.each(function() {

                var $this = $(this);

                var data = $this.data('ipFormFile');
                if (!data) {


                    var uniqueNumber = Math.floor(Math.random()*100000000);
                    var $uploadButton = $this.find('.ipmFileAddButton');
                    if (!$uploadButton.attr('id')) {
                        $uploadButton.attr('id', 'ipModFormFileAddButton_' + uniqueNumber);
                    }
                    var $uploadContainer = $this;
                    if (!$uploadContainer.attr('id')) {
                        $uploadContainer.attr('id', 'ipModFormFileContainer_' + uniqueNumber);
                    }




                    var uploaderConfig = {
                        runtimes : 'gears,html5,flash,silverlight,browserplus',
                        browse_button : $uploadButton.attr('id'),

                        max_file_size : '10000Mb',
                        chunk_size : '1mb',
                        url : ip.baseUrl, //website root (available globally in ImpressPages environment)
                        multipart_params : {
                            g : 'administrator',
                            m : 'repository',
                            a : 'upload',
                            secureFolder : 1
                        },

                        //if you add "multipart: false," IE fails.

                        flash_swf_url : ip.baseUrl + ip.libraryDir + 'js/plupload/plupload.flash.swf',
                        silverlight_xap_url : ip.baseUrl + ip.libraryDir + 'js/plupload/plupload.silverlight.xap',

                        button_browse_hover : true,
                        //drop_element : "ipModuleRepositoryDragContainer",
                        autostart : true,
                        container: $uploadContainer.attr('id')
                    };
                    console.log(uploaderConfig);
                    var uploader = new plupload.Uploader(uploaderConfig);console.log(uploader);
                    uploader.bind('Init', function(up) {
                        // if dragdrop is possible, we'll enhance UI
//                        if (up.features.dragdrop) {
//                            $('#'+uploaderConfig.drop_element)
//                                .addClass('dragdrop')
//                                .bind('dragover', function(){
//                                    $(this).addClass('hover');
//                                })
//                                .bind('dragexit drop', function(){
//                                    $(this).removeClass('hover');
//                                });
//                        }
                    });

                    uploader.bind('Error', $.proxy(methods._error, this));
                    uploader.bind('UploadProgress', $.proxy(methods._uploadProgress, this));
                    uploader.bind('FileUploaded', $.proxy(methods._fileUploaded, this));
                    uploader.init();

                    // for handling method to work uploader needs to be initialised first
                    uploader.bind('FilesAdded', $.proxy(methods._filesAdded, this));

                    $this.data('ipFormFile', {
                        uniqueNumber: uniqueNumber,
                        inputName: $this.data('inputname')
                    });
                }
            });
        },


        _error : function(up, err) {
            alert(err.message);
//            var $newError = $(this).find('.ipmErrorSample').clone().removeClass('ipmErrorSample').removeClass('ipgHide');
//            $newError.text(err.message);
//            setTimeout(function(){$newError.remove();}, 9000);
//            $(this).find('.ipmCurErrors').append($newError);
//            up.refresh(); // Reposition Flash/Silverlight


        },

        _filesAdded : function(up, files) {
            var $this = $(this);
            $.each(files, function(i, file) {
                var $newFile = $this.find('.ipmFileTemplate').clone();
                $newFile.removeClass('ipgHide').removeClass('ipmFileTemplate');
                $newFile.attr('id', 'ipModFormFile_' + $this.data('ipFormFile').uniqueNumber + '_' + file.id);
                $newFile.find('.ipmFileName').text(file.name);
                $this.find('.ipmFiles').append($newFile);
            });
            up.refresh(); // Reposition Flash/Silverlight
            up.start();
        },

        _uploadProgress : function(up, file) {
            var $this = $(this);
            var $file = $('#ipModFormFile_' + $this.data('ipFormFile').uniqueNumber + '_' + file.id);
            $file.find('.ipmFileProgressValue').width(Math.round($file.find('.ipmFileProgress').width() * file.percent / 100));
            $file.trigger('progress.ipModuleFormFile', [file.percent]);
        },

        _fileUploaded : function(up, file, response) {
            var $this = $(this);

            var answer = jQuery.parseJSON(response.response);

            if (answer.error) {
                $.proxy(methods._error, this)(up, answer.error);
            } else {
                console.log('completed');
                console.log(file);
                console.log(answer.fileName); return;
                var $fileInput = $('<input name="' + $this.data('ipFormFile').inputName + '[\'file\']" type="hidden" value="' + answer.fileName + '" />');
                $this.append($fileInput);
                var $fileInput = $('<input name="' + $this.data('ipFormFile').inputName + '[\'originalFileName\']" type="hidden" value="' + answer.fileName + '" />');
                $this.append($fileInput);
            }

            $this.find('#ipUpload_' + file.id).remove();
        },

        _getFiles : function () {
            var $this = $(this);
            var files = new Array();
            $this.find('.ipmFiles div').each(function(){

                var $this = $(this);

                if ($this.data('deleted')) {
                    return;
                }

                files.push({
                    fileName : $this.data('fileName'),
                    file : $this.data('file'),
                    renameTo : $this.find('.ipaRenameTo').val(),
                    dir : $this.data('dir')
                });
            });
            return files;
        }



    };

    $.fn.ipFormFile = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipFormFile');
        }

    };

})(jQuery);


$('.ipModuleForm .ipmFileContainer').ipFormFile();
