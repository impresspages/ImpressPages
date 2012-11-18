

"use strict";

(function($) {

    var methods = {

        init : function(options) {

            return this.each(function() {

                var $this = $(this);

                var data = $this.data('ipRepositoryFileContainer');
                if (!data) {



                    var uploaderConfig = {
                        runtimes : 'gears,html5,flash,silverlight,browserplus',
                        browse_button : 'ipModRepositoryUploadButton',

                        max_file_size : '10000Mb',
                        chunk_size : '1mb',
                        url : ip.baseUrl, //website root (available globally in ImpressPages environment)
                        multipart_params : {
                            g : 'developer',
                            m : 'upload',
                            a : 'upload'
                        },

                        //if you add "multipart: false," IE fails.

                        flash_swf_url : ip.baseUrl + ip.libraryDir + 'js/plupload/plupload.flash.swf',
                        silverlight_xap_url : ip.baseUrl + ip.libraryDir + 'js/plupload/plupload.silverlight.xap',

                        button_browse_hover : true,
                        drop_element : "ipModRepositoryDragContainer",
                        autostart : true,
                        container: "ipModRepositoryTabUpload"
                    };


                    var uploader = new plupload.Uploader(uploaderConfig);
                    uploader.init();


                    uploader.bind('Error', $.proxy(methods._error, this));
                    uploader.bind('FilesAdded', $.proxy(methods._filesAdded, this));
                    uploader.bind('UploadProgress', $.proxy(methods._uploadProgress, this));
                    uploader.bind('FileUploaded', $.proxy(methods._fileUploaded, this));

                    $( ".ipmFiles" ).sortable();
                    $( ".ipmFiles" ).sortable('option', 'handle', '.ipaFileMove');
                }
            });
        },

        _error : function(up, err) {
            var $newError = $(this).find('.ipmErrorSample').clone().removeClass('ipmErrorSample').removeClass('ipgHide');
            $newError.text(err.message);
            setTimeout(function(){$newError.remove();}, 3000);
            $(this).find('.ipmCurErrors').append($newError);
            console.log("Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : ""));
            up.refresh(); // Reposition Flash/Silverlight
        },

        _filesAdded : function(up, files) {
            var $this = $(this);
            $.each(files, function(i, file) {
                var $newFileProgressbar = $this.find('.ipUploadProgressItemSample .ipUploadProgressItem').clone();
                $newFileProgressbar.attr('id', 'ipUpload_' + file.id);
                $newFileProgressbar.find('.ipUploadTitle').text(file.name);
                $newFileProgressbar.find('.ipUploadProgressbar').progressbar({value : file.percent});
                $this.find('.ipUploadProgressContainer').append($newFileProgressbar);
                //console.log('File added ' + file.id + ' ' + file.name + ' (' + plupload.formatSize(file.size) + ')');
            });
            up.refresh(); // Reposition Flash/Silverlight
            up.start();
        },

        _uploadProgress : function(up, file) {
            var $this = $(this);
            $this.find('#ipUpload_' + file.id + ' .ipUploadProgressbar').progressbar({value : file.percent});
            $('#' + file.id + " b").html(file.percent + "%");
        },

        _fileUploaded : function(up, file, response) {
            var $this = $(this);

            var answer = jQuery.parseJSON(response.response);

            if (answer.error) {
                $.proxy(methods._error, this)(up, answer.error);
            } else {
                var $fileRecord = $this.find('.ipmFileSample').clone();
                $fileRecord.removeClass('ipgHide');
                $fileRecord.removeClass('ipmFileSample');
                $fileRecord.find('.ipaFileTitle').val(answer.fileName);
                $this.find('.ipmFiles').append($fileRecord);
            }

            $this.find('#ipUpload_' + file.id).remove();
        },



        getFiles : function () {
            var $this = this;
            return $this.find('.ipaFileTemplate');
        }



    };

    $.fn.ipRepositoryFileContainer = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipAdminWidgetButton');
        }

    };

})(jQuery);



