<h1><?php _e('Database installation', 'ipInstall') ?></h1>

<div class="errorContainer"></div>
<form onsubmit="return false">
    <p><strong><?php _e('Database Host (eg. localhost or 127.0.0.1)', 'ipInstall') ?></strong><input id="db_server" type="text" name="server" value="<?php echo htmlspecialchars($db['hostname']) ?>"></p>
    <p><strong><?php _e('User name', 'ipInstall') ?></strong><input id="db_user" type="text" name="db_user" value="<?php echo htmlspecialchars($db['username']) ?>"></p>
    <p><strong><?php _e('User password', 'ipInstall') ?></strong><input id="db_pass" type="password" name="db_pass" value="<?php echo htmlspecialchars($db['password']) ?>"></p>
    <p><strong><?php _e('Database name', 'ipInstall') ?></strong><input id="db_db" type="text" name="db" value="<?php echo htmlspecialchars($db['database']) ?>"></p>
    <p><strong><?php _e('Table prefix (use underscore to separate prefix).', 'ipInstall') ?></strong><input id="db_prefix" maxlength="7" type="text" name="prefix" value="<?php echo htmlspecialchars($db['tablePrefix']) ?>"></p>

</form>
<p><?php _e('Attention!!! All old tables with the same prefix will be deleted!', 'ipInstall') ?></p>
<a class="button_act" href="#"><?php _e('Next', 'ipInstall') ?></a>
<a class="button" href="?step=2"><?php _e('Back', 'ipInstall') ?></a>