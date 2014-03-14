<img
     data-key="<?php echo esc($key); ?>"
     data-defaultvalue="<?php echo esc($defaultValue); ?>"
     data-options='<?php echo json_encode($options); // single quotes around because json encodes with double quotes ?>'
     data-cssclass="<?php echo esc($cssClass); ?>"
     class="ipsModuleInlineManagementImage ipModuleInlineManagement<?php echo $empty ? ' _empty' : ''; ?> <?php echo esc($cssClass); ?>"
     src="<?php echo $empty ? esc($defaultValue) : esc($value); ?>"
     alt=""
     style="<?php if(!empty($options['height'])) { echo 'height: '.$options['height'].'px;'; } ?><?php if(!empty($options['width'])) { echo 'width: '.$options['width'].'px;'; } ?>"
/>
