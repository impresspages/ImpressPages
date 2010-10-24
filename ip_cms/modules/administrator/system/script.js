
function mod_administrator_system_publish_updates(response){
  var container = document.getElementById('systemInfo');
  var messages = eval('(' + response + ')');
  
  if(messages.length > 0){
    container.style.display = '';
    var i = 0;
    for (i=0; i<messages.length; i++){
      container.innerHTML = container.innerHTML + '<div class="' + messages[i]['type'] + '">' + messages[i]['message'] + '</div>';
    }
  }
}

LibDefault.ajaxMessage(document.location, 'module_name=system&module_group=administrator&action=getSystemInfo', mod_administrator_system_publish_updates);
