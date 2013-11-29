<div
    <?php if ($managementState){ ?>
        data-widgetdata="<?php echo esc(json_encode($widgetData), 'attr'); ?>"
        data-widgetname="<?php echo esc($widgetName, 'attr'); ?>"
        data-widgetinstanceid="<?php echo esc($widgetInstanceId, 'attr'); ?>"
    <?php } ?>
    class="ipWidget ipPreviewWidget  ipWidget-<?php echo $widgetName; ?> ipLayout-<?php echo $widgetLayout; ?>">
<?php //TODOX remove ipPreviewWidget class. It is redundant ?>
<?php echo $html; ?>
</div>
