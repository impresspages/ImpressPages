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

    public function compile($themeName, $lessFile)
    {
        $compiledCssUrl = BASE_URL . THEME_DIR . $themeName . '/css/' . $lessFile . '.css';
        if ($this->isLessCached($themeName, $lessFile)) {
            if (!DEVELOPMENT_ENVIRONMENT && !$this->shouldRebuildCache($themeName, $lessFile)) {
                return $compiledCssUrl;
            }
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
        file_put_contents($this->compiledFilename($themeName, $lessFile), $css);

        return $compiledCssUrl;
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
                case 'hidden':
                case 'range':
                    $lessValue = $rawValue;
                    if (!empty($option['units'])) {
                        $lessValue .= $option['units'];
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

        return file_exists($compiledFilename);
    }

    protected function shouldRebuildCache($themeName, $lessFile)
    {
        $compiledFilename = $this->compiledFilename($themeName, $lessFile);
        $compileTime = filemtime($compiledFilename);

        $items = glob(BASE_DIR . THEME_DIR . $themeName . '/less/*');

        for ($i = 0; $i < count($items); $i++) {

            if (is_dir($items[$i])) {
                $add = glob($items[$i] . "/*");
                $items = array_merge($items, $add);
            }
        }

        foreach ($items as $path) {
            if (preg_match('/[.]less$/', $path)) {
                if (filemtime($path) > $compileTime) {
                    return true;
                }
            }
        }

        return false;
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