<div ng-app="Administrators" ng-controller="ipAdministratorsController" class="ipModuleAdministrators ipsModuleAdministrators">
    <div class="_menu">
        <div class="_actions">
            <button class="btn btn-new" ng-click="addModal()"><i class="fa fa-plus" aria-hidden="true"></i> <?php _e('Add administrator', 'Ip-admin'); ?></button>
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
            <button class="btn btn-default" role="button" ng-click="updateModal()"><?php _e('Edit', 'Ip-admin'); ?> <i class="fa fa-fw fa-edit"></i></button>
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
            <div class="ipAdminPermission" ng-show="permission == 'Super admin' || !activeAdministrator.permissions['Super admin']" ng-repeat="permission in availablePermissions">
                <button type="button" class="ipAdminSwitch" role="switch" id="ipAdminPermission{{$index}}"
                        aria-checked="{{activeAdministrator.permissions[permission] ? 'true' : 'false'}}"
                        ng-class="{_on: activeAdministrator.permissions[permission]}"
                        ng-click="setPermission(permission, !activeAdministrator.permissions[permission])"><span class="_knob"></span></button>
                <label for="ipAdminPermission{{$index}}">{{permission}}</label>
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
