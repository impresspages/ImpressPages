<div class="file <?php echo escAttr($classes); ?> ipsRepositoryFileContainer"
     data-inputname="<?php echo escAttr($inputName); ?>"
     data-filelimit="<?php echo (int)$fileLimit; ?>"
     data-preview="<?php echo escAttr($preview); ?>"
     data-secure="<?php echo escAttr($secure); ?>"
     data-path="<?php echo escAttr($path); ?>"
     data-filter="<?php echo escAttr($filter); ?>"
     data-filterextensions='<?php echo escAttr(json_encode($filterExtensions)); ?>'>
    <a <?php echo $attributesStr; ?> class="btn btn-default ipsSelect ipsFileAddButton" href="#"><?php _e(
            'Select',
            'Ip-admin'
        ); ?></a>

    <div class="ipsFiles">
        <?php if ($value && is_array($value)) { ?>
            <?php foreach ($value as $file) { ?>
                <div class="_file ipsFile">
                    <button type="button" class="close ipsRemove">&times;</button>
                    <div><a target="_blank" class="ipsLink ipsFileName" href="<?php echo ipFileUrl('file/repository/' . $file) ?>"><?php echo esc($file); ?></a></div>
                    <input type="hidden" name="<?php echo escAttr($inputName); ?>[]"
                           value="<?php echo escAttr($file); ?>"/>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="well _file ipsFile ipsFileTemplate hidden">
        <button type="button" class="close ipsRemove" aria-hidden="true">&times;</button>
        <div><a target="_blank" class="ipsLink ipsFileName" href=""></a></div>
        <input type="hidden" name="" value=""/>
    </div>
</div>
