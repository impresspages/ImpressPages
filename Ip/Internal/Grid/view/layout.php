<?php echo ipView('Ip/Internal/Grid/view/head.php', $this->getVariables()); ?>
<?php echo ipView('Ip/Internal/Grid/view/actions.php', $this->getVariables()); ?>
<?php echo ipView('Ip/Internal/Grid/view/table.php', $this->getVariables()); ?>
<?php echo $pagination->render(ipFile('Ip/Internal/Grid/view/pagination.php')); ?>
<?php echo ipView('Ip/Internal/Grid/view/deleteModal.php', $this->getVariables()); ?>
<?php echo ipView('Ip/Internal/Grid/view/updateModal.php', $this->getVariables()); ?>
<?php echo ipView('Ip/Internal/Grid/view/createModal.php', $this->getVariables()); ?>
<?php echo ipView('Ip/Internal/Grid/view/searchModal.php', $this->getVariables()); ?>
<?php echo ipView('Ip/Internal/Grid/view/foot.php', $this->getVariables()); ?>
