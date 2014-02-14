/**
 * @package ImpressPages
 *
 */


(function($) {
    "use strict";

    var methods = {

        init : function(options) {

            if (typeof(options) === 'undefined') {
                options = {};
            }

            return this.each(function() {

                var $this = $(this);
                var context = $this;

                var data = $this.data('ipFormRepositoryFile');
                if (!data) {
                    $this.find('.ipsSelect').on('click', function (e) {
                        e.preventDefault();
                        var repository = new ipRepository({preview: 'list'});
                        repository.bind('ipRepository.filesSelected', $.proxy(filesSelected, context));
                    })

                    $this.data('ipFormRepositoryFile', {
                        inputName: $this.data('inputname'),
                        limit: $this.data('filelimit')
                    });
                }
            });
        },

        _filesAdded : function(up, files) {
            var $this = $(this);
            $.each(files, function(i, file) {
                var $file = $('#ipModFormFile_' + $this.data('ipFormRepositoryFile').uniqueNumber + '_' + file.id);
                if (!$file.length) {//in some cases _error method creates file record. This line is to avoid adding the same file twice
                    var $newFile = $this.find('.ipmFileTemplate').clone();
                    $newFile.data('fileId', file.id);
                    $newFile.removeClass('ipgHide').removeClass('ipmFileTemplate');
                    $newFile.attr('id', 'ipModFormFile_' + $this.data('ipFormRepositoryFile').uniqueNumber + '_' + file.id);
                    $newFile.find('.ipmFileName').text(file.name);
                    $newFile.find('.ipsRemove').click(function(e){
                        var $this = $(this);
                        var $file = $this.closest('.ipmFile');
                        var fileId = $file.data('fileId');

                        /* Seems that removeFile method is used just for files that are not started to be upload
                        var uploader = $this.closest('.ipsFileContainer').data('ipFormRepositoryFile').uploader;
                        var uploaderFile = uploader.getFile(fileId)
                        uploader.removeFile(uploaderFile);
                        */

                        $file.remove();
                    });
                    $this.find('.ipmFiles').append($newFile);
                }
            });
            up.refresh(); // Reposition Flash/Silverlight
            up.start();
        },

        _fileUploaded : function(up, file, response) {
            var $this = $(this);
            var $file = $('#ipModFormFile_' + $this.data('ipFormRepositoryFile').uniqueNumber + '_' + file.id);
            if (!$file.length) {
                return; //file has been removed by user
            }

            var answer = jQuery.parseJSON(response.response);

            var $fileInput = $('<input class="ipmUploadedData" name="' + $this.data('ipFormRepositoryFile').inputName + '[file][]" type="hidden" value="' + answer.fileName + '" />');
            $file.append($fileInput);
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
                    renameTo : $this.find('.ipsRenameTo').val(),
                    dir : $this.data('dir')
                });
            });
            return files;
        }

    };

    var filesSelected = function (event, files) {
        var $this = this;
        for (var index in files) {
            var $newFile = $this.find('.ipmFileTemplate').clone();
            $newFile.removeClass('ipgHide').removeClass('ipmFileTemplate');
            $newFile.find('.ipmFileName').text(files[index].fileName);
            $newFile.find('.ipsRemove').click(function(e){
                var $this = $(this);
                var $file = $this.closest('.ipmFile');
                $file.remove();
            });
            if ($this.data('ipFormRepositoryFile').limit) {
                if ($this.find('.ipmFiles').children().length + 1 > $this.data('ipFormRepositoryFile').limit) {
                    if ($this.find('.ipmFiles').children().first().length == 1) {
                        $this.find('.ipmFiles').children().first().remove();
                    }
                }
            }

            $this.find('.ipmFiles').append($newFile);
        }
    }


    $.fn.ipFormRepositoryFile = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipFormRepositoryFile');
        }

    };

    $('.ipsModuleForm .ipsRepositoryFileContainer').ipFormRepositoryFile();

})(ip.jQuery);
