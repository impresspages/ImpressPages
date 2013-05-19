function ajaxMessage(url, parameters){
    parameters = 'manual=1&' + parameters;
    var xmlHttp;
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
            switch(responseObject.errorCode){
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
                case 'ERROR_LONG_PREFIX':
                    document.getElementById('errorLongPrefix').style.display = 'block';
                    break;
                case 'ERROR_INCORRECT_PREFIX':
                    document.getElementById('errorIncorrectPrefix').style.display = 'block';
                    break;
                case 'ERROR_OK':
                    document.location= 'index.php?step=4';
                    break;
                default:
                    document.getElementById('errorQuery').style.display = 'block';
                    break;
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
    document.getElementById('errorAllFields').style.display = 'none';
    document.getElementById('errorConnect').style.display = 'none';
    document.getElementById('errorDb').style.display = 'none';
    document.getElementById('errorQuery').style.display = 'none';
    document.getElementById('errorLongPrefix').style.display = 'none';
    document.getElementById('errorIncorrectPrefix').style.display = 'none';


    var url = '';
    var server = document.getElementById('db_server').value;
    var user = document.getElementById('db_user').value;
    var pass = document.getElementById('db_pass').value;
    var db = document.getElementById('db_db').value;
    var prefix = document.getElementById('db_prefix').value;
    if(server == '' ||  user == '' || db == '' || prefix == ''){
        document.getElementById('errorAllFields').style.display = 'block';
    }else{
        url = 'action=create_database&server=' + encodeURIComponent(server) + '&db_user=' + encodeURIComponent(user) + '&db_pass=' + encodeURIComponent(pass) + '&db=' + encodeURIComponent(db) + '&prefix=' + encodeURIComponent(prefix);
        ajaxMessage('worker.php', url);
    }
}

$(document).ready(function() {
    $('.button_act').click(function(e){
        e.preventDefault();
        execute_ajax();
    });
});

