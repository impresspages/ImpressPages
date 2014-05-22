<h1><?php _e('Website configuration', 'Install'); ?></h1>

<div class="ipsErrorContainer"></div>
<form role="form" class="ipsConfigurationForm">
    <div class="form-group">
        <label for="ipsConfigWebsiteName"><?php _e('Website name', 'Install'); ?></label>
        <input type="text" class="form-control" id="ipsConfigWebsiteName" name="configWebsiteName" value="<?php echo htmlspecialchars($config['websiteName']); ?>">
    </div>
    <div class="form-group">
        <label for="ipsConfigWebsiteEmail"><?php _e('E-mail (username)', 'Install'); ?></label>
        <input type="email" class="form-control" id="ipsConfigWebsiteEmail" name="configWebsiteEmail" value="<?php echo htmlspecialchars($config['websiteEmail']); ?>">
    </div>
    <div class="form-group">
        <label for="ipsConfigTimezone"><?php _e('Time zone', 'Install'); ?></label>
        <select class="form-control" id="ipsConfigTimezone" name="configTimezone">
            <?php echo $timezoneSelectOptions; ?>
        </select>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" id="ipsConfigSupport" name="configSupport"<?php if ($config['support']) { echo ' checked="checked"'; } ?>> <?php _e('Yes, I want to get ImpressPages help and support', 'Install'); ?>
        </label>
    </div>
    <p>
        <?php echo sprintf(__('By proceeding you agree with <a href="#" class="%s">Terms of Use</a>.', 'Install', false), 'ipsTOS'); ?>
        <a href="#" class="ipsMoreConfiguration"><?php _e('More configuration options', 'Install'); ?></a></button>
    </p>

    <div id="ipsMoreConfiguration"<?php if(!$config['expanded']) {echo ' class="hidden"'; } ?>>
        <h2><?php _e('More configuration options', 'Install'); ?></h2>
        <input type="hidden" id="ipsConfigExpanded" name="configExpanded" value="<?php echo htmlspecialchars($config['expanded']); ?>">
        <div class="form-group">
            <label for="ipsConfigAdminUsername"><?php _e('Administrator login', 'Install'); ?></label>
            <input type="text" class="form-control" id="ipsConfigAdminUsername" name="configAdminUsername" value="<?php echo htmlspecialchars($config['adminUsername']); ?>">
            <p class="help-block"><?php _e('Use a different username. Otherwise default email will be used.', 'Install'); ?></p>
        </div>
        <div class="form-group">
            <label for="ipsConfigAdminPassword"><?php _e('Administrator password', 'Install'); ?></label>
            <input type="password" class="form-control" id="ipsConfigAdminPassword" name="configAdminPassword" value="<?php echo htmlspecialchars($config['adminPassword']); ?>">
            <p class="help-block"><?php _e('Set your password or system will generate it for you.', 'Install'); ?></p>
        </div>
        <div class="form-group">
            <label for="ipsConfigAdminEmail"><?php _e('Administrator email', 'Install'); ?></label>
            <input type="email" class="form-control" id="ipsConfigAdminEmail" name="configAdminEmail" value="<?php echo htmlspecialchars($config['adminEmail']); ?>">
            <p class="help-block"><?php _e('Set a different email. Otherwise default email will be used.', 'Install'); ?></p>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" id="ipsConfigDevelopmentEnvironment" name="configDevelopmentEnvironment"<?php if ($config['developmentEnvironment']) { echo ' checked="checked"'; } ?>> <?php _e('Show error and debug information', 'Install'); ?>
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" id="ipsConfigShowErrors" name="configShowErrors"<?php if ($config['showErrors']) { echo ' checked="checked"'; } ?>> <?php _e('Show errors on a page', 'Install'); ?>
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" id="ipsConfigDebugMode" name="configDebugMode"<?php if ($config['debugMode']) { echo ' checked="checked"'; } ?>> <?php _e('Load raw unminified JavaScript files, alert AJAX errors', 'Install'); ?>
            </label>
        </div>
    </div>
    <div class="modal fade" id="ipsTOS" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"><?php _e('ImpressPages Legal Notices', 'Install'); ?></h4>
                </div>
                <div class="modal-body" style="max-height: 300px; overflow: auto;">
                    <h2>ImpressPages</h2>
                    <p> Copyright <?php echo date("Y"); ?> by <a href="http://www.impresspages.org">ImpressPages, UAB</a></p>
                    <p>This program is free software: you can redistribute it and/or modify it under the terms of the <a href="<?php echo ipFileUrl('license.html') ?>">GNU General Public License or MIT License</a>.</p>
                    <p>This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.</p>
                    <p>You are required to keep these "Appropriate Legal Notices" intact as specified in GPL3 section 5(d) and 7(b) and MIT.</p>

                    <h2>Automatic updates</h2>
                    <p>ImpressPages is configured to check for updates automatically. This requires for the technical data of the website to be transferred to ImpressPages service servers. This process does not transfer any part of the website's content.</p>

                    <h2>Emails</h2>
                    <p>We proactively offer support and help for everyone who uses ImpressPages. All administrators with super admin permissions will be associated with the website. They might get emails from us if we see that we can help.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <p class="text-right">
        <button type="submit" class="btn btn-primary ipsUrlRewritesCheck ipsConfigurationSubmit"><?php _e('Next', 'Install'); ?></button>
    </p>
</form>
