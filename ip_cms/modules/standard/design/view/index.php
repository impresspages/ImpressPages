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

        <h2><i class="icon-ok"></i> <?php echo $this->esc($theme->getTitle()); ?></h2>

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
                <h3>{{Local Themes}}</h3>
                <ul>
                    <?php foreach ($availableThemes as $localTheme) { ?>
                        <?php /* @var $localTheme \Modules\standard\design\Theme */ ?>
                        <?php if ($localTheme == $theme) { continue; } ?>

                        <li><a href="#" class="ipsInstallTheme" data-theme='<?php echo $this->esc($localTheme->getName()) ?>'><?php echo $localTheme->getTitle() ?></a></li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>
    </div>

    <a href="#" class="ipsOpenOptions">{{Options}}</a> | <a href="#" class="ipsOpenMarket">{{BUY THEME}}</a>

    <div class="ipmPreview ipsPreview" style="display: none;">
        <div class="ipmClosePreview ipsClosePreview"></div>
        <iframe class="ipaFrame" src=""></iframe>
    </div>

    <div class="ipmThemeMarketContainer" id="ipsThemeMarketContainer" data-marketurl="<?php echo $this->esc($marketUrl) ?>" style="display: none">
        <div class="ipmCloseThemeMarket"><a href="#" class="ipsCloseThemeMarket">&lt; Back to My Theme</a></div>
        <!-- <iframe name="easyXDM*" /> -->
    </div>
</div>