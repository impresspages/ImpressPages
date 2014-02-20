<div class="ip">
    <div class="ipAdminWidgetControls ipsWidgetControls">
        <div class="_container ipsContainer clearfix">
            <?php if (!empty($optionsMenu)) { ?>
                <button class="btn btn-controls btn-xs _settings" data-toggle="dropdown" title="<?php _e('Settings', 'ipAdmin'); ?>"><i class="fa fa-cog"></i></button>
                <ul class="dropdown-menu" role="menu">
                    <?php foreach($optionsMenu as $menuItem) { ?>
                        <li>
                            <a
                                <?php
                                if (is_array($menuItem['attributes'])) {
                                    foreach ($menuItem['attributes'] as $key => $value) {
                                        echo esc($key, 'attr') . '="' . esc($value, 'attr') . '"';
                                    }
                                }
                                ?>
                                href="#"><?php echo esc($menuItem['title']) ?></a>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
            <button class="btn btn-controls btn-xs _drag ipsWidgetDrag" title="<?php _e('Drag', 'ipAdmin'); ?>">&nbsp;</button>
            <button class="btn btn-controls btn-xs _delete ipsWidgetDelete" title="<?php _e('Delete', 'ipAdmin'); ?>"><i class="fa fa-trash-o"></i></button>
        </div>
    </div>
</div>
