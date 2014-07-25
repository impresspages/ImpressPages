<div class="<?php echo $classes; ?> ipsFileContainer" data-inputname='<?php echo addslashes($inputName); ?>'>
    <a <?php echo $attributesStr; ?> class="btn btn-default ipsFileAddButton" href="#"><?php _e(
            'Upload',
            'Ip-admin'
        ); ?></a>
    <input type="hidden" name="<?php echo addslashes($inputName); ?>"/>
</div>
