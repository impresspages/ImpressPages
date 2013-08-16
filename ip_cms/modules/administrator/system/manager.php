<?php

/**
 * @package ImpressPages
 *
 *
 */


namespace Modules\administrator\system;




class Manager {
    function manage() {
        return ('<script type="text/javascript">document.location=\''.BASE_URL.'?g=administrator&m=system&ba=index\';</script>');
    }
}

