<h1><?php echo __('Choose interface language', 'ipInstall') ?></h1>

<div id="errorSiteName" class="noDisplay"><p class="error"><?php echo __('Please enter website name.', 'ipInstall') ?></p></div>
<div id="errorSiteEmail" class="noDisplay"><p class="error"><?php echo __('Please enter correct website email.', 'ipInstall') ?></p></div>
<div id="errorLogin" class="noDisplay"><p class="error"><?php echo __('Please enter administrator login and password.', 'ipInstall') ?></p></div>
<div id="errorEmail" class="noDisplay"><p class="error"><?php echo __('Please enter correct administrator e-mail address.', 'ipInstall') ?></p></div>
<div id="errorConfig" class="noDisplay"><p class="error"><?php echo __('Can\'t write configuration "/ip_config.php"', 'ipInstall') ?></p></div>
<div id="errorRobots" class="noDisplay"><p class="error"><?php echo __('Can\'t write "/robots.txt"', 'ipInstall') ?></p></div>
<div id="errorTimeZone" class="noDisplay"><p class="error"><?php echo __('Please choose website time zone.', 'ipInstall') ?></p></div>
<div id="errorConnect" class="noDisplay"><p class="error"><?php echo __('Can\'t connect to database.', 'ipInstall') ?></p></div>
<div id="errorDb" class="noDisplay"><p class="error"><?php echo __('Specified database does not exists.', 'ipInstall') ?></p></div>
<div id="errorQuery" class="noDisplay"><p class="error"><?php echo __('Unknown SQL error.', 'ipInstall') ?></p></div>
<form onsubmit="return false;">
    <p><strong><?php echo __('Website name', 'ipInstall') ?></strong><input id="config_site_name" type="text" name="site_name"></p>
    <p><strong><?php echo __('Website e-mail address', 'ipInstall') ?></strong><input id="config_site_email" type="text" name="site_email"></p>
    <p><strong><?php echo __('Administrator login', 'ipInstall') ?></strong><input id="config_login" type="text" name="install_login"></p>
    <p><strong><?php echo __('Administrator password', 'ipInstall') ?></strong><input id="config_pass" type="password" name="install_pass"></p>
    <p><strong><?php echo __('E-mail for error reporting (optional)', 'ipInstall') ?></strong><input id="config_email" type="text" name="email" ></p>
    <p><strong><?php echo __('Please choose website time zone', 'ipInstall') ?></strong>
        <select id="config_timezone" name="config_timezone">
            <?php echo $timezoneSelectOptions ?>
        </select>
    </p>

</form>
<a class="button_act" href="#" ><?php echo __('IP_NEXT', 'ipInstall') ?></a>
<a class="button" href="?step=3"><?php echo __('IP_BACK', 'ipInstall') ?></a>
