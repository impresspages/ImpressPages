<div id="ipWidget_<?php echo $widgetRecord['id']; ?>" class="ipWidget ipWidget_<?php echo $widgetRecord['name']; ?>" >
	<?php
	if ($managementState){ 
		echo \Ip\View::create('standard/content_management/view/widget_data.php', array('widgetRecord' => $widgetRecord))->render();
	}
	?>
    <?php echo $html ?>
</div>