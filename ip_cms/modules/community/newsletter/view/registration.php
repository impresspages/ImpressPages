<div class="ipModuleNewsletter">
<?php $newsletterTitle = $this->par('community/newsletter/subscription_translations/newsletter'); ?>
<?php echo $this->renderWidget('IpTitle', array('title' => $newsletterTitle)); ?> 
    <span class="ipmError" class="error"><?php echo $this->par('community/newsletter/subscription_translations/text_incorrect_email'); ?></span>
    <form class="ipmForm" method="post" action=""> 
      <div>
        <input type="text" name="email" class="input" /> 
      </div>
      <div>
        <a href="#" class="ipmSubscribe" ><?php echo $this->escPar('community/newsletter/subscription_translations/subscribe'); ?></a>
    <?php if ($this->par('community/newsletter/options/show_unsubscribe_button')) { ?>
        <a href="#" class="ipmUnsubscribe"><?php echo $this->escPar('community/newsletter/subscription_translations/unsubscribe'); ?></a>
    <?php } ?>
        <div class="clear"><!-- --></div>
      </div> 
    </form>
</div>