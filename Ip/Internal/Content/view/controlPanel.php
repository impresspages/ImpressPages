<div class="ip ipAdminPanelContainer">
    <div class="ipAdminPanel">
        <div class="ipAdminControls hidden-xs">
            <div class="ipAdminWidgetsSearch clearfix">
                <div class="ipaControls">
                    <span class="ipaArrow"></span>
                    <input type="text" class="ipAdminInput ipaInput ipsInput" value="<?php _e('Search widgets', 'ipAdmin') ?>" />
                    <a href="#" class="ipaButton ipsButton"></a>
                </div>
            </div>
        </div>

        <div class="ipAdminWidgets">
    <?php if(!$manageableRevision){ ?>
            <div class="ipAdminWidgetsDisable">
                <p>
                    <?php echo __('This is a preview of older revision, created at', 'ipAdmin'); ?> <?php echo date("Y-m-d H:i", $currentRevision['createdAt']) ?>
                    <a href="#" class="ipsContentPublish"><?php _e('Publish this revision', 'ipAdmin'); ?></a>
                    <a href="#" class="ipsContentSave"><?php _e('Duplicate and edit this revision', 'ipAdmin'); ?></a>
                </p>
            </div>
    <?php } ?>
            <a href="#" class="ipAdminWidgetsScroll ipaLeft ipsLeft"></a>
            <a href="#" class="ipAdminWidgetsScroll ipaRight ipsRight"></a>
            <div class="ipAdminWidgetsContainer">
    <?php $scrollWidth = count($widgets)*87; // to keep all elements on one line ?>
                <ul<?php echo ' style="width: '.$scrollWidth.'px;"'; ?>>
    <?php foreach ($widgets as $widgetKey => $widget) { ?>
                    <li>
                        <div id="ipAdminWidgetButton-<?php echo $widget->getName(); ?>" class="ipActionWidgetButton">
                            <a href="#">
                                <span class="ipaTitle"><span><?php echo htmlspecialchars($widget->getTitle()); ?></span></span>
                                <img src="<?php echo esc($widget->getIcon()) ?>" alt="<?php echo htmlspecialchars($widget->getTitle()); ?>" />
                            </a>
                        </div>
                    </li>
    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="ipAdminErrorContainer"></div>
        <div class="ipAdminErrorSample">
            <p class="ipAdminError"></p>
        </div>
    </div>
</div>
