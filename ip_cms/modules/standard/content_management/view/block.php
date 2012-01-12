<?php
    $showEmpty = false;
    if ($site->managementState() && count($widgetsHtml) == 0) {
        $showEmpty = true;
    }
?>
<div id="ipBlock-<?php echo $blockName; ?>" class="ipBlock<?php echo ($showEmpty ? ' ipbEmpty' : ''); ?>">
<?php
    foreach($widgetsHtml as $key => $widgetHtml) {
        echo $widgetHtml;
    }
?>
</div>
