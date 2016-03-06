<?php if (count($actions)) { ?>
    <div class="_actions">
        <?php foreach($actions as $action) { ?>
            <?php if ($action['type'] == 'Button') { ?>
                <button
                    type="button"
                    class="btn btn-default ipsAction <?php echo escAttr($action['class']); ?>"
                    <?php if (!empty($action['data'])) { ?>
                        data="<?php echo json_encode($action['data']); ?>"
                    <?php } ?>
                >
                    <?php echo esc($action['label']); ?>
                </button>
            <?php } ?>
            <?php if ($action['type'] == 'Select') { ?>
                <div class="btn-group <?php echo escAttr($action['class']); ?>">
                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="true">
                        <?php echo esc($action['label']); ?>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" >
                        <?php foreach($action['values'] as $value){ ?>
                            <li role="presentation"><a class="<?php echo escAttr($action['itemClass']); ?>" data-value="<?php echo escAttr($value['value']) ?>" role="menuitem" tabindex="-1" href="#"><?php echo esc($value['label']) ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            <?php if ($action['type'] == 'Html') { ?>
                <?php echo $action['html'] ?>
            <?php } ?>
        <?php } ?>
    </div>
<?php } ?>
