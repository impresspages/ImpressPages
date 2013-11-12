<h1><?php echo __('Database installation', 'ipInstall') ?></h1>

<div class="errorContainer"></div>
<form onsubmit="return false">
    <p><strong><?php echo __('Database Host (eg. localhost or 127.0.0.1)', 'ipInstall') ?></strong><input id="db_server" type="text" name="server" value="<?php echo htmlspecialchars($db['hostname']) ?>"></p>
    <p><strong><?php echo __('User name', 'ipInstall') ?></strong><input id="db_user" type="text" name="db_user" value="<?php echo htmlspecialchars($db['username']) ?>"></p>
    <p><strong><?php echo __('User password', 'ipInstall') ?></strong><input id="db_pass" type="password" name="db_pass" value="<?php echo htmlspecialchars($db['password']) ?>"></p>
    <p><strong><?php echo __('DATABASE_NAME', 'ipInstall') ?></strong><input id="db_db" type="text" name="db" value="<?php echo htmlspecialchars($db['database']) ?>"></p>
    <p><strong><?php echo __('Table prefix (use underscore to separate prefix).', 'ipInstall') ?></strong><input id="db_prefix" maxlength="7" type="text" name="prefix" value="<?php echo htmlspecialchars($db['tablePrefix']) ?>"></p>

</form>
<p><?php echo __('Attention!!! All old tables with the same prefix will be deleted!', 'ipInstall') ?></p>
<a class="button_act" href="#"><?php echo __('Next', 'ipInstall') ?></a>
<a class="button" href="?step=2"><?php echo __('Back', 'ipInstall') ?></a>