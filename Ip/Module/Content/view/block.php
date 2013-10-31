<?php
    $showEmpty = false;
    if ($managementState && !$widgetsHtml) {
        $showEmpty = true;
    }
?>
<div id="ipBlock-<?php echo $blockName; ?>" data-revisionid='<?php echo $revisionId ?>' class="ipBlock<?php echo ($showEmpty ? ' ipbEmpty' : ''); ?>"><?php

    if ($widgetsHtml) {
        foreach($widgetsHtml as $key => $widgetHtml) {
            echo $widgetHtml;
        }
    } elseif ($managementState) {
?>
        <div class="ipbExampleContent">
            <div class="ipbDefault"><?php echo $this->escPar('standard/content_management/admin_translations/placeholder_text') ?></div>
            <div class="ipbUser">
<?php
        if ($exampleContent) {
            echo $exampleContent;
        } else {
            echo $this->subview('exampleContent.php')->render();
        }
?>
            </div>
        </div>
<?php
    }
?>
</div>
