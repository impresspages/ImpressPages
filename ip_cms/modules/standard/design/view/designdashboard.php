<div>
    <?php echo $this->esc($themeTitle); ?>
    <?php echo $this->esc($themeName); ?>
    <?php echo $this->esc($themeVersion); ?>
<!--    <iframe style="-webkit-transform: scale(0.5); -webkit-transform-origin: 0 0; width: 1100px; height: 500px;" src="--><?php //echo addslashes(BASE_URL) ?><!--"></iframe>-->
    <img style="width: 100px;" src="<?php echo $this->esc($themeThumbnail); ?>" alt="<?php echo addslashes($this->esc($themeTitle)); ?>" />
</div>
<style>
    #ipModuleDesignContainer iframe{
        width: 100%;
        height: 800px;
        outline: 1px solid black;
    }
</style>
<a href="#" class="ipModuleDesignOptions">{{Options}}</a>
<a href="#" class="ipModuleDesignOpenMarket">{{Find theme}}</a>
<div style="width: 900px; height: 800px;" id="ipModuleDesignContainer" data-marketurl="<?php echo addslashes($marketUrl) ?>">

</div>
