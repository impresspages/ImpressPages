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
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/ipRepository.js');
            $site->addCss(BASE_URL.MODULE_DIR.'administrator/repository/public/repository.css');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/ipRepositoryUploader.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/ipRepositoryAll.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/ipRepositoryBuy.js');

            $marketUrl = 'http://market.impresspages.org/en/images-v1/';
            $popupData = array(
                'marketUrl' => $marketUrl
            );

            $site->addJavascriptVar('ipRepositoryHtml', \Ip\View::create('view/popup.php', $popupData)->render());
        }
    }

}


