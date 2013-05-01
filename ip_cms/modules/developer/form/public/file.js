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
                    var uploader = new plupload.Uploader(uploaderConfig);
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
            var $this = $(this);

            var $file = $('#ipModFormFile_' + $this.data('ipFormFile').uniqueNumber + '_' + err.file.id);
            if (!$file.length) {
                //some errors occure before filesAdded event. In that case we artificially throw that event to have file object where to display the error
                $.proxy(methods._filesAdded, this)(up, $(err.file));
            }

            $.proxy(methods._displayError, this)(err.file.id, err.message);
        },

        _displayError : function (fileId, errorMessage, fileName) {
            var $this = $(this);
            var $file = $('#ipModFormFile_' + $this.data('ipFormFile').uniqueNumber + '_' + fileId);
            $file.find('.ipmUploadError').text(errorMessage);
            $file.find('.ipmFileProgress').remove();

        },

        _filesAdded : function(up, files) {
            var $this = $(this);
            $.each(files, function(i, file) {
                var $file = $('#ipModFormFile_' + $this.data('ipFormFile').uniqueNumber + '_' + file.id);
                if (!$file.length) {//in some cases _error method creates file record. This line is to avoid adding the same file twice
                    var $newFile = $this.find('.ipmFileTemplate').clone();
                    $newFile.removeClass('ipgHide').removeClass('ipmFileTemplate');
                    $newFile.attr('id', 'ipModFormFile_' + $this.data('ipFormFile').uniqueNumber + '_' + file.id);
                    $newFile.find('.ipmFileName').text(file.name);
                    $this.find('.ipmFiles').append($newFile);
                } else {
                    console.log('exist ' + file.name);

                }
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

        _fileUploaded : function(up, file, response) {console.log(response);
            var $this = $(this);
            var $file = $('#ipModFormFile_' + $this.data('ipFormFile').uniqueNumber + '_' + file.id);

            var answer = jQuery.parseJSON(response.response);

            if (answer.error) {
                $.proxy(methods._displayError, this)(file.id, answer.error.message);
            } else {
                var $fileInput = $('<input name="' + $this.data('ipFormFile').inputName + '[file][]" type="hidden" value="' + answer.fileName + '" />');
                $this.append($fileInput);
                var $fileInput = $('<input name="' + $this.data('ipFormFile').inputName + '[originalFileName][]" type="hidden" value="' + file.name + '" />');
                $this.append($fileInput);
                $file.find('.ipmFileProgress').remove();

            }

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
