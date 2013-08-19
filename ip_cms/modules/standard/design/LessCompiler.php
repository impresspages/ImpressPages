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
        header('HTTP/1.0 200 OK');
        header('Content-Type: text/css');

        if ($this->isLessCached($themeName, $lessFile)) {
            echo file_get_contents($this->compiledFilename($themeName, $lessFile));
            exit();
        }

        $model = Model::instance();

        $theme = $model->getTheme($themeName);
        $options = $theme->getOptions();

        $configModel = ConfigModel::instance();
        $config = $configModel->getAllConfigValues($themeName);

        $less = "@import '{$lessFile}'; " . $this->generateLessVariables($options, $config);

        require_once BASE_DIR . LIBRARY_DIR . 'php/leafo/lessphp/lessc.inc.php';
        $lessc = new \lessc();
        $lessc->setImportDir(BASE_DIR . THEME_DIR . $themeName . '/less');
        $css = $lessc->compile($less);
        echo $css;
        flush();
        file_put_contents($this->compiledFilename($themeName, $lessFile), $css);
        exit();
    }

    protected function generateLessVariables($options, $config)
    {
        $less = '';

        foreach ($options as $option) {
            $rawValue = array_key_exists($option['name'], $config) ? $config[$option['name']] : $option['default'];

            if (empty($rawValue)) {
                continue; // ignore empty values
            }

            switch ($option['type']) {
                case 'color':
                    $lessValue = $rawValue;
                    break;
                case 'range':
                    $lessValue = $rawValue;
                    if (!empty($options['units'])) {
                        $lessValue .= $options['units'];
                    }
                    break;
                default:
                    $lessValue = json_encode($rawValue);
            }

            $less .= "\n@{$option['name']}: {$lessValue};";
        }



        return $less;
    }

    protected function isLessCached($themeName, $lessFile)
    {
        $compiledFilename = $this->compiledFilename($themeName, $lessFile);

        if (!file_exists($compiledFilename)) {
            return false;
        }

        $items = glob(BASE_DIR . THEME_DIR . $themeName . '/less/*');

        for ($i = 0; $i < count($items); $i++) {

            if (is_dir($items[$i])) {
                $add = glob($items[$i] . "/*");
                $items = array_merge($items, $add);
            }
        }

        $compileTime = filemtime($compiledFilename);

        foreach ($items as $path) {
            if (preg_match('/[.]less$/', $path)) {
                if (filemtime($path) > $compileTime) {
                    return false;
                }
            }
        }

        return true;
    }

    public function clearCache($themeName)
    {
        $compiledFiles = glob(BASE_DIR . THEME_DIR . $themeName . '/css/*.less.css');

        // TODOX check permissions
        foreach ($compiledFiles as $compiledFile) {
            unlink($compiledFile);
        }
    }

    private function compiledFilename($themeName, $lessFile)
    {
        return BASE_DIR . THEME_DIR . $themeName . "/css/{$lessFile}.css";
    }
}