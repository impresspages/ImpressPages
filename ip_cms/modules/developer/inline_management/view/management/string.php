<div class="ipmEdit"><span><?php echo $this->escPar('standard/configuration/admin_translations/edit'); ?></span>
    <<?php echo $tag ?> class="ipModuleInlineManagementString <?php echo $cssClass ?>" data-cssclass='<?php echo $this->esc($cssClass) ?>' data-key='<?php echo $this->esc($key) ?>' data-htmltag='<?php echo $this->esc($tag) ?>'>
        <?php echo nl2br($this->esc($value)) ?>
    </<?php echo $tag ?>>
</div>