<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2012 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */

namespace IpUpdate\Library\Migration\To2_0rc2;


class Script extends \IpUpdate\Library\Migration\General{
    
    private $conn;
    
    public function process($cf)
    {
        $db = new \IpUpdate\Library\Model\Db();
        $conn = $db->connect($cf, \IpUpdate\Library\Model\Db::DRIVER_MYSQL);
        $this->conn = $conn;

        $parametersRefractor = new \ParametersRefractor();

        $module = \Db_100::getModule(null, 'standard', 'content_management');

        $group = $parametersRefractor->getParametersGroup($module['id'], 'admin_translations');
        if ($group) {
            if(!\Db_100::getParameter('standard', 'menu_management', 'admin_translations', 'default')) {
                \Db_100::addStringParameter($group['id'], 'Default layout', 'default', 'Default', 1);
            }


        }

    }

    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getSourceVersion()
     */
    public function getSourceVersion()
    {
        return '2.0rc1';
    }

    /**
     * (non-PHPdoc)
     * @see IpUpdate\Library\Migration.General::getDestinationVersion()
     */
    public function getDestinationVersion()
    {
        return '2.0rc2';
    }

}
