<div id="ipWidget-<?php echo $widgetRecord['instanceId']; ?>" class="ipWidget ipAdminWidget ipAdminWidget-<?php echo $widgetRecord['name']; ?> ipLayout-<?php echo $widgetRecord['layout']; ?>">
    <div class="ipaHeader ipgClear">
        <span class="ipaTitle"><?php echo htmlspecialchars($widgetTitle) ?></span>
        <select class="ipAdminWidgetLayouts">
<?php foreach($layouts as $layoutKey => $layout) { ?>
            <option value="<?php echo htmlspecialchars($layout['name']); ?>"<?php if ($layout['name'] == $widgetRecord['layout']) { echo ' selected="selected"'; } ?>>
                <?php echo htmlspecialchars($layout['title']) ?>
            </option>
<?php } ?>
        </select>
    </div>
<?php
    $tmpData = $widgetRecord;
    //but it is needed at management initialization unset($tmpData['data']); //data is removed because it will constantly change during management process
    $tmpData['state'] = 'management';
    echo \Ip\View::create('widget_data.php', array('widgetInstance' => $tmpData))->render();
?>
    <div class="ipaBody">
<?php echo $managementHtml; ?>
    </div>
    <div class="ipaFooter">
        <a href="#" class="ipAdminButton ipaConfirm ipActionWidgetSave">Save</a>
        <a href="#" class="ipAdminButton ipActionWidgetCancel">Cancel</a>
    </div>
</div>
