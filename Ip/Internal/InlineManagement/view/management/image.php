<img
     data-key="<?php echo esc($key); ?>"
     data-defaultvalue="<?php echo esc($defaultValue); ?>"
     data-options='<?php echo json_encode($options); ?>'
     data-cssclass="<?php echo esc($cssClass); ?>"
     class="ipModuleInlineManagement ipmImage<?php echo $empty ? ' ipmEmpty' : '' ?> <?php echo esc($cssClass); ?>"
     src="<?php echo $empty ? ipFileUrl('Ip/Internal/InlineManagement/assets/empty.gif') : esc($value); ?>" alt="" />
