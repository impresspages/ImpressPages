<?php
/* @var $theme \Modules\standard\design\Theme */
/* @var $this \Ip\View */
?>
<div class="ipModuleDesign">
    <?php if ($theme) { ?>
        <div>
            <img style="width: 446px; height: 266px" src="<?php echo $this->esc($theme->getThumbnail()); ?>" alt="<?php echo $this->esc($theme->getTitle()); ?>" />
        </div>

        <h2><?php echo $this->esc($theme->getTitle()); ?></h2>

        <p>Description</p>

        <p>Author: <a href="#">ImpressPages</a></p>
        <p>Created: 2013-08-05 (not supported)</p>
        <p>Last update: 2013-08-05 (not supported)</p>
        <p>Compatible: IE8, IE9, IE10, Firefox, Safari, Opera, Chrome (not supported)</p>
        <p>Layout: responsive (not supported)</p>
        <p>Files included: Layered PSD, HTML files, CSS files, JS files</p>
        <p>Compatible versions: <?php echo $this->esc($theme->getVersion()); ?></p>
    <?php } else { ?>
        <p>Custom theme.</p>
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
    <a href="#" class="ipaOpenMarket">{{Find theme}}</a>
    <div style="width: 900px; height: 800px;" id="ipModuleDesignContainer" data-marketurl="<?php echo $this->esc($marketUrl) ?>">

    </div>
</div>