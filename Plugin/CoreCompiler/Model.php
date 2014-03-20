<?php


namespace Plugin\CoreCompiler;


class Model
{

    public function generateAdminJS()
    {
        // source:
        // - root/Ip/Internal/Core/assets/admin/
        // output:
        // - assets/admin.min.js

        $items = array();
        $items[] = ipFile('Ip/Internal/Core/assets/admin/managementMode.js');
        $items[] = ipFile('Ip/Internal/Core/assets/admin/functions.js');

        $items[] = ipFile('Ip/Internal/Core/assets/admin/validator.js');
        $items[] = ipFile('Ip/Internal/Core/assets/admin/bootstrap.js');

        $jsFile = ipFile('Ip/Internal/Core/assets/admin.min.js');

        $this->minifyJS($items, $jsFile);
    }

    public function generateIpCoreJS()
    {
        // source:
        // - root/Ip/Internal/Core/assets/ipCoreJs/
        // output:
        // - assets/ipCoreJs.min.js

        $items = array();
        $items[] = ipFile('Ip/Internal/Core/assets/ipCore/jquery.js'); // default, global jQuery
        $items[] = ipFile('Ip/Internal/Core/assets/ipCore/console.log.js');
        $items[] = ipFile('Ip/Internal/Core/assets/ipCore/functions.js');
        $items[] = ipFile('Ip/Internal/Core/assets/ipCore/jquery.tools.form.js');

        $items[] = ipFile('Ip/Internal/Core/assets/ipCore/form/color.js');
        $items[] = ipFile('Ip/Internal/Core/assets/ipCore/form/file.js');
        $items[] = ipFile('Ip/Internal/Core/assets/ipCore/form/richtext.js');
        $items[] = ipFile('Ip/Internal/Core/assets/ipCore/form/repositoryFile.js');
        $items[] = ipFile('Ip/Internal/Core/assets/ipCore/form/url.js');
        $items[] = ipFile('Ip/Internal/Core/assets/ipCore/form.js');

        $items[] = ipFile('Ip/Internal/Core/assets/ipCore/validator.js');
        $items[] = ipFile('Ip/Internal/Core/assets/ipCore/widgets.js');
        $items[] = ipFile('Ip/Internal/Core/assets/ipCore/ipCore.js');

        $jsFile = ipFile('Ip/Internal/Core/assets/ipCore.min.js');

        $this->minifyJS($items, $jsFile);
    }

    public function generateManagementJS()
    {
        // source:
        // - root/Ip/Internal/Content/assets/management/
        // - root/Ip/Internal/Content/Widget/*/assets/
        // output:
        // - assets/management.min.js

        $items[] = ipFile('Ip/Internal/Content/assets/management/ipContentManagementInit.js');
        $items[] = ipFile('Ip/Internal/Content/assets/management/content.js');
        $items[] = ipFile('Ip/Internal/Content/assets/management/jquery.ip.contentManagement.js');
        $items[] = ipFile('Ip/Internal/Content/assets/management/jquery.ip.widgetbutton.js');
        $items[] = ipFile('Ip/Internal/Content/assets/management/jquery.ip.layoutModal.js');
        $items[] = ipFile('Ip/Internal/Content/assets/management/jquery.ip.block.js');
        $items[] = ipFile('Ip/Internal/Content/assets/management/jquery.ip.widget.js');
        $items[] = ipFile('Ip/Internal/Content/assets/management/exampleContent.js');
        $items[] = ipFile('Ip/Internal/Content/assets/management/drag.js');

        $items[] = ipFile('Ip/Internal/Content/Widget/Columns/assets/Columns.js');
        $items[] = ipFile('Ip/Internal/Content/Widget/File/assets/File.js');
        $items[] = ipFile('Ip/Internal/Content/Widget/File/assets/jquery.ipWidgetFile.js');
        $items[] = ipFile('Ip/Internal/Content/Widget/File/assets/jquery.ipWidgetFileContainer.js');
        $items[] = ipFile('Ip/Internal/Content/Widget/Form/assets/Form.js');
        $items[] = ipFile('Ip/Internal/Content/Widget/Form/assets/FormContainer.js');
        $items[] = ipFile('Ip/Internal/Content/Widget/Form/assets/FormField.js');
        $items[] = ipFile('Ip/Internal/Content/Widget/Form/assets/FormOptions.js');
        $items[] = ipFile('Ip/Internal/Content/Widget/Html/assets/Html.js');
        $items[] = ipFile('Ip/Internal/Content/Widget/Video/assets/Video.js');
        $items[] = ipFile('Ip/Internal/Content/Widget/Image/assets/Image.js');
        $items[] = ipFile('Ip/Internal/Content/Widget/Gallery/assets/Gallery.js');
        $items[] = ipFile('Ip/Internal/Content/Widget/Text/assets/Text.js');
        $items[] = ipFile('Ip/Internal/Content/Widget/Title/assets/Title.js');
        $items[] = ipFile('Ip/Internal/Content/Widget/Title/assets/TitleModal.js');
        $items[] = ipFile('Ip/Internal/Content/Widget/Map/assets/Map.js');


        $jsFile = ipFile('Ip/Internal/Content/assets/management.min.js');

        $this->minifyJS($items, $jsFile);
    }

    public function generateInlineManagementJS()
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
    public function generateIpContent()
    {

        $items = $this->globRecursive(ipFile('Ip/Internal/Core/assets/ipContent/less/') . '*.less');
        if (!$items) {
            return false;
        }

        $lessFile = ipFile('Ip/Internal/Core/assets/ipContent/ipContent.less');
        $cssFile = ipFile('Ip/Internal/Core/assets/ipContent/ipContent.css');
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
            $parserOptions = array(
                'cache_dir' => ipFile('file/tmp/less/'),
                'relativeUrls' => false
            );
            $parser = new \Less_Parser($parserOptions);
            $parser->parseFile($lessFile);
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
    public function generateCoreBootstrap()
    {
        $items = $this->globRecursive(ipFile('Ip/Internal/Core/assets/admin/ip/') . '*.less');
        if (!$items) {
            return false;
        }

        $lessTempFile = ipFile('Ip/Internal/Core/assets/admin/admin.tmp.less');
        $cssTempFile = ipFile('Ip/Internal/Core/assets/admin/admin.tmp.css');
        $lessFile = ipFile('Ip/Internal/Core/assets/admin/admin.less');
        $cssFile = ipFile('Ip/Internal/Core/assets/admin/admin.css');

        $lastBuildTimeTemp = file_exists($cssTempFile) ? filemtime($cssTempFile) : 0;
        $lastBuildTime = file_exists($cssFile) ? filemtime($cssFile) : 0;

        $hasChangedTemp = false;
        $hasChanged = false;

        foreach ($items as $path) {
            if (filemtime($path) > $lastBuildTimeTemp) {
                $hasChangedTemp = true;
                break;
            }
        }

        if ($lastBuildTimeTemp > $lastBuildTime) {
            $hasChanged = true;
        }

        if (!$hasChangedTemp && !$hasChanged) {
            return;
        }

        try {
            require_once ipFile('Ip/Lib/less.php/Less.php');
            $parserOptions = array(
                'cache_dir' => ipFile('file/tmp/less/'),
                'relativeUrls' => false
            );

            if ($hasChangedTemp) { // skipping temp compilation if only main file is missing
                $parserTemp = new \Less_Parser($parserOptions);
                $parserTemp->parseFile($lessTempFile);
                $cssTemp = $parserTemp->getCss();
                file_put_contents($cssTempFile, $cssTemp);
            }

            $parser = new \Less_Parser($parserOptions);
            $parser->parseFile($lessFile);
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
