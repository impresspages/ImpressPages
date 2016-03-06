<div class="ip">
    <div class="ipAdminWidgetToolbar hidden" id="ipWidgetHeadingControls">
        <div class="btn-toolbar" role="toolbar">
            <div class="btn-group">
                <?php for($i=1; $i <= $maxLevel; $i++) { ?>
                    <?php $text = 'H' . $i; ?>
                <button type="button" data-level="<?php echo $i ?>" class="btn btn-controls ipsH"><?php _e($text, 'Ip-admin'); ?></button>
                <?php } ?>
                <button type="button" class="btn btn-controls ipsOptions"><?php _e('Options', 'Ip-admin'); ?></button>
            </div>
        </div>
    </div>
</div>
