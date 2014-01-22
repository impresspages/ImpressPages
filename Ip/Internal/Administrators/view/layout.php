<div class="ipModuleAdministrators" ng-app="Administrators" ng-controller="ipAdministratorsController" >
        <div ng-repeat="administrator in administrators" class="panel panel-default">
            <div class="panel-heading">{{administrator.username}}</div>
            <div class="panel-body">
                <b><?php //echo esc($administrator['username']) ?></b>
            </div>
        </div>
    <a class="ipsAdd"><?php echo __('Add', 'ipAdmin'); ?></a>
</div>
<?php echo ipView('Ip/Internal/Administrators/view/addModal.php', array('createForm' => $createForm))->render(); ?>
