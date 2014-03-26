<?php
/**
 * @var $pages \Ip\Page[]
 */
$pages;
?>
<?php if ($homeUrl) { ?>
    <a href="<?php echo $homeUrl; ?>"><?php _e('Home', 'Ip') ?></a>
    <?php echo $separator; ?>
<?php } ?>
<?php foreach ($pages as $key => $page) { ?>
    <a href="<?php echo escAttr($page->getLink()); ?>" title="<?php echo escAttr($page->getTitle()); ?>"><?php echo esc($page->getTitle()); ?></a>
    <?php echo $key < count($pages) -1 ? $separator : ''; ?>
<?php } ?>
