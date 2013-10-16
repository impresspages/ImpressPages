<?php
/** @var $this \Ip\View */
?>
<div class="ipAdminMenu ipsModule">
    <?php
        echo $this->generateMenu('admin_navigation', $menuItems);
        //TODOX in this way anyone who can access menu config, can change this menu to anything :| secure somehow
    ?>
</div>
