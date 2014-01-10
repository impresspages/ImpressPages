<?php
/**
 * @package   ImpressPages
 */

namespace Plugin\Install;


class LayoutResponse extends \Ip\Response\Layout
{
    public function send()
    {
        ipContent()->setBlockContent('main', $this->content);


        if ($this->getLayout()) {
            $layout = $this->getLayout();
        } else {
            $layout = 'main.php';
        }

        if ($layout[0] == '/' || $layout[1] == ':') { // Check if absolute path: '/' for unix, 'C:' for windows
            $viewFile = $layout;
        } else {
            $viewFile = ipThemeFile($layout);
        }
        $this->setContent(ipView($viewFile, $this->getLayoutVariables())->render());

        $this->output();
    }

    private function output()
    {
        $headers = $this->getHeaders();
        foreach($headers as $header) {
            header($header);
        }
        if ($this->getStatusCode()) {
            if (function_exists('http_response_code')) {
                http_response_code($this->getStatusCode());
            } else {
                header('X-Ignore-This: workaround', true, $this->getStatusCode());
            }
        }
        echo $this->getContent();
    }

    public function generateHead() {
        $cacheVersion = \Ip\Application::getVersion();
        $cssFiles = $this->getCss();

        foreach($cssFiles as &$file) {
            if ($file['cacheFix']) {
                $file['value'] .= (strpos($file['value'], '?') !== false ? '&' : '?') . $cacheVersion;
            }
        }


        $data = array (
            'title' => $this->getTitle(),
            'keywords' => $this->getKeywords(),
            'description' => $this->getDescription(),
            'favicon' => $this->getFavicon(),
            'charset' => $this->getCharset(),
            'css' => $cssFiles
        );

        return ipView(ipFile('Ip/Internal/Config/view/head.php'), $data)->render();
    }

    public function generateJavascript() {
        $cacheVersion = \Ip\Application::getVersion();
        $javascriptFiles = $this->getJavascript();
        foreach($javascriptFiles as &$level) {
            foreach($level as &$file) {
                if ($file['type'] == 'file' && $file['cacheFix']) {
                    $file['value'] .= (strpos($file['value'], '?') !== false ? '&' : '?') . $cacheVersion;
                }
            }
        }

        $data = array (
            'ip' => array(
                'baseUrl' => ipConfig()->baseUrl(),
                'languageId' => null,
                'languageUrl' => null,
                'theme' => ipConfig()->getRaw('THEME'),
                'zoneName' => null,
                'pageId' => null,
                'revisionId' => null,
                'securityToken' => \Ip\ServiceLocator::application()->getSecurityToken(),
                'developmentEnvironment' => ipConfig()->isDevelopmentEnvironment(),
                'debugMode' => ipconfig()->isDebugMode(),
                'managementState' => false
            ),
            'javascriptVariables' => $this->getJavascriptVariables(),
            'javascript' => $javascriptFiles,
        );

        return ipView(ipFile('Ip/Internal/Config/view/javascript.php'), $data)->render();
    }
}