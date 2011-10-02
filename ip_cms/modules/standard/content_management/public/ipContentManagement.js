/**
 * @package ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

$(document).ready(function() {

	$ipObject = $(document);

	$ipObject.bind('initFinished.ipContentManagement', ipInitBlocks);
	

    $ipObject.ipContentManagement();

});

function ipInitBlocks(event, options) {
    if (options.manageableRevision) {
        $('.ipBlock').ipBlock(options);
        $ipObject.bind('pageSaveStart.ipContentManagement', ipPageSaveStart);
    }
}

function ipPageSaveStart (event) {
    $('.ipBlock').ipBlock('pageSaveStart');
}

/**
 * 
 * Object used to store active job in page save progress
 * 
 * @param string name name of the job
 * @param int timeLeft predicted execution time in secconds 
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
	this.timeLeft = timeLeft; //secconds. Approximate value
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

	function setTitle (title) {
		this.title = title;
	}
	
	function setProgress (progress) {
		if (progress > 1) {
			progress = 1;
		}
		if (progress < 0) {
			progress = 0;
		}
		this.progress = progress;
	}
	
	function setTimeLeft (timeLeft) {
		if (timeLeft < 0) {
			timeLeft = 0;
		}
		this.timeLeft = timeLeft;		
	}
	
	function setFinished (finished) {
		this.finished = finished;
		this.setTimeLeft (0);
		this.setProgress (100);
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
	


	function getFinished () {
		return this.finished;
	}
		
}
