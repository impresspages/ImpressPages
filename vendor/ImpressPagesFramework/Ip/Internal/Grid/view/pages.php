<div class="_pages ipsPages">
    <?php echo $pagination->render(ipFile('Ip/Internal/Grid/view/pagination.php')); ?>
    <?php echo ipView('Ip/Internal/Grid/view/pageSize.php', $this->getVariables()); ?>
</div>
