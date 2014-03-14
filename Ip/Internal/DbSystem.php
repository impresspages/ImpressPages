<?php
/**
 * @package ImpressPages
 *
 *
 */


namespace Ip\Internal;


/**
 * db class to make system operations
 * Provide some general functions.
 * @package ImpressPages
 */
class DbSystem{    //system variables

    public static function replaceUrls($oldUrl, $newUrl)
    {
        $db = ipDb();

        if ($oldUrl == '' || $newUrl == '') {
            return;
            trigger_error('Can\'t update URL');
        }

        $oldUrlParts = explode('?', $oldUrl);
        $oldUrl = $oldUrlParts[0];

        $newUrlParts = explode('?', $newUrl);
        $newUrl = $newUrlParts[0];

        $sql = 'UPDATE ' . ipTable('storage') . ' SET `value` = REPLACE(`value`, ?, ?)';
        $db->execute($sql, array($oldUrl,  $newUrl));
        
        $fromJsonUrl = json_encode($oldUrl);
        $fromJsonUrl = substr($fromJsonUrl, 1, -1);
        $toJsonUrl = json_encode($newUrl);
        $toJsonUrl = substr($toJsonUrl, 1, -1);
        
        $sql = 'UPDATE ' . ipTable('widget') . ' SET `data` = REPLACE(`data`, ?, ?)';
        $db->execute($sql, array($fromJsonUrl, $toJsonUrl));

        return true;
    }

}