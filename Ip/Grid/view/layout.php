<div class="ip scopeGrid">
    <?php echo $this->subview('actions.php'); ?>
    <?php echo $this->subview('table.php'); ?>
    <?php echo $pagination->render(ipFile('Ip/Grid/view/pagination.php')); ?>
    <?php echo $this->subview('deleteModal.php'); ?>
</div>