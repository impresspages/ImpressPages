<?php
/** @var $paymentMethods \Ip\Payment[] */
?>
<?php echo ipRenderWidget('Heading', array('title' => __('Choose a payment method', 'Ip', false))) ?>
<div class="ipEcommerce">
<?php echo ipRenderWidget('Divider', [], 'space') ?>
<?php foreach($paymentMethods as $paymentMethod) { ?>
    <?php
        echo ipRenderWidget('Text', array('text' => '<div class="_paymentMethod _paymentMethod_' . escAttr($paymentMethod->name()) .'"><a href="#" class="ipsPaymentMethod" data-name="'. escAttr($paymentMethod->name()) .'">'. $paymentMethod->html() .'</a></div>'));
    ?>
<?php } ?>
</div>
