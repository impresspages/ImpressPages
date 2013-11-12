<h1><?php echo __('Choose interface language', 'ipInstall') ?></h1>

<div class="errorContainer"></div>
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
<a class="button_act" href="#" ><?php echo __('Next', 'ipInstall') ?></a>
<a class="button" href="?step=3"><?php echo __('Back', 'ipInstall') ?></a>
