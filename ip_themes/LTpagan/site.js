$(document).ready(function() {
    $('.ipWidget-IpImageGallery li a, .ipWidget-IpImage a').colorbox({
        rel:'ipwImage',
        maxWidth:'90%',
        maxHeight:'90%'
    });
    $('.topmenu').stickyMenu('sticky'); // adding 'sticky' class to 'topmenu' when it becomes invisible
});

(function($, window) {

    /**
     * jQuery plugin - https://github.com/gneatgeek/stickymenu
     *
     * @namespace fn.stickyMenu
     * @method stickyMenu
     * @param {Object} cname - Class name to append to the object
     * @return {Object} this - Maintains Chainability
     */
    $.fn.stickyMenu = function(cname) {
        var pinned,
            menu = this,
            start = menu.offset().top;

        if ( !cname )
            cname = 'sticky';

        $(window).bind('scroll.stickymenu-' + menu.attr('id'), function() {
            if ( pinned ) {
                if ( $(this).scrollTop() <= start ) {
                    menu.next().remove(); // custom: removing fake element
                    menu.css('width',''); // custom: removing width definition
                    menu.toggleClass(cname);
                    pinned = false;
                }
            } else if ( $(this).scrollTop() > start ) {
                menu.after('<div style="height: '+menu.height()+'px; width: '+menu.width()+'px;"></div>'); // custom: creating fake element in same size
                menu.css('width',menu.width()+'px'); // custom: keeping same width
                menu.toggleClass(cname);
                pinned = true;
            }
        });

        return this;
    };
})(jQuery, window);
