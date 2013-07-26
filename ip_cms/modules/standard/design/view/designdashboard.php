<div>
    <?php echo $this->esc($themeName); ?>
    <?php echo $this->esc($themeVersion); ?>
    <img width="200" src="<?php echo $this->esc($themePreviewImage) ?>" alt="<?php echo addslashes($themeName); ?>" />
</div>
<style>
    #ipModuleDesignContainer iframe{
        width: 100%;
        height: 800px;
        outline: 1px solid black;
    }
</style>
<div style="width: 900px; height: 800px;" id="ipModuleDesignContainer" data-marketurl="<?php echo addslashes($marketUrl) ?>">

</div data->
<!--<iframe style="width: 900px; height: 500px; clear: both;" src="--><?php //echo addslashes($marketUrl) ?><!--">-->

<!--</iframe>-->
