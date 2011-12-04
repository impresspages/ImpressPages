<div style="padding: 10px;">
    <div class="ipWidget_ipPictureGallery_uploadFile"
        style="float: left;"></div>
    <div class="clear">
        <!--  -->
    </div>
</div>
<ul class="ipWidget_ipPictureGallery_container">
</ul>
<style>
.ipWidget_ipPictureGallery_hidden {
	display: none;
}

.ipWidget_ipPictureGallery_pictureTemplate {
	float: left;
	width: px;
	height: px;
	background-color: #aaa;
}
</style>
<div class="clear">
    <!-- -->
</div>
<ul class="ipWidget_ipPictureGallery_hidden">
    <li class="ipWidget_ipPictureGallery_pictureTemplate">
        <form>
            <span class="ipWidget_ipPictureGallery_pictureMoveHandle">Move</span>
            <input class="ipWidget_ipPictureGallery_pictureTitle"
                type="text" name="title" value="" />
            <div class="ipWidget_ipPictureGallery_picturePreview"
                style="width: 200px;"></div>
            <span class="ipWidget_ipPictureGallery_pictureRemove">Remove</span>
        </form>
    </li>
</ul>
<div class="ipWidget_ipPictureGallery_hidden">
    <form>
        <input name="smallPictureWidth" type="hidden"
            value="<?php echo (int)$smallPictureWidth ?>" /> <input
            type="smallPictureHeight"
            value="<?php echo (int)$smallPictureHeight ?>" />
    </form>
</div>
