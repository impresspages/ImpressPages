<div id="ipWidget_<?php echo $widgetRecord['instanceId']; ?>"
    class="ipWidget ipWidgetPreview ipWidget_<?php echo $widgetRecord['name']; ?>">
    <?php
    if ($managementState){
        $tmpData = $widgetRecord;
        unset($tmpData['data']); //data is removed because it will constantly change during management process
        $tmpData['state'] = 'preview';
        echo \Ip\View::create('widget_data.php', array('widgetInstance' => $tmpData))->render();

        echo "Controls for this widget does not exist. Widget name: ".htmlspecialchars($widgetRecord['name']);
    }
    ?>
</div>
