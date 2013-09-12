<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo $this->renderWidget('IpTitle', array('title' => 'This is an example title')); ?>

<?php echo $this->renderWidget('IpText', array('text' => '
    <p>Here is a text block where to can put any information. It supports <b>bold</b>, <em>italics</em>, <span style="text-decoration: underline;">underline</span>, <a href="http://www.impresspages.org">various links</a>. You can make lists:</p>
    <ul>
        <li>You add widgets to any block by simply dragging and dropping;</li>
        <li>You can paste any content to a text widget and it will adapt to your website styles automatically;</li>
        <li>And many more.</li>
    </ul>
')); ?>
<?php echo $this->renderWidget('IpTitle', array('title' => 'For titles always use "Title" widget'), 'level2'); ?>

<?php echo $this->renderWidget('IpTextImage', array('imageSmall' => MODULE_DIR.'standard/content_management/img/example_image.jpg', 'text' => '
    <p>You can put an image next to the text. It can be on the left or right. Just select a different layout. It\'s easy as that.</p>
    <p>You can add any widget to work with different types of content. There are many of them: title, text, separator, text with image, image, image gallery, logo gallery, file, table, HTML code, F.A.Q., Contact form.</p>
')); ?>

<?php echo $this->renderWidget('IpTitle', array('title' => 'Have a great experience to get most out of your website!'), 'level3'); ?>
