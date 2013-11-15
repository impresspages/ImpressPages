<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Ip\Response;

/**
 *
 * Event dispatcher class
 *
 */
class Layout extends \Ip\Response {
    protected $layout = 'main.php';

    /** array js variables */
    private $javascriptVariables = array();

    /** array required javascript files */
    private $requiredJavascript = array();

    /** array required css files */
    private $requiredCss = array();

    public function send()
    {
        ipSetBlockContent('main', $this->content);
        $this->content = 'TODOX';
        parent::send();
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function addCss($file, $stage = 1) {
        $this->requiredCss[(int)$stage][$file] = $file;
    }

    public function removeCss($file) {
        foreach($this->requiredCss as $levelKey => &$level) {
            if (isset($this->requiredCss[$levelKey][$file])) {
                unset($this->requiredCss[$levelKey][$file]);
            }
        }
    }

    public function getCss() {
        ksort($this->requiredCss);
        $cssFiles = array();
        foreach($this->requiredCss as $levelKey => $level) {
            $cssFiles = array_merge($cssFiles, $level);
        }
        return $cssFiles;
    }

    public function addJavascriptContent($key, $javascript, $stage = 1) {
        $this->requiredJavascript[(int)$stage][$key] = array (
            'type' => 'content',
            'value' => $javascript
        );
    }

    /**
     * @deprecated
     * @param $name
     * @param $value
     * @param int $stage
     */
    public function addJavascriptVar($name, $value, $stage = 1) {
        $this->addJavascriptVariable($name, $value);
    }

    public function addJavascript($file, $stage = 1) {
        $this->requiredJavascript[(int)$stage][$file] = array (
            'type' => 'file',
            'value' => $file
        );
    }

    public function removeJavascript($file) {
        foreach($this->requiredJavascript as $levelKey => &$level) {
            if (isset($this->requiredJavascript[$levelKey][$file]) && $this->requiredJavascript[$levelKey][$file]['type'] == 'file') {
                unset($this->requiredJavascript[$levelKey][$file]);
            }
        }
    }



    public function removeJavascriptContent($key) {
        foreach($this->requiredJavascript as $levelKey => &$level) {
            if (isset($this->requiredJavascript[$levelKey][$key]) && $this->requiredJavascript[$levelKey][$key]['type'] == 'content') {
                unset($this->requiredJavascript[$levelKey][$key]);
            }
        }
    }

    public function getJavascript() {
        ksort($this->requiredJavascript);
        return $this->requiredJavascript;
    }

    public function addJavascriptVariable($name, $value) {
        $this->javascriptVariables[$name] = $value;
    }

    public function removeJavascriptVariable($name) {
        if (isset($this->javascriptVariables[$name])) {
            unset($this->javascriptVariables[$name]);
        }
    }

    public function getJavascriptVariables() {
        return $this->javascriptVariables;
    }

    public function generateHead() {
        $cacheVersion = \Ip\DbSystem::getSystemVariable('cache_version');
        $cssFiles = $this->getCss();

        $inDesignPreview = false;

        $data = \Ip\Request::getRequest();

        if (!empty($data['ipDesign']['pCfg']) && (defined('IP_ALLOW_PUBLIC_THEME_CONFIG') || isset($_REQUEST['ipDesignPreview']))) {
            $config = \Ip\Module\Design\ConfigModel::instance();
            $inDesignPreview = $config->isInPreviewState();
        }

        if (!$inDesignPreview) {
            foreach($cssFiles as &$file) {
                $file .= (strpos($file, '?') !== false ? '&' : '?') . $cacheVersion;
            }
        } else {
            foreach($cssFiles as &$file) {

                $path = pathinfo($file);

                if ($path['dirname'] . '/' == \Ip\Config::themeFile('') && file_exists(\Ip\Config::themeFile($path['filename'] . '.less'))) {
                    $designService = \Ip\Module\Design\Service::instance();
                    $file = $designService->getRealTimeUrl(\Ip\Config::theme(), $path['filename']);
                } else {
                    $file .= (strpos($file, '?') !== false ? '&' : '?') . $cacheVersion;
                }
            }
        }

        $site = \Ip\ServiceLocator::getSite();

        $data = array (
            'title' => $site->getTitle(),
            'keywords' => $site->getKeywords(),
            'description' => $site->getDescription(),
            'favicon' => \Ip\Config::baseUrl('favicon.ico'),
            'charset' => \Ip\Config::getRaw('CHARSET'),
            'css' => $cssFiles
        );

        return \Ip\View::create(\Ip\Config::coreModuleFile('Config/view/head.php'), $data)->render();
    }

    public function generateJavascript() {
        $cacheVersion = \Ip\DbSystem::getSystemVariable('cache_version');
        $javascriptFiles = $this->getJavascript();
        foreach($javascriptFiles as &$level) {
            foreach($level as &$file) {
                if ($file['type'] == 'file') {
                    $file['value'] .= (strpos($file['value'], '?') !== false ? '&' : '?') . $cacheVersion;
                }
            }
        }
        $site = \Ip\ServiceLocator::getSite();
        $revision = $site->getRevision();
        $data = array (
            'ipBaseUrl' => \Ip\Config::baseUrl(''),
            'ipLanguageUrl' => $site->generateUrl(),
            'ipLibraryDir' => \Ip\Config::getRaw('LIBRARY_DIR'),
            'ipThemeDir' => \Ip\Config::getRaw('THEME_DIR'),
            'ipModuleDir' => \Ip\Config::getRaw('MODULE_DIR'),
            'ipTheme' => \Ip\Config::getRaw('THEME'),
            'ipManagementUrl' => $site->generateUrl(),
            'ipZoneName' => ipGetCurrentZone() ? ipGetCurrentZone()->getName() : null,
            'ipPageId' => ipGetCurrentPage() ?ipGetCurrentPage()->getId() : null,
            'ipRevisionId' => $revision['revisionId'],
            'ipSecurityToken' =>\Ip\ServiceLocator::getApplication()->getSecurityToken(),
            'javascript' => $javascriptFiles,
            'javascriptVariables' => $this->getJavascriptVariables()
        );
        return \Ip\View::create(\Ip\Config::coreModuleFile('Config/view/javascript.php'), $data)->render();
    }


}