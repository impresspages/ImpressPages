<ul class="modAdministratorSearchList">
    <?php foreach ($elements as $key => $element) { ?>
    <?php 
        $tmpTitle = $element->getPageTitle();
        if($tmpTitle == ''){ 
            $tmpTitle = $element->getButtonTitle(); 
        }
    ?>
    <li> 
        <a class="modAdministratorSearchLink" href="<?php echo $element->getLink(); ?>"><?php echo $this->esc($tmpTitle) ?></a>
        <?php if($this->par('administrator/search/options/show_description')){ ?>
            <p class="modAdministratorSearchDescription"><?php echo $this->esc($element->getDescription()) ?></p>
        <?php }?>
    </li>
    <?php } ?>
</ul>
