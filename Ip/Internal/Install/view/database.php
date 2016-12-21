<h1><?php _e('Database installation', 'Install'); ?></h1>

<div class="ipsErrorContainer"></div>
<form role="form" class="ipsDatabaseForm">
    <div class="form-group">
        <label for="db_server"><?php _e('Database Host (eg. localhost or 127.0.0.1)', 'Install'); ?></label>
        <input type="text" class="form-control" id="db_server" name="server" value="<?php echo htmlspecialchars($db['hostname']); ?>">
    </div>
    <div class="form-group">
        <label for="db_user"><?php _e('User name', 'Install'); ?></label>
        <input type="text" class="form-control" id="db_user" autocomplete="off" name="db_user" value="<?php echo htmlspecialchars($db['username']); ?>">
    </div>
    <div class="form-group">
        <label for="db_pass"><?php _e('User password', 'Install'); ?></label>
        <input type="password" class="form-control" id="db_pass" autocomplete="off" name="db_pass" value="<?php echo htmlspecialchars($db['password']); ?>">
    </div>
    <div class="form-group">
        <label for="db_db"><?php _e('Database name', 'Install'); ?></label>
        <input type="text" class="form-control" id="db_db" name="db" value="<?php echo htmlspecialchars($db['database']); ?>">
    </div>
    <div class="form-group">
        <label for="db_prefix"><?php _e('Table prefix (use underscore to separate prefix).', 'Install'); ?></label>
        <input type="text" maxlength="7" class="form-control" id="db_prefix" name="prefix" value="<?php echo htmlspecialchars($db['tablePrefix']); ?>">
    </div>
    <p class="text-right">
        <button type="submit" class="btn btn-primary ipsDatabaseSubmit"><?php _e('Next', 'Install'); ?></button>
    </p>
</form>
