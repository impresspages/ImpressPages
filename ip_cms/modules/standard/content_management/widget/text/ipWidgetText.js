$(document).ready(function() {
	
//    $('.ipWidget_text').live('ipInitManagement', ipWidgetTextInitManagment);
//    $('.ipWidget_text').live('ipSave', ipWidget_text_save);
//    $('.ipWidget_text').live('ipclick', function(event, parameters){console.log(event);	console.log(parameters); alert('testClick2');});
//	$('.ipWidget_text').live('save.ipWidget', ipWidgetTextSave);
});


function ipWidget_text(widgetObject) {
    this.widgetObject = widgetObject;
    
    this.save = save;
    this.manageInit = manageInit;
    this.saveResponse = saveResponse;

    function manageInit() {
    	
    }
    
	function save () {
		console.log('saving');
		
//	    data = Object();
//		data.g = 'standard';
//		data.m = 'content_management';
//		data.a = 'saveWidget';
//		data.widgetId = $(this.widgetObject).data('ipWidget').id;
//		data.text = $(this.widgetObject).find('textarea').first().val();
//
//		$.ajax( {
//			type : 'POST',
//			url : ipBaseUrl,
//			data : data,
//			context : this,
//			success : this.saveResponse,
//			dataType : 'json'
//		});                	
  			
	}
	
	function saveResponse (response) {
		console.log('saveResponse');
		console.log($(this.widgetObject).data('ipWidget').id);
		$(this.widgetObject).trigger('saveSuccess.ipWidget');
		
	}
};

