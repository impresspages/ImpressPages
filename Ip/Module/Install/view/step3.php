<h1><?php echo __('Database installation', 'ipInstall') ?></h1>

<div id="errorAllFields" class="noDisplay"><p class="error"><?php echo __('Please fill in all fields', 'ipInstall') ?></p></div>
<div id="errorConnect" class="noDisplay"><p class="error"><?php echo __('Can\'t connect to database', 'ipInstall') ?></p></div>
<div id="errorDb" class="noDisplay"><p class="error"><?php echo __('Specified database does not exists', 'ipInstall') ?></p></div>
<div id="errorQuery" class="noDisplay"><p class="error"><?php echo __('Unknown SQL error', 'ipInstall') ?></p></div>
<div id="errorLongPrefix" class="noDisplay"><p class="error"><?php echo __('Prefix can\'t be longer than 7 symbols', 'ipInstall') ?></p></div>
<div id="errorIncorrectPrefix" class="noDisplay"><p class="error"><?php echo __('Prefix can\'t contain any special characters and should sart with letter', 'ipInstall') ?></p></div>
<form onsubmit="return false">
    <p><strong><?php echo __('Database Host (eg. localhost or 127.0.0.1)', 'ipInstall') ?></strong><input id="db_server" type="text" name="server" value="<?php echo $_SESSION['db_server'] ?>"></p>
    <p><strong><?php echo __('User name', 'ipInstall') ?></strong><input id="db_user" type="text" name="db_user" value="<?php echo $_SESSION['db_user'] ?>"></p>
    <p><strong><?php echo __('User password', 'ipInstall') ?></strong><input id="db_pass" type="password" name="db_pass" value="<?php echo $_SESSION['db_pass'] ?>"></p>
    <p><strong><?php echo __('DATABASE_NAME', 'ipInstall') ?></strong><input id="db_db" type="text" name="db" value="<?php echo $_SESSION['db_db'] ?>"></p>
    <p><strong><?php echo __('Table prefix (use underscore to separate prefix).', 'ipInstall') ?></strong><input id="db_prefix" maxlength="7" type="text" name="prefix" value="<?php echo $_SESSION['db_prefix'] ?>"></p>

</form>
<p><?php echo __('Attention!!! All old tables with the same prefix will be deleted!', 'ipInstall') ?></p>
<a class="button_act" href="#"><?php echo __('Next', 'ipInstall') ?></a>
<a class="button" href="?step=2"><?php echo __('Back', 'ipInstall') ?></a>