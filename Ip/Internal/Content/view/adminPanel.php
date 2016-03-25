<?php
/**
 * @var Ip\WidgetController[] $widgets
 */
?>
<div class="ip ipsAdminPanelContainer">
    <div class="ipAdminPanel ipsAdminPanel">
        <?php /*
 widget search functionality works but no need to have it now

            <div class="ipsAdminPanelWidgetsSearch clearfix">
                <div class="ipaControls">
                    <span class="ipaArrow"></span>
                    <input type="text" class="ipAdminInput ipaInput ipsInput" value="<?php _e('Search widgets', 'Ip-admin') ?>" />
                    <a href="#" class="ipaButton ipsButton"></a>
                </div>
            </div>
*/ ?>

        <?php if(!$manageableRevision){ ?>
            <div class="_disable">
                <p>
                    <?php echo __('This is a preview of older revision, created at', 'Ip-admin'); ?> <?php echo ipFormatDateTime(strtotime($currentRevision['createdAt']), 'Ip-admin') ?>
                    <a href="#" class="ipsContentPublish"><?php _e('Publish this revision', 'Ip-admin'); ?></a>
                    <a href="#" class="ipsContentSave"><?php _e('Duplicate and edit this revision', 'Ip-admin'); ?></a>
                </p>
            </div>
        <?php } ?>

            <div class="_widgetCategories">
                <?php $catCount = count($widgets); ?>
                <?php if (count($tags) > 1) { ?>
                    <?php $active = true; ?>
                    <?php $current = 0; ?>
                    <?php foreach ($tags as $key => $tag){ ?>
                        <?php if ($current % $categorySplit === 0) { ?>
                            <ul class="_widgetTabSwitches">
                        <?php } // $current % $categorySplit === 0 ?>
                        <li class="_widgetTabSwitch ipsWidgetTag" data-tag="<?php echo escAttr($key) ?>">
                            <a href="#">
                                <?php _e($key, 'Ip-admin') ?>
                            </a>
                        </li>
                        <?php $current++; ?>
                        <?php if ($current % $categorySplit === 0){ ?>
                            </ul>
                        <?php } ?>
                        <?php $active = false; ?>

                    <?php } ?>
                <?php } // $catCount > 1 ?>
            </div>
        <div class="_widgets ipsWidgetList">

            <a href="#" class="_scrollButton _left ipsLeft"></a>
            <a href="#" class="_scrollButton _right ipsRight"></a>
            <div class="_container ipsAdminPanelWidgetsContainer">
                <?php $scrollWidth = count($widgets)*(55 + 30 + 2*3); // to keep all elements on one line ?>
                <ul<?php echo ' style="width: '.$scrollWidth.'px;"'; ?>>
                    <?php foreach ($widgets as $widgetKey => $widget) { ?>
                        <li class="ipsWidgetItem ipsWidgetItem-<?php echo $widget->getName(); ?>">
                            <div id="ipAdminWidgetButton-<?php echo $widget->getName(); ?>" class="_button ipsAdminPanelWidgetButton">
                                <a href="#" title="<?php echo escAttr($widget->getTitle()); ?>">
                                    <span class="_title"><span><?php echo esc($widget->getTitle()); ?></span></span>
                                    <img class="_icon" src="<?php echo escAttr($widget->getIcon()) ?>" alt="<?php echo escAttr($widget->getTitle()); ?>" />
                                </a>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>
