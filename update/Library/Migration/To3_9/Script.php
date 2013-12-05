<?php
/**
 * @package   ImpressPages
 */

namespace IpUpdate\Library\Migration\To3_9;


class Script extends \IpUpdate\Library\Migration\General
{

    public function process($cf)
    {

    }


    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getSourceVersion()
     */
    public function getSourceVersion()
    {
        return '3.8';
    }


    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getDestinationVersion()
     */
    public function getDestinationVersion()
    {
        return '3.9';
    }

}
