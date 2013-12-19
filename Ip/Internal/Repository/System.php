<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Repository;


class System{


    public function init(){

        if (\Ip\ServiceLocator::content()->isManagementState()) {
            ipAddJavascript(ipFileUrl('Ip/Internal/Ip/assets/js/jquery-ui/jquery-ui.js'));
            ipAddJavascript(ipFileUrl('Ip/Internal/Repository/assets/admin/ipRepository.js'));
            ipAddCss(ipFileUrl('Ip/Internal/Repository/assets/admin/repository.css'));
            ipAddCss(ipFileUrl('Ip/Internal/Ip/assets/fonts/font-awesome/font-awesome.css'));
            ipAddJavascript(ipFileUrl('Ip/Internal/Repository/assets/admin/ipRepositoryUploader.js'));
            ipAddJavascript(ipFileUrl('Ip/Internal/Repository/assets/admin/ipRepositoryAll.js'));
            ipAddJavascript(ipFileUrl('Ip/Internal/Repository/assets/admin/ipRepositoryBuy.js'));
            ipAddJavascript(ipFileUrl('Ip/Internal/System/assets/market.js'));
            ipAddJavascript(ipFileUrl('Ip/Internal/Ip/assets/js/easyXDM/easyXDM.min.js'));

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


