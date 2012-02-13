<?php
/* HTML is the same, styles changes through ipLayout-right class */
echo \Ip\View::create('default.php', $this->getData())->render();
?>
