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
        $lessc->setImportDir(array(BASE_DIR . THEME_DIR . $themeName, BASE_DIR . LIBRARY_DIR . 'css/ipContent'));
        $lessc->setFormatter('compressed');
        $css = $lessc->compile($less);
        $css = "/* Edit {$lessFile}, not this file. */ " . $css;
        return $css;
    }


    protected function generateLessVariables($options, $config)
    {
        $less = '';

        foreach ($options as $option) {
            if (empty($option['name']) || empty($option['type'])) {
                continue; // ignore invalid nodes
            }

            if (!empty($config[$option['name']])) {
                $rawValue = $config[$option['name']];
            } elseif (!empty($option['default'])) {
                $rawValue = $option['default'];
            } else {
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
        $items = $this->globRecursive(BASE_DIR . THEME_DIR . $themeName . '/*.less');
        if (!$items) {
            return false;
        }

        $lastBuildTime = $this->getLastBuildTime($themeName);

        foreach ($items as $path) {
            if (filemtime($path) > $lastBuildTime) {
                return true;
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

    /**
     * Recursive glob function from PHP manual (http://php.net/manual/en/function.glob.php)
     */
    protected function globRecursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);

        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->globRecursive($dir . '/' . basename($pattern), $flags));
        }

        return $files;
    }

    public function rebuildIpContent()
    {
        $items = $this->globRecursive(BASE_DIR . LIBRARY_DIR . 'css/ipContent/less/*.less');
        if (!$items) {
            return false;
        }

        $cssFile = BASE_DIR . LIBRARY_DIR . 'css/ipContent/ip_content.css';
        $lastBuildTime = filemtime($cssFile);

        $hasChanged = false;

        foreach ($items as $path) {
            if (filemtime($path) > $lastBuildTime) {
                $hasChanged = true;
                break;
            }
        }

        if (!$hasChanged) {
            return;
        }

        require_once BASE_DIR . LIBRARY_DIR . 'php/leafo/lessphp/lessc.inc.php';
        $lessc = new \lessc();
        $lessc->setImportDir(BASE_DIR . LIBRARY_DIR . 'css/ipContent');
        $lessc->setPreserveComments(true);
        $css = $lessc->compileFile(BASE_DIR . LIBRARY_DIR . 'css/ipContent/less/ipContent/ipContent.less');
        file_put_contents($cssFile, $css);
    }
}