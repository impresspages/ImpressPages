<?php
/**
 * @package		ImpressPages
 * @copyright	Copyright (C) 2011 JSC Apro media.
 * @license		GNU/GPL, see ip_license.html
 */
namespace Modules\standard\content_management; 
if (!defined('BACKEND')) exit;
 
require_once(__DIR__.'/db.php');

global $db_module;

class Manager{
   
   function manage(){
        return ('<script type="text/javascript">document.location=\''.BASE_URL.'?cms_action=manage\';</script>');
   }

}

