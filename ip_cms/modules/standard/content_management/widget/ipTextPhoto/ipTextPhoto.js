/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

function ipWidget_ipTextPhoto(widgetObject) {
    this.widgetObject = widgetObject;
    
    this.prepareData = prepareData;
    this.manageInit = manageInit;
    this.uploadPhoto = uploadPhoto;

    function manageInit() {
        $('.ipWidget_ipTextPhoto_uploadPhoto').bind('click', this.uploadPhoto);
    }
    
    
    
	function prepareData () {
		console.log('saving');
		
		var data = Object();
		
		data.text = $(this.widgetObject).find('textarea').first().val();
		console.log(this.widgetObject);
		$(this.widgetObject).trigger('preparedWidgetData.ipWidget', [data]);
	}	
	
	function uploadPhoto () {
	    
	}

};

