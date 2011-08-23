<div id="ipWidget_<?php echo $widgetRecord['id']; ?>" class="ipWidget ipWidgetManagement ipWidget_<?php echo $widgetRecord['name']; ?>" >
    <?php
        $tmpData = $widgetRecord;       
        unset($tmpData['data']); //data is removed because it will constantly change during management process
        $tmpData['state'] = 'preview';
        echo \Ip\View::create('widget_data.php', array('widgetRecord' => $tmpData))->render();
    ?>
    <div>
        <?php echo $managementHtml ?>
    </div>
    <div class="ipWidgetButtons">
        <span class="ipWidgetButton ipWidgetSave">Save</span>
        <span class="ipWidgetButton ipWidgetCancel">Cancel</span>
        <div class="clear"><!--  --></div>
    </div>
</div>
