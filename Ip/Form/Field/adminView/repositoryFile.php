<div class="file <?php echo $classes; ?> ipsRepositoryFileContainer" data-inputname="<?php echo addslashes($inputName); ?>" data-filelimit="<?php echo $fileLimit; ?>" data-preview="<?php echo $preview; ?>">
    <a <?php echo $attributesStr; ?> class="btn btn-default ipsSelect ipsFileAddButton"  href="#" ><?php _e('Select', 'Ip-admin'); ?></a>
    <div class="ipsFiles">
        <?php if ($value && is_array($value)) { ?>
            <?php foreach($value as $file) {  ?>
                <div class="_file ipsFile">
                    <button type="button" class="close ipsRemove">&times;</button>
                    <div class="ipsFileName"><?php echo esc($file); ?></div>
                    <input type="hidden" name="<?php echo escAttr($inputName); ?>[]" value="<?php echo escAttr($file); ?>" />
                </div>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="_file ipsFile ipsFileTemplate hidden">
        <button type="button" class="close ipsRemove" aria-hidden="true">&times;</button>
        <div class="ipsFileName"></div>
        <input type="hidden" name="" value="" />
    </div>
</div>
