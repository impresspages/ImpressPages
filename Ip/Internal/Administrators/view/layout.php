<div ng-app="Administrators" ng-controller="ipAdministratorsController" class="ipModuleAdministrators">
    <div class="ipmList">
        <div ng-repeat="administrator in administrators" ng-click="activateAdministrator(administrator)" ng-class="[administrator.id == activeAdministrator.id ? 'active' : '',  'panel', 'panel-default']">
            <div class="panel-heading">{{administrator.username}}</div>
            <div class="panel-body">
                <b><?php //echo esc($administrator['username']) ?></b>
            </div>
        </div>
        <a ng-click="addModal()"><?php echo __('Add', 'ipAdmin'); ?></a>
    </div>
    <div class="ipmAdministrator" ng-show="activeAdministrator">
        <h1><?php _e('Administrator profile', 'ipAdmin') ?></h1>
        <br/>
        <a><?php _e('Delete', 'ipAdmin') ?></a>
        <br/><br/>
        <div ng-show="!editMode">
            <b><?php _e('Username', 'ipAdmin') ?></b> {{activeAdministrator.username}}
            <br/>
            <b><?php _e('Email', 'ipAdmin') ?></b> {{activeAdministrator.email}}
            <br/>
            <a ng-click="updateModal()"><?php _e('Edit', 'ipAdmin') ?></a>
        </div>
        <div ng-show="editMode">
            <?php echo $updateForm->render() ?>
        </div>
    </div>
    <?php echo ipView('Ip/Internal/Administrators/view/addModal.php', array('createForm' => $createForm))->render(); ?>
    <?php echo ipView('Ip/Internal/Administrators/view/updateModal.php', array('updateForm' => $updateForm))->render(); ?>
</div>
