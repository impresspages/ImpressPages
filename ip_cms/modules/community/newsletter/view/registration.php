<div class="ipModuleNewsletter">
<?php $newsletterTitle = $this->par('community/newsletter/subscription_translations/newsletter'); ?>
<?php echo $this->renderWidget('IpTitle', array('title' => $newsletterTitle)); ?>
    <form class="ipmForm clearfix" method="post" action="">
        <div class="ipmError"></div>
        <p>
            <label class="ipmLabel">
                <?php echo $this->escPar('community/newsletter/subscription_translations/label'); ?>
                <input type="text" name="email" class="ipmInput" />
            </label>
        </p>
        <p class="ipmButtons">
            <a href="#" class="ipmButton ipmSubscribe" ><?php echo $this->escPar('community/newsletter/subscription_translations/subscribe'); ?></a>
<?php if ($this->par('community/newsletter/options/show_unsubscribe_button')) { ?>
            <a href="#" class="ipmButton ipmUnsubscribe"><?php echo $this->escPar('community/newsletter/subscription_translations/unsubscribe'); ?></a>
<?php } ?>
        </p>
    </form>
</div>
