<?php foreach($cols as $columnUniqueStr) { ?>
    <div class="ipwCol ipsCol" style="width:<?php echo (100 / count($cols)) ?>%;" style="width: 25%; vertical-align: top;">
<!--        --><?php //echo $columnUniqueStr ?>
<!--        --><?php //var_dump($cols);?>
        <?php echo ipBlock($columnUniqueStr)->exampleContent(' ')->render($revisionId); ?>
    </div>
<?php } ?>
