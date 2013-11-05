function ajaxMessage(url, parameters){
    var xmlHttp;
    parameters = 'manual=1&' + parameters;

    try	{// Firefox, Opera 8.0+, Safari
        xmlHttp=new XMLHttpRequest();
    }catch (e){// Internet Explorer
        try{
            xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
        }catch (e){
            try{
                xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (e){
                alert("Your browser does not support AJAX!");
                return false;
            }
        }
    }
    xmlHttp.onreadystatechange=function()
    {

        if(xmlHttp.readyState==4){
            var response = xmlHttp.responseText;
            if(response != ''){
            var responseObject = eval('(' + response + ')');
            if (responseObject.result) {
                document.location = 'index.php?step=5';
            } else {
                var responseArray = responseObject.error.message.split(' ');

                for (var i in responseArray) {
                    var response = responseArray[i];
                    switch(response){
                        case 'ERROR_SITE_NAME':
                            document.getElementById('errorSiteName').style.display = 'block';
                            break;
                        case 'ERROR_SITE_EMAIL':
                            document.getElementById('errorSiteEmail').style.display = 'block';
                            break;
                        case 'ERROR_EMAIL':
                            document.getElementById('errorEmail').style.display = 'block';
                            break;
                        case 'ERROR_CONFIG':
                            document.getElementById('errorConfig').style.display = 'block';
                            break;
                        case 'ERROR_ROBOTS':
                            document.getElementById('errorRobots').style.display = 'block';
                            break;
                        case 'ERROR_CONNECT':
                            document.getElementById('errorConnect').style.display = 'block';
                            break;
                        case 'ERROR_DB':
                            document.getElementById('errorDb').style.display = 'block';
                            break;
                        case 'ERROR_QUERY':
                            var textNode = document.createTextNode(responseObject.error);
                            document.getElementById('errorQuery').innerHTML = '';
                            document.getElementById('errorQuery').appendChild(textNode);
                            document.getElementById('errorQuery').innerHTML = '<p class="error">' + document.getElementById('errorQuery').innerHTML + '</p>';
                            document.getElementById('errorQuery').style.display = 'block';
                            break;
                        case 'ERROR_LOGIN':
                            document.getElementById('errorLogin').style.display = 'block';
                            break;
                        case 'ERROR_TIME_ZONE':
                            document.getElementById('errorTimeZone').style.display = 'block';
                            break;
                        default:
                            alert('Server gave no answer');
                            break;
                    }

                }
            }
        }

    }
}

xmlHttp.open("POST",url, true);
xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xmlHttp.setRequestHeader("Content-length", parameters.length);
xmlHttp.setRequestHeader("Connection", "close");
xmlHttp.send(parameters);
}


function execute_ajax(){
    document.getElementById('errorSiteName').style.display = 'none';
    document.getElementById('errorSiteEmail').style.display = 'none';
    document.getElementById('errorEmail').style.display = 'none';
    document.getElementById('errorConfig').style.display = 'none';
    document.getElementById('errorRobots').style.display = 'none';
    document.getElementById('errorConnect').style.display = 'none';
    document.getElementById('errorDb').style.display = 'none';
    document.getElementById('errorQuery').style.display = 'none';
    document.getElementById('errorLogin').style.display = 'none';
    document.getElementById('errorTimeZone').style.display = 'none';


    var url = '';
    var site_name = document.getElementById('config_site_name').value;
    var site_email = document.getElementById('config_site_email').value;
    var login = document.getElementById('config_login').value;
    var pass = document.getElementById('config_pass').value;
    var email = document.getElementById('config_email').value;
    var timezone = document.getElementById('config_timezone').value;
    {
        url = 'a=config&install_login=' + encodeURIComponent(login) + '&install_pass=' + encodeURIComponent(pass) + '&email=' + encodeURIComponent(email) + '&timezone=' + encodeURIComponent(timezone) +'&site_name=' + encodeURIComponent(site_name) + '&site_email=' + encodeURIComponent(site_email);
        ajaxMessage('index.php', url);
    }
}


$(document).ready(function() {
    $('.button_act').click(function(e){
        e.preventDefault();
        execute_ajax();
    });
});

