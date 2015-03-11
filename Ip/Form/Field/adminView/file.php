<div class="file <?php echo $classes; ?> ipsFileContainer"
     data-inputname="<?php echo addslashes($inputName);?>"
     data-filelimit="<?php echo (int)$fileLimit; ?>">
    <a <?php echo $attributesStr; ?> class="btn btn-default ipsFileAddButton" href="#"><?php _e(
            'Upload',
            'Ip-admin'
        ); ?></a>
    <input type="hidden" name="<?php echo addslashes($inputName); ?>"/>
    <!-- It is needed for jQuery Tools to position error message -->
    <div class="ipsFiles"></div>
    <div class="well _file ipsFile ipsFileTemplate hidden">
        <button type="button" class="close ipsRemove" aria-hidden="true">&times;</button>
        <div class="_error ipsUploadError"></div>
        <div class="ipsFileName"></div>
        <div class="progress ipsFileProgress">
            <div class="progress-bar progress-bar-striped active ipsFileProgressValue"></div>
        </div>
    </div>
</div>
