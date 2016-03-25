/**
 * Font Selector - jQuery plugin 0.1
 *
 * Copyright (c) 2012 Chris Dyer
 *
 * Modified by Mangirdas Skripka 2012-08-07
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following
 * conditions are met:
 *
 * Redistributions of source code must retain the above copyright notice, this list of conditions and the following
 * disclaimer. Redistributions in binary form must reproduce the above copyright notice, this list of conditions
 * and the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING,
 * BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO
 * EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS;
 * OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
 * SUCH DAMAGE.
 *
 */

jQuery.fn.ipInlineManagementFontSelector = function (options) {

    if (options == 'getFont') {
        var root = $(this);
        if (!root.attr('style')) {
            //deafult option selected;
            return null
        }
        return root.css('font-family');
    }

    var settings = $.extend({
        'hide_fallbacks': false,
        'selected': function (style) {
        },
        'initial': ''
    }, options);

    return this.each(function () {

        var root = $(this);
        var ul = $(this).find('ul');
        ul.hide();
        var visible = false;

        if (settings['initial'] != '') {
            var curFontExists = false;
            var initialBaseFont = $.trim(settings['initial'].substr(0, settings['initial'].indexOf(',')).replace(/\'/g, ''));
            root.find('li a').each(function () {
                var font = $(this).text();
                var baseFont = $.trim(font.substr(0, font.indexOf(',')).replace(/\'/g, ''));
                if (baseFont.toLowerCase() == initialBaseFont.toLowerCase()) {
                    curFontExists = true;
                }
            });
            if (!curFontExists) {
                var defaultFontLabel = root.find('.ipsDefaultFont').html();
                root.find('.ipsFontName').html(defaultFontLabel.substr(0, defaultFontLabel.indexOf(',')));
                root.css('font-family', root.find('.ipsDefaultFont').css('font-family'));
            } else {
                root.find('.ipsFontName').html(initialBaseFont);
                root.css('font-family', settings['initial']);
            }
        }

        ul.find('li a').each(function () {
            $(this).css("font-family", $(this).text());

            if (settings['hide_fallbacks']) {
                var content = $(this).text();
                $(this).text(content.substr(0, content.indexOf(',')));
            }
        });

        ul.find('li a').click(function () {

            if (!visible)
                return;

            ul.slideUp('fast', function () {
                visible = false;
            });

            root.find('.ipsFontName').html($(this).text());
            root.css('font-family', $(this).css('font-family'));

            if ($(this).hasClass('ipsDefaultFont')) {
                settings['selected']('');
                root.css('font-family', '');
            } else {
                settings['selected']($(this).css('font-family'));
            }
        });

        $(this).click(function (event) {

            if (visible)
                return;

            event.stopPropagation();

            ul.slideDown('fast', function () {
                visible = true;
            });
        });

        $('html').click(function () {
            if (visible) {
                ul.slideUp('fast', function () {
                    visible = false;
                });
            }
        })
    });
};

