<div class="ip scopeGrid">
    <?php echo $this->subview('actions.php'); ?>
    <?php echo $this->subview('table.php'); ?>
    <?php echo $pagination->render(ipFile('Ip/Internal/Grid/view/pagination.php')); ?>
    <?php echo $this->subview('deleteModal.php'); ?>
    <?php echo $this->subview('updateModal.php'); ?>
</div>