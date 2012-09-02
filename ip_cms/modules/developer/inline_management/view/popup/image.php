<div class="ipaImage"></div>
<div class="clear" style="clear: both;"><!-- --></div>
<div class="ipaControls">
    Apply to
    <select class="ipaType">
        <?php foreach ($types as $key => $type){ ?>
            <option value="<?php echo $this->esc($type['value']) ?>" <?php echo !empty($type['selected']) ? 'selected="selected"' : '' ?>><?php echo $this->esc($type['title']) ?></option>
        <?php } ?>
        <!-- <option value="default">Page "Services" and sub-pages</option>
        <option value="default">This page and sub-pages</option>
        <option value="default">All "EN" pages</option>
        <option value="default">All pages</option> -->
    </select>
    <br/>
    <a class="ipAdminButton ipaConfirm" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/confirm'); ?></a>
    <a class="ipAdminButton ipaCancel" href="#"><?php echo $this->escPar('standard/configuration/admin_translations/cancel'); ?></a>
</div>