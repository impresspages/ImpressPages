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
        $module = \Db::getModule(null, $_POST['module_group'], $_POST['module']);

        $users = \Db::getAllUsers();
        foreach ($users as $user) {
            \Db::addPermissions($user['id'], $module['id']);
        }
        exit;
    }
}