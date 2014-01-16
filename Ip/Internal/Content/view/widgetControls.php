<div class="ipsWidgetControls ipAdminWidgetControls">
    <?php if (!empty($optionsMenu)) { ?>
        <div class="ip" style="float: left; display: block;">
        <div class="btn-group ipaButton">
            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
                <span class="caret"><?php _e('Settings', 'ipAdmin') ?></span>
                <span class="sr-only"></span>
            </button>
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
        </div>
        </div>
    <?php } ?>
    <a href="#" class="ipaButton ipActionWidgetMove"><span><?php _e('Move', 'ipAdmin') ?></span></a>
    <a href="#" class="ipaButton ipActionWidgetDelete"><span><?php _e('Delete', 'ipAdmin') ?></span></a>
</div>
