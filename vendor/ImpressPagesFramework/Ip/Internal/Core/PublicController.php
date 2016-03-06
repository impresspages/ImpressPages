<?php
/**
 * @package   ImpressPages
 */


namespace Ip\Internal\Core;


class PublicController extends \Ip\Controller
{
    /**
     * Dummy function used to preserve user session
     */
    public function ping()
    {
        return new \Ip\Response\Json(array(1));
    }

    public function pageNotFound()
    {
        $content = null;
        $error404Page = ipContent()->getPageByAlias('error404');
        if ($error404Page) {
            $revision = \Ip\Internal\Revision::getPublishedRevision($error404Page->getId());
            $content = \Ip\Internal\Content\Model::generateBlock('main', $revision['revisionId'], 0, 0);
        }
        return new \Ip\Response\PageNotFound($content);
    }
}
