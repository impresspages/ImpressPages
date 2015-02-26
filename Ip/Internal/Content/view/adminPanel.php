<?php
/**
 * @var Ip\WidgetController[] $widgets
 * @var boolean               $mobile
 * @var int                   $categorySplit
 * @var array                 $widgets
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

        <?php if (!$manageableRevision) { ?>
            <div class="_disable">
                <p>
                    <?php echo __('This is a preview of older revision, created at', 'Ip-admin'); ?> <?php echo ipFormatDateTime(strtotime($currentRevision['createdAt']), 'Ip-admin') ?>
                    <a href="#" class="ipsContentPublish"><?php _e('Publish this revision', 'Ip-admin'); ?></a>
                    <a href="#" class="ipsContentSave"><?php _e('Duplicate and edit this revision', 'Ip-admin'); ?></a>
                </p>
            </div>
        <?php } ?>

        <div class="_widgets">

            <?php // if mobile, we don't want to include categories ?>
            <?php if (!$mobile) { ?>

                <div class="_widgetCategories">

                    <?php $catCount = count($widgets); ?>
                    <?php if ($catCount > 1) : ?>

                        <?php $active = true; ?>
                        <?php $current = 0; ?>

                        <?php foreach ($widgets as $categoryKey => $list) : ?>

                            <?php if ($current % $categorySplit === 0) : ?>
                                <ul class="_widgetTabSwitches">
                            <?php endif; // $current % $categorySplit === 0 ?>

                            <li class="_widgetTabSwitch<?php echo $active ? ' _active' : '' ?>">
                                <a href="#_widgetTab_<?php echo strtolower(preg_replace('|[^A-Za-z0-9]|', '', $categoryKey)) ?>">
                                    <?php echo $categoryKey ?>
                                </a>
                            </li>

                            <?php $current++; ?>

                            <?php if ($current % $categorySplit === 0) : ?>
                                </ul>
                            <?php endif; // $current % $categorySplit === 0 ?>

                            <?php $active = false; ?>

                        <?php endforeach; ?>

                    <?php endif; // $catCount > 1 ?>

                </div>

            <?php } // !$mobile ?>

            <div class="_widgetTabs">

                <?php $active = !$mobile; ?>

                <?php if ($mobile) : ?>
                <div id="_widgetTab_all" class="_widgetTab _active">
                    <a href="#" class="_scrollButton _left ipsLeft"></a>
                    <a href="#" class="_scrollButton _right ipsRight"></a>

                    <ul>
                        <?php endif; // $mobile ?>

                        <?php foreach ($widgets as $categoryKey => $list) : ?>

                            <?php if (!$mobile) { ?>
                                <div id="_widgetTab_<?php echo strtolower(preg_replace('|[^A-Za-z0-9]|', '', $categoryKey)) ?>" class="_widgetTab<?php echo $active ? ' _active' : '' ?>">

                                <a href="#" class="_scrollButton _left ipsLeft"></a>
                                <a href="#" class="_scrollButton _right ipsRight"></a>

                                <ul>
                            <?php } ?>

                            <?php foreach ($list as $widgetKey => $widget) : ?>

                                <li>
                                    <div id="ipAdminWidgetButton-<?php echo $widget->getName(); ?>" class="_button ipsAdminPanelWidgetButton">
                                        <a href="#" title="<?php echo esc($widget->getTitle()); ?>">
                                                <span class="_title">
                                                    <span><?php echo esc($widget->getTitle()); ?></span>
                                                </span>
                                            <img class="_icon" src="<?php echo esc($widget->getIcon()) ?>" alt="<?php echo htmlspecialchars($widget->getTitle()); ?>"/>
                                        </a>
                                    </div>
                                </li>

                            <?php endforeach ?>

                            <?php if (!$mobile) { ?>
                                </ul>
                                </div>
                            <?php } ?>

                            <?php $active = false; ?>

                        <?php endforeach; ?>

                        <?php if ($mobile) { ?>
                    </ul>
                </div> <?php // <div id="_widgetTab_all" class="_widgetTab _active"> ?>
                <?php } // $mobile ?>

                <div class="clearfix"></div>

            </div>

            <div class="clearfix"></div>

        </div>
    </div>
</div>
