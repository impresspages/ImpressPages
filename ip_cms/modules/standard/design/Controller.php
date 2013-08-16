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

        if ($this->isLessCached($less)) {
            echo file_get_contents(BASE_DIR . THEME_DIR . THEME . '/css/compiled.css');
            exit();
        }

        require_once BASE_DIR . LIBRARY_DIR . 'php/leafo/lessphp/lessc.inc.php';
        $lessc = new \lessc();
        $lessc->setImportDir(BASE_DIR . THEME_DIR . THEME . '/less');
        $css = $lessc->compile($less);
        echo $css;
        flush();
        file_put_contents(BASE_DIR . THEME_DIR . THEME . '/css/compiled.css', $css);
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

    protected function isLessCached($less)
    {
        // TODOX check weather variables were changed
        // check filemtime()

        //$files = scandir(BASE_DIR . THEME_DIR . THEME . '/less');
        //$files = glob(BASE_DIR . THEME_DIR . THEME . '/less/*.less');

        $items = glob(BASE_DIR . THEME_DIR . THEME . '/less/*');

        for ($i = 0; $i < count($items); $i++) {

            if (is_dir($items[$i])) {
                $add = glob($items[$i] . "/*");
                $items = array_merge($items, $add);
            }
        }

        $compileTime = filemtime(BASE_DIR . THEME_DIR . THEME . '/css/compiled.css');

        foreach ($items as $path) {
            if (preg_match('/[.]less$/', $path)) {
                if (filemtime($path) > $compileTime) {
                    return false;
                }
            }
        }

        return true;
    }
}
