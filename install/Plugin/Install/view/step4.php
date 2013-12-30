<h1><?php _e('Choose interface language', 'ipInstall') ?></h1>

<div class="errorContainer"></div>
<form onsubmit="return false;">
    <p><strong><?php _e('Website name', 'ipInstall') ?></strong><input id="configSiteName" type="text" name="siteName"></p>
    <p><strong><?php _e('Website e-mail address', 'ipInstall') ?></strong><input id="configSiteEmail" type="text" name="siteEmail"></p>
    <p><strong><?php _e('Administrator login', 'ipInstall') ?></strong><input id="config_login" type="text" name="install_login"></p>
    <p><strong><?php _e('Administrator password', 'ipInstall') ?></strong><input id="config_pass" type="password" name="install_pass"></p>
    <p><strong><?php _e('E-mail for error reporting (optional)', 'ipInstall') ?></strong><input id="config_email" type="text" name="email" ></p>
    <p><strong><?php _e('Please choose website time zone', 'ipInstall') ?></strong>
        <select id="config_timezone" name="config_timezone">
            <?php echo $timezoneSelectOptions ?>
        </select>
    </p>

</form>
<a class="button_act" href="#" ><?php _e('Next', 'ipInstall') ?></a>
<a class="button" href="?step=3"><?php _e('Back', 'ipInstall') ?></a>
