<?php
/** @var $this \Ip\View */
?>
<div class="ip ipAdminMenu ipsModule well">
    <div class="nav nav-tabs nav-stacked">
        <?php
            echo $this->generateMenu('admin_navigation', $menuItems, 'bootstrapNav.php');
            //TODOX in this way anyone who can access menu config, can change this menu to anything :| secure somehow
        ?>
    </div>
</div>
