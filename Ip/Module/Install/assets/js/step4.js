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
        url = 'a=writeConfig&install_login=' + encodeURIComponent(login) + '&install_pass=' + encodeURIComponent(pass) + '&email=' + encodeURIComponent(email) + '&timezone=' + encodeURIComponent(timezone) +'&site_name=' + encodeURIComponent(site_name) + '&site_email=' + encodeURIComponent(site_email);
        ajaxMessage('index.php', url);
    }
}


$(document).ready(function() {
    $('.button_act').click(function(e){
        e.preventDefault();
        ModuleInstall.step4Click();
    });
});

