<div class="ipmFileContainer <?php echo $classes ?>" data-inputname='<?php echo addslashes($inputName) ?>'>
    <div class="ipmHiddenInput"><!-- div that hides input field. It is needed for jQuery Tools to position error message -->
        <input type="text" name="<?php echo addslashes($inputName) ?>" />
    </div>
    <a <?php echo $attributesStr ?> class="ipmControlInput ipmFileAddButton"  href="#" >{{Upload}}</a>
    <div class="ipmFiles">

    </div>
    <div class="ipmFileTemplate ipgHide">
        <div class="ipmFileLabel">
            <span class="ipmFileName"></span>
            <span class="ipmRemove"></span>
        </div>
        <div class="ipmFileProgress">
            <div class="ipmFileProgressValue"></div>
        </div>
    </div>
</div>