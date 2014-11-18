<?php
// Header
echo ipView('Ip/Internal/Grid/view/header.php', $this->getVariables());

// Actions
echo ipView('Ip/Internal/Grid/view/actions.php', $this->getVariables());
if ($pagination->pagerSize() * ($pagination->currentPage() - 1) + count($data) > 100) {
    echo ipView('Ip/Internal/Grid/view/pages.php', array_merge($this->getVariables(), array('position' => 'top')));
}

// Main content
echo ipView('table.php', $this->getVariables());

// Actions
if ($pagination->currentPage() > 1 || count($data) > 10) {
    echo ipView('Ip/Internal/Grid/view/pages.php', array_merge($this->getVariables(), array('position' => 'bottom')));
}

// Modals
echo ipView('Ip/Internal/Grid/view/deleteModal.php', $this->getVariables());
echo ipView('Ip/Internal/Grid/view/updateModal.php', $this->getVariables());
echo ipView('Ip/Internal/Grid/view/createModal.php', $this->getVariables());
echo ipView('Ip/Internal/Grid/view/searchModal.php', $this->getVariables());

// Footer
echo ipView('Ip/Internal/Grid/view/footer.php', $this->getVariables());
?>
