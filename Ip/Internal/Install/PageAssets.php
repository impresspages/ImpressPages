<?php


namespace Ip\Internal\Install;


class PageAssets extends \Ip\Internal\PageAssets
{
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


        $data = array(
            'ip' => array(
                'baseUrl' => ipConfig()->baseUrl(),
                'languageId' => null,
                'languageUrl' => '',
                'theme' => ipConfig()->get('theme'),
                'pageId' => null,
                'securityToken' => \Ip\ServiceLocator::application()->getSecurityToken(),
                'developmentEnvironment' => ipConfig()->isDevelopmentEnvironment(),
                'debugMode' => ipconfig()->isDebugMode(),
                'isManagementState' => false,
                'isAdminState' => false,
                'isAdminNavbarDisabled' => false
            ),
            'javascriptVariables' => $this->getJavascriptVariables(),
            'javascript' => $javascriptFilesSorted,
        );
        return ipView(ipFile('Ip/Internal/Config/view/javascript.php'), $data)->render();
    }

    protected function getCacheVersion()
    {
        return 1;
    }

    protected function getCurrentRevision()
    {
        return array(
            'revisionId' => 1,
        );
    }

}
