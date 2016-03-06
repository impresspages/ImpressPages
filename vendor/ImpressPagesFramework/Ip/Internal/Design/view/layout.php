<div class="ipModuleDesign">
    <h1><?php _e('My theme', 'Ip-admin'); ?></h1>

    <div class="_selectedTheme">
        <div class="_preview">
            <img src="<?php echo esc($theme->getThumbnailUrl()); ?>" alt="<?php echo esc($theme->getTitle()); ?>" />
        </div>

        <div class="_actions">
            <?php if ($showConfiguration){ ?>
                <a href="#" class="btn btn-primary ipsOpenOptions"><?php _e('Options', 'Ip-admin'); ?></a>
                <br/><br/>
            <?php } ?>
            <a href="<?php echo $contentManagementUrl ?>" class="btn btn-primary"><?php echo esc($contentManagementText); ?></a>
        </div>
        <h2 class="clearfix">
            <i class="fa fa-check"></i>
            <?php echo esc($theme->getTitle()); ?>
            <small>(<?php echo esc($theme->getVersion()); ?>)</small>
        </h2>
        <div class="_plugins">
            <?php if ($pluginNote) { ?>
            <div class="alert alert-block">
                <?php echo esc($pluginNote); ?>
            </div>
            <?php } ?>
            <dl class="dl-horizontal">
                <?php foreach ($plugins as $key => $plugin ) {?>
                    <?php if ($key == 0) { ?>
                        <dt><?php _e('Available plugins', 'Ip-admin'); ?></dt>
                    <?php } ?>
                    <dd>
                        <?php echo esc(!empty($plugin['title']) ? $plugin['title'] : $plugin['name']); ?>
                        <a href="#" class="btn btn-xs btn-primary ipsInstallPlugin" data-pluginname="<?php echo esc($plugin['name']) ?>"><?php _e('Install', 'Ip-admin'); ?></a>
                    </dd>
                <?php } ?>
            </dl>
        </div>
    </div>

    <div class="_themes">
        <div class="_market <?php echo $marketUrl == '' ? 'hidden' : '' ?>">
            <div class="_wrapper">
                <span class="_title"><?php _e('Marketplace', 'Ip-admin'); ?></span>
                <span class="_notice"><?php _e('Want a new look? Search for a new theme.', 'Ip-admin'); ?></span>
                <a href="" class="btn btn-success ipsOpenMarket"><?php _e('Browse themes', 'Ip-admin'); ?></a>
            </div>
        </div>
        <div class="_localThemes">
            <?php if (count($availableThemes) > 1) { ?>
                <h2><?php _e('Local themes', 'Ip-admin'); ?></h2>
                <ul class="_list clearfix">
                    <?php
                        foreach ($availableThemes as $localTheme) {
                            /* @var $localTheme \Ip\Internal\Design\Theme */
                            if ($localTheme->getName() == $theme->getName()) {
                                continue;
                            }
                    ?>
                        <li>
                            <div class="_preview">
                                <img src="<?php echo esc($localTheme->getThumbnailUrl()); ?>" alt="<?php echo esc($localTheme->getTitle()); ?>" />
                            </div>
                            <span class="_title">
                                <?php echo esc($localTheme->getTitle()); ?>
                                <small>(<?php echo esc($localTheme->getVersion()); ?>)</small>
                            </span>
                            <div class="_actions">
                                <a href="#" class="btn btn-primary ipsInstallTheme" data-theme='<?php echo esc($localTheme->getName()) ?>'>
                                    <?php _e('Install', 'Ip-admin'); ?>
                                </a>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>
    </div>

    <div class="_popup ipsThemeMarketPopup hidden">
        <div class="_container" id="ipsModuleThemeMarketContainer" data-marketurl="<?php echo esc($marketUrl); ?>">
            <button type="button" class="btn btn-default _back ipsThemeMarketPopupClose"><i class="fa fa-angle-double-left"></i> <?php _e('Back to Design', 'Ip-admin'); ?></button>
        </div>
    </div>

    <div class="_popup ipsPreview hidden">
        <button type="button" class="btn btn-danger _close ipsPreviewClose" title="<?php _e('Close', 'Ip-admin'); ?>"><i class="fa fa-times"></i></button>
        <iframe class="ipsFrame" src="" frameborder="0"></iframe>
    </div>
</div>
