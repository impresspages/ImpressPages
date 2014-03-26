<img
     data-key="<?php echo escAttr($key); ?>"
     data-defaultvalue="<?php echo escAttr($defaultValue); ?>"
     data-options="<?php echo escAttr(json_encode($options)); // single quotes around because json encodes with double quotes ?>"
     data-cssclass="<?php echo escAttr($cssClass); ?>"
     class="ipsModuleInlineManagementImage ipModuleInlineManagement<?php echo $empty ? ' _empty' : ''; ?> <?php echo escAttr($cssClass); ?>"
     src="<?php echo $empty ? escAttr($defaultValue) : escAttr($value); ?>"
     alt=""
     style="<?php if(!empty($options['height'])) { echo 'height: '.(int)$options['height'].'px;'; } ?><?php if(!empty($options['width'])) { echo 'width: '.(int)$options['width'].'px;'; } ?>"
/>
