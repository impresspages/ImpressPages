<div class="file <?php echo $classes ?> ipsFileContainer" data-inputname='<?php echo addslashes($inputName) ?>'>
    <a <?php echo $attributesStr ?> class="btn btn-default ipmFileAddButton"  href="#" ><?php _e('Upload', 'Ip-admin') ?></a>
    <input type="hidden" name="<?php echo addslashes($inputName) ?>" /> <!-- It is needed for jQuery Tools to position error message -->
    <div class="ipmFiles"></div>
    <div class="well ipmFileTemplate ipmFile hidden">
        <button type="button" class="close ipsRemove" aria-hidden="true">&times;</button>
        <div class="ipmUploadError"></div>
        <div class="ipmFileName"></div>
        <!-- div class="ipmFileProgress">
            <div class="ipmFileProgressValue"></div>
        </div -->
        <div class="progress progress-striped active ipmFileProgress">
            <div class="progress-bar ipmFileProgressValue"></div>
        </div>
    </div>
</div>
