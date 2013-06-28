/**
 * @package ImpressPages
 *
 *
 */

$(document).ready(function() {

    $ipObject = $(document);

    $ipObject.bind('initFinished.ipContentManagement', ipAdminPanelInit);
    $ipObject.bind('initFinished.ipContentManagement', ipAdminWidgetsScroll);
    $(window).bind('resizeEnd',                        ipAdminWidgetsScroll);
    $ipObject.bind('initFinished.ipContentManagement', ipAdminWidgetsSearch);

    $ipObject.ipContentManagement();

    // case insensitive search
    jQuery.expr[':'].icontains = function(a, i, m) {
        return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
    };
});

$(window).resize(function() {
    if(this.resizeTO) { clearTimeout(this.resizeTO); }
    this.resizeTO = setTimeout(function() {
        $(this).trigger('resizeEnd');
    }, 100);
});



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
    var containerWidth = scrollableAPI.getRoot().width() + 24; // adding left side compensation
    var scrollBy = Math.floor(containerWidth / itemWidth); // define number of items to scroll
    if(scrollBy < 1) { scrollBy = 1; } // setting the minimum
    $('.ipAdminWidgets .ipaRight, .ipAdminWidgets .ipaLeft').unbind('click'); // unbind if reinitiating dynamically
    scrollableAPI.begin(); // move to scroller to default position (beginning)
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
 * Function used to search Widgets on Administration Panel
 * 
 * @param none
 * @returns nothing
 * 
 * 
 */
function ipAdminWidgetsSearch() {
    var $input = $('.ipAdminWidgetsSearch .ipaInput');
    var $button = $('.ipAdminWidgetsSearch .ipaButton');
    var $widgets = $('.ipAdminWidgetsContainer li');

    $input.focus(function(){
        if( this.value == this.defaultValue ){
            this.value = '';
        };
    }).blur(function(){
        if( this.value == '' ){
            this.value = this.defaultValue;
        };
    }).keyup(function(){
        var value = this.value;
        $widgets.css('display',''); // restate visibility
        if (value && value != this.defaultValue ) {
            $widgets.not(':icontains(' + value + ')').css('display','none');
            $button.addClass('ipaClear');
        } else {
            $button.removeClass('ipaClear');
        }
        ipAdminWidgetsScroll(); // reinitiate scrollable
    });

    $button.click(function(event){
        event.preventDefault();
        $this = $(this);
        if ($this.hasClass('ipaClear')) {
            $input.val('').blur().keyup(); // blur returns default value; keyup displays all hidden widgets
            $this.removeClass('ipaClear'); // makes button look default
        }
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


