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

        //redirect if needed
        if (in_array($currentPage->getType(), array('subpage', 'redirect')) && !ipIsManagementState()) {
            return new \Ip\Response\Redirect($currentPage->getLink());
        }

        //change layout if safe mode
        if (\Ip\Module\Admin\Service::isSafeMode()) {
            ipSetLayout(ipFile('Ip/Module/Admin/view/safeModeLayout.php'));
        } else {
            ipSetLayout(Service::getPageLayout($currentPage));
        }

        //initialize management
        ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/content.js'));
        if (ipIsManagementState()) {
            $this->initManagement();
        } else {
            if (\Ip\Module\Admin\Backend::userId()) {
                //user has access to the backend
                ipAddJavascriptVariable('ipContentShowEditButton', 1);
            }
        }

        //show error404 page if needed
        if (
            ipContent()->getLanguageUrl() != ipContent()->getCurrentLanguage()->getUrl() ||
            $currentPage instanceof \Ip\Page404
        ) {
            return new \Ip\Response\PageNotFound();
        }

        //show page content
        $response = ipResponse();
        $response->setDescription(\Ip\ServiceLocator::content()->getDescription());
        $response->setKeywords(ipContent()->getKeywords());
        $response->setTitle(ipContent()->getTitle());


        return $currentPage->generateContent();
    }

    private function initManagement()
    {
        $widgets = Service::getAvailableWidgets();
        $snippets = array();
        foreach($widgets as $widget) {
            $snippets = array_merge($snippets, $widget->adminSnippets());
        }
        ipAddJavascriptVariable('ipWidgetSnippets', $snippets);

        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/tinymce/pastePreprocess.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/tinymce/default.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/tinymce/table.js'));

        ipAddCss(ipFileUrl('Ip/Module/Ip/assets/bootstrap/bootstrap.css'));
        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/bootstrap/bootstrap.js'));


        ipAddJavascriptVariable('ipContentInit', Model::initManagementData());

        if (ipConfig()->getRaw('DEBUG_MODE')) {
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/management/ipContentManagement.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/management/jquery.ip.contentManagement.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/management/jquery.ip.pageOptions.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/management/jquery.ip.widgetbutton.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/management/jquery.ip.layoutModal.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/management/jquery.ip.block.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/management/jquery.ip.widget.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/management/exampleContent.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/management/drag.js'));

            ipAddJavascript(ipFileUrl('Ip/Module/Content/Widget/IpColumns/assets/IpColumns.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/Widget/IpFaq/assets/IpFaq.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/Widget/IpFile/assets/IpFile.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/Widget/IpForm/assets/IpForm.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/Widget/IpForm/assets/IpFormContainer.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/Widget/IpForm/assets/IpFormField.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/Widget/IpForm/assets/IpFormOptions.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/Widget/IpHtml/assets/IpHtml.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/Widget/IpImage/assets/IpImage.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/Widget/IpImageGallery/assets/IpImageGallery.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/Widget/IpTable/assets/IpTable.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/Widget/IpText/assets/IpText.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/Widget/IpTextImage/assets/IpTextImage.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/Widget/IpTitle/assets/IpTitle.js'));

        } else {
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/management.min.js'));
        }


        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/js/jquery-ui/jquery-ui.js'));
        ipAddCss(ipFileUrl('Ip/Module/Ip/assets/js/jquery-ui/jquery-ui.css'));

        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/js/jquery-tools/jquery.tools.ui.scrollable.js'));

        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/js/tiny_mce/jquery.tinymce.min.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/js/tiny_mce/tinymce.min.js'));

        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/js/plupload/plupload.full.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/js/plupload/plupload.browserplus.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/js/plupload/plupload.gears.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/js/plupload/jquery.plupload.queue/jquery.plupload.queue.js'));


        ipAddJavascript(ipFileUrl('Ip/Module/Upload/assets/jquery.ip.uploadImage.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Upload/assets/jquery.ip.uploadFile.js'));

        ipAddCss(ipFileUrl('Ip/Module/Content/assets/widgets.css'));
        ipAddJavascriptVariable('isMobile', \Ip\Internal\Browser::isMobile());



        if (ipIsManagementState()) {
            ipAddJavascriptVariable('ipWidgetLayoutModalTemplate', \Ip\View::create('view/widgetLayoutModal.php')->render());
        }

    }

}