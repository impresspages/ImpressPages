
function mod_administrator_system_publish_updates(response){
  var container = document.getElementById('systemInfo');
  var messages = '';
  if(response != '') {
    messages = eval('(' + response + ')');
    if(messages.length > 0){
      container.style.display = '';
      var i = 0;
      for (i=0; i<messages.length; i++){
        container.innerHTML = container.innerHTML + '<div class="' + messages[i]['type'] + '">' + messages[i]['message']+'</div>';
        
        if (messages[i]['code'] == 'update') {
            container.innerHTML = container.innerHTML + ' <a target="_blank" class="button" href="' + messages[i]['downloadUrl'] + '">Download</a> <a class="button actStartUpdate" href="">Start update</a>';
        }
        container.innerHTML = container.innerHTML + '<div class="clear"></div>';
      }
    }
  }
  
}

LibDefault.ajaxMessage(document.location, 'module_name=system&module_group=administrator&action=getSystemInfo', mod_administrator_system_publish_updates);
