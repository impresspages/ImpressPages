<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Modules\standard\content_management;

class Manager{
     
    function manage(){
        return ('<script type="text/javascript">document.location=\''.BASE_URL.'?cms_action=manage\';</script>');
    }

}

