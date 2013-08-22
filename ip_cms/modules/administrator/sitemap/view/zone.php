<?php
/** @var $this \Ip\View */
$this;
?>
<?php if (!empty($zoneTitle)){ ?>
    <?php echo $this->renderWidget('IpTitle', array('title' => $zoneTitle), 'level2') ?>
<?php } ?>
<?php echo $this->renderWidget('IpText', array('text' => $elements)) ?>
