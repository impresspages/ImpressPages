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

        $lessCompiler = LessCompiler::instance();
        $lessCompiler->serve(THEME, 'theme.less');
    }


}
