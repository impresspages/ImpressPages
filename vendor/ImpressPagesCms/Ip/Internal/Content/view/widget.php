<div
    class="ipWidget ipWidget-<?php echo escAttr($widgetName); ?> ipSkin-<?php echo escAttr($widgetSkin); ?>"
    <?php if ($managementState) { ?>
        id="ipWidget-<?php echo (int)$widgetId; ?>"
        data-widgetdata="<?php echo escAttr(json_encode($widgetData)); ?>"
        data-widgetname="<?php echo escAttr($widgetName); ?>"
        data-widgetid="<?php echo escAttr($widgetId); ?>"
    <?php } ?>
    >
    <?php if ($managementState && $widgetName != 'Columns'){ ?>
        <?php echo ipView('widgetControls.php', $this->getVariables())->render(); ?>
    <?php } ?>
<?php echo $html; ?>
</div>
