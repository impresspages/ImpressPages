/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license GNU/GPL, see ip_license.html
 */

function ipWidget_IpText(widgetObject) {
    this.widgetObject = widgetObject;
    
    this.prepareData = prepareData;
    this.manageInit = manageInit;

    function manageInit() {
    	
    }
    
	function prepareData () {
		
		var data = Object();
		
		data.text = $(this.widgetObject).find('textarea').first().val();
		$(this.widgetObject).trigger('preparedWidgetData.ipWidget', [data]);
	}	

};

