<div class="ipModuleInlineManagement" data-cssclass="<?php echo $this->esc($cssClass) ?>">
    <div class="ipmText" <?php echo (isset($type) && $type == 'text') ? '' : 'style="display: none;"'; ?>>
        <?php echo isset($logoTextHtml) ? $logoTextHtml : '' ?>
    </div>
    <div class="ipmImage" <?php echo (!isset($type) || $type != 'text') ? '' : 'style="display: none;"'; ?>>
        <?php echo isset($logoImageHtml) ? $logoImageHtml : '' ?>
    </div>
</div>
