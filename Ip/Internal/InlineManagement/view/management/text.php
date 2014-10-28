<<?php echo $tag ?> <?php echo $attributesStr; ?> class="ipModuleInlineManagement ipsModuleInlineManagementText <?php echo $cssClass; ?><?php echo str_replace(' ', '',$value) == '' ? ' _empty' : ''; ?>" data-cssclass='<?php echo esc($cssClass); ?>' data-key='<?php echo esc($key); ?>' data-htmltag='<?php echo esc($tag); ?>' data-defaultvalue='<?php echo esc($defaultValue); ?>'>
    <?php echo $value ?>
</<?php echo $tag ?>>
