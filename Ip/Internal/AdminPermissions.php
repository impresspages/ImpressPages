<?php

namespace Ip\Internal;


class AdminPermissions
{
    protected $userId;

    protected $permissions = array();

    public function __construct()
    {
    }

    public function hasPermission($permission, $administratorId = null)
    {
        if ($permission == 'Repository') {
            return true; //all admins allowed to access repository
        }

        if ($administratorId == null) {
            $administratorId = ipAdminId();
        }
        if (!isset($this->permissions[$administratorId])) {
            $this->permissions[$administratorId] = AdminPermissionsModel::getUserPermissions($administratorId);
        }
        return isset($this->permissions[$administratorId][$permission]) || isset($this->permissions[$administratorId]['Super admin']);
    }
}
