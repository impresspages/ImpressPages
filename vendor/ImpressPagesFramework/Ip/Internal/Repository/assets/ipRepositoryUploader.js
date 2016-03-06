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

                var data = $this.data('ipRepositoryUploader');
                if (!data) {
                    var $popup = $('.ipsModuleRepositoryPopup');
                    $this.data('ipRepositoryUploader', options);

                    var uploaderConfig = {
                        runtimes: 'gears,html5,flash,silverlight,browserplus',
                        browse_button: 'ipsModuleRepositoryUploadButton',

                        max_file_size: '10000Mb',
                        chunk_size: '1mb',
                        url: ip.baseUrl, //website root (available globally in ImpressPages environment)
                        multipart_params: {
                            sa: 'Repository.upload',
                            secureFolder: options.secure || 0,
                            securityToken: ip.securityToken
                        },

                        //if you add "multipart: false," IE fails.

                        flash_swf_url: ipFileUrl('Ip/Internal/Core/assets/ipCore/plupload/plupload.flash.swf'),
                        silverlight_xap_url: ipFileUrl('Ip/Internal/Core/assets/ipCore/plupload/plupload.silverlight.xap'),

                        button_browse_hover: true,
                        drop_element: "ipsModuleRepositoryDragContainer",
                        autostart: true,
                        container: "ipsModuleRepositoryTabUpload"
                    };

                    var uploader = new plupload.Uploader(uploaderConfig);
                    uploader.bind('Init', function (up) {
                        // if dragdrop is possible, we'll enhance UI
                        if (up.features.dragdrop) {
                            $('#' + uploaderConfig.drop_element)
                                .addClass('dragdrop')
                                .bind('dragover', function () {
                                    $(this).addClass('hover');
                                })
                                .bind('dragexit drop', function () {
                                    $(this).removeClass('hover');
                                });
                        }
                    });

                    uploader.bind('Error', $.proxy(methods._error, this));
                    uploader.bind('UploadProgress', $.proxy(methods._uploadProgress, this));
                    uploader.bind('FileUploaded', $.proxy(methods._fileUploaded, this));
                    uploader.init();

                    // for handling method to work uploader needs to be initialised first
                    uploader.bind('FilesAdded', $.proxy(methods._filesAdded, this));

                    $(window).bind("resize.ipRepositoryUploader", $.proxy(methods._resize, this));
                    $popup.bind('ipModuleRepository.close', $.proxy(methods._teardown, this));
                    $.proxy(methods._resize, this)();

                }
            });
        },


        _storeFilesResponse: function (response) {
            var $this = $(this);

            if (!response || !response.status || response.status == 'error') {
                //incorrect response
            }

            $this.ipRepositoryAll('addRecentFiles', response.files);
        },

        _cancel: function (e) {
            e.preventDefault();
            $(this).trigger('ipModuleRepository.cancel');
        },

        _error: function (up, err) {
            var $newError = $(this).find('.ipsErrorSample').clone().removeClass('ipsErrorSample').removeClass('hidden');
            $newError.text(err.message);
            setTimeout(function () {
                $newError.remove();
            }, 9000);
            $(this).find('.ipsCurErrors').append($newError);
            up.refresh(); // Reposition Flash/Silverlight
        },

        _filesAdded: function (up, files) {
            var $this = $(this);
            $.each(files, function (i, file) {
                var $newFileProgressbar = $this.find('.ipsUploadProgressItemSample .ipsUploadProgressItem').clone();
                $newFileProgressbar.attr('id', 'ipUpload_' + file.id);
                $newFileProgressbar.find('.ipsUploadTitle').text(file.name);
                $newFileProgressbar.find('.ipsUploadProgressbar').progressbar({value: file.percent});
                $this.find('.ipsUploadProgressContainer .ipsBrowseButtonWrapper').first().after($newFileProgressbar);
            });
            up.refresh(); // Reposition Flash/Silverlight
            up.start();
        },

        _uploadProgress: function (up, file) {
            var $this = $(this);
            $this.find('#ipUpload_' + file.id + ' .ipsUploadProgressbar').progressbar({value: file.percent});
            $('#' + file.id + " b").html(file.percent + "%");
        },

        _fileUploaded: function (up, file, response) {
            var $this = $(this);
            var answer = jQuery.parseJSON(response.response);
            if (answer.error) {
                $.proxy(methods._error, this)(up, answer.error);
            } else {

                var files = new Array();
                files.push({
                    fileName: answer.fileName,
                    renameTo: answer.fileName
                });

                var options = $this.data('ipRepositoryUploader');

                var data = Object();
                data.aa = 'Repository.storeNewFiles';
                data.files = files;
                data.securityToken = ip.securityToken;
                data.secure = options.secure;
                data.path = options.path;

                $.ajax({
                    type: 'POST',
                    url: ip.baseUrl,
                    data: data,
                    context: $this,
                    success: $.proxy(methods._storeFilesResponse, this),
                    dataType: 'json'
                });

            }

            $this.find('#ipUpload_' + file.id).remove();
        },

        // set back our element
        _teardown: function () {
            $(window).unbind('resize.ipRepositoryUploader');
        },

        _resize: function (e) {
            var $popup = $('.ipsModuleRepositoryPopup');
            var $block = $popup.find('.ipsUpload');
            var tabsHeight = parseInt($popup.find('.ipsTabs').outerHeight());
            $block.outerHeight((parseInt($(window).height()) - tabsHeight));
        }

    };

    $.fn.ipRepositoryUploader = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipRepositoryUploader');
        }

    };

})(jQuery);
