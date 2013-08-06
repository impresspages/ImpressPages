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
        <ul>
            <?php foreach ($availableThemes as $localTheme) { ?>
                <?php /* @var $localTheme \Modules\standard\design\Theme */ ?>
                <?php if ($localTheme == $theme) { continue; } ?>
                <li><?php echo $localTheme->getTitle() ?></li>
            <?php } ?>
        </ul>
    <?php } ?>


    <div class="ipaPreview" style="display: none;">
        <iframe class="ipaFrame" src="" style="width: 1200px; height: 500px; border: 1px solid;"></iframe>
    </div>

    <style>
        #ipModuleDesignContainer iframe {
            width: 100%;
            height: 800px;
            outline: 1px solid black;
        }
    </style>
    <a href="#" class="ipaOpenOptions">{{Options}}</a>
    <a href="#" class="ipaOpenMarket">{{BUY THEME}}</a>
    <div style="width: 900px; height: 800px;" id="ipModuleDesignContainer" data-marketurl="<?php echo $this->esc($marketUrl) ?>">

    </div>
</div>