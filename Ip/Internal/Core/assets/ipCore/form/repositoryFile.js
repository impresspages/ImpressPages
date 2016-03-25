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
                var context = $this;

                var data = $this.data('ipFormRepositoryFile');
                if (!data) {
                    $this.find('.ipsSelect').on('click', function (e) {
                        e.preventDefault();
                        var repository = new ipRepository({preview: $this.data('preview'), secure: $this.data('secure'),path: $this.data('path'), filter: $this.data('filter'), filterExtensions: $this.data('filterextensions')});
                        repository.bind('ipRepository.filesSelected', $.proxy(filesSelected, context));
                    });

                    $this.data('ipFormRepositoryFile', {
                        inputName: $this.data('inputname'),
                        limit: parseInt($this.data('filelimit')),
                        preview: parseInt($this.data('preview'))
                    });

                    $this.find('.ipsFiles .ipsFile .ipsRemove').on('click', $.proxy(removeFile, this));

                }
            });
        },


        _getFiles: function () {
            var $this = $(this);
            var files = new Array();
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

    var filesSelected = function (event, files) {
        var $this = this;
        var context = this;

        for (var index in files) {
            var fileName = files[index].fileName;
            var $newFile = $this.find('.ipsFileTemplate').clone();
            $this.find('.ipsFileTemplate input').change(); //to make js on change event to work
            $newFile.removeClass('hidden').removeClass('ipsFileTemplate');

            $newFile.find('.ipsLink').text(fileName);
            $newFile.find('.ipsLink').attr('href', files[index].originalUrl);
            $newFile.find('input').val(fileName).attr('name', $this.data('ipFormRepositoryFile').inputName + '[]');
            $newFile.find('.ipsRemove').click($.proxy(removeFile, context));
            if ($this.data('ipFormRepositoryFile').limit > 0) {
                if ($this.find('.ipsFiles').children().length + 1 > $this.data('ipFormRepositoryFile').limit) {
                    if ($this.find('.ipsFiles').children().first().length === 1) {
                        $this.find('.ipsFiles').children().first().remove();
                    }
                }
            }
            $this.find('.ipsFiles').append($newFile);
            $this.trigger('ipFieldFileAdded', [fileName]);
        }
    };

    var removeFile = function (e) {
        var $this = $(this);
        var $currentTarget = $(e.currentTarget);
        var $file = $currentTarget.closest('.ipsFile');
        $this.find('.ipsFileTemplate input').change(); //to make js on change event to work
        $file.remove();
        $this.trigger('ipFieldFileRemoved');
    };


    $.fn.ipFormRepositoryFile = function (method) {
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
