<?php
/**
 * @package ImpressPages
 *
 *
 */

namespace Modules\administrator\administrators;

class AdminController extends \Ip\Controller {
    public function addPermissions() {
        if (!isset($_POST['module_group']) || !isset($_POST['module'])) {
            echo 'error'; return;
        }
        $module = \Ip\Deprecated\Db::getModule(null, $_POST['module_group'], $_POST['module']);

        $users = \Ip\Deprecated\Db::getAllUsers();
        foreach ($users as $user) {
            \Ip\Deprecated\Db::addPermissions($user['id'], $module['id']);
        }
        exit;
    }
}