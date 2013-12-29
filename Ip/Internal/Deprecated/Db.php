<?php
/**
 * @package ImpressPages
 *
 *
 */


namespace Ip\Internal\Deprecated;

/**
 * Main db class
 * Connects to database, provide some general functions.
 * @package ImpressPages
 */
class Db
{


    /**
     * Finds information about specified module, or returns first module.
     * @param int $id module id
     * @param string $groupName
     * @param string $moduleName
     * @return array
     */
    public static function getModule($id = null, $groupName = null, $moduleName = null)
    {
        if ($id != null) {
            $sql = "select m.translation as m_translation, m.core, m.id, g.name as g_name, g.translation as g_translation, m.name as m_name, m.version from `" . DB_PREF . "module_group` g, `" . DB_PREF . "module` m where m.id = '" . ip_deprecated_mysql_real_escape_string(
                    $id
                ) . "' and  m.group_id = g.id order by g.row_number, m.row_number limit 1";
        } elseif ($groupName != null && $moduleName != null) {
            $sql = "select m.translation as m_translation, m.core, m.id, g.name as g_name, g.translation as g_translation, m.name as m_name, m.version from `" . DB_PREF . "module_group` g, `" . DB_PREF . "module` m where g.name = '" . ip_deprecated_mysql_real_escape_string(
                    $groupName
                ) . "' and m.group_id = g.id and m.name= '" . ip_deprecated_mysql_real_escape_string(
                    $moduleName
                ) . "' order by g.row_number, m.row_number limit 1";
        } else {
            $sql = "select m.translation as m_translation, m.core, m.id, g.name as g_name, g.translation as g_translation, m.name as m_name, m.version from `" . DB_PREF . "module_group` g, `" . DB_PREF . "module` m where m.group_id = g.id order by g.row_number, m.row_number limit 1";
        }
        $rs = ip_deprecated_mysql_query($sql);
        if ($rs) {
            if ($lock = ip_deprecated_mysql_fetch_assoc($rs)) {
                return $lock;
            } else {
                return false;
            }
        } else {
            trigger_error($sql . " " . ip_deprecated_mysql_error());
            return false;
        }
    }

    public static function addPermissions($userId, $moduleId)
    {
        ipDb()->insert('user_to_mod', array(
                'userId' => $userId,
                'moduleId' => $moduleId,
            ));
    }

    public static function getAllUsers()
    {
        return ipDb()->select('*', 'user');
    }

    //end parameters

}