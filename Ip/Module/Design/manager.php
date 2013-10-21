<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Module\Design;

class Manager{

    function manage(){
        return ('<script type="text/javascript">document.location=\''.BASE_URL.'?g=standard&m=design&aa=index\';</script>');
    }

}

