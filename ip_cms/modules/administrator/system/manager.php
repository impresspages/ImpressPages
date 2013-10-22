<?php

/**
 * @package ImpressPages
 *
 *
 */


namespace Modules\administrator\system;




class Manager {
    function manage() {
        header('location: ' . BASE_URL . '?g=administrator&m=system&aa=index');
        exit();
    }
}

