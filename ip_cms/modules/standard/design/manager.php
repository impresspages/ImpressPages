<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Modules\standard\design;

class Manager{

    function manage(){
        return ('<script type="text/javascript">document.location=\''.BASE_URL.'?g=standard&m=design&ba=index\';</script>');
    }

}

