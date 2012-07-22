<h1><?php echo $this->escTran('error_write_permission_title'); ?></h1>
<p><?php echo str_replace('[[file]]', $file, $this->tran('error_write_permission_text')); ?></p>
<a class="button actProceed" href="#"><?php echo $this->escTran('button_proceed'); ?></a>
<span class="seleniumWritePermission"></span>