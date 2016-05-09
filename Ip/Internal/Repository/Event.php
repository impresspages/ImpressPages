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

        if (ipIsManagementState() || ipRoute()->isAdmin() || ipRequest()->getQuery('ipDesignPreview')) {
            ipAddJs('Ip/Internal/Core/assets/js/jquery-ui/jquery-ui.js');
            ipAddJs('Ip/Internal/Repository/assets/ipRepository.js');
            ipAddJs('Ip/Internal/Repository/assets/ipRepositoryUploader.js');
            ipAddJs('Ip/Internal/Repository/assets/ipRepositoryAll.js');
            ipAddJs('Ip/Internal/Repository/assets/ipRepositoryBuy.js');
            ipAddJs('Ip/Internal/System/assets/market.js');
            ipAddJs('Ip/Internal/Core/assets/js/easyXDM/easyXDM.min.js');

            $marketUrl = ipConfig()->get('imageMarketUrl', ipConfig()->protocol() . '://market.impresspages.org/images-v1/');

            $popupData = array(
                'marketUrl' => $marketUrl,
                'allowUpload' => ipAdminPermission('Repository upload'),
                'allowRepository' => ipAdminPermission('Repository')
            );

            ipAddJsVariable('ipRepositoryHtml', ipView('view/popup.php', $popupData)->render());
            ipAddJsVariable(
                'ipRepositoryTranslate_confirm_delete',
                __('Are you sure you want to delete selected files?', 'Ip-admin')
            );
            ipAddJsVariable(
                'ipRepositoryTranslate_delete_warning',
                __('Some of the selected files are still used somewhere on your website. Do you still want to remove them? ', 'Ip-admin')
            );
        }


    }

}


