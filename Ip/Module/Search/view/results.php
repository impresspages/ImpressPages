<?php 
echo $this->renderWidget('IpTitle', array('title' => $this->par('Search.search')));
$listHtml = \Ip\View::create('elements_list.php', array('elements' => $foundElementsCombined));
echo $this->renderWidget('IpText', array('text' => $listHtml));


foreach ($foundElements as $zoneKey => $zoneBunch) { 
    echo $this->renderWidget('IpTitle', array('title' => $site->getZone($zoneKey)->getTitle()), 'level2');
    $listHtml = \Ip\View::create('elements_list.php', array('elements' => $zoneBunch));
    echo $this->renderWidget('IpText', array('text' => $listHtml));
    
}
?>