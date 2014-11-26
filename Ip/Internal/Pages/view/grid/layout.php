<?php
// Header
echo ipView('Ip/Internal/Grid/view/header.php', $this->getVariables());

// Actions
echo ipView('Ip/Internal/Grid/view/actions.php', $this->getVariables());
if ($pagination->pagerSize() * ($pagination->totalPages() - 1) + count($data) > 100) {
    // Show top pagination if we have more than 100 records.
    echo ipView('Ip/Internal/Grid/view/pages.php', array_merge($this->getVariables(), array('position' => 'top')));
}

// Main content
echo ipView('table.php', $this->getVariables());

// Actions
if ($pagination->pagerSize() * ($pagination->totalPages() - 1) + count($data) > 10 || $pagination->currentPage() > 1) {
    //show pagination if we have more than 10 records
    echo ipView('Ip/Internal/Grid/view/pages.php', array_merge($this->getVariables(), array('position' => 'bottom')));
}

// Modals
echo ipView('Ip/Internal/Grid/view/deleteModal.php', $this->getVariables());
echo ipView('Ip/Internal/Grid/view/updateModal.php', $this->getVariables());
echo ipView('Ip/Internal/Grid/view/createModal.php', $this->getVariables());
echo ipView('Ip/Internal/Grid/view/searchModal.php', $this->getVariables());
echo ipView('Ip/Internal/Grid/view/moveModal.php', $this->getVariables());

// Footer
echo ipView('Ip/Internal/Grid/view/footer.php', $this->getVariables());

?>
