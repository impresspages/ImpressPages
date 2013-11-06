<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Repository;


class System{


    public function init(){
        $site = \Ip\ServiceLocator::getSite();
        $parametersMod = \Ip\ServiceLocator::getParametersMod();

        if ($site->managementState()) {
            $site->addJavascript(\Ip\Config::libraryUrl('js/jquery/jquery.js'));
            $site->addJavascript(\Ip\Config::libraryUrl('js/jquery-ui/jquery-ui.js'));
            $site->addJavascript(\Ip\Config::coreModuleUrl('Repository/public/admin/ipRepository.js'));
            $site->addCss(\Ip\Config::coreModuleUrl('Repository/public/admin/repository.css'));
            $site->addCss(\Ip\Config::libraryUrl('fonts/font-awesome/font-awesome.css'));
            $site->addJavascript(\Ip\Config::coreModuleUrl('Repository/public/admin/ipRepositoryUploader.js'));
            $site->addJavascript(\Ip\Config::coreModuleUrl('Repository/public/admin/ipRepositoryAll.js'));
            $site->addJavascript(\Ip\Config::coreModuleUrl('Repository/public/admin/ipRepositoryBuy.js'));
            $site->addJavascript(\Ip\Config::coreModuleUrl('System/public/market.js'));
            $site->addJavascript(\Ip\Config::libraryUrl('js/easyXDM/easyXDM.min.js'));

            if (defined('TEST_MARKET_URL')) {
                $marketUrl = TEST_MARKET_URL.'images-v1/';
            } else {
                $marketUrl = 'http://market.impresspages.org/images-v1/';
            }

            $popupData = array(
                'marketUrl' => $marketUrl
            );

            $site->addJavascriptVariable('ipRepositoryHtml', \Ip\View::create('view/popup.php', $popupData)->render());
            $site->addJavascriptVariable('ipRepositoryTranslate_confirm_delete', $parametersMod->getValue('Repository.confirm_delete'));
            $site->addJavascriptVariable('ipRepositoryTranslate_delete_warning', $parametersMod->getValue('Repository.delete_warning'));
        }


    }

}


