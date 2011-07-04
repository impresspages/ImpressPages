$(document).ready(function() {
	
    $('.ipWidget_text').live('ipInitManagement', ipWidgetTextInitManagment);
    $('.ipWidget_text').live('ipSave', ipWidget_text_save);
    $('.ipWidget_text').live('ipclick', function(event, parameters){console.log(event);	console.log(parameters); alert('testClick2');});
	
});


function ipWidgetTextInitManagment(event, widgetId){
	alert(widgetId);
	
}

function ipWidget_text_initManagement(widget){
	alert('initManagement');
}


function ipWidget_text_save(widget, forced){
	var data = Object();
	alert('good');
	console.log(forced);
	//data.text = $(event.currentTarget).find('textarea').val();
	
	return data;
}
function ipWidgetTextSave(event){
	
	console.log(event);
	
	alert($(event.currentTarget).find('textarea').val());
	
	
//    data = Object();
//    data.g = 'standard';
//    data.m = 'content_management';
//    data.a = 'updateWidget';
//
//    $.ajax({
//        type : 'POST',
//        url : ipBaseUrl,
//        data : data,
//        success : ipInitManagementResponse,
//        dataType : 'json'
//    });
//    

	alert('save');
}