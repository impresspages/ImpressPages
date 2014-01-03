<div id="ipWidget-<?php echo $widgetRecord['instanceId']; ?>" class="ipWidget ipPreviewWidget ipWidget-<?php echo $widgetRecord['name']; ?> ipLayout-<?php echo $widgetRecord['layout']; ?>">
<?php
    if ($managementState){
        $tmpData = $widgetRecord;
        unset($tmpData['data']); //data is removed because it will constantly change during management process
        $tmpData['state'] = 'preview';
        echo ipView('widget_data.php', array('widgetInstance' => $tmpData))->render();

        echo $this->par('Content.missing_widget', array('widgetName' => esc($widgetRecord['name'])));
    }
?>
</div>
