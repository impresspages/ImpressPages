<div class="ipModuleSearch">
    <form class="ipmForm" method="post" action="<?php echo $actionUrl ?>"> 
        <input type="hidden" name="action" value="search" />
        <input type="text" class="ipmInput" name="q" value="<?php echo htmlspecialchars($value) ?>" />
        <input type="submit" class="ipmButton" value="<?php echo $this->escPar('administrator/search/translations/search'); ?>" />
    </form>
</div>
