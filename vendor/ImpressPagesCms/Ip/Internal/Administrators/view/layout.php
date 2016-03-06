<div ng-app="Administrators" ng-controller="ipAdministratorsController" class="ipModuleAdministrators ipsModuleAdministrators">
    <div class="_menu">
        <div class="_actions">
            <button class="btn btn-new" ng-click="addModal()"><i class="fa fa-plus"></i> <?php _e('Add', 'Ip-admin'); ?></button>
        </div>
        <ul>
            <li ng-repeat="administrator in administrators" ng-class="[administrator.id == activeAdministrator.id ? 'active' : '']" ng-cloak>
                <a href="" ng-click="activateAdministrator(administrator)">{{administrator.username}}</a>
            </li>
        </ul>
    </div>
    <div class="page-header">
        <h1><?php _e('Administrator profile', 'Ip-admin'); ?></h1>
    </div>
    <div ng-show="activeAdministrator" ng-cloak>
        <div class="_actions clearfix">
            <button class="btn btn-danger pull-right" role="button" ng-show="activeAdministrator.id != ipAdministratorsAdminId" ng-click="deleteModal()"><?php _e('Delete', 'Ip-admin'); ?><i class="fa fa-fw fa-trash-o"></i></button>
            <button class="btn btn-new" role="button" ng-click="updateModal()"><?php _e('Edit', 'Ip-admin'); ?> <i class="fa fa-fw fa-edit"></i></button>
        </div>
        <div ng-show="!editMode">
            <h2><?php _e('General', 'Ip-admin'); ?></h2>
            <div class="row">
                <div class="col-md-5">
                    <h3><?php _e('Username', 'Ip-admin'); ?></h3>
                    <p>{{activeAdministrator.username}}</p>
                </div>
                <div class="col-md-7">
                    <h3><?php _e('Email', 'Ip-admin'); ?></h3>
                    <p>{{activeAdministrator.email}}</p>
                </div>
            </div>

            <h2><?php _e('Permissions', 'Ip-admin'); ?></h2>
            <div class="checkbox" ng-click="setPermission(permission, !activeAdministrator.permissions[permission])" ng-show="permission == 'Super admin' || !activeAdministrator.permissions['Super admin']" ng-repeat="permission in availablePermissions">
                <label>
                    <div class="bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-mini" ng-class="[activeAdministrator.permissions[permission] ? 'bootstrap-switch-on' : 'bootstrap-switch-off']">
                        <div class="bootstrap-switch-container">
                            <span class="bootstrap-switch-handle-on bootstrap-switch-new">&nbsp;</span>
                            <span class="bootstrap-switch-label"><span class="fa fa-circle"></span></span>
                            <span class="bootstrap-switch-handle-off bootstrap-switch-default">&nbsp;</span>
                        </div>
                    </div>
                    {{permission}}
                </label>
            </div>
        </div>
        <div ng-show="editMode">
            <?php echo $updateForm->render(); ?>
        </div>
    </div>
    <?php echo ipView('Ip/Internal/Administrators/view/addModal.php', array('createForm' => $createForm))->render(); ?>
    <?php echo ipView('Ip/Internal/Administrators/view/updateModal.php', array('updateForm' => $updateForm))->render(); ?>
    <?php echo ipView('Ip/Internal/Administrators/view/deleteModal.php', array('updateForm' => $updateForm))->render(); ?>
</div>
