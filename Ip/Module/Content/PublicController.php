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
        if (
            \Ip\ServiceLocator::getContent()->getLanguageUrl() != ipGetCurrentlanguage()->getUrl() ||
            ipGetCurrentPage()->getType() === 'error404'
        ) {
            return new \Ip\Response\PageNotFound();
        }

        if (in_array(ipGetCurrentPage()->getType(), array('subpage', 'redirect')) && !ipIsManagementState()) {
            return new \Ip\Response\Redirect(ipGetCurrentPage()->getLink());
        }

        ipSetBlockContent('main', ipGetCurrentPage()->generateContent());
        if (\Ip\Module\Admin\Service::isSafeMode()) {
            ipSetLayout(\Ip\Config::coreModuleFile('Admin/View/safeModeLayout.php'));
        }

    }
}