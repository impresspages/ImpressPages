<?php
echo \Ip\View::create('widget_data.php', array('widgetRecord' => $widgetRecord))->render();
?>
    <div>
        <?php echo $managementHtml ?>
    </div>
    <div class="ipWidgetButtons">
        <span class="ipWidgetButton ipWidgetSave">Save</span>
        <span class="ipWidgetButton ipWidgetCancel">Cancel</span>
        <div class="clear"><!--  --></div>
    </div>
