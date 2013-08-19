<?php
/* @var $theme \Modules\standard\design\Theme */
/* @var $this \Ip\View */
?>
<div class="ip ipModuleDesign" xmlns="http://www.w3.org/1999/html">
    <h1>{{My Theme}}</h1>

    <div class="ipmSelectedTheme">
        <div class="ipmThemePreview">
            <img src="<?php echo $this->esc($theme->getThumbnailUrl()); ?>" alt="<?php echo $this->esc($theme->getTitle()); ?>" />
        </div>

        <div class="ipmThemeActions">
<!--            <a href="#" class="btn btn-link">{{Download}}</a>-->
            <?php if ($showConfiguration){ ?>
                <a href="#" class="btn btn-primary ipsOpenOptions">{{Edit}}</a>
            <?php } ?>
        </div>
        <h2>
            <i class="icon-ok"></i>
            <?php echo $this->esc($theme->getTitle()); ?>
            <small>(<?php echo $this->esc($theme->getVersion()); ?>)</small>
        </h2>

        <dl class="dl-horizontal">
            <?php if ($theme->getAuthorTitle()) { ?>
                <dt>{{Author:}}</dt>
                <dd><?php echo $this->esc($theme->getAuthorTitle()); ?></dd>
            <?php } ?>
        </dl>
    </div>

    <div class="ipmOtherThemes">
        <div class="ipmThemeMarket">
            <div class="ipmButtonWrapper">
                <span class="ipmTitle">{{Theme Market}}</span>
                <span class="impNotice">{{Want a new look? Search for a new theme.}}</span>
                <a href="#" class="btn btn-success ipsOpenMarket">{{Browse Themes}}</a>
            </div>
        </div>
        <div class="ipmLocalThemes">
            <?php if (count($availableThemes) > 1) { ?>
                <h2>{{Local Themes}}</h2>
                <ul class="ipmThemesList clearfix">
                    <?php
                        foreach ($availableThemes as $localTheme) {
                            /* @var $localTheme \Modules\standard\design\Theme */
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
                                        {{Install}}
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
                <li><a href="#ipModuleThemeMarketAll">{{All Themes}}</a></li>
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