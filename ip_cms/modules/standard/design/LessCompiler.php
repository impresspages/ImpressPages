<?php

namespace Modules\standard\design;


/**
 * Compiles, serves and caches *.less files
 *
 * @package Modules\standard\design
 */
class LessCompiler
{
    /**
     * @return self
     */
    public static function instance()
    {
        return new self();
    }

    public function serve($themeName, $lessFile)
    {
        $model = Model::instance();

        $theme = $model->getTheme($themeName);
        $options = $theme->getOptions();

        $configModel = ConfigModel::instance();
        $config = $configModel->getAllConfigValues($themeName);

        $less = "@import '{$lessFile}'; " . $this->generateLessVariables($options, $config);

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

    protected function generateLessVariables($options, $config)
    {
        $less = '';

        foreach ($options as $option)
        {
            $rawValue = array_key_exists($option['name'], $config) ? $config[$option['name']] : $option['default'];

            switch ($option['type']) {
                case 'color':
                    $lessValue = $rawValue;
                    break;
                case 'range':
                    $lessValue = $rawValue;
                    break;
                default:
                    $lessValue = json_encode($rawValue);
            }

            $less .= "\n@{$option['name']}: {$lessValue};";
        }

        return $less;
    }

    protected function isLessCached($less)
    {
        // TODOX check weather variables were changed

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