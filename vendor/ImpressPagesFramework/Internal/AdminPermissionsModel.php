<?php

namespace Ip\Internal;


class AdminPermissionsModel
{
    public static function getUserPermissions($administratorId = null)
    {
        if ($administratorId === null) {
            $administratorId = ipAdminId();
        }
        $permissions = ipDb()->selectColumn('permission', 'permission', array('administratorId' => $administratorId));
        if (!empty($permissions)) {
            $permissions = array_combine($permissions, $permissions);
        }

        return $permissions;
    }

    /**
     * Get list of all available permissions on the system
     */
    public static function availablePermissions()
    {
        $permissions = array(
            'Super admin',
            'Content',
            'Pages',
            'Design',
            'Plugins',
            'Config',
            'Config advanced',
            'Languages',
            'System',
            'Administrators',
            'Log',
            'Email',
            'Repository',
            'Repository upload'
        );

        $plugins = \Ip\Internal\Plugins\Model::getActivePluginNames();
        foreach ($plugins as $plugin) {
            if (is_file(ipFile('Plugin/' . $plugin . '/AdminController.php'))) {
                array_push($permissions, $plugin);
            }
        }

        $permissions = ipFilter('ipAvailablePermissions', $permissions);
        return $permissions;
    }

    public static function removeUserPermissions($administratorId = null)
    {
        if ($administratorId === null) {
            $administratorId = ipAdminId();
        }
        ipDb()->delete('permission', array('administratorId' => $administratorId));
    }

    public static function addPermission($permission, $administratorId = null)
    {
        if ($administratorId === null) {
            $administratorId = ipAdminId();
        }
        $data = array(
            'permission' => $permission,
            'administratorId' => $administratorId
        );
        ipDb()->insert('permission', $data, true);
    }

    public static function removePermission($permission, $administratorId = null)
    {
        if ($administratorId === null) {
            $administratorId = ipAdminId();
        }
        $condition = array(
            'permission' => $permission,
            'administratorId' => $administratorId
        );
        ipDb()->delete('permission', $condition);
    }
}
