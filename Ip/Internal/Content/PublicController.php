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
        $page = ipContent()->getCurrentPage();

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
        if (ipIsManagementState()) {
            if (!ipRequest()->getQuery('ipDesignPreview')) {
                Helper::initManagement();
            }
        }

        //show page content
        $response = ipResponse();
        $response->setDescription(\Ip\ServiceLocator::content()->getDescription());
        $response->setKeywords(ipContent()->getKeywords());
        $response->setTitle(ipContent()->getTitle());

        $revision = \Ip\ServiceLocator::content()->getCurrentRevision();
        if ($revision) {
            return \Ip\Internal\Content\Model::generateBlock('main', $revision['revisionId'], 0, ipIsManagementState());
        } else {
            return '';
        }

    }




}
