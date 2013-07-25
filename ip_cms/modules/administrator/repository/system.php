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
            $site->addCss(BASE_URL.LIBRARY_DIR.'fonts/font-awesome/font-awesome.css');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/admin/ipRepositoryUploader.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/admin/ipRepositoryAll.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/admin/ipRepositoryBuy.js');
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/easyXDM/easyXDM.min.js');

            if (defined('TEST_MARKET_URL')) {
                $marketUrl = TEST_MARKET_URL.'en/images-v1/';
            } else {
                $marketUrl = 'http://market.impresspages.org/en/images-v1/';
            }

            $popupData = array(
                'marketUrl' => $marketUrl
            );

            $site->addJavascriptVar('ipRepositoryHtml', \Ip\View::create('view/popup.php', $popupData)->render());
        }


    }

}


