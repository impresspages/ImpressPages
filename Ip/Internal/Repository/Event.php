<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Repository;


class Event
{
    public static function ipInit()
    {

        if (ipIsManagementState()) {
            ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/jquery-ui/jquery-ui.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Repository/assets/admin/ipRepository.js'));
            ipAddCss(ipFileUrl('Ip/Internal/Repository/assets/admin/repository.css'));
            ipAddCss(ipFileUrl('Ip/Internal/Ip/assets/fonts/font-awesome/font-awesome.css'));
            ipAddJs(ipFileUrl('Ip/Internal/Repository/assets/admin/ipRepositoryUploader.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Repository/assets/admin/ipRepositoryAll.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Repository/assets/admin/ipRepositoryBuy.js'));
            ipAddJs(ipFileUrl('Ip/Internal/System/assets/market.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/easyXDM/easyXDM.min.js'));

            if (defined('TEST_MARKET_URL')) {
                $marketUrl = TEST_MARKET_URL.'images-v1/';
            } else {
                $marketUrl = 'http://market.impresspages.org/images-v1/';
            }

            $popupData = array(
                'marketUrl' => $marketUrl
            );

            ipAddJsVariable('ipRepositoryHtml', ipView('view/popup.php', $popupData)->render());
            ipAddJsVariable('ipRepositoryTranslate_confirm_delete', __('Are you sure you want to delete selected files?', 'ipAdmin'));
            ipAddJsVariable('ipRepositoryTranslate_delete_warning', __('Some of the selected files cannot be deleted because they are used.', 'ipAdmin'));
        }


    }

}


