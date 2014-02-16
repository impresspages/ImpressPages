<div class="file <?php echo $classes ?> ipsRepositoryFileContainer" data-inputname="<?php echo addslashes($inputName) ?>" data-filelimit="<?php echo $fileLimit ?>">
    <a <?php echo $attributesStr ?> class="btn btn-default ipsSelect ipmFileAddButton"  href="#" ><?php _e('Select', 'ipAdmin') ?></a>
    <div style="display: none;" class="ipsInputContainer">
        <?php if ($value) { ?>
            <input type="hidden" name="<?php echo esc($inputName, 'attr') ?>[]" value="<?php echo esc($value, 'attr') ?>" />
        <?php } ?>
    </div>
    <div class="ipmFiles">
        <?php if ($value) { ?>
            <div class="ipmFile">
                <button type="button" class="close ipsRemove">&times;</button>
                <div class="ipmFileName"><?php echo esc($value) ?></div>
            </div>
        <?php } ?>
    </div>
    <div class="ipmFile ipmFileTemplate">
        <button type="button" class="close ipsRemove" aria-hidden="true">&times;</button>
        <div class="ipmFileName"></div>
    </div>
</div>
