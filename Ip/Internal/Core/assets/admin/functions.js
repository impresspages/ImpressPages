


/**
 * show the "browse link" modal, and call callback_function with result
 *
 * @param {Object} callback
 */

function ipBrowseLink(callback) {

    var $ = ip.jQuery,
        $modal = $('#ipBrowseLinkModal'),
        $iframe = $modal.find('.ipsPageSelectIframe');

    $modal.modal();
    var $iframeContent = $iframe.contents();

    $modal.find('.ipsConfirm').on('click', function () {
        var iframeWindow = $iframe.get(0).contentWindow;
        var pageId = iframeWindow.angular.element(iframeWindow.$('.ipAdminPages')).scope().selectedPageId;
        $.ajax({
            type: 'GET',
            url: ip.baseUrl,
            data: {aa: 'Core.getPageUrl', pageId: pageId},
            dataType: 'json',
            success: function (response) {
                callback(response.url);
            },
            error: function (response) {
                if (ip.developmentEnvironment || ip.debugMode) {
                    alert('Server response: ' + response.responseText);
                }
            }
        });
    });
}


function ipBrowseFile(callback, options)
{
    var repository = new ipRepository({preview: 'list'});
    repository.bind('ipRepository.filesSelected', function (event, files) {
        callback(files);
    });
}

