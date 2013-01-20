


"use strict";



(function($) {

    var methods = {

        init: function(options) {

            return this.each(function() {
                var $this = $(this);

                $this.data('ipRepositoryBuyPreview', {
                    accountId: options.accountId,
                    previewImageUrl: options.previewImageUrl
                });

                $this.find('.ipmPreviewLoaded').hide();
                $this.find('.ipmPreviewLoading').show();

                $this.find('.ipmPreview').attr('src', options.previewImageUrl);

                $.proxy(methods._center, this)();

                $this.show();

                var $preloadImg = $('<img>');
                $preloadImg[0].src = options.previewImageUrl;
                $preloadImg.bind('load', $.proxy(methods._previewLoaded, this));

            });

        },

        _previewLoaded: function() {
            var $this = $(this);
            var data = $this.data('ipRepositoryBuyPreview');
            $this.find('.ipmPreviewLoaded').show();
            $this.find('.ipmPreviewLoading').hide();
            $this.find('.ipmPreviewImage').attr('src', data.previewImageUrl);
            $this.css("top", 0);
            $this.css("left", 0);
            $.proxy(methods._center, this)();
        },


        _center: function() {
            var $this = $(this);
            $this.css("position","absolute");
            $this.css("top", Math.max(0, (($(window).height() - $this.outerHeight()) / 2)) + "px"); // + $(window).scrollTop())
            $this.css("left", Math.max(0, (($(window).width() - $this.outerWidth()) / 2)) + "px"); // $(window).scrollLeft()) +
        }



    };




$.fn.ipRepositoryBuyPreview = function(method) {
    if (methods[method]) {
        return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    } else if (typeof method === 'object' || !method) {
        return methods.init.apply(this, arguments);
    } else {
        $.error('Method ' + method + ' does not exist on jQuery.ipRepositoryRecent');
    }

};

})(jQuery);
