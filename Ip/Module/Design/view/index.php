<?php
/* @var $theme \Ip\Module\Design\Theme */
/* @var $this \Ip\View */
?>
<div class="ip ipModuleDesign" xmlns="http://www.w3.org/1999/html">
    <h1><?php echo $this->escPar('Design.my_theme'); ?></h1>

    <div class="ipmSelectedTheme">
        <div class="ipmThemePreview">
            <img src="<?php echo $this->esc($theme->getThumbnailUrl()); ?>" alt="<?php echo $this->esc($theme->getTitle()); ?>" />
        </div>

        <div class="ipmThemeActions">
<!--            <a href="#" class="btn btn-link">Download</a>-->
            <?php if ($showConfiguration){ ?>
                <a href="#" class="btn btn-primary ipsOpenOptions"><?php echo $this->escPar('Design.options'); ?></a>
                <br/><br/>
            <?php } ?>
            <a href="<?php echo $contentManagementUrl ?>" class="btn btn-primary"><?php echo $this->esc($contentManagementText); ?></a>
        </div>
        <h2>
            <i class="icon-ok"></i>
            <?php echo $this->esc($theme->getTitle()); ?>
            <small>(<?php echo $this->esc($theme->getVersion()); ?>)</small>
        </h2>
        <div class="ipmPlugins">
            <?php if ($pluginNote) { ?>
            <div class="alert alert-block">
                <?php echo $this->esc($pluginNote); ?>
            </div>
            <?php } ?>
            <dl class="dl-horizontal">
                <?php foreach ($plugins as $key => $plugin ) {?>
                    <dt><?php echo $key == 0 ? $this->escPar('Design.available_plugins') . ':' : '' ?></dt>
                    <dd>
                        <?php echo $this->esc($plugin->getModuleTitle()); ?>
                        <a href="#" class="ipsInstallPlugin" data-pluginname="<?php echo $this->esc($plugin->getModuleKey()) ?>" data-plugingroup="<?php echo $this->esc($plugin->getModuleGroupKey()) ?>"><?php echo $this->escPar('Config.install'); ?></a>
                    </dd>
                <?php } ?>
            </dl>
        </div>
    </div>

    <div class="ipmOtherThemes">
        <div class="ipmThemeMarket">
            <div class="ipmButtonWrapper">
                <span class="ipmTitle"><?php echo $this->escPar('Design.theme_market'); ?></span>
                <span class="impNotice"><?php echo $this->escPar('Design.theme_market_description'); ?></span>
                <a href="#" class="btn btn-success ipsOpenMarket"><?php echo $this->escPar('Design.theme_market_browse'); ?></a>
            </div>
        </div>
        <div class="ipmLocalThemes">
            <?php if (count($availableThemes) > 1) { ?>
                <h2><?php echo $this->escPar('Design.local_themes'); ?></h2>
                <ul class="ipmThemesList clearfix">
                    <?php
                        foreach ($availableThemes as $localTheme) {
                            /* @var $localTheme \Ip\Module\Design\Theme */
                            if ($localTheme == $theme) { continue; }
                    ?>
                            <li>
                                <div class="ipmThemePreview">
                                    <img src="<?php echo $this->esc($localTheme->getThumbnailUrl()); ?>" alt="<?php echo $this->esc($localTheme->getTitle()); ?>" />
                                </div>
                                <span class="ipmThemeTitle">
                                    <?php echo $this->esc($localTheme->getTitle()); ?>
                                    <small>(<?php echo $this->esc($localTheme->getVersion()); ?>)</small>
                                </span>
                                <div class="ipmThemeActions">
                                    <a href="#" class="btn btn-primary ipsInstallTheme" data-theme='<?php echo $this->esc($localTheme->getName()) ?>'>
                                        <?php echo $this->escPar('Config.install'); ?>
                                    </a>
                                </div>
                            </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>
    </div>

    <div class="ipsThemeMarketPopup ipmThemeMarketPopup ipgHide">
        <div class="ipmPopupTabs">
            <ul>
                <li><a href="#ipModuleThemeMarketAll"><?php echo $this->escPar('Design.theme_market'); ?></a></li>
            </ul>

            <a href="#" class="ipmThemeMarketPopupClose ipsThemeMarketPopupClose ui-dialog-titlebar-close ui-corner-all" role="button"><span class="ui-icon ui-icon-closethick"></span></a>

            <div id="ipModuleThemeMarketAll">
                <div class="ipmThemeMarketContainer" id="ipModuleThemeMarketContainer" data-marketurl="<?php echo $this->esc($marketUrl) ?>">
                    <!-- <iframe name="easyXDM*" /> -->
                </div>
            </div>
        </div>
    </div>

    <div class="ipmPreview ipsPreview ipgHide">
        <button type="button" class="btn ipmPreviewClose ipsPreviewClose"><i class="icon-remove"></i></button>
        <iframe class="ipsFrame" src="" frameborder="0"></iframe>
    </div>
</div>