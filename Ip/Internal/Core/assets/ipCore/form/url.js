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
                var $input = $this.find('input');

                var data = $this.data('ipFormUrl');
                if (!data) {

                    $this.data('ipFormUrl', {initialized: 1});

                    $this.find('.ipsBrowse').on('click', function () {
                        var $$this = $(this);

                        // searching for parent modal
                        var $modal = $$this.closest('.modal');
                        var isInModal = $modal.length ? true : false;

                        // if action is in modal, we're hiding it
                        if (isInModal) {
                            $modal.modal('hide');
                        }

                        ipBrowseLink(function (link) {
                            if (link) {
                                $input.val(link).change();
                            }

                            // if action is in modal, we're need to reopen it
                            if (isInModal) {
                                $modal.modal('show');
                            }
                        });
                    });

                }
            });
        }
    };


    $.fn.ipFormUrl = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.ipFormRepositoryFile');
        }

    };

    $('.ipsModuleFormAdmin .type-url').ipFormUrl();

})(jQuery);




