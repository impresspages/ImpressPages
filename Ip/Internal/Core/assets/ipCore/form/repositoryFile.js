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

                    $this.find('.ipsFiles .ipsFile .ipsRemove').on('click', removeFile);

                }
            });
        },


        _getFiles : function () {
            var $this = $(this);
            var files = new Array();
            $this.find('.ipsFiles div').each(function(){

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
            var fileName = files[index].fileName;
            var $newFile = $this.find('.ipsFileTemplate').clone();
            $newFile.removeClass('hidden').removeClass('ipsFileTemplate');
            $newFile.find('.ipsFileName').text(fileName);
            $newFile.find('input').val(fileName).attr('name', $this.data('ipFormRepositoryFile').inputName + '[]');
            $newFile.find('.ipsRemove').click(removeFile);
            if ($this.data('ipFormRepositoryFile').limit) {
                if ($this.find('.ipsFiles').children().length + 1 > $this.data('ipFormRepositoryFile').limit) {
                    if ($this.find('.ipsFiles').children().first().length == 1) {
                        $this.find('.ipsFiles').children().first().remove();
                    }
                }
            }
            $this.find('.ipsFiles').append($newFile);

        }
    }

    var removeFile = function(e){
        var $this = $(this);
        var $file = $this.closest('.ipsFile');
        $file.remove();
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
//
//    $('.ipsModuleFormAdmin .ipsRepositoryFileContainer').ipFormRepositoryFile();

})(jQuery);
