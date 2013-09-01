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


    /**
     * @param string $themeName
     * @param string $lessFile
     * @return string
     */
    public function compileFile($themeName, $lessFile)
    {

        $model = Model::instance();

        $theme = $model->getTheme($themeName);
        $options = $theme->getOptions();

        $configModel = ConfigModel::instance();
        $config = $configModel->getAllConfigValues($themeName);

        $less = "@import '{$lessFile}'; " . $this->generateLessVariables($options, $config);

        require_once BASE_DIR . LIBRARY_DIR . 'php/leafo/lessphp/lessc.inc.php';
        $lessc = new \lessc();
        $lessc->setImportDir(BASE_DIR . THEME_DIR . $themeName);
        $css = $lessc->compile($less);
        return $css;
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

    public function shouldRebuild($themeName)
    {
        $lastBuildTime = $this->getLastBuildTime($themeName);
        $items = glob(BASE_DIR . THEME_DIR . $themeName . '/less/*');
        $items = array_merge($items, glob(BASE_DIR . THEME_DIR . $themeName . '/*'));

        for ($i = 0; $i < count($items); $i++) {

            if (is_dir($items[$i])) {
                $add = glob($items[$i] . "/*");
                $items = array_merge($items, $add);
            }
        }

        foreach ($items as $path) {
            if (preg_match('/[.]less$/', $path)) {

                if (filemtime($path) > $lastBuildTime) {
                    $debug = array(
                        'filetime' => filemtime($path),
                        'compileTime' => $lastBuildTime,
                    );

                    return true;
                }
            }
        }

        return false;
    }

    protected function getLastBuildTime($themeName)
    {
        $lessFiles = $this->getLessFiles($themeName);
        $lastBuild = 0;
        foreach ($lessFiles as $file) {
            $cssFile = substr($file, 0, -4) . 'css';
            if (!file_exists($cssFile)) {
                return 0; //we have no build or it is not completed!
            }
            $lastBuild = filemtime($cssFile);
        }
        return $lastBuild;
    }

    protected function getLessFiles($themeName)
    {
        $lessFiles = glob(BASE_DIR . THEME_DIR . $themeName . DIRECTORY_SEPARATOR . '*.less');
        if (!is_array($lessFiles)) {
            return array();
        }
        return $lessFiles;
    }

    /**
     * Rebuilds compiled css files.
     *
     * @param string $themeName
     */
    public function rebuild($themeName)
    {
        $lessFiles = $this->getLessFiles($themeName);
        foreach ($lessFiles as $file) {
            $lessFile = basename($file);
            $css = $this->compileFile($themeName, basename($lessFile));
            file_put_contents(BASE_DIR . THEME_DIR . $themeName . '/' . substr($lessFile, 0, -4) . 'css', $css);
        }
    }


}