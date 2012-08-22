
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
            container.innerHTML = container.innerHTML + ' <a target="_blank" class="button" href="' + messages[i]['downloadUrl'] + '">Download</a> <a class="button actStartUpdate" href="' + messages[i]['downloadUrl'] + '">Start update</a><br/><br/>';
        }
        container.innerHTML = container.innerHTML + '<div class="clear"></div>';
      }
    }
  }
  
}

LibDefault.ajaxMessage(document.location, 'module_name=system&module_group=administrator&action=getSystemInfo', mod_administrator_system_publish_updates);


$('.actStartUpdate').live('click', startUpdate);

function startUpdate(e)
{
    console.log('startupdate');
    e.preventDefault();

    var postData = Object();
    postData.g = 'administrator';
    postData.m = 'system';
    postData.a = 'startUpdate';

    $.ajax({
        url: BASE_URL,
        data: postData,
        dataType: 'json',
        type : 'POST',
        success: function (response){
            if (!response) {
                return;
            }
            if (response.status && response.status == 'success') {
                console.log('success');
            } else {
                if (response.error) {
                    alert(response.error);
                }
            }
        },
        error: function () {
            alert('Unknown error. Please see logs.');
        }
    });

}