<div class="ipModuleInlineManagement">
    <div class="ipmEdit"><span><?php echo $this->escPar('standard/configuration/admin_translations/edit'); ?></span></div>
    <div class="ipmText" <?php echo (isset($type) && $type == 'text') ? '' : 'style="display: none;"'; ?>>
        <?php echo isset($logoTextHtml) ? $logoTextHtml : '' ?>
    </div>
    <div class="ipmImage" <?php echo (!isset($type) || $type != 'text') ? '' : 'style="display: none;"'; ?>>
        <?php echo isset($logoImageHtml) ? $logoImageHtml : '' ?>
    </div>
</div>
