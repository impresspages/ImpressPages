<?php
/**
 * This comment block is used just to make IDE suggestions to work
 * @var $this \Ip\View
 */
?>
<?php echo ipRenderWidget('Title', array('title' => 'This is an example title')); ?>

<?php echo ipRenderWidget('Text', array('text' => '
    <p>Here is a text block where to can put any information. It supports <b>bold</b>, <em>italics</em>, <span style="text-decoration: underline;">underline</span>, <a href="http://www.impresspages.org">various links</a>. To make lists is really easy:</p>
    <ul>
        <li>Add widgets to any block by simply dragging and dropping;</li>
        <li>Paste any content to a text widget and it will adapt to your website\'s styles automatically;</li>
        <li>And many more.</li>
    </ul>
')); ?>
