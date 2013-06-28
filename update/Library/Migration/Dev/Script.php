<?php
/**
 * @package   ImpressPages
 *
 *
 */

namespace IpUpdate\Library\Migration\Dev;

/**
 * This is where all migrations of new features should be placed until version number is unknown.
 * This migration is being executed always. No mather which version you are updating to.
 * Before official release all content from process function should be moved to release migration script.
 */
class Script extends \IpUpdate\Library\Migration\General{

    private $conn;
    private $dbPref;

    public function process($cf)
    {










    }

    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getSourceVersion()
     */
    public function getSourceVersion()
    {
        return 'Dev';
    }

    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getDestinationVersion()
     */
    public function getDestinationVersion()
    {
        return 'Dev';
    }



}
