<?php
/**
 * @package ImpressPages
 */

namespace Ip\Internal;

class PageAssets
{
    /** array js variables */
    private $javascriptVariables = [];

    /** array required javascript files */
    private $requiredJavascript = [];

    /** array required css files */
    private $requiredCss = [];

    public function addCss($file, $attributes = [], $stage = 50, $cacheFix = true)
    {
        $this->requiredCss[(int)$stage][$file] = array(
            'value' => $file,
            'attributes' => $attributes,
            'cacheFix' => $cacheFix
        );
    }

    public function removeCss($file)
    {
        foreach ($this->requiredCss as $levelKey => &$level) {
            if (isset($this->requiredCss[$levelKey][$file])) {
                unset($this->requiredCss[$levelKey][$file]);
            }
        }
    }

    public function getCss()
    {
        ksort($this->requiredCss);
        $cssFiles = [];
        foreach ($this->requiredCss as $level) {
            $cssFiles = array_merge($cssFiles, $level);
        }
        return $cssFiles;
    }

    public function addJavascriptContent($key, $javascript, $stage = 50)
    {
        $this->requiredJavascript[(int)$stage][$key] = array(
            'type' => 'content',
            'value' => $javascript
        );
    }


    public function addJavascript($file, $attributes = [], $stage = 50, $cacheFix = true)
    {
        $this->requiredJavascript[(int)$stage][$file] = array(
            'type' => 'file',
            'value' => $file,
            'attributes' => $attributes,
            'cacheFix' => $cacheFix
        );
    }

    public function removeJavascript($file)
    {
        foreach ($this->requiredJavascript as $levelKey => &$level) {
            if (isset($this->requiredJavascript[$levelKey][$file]) && $this->requiredJavascript[$levelKey][$file]['type'] == 'file') {
                unset($this->requiredJavascript[$levelKey][$file]);
            }
        }
    }

    public function removeJavascriptContent($key)
    {
        foreach ($this->requiredJavascript as $levelKey => &$level) {
            if (isset($this->requiredJavascript[$levelKey][$key]) && $this->requiredJavascript[$levelKey][$key]['type'] == 'content') {
                unset($this->requiredJavascript[$levelKey][$key]);
            }
        }
    }

    public function getJavascript()
    {
        ksort($this->requiredJavascript);
        return $this->requiredJavascript;
    }

    public function addJavascriptVariable($name, $value)
    {
        $this->javascriptVariables[$name] = $value;
    }

    public function removeJavascriptVariable($name)
    {
        if (isset($this->javascriptVariables[$name])) {
            unset($this->javascriptVariables[$name]);
        }
    }

    public function getJavascriptVariables()
    {
        return $this->javascriptVariables;
    }

    public function generateHead()
    {
        $cacheVersion = $this->getCacheVersion();
        $cssFiles = $this->getCss();

        $inDesignPreview = false;

        $data = ipRequest()->getRequest();

        if (!empty($data['ipDesign']['pCfg']) || !empty($data['restoreDefault'])) {
            $inDesignPreview = \Ip\Internal\Design\ConfigModel::instance()->isInPreviewState();
        }

        if ($inDesignPreview) {
            $themeAssetsUrl = ipThemeUrl(\Ip\Application::ASSETS_DIR . '/');
            $designService = \Ip\Internal\Design\Service::instance();
            $theme = ipConfig()->theme();

            foreach ($cssFiles as &$file) {
                if (strpos($file['value'], $themeAssetsUrl) === 0) {
                    $pathinfo = pathinfo($file['value']);

                    if ($pathinfo['extension'] == 'css'
                        && $themeAssetsUrl . $pathinfo['basename'] == $file['value']
                    ) {
                        $themeFile = \Ip\Application::ASSETS_DIR . '/' . $pathinfo['filename'] . '.less';
                        if (file_exists(ipThemeFile($themeFile))) {
                            $file['value'] = $designService->getRealTimeUrl($theme, $themeFile);
                            $file['cacheFix'] = false;
                        }
                    }
                }

                if ($file['cacheFix']) {
                    $file['value'] .= (strpos($file['value'], '?') !== false ? '&' : '?') . $cacheVersion;
                }
            }
        } else {
            foreach ($cssFiles as &$file) {
                if ($file['cacheFix']) {
                    $file['value'] .= (strpos($file['value'], '?') !== false ? '&' : '?') . $cacheVersion;
                }
            }
        }

        $cssFiles = ipFilter('ipCss', $cssFiles);

        $response = ipResponse();
        $data = array(
            'title' => $response->getTitle(),
            'keywords' => $response->getKeywords(),
            'description' => $response->getDescription(),
            'favicon' => $response->getFavicon(),
            'charset' => $response->getCharset(),
            'css' => $cssFiles
        );

        $head = ipView('Ip/Internal/Config/view/head.php', $data)->render();
        $head = ipFilter('ipHead', $head);
        return $head;
    }

    public function generateJavascript()
    {
        $cacheVersion = $this->getCacheVersion();
        $javascriptFiles = $this->getJavascript();
        $javascriptFilesSorted = [];
        foreach ($javascriptFiles as $level) {
            foreach ($level as &$file) {
                if ($file['type'] == 'file' && $file['cacheFix']) {
                    $file['value'] .= (strpos($file['value'], '?') !== false ? '&' : '?') . $cacheVersion;
                }
            }
            $javascriptFilesSorted = array_merge($javascriptFilesSorted, $level);
        }
        $revision = $this->getCurrentRevision();

        $page = ipContent()->getCurrentPage();


        $javascriptFilesSorted = ipFilter('ipJs', $javascriptFilesSorted);


        $language = ipContent()->getCurrentLanguage();
        $data = array(
            'ip' => array(
                'baseUrl' => ipConfig()->baseUrl(),
                'safeMode' => \Ip\Internal\Admin\Service::isSafeMode(),
                'languageId' => $language->getId(),
                'languageUrl' => $language->getLink(),
                'languageCode' => $language->getCode(),
                'languageTextDirection' => $language->getTextDirection(),
                'theme' => ipConfig()->theme(),
                'pageId' => $page ? $page->getId() : null,
                'revisionId' => $revision['revisionId'],
                'securityToken' => \Ip\ServiceLocator::application()->getSecurityToken(),
                'developmentEnvironment' => ipConfig()->isDevelopmentEnvironment(),
                'debugMode' => ipconfig()->isDebugMode(),
                'isManagementState' => ipIsManagementState(),
                'isAdminState' => ipAdminId() ? 1 : 0,
                'isAdminNavbarDisabled' => ipRequest()->getQuery('disableAdminNavbar') ? 1 : 0,
                'gmapsApiKey' => ipGetOption('Config.gmapsApiKey')
            ),
            'javascriptVariables' => $this->getJavascriptVariables(),
            'javascript' => $javascriptFilesSorted,
        );
        $javascript = ipView('Ip/Internal/Config/view/javascript.php', $data)->render();
        $javascript = ipFilter('ipJavaScript', $javascript);
        return $javascript;
    }

    protected function getCacheVersion()
    {
        return \Ip\ServiceLocator::storage()->get('Ip', 'cacheVersion', 1);
    }

    protected function getCurrentRevision()
    {
        return \Ip\ServiceLocator::content()->getCurrentRevision();
    }

    public function removeAllCss()
    {
        $this->requiredCss = [];
    }

    public function removeAllJs()
    {
        $this->requiredJavascript = [];
    }

    public function removeAllJsVariables()
    {
        $this->javascriptVariables = [];
    }
}
