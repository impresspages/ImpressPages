<?php
/**
 * @package   ImpressPages
 */

namespace Ip\Internal\Install;


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

        if ($layout[0] == '/' || $layout[1] == ':') { // Check if absolute path: '/' for unix, 'C:' for Windows
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

}
