<?php
/**
 * @package   ImpressPages
 */


/**
 * Created by PhpStorm.
 * User: mangirdas
 * Date: 14.10.28
 * Time: 19.02
 */

namespace Ip\Internal\InlineManagement;


class Model
{
    public static function updateUrl($oldUrl, $newUrl)
    {
        self::updateTableUrl($oldUrl, $newUrl, 'inline_value_global', array('plugin', 'key'));
        self::updateTableUrl($oldUrl, $newUrl, 'inline_value_language', array('plugin', 'key', 'languageId'));
        self::updateTableUrl($oldUrl, $newUrl, 'inline_value_page', array('plugin', 'key', 'pageId'));
    }

    protected static function updateTableUrl($oldUrl, $newUrl, $table, $keyFields)
    {
        $old = parse_url($oldUrl);
        $new = parse_url($newUrl);

        $oldPart = $old['host'] . $old['path'];
        $newPart = $new['host'] . $new['path'];

        $quotedPart = substr(ipDb()->getConnection()->quote('://' . $oldPart), 1, -1);

        $search = '%'. addslashes($quotedPart) . '%';

        $tableWithPrefix = ipTable($table);

        $records = ipDb()->fetchAll("SELECT * FROM $tableWithPrefix WHERE `value` LIKE ?", array($search));

        if (!$records) {
            return;
        }

        if ($newUrl == ipConfig()->baseUrl()) {
            //the website has been moved

            $search = '%\b(https?://)' . preg_quote($oldPart, '%') . '%';
        } else {
            //internal page url has changed

            // \b - start at word boundary
            // (https?://) - protocol
            // (/?) - allow optional slash at the end of url
            // (?= ) - symbols expected after url
            // \Z - end of subject or end of line
            $search = '%\b(https?://)' . preg_quote($oldPart, '%') . '(/?)(?=["\'?]|\s|\Z)%';
        }

        foreach ($records as $row) {

            // ${1} - protocol, ${2} - optional '/'
            $after = preg_replace($search, '${1}' . $newPart . '${2}', $row['value']);
            if ($after != $row['value']) {
                $where = [];
                foreach($keyFields as $keyField) {
                    $where[$keyField] = $row[$keyField];
                }
                ipDb()->update($table, array('value' => $after), $where);
            }
        }
    }
}
