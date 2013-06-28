<?php
/**
 * @package ImpressPages
 * @copyright   Copyright (C) 2011 ImpressPages LTD.
 *
 */
namespace Modules\administrator\repository;


class System{


    public function init(){
        global $site;

        if ($site->managementState()) {
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js');
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery-ui/jquery-ui.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/admin/ipRepository.js');
            $site->addCss(BASE_URL.MODULE_DIR.'administrator/repository/public/admin/repository.css');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/admin/ipRepositoryUploader.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/admin/ipRepositoryAll.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/admin/ipRepositoryBuy.js');

            $marketUrl = 'http://market.impresspages.org/en/images-v1/';
            $popupData = array(
                'marketUrl' => $marketUrl
            );

            $site->addJavascriptVar('ipRepositoryHtml', \Ip\View::create('view/popup.php', $popupData)->render());
        }


    }

}


