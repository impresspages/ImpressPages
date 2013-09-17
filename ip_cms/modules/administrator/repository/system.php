<?php
/**
 * @package ImpressPages

 *
 */
namespace Modules\administrator\repository;


class System{


    public function init(){
        $site = \Ip\ServiceLocator::getSite();
        $parametersMod = \Ip\ServiceLocator::getParametersMod();

        if ($site->managementState()) {
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery/jquery.js');
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/jquery-ui/jquery-ui.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/admin/ipRepository.js');
            $site->addCss(BASE_URL.MODULE_DIR.'administrator/repository/public/admin/repository.css');
            $site->addCss(BASE_URL.LIBRARY_DIR.'fonts/font-awesome/font-awesome.css');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/admin/ipRepositoryUploader.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/admin/ipRepositoryAll.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/repository/public/admin/ipRepositoryBuy.js');
            $site->addJavascript(BASE_URL.MODULE_DIR.'administrator/system/public/market.js');
            $site->addJavascript(BASE_URL.LIBRARY_DIR.'js/easyXDM/easyXDM.min.js');

            if (defined('TEST_MARKET_URL')) {
                $marketUrl = TEST_MARKET_URL.'images-v1/';
            } else {
                $marketUrl = 'http://market.impresspages.org/images-v1/';
            }

            $popupData = array(
                'marketUrl' => $marketUrl
            );

            $site->addJavascriptVariable('ipRepositoryHtml', \Ip\View::create('view/popup.php', $popupData)->render());
            $site->addJavascriptVariable('ipRepositoryTranslate_confirm_delete', $parametersMod->getValue('administrator', 'repository', 'admin_translations', 'confirm_delete'));
            $site->addJavascriptVariable('ipRepositoryTranslate_delete_warning', $parametersMod->getValue('administrator', 'repository', 'admin_translations', 'delete_warning'));
        }


    }

}


