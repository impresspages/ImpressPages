<?php

namespace Ip\Internal;


class AdminPermissions
{
    protected $userId;

    protected $permissions = [];

    public function __construct()
    {
    }

    public function hasPermission($permission, $administratorId = null)
    {
        if (in_array($permission, array('InlineManagement'))) {
            $permission = 'Content';
        }

        if ($administratorId == null) {
            $administratorId = ipAdminId();
        }
        if (!isset($this->permissions[$administratorId])) {
            $this->permissions[$administratorId] = AdminPermissionsModel::getUserPermissions($administratorId);
        }

        $answer = isset($this->permissions[$administratorId][$permission]) || isset($this->permissions[$administratorId]['Super admin']);
        $answer = ipFilter(
            'ipAdminPermission',
            $answer,
            array('permission' => $permission, 'administratorId' => $administratorId)
        );

        return $answer;
    }
}
