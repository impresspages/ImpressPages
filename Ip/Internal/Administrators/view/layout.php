<div ng-app="Administrators" ng-controller="ipAdministratorsController" class="ipModuleAdministrators">
    <div class="page-header">
        <h1><?php _e('Administrators', 'ipAdmin'); ?></h1>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div ng-repeat="administrator in administrators" ng-click="activateAdministrator(administrator)" ng-class="[administrator.id == activeAdministrator.id ? 'active' : '',  'panel', 'panel-default']">
                <div class="panel-heading">{{administrator.username}}</div>
                <div class="panel-body">
                    <b><?php //echo esc($administrator['username']) ?></b>
                </div>
            </div>
            <button class="btn btn-new" ng-click="addModal()"><i class="fa fa-plus"></i> <?php _e('Add', 'ipAdmin'); ?></button>
        </div>
        <div class="col-md-9" ng-show="activeAdministrator">
            <h2><?php _e('Administrator profile', 'ipAdmin'); ?></h2>
            <br/>
            <a class="btn btn-danger" ng-show="activeAdministrator.id != ipAdministratorsAdminId" ng-click="deleteModal()"><?php _e('Delete', 'ipAdmin') ?> <i class="fa fa-fw fa-trash-o"></i></a>
            <br/><br/>
            <div ng-show="!editMode">
                <b><?php _e('Username', 'ipAdmin'); ?></b> {{activeAdministrator.username}}
                <br/>
                <b><?php _e('Email', 'ipAdmin'); ?></b> {{activeAdministrator.email}}
                <br/>
                <button class="btn btn-primary" ng-click="updateModal()"><?php _e('Edit', 'ipAdmin'); ?></button>
            </div>
            <div ng-show="editMode">
                <?php echo $updateForm->render(); ?>
            </div>
        </div>
    </div>
    <?php echo ipView('Ip/Internal/Administrators/view/addModal.php', array('createForm' => $createForm))->render(); ?>
    <?php echo ipView('Ip/Internal/Administrators/view/updateModal.php', array('updateForm' => $updateForm))->render(); ?>
    <?php echo ipView('Ip/Internal/Administrators/view/deleteModal.php', array('updateForm' => $updateForm))->render(); ?>
</div>
