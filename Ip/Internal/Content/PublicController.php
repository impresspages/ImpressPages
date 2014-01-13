<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Content;


class PublicController extends \Ip\Controller
{
    public function index()
    {
        //find current page
        $currentPage = ipCurrentPage();

        $zone = ipContent()->getZone($currentPage->get('zone'));

        if (!$zone) {
            return new \Ip\Response\PageNotFound();
        }

        $page = $zone->getCurrentPage();
        $currentPage->_set('page', $page);

        // redirect if needed
        if (in_array($page->getType(), array('subpage', 'redirect')) && !ipIsManagementState()) {
            return new \Ip\Response\Redirect($page->getLink());
        }

        // change layout if safe mode
        if (\Ip\Internal\Admin\Service::isSafeMode()) {
            ipSetLayout(ipFile('Ip/Internal/Admin/view/safeModeLayout.php'));
        } else {
            ipSetLayout(Service::getPageLayout($page));
        }

        // initialize management
        ipAddJs(ipFileUrl('Ip/Internal/Content/assets/managementMode.js'));
        if (ipIsManagementState()) {
            $this->initManagement();
        } else {
            if (\Ip\Internal\Admin\Backend::userId()) {
                //user has access to the backend
                ipAddJsVariable('ipContentShowEditButton', 1);
            }
        }

        //show page content
        $response = ipResponse();
        $response->setDescription(\Ip\ServiceLocator::content()->getDescription());
        $response->setKeywords(ipContent()->getKeywords());
        $response->setTitle(ipContent()->getTitle());

        return $page->generateContent();
    }

    private function initManagement()
    {
        $widgets = Service::getAvailableWidgets();
        $snippets = array();
        foreach ($widgets as $widget) {
            $snippets = array_merge($snippets, $widget->adminSnippets());
        }
        ipAddJsVariable('ipWidgetSnippets', $snippets);

        ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/tinymce/pastePreprocess.js'));
        ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/tinymce/default.js'));
        ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/tinymce/table.js'));

        ipAddCss(ipFileUrl('Ip/Internal/Ip/assets/bootstrap/bootstrap.css'));
        ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/bootstrap/bootstrap.js'));


        ipAddJsVariable('ipContentInit', Model::initManagementData());

        if (ipConfig()->isDebugMode()) {
            ipAddJs(ipFileUrl('Ip/Internal/Content/assets/management/ipContentManagementInit.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/assets/management/content.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/assets/management/jquery.ip.contentManagement.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/assets/management/jquery.ip.widgetbutton.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/assets/management/jquery.ip.layoutModal.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/assets/management/jquery.ip.block.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/assets/management/jquery.ip.widget.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/assets/management/exampleContent.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/assets/management/drag.js'));

            ipAddJs(ipFileUrl('Ip/Internal/Content/Widget/IpColumns/assets/IpColumns.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/Widget/IpFaq/assets/IpFaq.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/Widget/IpFile/assets/IpFile.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/Widget/IpForm/assets/IpForm.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/Widget/IpForm/assets/IpFormContainer.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/Widget/IpForm/assets/IpFormField.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/Widget/IpForm/assets/IpFormOptions.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/Widget/IpHtml/assets/IpHtml.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/Widget/IpImage/assets/IpImage.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/Widget/IpImageGallery/assets/IpImageGallery.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/Widget/IpTable/assets/IpTable.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/Widget/IpText/assets/IpText.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/Widget/IpTextImage/assets/IpTextImage.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/Widget/IpTitle/assets/IpTitle.js'));
            ipAddJs(ipFileUrl('Ip/Internal/Content/Widget/IpTitle/assets/IpTitleModal.js'));

        } else {
            ipAddJs(ipFileUrl('Ip/Internal/Content/assets/management.min.js'));
        }


        ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/jquery-ui/jquery-ui.js'));
        ipAddCss(ipFileUrl('Ip/Internal/Ip/assets/js/jquery-ui/jquery-ui.css'));

        ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/jquery-tools/jquery.tools.ui.scrollable.js'));

        ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/tiny_mce/jquery.tinymce.min.js'));
        ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/tiny_mce/tinymce.min.js'));

        ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/plupload/plupload.full.js'));
        ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/plupload/plupload.browserplus.js'));
        ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/plupload/plupload.gears.js'));
        ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/plupload/jquery.plupload.queue/jquery.plupload.queue.js'));


        ipAddJs(ipFileUrl('Ip/Internal/Upload/assets/jquery.ip.uploadImage.js'));
        ipAddJs(ipFileUrl('Ip/Internal/Upload/assets/jquery.ip.uploadFile.js'));

        ipAddCss(ipFileUrl('Ip/Internal/Content/assets/widgets.css'));
        ipAddJsVariable('isMobile', \Ip\Internal\Browser::isMobile());


        if (ipIsManagementState()) {
            ipAddJsVariable(
                'ipWidgetLayoutModalTemplate',
                ipView('view/widgetLayoutModal.php')->render()
            );
			ipAddJsVariable(
                'ipBrowseLinkModalTemplate',
                ipView('view/browseLinkModal.php')->render()
            );
			ipAddCss(ipFileUrl('Ip/Internal/Ip/assets/js/jstree/themes/default/style.min.css'));
			ipAddJs(ipFileUrl('Ip/Internal/Ip/assets/js/jstree/jstree.js'));
        }

    }

}