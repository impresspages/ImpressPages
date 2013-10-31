<?php
/**
 * @package ImpressPages

 *
 */

namespace Ip\Module\Config;


class PublicController extends \Ip\Controller{


    public function init() {
        header("Content-type: application/x-javascript");
        $secondsToCache = 3600; //one hour
        $ts = gmdate("D, d M Y H:i:s", time() + $secondsToCache) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$secondsToCache");        
        
    }

    
    function tinymceConfig() {
        global $site;
        $data = array();
        $answer = '';
        $answer .= \Ip\View::create('tinymce/paste_preprocess.js', $data)->render();
        $answer .= \Ip\View::create('tinymce/min.js', $data)->render();
        $answer .= \Ip\View::create('tinymce/med.js', $data)->render();
        $answer .= \Ip\View::create('tinymce/max.js', $data)->render();
        $answer .= \Ip\View::create('tinymce/table.js', $data)->render();
        $site->setOutput($answer);
    }

    function validatorConfig() {
        global $site;
        $data = array(
            'languageCode' => $site->getCurrentLanguage()->getCode()
        );
        $answer = '';
        $answer .= \Ip\View::create('jquerytools/validator.js', $data)->render();
        $site->setOutput($answer);
    }    
}