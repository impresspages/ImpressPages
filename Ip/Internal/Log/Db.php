<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip\Internal\Log;

class Db
{
	public static function deleteOldLogs($days) 
	{
		if (!ipDb()->isPgSQL()) {
			Db::deleteOldLogsMySQL($days);
		} else {
			Db::deleteOldLogsPgSQL($days);
		}
	}
	
    public static function deleteOldLogsMySQL($days)
    {
        $logTable = ipTable('log');
        ipDb()->execute(
            "delete from $logTable where  (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`time`)) > ?",
            array($days * 24 * 60 * 60)
        );
    }
    
    public static function deleteOldLogsPgSQL($days)
    {
    	$logTable = ipTable('log');
    	ipDb()->execute(
    	"delete from $logTable where  date_part('days', now() - time) > ?",
    	array($days)
    	);
    }

}



