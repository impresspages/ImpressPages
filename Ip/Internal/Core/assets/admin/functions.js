/**
 * show the "browse link" modal, and call callback_function with result
 *
 * @param {Object} callback
 */

function ipBrowseLink(callback) {

    var selectedPageId = null;
    var $modal = $('#ipBrowseLinkModal'),
        $iframe = $modal.find('.ipsPageSelectIframe');

    $iframe.attr('src', $iframe.data('source'));

    $modal.modal();
    var $iframeContent = $iframe.contents();

    $modal.find('.ipsConfirm').on('click', function () {
        var iframeWindow = $iframe.get(0).contentWindow;
        selectedPageId = iframeWindow.angular.element(iframeWindow.$('.ipAdminPages')).scope().selectedPageId;
        $modal.modal('hide');
    });

    $modal.off('hide.bs.modal').on('hide.bs.modal', function () {
        if (!selectedPageId) {
            callback('');
            return;
        }

        //page selected. Get the URL
        $.ajax({
            type: 'GET',
            url: ip.baseUrl,
            data: {aa: 'Pages.getPageUrl', pageId: selectedPageId},
            dataType: 'json',
            success: function (response) {
                callback(response.pageUrl);
            },
            error: function (response) {
                ipAdminNotify.ajaxError(response);
                callback('');
            }
        });
    });


}


function ipBrowseFile(callback, options) {
    if (typeof options === 'undefined') {
        options = {};
    }
    var repository = new ipRepository(options);
    repository.bind('ipRepository.filesSelected', function (event, files) {
        if (callback) {
            callback(files);
        }
    });
}



/**
 * Short status messages for admin actions: "Saving...", "Saved", "Couldn't save".
 * Success messages hide themselves, errors stay until closed.
 * Messages are announced to screen readers through an aria-live region.
 */
var ipAdminNotify = new function () {
    "use strict";
    var $container = null,
        hideTimer = null;

    var t = function (key) {
        if (typeof (ipAdminNotifyTranslations) !== 'undefined' && ipAdminNotifyTranslations[key]) {
            return ipAdminNotifyTranslations[key];
        }
        return key;
    };

    var getContainer = function () {
        if (!$container || !$.contains(document.body, $container[0])) {
            $container = $('<div class="ipAdminNotify" role="status" aria-live="polite"></div>');
            $('body').append($container);
        }
        return $container;
    };

    var show = function (type, text) {
        var $box = getContainer();
        clearTimeout(hideTimer);
        $box.attr('role', type === 'error' ? 'alert' : 'status');
        var $message = $('<div class="_message"></div>').addClass('_' + type);
        $message.append($('<span class="_text"></span>').text(text));
        if (type === 'error') {
            var $close = $('<button type="button" class="_close">&times;</button>').attr('aria-label', t('Close'));
            $close.on('click', function () {
                $box.empty();
            });
            $message.append($close);
        }
        $box.empty().append($message);
        if (type === 'saved') {
            hideTimer = setTimeout(function () {
                $box.empty();
            }, 3500);
        }
    };

    this.saving = function (text) {
        show('saving', text || t('Saving...'));
    };

    this.saved = function (text) {
        show('saved', text || t('Saved'));
    };

    this.error = function (text) {
        show('error', text || t("Couldn't save. Please try again."));
    };

    /**
     * Show a readable message for a failed jQuery ajax request.
     */
    this.ajaxError = function (response) {
        if (response && (ip.developmentEnvironment || ip.debugMode) && window.console) {
            console.log(response.responseText);
        }
        if (response && response.readyState === 0) {
            this.error(t("Couldn't reach the server. Check your connection and try again."));
        } else if (response && (response.status === 401 || response.status === 403)) {
            this.error(t('Your session may have expired. Reload the page and log in again.'));
        } else {
            this.error();
        }
    };

    this.hide = function () {
        if ($container) {
            $container.empty();
        }
    };

    /**
     * Show a "saved" message on the next page load, for actions that reload the page.
     */
    this.savedAfterReload = function (text) {
        try {
            window.sessionStorage.setItem('ipAdminNotifySaved', text || t('Saved'));
        } catch (e) {
            // storage unavailable, the message is simply skipped
        }
    };

    var showMessageFromPreviousPage = function () {
        var text = null;
        try {
            text = window.sessionStorage.getItem('ipAdminNotifySaved');
            window.sessionStorage.removeItem('ipAdminNotifySaved');
        } catch (e) {
            return;
        }
        if (text) {
            ipAdminNotify.saved(text);
        }
    };
    $(showMessageFromPreviousPage);
};

/**
 * Ask before throwing away unsaved changes. Returns true if it is fine to continue.
 */
function ipAdminConfirmDiscard() {
    "use strict";
    var text = 'You have unsaved changes. Leave without saving?';
    if (typeof (ipAdminNotifyTranslations) !== 'undefined' && ipAdminNotifyTranslations[text]) {
        text = ipAdminNotifyTranslations[text];
    }
    return confirm(text);
}

// Ask before a dialog with edited fields is closed by ×, Cancel, Escape or a click outside it.
$(document).on('show.bs.modal', '.modal', function (e) {
    "use strict";
    if (e.target === this) {
        $(this).data('ipModalDirty', false).data('ipModalSaving', false);
    }
});
$(document).on('input change', '.modal input, .modal select, .modal textarea', function (e) {
    "use strict";
    if (e.originalEvent) {
        $(this).closest('.modal').data('ipModalDirty', true).data('ipModalSaving', false);
    }
});
// Capture phase, so the flag is set before the dialog's own handlers close it.
(function () {
    "use strict";
    var markSaving = function (e) {
        var $target = $(e.target);
        if (e.type === 'click') {
            $target = $target.closest('.ipsConfirm, .ipsSave, .ipsAdd, .btn-primary');
        }
        if ($target.length) {
            $target.closest('.modal').data('ipModalSaving', true);
        }
    };
    document.addEventListener('click', markSaving, true);
    document.addEventListener('submit', markSaving, true);
})();
$(document).on('hide.bs.modal', '.modal', function (e) {
    "use strict";
    var $modal = $(this);
    if (e.target !== this || !$modal.data('ipModalDirty') || $modal.data('ipModalSaving')) {
        return;
    }
    if (ipAdminConfirmDiscard()) {
        $modal.data('ipModalDirty', false);
    } else {
        e.preventDefault();
    }
});

/**
 * Make collapsible form section titles (<legend data-toggle="collapse">) work with the keyboard
 * and tell screen readers whether the section is open.
 */
function ipAdminInitCollapsibleLegends($context) {
    "use strict";
    $context.find('legend[data-toggle=collapse]').each(function () {
        var $legend = $(this);
        var $target = $($legend.attr('data-target'));
        $legend
            .attr('role', 'button')
            .attr('tabindex', '0')
            .attr('aria-expanded', $target.hasClass('in') ? 'true' : 'false')
            .attr('aria-controls', $target.attr('id'))
            .off('keydown.ipCollapsible').on('keydown.ipCollapsible', function (e) {
                if (e.keyCode === 13 || e.keyCode === 32) {
                    e.preventDefault();
                    $legend.click();
                }
            });
        $target
            .off('shown.bs.collapse.ipCollapsible').on('shown.bs.collapse.ipCollapsible', function () {
                $legend.attr('aria-expanded', 'true');
            })
            .off('hidden.bs.collapse.ipCollapsible').on('hidden.bs.collapse.ipCollapsible', function () {
                $legend.attr('aria-expanded', 'false');
            });
    });
}
