<?php
/** @var $this \Ip\View */
$this;
?>
<ul class="ipModuleSitemap">
    <?php foreach($links as $link){ ?>
        <li><a href="<?php echo !empty($link['href']) ? $link['href'] : '' ?>"><?php echo $this->esc($link['title']) ?></a></li>
        <?php if (!empty($link['childlinks'])){ ?>
            <?php echo $this->subview('elements.php', array('links' => $link['childlinks']))->render(); ?>
        <?php } ?>
    <?php } ?>
</ul>
