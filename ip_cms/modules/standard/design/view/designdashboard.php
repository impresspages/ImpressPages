<div class="ipModuleDesign">
    <div>
        <?php echo $this->esc($themeTitle); ?>
        <?php echo $this->esc($themeName); ?>
        <?php echo $this->esc($themeVersion); ?>
        <img style="width: 100px;" src="<?php echo $this->esc($themeThumbnail); ?>" alt="<?php echo addslashes($this->esc($themeTitle)); ?>" />
    </div>

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
    <div style="width: 900px; height: 800px;" id="ipModuleDesignContainer" data-marketurl="<?php echo addslashes($marketUrl) ?>">

    </div>
</div>