<div ng-app="Administrators" ng-controller="ipAdministratorsController">
    <div class="ipModuleAdministrators">
        <div ng-repeat="administrator in administrators" ng-click="activateAdministrator(administrator)" ng-class="[administrator.id == activeAdministrator.id ? 'active' : '',  'panel', 'panel-default']">
            <div class="panel-heading">{{administrator.username}}</div>
            <div class="panel-body">
                <b><?php //echo esc($administrator['username']) ?></b>
            </div>
        </div>
        <a ng-click="addAdministratorModal()"><?php echo __('Add', 'ipAdmin'); ?></a>
    </div>
    <div ng-show="activeAdministrator">

    </div>
</div>
<?php echo ipView('Ip/Internal/Administrators/view/addModal.php', array('createForm' => $createForm))->render(); ?>
