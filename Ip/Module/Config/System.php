<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Config;


class System{


    public function init() {
        global $site;
        global $dispatcher;
        
        $dispatcher->bind('site.beforeError404', array($this, 'catchConfig'));
    }
    
    public function catchConfig(\Ip\Event $event) {
        global $site;
        
        switch($site->getZoneUrl()) {
            case 'tinymceConfig.js':
                $site->setOutput($this::generateTinyMceConfig());
                $this->setJsHeader();
                $event->addProcessed();
                break;
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
    

    
    private function generateTinyMceConfig() {
        $configJs = '';
        $configJs .= \Ip\View::create('tinymce/paste_preprocess.js')->render();
        $configJs .= \Ip\View::create('tinymce/min.js')->render();
        $configJs .= \Ip\View::create('tinymce/med.js')->render();
        $configJs .= \Ip\View::create('tinymce/max.js')->render();
        $configJs .= \Ip\View::create('tinymce/table.js')->render();
        return $configJs;
    }
    
    private function generateValidatorConfig() {
        global $site;
        $configJs = '';
        $data = array(
            'languageCode' => $site->getCurrentLanguage()->getCode()
        );
        $configJs = '';
        $configJs .= \Ip\View::create('jquerytools/validator.js', $data)->render();
        return $configJs;
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