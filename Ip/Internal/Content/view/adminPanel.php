<div class="ip ipsAdminPanelContainer">
    <div class="ipAdminPanel ipsAdminPanel">
<?php /*
 widget search functionality works but no need to have it now

            <div class="ipsAdminPanelWidgetsSearch clearfix">
                <div class="ipaControls">
                    <span class="ipaArrow"></span>
                    <input type="text" class="ipAdminInput ipaInput ipsInput" value="<?php _e('Search widgets', 'ipAdmin') ?>" />
                    <a href="#" class="ipaButton ipsButton"></a>
                </div>
            </div>
*/ ?>

        <div class="_widgets">
            <a href="#" class="_scrollButton _left ipsLeft"></a>
            <a href="#" class="_scrollButton _right ipsRight"></a>
            <div class="_container ipsAdminPanelWidgetsContainer">
                <?php $scrollWidth = count($widgets)*(55 + 30 + 2*3); // to keep all elements on one line ?>
                <ul<?php echo ' style="width: '.$scrollWidth.'px;"'; ?>>
                    <?php foreach ($widgets as $widgetKey => $widget) { ?>
                        <li>
                            <div id="ipAdminWidgetButton-<?php echo $widget->getName(); ?>" class="_button ipsAdminPanelWidgetButton">
                                <a href="#" title="<?php echo esc($widget->getTitle()); ?>">
                                    <span class="_title"><span><?php echo esc($widget->getTitle()); ?></span></span>
                                    <img src="<?php echo esc($widget->getIcon()) ?>" alt="<?php echo htmlspecialchars($widget->getTitle()); ?>" />
                                </a>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>
