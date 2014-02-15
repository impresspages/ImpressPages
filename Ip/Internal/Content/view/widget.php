<div
    <?php if ($managementState) { ?>
        id="ipWidget-<?php echo $widgetInstanceId; ?>"
        data-widgetdata="<?php echo esc(json_encode($widgetData), 'attr'); ?>"
        data-widgetname="<?php echo esc($widgetName, 'attr'); ?>"
        data-widgetinstanceid="<?php echo esc($widgetInstanceId, 'attr'); ?>"
    <?php } ?>
    class="ipWidget ipWidget-<?php echo $widgetName; ?> ipLayout-<?php echo $widgetLayout; ?>">
    <?php if ($managementState && $widgetName != 'Columns'){ ?>
        <?php echo ipView('widgetControls.php', $this->getVariables())->render(); ?>
    <?php } ?>
<?php echo $html; ?>
</div>
