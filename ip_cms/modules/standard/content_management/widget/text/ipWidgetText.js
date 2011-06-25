$(document).ready(function() {
	
    $('.ipWidget_text').live('ipInitManagement', ipWidgetTextInitManagment);
    $('.ipWidget_text').live('ipSave', ipWidgetTextSave);
    $('.ipWidget_text').live('click', function(){alert('testClick');});
	
});


function ipWidgetTextInitManagment(event, widgetId){
	alert(widgetId);
	
}

function ipWidgetTextSave(event){
	console.log(event);
	alert('save');
}