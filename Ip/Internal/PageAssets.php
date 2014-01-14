<?php
/**
 * @package ImpressPages
 */

namespace Ip\Internal;

class PageAssets
{
    /** array js variables */
    private $javascriptVariables = array();

    /** array required javascript files */
    private $requiredJavascript = array();

    /** array required css files */
    private $requiredCss = array();

    public function addCss($file, $attributes = array(), $stage = 50, $cacheFix = true)
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
        $cssFiles = array();
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


    public function addJavascript($file, $attributes = array(), $stage = 50, $cacheFix = true)
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

        if (!empty($data['ipDesign']['pCfg']) && (defined(
                    'IP_ALLOW_PUBLIC_THEME_CONFIG'
                ) || isset($_REQUEST['ipDesignPreview']))
        ) {
            $config = \Ip\Internal\Design\ConfigModel::instance();
            $inDesignPreview = $config->isInPreviewState();
        }

        if ($inDesignPreview) {
            foreach ($cssFiles as &$file) {
                $path = pathinfo($file['value']);
                if ($path['dirname'] . '/' == ipThemeFile('') && file_exists(
                        ipThemeFile($path['filename'] . '.less')
                    )
                ) {
                    $designService = \Ip\Internal\Design\Service::instance();
                    $file = $designService->getRealTimeUrl(ipConfig()->theme(), $path['filename']);
                } else {
                    if ($file['cacheFix']) {
                        $file['value'] .= (strpos($file['value'], '?') !== false ? '&' : '?') . $cacheVersion;
                    }
                }
            }
        } else {
            foreach ($cssFiles as &$file) {
                if ($file['cacheFix']) {
                    $file['value'] .= (strpos($file['value'], '?') !== false ? '&' : '?') . $cacheVersion;
                }
            }
        }

        $response = ipResponse();
        $data = array(
            'title' => $response->getTitle(),
            'keywords' => $response->getKeywords(),
            'description' => $response->getDescription(),
            'favicon' => $response->getFavicon(),
            'charset' => $response->getCharset(),
            'css' => $cssFiles
        );

        return ipView(ipFile('Ip/Internal/Config/view/head.php'), $data)->render();
    }

    public function generateJavascript()
    {
        $cacheVersion = $this->getCacheVersion();
        $javascriptFiles = $this->getJavascript();
        foreach ($javascriptFiles as &$level) {
            foreach ($level as &$file) {
                if ($file['type'] == 'file' && $file['cacheFix']) {
                    $file['value'] .= (strpos($file['value'], '?') !== false ? '&' : '?') . $cacheVersion;
                }
            }
        }
        $revision = $this->getCurrentRevision();
        $data = array(
            'ip' => array(
                'baseUrl' => ipConfig()->baseUrl(),
                'languageId' => ipContent()->getCurrentLanguage()->getId(),
                'languageUrl' => \Ip\Internal\Deprecated\Url::generate(),
                'theme' => ipConfig()->getRaw('THEME'),
                'zoneName' => ipContent()->getCurrentZone() ? ipContent()->getCurrentZone()->getName() : null,
                'pageId' => ipContent()->getCurrentPage() ? ipContent()->getCurrentPage()->getId() : null,
                'revisionId' => $revision['revisionId'],
                'securityToken' => \Ip\ServiceLocator::application()->getSecurityToken(),
                'developmentEnvironment' => ipConfig()->isDevelopmentEnvironment(),
                'debugMode' => ipconfig()->isDebugMode()
            ),
            'javascriptVariables' => $this->getJavascriptVariables(),
            'javascript' => $javascriptFiles,
        );
        return ipView(ipFile('Ip/Internal/Config/view/javascript.php'), $data)->render();
    }

    protected function getCacheVersion()
    {
        return \Ip\ServiceLocator::storage()->get('Ip', 'cacheVersion', 1);
    }

    protected function getCurrentRevision()
    {
        return \Ip\ServiceLocator::content()->getCurrentRevision();
    }
}