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

        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/tinymce/paste_preprocess.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/tinymce/min.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/tinymce/med.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/tinymce/max.js'));
        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/tinymce/table.js'));

        ipAddCss(ipFileUrl('Ip/Module/Ip/assets/bootstrap/bootstrap.css'));
        ipAddJavascript(ipFileUrl('Ip/Module/Ip/assets/bootstrap/bootstrap.js'));


        ipAddJavascriptVariable('ipContentInit', Model::initManagementData());

        if (ipConfig()->getRaw('DEBUG_MODE')) {
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/src/ipContentManagement.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/src/jquery.ip.contentManagement.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/src/jquery.ip.pageOptions.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/src/jquery.ip.widgetbutton.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/src/jquery.ip.layoutModal.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/src/jquery.ip.block.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/src/jquery.ip.widget.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/src/exampleContent.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/src/drag.js'));
        } else {
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/content.min.js'));
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



        if (ipConfig()->getRaw('DEBUG_MODE')) {
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpColumns.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpFaq.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpFile.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpForm.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpFormContainer.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpFormField.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpFormOptions.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpHtml.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpImage.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpImageGallery.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpTable.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpText.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpTextImage.js'));
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.src/IpTitle.js'));
        } else {
            ipAddJavascript(ipFileUrl('Ip/Module/Content/assets/widget.admin.min.js'));
        }

        if (ipIsManagementState()) {
            ipAddJavascriptVariable('ipWidgetLayoutModalTemplate', \Ip\View::create('view/widgetLayoutModal.php')->render());
        }

    }

}