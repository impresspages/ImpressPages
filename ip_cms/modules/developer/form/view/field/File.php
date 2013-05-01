<div class="ipmFileContainer <?php echo $classes ?>" data-inputname='<?php echo addslashes($inputName) ?>'>
    <div class="ipmHiddenInput"><!-- div that hides input field. It is needed for jQuery Tools to position error message -->
        <input type="text" name="<?php echo addslashes($inputName) ?>" />
    </div>
    <a <?php echo $attributesStr ?> class="ipmFileAddButton"  href="#" ><?php echo $this->escPar('developer/form/translations/upload'); ?></a>
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