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
    protected $layout = null;

    /** array js variables */
    private $javascriptVariables = array();

    /** array required javascript files */
    private $requiredJavascript = array();

    /** array required css files */
    private $requiredCss = array();

    private $layoutVariables = array();


    public function send()
    {
        ipSetBlockContent('main', $this->content);


        if ($this->getLayout() === null) {
            $this->chooseLayout();
        }
        $layout = $this->getLayout();

        if ($layout[0] == '/' || $layout[1] == ':') { // Check if absolute path: '/' for unix, 'C:' for windows
            $viewFile = $layout;
        } else {
            $viewFile = ipConfig()->themeFile($layout);
        }

        $this->setContent(\Ip\View::create($viewFile, $this->getLayoutVariables())->render());

        parent::send();
    }

    protected function chooseLayout()
    {
        if (\Ip\ServiceLocator::getRequest()->getControllerType() == \Ip\Request::CONTROLLER_TYPE_ADMIN) {
            $this->layout = ipConfig()->getCore('CORE_DIR') . 'Ip/Module/Admin/View/layout.php';
            $this->addCss(ipConfig()->coreModuleUrl('Assets/assets/css/bootstrap/bootstrap.css'));
            $this->addJavascript(ipConfig()->coreModuleUrl('Assets/assets/css/bootstrap/bootstrap.js'));
        } elseif (\Ip\Module\Admin\Model::isSafeMode()) {
            $this->layout = '/Admin/View/safeModeLayout.php';
        } else {
            $this->layout = 'main.php';
        }
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function addCss($file, $attributes = array(), $stage = 1, $cacheFix = true) {
        $this->requiredCss[(int)$stage][$file] = array (
            'value' => $file,
            'attributes' => $attributes,
            'cacheFix' => $cacheFix
        );
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
        foreach($this->requiredCss as $level) {
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


    public function addJavascript($file, $attributes = array(), $stage = 1, $cacheFix = true) {
        $this->requiredJavascript[(int)$stage][$file] = array (
            'type' => 'file',
            'value' => $file,
            'attributes' => $attributes,
            'cacheFix' => $cacheFix
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
        $cacheVersion = \Ip\Internal\DbSystem::getSystemVariable('cache_version');
        $cssFiles = $this->getCss();

        $inDesignPreview = false;

        $data = ipRequest()->getRequest();

        if (!empty($data['ipDesign']['pCfg']) && (defined('IP_ALLOW_PUBLIC_THEME_CONFIG') || isset($_REQUEST['ipDesignPreview']))) {
            $config = \Ip\Module\Design\ConfigModel::instance();
            $inDesignPreview = $config->isInPreviewState();
        }

        if ($inDesignPreview) {
            foreach($cssFiles as &$file) {
                $path = pathinfo($file['value']);
                if ($path['dirname'] . '/' == ipConfig()->themeFile('') && file_exists(ipConfig()->themeFile($path['filename'] . '.less'))) {
                    $designService = \Ip\Module\Design\Service::instance();
                    $file = $designService->getRealTimeUrl(ipConfig()->theme(), $path['filename']);
                } else {
                    if ($file['cacheFix']) {
                        $file['value'] .= (strpos($file['value'], '?') !== false ? '&' : '?') . $cacheVersion;
                    }
                }
            }
        } else {
            foreach($cssFiles as &$file) {
                if ($file['cacheFix']) {
                    $file['value'] .= (strpos($file['value'], '?') !== false ? '&' : '?') . $cacheVersion;
                }
            }
        }


        $data = array (
            'title' => \Ip\ServiceLocator::getContent()->gettitle(),
            'keywords' => \Ip\ServiceLocator::getContent()->getKeywords(),
            'description' => \Ip\ServiceLocator::getContent()->getDescription(),
            'favicon' => ipConfig()->baseUrl('favicon.ico'),
            'charset' => ipConfig()->getRaw('CHARSET'),
            'css' => $cssFiles
        );

        return \Ip\View::create(ipConfig()->coreModuleFile('Config/view/head.php'), $data)->render();
    }

    public function generateJavascript() {
        $cacheVersion = \Ip\Internal\DbSystem::getSystemVariable('cache_version');
        $javascriptFiles = $this->getJavascript();
        foreach($javascriptFiles as &$level) {
            foreach($level as &$file) {
                if ($file['type'] == 'file' && $file['cacheFix']) {
                    $file['value'] .= (strpos($file['value'], '?') !== false ? '&' : '?') . $cacheVersion;
                }
            }
        }
        $revision = \Ip\ServiceLocator::getContent()->getRevision();
        $data = array (
            'ipBaseUrl' => ipConfig()->baseUrl(''),
            'ipLanguageUrl' => \Ip\Internal\Deprecated\Url::generate(),
            'ipThemeDir' => ipConfig()->getRaw('THEME_DIR'),
            'ipTheme' => ipConfig()->getRaw('THEME'),
            'ipManagementUrl' => \Ip\Internal\Deprecated\Url::generate(),
            'ipZoneName' => ipGetCurrentZone() ? ipGetCurrentZone()->getName() : null,
            'ipPageId' => ipGetCurrentPage() ?ipGetCurrentPage()->getId() : null,
            'ipRevisionId' => $revision['revisionId'],
            'ipSecurityToken' =>\Ip\ServiceLocator::application()->getSecurityToken(),
            'javascript' => $javascriptFiles,
            'javascriptVariables' => $this->getJavascriptVariables()
        );
        return \Ip\View::create(ipConfig()->coreModuleFile('Config/view/javascript.php'), $data)->render();
    }


    public function setLayoutVariable($name, $value)
    {
        $this->layoutVariables[$name] = $value;
    }

    public function getLayoutVariables()
    {
        return $this->layoutVariables;
    }

}