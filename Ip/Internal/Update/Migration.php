<?php
/**
 * @package   ImpressPages
 */


namespace Ip\Internal\Update;


class Migration {
    public static function update_2()
    {
        $table = ipTable('widget');
        $sql = "ALTER TABLE $table CHANGE  `layout`  `skin` VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
        ipDb()->execute($sql);
    }
    public static function update_3()
    {
        $table = ipTable('permission');
        $sql = "
            CREATE TABLE IF NOT EXISTS $table (
              `administratorId` int(11) DEFAULT NULL,
              `permission` varchar(255) NOT NULL DEFAULT '',
              PRIMARY KEY (`administratorId`, `permission`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
        ";
        ipDb()->execute($sql);

        $permissions = $permissions = ipDb()->selectColumn('permission', 'permission', array());
        if (empty($permissions)) {
            $administrators = \Ip\Internal\Administrators\Model::getAll();
            foreach ($administrators as $administrator) {
                \Ip\Internal\AdminPermissionsModel::addPermission('superadmin', $administrator['id']);
            }
        }
    }
}
