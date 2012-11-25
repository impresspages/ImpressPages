<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 * @license see ip_license.html
 */
namespace Modules\administrator\repository;


class System{


    public function init(){
        global $site;

        if ($site->managementState()) {
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js');
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery-ui/jquery-ui.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/elfinder/js/elfinder.min.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/ipRepository.js');
            $site->addCss(BASE_URL.MODULE_DIR.'administrator/repository/public/elfinder/css/elfinder.min.css');
            $site->addCss(BASE_URL.MODULE_DIR.'administrator/repository/public/elfinder/css/theme.css');
            $site->addCss(BASE_URL.MODULE_DIR.'administrator/repository/public/repository.css');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/uploader.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/recent.js');
            $site->addJavascriptVar('ipRepositoryHtml', \Ip\View::create('view/popup.php')->render());
        }
    }

}


