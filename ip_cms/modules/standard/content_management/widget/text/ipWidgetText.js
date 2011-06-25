$(document).ready(function() {
	
    $('.ipWidget_text').live('ipInitManagement', ipWidgetTextInitManagment);
    $('.ipWidget_text').live('ipSave', function(event, message1, message2){alert('test'); alert(message1)});
    $('.ipWidget_text').live('click', function(){alert('testClick');});
	
});


function ipWidgetTextInitManagment(event, widgetId){
	alert(widgetId);
	
}