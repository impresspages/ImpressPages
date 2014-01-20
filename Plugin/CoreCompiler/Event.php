<?php

namespace Plugin\CoreCompiler;


class Event
{
    public static function ipInit()
    {
        /**
         * 1. define assets to compile: LESS, JS
         * 2. define the where goes output
         * 3. load required classes for each process
         * 4. check if action needed
         * 5. execute
         */

        $Event = new self();
        $Event->generateManagementJS();
        $Event->generateInlineManagementJS();
        $Event->generateIpContent();
        $Event->generateCoreBootstrap();
    }

    protected function generateManagementJS()
    {
        // source:
        // - root/Ip/Internal/Content/assets/management/
        // - root/Ip/Internal/Content/Widget/*/assets/
        // output:
        // - assets/management.min.js

        $items = $this->globRecursive(ipFile('Ip/Internal/Content/assets/management/') . '*.js');
        $items = array_merge($items, $this->globRecursive(ipFile('Ip/Internal/Content/Widget/*/assets/') . '*.js'));
        if (!$items) {
            return false;
        }

        $jsFile = ipFile('Ip/Internal/Content/assets/management.min.js');

        $this->minifyJS($items, $jsFile);
    }

    protected function generateInlineManagementJS()
    {
        // source:
        // - root/Ip/Internal/InlineManagement/assets/src/
        // output:
        // - assets/inlineManagement.min.js

        $items = $this->globRecursive(ipFile('Ip/Internal/InlineManagement/assets/src/') . '*.js');
        if (!$items) {
            return false;
        }

        $jsFile = ipFile('Ip/Internal/InlineManagement/assets/inlineManagement.min.js');

        $this->minifyJS($items, $jsFile);
    }

    /**
     * Generate default content styles
     *
     * @return none
     */
    protected function generateIpContent()
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

        try {
            require_once ipFile('Ip/Lib/less.php/Less.php');
            $parser = new \Less_Parser();
            $parser->parseFile(ipFile('Ip/Internal/Ip/assets/css/ipContent/less/ipContent/ipContent.less'));
            $css = $parser->getCss();
            file_put_contents($cssFile, $css);
        } catch(Exception $e) {
            ipLog()->error('Less compilation error: IpContent - ' . $e->getMessage());
        }
    }

    /**
     * Generate core Bootstrap styles
     *
     * @return none
     */
    protected function generateCoreBootstrap()
    {
        $items = $this->globRecursive(ipFile('Ip/Internal/Ip/assets/bootstrap/less/') . '*.less');
        if (!$items) {
            return false;
        }

        $cssTempFile = ipFile('Ip/Internal/Ip/assets/bootstrap/less/bootstrap.temp.less');
        $lastBuildTime = file_exists($cssTempFile) ? filemtime($cssTempFile) : 0;

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

        try {
            require_once ipFile('Ip/Lib/less.php/Less.php');
            $parserTemp = new \Less_Parser();
            $parserTemp->parseFile(ipFile('Ip/Internal/Ip/assets/bootstrap/less/bootstrap.less'));
            $cssTemp = $parserTemp->getCss();
            file_put_contents($cssTempFile, $cssTemp);

            $parser = new \Less_Parser();
            $cssFile = ipFile('Ip/Internal/Ip/assets/bootstrap/bootstrap.css');
            $parser->parseFile(ipFile('Ip/Internal/Ip/assets/bootstrap/bootstrap.less'));
            $css = $parser->getCss();
            file_put_contents($cssFile, $css);
        } catch(Exception $e) {
            ipLog()->error('Less compilation error: Core Bootstrap - ' . $e->getMessage());
        }
    }

    protected function minifyJS($filesToMinify, $fileForOutput, $force = false)
    {
        $items = $filesToMinify;
        $jsFile = $fileForOutput;

        $lastBuildTime = file_exists($jsFile) ? filemtime($jsFile) : 0;
        $hasChanged = false;

        foreach ($items as $path) {
            if (filemtime($path) > $lastBuildTime) {
                $hasChanged = true;
                break;
            }
        }

        if (!$hasChanged && !$force) {
            return;
        }

        $js = '';
        foreach ($items as $path) {
            if (is_readable($path)) {
                $js .= file_get_contents($path);
            } else {
                ipLog()->error('Cannot read file to minify it: '. $path);
            }
        }
        require_once 'lib/JSMin.php';
        $minJS = \JSMin::minify($js);
        file_put_contents($jsFile, $minJS);
    }

    protected function minifyCSS($filesToMinify, $fileForOutput, $force = false)
    {
        $items = $filesToMinify;
        $cssFile = $fileForOutput;

        $lastBuildTime = file_exists($cssFile) ? filemtime($cssFile) : 0;
        $hasChanged = false;

        foreach ($items as $path) {
            if (filemtime($path) > $lastBuildTime) {
                $hasChanged = true;
                break;
            }
        }

        if (!$hasChanged && !$force) {
            return;
        }

        $css = '';
        foreach ($items as $path) {
            if (is_readable($path)) {
                $css .= file_get_contents($path);
            } else {
                ipLog()->error('Cannot read file to minify it: '. $path);
            }
        }

        require_once 'lib/CSSMin.php';
        $cssmin = new \CSSmin();
        $minCSS = trim($cssmin->run($css));
        file_put_contents($cssFile, $minCSS);
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
}
