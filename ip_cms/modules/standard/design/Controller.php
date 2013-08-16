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


        $configModel = ConfigModel::instance();
        $config = $configModel->getAllConfigValues(THEME);

        $less = "@import '{$file}.less'; " . $this->generateLessVariables($config);

        header('HTTP/1.0 200 OK');
        header('Content-Type: text/css');

        require_once BASE_DIR . LIBRARY_DIR . 'php/leafo/lessphp/lessc.inc.php';
        $lessc = new \lessc();
        $lessc->setImportDir(BASE_DIR . THEME_DIR . THEME . '/less');
        echo $lessc->compile($less);
        exit();
    }

    protected function generateLessVariables($config)
    {
        $less = '';
        foreach ($config as $key => $value) {
            if (!empty($value)) {
                $less .= "\n@{$key}: $value;";
            }
        }

        return $less;
    }
}
