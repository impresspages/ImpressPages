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
        $less .= $this->generateLessVariables($options, $config);

        $css = '';

        try {
            require_once ipFile('Ip/Lib/less.php/Less.php');
            $themeDir = ipFile('Theme/' . $themeName . '/assets/');
            $ipContentDir = ipFile('Ip/Internal/Core/assets/ipContent/');

            // creating new context to pass theme assets directory dynamically to a static callback function
            $context = $this;
            $callback = function ($parseFile) use ($context, $themeDir) {
                return $context->overrideImportDirectory($themeDir, $parseFile);
            };

            $parserOptions = array(
                'import_callback' => $callback,
                'cache_dir' => ipFile('file/tmp/less/'),
                'relativeUrls' => false,
                'sourceMap' => true
            );
            $parser = new \Less_Parser($parserOptions);
            $directories = array(
                $themeDir => '',
                $ipContentDir => ''
            );
            $parser->SetImportDirs($directories);
            $parser->parse($less);
            $css = $parser->getCss();
            $css = "/* Edit {$lessFile}, not this file. */" . "\n" . $css;
        } catch (\Exception $e) {
            ipLog()->error('Less compilation error: Theme - ' . $e->getMessage());
        }

        return $css;
    }

    public static function overrideImportDirectory($themeAssetsDir, $parseFile)
    {
        $full_path = $themeAssetsDir . $parseFile->getPath();
        $uri = ''; // relative path doesn't work correctly

        // if file exists in theme directory it means we want to override the default path
        if (file_exists($full_path)) {
            return array($full_path, $uri);
        }
    }

    protected function generateLessVariables($options, $config)
    {
        $less = '';

        foreach ($options as $option) {
            if (isset($option['addToLess']) && !$option['addToLess']) {
                continue;
            }

            if (empty($option['name']) || empty($option['type'])) {
                continue; // ignore invalid nodes
            }

            if (isset($config[$option['name']])) {
                $rawValue = $config[$option['name']];
            } elseif (isset($option['default'])) {
                $rawValue = $option['default'];
            } else {
                $rawValue = '';//continue;
            }

            switch ($option['type']) {
                case 'select':
                case 'Select':
                case 'color':
                case 'Color':
                    $lessValue = $rawValue;
                    break;
                case 'RepositoryFile':
                    $lessValue = $rawValue;
                    $lessValue = "'" . ipFileUrl('file/repository/' . escAttr($lessValue)) . "'";
                    break;
                default:
                case 'hidden':
                case 'Hidden':
                case 'range':
                case 'Range':
                    $lessValue = $rawValue;
                    if (!empty($option['units'])) {
                        $lessValue .= $option['units'];
                    }
                    if (!isset($option['escape']) || $option['escape']) {
                        $lessValue = "'" . preg_replace('~[\r\n]+~', '\\r\\n', escAttr($lessValue)) . "'";
                    }
                    break;
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
            return [];
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
            file_put_contents(
                ipFile(
                    'Theme/' . $themeName . '/' . \Ip\Application::ASSETS_DIR . '/' . substr($lessFile, 0, -4) . 'css'
                ),
                $css
            );
        }
    }

    /**
     * Recursive glob function from PHP manual (http://php.net/manual/en/function.glob.php)
     */
    protected function globRecursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        if (!is_array($files)) {
            //some systems return false instead of empty array if no matches found in glob function
            $files = [];
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
}
