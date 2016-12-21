<h1><?php _e('Website configuration', 'Install'); ?></h1>

<div class="ipsErrorContainer"></div>
<form role="form" class="ipsConfigurationForm">
    <div class="form-group">
        <label for="ipsConfigWebsiteName"><?php _e('Website name', 'Install'); ?></label>
        <input type="text" class="form-control" id="ipsConfigWebsiteName" name="configWebsiteName" value="<?php echo htmlspecialchars($config['websiteName']); ?>">
    </div>
    <div class="form-group">
        <label for="ipsConfigWebsiteEmail"><?php _e('Website e-mail address', 'Install'); ?></label>
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
            <input type="checkbox" id="ipsConfigSupport" name="configSupport"<?php if ($config['support']) { echo ' checked="checked"'; } ?>> <?php _e('Get ImpressPages support and updates', 'Install'); ?>
        </label>
    </div>
    <p>
        <?php echo sprintf(__('By proceeding you agree with <a href="#" class="%s">Terms of Use</a>.', 'Install', false), 'ipsTOS'); ?>
    </p>
    <div class="modal fade" id="ipsTOS" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"><?php _e('ImpressPages Terms of Use', 'Install'); ?></h4>
                </div>
                <div class="modal-body" style="max-height: 270px; overflow: auto;">
                    <h2>Legal</h2>
                    <p>Copyright <?php echo date("Y"); ?> by <a href="http://www.impresspages.org">ImpressPages, UAB</a></p>
                    <p>This program is free software: you can re-distribute it and/or modify it under the terms of the <a href="<?php echo ipFileUrl('license.html') ?>">GNU General Public License or MIT License</a>.</p>
                    <p>This program is distributed hoping it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.</p>
                    <p>You are required to keep these "Appropriate Legal Notices" intact as specified in GPL3 section 5(d) and 7(b) and MIT.</p>

                    <h2>Usage statistics</h2>
                    <p>Your website is configured to share usage statistics with ImpressPages to help us make the software better in the future. We may periodically send notifications or promotional materials related to your website, our products and services to the administrators of the website.</p>
                    <p class="alert alert-warning">Website content is NOT transferred.</p>
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
