<?php

define('CMS', true); // make sure other files are accessed through this file.
define('FRONTEND', true); // make sure other files are accessed through this file.

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', '1');



if(is_file(__DIR__.'/ip_config.php')) {
  require (__DIR__.'/ip_config.php');
} else {
  require (__DIR__.'/../ip_config.php');
}

require (BASE_DIR.INCLUDE_DIR.'db.php');


Db::connect();





$sql = "
SELECT * FROM ".DB_PREF."m_content_management_widget WHERE 1
";

$rs = mysql_query($sql);

if (!$rs) {
    trigger_error($sql.' '.mysql_error());
    exit;
}

while($lock = mysql_fetch_assoc($rs)) {
    switch ($lock['widgetName']) {
        case 'contact_form':
            
            $sqlWidget = "
            	SELECT
            		* 
            	FROM
            		".DB_PREF."mc_misc_contact_form
            	WHERE
            		id = ".(int)$lock['elementId']." 
            ";
            $rsWidget = mysql_query($sqlWidget);
            
            if (!$rsWidget)
            
            $data = array (
                'thankYou' => ,
            )
            break;
        case '':
        
            break;
        case '':
        
            break;
        case '':
        
            break;
            
    }

    
}






Db::disconnect();


echo '<br />FINISH<br />';

