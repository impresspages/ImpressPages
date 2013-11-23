<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Module\Content;


class PublicController extends \Ip\Controller
{
    public function index()
    {
        $currentPage = ipContent()->getCurrentPage();

        if (
            ipContent()->getLanguageUrl() != ipContent()->getCurrentLanguage()->getUrl() ||
            $currentPage->getType() === 'error404'
        ) {
            return new \Ip\Response\PageNotFound();
        }

        if (in_array($currentPage->getType(), array('subpage', 'redirect')) && !ipIsManagementState()) {
            return new \Ip\Response\Redirect($currentPage->getLink());
        }

        if (\Ip\Module\Admin\Service::isSafeMode()) {
            ipSetLayout(ipConfig()->coreModuleFile('Admin/view/safeModeLayout.php'));
        } else {
            ipSetLayout(Service::getPageLayout($currentPage));
        }

        ipAddJavascript(ipConfig()->coreModuleUrl('Content/assets/content.js'));

        if (ipIsManagementState()) {
            $this->initManagement();
        } else {
            if (\Ip\Module\Admin\Backend::userId()) {
                //user has access to the backend
                ipAddJavascriptVariable('ipContentShowEditButton', 1);
            }
        }

        return $currentPage->generateContent();
    }

    private function initManagement()
    {

        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/tinymce/paste_preprocess.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/tinymce/min.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/tinymce/med.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/tinymce/max.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/tinymce/table.js'));


        ipAddJavascriptVariable('ipContentInit', Model::initManagementData());

        ipAddJavascript(ipConfig()->coreModuleUrl('Content/assets/ipContentManagement.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Content/assets/jquery.ip.contentManagement.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Content/assets/jquery.ip.pageOptions.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Content/assets/jquery.ip.widgetbutton.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Content/assets/jquery.ip.block.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Content/assets/jquery.ip.widget.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Content/assets/exampleContent.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Content/assets/drag.js'));


        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/jquery-ui/jquery-ui.js'));
        ipAddCss(ipConfig()->coreModuleUrl('Assets/assets/js/jquery-ui/jquery-ui.css'));

        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/jquery-tools/jquery.tools.ui.scrollable.js'));

        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/tiny_mce/jquery.tinymce.js'));

        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/plupload/plupload.full.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/plupload/plupload.browserplus.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/plupload/plupload.gears.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Assets/assets/js/plupload/jquery.plupload.queue/jquery.plupload.queue.js'));


        ipAddJavascript(ipConfig()->coreModuleUrl('Upload/assets/jquery.ip.uploadImage.js'));
        ipAddJavascript(ipConfig()->coreModuleUrl('Upload/assets/jquery.ip.uploadFile.js'));

        ipAddCss(ipConfig()->coreModuleUrl('Content/assets/widgets.css'));
        ipAddJavascriptVariable('isMobile', \Ip\Browser::isMobile());

    }

}