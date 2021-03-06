<?php
/**
 * Template file of Collection Press To Item show
 * @author Avinash
 * You can add this file to theme folder by creating collectionpress folder and paste this file  there.
 * Path will be "<theme_name>/collectionpress/cp_item.php"
 * */

if (isset($_GET) && isset($_GET['item_id'])) {
    if ($_GET['item_id']!='') {
        $item_id = $_GET['item_id'];
        $cp_author = new CPR_AuthorReg();
        $response = $cp_author->get_item_by_id($item_id);
    }
}
?>
<?php if (isset($response) && $response!='') : ?>
    <ul>
    <?php foreach ($response->metadata as $md) : ?>
        <li>
            <p><?php echo $md->element; ?></p>
            <p><?php echo $md->qualifier; ?></p>
            <p><?php echo $md->value; ?></p>
        </li>
    <?php endforeach;?>
    </ul>
<?php else: ?>
    <p><?php echo __("Item Id is incorrect!", 'cpress') ?></p>
<?php endif; ?>
