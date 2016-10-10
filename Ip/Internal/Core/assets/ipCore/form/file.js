/**
 * @package ImpressPages
 *
 */


(function ($) {
    "use strict";

    var methods = {

        init: function (options) {

            return this.each(function () {

                var $this = $(this);

                var data = $this.data('ipFormFileSettings');
                if (!data) {
                    $this.data('ipFormFileSettings', {
                        limit: parseInt($this.data('filelimit'))
                    });

                    //the only reliable way to wait till PLupload loads is to periodically check if it has been loaded

                    var loadInterval = setInterval(function () {
                        initPlupload($this, loadInterval);
                    }, 300);


                }
            });
        },

        _error: function (up, err) {
            var $this = this;

            var $file = $('#ipModFormFile_' + $this.data('ipFormFile').uniqueNumber + '_' + err.file.id);
            if (!$file.length) {
                //some errors occure before filesAdded event. In that case we artificially throw that event to have file object where to display the error
                $.proxy(methods._filesAdded, this)(up, $(err.file));
            }

            $.proxy(methods._displayError, this)(err.file.id, err.message);
        },

        _displayError: function (fileId, errorMessage, fileName) {
            var $this = $(this);
            var $file = $('#ipModFormFile_' + $this.data('ipFormFile').uniqueNumber + '_' + fileId);
            $file.find('.ipsUploadError').text(errorMessage);
            $file.find('.ipsFileProgress').remove();

        },

        _filesAdded: function (up, files) {
            var $this = this;
            $.each(files, function (i, file) {
                var $file = $('#ipModFormFile_' + $this.data('ipFormFile').uniqueNumber + '_' + file.id);
                if (!$file.length) {//in some cases _error method creates file record. This line is to avoid adding the same file twice
                    var $newFile = $this.find('.ipsFileTemplate').clone();
                    $newFile.removeClass('ipsFileTemplate');
                    $newFile.data('fileId', file.id);
                    $newFile.removeClass('hidden');
                    $newFile.attr('id', 'ipModFormFile_' + $this.data('ipFormFile').uniqueNumber + '_' + file.id);
                    $newFile.find('.ipsFileName').text(file.name);
                    $newFile.find('.ipsRemove').click(function (e) {
                        var $this = $(this);
                        var $file = $this.closest('.ipsFile');
                        var fileId = $file.data('fileId');

                        /* Seems that removeFile method is used just for files that are not started to be upload
                         var uploader = $this.closest('.ipsFileContainer').data('ipFormFile').uploader;
                         var uploaderFile = uploader.getFile(fileId)
                         uploader.removeFile(uploaderFile);
                         */
                        $file.trigger('removed.ipFileField');
                        $file.remove();

                    });
                    if ($this.data('ipFormFileSettings').limit >= 0) {
                        if ($this.find('.ipsFiles').children().length + 1 > $this.data('ipFormFileSettings').limit) {
                            if ($this.find('.ipsFiles').children().first().length === 1) {
                                $this.find('.ipsFiles').children().first().remove();
                            }
                        }
                    }
                    $this.find('.ipsFiles').append($newFile);
                    $newFile.trigger('added.ipFileField');

                }
            });
            up.refresh(); // Reposition Flash/Silverlight
            up.start();
        },

        _uploadProgress: function (up, file) {
            var $this = this;
            var $file = $('#ipModFormFile_' + $this.data('ipFormFile').uniqueNumber + '_' + file.id);
            $file.find('.ipsFileProgressValue').width(file.percent + '%');
            $file.trigger('progress.ipFileField', [file.percent, file]);
        },

        _fileUploaded: function (up, file, response) {
            var $this = this;
            var $file = $('#ipModFormFile_' + $this.data('ipFormFile').uniqueNumber + '_' + file.id);
            if (!$file.length) {
                return; //file has been removed by user
            }

            var answer = jQuery.parseJSON(response.response);

            if (answer.error) {
                $.proxy(methods._displayError, this)(file.id, answer.error.message);
            } else {
                file.realFileName = answer['fileName'];
                var $fileInput = $('<input class="ipmUploadedData" name="" type="hidden" value="" />');
                $fileInput.attr('name', $this.data('ipFormFile').inputName + '[file][]');
                $fileInput.attr('value', answer.fileName);
                $file.append($fileInput);
                var $fileInput = $('<input class="ipmUploadedData" name="" type="hidden" value="" />');
                $fileInput.attr('name', $this.data('ipFormFile').inputName + '[originalFileName][]');
                $fileInput.attr('value', file.name);
                $file.append($fileInput);
                $file.find('.ipsFileProgress').remove();
                $file.trigger('uploaded.ipFileField', [file]);

            }

        },

        _getFiles: function () {
            var $this = $(this);
            var files = [];
            $this.find('.ipsFiles div').each(function () {

                var $this = $(this);

                if ($this.data('deleted')) {
                    return;
                }

                files.push({
                    fileName: $this.data('fileName'),
                    file: $this.data('file'),
                    renameTo: $this.find('.ipsRenameTo').val(),
                    dir: $this.data('dir')
                });
            });
            return files;
        }




    };


    var initPlupload = function (field, loadInterval) {
        if (typeof(plupload) == 'undefined') {
            //Wait for spectrum to load
            return;
        }
        clearInterval(loadInterval);

        var $this = field;

        var uniqueNumber = Math.floor(Math.random() * 100000000);
        var $uploadButton = $this.find('.ipsFileAddButton');
        if (!$uploadButton.attr('id')) {
            $uploadButton.attr('id', 'ipModFormFileAddButton_' + uniqueNumber);
        }
        var $uploadContainer = $this;
        if (!$uploadContainer.attr('id')) {
            $uploadContainer.attr('id', 'ipModFormFileContainer_' + uniqueNumber);
        }

        var uploaderConfig = {
            runtimes: 'gears,html5,flash,silverlight,browserplus',
            browse_button: $uploadButton.attr('id'),

            max_file_size: '10000Mb',
            chunk_size: '1mb',
            url: ip.baseUrl, //website root (available globally in ImpressPages environment)
            multipart_params: {
                sa: 'Repository.upload',
                secureFolder: 1,
                securityToken: ip.securityToken
            },

            //if you add "multipart: false," IE fails.

            flash_swf_url: ipFileUrl('Ip/Internal/Core/assets/ipCore/plupload/plupload.flash.swf'),
            silverlight_xap_url: ipFileUrl('Ip/Internal/Core/assets/ipCore/plupload/plupload.silverlight.xap'),

            button_browse_hover: true,
            //drop_element : "ipModuleRepositoryDragContainer",
            autostart: true,
            container: $uploadContainer.attr('id')
        };
        var uploader = new plupload.Uploader(uploaderConfig);
        uploader.bind('Error', $.proxy(methods._error, $this));
        uploader.bind('UploadProgress', $.proxy(methods._uploadProgress, $this));
        uploader.bind('FileUploaded', $.proxy(methods._fileUploaded, $this));

        uploader.init();
        // for handling method to work uploader needs to be initialised first
        uploader.bind('FilesAdded', $.proxy(methods._filesAdded, $this));

        $this.data('ipFormFile', {
            uniqueNumber: uniqueNumber,
            inputName: $this.data('inputname'),
            uploader: uploader
        });
    };

    $.fn.ipFormFile = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipFormFile');
        }

    };

    $('.ipsModuleFormPublic .ipsFileContainer').ipFormFile();

})(jQuery);
