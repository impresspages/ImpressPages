<?php
/**
 * @package	ImpressPages
 * @copyright	Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\standard\content_management;

class Manager{
     
    function manage(){
        return ('<script type="text/javascript">document.location=\''.BASE_URL.'?cms_action=manage\';</script>');
    }

}

