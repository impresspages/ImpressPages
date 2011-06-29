<div id="ipWidget_<?php echo $widgetRecord['id']; ?>" class="ipWidget ipWidget_<?php echo $widgetRecord['name']; ?>">
<?php
echo \Ip\View::create('standard/content_management/view/widget_data.php', array('widgetRecord' => $widgetRecord))->render();
?>
    <div>
        <?php echo $managementHtml ?>
    </div>
    <div>
        <span class="ipWidgetButton ipWidgetButtonSave">Save</span>
        <span class="ipWidgetButton ipWidgetButtonCancel">Cancel</span>
    </div>
</div>