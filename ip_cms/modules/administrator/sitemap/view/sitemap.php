<?php
/** @var $this \Ip\View */
$this;
?>
<?php echo $this->renderWidget('IpTitle', array('title' => $this->escPar('administrator/sitemap/translations/sitemap'))) ?>
<?php echo $this->renderWidget('IpText', array('text' => $list)) ?>
