use Library\Php\Text\Html2Text;
<div id="ipWidget_<?php echo $widgetRecord['instanceId']; ?>" class="ipWidget ipWidgetManagement ipWidget_<?php echo $widgetRecord['name']; ?>" >
    <div class="ipWidgetManagementHeader">
        <span class="ipWidgetManagementTitle"><?php echo htmlspecialchars($widgetTitle) ?></span>
        <select class="ipWidgetLayoutSelect">
        <?php foreach($layouts as $layoutKey => $layout) { ?>
            <option value="<?php echo htmlspecialchars($layout['name']); ?>">
                <?php echo htmlspecialchars($layout['title']) ?>
            </option>        
        <?php } ?>
        </select>
        <div class="clear"><!--  --></div>
    </div>
    <?php
        $tmpData = $widgetRecord;       
        //but it is needed at management initialization unset($tmpData['data']); //data is removed because it will constantly change during management process
        $tmpData['state'] = 'management';
        echo \Ip\View::create('widget_data.php', array('widgetInstance' => $tmpData))->render();
    ?>
    <div class="ipWidgetManagementBody">
        <?php echo $managementHtml ?>
    </div>
    <div class="ipWidgetManagementFooter">
        <span class="ipWidgetButton ipWidgetSave">Save</span>
        <span class="ipWidgetButton ipWidgetCancel">Cancel</span>
        <div class="clear"><!--  --></div>
    </div>    
</div>
