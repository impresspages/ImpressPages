<?php

namespace Ip\Internal\Design;


/**
 * Compiles, serves and caches *.less files
 *
 * @package Ip\Internal\Design
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
        $options = $theme->getOptionsAsArray();

        $configModel = ConfigModel::instance();
        $config = $configModel->getAllConfigValues($themeName);

        $less = "@import '{$lessFile}';";
        $less.= $this->generateLessVariables($options, $config);

        require_once ipFile('Ip/Lib/Lessphp/lessc.inc.php');
        $lessc = new \lessc();
        $themeDir = rtrim(ipFile('Theme/' . $themeName . '/' . \Ip\Application::ASSETS_DIR . '/'), '/');
        $lessc->setImportDir(array($themeDir, ipFile('Ip/Internal/Ip/assets/css/ipContent')));
        //$lessc->setFormatter('compressed');
        $lessc->setVariables(array(
                'ipContentDir' => 'less/ipContent',
            ));
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
        $items = $this->globRecursive(ipFile('Theme/' . $themeName . '/') . '*.less');
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
        $lessFiles = glob(ipFile('Theme/' . $themeName . '/' . \Ip\Application::ASSETS_DIR . '/') . '*.less');
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
            file_put_contents(ipFile('Theme/' . $themeName . '/' . \Ip\Application::ASSETS_DIR . '/' . substr($lessFile, 0, -4) . 'css'), $css);
        }
    }

    /**
     * Recursive glob function from PHP manual (http://php.net/manual/en/function.glob.php)
     */
    protected function globRecursive($pattern, $flags = 0)
    {
        //some systems return false instead of empty array if no matches found in glob function
        $files = glob($pattern, $flags);
        if (!is_array($files)) {
            return array();
        }

        $dirs = glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT);
        if (!is_array($dirs)) {
            return $files;
        }
        foreach ($dirs as $dir) {
            $files = array_merge($files, $this->globRecursive($dir . '/' . basename($pattern), $flags));
        }

        return $files;
    }

    public function rebuildIpContent()
    {
        $items = $this->globRecursive(ipFile('Ip/Internal/Ip/assets/css/ipContent/less/') . '*.less');
        if (!$items) {
            return false;
        }

        $cssFile = ipFile('Ip/Internal/Ip/assets/css/ipContent/ipContent.css');
        $lastBuildTime = file_exists($cssFile) ? filemtime($cssFile) : 0;

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

        require_once ipFile('Ip/Lib/Lessphp/lessc.inc.php');
        $lessc = new \lessc();
        $lessc->setImportDir(ipFile('Ip/Internal/Ip/assets/css/ipContent'));
        $lessc->setPreserveComments(true);
        $css = $lessc->compileFile(ipFile('Ip/Internal/Ip/assets/css/ipContent/less/ipContent/ipContent.less'));
        file_put_contents($cssFile, $css);
    }
}