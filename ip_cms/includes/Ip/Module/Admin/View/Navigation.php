<?php
/** @var $this \Ip\View */
$this;
?>

<div class="ipModuleAdminNavigation">
    <?php
        echo $this->generateMenu('admin_navigation', $menuItems);
        //TODOX in this way anyone who can access menu config, can change this menu to anything :| secure somehow
    ?>
</div>