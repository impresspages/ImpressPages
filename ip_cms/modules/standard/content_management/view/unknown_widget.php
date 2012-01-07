<div id="ipWidget-<?php echo $widgetRecord['instanceId']; ?>" class="ipWidget ipPreviewWidget ipWidget-<?php echo $widgetRecord['name']; ?> ipLayout-<?php echo $widgetRecord['layout']; ?>">
<?php
    if ($managementState){
        $tmpData = $widgetRecord;
        unset($tmpData['data']); //data is removed because it will constantly change during management process
        $tmpData['state'] = 'preview';
        echo \Ip\View::create('widget_data.php', array('widgetInstance' => $tmpData))->render();

        echo $this->par('standard/content_management/admin_translations/missing_widget', array('widgetName' => $this->esc($widgetRecord['name'])));
    }
?>
</div>
