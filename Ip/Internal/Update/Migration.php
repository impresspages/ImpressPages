<?php
/**
 * @package   ImpressPages
 */


namespace Ip\Internal\Update;


class Migration
{


    //CHANGE_ON_VERSION_UPDATE
    public static function update_101()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"5.0.3"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_100()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"5.0.2"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_99()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"5.0.1"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_98()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"5.0.0"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_97()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.10.1"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_96()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.10.0"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_95()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.9.0"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_94()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.8.1"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_93()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.8.0"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_92()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.7.1"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_91()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.7.0"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_90()
    {
        ipStorage()->set('Ip', 'upgradedFrom4.6.6', 1); // Map widget acts differently if system is upgraded from 4.6.6
    }

    public static function update_89()
    {
        ipSetOption('Config.gmapsApiKey', ''); // introducing Google Maps API key configuration
    }

    public static function update_88()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.6.6"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_87()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.6.5"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_86()
    {
        ipDb()->execute("ALTER TABLE " . ipTable('email_queue') ." CHANGE `lockedAt` `lockedAt` TIMESTAMP NULL DEFAULT NULL");
    }

    public static function update_85()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.6.4"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_84()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.6.3"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_83()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.6.2"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_82()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.6.1"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_81()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.6.0"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_80()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.5.2"'), array('key' => 'version', 'plugin' => 'Ip'));
    }



    public static function update_79()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.5.1"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_78()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.5.0"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_77()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.4.2"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_76()
    {
        ipSetOption('Content.widgetHeadingMaxLevel', 3);
    }

    public static function update_75()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.4.1"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_74()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.4.0"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_73()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.3.0"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_72()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.2.9"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_71()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.2.8"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_70()
    {
        ipSetOption('Config.trailingSlash', 0); // to keep the system as it was before. New default value is 1;
    }

    public static function update_69()
    {
        //this change was already applied in 60 version. But installation script wasn't changed accordingly. So we are reexecuting this query for installations between 60 and 69
        ipDb()->execute("ALTER TABLE " . ipTable('page') ." CHANGE `createdAt` `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;");
    }

    public static function update_68()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.2.7"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_67()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.2.6"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_66()
    {
        ipDb()->execute("ALTER TABLE " . ipTable('revision') ." DROP `createdAtBkp`");
    }

    public static function update_65()
    {
        ipDb()->execute("UPDATE " . ipTable('revision') ." SET `createdAt` = FROM_UNIXTIME(`createdAtBkp`) WHERE 1");
    }

    public static function update_64()
    {
        ipDb()->execute("ALTER TABLE " . ipTable('revision') ." ADD `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
    }

    public static function update_63()
    {
        ipDb()->execute("ALTER TABLE " . ipTable('revision') ." CHANGE `createdAt` `createdAtBkp` INT(11) NOT NULL");
    }



    public static function update_62()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.2.5"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_61()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.2.4"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_60()
    {
        ipDb()->execute("ALTER TABLE " . ipTable('page') ." CHANGE `createdAt` `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;");
    }

    public static function update_59()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.2.3"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_58()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.2.2"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_57()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.2.1"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_56()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.2.0"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_55()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.1.4"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_54()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.1.3"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_53()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.1.2"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_52()
    {
        $table = ipTable('repository_file');
        ipDb()->execute(
            "UPDATE  $table SET `baseDir` = 'file/repository/' "
        );
    }

    public static function update_51()
    {
        $table = ipTable('repository_file');
        ipDb()->execute(
            "ALTER TABLE  $table ADD `baseDir` VARCHAR(255) NOT NULL AFTER `plugin`"
        );
    }

    public static function update_50()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.1.1"'), array('key' => 'version', 'plugin' => 'Ip'));
    }



    public static function update_49()
    {
        ipDb()->update('storage', array('value' => '"4.1.0"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_48()
    {
        $table = ipTable('plugin');
        ipDb()->execute(
            "ALTER TABLE  $table CHANGE  `name`  `name` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL"
        );
    }


    public static function update_47()
    {
        $table = ipTable('plugin');
        ipDb()->execute(
            "ALTER TABLE  $table CHANGE  `name`  `name` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL"
        );
    }


    public static function update_46()
    {
        $table = ipTable('page');

        ipDb()->execute(
            "ALTER TABLE $table CHANGE  `createdAt`  `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP"
        );
    }


    public static function update_45()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.0.17"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_44()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.0.16"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_43()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.0.15"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_42()
    {
        ipDb()->delete('page_storage', array('key' => 'layout'));
    }

    public static function update_41()
    {
        $layouts = ipDb()->selectAll('page_storage', '*', array('key' => 'layout'));
        foreach ($layouts as $layout) {
            ipDb()->update('page', array('layout' => json_decode($layout['value'])), array('id' => $layout['pageId']));
        }
    }

    public static function update_40()
    {
        $table = ipTable('page');
        $sql = "
        ALTER TABLE  " . $table . " ADD  `layout` VARCHAR( 255 ) NULL AFTER  `alias`
        ";
        ipDb()->execute($sql);
    }

    public static function update_39()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.0.14"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_38()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.0.13"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_37()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.0.12"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_36()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.0.11"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_35()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.0.10"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_34()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.0.9"'), array('key' => 'version', 'plugin' => 'Ip'));
    }

    public static function update_33()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.0.8"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_32()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.0.7"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_31()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.0.6"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_30()
    {
        ipStorage()->set('Ip', 'cacheVersion', ipStorage()->get('Ip', 'cacheVersion', 1) + 1);
        ipDb()->update('storage', array('value' => '"4.0.5"'), array('key' => 'version', 'plugin' => 'Ip'));
    }


    public static function update_29()
    {
        $table = ipTable('repository_reflection');
        $sql = "
           ALTER TABLE $table ADD  `options` TEXT CHARACTER SET ASCII COLLATE ascii_bin NOT NULL AFTER  `reflectionId`
        ";
        ipDb()->execute($sql);
    }


    public static function update_28()
    {
        $table = ipTable('repository_reflection');
        $sql = "
           ALTER TABLE $table CHANGE  `transformFingerprint`  `optionsFingerprint` CHAR( 32 ) CHARACTER SET ASCII COLLATE ascii_bin NOT NULL COMMENT  'unique cropping options key'
        ";
        ipDb()->execute($sql);
    }


    public static function update_27()
    {
        $fromTable = ipTable('respository_file');
        $toTable = ipTable('repository_file');
        $sql = "
           RENAME TABLE  $fromTable TO  $toTable ;
        ";
        ipDb()->execute($sql);
    }

    public static function update_26()
    {
        $fromTable = ipTable('widgetOrder');
        $toTable = ipTable('widget_order');
        $sql = "
           RENAME TABLE  $fromTable TO  $toTable ;
        ";
        ipDb()->execute($sql);
    }

    public static function update_25()
    {
        $fromTable = ipTable('themeStorage');
        $toTable = ipTable('theme_storage');
        $sql = "
           RENAME TABLE  $fromTable TO  $toTable ;
        ";
        ipDb()->execute($sql);
    }

    public static function update_24()
    {
        $fromTable = ipTable('repositoryReflection');
        $toTable = ipTable('repository_reflection');
        $sql = "
           RENAME TABLE  $fromTable TO  $toTable ;
        ";
        ipDb()->execute($sql);
    }

    public static function update_23()
    {
        $fromTable = ipTable('repositoryFile');
        $toTable = ipTable('respository_file');
        $sql = "
           RENAME TABLE  $fromTable TO  $toTable ;
        ";
        ipDb()->execute($sql);
    }

    public static function update_22()
    {
        $fromTable = ipTable('pageStorage');
        $toTable = ipTable('page_storage');
        $sql = "
           RENAME TABLE  $fromTable TO  $toTable ;
        ";
        ipDb()->execute($sql);
    }

    public static function update_21()
    {
        $fromTable = ipTable('inlineValueGlobal');
        $toTable = ipTable('inline_value_global');
        $sql = "
           RENAME TABLE  $fromTable TO  $toTable ;
        ";
        ipDb()->execute($sql);
    }

    public static function update_20()
    {
        $fromTable = ipTable('inlineValueForPage');
        $toTable = ipTable('inline_value_page');
        $sql = "
           RENAME TABLE  $fromTable TO  $toTable ;
        ";
        ipDb()->execute($sql);
    }

    public static function update_19()
    {
        $fromTable = ipTable('inlineValueForLanguage');
        $toTable = ipTable('inline_value_language');
        $sql = "
           RENAME TABLE  $fromTable TO  $toTable ;
        ";
        ipDb()->execute($sql);
    }


    public static function update_18()
    {
        $fromTable = ipTable('emailQueue');
        $toTable = ipTable('email_queue');
        $sql = "
           RENAME TABLE  $fromTable TO  $toTable ;
        ";
        ipDb()->execute($sql);
    }


    public static function update_17()
    {
        $widgetTable = ipTable('widget');
        $sql = "
           UPDATE $widgetTable SET  `updatedAt` = `createdAt` WHERE 1
        ";
        ipDb()->execute($sql);
    }

    public static function update_16()
    {
        $widgetTable = ipTable('widget');
        $sql = "
           ALTER TABLE $widgetTable ADD  `updatedAt` INT NOT NULL AFTER  `createdAt`
        ";
        ipDb()->execute($sql);
    }

    public static function update_15()
    {
        $widgetTable = ipTable('widget');
        $instanceTable = ipTable('widgetInstance');
        $sql = "
           RENAME TABLE  $instanceTable TO  $widgetTable ;
        ";
        ipDb()->execute($sql);
    }


    public static function update_14()
    {
        $widgetTable = ipTable('widget');
        $sql = "
           DROP TABLE $widgetTable
        ";
        ipDb()->execute($sql);
    }


    public static function update_13()
    {
        $instanceTable = ipTable('widgetInstance');
        $sql = "
            ALTER TABLE $instanceTable DROP `widgetId`
        ";
        ipDb()->execute($sql);

    }


    public static function update_12()
    {
        $table = ipTable('widgetInstance');


        $sql = "
            UPDATE
                $table
            SET
                `data` = concat('{\"cols\":[\"column', `widgetId`, '_1\",\"column', `widgetId`, '_2\"]}')
            WHERE
                `name` = 'Columns' AND
                `data` not like '%\"cols\":%'
            ";

        ipDb()->execute($sql);


        $allRecords = ipDb()->selectAll('widgetInstance', '*');
        foreach ($allRecords as $record) {
            $sql = "
            UPDATE
                $table
            SET
                `data` = REPLACE(`data`, 'column" . (int)$record['widgetId'] . "_', 'column" . (int)$record['id'] . "_')
            WHERE
                `name` = 'Columns'
            ";

            ipDb()->execute($sql);

            $sql = "
            UPDATE
                $table
            SET
                `blockName` = REPLACE(`blockName`, 'column" . (int)$record['widgetId'] . "_', 'column" . (int)$record['id'] . "_')
            WHERE
                1
            ";

            ipDb()->execute($sql);

        }


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
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
        ";
        ipDb()->execute($sql);

        $permissions = $permissions = ipDb()->selectColumn('permission', 'permission', []);
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
