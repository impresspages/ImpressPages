<?php
/**
 * @package   ImpressPages
 */


namespace Ip\Internal\Update;


class Migration {


    //CHANGE_ON_VERSION_UPDATE



    public static function update_14()
    {
        $widgetTable = ipTable('widget');
        $instanceTable = ipTable('widgetInstance');
        $sql = "
           RENAME TABLE  $instanceTable TO  $widgetTable ;
        ";
        ipDb()->execute($sql);
    }



    public static function update_13()
    {
        $widgetTable = ipTable('widget');
        $sql = "
           DROP TABLE $widgetTable
        ";
        ipDb()->execute($sql);
    }

    public static function update_12()
    {
        $instanceTable = ipTable('widgetInstance');
        $sql = "
            ALTER TABLE $instanceTable DROP `widgetId`
        ";
        ipDb()->execute($sql);

    }

    public static function update_11()
    {
        $widgetTable = ipTable('widget', 'widget');
        $instanceTable = ipTable('widgetInstance', 'instance');
        $sql = "
            UPDATE $widgetTable, $instanceTable
            SET
            `instance`.`name` = `widget`.`name`,
            `instance`.`skin` = `widget`.`skin`,
            `instance`.`data` = `widget`.`data`
            WHERE
            `instance`.`widgetId` = `widget`.`id`
        ";
        ipDb()->execute($sql);

    }

    public static function update_10()
    {
        $table = ipTable('widgetInstance');
        $sql = "
            ALTER TABLE  $table
            ADD  `name` VARCHAR( 50 ) NOT NULL AFTER  `id` ,
            ADD  `skin` VARCHAR( 25 ) NOT NULL AFTER  `name` ,
            ADD  `data` TEXT NOT NULL AFTER  `skin`
        ";
        ipDb()->execute($sql);

    }


    public static function update_9()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.0.4"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_8()
    {
        ipDb()->update('storage', array('value' => '"4.0.3"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    /**
     * Rename Title widget to Heading widget.
     */
    public static function update_7()
    {
        ipDb()->update('widget', array('name' => 'Heading'), array('name' => 'Title'));
        ipDb()->update('widgetOrder', array('widgetName' => 'Heading'), array('widgetName' => 'Title'));
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);

        ipDb()->update('storage', array('value' => '"4.0.2"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_5()
    {
        //remove old installation script which is insecure.
        $installFile = ipFile('install/index.php');
        if (is_file($installFile) && is_writable($installFile)) {
            unlink($installFile);
        }
    }


    public static function update_4()
    {
        ipDb()->update('storage', array('value' => '"4.0.1"'), array('key' => 'version', 'plugin' => 'Ip'));
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
                \Ip\Internal\AdminPermissionsModel::addPermission('Super admin', $administrator['id']);
            }
        }
    }

    public static function update_2()
    {
        $table = ipTable('widget');
        $sql = "ALTER TABLE $table CHANGE  `layout`  `skin` VARCHAR( 25 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL";
        ipDb()->execute($sql);
    }





}
