<?php if ($homeUrl) { ?>
    <a href="<?php echo $homeUrl; ?>"><?php echo $this->esc(__('Home', 'ipPublic')); ?></a>
    <?php echo $separator; ?>
<?php } ?>
<?php foreach ($breadcrumbElements as $key => $element) { ?>
    <a href="<?php echo $element->getLink(); ?>" title="<?php echo htmlspecialchars($element->getPageTitle()); ?>"><?php echo htmlspecialchars($element->getButtonTitle()); ?></a>
    <?php echo $key < count($breadcrumbElements) -1 ? $separator : ''; ?>
<?php } ?>
