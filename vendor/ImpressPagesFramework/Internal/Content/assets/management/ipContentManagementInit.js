/**
 * @package ImpressPages
 *
 */




function replacePublishedToPublish() {
    "use strict";
    var $publishButton = $('.ipsContentPublish');
    $publishButton.text(ipPublishTranslation);
    $publishButton.removeClass('btn-default').addClass('btn-warning');

    var $revisionsButton = $('.ipsContentRevisions');
    $revisionsButton.removeClass('btn-default').addClass('btn-warning');
}

$(document).ready(function () {
    "use strict";

    $(document).ipContentManagement();

    //preinit TinyMCE. Without it edit focus doesn't work after adding a widget
    var $emptyDiv = $('<div contenteditable="true" style="display: none"></div>');
    $('body').append($emptyDiv);
    $emptyDiv.tinymce(ipTinyMceConfig());
    setTimeout(function () {
        $emptyDiv.remove();
    }, 10000);

    $(document).on('ipWidgetAdded', replacePublishedToPublish);
    $(document).on('ipWidgetDeleted', replacePublishedToPublish);
    $(document).on('ipWidgetMoved', replacePublishedToPublish);
    $(document).on('ipWidgetSaved', replacePublishedToPublish);


});
