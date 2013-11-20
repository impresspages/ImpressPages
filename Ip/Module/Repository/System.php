<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Module\Repository;


class System{


    public function init(){

        if (\Ip\ServiceLocator::content()->isManagementState()) {
            ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/jquery.js'));
            ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/jquery-ui/jquery-ui.js'));
            ipAddJavascript(ipConfig()->coreModuleUrl('Repository/public/admin/ipRepository.js'));
            ipAddCss(ipConfig()->coreModuleUrl('Repository/public/admin/repository.css'));
            ipAddCss(ipConfig()->coreModuleUrl('Assets/assets/fonts/font-awesome/font-awesome.css'));
            ipAddJavascript(ipConfig()->coreModuleUrl('Repository/public/admin/ipRepositoryUploader.js'));
            ipAddJavascript(ipConfig()->coreModuleUrl('Repository/public/admin/ipRepositoryAll.js'));
            ipAddJavascript(ipConfig()->coreModuleUrl('Repository/public/admin/ipRepositoryBuy.js'));
            ipAddJavascript(ipConfig()->coreModuleUrl('System/public/market.js'));
            ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/easyXDM/easyXDM.min.js'));

            if (defined('TEST_MARKET_URL')) {
                $marketUrl = TEST_MARKET_URL.'images-v1/';
            } else {
                $marketUrl = 'http://market.impresspages.org/images-v1/';
            }

            $popupData = array(
                'marketUrl' => $marketUrl
            );

            ipAddJavascriptVariable('ipRepositoryHtml', \Ip\View::create('view/popup.php', $popupData)->render());
            ipAddJavascriptVariable('ipRepositoryTranslate_confirm_delete', __('Are you sure you want to delete selected files?', 'ipAdmin'));
            ipAddJavascriptVariable('ipRepositoryTranslate_delete_warning', __('Some of the selected files cannot be deleted because they are used.', 'ipAdmin'));
        }


    }

}


