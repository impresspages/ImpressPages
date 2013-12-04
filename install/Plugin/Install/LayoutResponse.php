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
        $this->setContent(\Ip\View::create($viewFile, $this->getLayoutVariables())->render());

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

        return \Ip\View::create(ipFile('Ip/Module/Config/view/head.php'), $data)->render();
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
            'ipBaseUrl' => ipConfig()->baseUrl(),
            'ipLanguageId' => null,
            'ipLanguageUrl' => null,
            'ipTheme' => ipConfig()->getRaw('THEME'),
            'ipManagementUrl' => null,
            'ipZoneName' => null,
            'ipPageId' => null,
            'ipRevisionId' => null,
            'ipSecurityToken' => \Ip\ServiceLocator::application()->getSecurityToken(),
            'javascript' => $javascriptFiles,
            'javascriptVariables' => $this->getJavascriptVariables()
        );
        return \Ip\View::create(ipFile('Ip/Module/Config/view/javascript.php'), $data)->render();
    }
}