<div class="_container ipsColsContainer">
<?php foreach($cols as $i => $columnUniqueStr) { ?>
    <div class="ipsCol _col" style="width:<?php echo $widths[$i]; ?>%;">
        <?php
        $block = ipBlock($columnUniqueStr)->exampleContent(' ');
        if (!empty($static)) {
            $block->asStatic();
        }
        echo $block->render($revisionId); ?>
    </div>
<?php } ?>
</div>
