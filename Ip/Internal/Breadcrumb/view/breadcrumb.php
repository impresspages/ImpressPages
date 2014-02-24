<?php
/**
 * @var $pages \Ip\Page[]
 */
$pages;
?>
<?php if ($homeUrl) { ?>
    <a href="<?php echo $homeUrl; ?>"><?php _e('Home', 'ipPublic') ?></a>
    <?php echo $separator; ?>
<?php } ?>
<?php foreach ($pages as $key => $page) { ?>
    <a href="<?php echo esc($page->getLink(), 'attr'); ?>" title="<?php echo esc($page->getTitle(), 'attr'); ?>"><?php echo esc($page->getTitle()); ?></a>
    <?php echo $key < count($pages) -1 ? $separator : ''; ?>
<?php } ?>
