<?php if (count($actions)) { ?>
    <div class="_actions">
        <?php foreach($actions as $action) { ?>
            <button
                type="button"
                class="btn btn-default ipsAction <?php echo esc(empty($action['class']) ? '' : $action['class'], 'attr'); ?>"
                <?php if (!empty($action['data'])) { ?>
                    data="<?php echo json_encode($action['data']); ?>"
                <?php } ?>
            >
                <?php if (!empty($action['label'])) { ?>
                    <?php echo esc($action['label']); ?>
                <?php } ?>
            </button>
        <?php } ?>
    </div>
<?php } ?>
