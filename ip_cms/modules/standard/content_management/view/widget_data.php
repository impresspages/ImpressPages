<!-- Included all widget data to pass to javascript. -->
<div class="ipWidgetData">	
<?php foreach($widgetRecord as $key => $value) { ?>				
		<input name="<?php echo addslashes($key); ?>" value="<?php echo addslashes($value); ?>" />
<?php } ?>
</div>