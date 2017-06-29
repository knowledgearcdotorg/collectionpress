<?php
/**
 * Template file of Collection Press To Author Display
 * @author Avinash
 * You can add this file to theme folder by creating collectionpress folder and paste this file  there.
 * Path will be "<theme_name>/collectionpress/item_display.php"
 * */
?>
<ul>
    <?php foreach($response->response->docs as $doc): ?>
    	<?php $parts = array();?>
        <li>
            <?php if (is_array($title = $doc->title)): ?>
                <?php $title = array_shift($title); ?>

                <?php  if ($handle = $doc->handle): ?>
                    <?php $url = $options['item_url']."/".$handle; ?>
                    <?php $title = sprintf('<a href="%s" target="_blank">%s</a>', $url, $title); ?>
                <?php endif;?>
                <?php $parts[] = $title; ?>
            <?php endif;?>

            <?php if (is_array($publisher = $doc->{"dc.publisher"})): ?>
                <?php $parts[] = array_shift($publisher); ?>
            <?php endif; ?>

            <?php if (is_array($dateIssued = $doc->dateIssued)): ?>
                <?php $parts[] = array_shift($dateIssued); ?>
            <?php endif; ?>
            
            <?php echo implode(", ", $parts); ?>            
        </li>
</ul>

