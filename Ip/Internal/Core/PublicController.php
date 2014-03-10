<?php
/**
 * @package   ImpressPages
 */


namespace Ip\Internal\Core;


class PublicController extends \Ip\Controller {
    /**
     * Dummy function used to preserve user session
     */
    public function ping()
    {
        return new \Ip\Response\Json(array(1));
    }
}
