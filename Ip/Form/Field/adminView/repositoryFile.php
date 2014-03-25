<div class="file <?php echo $classes ?> ipsRepositoryFileContainer" data-inputname="<?php echo addslashes($inputName) ?>" data-filelimit="<?php echo $fileLimit ?>">
    <a <?php echo $attributesStr ?> class="btn btn-default ipsSelect ipmFileAddButton"  href="#" ><?php _e('Select', 'Ip-admin') ?></a>
    <div class="ipsFiles ipmFiles">
        <?php if ($value && is_array($value)) { ?>
            <?php foreach($value as $file) {  ?>
                <div class="ipsFile ipmFile">
                    <button type="button" class="close ipsRemove">&times;</button>
                    <div class="ipmFileName"><?php echo esc($file) ?></div>
                    <input type="hidden" name="<?php echo esc($inputName, 'attr') ?>[]" value="<?php echo esc($file, 'attr') ?>" />
                </div>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="ipsFile ipmFile ipsFileTemplate hidden">
        <button type="button" class="close ipsRemove" aria-hidden="true">&times;</button>
        <div class="ipmFileName ipsFileName"></div>
        <input type="hidden" name="" value="" />
    </div>
</div>
