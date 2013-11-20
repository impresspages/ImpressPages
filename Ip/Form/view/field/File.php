<div class="file <?php echo $classes ?>" data-inputname='<?php echo addslashes($inputName) ?>'>
    <a <?php echo $attributesStr ?> class="ipmFileAddButton"  href="#" ><?php _e('Upload', 'ipAdmin') ?></a>
    <div>
        <input class="ipmControlBlank" type="text" name="<?php echo addslashes($inputName) ?>" /> <!-- It is needed for jQuery Tools to position error message -->
    </div>
    <div class="ipmFiles">

    </div>
    <div class="ipmFileTemplate ipmFile ipgHide">
        <div class="ipmRemove"></div>
        <div class="ipmUploadError"></div>
        <div class="ipmFileName"></div>
        <div class="ipmFileProgress">
            <div class="ipmFileProgressValue"></div>
        </div>
    </div>
</div>