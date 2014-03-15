<?php

namespace Ip\Internal;


class AdminPermissionsModel
{
    public static function getUserPermissions($administratorId = null)
    {
        if ($administratorId === null) {
            $administratorId = ipAdminId();
        }
        //$permissions = ipDb()->selectColumn('permission', 'permission', array('administratorId' => $administratorId)); doesn't work at the moment #selectColumn
        $permissions = ipDb()->selectColumn('permission', 'permission', array(), ' AND `administratorId` = ' . (int) $administratorId);
        if (!empty($permissions)) {
            $permissions = array_combine($permissions, $permissions);
        }
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
        $data =  array(
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
        $condition =  array(
            'permission' => $permission,
            'administratorId' => $administratorId
        );
        ipDb()->delete('permission', $condition);
    }
}
