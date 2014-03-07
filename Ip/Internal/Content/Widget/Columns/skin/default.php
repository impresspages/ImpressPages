<?php foreach($cols as $i => $columnUniqueStr) { ?>
    <div class="ipsCol _col<?php if($i==0){echo ' first';}?><?php if($i==count($cols) - 1){echo ' last';}?>" style="width:<?php echo $widths[$i] ?>%;">
        <?php
        $block = ipBlock($columnUniqueStr)->exampleContent(' ');
        if (!empty($static)) {
            $block->asStatic();
        }
        echo $block->render($revisionId); ?>
    </div>
<?php } ?>
