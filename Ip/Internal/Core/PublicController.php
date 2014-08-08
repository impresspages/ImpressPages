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
        return new \Ip\Response\PageNotFound();
    }
}
