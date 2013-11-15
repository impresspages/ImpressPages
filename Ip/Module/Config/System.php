<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Config;


class System{


    public function init() {
        \Ip\ServiceLocator::getDispatcher()->bind('site.beforeError404', array($this, 'catchConfig'));
    }
    
    public function catchConfig(\Ip\Event $event) {
        global $site;
        
        switch($site->getZoneUrl()) {
            case 'validatorConfig.js':
                $site->setOutput($this::generateValidatorConfig());
                $this->setJsHeader();
                $event->addProcessed();
                break;
            default:
                //do nothing;
        }
        if ($site->getZoneUrl() == 'ipConfig.js') {
        }
        
    }
    
    private function generateValidatorConfig() {
        global $site;
        $data = array(
            'languageCode' => $site->getCurrentLanguage()->getCode()
        );
        return \Ip\View::create('jquerytools/validator.js', $data)->render();
    }
    
    private function setJsHeader() {
        header("Content-type: application/x-javascript");
        $secondsToCache = 3600; //one hour
        $ts = gmdate("D, d M Y H:i:s", time() + $secondsToCache) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$secondsToCache");
    }
}