<a href="<?php echo $homeUrl; ?>"><?php echo $this->esc(__('Home', 'ipPublic')); ?></a>
<?php foreach ($breadcrumbElements as $key => $element) { ?>
    <?php echo $separator; ?>
<a href="<?php echo $element->getLink(); ?>" title="<?php echo htmlspecialchars($element->getPageTitle()); ?>"><?php echo htmlspecialchars($element->getButtonTitle()); ?></a>
<?php } ?>
