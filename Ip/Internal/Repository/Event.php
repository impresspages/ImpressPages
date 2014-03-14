<?php
/**
 * @package ImpressPages

 *
 */
namespace Ip\Internal\Repository;


class Event
{
    public static function ipBeforeController()
    {

        if (ipIsManagementState()) {
            ipAddJs('Ip/Internal/Core/assets/js/jquery-ui/jquery-ui.js');
            ipAddJs('Ip/Internal/Repository/assets/ipRepository.js');
            ipAddJs('Ip/Internal/Repository/assets/ipRepositoryUploader.js');
            ipAddJs('Ip/Internal/Repository/assets/ipRepositoryAll.js');
            ipAddJs('Ip/Internal/Repository/assets/ipRepositoryBuy.js');
            ipAddJs('Ip/Internal/System/assets/market.js');
            ipAddJs('Ip/Internal/Core/assets/js/easyXDM/easyXDM.min.js');

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


