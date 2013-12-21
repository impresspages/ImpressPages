<div class="actions">
    <?php foreach($actions as $action) { ?>
        <button type="button" class="btn btn-default ipsAction" data="<?php esc($action['data']) ?>" >
            <?php echo esc($action['title']) ?>
        </button>
    <?php } ?>
</div>