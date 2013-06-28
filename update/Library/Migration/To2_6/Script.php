<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace IpUpdate\Library\Migration\To2_6;


class Script extends \IpUpdate\Library\Migration\General{

    public function process($cf)
    {
    }

    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getSourceVersion()
     */
    public function getSourceVersion()
    {
        return '2.5';
    }

    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getDestinationVersion()
     */
    public function getDestinationVersion()
    {
        return '2.6';
    }




}
