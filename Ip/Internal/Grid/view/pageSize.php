<div class="dropdown _pageSize ipsPageSize">
    Items per page
    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
        <?php echo (int) $pageSize ?>
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
        <?php foreach($pageSizes as $size) { ?>
            <li role="presentation"><a class="ipsPageSizeSetting" data-rows="<?php echo escAttr($size) ?>" role="menuitem" tabindex="-1" href="#"><?php echo esc($size) ?></a></li>
        <?php } ?>
    </ul>
</div>
