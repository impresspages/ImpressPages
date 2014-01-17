<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Email;

class Db{

    public static function getEmail($id)
    {
        return ipDb()->selectRow('*', 'email_queue', array('id' => $id));
    }

    public static function addEmail($from, $fromName, $to, $toName, $subject, $email, $immediate, $html, $filesStr, $fileNamesStr, $mimeTypesStr){
        return ipDb()->insert('email_queue', array(
                'from' => $from,
                'from_name' => $fromName,
                'to' => $to,
                'to_name' => $toName,
                'subject' => $subject,
                'email' => $email,
                'immediate' => $immediate ? 1 : 0,
                'html' => $html,
                'files' => $filesStr,
                'file_names' => $fileNamesStr,
                'file_mime_types' => $mimeTypesStr,
            ));
    }

    public static function lock($count, $key)
    {
        $table = ipTable('email_queue');

        $sql = "update $table set
		`lock` = ?, `locked_on` = NOW()
		where `lock` is NULL and send is NULL order by
		immediate desc, id asc limit ".$count;

        return ipDb()->execute($sql, array($key));
    }

    public static function lockOnlyImmediate($count, $key)
    {
        $table = ipTable('email_queue');

        $sql = "update $table set
		`lock` = ?, `locked_on` = NOW()
		where `immediate` and `lock` is NULL and `send` is NULL order by
		`id` asc limit ".$count;

        return ipDb()->execute($sql, array($key));
    }

    public static function unlock($key){
        return ipDb()->update('email_queue', array(
                'send' => date('Y-m-d H:i:s'),
                'lock' => NULL,
                'locked_on' => NULL,
            ), array(
                'lock' => $key
            ));
    }

    public static function unlockOne($id)
    {
        return ipDb()->update('email_queue', array(
                'send' => date('Y-m-d H:i:s'),
                'lock' => NULL,
                'locked_on' => NULL,
            ), array(
                'id' => $id,
            ));
    }


    public static function getLocked($key)
    {
        return ipDb()->selectAll('*', 'email_queue', array('lock' => $key));
    }

    public static function markSend($key)
    {
        return ipDb()->update('email_queue', array(
                'send' => date('Y-m-d H:i:s'),
            ), array(
                'lock' => $key
            ));
    }

    public static function delteOldSent($hours)
    {
        $table = ipTable('email_queue');
        $sql = "delete from $table where
		`send` is not NULL
		and ".((int)$hours)." < TIMESTAMPDIFF(HOUR,`send`,NOW())";
        return ipDb()->execute($sql);
    }

    /*apparently there were some errors if exists old locked records. */
    public static function deleteOld($hours)
    {
        $table = ipTable('email_queue');
        $sql = "delete from $table where
		(`lock` is not NULL and ".((int)$hours)." < TIMESTAMPDIFF(HOUR,`locked_on`,NOW()))
		or
		(`send` is not NULL and ".((int)$hours)." < TIMESTAMPDIFF(HOUR,`send`,NOW()))
		";

        return ipDb()->execute($sql);
    }

    public static function sentOrLockedCount($minutes)
    {
        $table = ipTable('email_queue');
        $sql = "select count(*) as `sent` from $table where
		(`send` is not NULL and ".((int)$minutes)." > TIMESTAMPDIFF(MINUTE,`send`,NOW()))
		or
		(`lock` is not NULL and send is null) ";

        return ipDb()->fetchValue($sql);
    }

}



