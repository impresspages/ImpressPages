<?php
/**
 * @package ImpressPages
 *
 */
namespace Ip\Internal\Core;


class AdminController extends \Ip\Controller
{

    public function getPageUrl()
    {
        $url = '';
        $success = 1;
        $pageId = ipRequest()->getQuery('pageId');
        if (!$pageId) {
            throw new \Ip\Exception("Required parameter is missing");
        }
        $page = ipContent()->getPage($pageId);
        if ($page) {
            $url = $page->getLink();
        } else {
            $success = false;
        }
        $answer = array(
            'url' => $url,
            'success' => $success
        );

        return new \Ip\Response\Json($answer);
    }

}
