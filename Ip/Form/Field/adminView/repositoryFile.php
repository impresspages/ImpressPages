<div class="file <?php echo $classes ?> ipsRepositoryFileContainer" data-inputname="<?php echo addslashes($inputName) ?>" data-filelimit="<?php echo $fileLimit ?>">
    <a <?php echo $attributesStr ?> class="btn btn-default ipsSelect ipmFileAddButton"  href="#" ><?php _e('Select', 'ipAdmin') ?></a>
    <input type="hidden" name="<?php echo addslashes($inputName) ?>" /> <!-- It is needed for jQuery Tools to position error message -->
    <div class="ipmFiles"></div>
    <div class="ipmFile ipmFileTemplate">
        <button type="button" class="close ipsRemove" aria-hidden="true">&times;</button>
        <div class="ipmFileName"></div>
    </div>
</div>
