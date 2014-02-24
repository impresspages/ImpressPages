/**
 * @package ImpressPages
 *
 *
 */

/**
 *
 * Available options:
 *
 *
 * uploadHandler - link to PHP script that will accept uploads (not implemented)
 *
 */


(function($) {
    "use strict";

    var methods = {
        init : function(options) {

            return this.each(function() {
                var $this = $(this);

                var data = $this.data('ipUploadFile');
                // If the plugin hasn't been initialized yet
                if ( ! data ) {

                    if (typeof options.maxFileSize == 'undefined') {
                        options.maxFileSize = '10000mb';
                    }
                    if (typeof options.filterExtensions == 'undefined') {
                        options.filterExtensions = null
                    }



                    var uniqueId = Math.floor(Math.random()*9999999999999999) + 1;

                    $this.data('ipUploadFile', {
                        maxFileSize : options.maxFileSize,
                        filterExtensions : options.filterExtensions,
                        uniqueId : uniqueId

                    });

                    var data = Object();
                    data.a = 'Upload.getFileContainerHtml';

                    $.ajax({
                        type : 'GET',
                        url : ip.baseUrl,
                        data : data,
                        context : $this,
                        success : methods._containerHtmlResponse,
                        dataType : 'json'
                    });



                }
            });

        },



        _containerHtmlResponse : function (response) {
            var $this = this;

            if (response.status != 'success') {
                return;
            }

            $this.html(response.html);
            var data = $this.data('ipUploadFile');


            $this.find('.ipUploadBrowseButton').attr('id', 'ipUploadButton_' + data.uniqueId);


            var uploaderConfig = {
                    runtimes : 'gears,html5,flash,silverlight,browserplus',
                    browse_button : 'ipUploadButton_' + data.uniqueId,

                    max_file_size : data.maxFileSize,
                    url : ip.baseUrl, //website root (available globaly in ImpressPages environment)
                    multipart_params : {
                        aa : 'Upload.upload',
                        securityToken : ip.securityToken
                    },

                    //if you add "multipart: false," IE fails.

                    flash_swf_url : ipFileUrl('Ip/Internal/Core/assets/admin/plupload/plupload.flash.swf'),
                    silverlight_xap_url : ipFileUrl('Ip/Internal/Core/assets/admin/plupload/plupload.silverlight.xap')
            };

            if (data.filterExtensions) {
                uploaderConfig.filters = [{title : "Filtered files", extensions : data.filterExtensions.join(',')}];
            }

            var uploader = new plupload.Uploader(uploaderConfig);


            uploader.bind('Init', function(up, params) {
            });


            uploader.init();


            uploader.bind('FilesAdded', function(up, files) {

                $.each(files, function(i, file) {
                    $this.trigger('fileAdded.ipUploadFile', file);
                    $this.ipUploadFile('_fileAdded', up, file)
                    //console.log('File added ' + file.id + ' ' + file.name + ' (' + plupload.formatSize(file.size) + ')');
                });
                up.refresh(); // Reposition Flash/Silverlight
                up.start();
            });

            uploader.bind('UploadProgress', function(up, file) {
                $this.ipUploadFile('_fileProgress', up, file)
                $this.trigger('uploadProgress.ipUploadFile', file);
                //$('#' + file.id + " b").html(file.percent + "%");
            });

            uploader.bind('Error', function(up, err) {
                var errorMessage = err.message + (err.file ? " \"" + err.file.name + "\"" : "");
                $this.trigger('error.ipUploadFile', errorMessage);
                up.refresh(); // Reposition Flash/Silverlight
            });

            uploader.bind('FileUploaded', function(up, file, response) {
                $this.ipUploadFile('_uploadedNewFile', up, file, response);
            });

        },

        _fileProgress : function (up, file) {
            $this.find('#ipUpload_' + file.id + ' .ipUploadProgressbar').progressbar({value : file.percent});
        },

        _fileAdded : function (up, file) {
            $this = $(this);

            var $newFileProgressbar = $this.find('.ipUploadProgressItemSample .ipUploadProgressItem').clone();
            $newFileProgressbar.attr('id', 'ipUpload_' + file.id);
            $newFileProgressbar.find('.ipUploadTitle').text(file.name);
            $newFileProgressbar.find('.ipUploadProgressbar').progressbar({value : file.percent});
            $this.find('.ipUploadProgressContainer').append($newFileProgressbar);
            //console.log('File added ' + file.id + ' ' + file.name + ' (' + plupload.formatSize(file.size) + ')');
        },

        _uploadedNewFile : function (up, file, response) {
            var $this = $(this);
            var answer = jQuery.parseJSON(response.response);

            if (answer.error) {
                $this.trigger('error.ipUploadFile', answer.error.message);
            } else {
                var data = $this.data('ipUploadFile');
                $this.data('ipUploadFile', data);
                $this.trigger('filesSelected.ipUploadFile', [answer.fileName]);
            }

            $this.find('#ipUpload_' + file.id).remove();
        }



    };


    $.fn.ipUploadFile = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipUploadFile');
        }


    };

})(ip.jQuery);
