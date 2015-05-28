// defining global variables
var ipRepository;
var ipRepositoryESC;

(function ($) {
    "use strict";

    ipRepository = function (options) {
        if ($('.ipsModuleRepositoryPopup').length) {
            return; //repository window is already open. Do nothing.
        }

        // define options defaults
        options.preview = options.preview || 'list';
        options.filter = options.filter || null;

        if (options.secure) {
            options.preview = 'list';
        }
        if (!options.path) {
            options.path = '';
        }
        if (!options.filter) {
            options.filter = null;
        }
        if (!options.filterExtensions) {
            options.filterExtensions = null;
        }

        $(document.body).append(ipRepositoryHtml);
        var $popup = $('.ipsModuleRepositoryPopup');

        //initialize first tab
        $popup.find('#ipsModuleRepositoryTabUpload').ipRepositoryUploader({secure:options.secure, path:options.path});
        $popup.find('#ipsModuleRepositoryTabUpload').ipRepositoryAll(options);

        // todox: initialize each tab
        $popup.find('.ipsTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var tabHref = $(e.target).attr('href');
            switch (tabHref) {
                case '#ipsModuleRepositoryTabBuy':
                    $popup.find('#ipsModuleRepositoryTabBuy').ipRepositoryBuy();
                    break;
            }
        });

        $popup.bind('ipModuleRepository.confirm', function (e, files) {
            $(this).trigger('ipRepository.filesSelected', [files]);
            $(this).trigger('ipModuleRepository.close');
        });

        $popup.bind('ipModuleRepository.cancel', function (e) {
            $(this).trigger('ipModuleRepository.close');
        });

        $popup.bind('ipModuleRepository.close', function (e) {
            $(document).off('keyup', ipRepositoryESC);
            $('.ipsModuleRepositoryPopup').remove();
            if(!$('.modal[aria-hidden=false]').length) {
                $('body').removeClass('modal-open');
            }
        });

        $popup.find('.ipsClose').hover(function () {
            $(this).addClass('ui-state-hover');
        }, function () {
            $(this).removeClass('ui-state-hover');
        });

        $popup.find('.ipsClose').click(function (e) {
            $(this).trigger('ipModuleRepository.cancel');
            e.preventDefault();
        });

        $(document).on('keyup', ipRepositoryESC);

        $(document.body).addClass('modal-open');

        return $popup;

    };


    ipRepositoryESC = function (e) {
        var $popup = $('.ipsModuleRepositoryPopup');
        if (e.keyCode == 27) {
            $popup.trigger('ipModuleRepository.cancel');
        }
    };

})(jQuery);
