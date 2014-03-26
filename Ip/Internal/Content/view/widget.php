<div
    <?php if ($managementState) { ?>
        id="ipWidget-<?php echo (int)$widgetInstanceId; ?>"
        data-widgetdata="<?php echo escAttr(json_encode($widgetData)); ?>"
        data-widgetname="<?php echo escAttr($widgetName); ?>"
        data-widgetinstanceid="<?php echo escAttr($widgetInstanceId); ?>"
    <?php } ?>
    class="ipWidget ipWidget-<?php echo escAttr($widgetName); ?> ipSkin-<?php echo escAttr($widgetSkin); ?>">
    <?php if ($managementState && $widgetName != 'Columns'){ ?>
        <?php echo ipView('widgetControls.php', $this->getVariables())->render(); ?>
    <?php } ?>
<?php echo $html; ?>
</div>
