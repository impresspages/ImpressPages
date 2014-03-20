<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $items \Ip\Menu\Item[]
 * @var $this \Ip\View
 */

$ul_id = '';
if (isset($attributes['ul']['id']))
{
	$ul_id = 'id="'.$attributes['ul']['id'].'"';
}

$ul_class = "level$depth";
if (isset($attributes['ul']['class']))
{
	$ul_class .= ' '.$attributes['ul']['class']; // append to level$depth
}

?>
<?php if (isset($items[0])){?>
    <?php $firstItem = $items[0]; ?>
    <ul <?php echo $ul_id; ?> class="<?php echo $ul_class; ?>"><?php
foreach($items as $item) {
    echo ipView('Ip/Internal/Config/view/menuItem.php', array('menuItem' => $item, 'depth' => $depth))->render();
} ?></ul>
<?php } ?>
