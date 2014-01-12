<?php
/**
 * preview / layout template for multicolumn widget. This template needs to render
 * the required markup for columns, and generate a unique block inside of each
 * column. To make block unique, use $baseId which is provided by controller.
 *
 * For example, to generate two bootstrap columns, markup could look like this:
 *
 *  <div class="row">
 *    <div class="span4"><?php echo ipBlock('multicol'.$baseId.'left') ?></div>
 *    <div class="span8"><?php echo ipBlock('multicol'.$baseId.'right') ?></div>
 *  </div>
 *
 * The following code based on <table> is ONLY included to support templates which do
 * not have a grid system. Templates which do have a proper grid system should really
 * override this.
 *
 */

if (empty($columns)) {
    $columns = 2;
}

?>

<?php for($i = 1; $i <= $columns; $i++) { ?>
    <div class="ipwCol" style="width:<?php echo (100 / $columns) ?>%;" style="width: 25%; vertical-align: top;">
        <?php echo ipBlock('multicol'.$widgetId.'_'.$i)->exampleContent(' ')->render($revisionId); ?>
    </div>
<?php } ?>
