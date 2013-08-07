<?php
/* @var $theme \Modules\standard\design\Theme */
/* @var $this \Ip\View */
?>
<div class="ipModuleDesign">
    <?php if ($theme) { ?>
        <div>
            <img style="width: 446px; height: 266px" src="<?php echo $this->esc($theme->getThumbnailUrl()); ?>" alt="<?php echo $this->esc($theme->getTitle()); ?>" />
        </div>

        <h2><?php echo $this->esc($theme->getTitle()); ?></h2>

        <p>Author: <?php echo $this->esc($theme->getAuthorTitle()); ?></p>
    <?php } else { ?>
        <p>Custom theme.</p>
    <?php } ?>

    <?php if (count($availableThemes) > 1) { ?>
        <h3>Local Themes</h3>
        <ul>
            <?php foreach ($availableThemes as $localTheme) { ?>
                <?php /* @var $localTheme \Modules\standard\design\Theme */ ?>
                <?php if ($localTheme == $theme) { continue; } ?>

                <li><a href="#" class="ipsInstallTheme" data-theme='<?php echo $this->esc($localTheme->getName()) ?>'><?php echo $localTheme->getTitle() ?></a></li>
            <?php } ?>
        </ul>
    <?php } ?>

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