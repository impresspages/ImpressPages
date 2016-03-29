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
                if (ip.developmentEnvironment || ip.debugMode) {
                    alert('Server response: ' + response.responseText);
                }
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

