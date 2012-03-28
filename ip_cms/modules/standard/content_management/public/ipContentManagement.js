/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */

$(document).ready(function() {

    $ipObject = $(document);

    $ipObject.bind('initFinished.ipContentManagement', ipInitBlocks);
    $ipObject.bind('initFinished.ipContentManagement', ipAdminPanelInit);
    $ipObject.bind('initFinished.ipContentManagement', ipAdminWidgetsScroll);

    $ipObject.ipContentManagement();

});

function ipInitBlocks(event, options) {
    if (options.manageableRevision) {
        $('.ipBlock').ipBlock(options);
        $ipObject.bind('pageSaveStart.ipContentManagement', ipActionSaveStart);
    }
}

function ipActionSaveStart(event) {
    $('.ipBlock').ipBlock('pageSaveStart');
}

/**
 * 
 * Function used to paginate Widgets on Administration Panel
 * 
 * @param none
 * @returns nothing
 * 
 * 
 */
function ipAdminWidgetsScroll() {
    var $scrollable = $('.ipAdminWidgetsContainer'); // binding object
    $scrollable.scrollable({
        items: 'li' // items are <li> elements; on scroll styles will be added to <ul>
    });
    var scrollableAPI = $scrollable.data('scrollable'); // getting instance API
    var itemWidth = scrollableAPI.getItems().eq(0).outerWidth(true);
    var containerWidth = scrollableAPI.getRoot().width();
    var scrollBy = Math.floor(containerWidth / itemWidth); // define number of items to scroll
    if(scrollBy < 1) { scrollBy = 1; } // setting the minimum
    $('.ipAdminWidgets .ipaRight').click(function(event){
        event.preventDefault();
        scrollableAPI.move(scrollBy);
    });
    $('.ipAdminWidgets .ipaLeft').click(function(event){
        event.preventDefault();
        scrollableAPI.move(-scrollBy);
    });
}

/**
 * 
 * Function used to create a space on a page for Administration Panel
 * 
 * @param none
 * @returns nothing
 * 
 * 
 */
function ipAdminPanelInit() {
    $container = $('.ipAdminPanelContainer'); // the most top element physically creates a space
    $panel = $('.ipAdminPanel'); // Administration Panel that stays always visible
    $container.height($panel.height()); // setting the height to container
}

/**
 * 
 * Object used to store active job in page save progress
 * 
 * @param string
 *            name name of the job
 * @param int
 *            timeLeft predicted execution time in secconds
 * @returns {ipSaveJob}
 * 
 * 
 */
function ipSaveJob(title, timeLeft) {

    var title;
    var predictedTime;
    var progress;
    var finished;

    this.title = title;
    this.timeLeft = timeLeft; // secconds. Approximate value
    this.progress = 0; // 0 - 1
    this.finished = false;

    this.setTitle = setTitle;
    this.setProgress = setProgress;
    this.setTimeLeft = setTimeLeft;
    this.setFinished = setFinished;
    this.getTitle = getTitle;
    this.getProgress = getProgress;
    this.getTimeLeft = getTimeLeft;
    this.getFinished = getFinished;

    function setTitle(title) {
        this.title = title;
    }

    function setProgress(progress) {
        if (progress > 1) {
            progress = 1;
        }
        if (progress < 0) {
            progress = 0;
        }
        this.progress = progress;
    }

    function setTimeLeft(timeLeft) {
        if (timeLeft < 0) {
            timeLeft = 0;
        }
        this.timeLeft = timeLeft;
    }

    function setFinished(finished) {
        this.finished = finished;
        this.setTimeLeft(0);
        this.setProgress(100);
    }

    function getTitle() {
        return this.title;
    }

    function getProgress() {
        return this.progress;
    }

    function getTimeLeft() {
        return this.timeLeft;
    }

    function getFinished() {
        return this.finished;
    }

}
