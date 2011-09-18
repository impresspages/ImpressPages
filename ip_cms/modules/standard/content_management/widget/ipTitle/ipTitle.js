/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

function ipWidget_title(widgetObject) {
    this.widgetObject = widgetObject;
    this.prepareData = prepareData;
    this.manageInit = manageInit;

    function manageInit() {
    	
    }
    
	function prepareData () {
		var data = Object();
		//data.title = $(this.widgetObject).find('input').val();
		data.title = $(this.widgetObject).find('input[name="title"]').first().val();
		
		//$(this.widgetObject).find('input').hide();
		//this.widgetObject.hide();
		$(this.widgetObject).trigger('preparedWidgetData.ipWidget', [data]);
	}	

};

