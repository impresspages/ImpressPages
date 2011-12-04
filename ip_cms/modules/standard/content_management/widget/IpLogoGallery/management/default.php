<div style="padding: 10px;">
    <div class="ipWidget_ipLogoGallery_uploadFile" style="float: left;"></div>
    <div class="clear">
        <!--  -->
    </div>
</div>
<ul class="ipWidget_ipLogoGallery_container">
</ul>
<style>
.ipWidget_ipLogoGallery_hidden {
	display: none;
}

.ipWidget_ipLogoGallery_logoTemplate {
	float: left;
	width: px;
	height: px;
	background-color: #aaa;
}
</style>
<div class="clear">
    <!-- -->
</div>
<ul class="ipWidget_ipLogoGallery_hidden">
    <li class="ipWidget_ipLogoGallery_logoTemplate">
        <form>
            <span class="ipWidget_ipLogoGallery_logoMoveHandle">Move</span>
            <input class="ipWidget_ipLogoGallery_logoTitle" type="text"
                name="title" value="" />
            <div class="ipWidget_ipLogoGallery_logoPreview"
                style="width: 200px;"></div>
            <span class="ipWidget_ipLogoGallery_logoRemove">Remove</span>
        </form>
    </li>
</ul>
<div class="ipWidget_ipLogoGallery_hidden">
    <form>
        <input name="logoWidth" type="hidden"
            value="<?php echo (int)$logoWidth ?>" /> <input
            type="logoHeight" value="<?php echo (int)$logoHeight ?>" />
    </form>
</div>
