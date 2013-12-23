<?php

namespace Ip\Internal\Permissions;


class UserPermissions
{
    protected $userId;

    public function __construct($userId = NULL)
    {
        if ($userId === NULL) {
            $userId = \Ip\Internal\Admin\Backend::userId();
        }
        $this->userId = $userId;
    }

    public function isAllowed($plugin, $action = null, $data = NULL)
    {
        return true;
    }
}
