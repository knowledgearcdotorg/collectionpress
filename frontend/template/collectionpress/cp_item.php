<?php
/**
 * Template Name: CP Item List
 * Template file of Collection Press To Item show
 * @author Avinash
 * You can add this file to theme folder by creating collectionpress folder and paste this file  there.
 * Path will be "<theme_name>/collectionpress/cp_item.php"
 * */

get_header();
if (isset($_GET) && isset($_GET['item_id'])) {
    if ($_GET['item_id']!='') {
        $item_id = $_GET['item_id'];
        $cp_author = new CP_Author();
        //~ do_action( 'show_item_details', $item_id );
        $response = $cp_author->cp_get_item_by_id($item_id);
    }
}
?>


<div id="main-content">
    <div class="container">
        <div id="content-area" class="clearfix">
            <div class="content-wrap">
                <?php if (isset($response) && $response!='') : ?>
                    <?php foreach ($response->metadata as $md) : ?>

                        <p><?php echo $md->element; ?></p>
                        <p><?php echo $md->qualifier; ?></p>
                        <p><?php echo $md->value; ?></p>

                    <?php endforeach;?>
                <?php else: ?>
                    <p><?php echo __("Item Id is incorrect!", 'cpress') ?></p>
                <?php endif; ?>
            </div> <!-- #left-area -->
        <?php get_sidebar(); ?>
        </div> <!-- #content-area -->
    </div> <!-- .container -->
</div> <!-- #main-content -->

<?php get_footer(); ?>
