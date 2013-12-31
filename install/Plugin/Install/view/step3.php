<h1><?php _e('Database installation', 'ipInstall') ?></h1>

<div class="errorContainer"></div>
<form role="form" onsubmit="return false">
    <div class="form-group">
        <label for="db_server"><?php _e('Database Host (eg. localhost or 127.0.0.1)', 'ipInstall'); ?></label>
        <input type="text" class="form-control" id="db_server" name="server" value="<?php echo htmlspecialchars($db['hostname']); ?>">
    </div>
    <div class="form-group">
        <label for="db_user"><?php _e('User name', 'ipInstall'); ?></label>
        <input type="text" class="form-control" id="db_user" name="db_user" value="<?php echo htmlspecialchars($db['username']); ?>">
    </div>
    <div class="form-group">
        <label for="db_pass"><?php _e('User password', 'ipInstall'); ?></label>
        <input type="password" class="form-control" id="db_pass" name="db_pass" value="<?php echo htmlspecialchars($db['password']); ?>">
    </div>
    <div class="form-group">
        <label for="db_db"><?php _e('Database name', 'ipInstall'); ?></label>
        <input type="text" class="form-control" id="db_db" name="db" value="<?php echo htmlspecialchars($db['database']); ?>">
    </div>
    <div class="form-group">
        <label for="db_prefix"><?php _e('Table prefix (use underscore to separate prefix).', 'ipInstall'); ?></label>
        <input type="text" maxlength="7" class="form-control" id="db_prefix" name="prefix" value="<?php echo htmlspecialchars($db['tablePrefix']); ?>">
    </div>
    <p class="alert alert-warning"><?php _e('Important!!! All old tables with the same prefix will be deleted!', 'ipInstall') ?></p>
</form>
<p class="text-right">
    <a class="btn btn-default" href="?step=2"><?php _e('Back', 'ipInstall') ?></a>
    <a class="btn btn-primary ipsStep3" href="#"><?php _e('Next', 'ipInstall') ?></a>
</p>
