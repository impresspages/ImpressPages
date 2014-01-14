<?php foreach($cols as $i => $columnUniqueStr) { ?>
    <div class="ipwCol<?php if($i==0){echo ' first';}?><?php if($i==count($cols) - 1){echo ' last';}?>" style="width:<?php echo (100 / count($cols)) ?>%;">
        <?php echo ipBlock($columnUniqueStr)->exampleContent(' ')->render($revisionId); ?>
    </div>
<?php } ?>
