<?php
/** @var $paymentMethods \Ip\Payment[] */
?>
<?php echo ipRenderWidget('Heading', array('title' => __('Choose a payment method', 'Ip', false))) ?>
<div class="ipEcommerce">
<?php foreach($paymentMethods as $paymentMethod) { ?>
    <div class="_paymentMethod">
        <a href="#" class="ipsPaymentMethod" data-name="<?php echo escAttr($paymentMethod->name()) ?>"><img src="<?php echo $paymentMethod->icon() ?>" alt="<?php echo escAttr($paymentMethod->name()) ?>" /></a>
    </div>
<?php } ?>
</div>
