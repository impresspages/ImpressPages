<?php

namespace Modules\standard\design;

use Ip\ServiceLocator;

if (!defined('CMS')) {
    exit;
}

class Controller extends \Ip\Controller
{
    public function less()
    {
        $request = ServiceLocator::getRequest();
        $file = $request->getQuery('file');

        require_once BASE_DIR . LIBRARY_DIR . 'php/leafo/lessphp/lessc.inc.php';
        $less = new \lessc();

        $themeLessPath = BASE_DIR . THEME_DIR . THEME . '/less/' . $file . '.less';

        header('HTTP/1.0 200 OK');
        header('Content-Type: text/css');
        echo $less->compileFile($themeLessPath);
        exit();
    }
}
