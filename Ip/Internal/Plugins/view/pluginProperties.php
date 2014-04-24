<div class="page-header">
    <h1><?php _e('Plugin configuration', 'Ip-admin'); ?></h1>
</div>
<div class="_actions clearfix">
    <?php if ($plugin['active']) { ?>
        <button class="ipsDeactivate btn btn-new" type="button" role="button"><?php _e('Deactivate', 'Ip-admin'); ?></button>
    <?php } else { ?>
        <button class="ipsRemove btn btn-danger pull-right" type="button" role="button"><?php _e('Delete', 'Ip-admin'); ?><i class="fa fa-fw fa-trash-o"></i></button>
        <button class="ipsActivate btn btn-new" type="button" role="button"><?php _e('Activate', 'Ip-admin'); ?></button>
    <?php } ?>
</div>
<h2><?php echo $plugin['title']; ?></h2>
<p><?php echo $plugin['description']; ?></p>
<?php echo $form->render(); ?>
<button class="ipsSave btn btn-default" type="button" role="button"><?php _e('Save', 'Ip-admin'); ?></button>
