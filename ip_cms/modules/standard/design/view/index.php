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

        <h2>
            <i class="icon-ok"></i>
            <?php echo $this->esc($theme->getTitle()); ?>
            <small>(<?php echo $this->esc($theme->getVersion()); ?>)</small>
        </h2>

        <a href="#" class="ipsOpenOptions">{{Options}}</a>

        <p>{{Author:}} <?php echo $this->esc($theme->getAuthorTitle()); ?></p>
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
                <ul class="ipmThemeList clearfix">
                    <?php
                        foreach ($availableThemes as $localTheme) {
                            /* @var $localTheme \Modules\standard\design\Theme */
                            if ($localTheme == $theme) { continue; }
                    ?>
                            <li>
                                <div class="ipmThemeActions">
                                    <a href="#" class="btn btn-primary ipsInstallTheme" data-theme='<?php echo $this->esc($localTheme->getName()) ?>'>
                                        {{Install}}
                                    </a>
                                </div>
                                <div class="ipmThemePreview">
                                    <img src="<?php echo $this->esc($localTheme->getThumbnailUrl()); ?>" alt="<?php echo $this->esc($localTheme->getTitle()); ?>" />
                                </div>
                                <span class="ipmThemeTitle">
                                    <?php echo $this->esc($localTheme->getTitle()); ?>
                                    <small>(<?php echo $this->esc($localTheme->getVersion()); ?>)</small>
                                </span>
                            </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>
    </div>

    <div class="ipmPreview ipsPreview ipgHide">
        <div class="ipmClosePreview ipsClosePreview"></div>
        <iframe class="ipaFrame" src=""></iframe>
    </div>

    <div class="ipmThemeMarketContainer ipgHide" id="ipsThemeMarketContainer" data-marketurl="<?php echo $this->esc($marketUrl) ?>">
        <div class="ipmCloseThemeMarket"><a href="#" class="ipsCloseThemeMarket">&lt; Back to My Theme</a></div>
        <!-- <iframe name="easyXDM*" /> -->
    </div>
</div>