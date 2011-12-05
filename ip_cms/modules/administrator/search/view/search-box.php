<form class="modAdministratorSearchForm" method="post" action="<?php echo $url ?>"> 
    <input type="hidden" name="action" value="search" />
    <input type="text" name="q" value="<?php echo htmlspecialchars($value) ?>" class="modAdministratorSearchInput" />
    <input type="submit" value="<?php echo htmlspecialchars($parametersMod->getValue('administrator', 'search', 'translations', 'search')) ?>"/> 
</form>